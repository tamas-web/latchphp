<?php

namespace App\Controllers;

use App\Alerts\Alert;
use App\Config;
use App\Datatype\Response;
use App\Database\Connector as DB;
use App\Events\{ Emitter, Listener };
use App\Filters\Filter;
use App\Html\Link;
use App\Http\{ URL, Request };
use App\Libraries\{ Controller, User, Password };
use App\Models\Users;
use App\Models\Settings\{ Role, Roles };

class UsersController extends Controller {

	use \App\Traits\randomChars;

	protected $pageTitle = 'Users';

	/**
	 * Build the role <select> option list (all roles; placeholder first).
	 */
	protected function roleOptions() : array {

		$ranks = [
			[
				'id'   => '',
				'name' => 'Select a role',
			],
		];

		foreach( Roles::getAllRoles() as $role ){

			$ranks[] = [
				'id'   => $role->id,
				'name' => $role->name,
			];

		}

		return $ranks;

	}

	public function users( Request $request ) : void {

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin/users', 'Users' ),
		] );

		$showActiveOnly = (bool) $request->user()->meta()->showActiveOnly_users;

		// switch pressed form update
		if( $request->get( 'form' ) === 'update' ){

			$show = $request->get( 'showActive', fn( $show ) => (bool) $show );
			$request->user()->meta()->save( 'showActiveOnly_users', (int) $show );

			$showActiveOnly = $show;

		}

		$request->view()
			->body()
				->data( [
					'users'          => Users::getPaginatedUsers(
						page:           $request->get( 'page', 'intval' ),
						showActiveOnly: $showActiveOnly,
					),
					'roles'          => Roles::getAllRoles(),
					'showActiveOnly' => $showActiveOnly,
				] )
				->add( 'users.index' );

		$request->view()->render();

	}

	public function user( Request $request ) : void {

		$id = (int) $request->getParameter( 0 );

		$user = Users::getUserData( $id );

		if( $user ){

			// only a super admin may open another super-admin account (your own profile is allowed)
			if( $request->user()->id !== $user->id && $user->is_super_admin && ! $request->user()->isSuperAdmin() ){

				Alert::warning( 'You do not have permission to edit this account.' );

				$request->redirect( '/admin/users' );

			}

			Filter::add( 'page_title', fn( $title ) => sprintf( 'Edit %s', join( ' ', [ $user->name, $user->surname ] ) ) );

			Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
				...$breadcrumbs,
				new Link( '/admin/users', 'Users' ),
				new Link( sprintf( '/admin/users/%u', $user->id ), join( ' ', [ $user->name, $user->surname ] ) ),
			] );

			$request->view()
				->js( [
					'generate-password' => 'assets/js/misc/generatePassword.js',
				] );

			$request->view()
				->body()
					->data( [
						'user'         => $user,
						'ranks'        => $this->roleOptions(),
						'canGrantRole' => $request->user()->can( 'users.grant.role' ),
					] )
					->add( 'users.view' );

			$request->view()->render();

		} else {

			$request->redirect( '/admin/users' );

		}

	}

	public function update( Request $request ) : void {

		$response = new Response;

		$id = (int) $request->getParameter( 0 );

		$user = User::findById( $id );

		if( $user ){

			Listener::on( 'user_edit_save', [ $this, 'updateUserAccount' ] );

			Emitter::broadcast( 'user_edit_save', $user, $request, $response );

		} else {

			Alert::error( 'This user does not exist.' );

			$request->redirect( '/admin/users' );

		}

		echo $response;

	}

	public function updateUserAccount( User $user, Request $request, Response $response ) : void {

		if( ! $request->post( 'updateUserAccount' ) ){

			return;

		}

		$actor  = $request->user();
		$isSelf = $actor->id === $user->id;

		// only a super admin may edit another super-admin account (your own profile is allowed)
		if( ! $isSelf && $user->isSuperAdmin() && ! $actor->isSuperAdmin() ){

			Alert::error( 'You do not have permission to edit this account.' );

			return;

		}

		$phoneEnabled  = (bool) Config::get( 'settings.users.phone.enabled' );
		$phoneRequired = (bool) Config::get( 'settings.users.phone.required' );

		$name        = $request->post( 'name', 'trim' );
		$surname     = $request->post( 'surname', 'trim' );
		$phone       = $phoneEnabled ? $request->post( 'phone', fn( $phone ) => sanitize_phone_number( $phone ) ) : $user->phone;
		$email       = $request->post( 'email', fn( $email ) => validate_email( $email ) );
		$deauthorize = $request->post( 'deauthorizeAllDevices' );
		$password    = $request->post( 'password', 'trim' );

		// assigning a role requires users.grant.role (never on your own account); enable/disable
		// just requires being able to edit someone else's account
		$canGrantRole = ! $isSelf && $actor->can( 'users.grant.role' );
		$idRole       = $canGrantRole ? $request->post( 'id_role', fn( $value ) => (int) $value ) : $user->getRoleId();
		$enabled      = $isSelf ? $user->isEnabled() : (bool) $request->post( 'user_account_enabled' );
		$role         = ( $canGrantRole && $idRole ) ? Role::get( $idRole ) : null;

		// only a super admin may grant/revoke the super-admin bit, and never on their own account
		$canToggleSuperAdmin = ! $isSelf && $actor->isSuperAdmin();
		$superAdmin          = $canToggleSuperAdmin ? (bool) $request->post( 'user_super_admin' ) : $user->isSuperAdmin();

		$is_required = [
			'name'    => $name,
			'surname' => $surname,
			'email'   => $email,
		];

		$messages = [
			'name'    => 'First name',
			'surname' => 'Last name',
			'email'   => 'E-mail',
		];

		if( $phoneEnabled && $phoneRequired ){

			$is_required['phone'] = $phone;
			$messages['phone']    = 'Phone';

		}

		if( $canGrantRole ){

			$is_required['id_role'] = $idRole;
			$messages['id_role']    = 'Role';

		}

		$missing_values = [];

		foreach( $is_required as $key => $value ){

			if( ! $value ){

				$missing_values[] = sprintf( '<li>%s</li>', $messages[ $key ] ?? $key );
				$response['form_errors'][] = $key;

			}

		}

		if( $missing_values ){

			Alert::error( sprintf( 'Please check the following fields:<ul>%s</ul>', join( '', $missing_values ) ) );

		}

		// the chosen role must exist (only when the actor may assign roles)
		if( $canGrantRole && $idRole && ! $role ){

			$response['form_errors'][] = 'id_role';

			Alert::error( 'The selected role is invalid.' );

		}

		if( ! Alert::has() ){

			$current_name    = $user->name;
			$current_surname = $user->surname;
			$current_phone   = $user->phone;
			$current_email   = $user->email;
			$current_options = $user->options;
			$current_role    = $user->getRoleId();

			$doUpdate = 0;

			// if user name changed
			if( $current_name !== $name ){

				$user->name = $name;
				$doUpdate |= 1;

			}

			// if user surname changed
			if( $current_surname !== $surname ){

				$user->surname = $surname;
				$doUpdate |= 1;

			}

			// if user phone changed
			if( $phoneEnabled && $current_phone != $phone ){

				if( Users::checkIfPhoneExists( $phone, $user->id ) ){

					Alert::error( 'This phone number is already in use.' );

				} else {

					$user->phone = $phone;
					$user->clearOption( self::ACCOUNT_PHONE_CONFIRMED );
					$doUpdate |= 1;

				}

			}

			// if user email changed
			if( $current_email !== $email ){

				if( Users::checkIfEmailExists( $email, $user->id ) ){

					Alert::error( 'This email address is already in use.' );

				} else {

					$user->email = $email;
					$doUpdate |= 1;

				}

			}

			// role assignment only when the actor holds users.grant.role
			if( $canGrantRole && $current_role !== $idRole ){

				$user->setRole( $idRole );
				$doUpdate |= 1;

			}

			// enable/disable only when managing another account
			if( ! $isSelf ){

				if( $enabled ){

					$user->setOption( self::ACCOUNT_ENABLED );

				} else {

					$user->clearOption( self::ACCOUNT_ENABLED );

				}

			}

			// super-admin bit: only a super admin may change it (and never on their own account)
			if( $canToggleSuperAdmin ){

				if( $superAdmin ){

					$user->setOption( self::ACCOUNT_ADMIN );

				} else {

					$user->clearOption( self::ACCOUNT_ADMIN );

				}

			}

			// manual e-mail / phone confirmation - any account the editor can edit, the editor's own
			// included (benign; the page is gated users.edit). The two checkboxes own these bits and
			// are authoritative over the field-change blocks above.
			if( $request->post( 'user_email_confirmed' ) ){

				$user->setOption( self::ACCOUNT_EMAIL_CONFIRMED );

			} else {

				$user->clearOption( self::ACCOUNT_EMAIL_CONFIRMED );

			}

			if( $phoneEnabled ){

				if( $request->post( 'user_phone_confirmed' ) ){

					$user->setOption( self::ACCOUNT_PHONE_CONFIRMED );

				} else {

					$user->clearOption( self::ACCOUNT_PHONE_CONFIRMED );

				}

			}

			// if options changed
			if( $current_options !== $user->options ){

				$doUpdate |= 1;

			}

			// deauthorize all devices
			if( $deauthorize ){

				$user->public_key  = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );
				$user->private_key = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );

				$doUpdate |= 1;

			}

			// password change
			if( $password ){

				if( ! is_strong_password( $password ) ){

					$response['form_errors'][] = 'password';

					$has_minimum_length = password_minim_length( $password );
					$has_special_char   = password_has_special_char( $password );
					$has_letter         = password_has_letter( $password );

					$pass_errors = [];

					if( ! $has_minimum_length ){

						$pass_errors[] = '<li>at least 8 characters</li>';

					}

					if( ! $has_special_char ){

						$pass_errors[] = '<li>at least one number or special character</li>';

					}

					if( ! $has_letter ){

						$pass_errors[] = '<li>at least one letter</li>';

					}

					if( $pass_errors ){

						Alert::error( sprintf( 'The new password does not meet the following requirements:<ul>%s</ul>', join( '', $pass_errors ) ) );

					}

				} else {

					$user->password = Password::hash( $password );

					$doUpdate |= 1;

				}

			}

			if( ! Alert::has() ){

				if( ! $doUpdate ){

					Alert::info( 'The account details do not need updating.' );

				} else {

					if( $user->save() ){

						Alert::success( 'The account was updated successfully.' );

						$request->redirect( URL::BUILD( sprintf( 'admin/users/%u', $user->id ) ) );

					} else {

						Alert::error( 'Account update failed. Please try again later.' );

					}

				}

			}

		}

	}

	public function deleteForm( Request $request ) : void {

		$response = new Response;

		$id = (int) $request->getParameter( 0 );

		$user = User::findById( $id );

		if( $user && $request->user()->id !== $id && ( ! $user->isSuperAdmin() || $request->user()->isSuperAdmin() ) ){

			Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
				...$breadcrumbs,
				new Link( '/admin/users', 'Users' ),
				new Link( sprintf( '/admin/users/%u', $user->id ), join( ' ', [ $user->name, $user->surname ] ) ),
				new Link( sprintf( '/admin/users/%u/delete', $user->id ), 'Delete' ),
			] );

			$request->view()
				->body()
					->data( [
						'user' => $user,
					] )
					->add( 'users.delete' );

			$request->view()->render();

		} else {

			$request->redirect( '/admin/users' );

		}

	}

	public function delete( Request $request ) : void {

		$response = new Response;

		$id = (int) $request->getParameter( 0 );

		$user = User::findById( $id );

		if( $user && $request->user()->id !== $id && ( ! $user->isSuperAdmin() || $request->user()->isSuperAdmin() ) ){

			if( Users::deleteUser( $id ) ){

				Alert::success( 'User deleted successfully!' );

				$request->redirect( '/admin/users' );

			} else {

				Alert::error( 'Action failed. Please try again later.' );

			}

		} else {

			$request->redirect( '/admin/users' );

		}

		echo $response;

	}

	public function addForm( Request $request ) : void {

		Filter::add( 'page_title', fn( $title ) => 'Add user' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin/users', 'Users' ),
			new Link( '/admin/users/add', 'Add new' ),
		] );

		$request->view()
			->js( [
				'generate-password' => '/assets/js/misc/generatePassword.js',
			] );

		$request->view()
			->body()
				->data( [
					'ranks'        => $this->roleOptions(),
					'canGrantRole' => $request->user()->can( 'users.grant.role' ),
				] )
				->add( 'users.add' );

		$request->view()->render();

	}

	public function add( Request $request ) : void {

		$response = new Response;

		if( 'newAccount' === $request->post( 'newAccount' ) ){

			$phoneEnabled  = (bool) Config::get( 'settings.users.phone.enabled' );
			$phoneRequired = (bool) Config::get( 'settings.users.phone.required' );

			// a role may only be chosen when the actor holds users.grant.role; otherwise the
			// new account defaults to the least-privilege User role
			$canGrantRole    = $request->user()->can( 'users.grant.role' );
			$idRole          = $canGrantRole ? $request->post( 'id_role', fn( $value ) => (int) $value ) : Role::idByName( 'User' );
			$role            = ( $canGrantRole && $idRole ) ? Role::get( $idRole ) : null;
			$name            = $request->post( 'name', 'trim' );
			$surname         = $request->post( 'surname', 'trim' );
			$phone           = $phoneEnabled ? $request->post( 'phone', fn( $phone ) => sanitize_phone_number( $phone ) ) : null;
			$email           = $request->post( 'email', fn( $email ) => validate_email( $email ) );
			$password        = $request->post( 'password', 'trim' );
			$is_required = [
				'name'            => $name,
				'surname'         => $surname,
				'email'           => $email,
				'password'        => $password,
			];

			$messages = [
				'name'            => 'First name',
				'surname'         => 'Last name',
				'email'           => 'E-mail',
				'password'        => 'Password',
			];

			if( $phoneEnabled && $phoneRequired ){

				$is_required['phone'] = $phone;
				$messages['phone']    = 'Phone';

			}

			if( $canGrantRole ){

				$is_required['id_role'] = $idRole;
				$messages['id_role']    = 'Role';

			}

			$missing_values = [];

			foreach( $is_required as $key => $value ){

				if( ! $value ){

					$missing_values[] = sprintf( '<li>%s</li>', $messages[ $key ] ?? $key );
					$response['form_errors'][] = $key;

				}

			}

			if( $missing_values ){

				Alert::error( sprintf( 'Please check the following fields:<ul>%s</ul>', join( '', $missing_values ) ) );

			}

			if( ! is_strong_password( $password ) ){

				$response['form_errors'][] = 'password';

				$has_minimum_length = password_minim_length( $password );
				$has_special_char   = password_has_special_char( $password );
				$has_letter         = password_has_letter( $password );

				$pass_errors = [];

				if( ! $has_minimum_length ){

					$pass_errors[] = '<li>at least 8 characters</li>';

				}

				if( ! $has_special_char ){

					$pass_errors[] = '<li>at least one number or special character</li>';

				}

				if( ! $has_letter ){

					$pass_errors[] = '<li>at least one letter</li>';

				}

				if( $pass_errors ){

					Alert::error( sprintf( 'The password does not meet the following requirements:<ul>%s</ul>', join( '', $pass_errors ) ) );

				}

			}

			if( ! Alert::has() ){

				// check if email or phone exists
				$_check = Users::checkIfEmailPhoneExists( email: $email, phone: (string) $phone );

				if( $_check['email'] ){

					Alert::error( 'The email address is already in use.' );

				}

				if( $phoneEnabled && $_check['phone'] ){

					Alert::error( 'The phone number is already in use.' );

				}

				// the chosen role must exist
				if( $canGrantRole && ! $role ){

					$response['form_errors'][] = 'id_role';

					Alert::error( 'The selected role is invalid.' );

				}

				if( ! Alert::has() ){

					DB::getInstance()->autocommit( false );

					$options = self::ACCOUNT_ENABLED;

					$options |= self::ACCOUNT_EMAIL_CONFIRMED;

					if( $phoneEnabled ){
						$options |= self::ACCOUNT_PHONE_CONFIRMED;
					}

					// only a super admin may create another super admin
					if( $request->user()->isSuperAdmin() && $request->post( 'user_super_admin' ) ){
						$options |= self::ACCOUNT_ADMIN;
					}

					$user = Users::createUser(
						name: $name,
						surname: $surname,
						phone: $phone,
						email: $email,
						password: $password,
						options: $options,
						idRole: $idRole,
					);

					if( $user ){

						if( ! Alert::has() ){

							// turn autocommit on
							DB::getInstance()->autocommit( true );

							// redirect
							$request->redirect( '/admin/users' );

							// form reset
							$response['form_reset'] = true;

							Alert::success( 'User added successfully.' );

						}

					} else {

						Alert::error( 'Failed to add user. Please try again later.' );

					}

				}

			}

		} else {

			Alert::error( 'An error occurred while submitting the form. Reload the page and try again.' );

		}

		echo $response;

	}

}
