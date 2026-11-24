<?php

namespace App\Controllers;

use App\Alerts\Alert;
use App\Config;
use App\Datatype\Response;
use App\Filters\Filter;
use App\Html\Link;
use App\Http\{ Request, URL };
use App\Libraries\{ Controller, Password };
use App\Models\Users;

class ProfileController extends Controller {

	use \App\Traits\randomChars;

	protected $pageTitle = 'My profile';

	/**
	 * Self-service profile editor - any logged-in user, their own account only.
	 */
	public function profileForm( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'My profile' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/profile', 'My profile' ),
		] );

		$request->view()
			->js( [
				'generate-password' => 'assets/js/misc/generatePassword.js',
			] );

		$request->view()
			->body()
				->data( [
					'user' => Users::getUserData( $request->user()->id ),
				] )
				->add( 'profile.index' );

		$request->view()->render();

	}

	/**
	 * Always operates on $request->user() - never an id from the request. Mirrors the self-edit path
	 * of UsersController::updateUserAccount, with the account-management fields (role / enabled /
	 * super-admin) intentionally absent.
	 */
	public function profileUpdate( Request $request ) : void {

		$response = new Response;

		if( ! $request->post( 'updateProfile' ) ){

			echo $response;
			return;

		}

		$user = $request->user();

		$phoneEnabled  = (bool) Config::get( 'settings.users.phone.enabled' );
		$phoneRequired = (bool) Config::get( 'settings.users.phone.required' );

		$name        = $request->post( 'name', 'trim' );
		$surname     = $request->post( 'surname', 'trim' );
		$phone       = $phoneEnabled ? $request->post( 'phone', fn( $phone ) => sanitize_phone_number( $phone ) ) : $user->phone;
		$email       = $request->post( 'email', fn( $email ) => validate_email( $email ) );
		$password    = $request->post( 'password', 'trim' );
		$deauthorize = $request->post( 'deauthorizeAllDevices' );

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

		if( ! Alert::has() ){

			$doUpdate = 0;

			if( $user->name !== $name ){

				$user->name = $name;
				$doUpdate |= 1;

			}

			if( $user->surname !== $surname ){

				$user->surname = $surname;
				$doUpdate |= 1;

			}

			if( $phoneEnabled && $user->phone != $phone ){

				if( Users::checkIfPhoneExists( $phone, $user->id ) ){

					Alert::error( 'This phone number is already in use.' );

				} else {

					$user->phone = $phone;
					$user->clearOption( self::ACCOUNT_PHONE_CONFIRMED );
					$doUpdate |= 1;

				}

			}

			if( $user->email !== $email ){

				if( Users::checkIfEmailExists( $email, $user->id ) ){

					Alert::error( 'This email address is already in use.' );

				} else {

					$user->email = $email;
					$user->clearOption( self::ACCOUNT_EMAIL_CONFIRMED );
					$doUpdate |= 1;

				}

			}

			// deauthorize all devices (rotate the key-pair)
			if( $deauthorize ){

				$user->public_key  = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );
				$user->private_key = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );
				$doUpdate |= 1;

			}

			// password change (optional)
			if( $password ){

				$password_confirm = $request->post( 'password_confirm', 'trim' );

				if( ! is_strong_password( $password ) ){

					$response['form_errors'][] = 'password';

					$pass_errors = [];

					if( ! password_minim_length( $password ) ){
						$pass_errors[] = '<li>at least 8 characters</li>';
					}

					if( ! password_has_special_char( $password ) ){
						$pass_errors[] = '<li>at least one number or special character</li>';
					}

					if( ! password_has_letter( $password ) ){
						$pass_errors[] = '<li>at least one letter</li>';
					}

					if( $pass_errors ){

						Alert::error( sprintf( 'The new password does not meet the following requirements:<ul>%s</ul>', join( '', $pass_errors ) ) );

					}

				} elseif( 0 !== strcmp( $password, (string) $password_confirm ) ){

					$response['form_errors'][] = 'password';
					$response['form_errors'][] = 'password_confirm';

					Alert::error( 'The passwords entered do not match.' );

				} else {

					$user->password = Password::hash( $password );
					$doUpdate |= 1;

				}

			}

			if( ! Alert::has() ){

				if( ! $doUpdate ){

					Alert::info( 'The profile details do not need updating.' );

				} elseif( $user->save() ){

					Alert::success( 'Profile updated successfully.' );

					$request->redirect( URL::BUILD( 'profile' ) );

				} else {

					Alert::error( 'Profile update failed. Please try again later.' );

				}

			}

		}

		echo $response;

	}

}
