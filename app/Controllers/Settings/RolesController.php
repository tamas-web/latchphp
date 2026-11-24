<?php

namespace App\Controllers\Settings;

use App\Alerts\Alert;
use App\Datatype\Response;
use App\Database\Connector as DB;
use App\Http\{ Request, URL };
use App\Html\Link;
use App\Filters\Filter;
use App\Libraries\{ Controller };
use App\Models\{ Settings\Role, Settings\Roles, Settings\Permissions };

class RolesController extends Controller {

	protected $pageTitle = 'Roles and permissions';

	public function rolesAndPermissions( Request $request, $route ) : void {

		Filter::add( 'page_title', fn() => 'Roles and permissions' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/roles-permissions', 'Roles and permissions' ),
		] );

		$request->view()
			->body()
				->add( 'settings.roles.index' );

		$request->view()->render();

	}

	public function roles( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Roles' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/roles-permissions', 'Roles and permissions' ),
			new Link( '/admin/settings/roles-permissions/roles', 'Roles' ),
		] );

		$request->view()
			->body()
				->data( [
					'roles' => Roles::getPaginatedRoles( page: $request->get( 'page', 'intval' ) ),
				] )
				->add( 'settings.roles.roles' );

		$request->view()->render();

	}

	public function roleAddForm( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Add new role' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/roles-permissions', 'Roles and permissions' ),
			new Link( '/admin/settings/roles-permissions/roles', 'Roles' ),
			new Link( '/admin/settings/roles-permissions/roles/add', 'Add new role' ),
		] );

		$request->view()
			->js( [
				'settings-roles' => 'assets/js/settings/roles.js',
			] );

		$request->view()
			->body()
				->add( 'settings.roles.role-add' );

		$request->view()->render();

	}

	public function roleAddApi( Request $request ) : void {

		$response = new Response;

		$name = $request->post( 'name', fn( $name ) => trim( sanitize_string( strip_tags( $name ) ) ) );

		if( ! $name ){

			Alert::error( 'Please provide a name for the role.' );

		}

		if( ! Alert::has() ){

			if( Role::exists( $name ) ){

				Alert::error( 'This role already exists' );

			} else {

				$roleId = Role::add( $name );

				if( $roleId ){

					$request->redirect( sprintf( '/admin/settings/roles-permissions/roles/%u/edit', $roleId ) );

				} else {

					Alert::error( 'We could not add the role. Please try again later.' );

				}

			}

		}

		echo $response;

	}

	public function roleDeleteForm( Request $request ) : void {

		$roleId = (int) $request->getParameter( 0 );

		$role   = Role::get( $roleId );

		if( $role ){

			Filter::add( 'page_title', fn() => 'Delete role' );

			Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
				...$breadcrumbs,
				new Link( '/admin', 'Administration' ),
				new Link( '/admin/settings/roles-permissions', 'Roles and permissions' ),
				new Link( '/admin/settings/roles-permissions/roles', 'Roles' ),
				new Link(
					sprintf( '/admin/settings/roles-permissions/roles/%u/delete', $role->id ),
					sprintf( 'Delete role %s', $role->name )
				),
			] );

			$request->view()
				->body()
					->data( [
						'role' => $role,
					] )
					->add( 'settings.roles.role-delete' );

			$request->view()->render();

		} else {

			$request->redirect( '/admin/settings/roles-permissions/roles' );

		}

	}

	public function roleDeleteApi( Request $request ) : void {

		$response = new Response;

		$roleId = (int) $request->getParameter( 0 );

		$role   = Role::get( $roleId );

		if( $role ){

			if( ! Role::isAssigned( $role->id ) ){

				DB::getInstance()->autocommit( false );

				if( Role::delete( $role->id ) && Permissions::deleteAll( $role->id ) ){

					DB::getInstance()->autocommit( true );

					Alert::success( 'Role deleted successfully!' );

					$request->redirect( URL::BUILD( 'admin/settings/roles-permissions/roles' ) );

				} else {

					DB::getInstance()->rollback();
					DB::getInstance()->autocommit( true );

					Alert::error( 'Action failed. Please try again later.' );

				}

			} else {

				Alert::error( 'This role is in use.' );

			}

		} else {

			Alert::error( 'This role does not exist.' );

		}

		echo $response;

	}

	public function roleEditForm( Request $request ) : void {

		$roleId = (int) $request->getParameter( 0 );

		$role   = Role::get( $roleId );

		if( $role ){

			Filter::add( 'page_title', fn() => 'Edit role' );

			Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
				...$breadcrumbs,
				new Link( '/admin', 'Administration' ),
				new Link( '/admin/settings/roles-permissions', 'Roles and permissions' ),
				new Link( '/admin/settings/roles-permissions/roles', 'Roles' ),
				new Link(
					sprintf( '/admin/settings/roles-permissions/roles/%u/edit', $role->id ),
					sprintf( 'Edit role %s', $role->name )
				),
			] );

			$request->view()
				->js( [
					'settings-roles' => [ 'assets/js/settings/roles.js', 'type="module"' ],
				] );

			$request->view()
				->body()
					->data( [
						'role'        => $role,
						'permissions' => (object) [
							'all'       => Permissions::getAllPermissions(),
							'allocated' => Permissions::get( $role->id ),
						],
					] )
					->add( 'settings.roles.role-edit' );

			$request->view()->render();

		} else {

			$request->redirect( '/admin/settings/roles-permissions/roles' );

		}

	}

	public function roleEditApi( Request $request ) : void {

		$response = new Response;

		$roleId = (int) $request->getParameter( 0 );

		$role   = Role::get( $roleId );

		if( $role ){

			$name        = $request->post( 'name', fn( $name ) => trim( sanitize_string( strip_tags( $name ) ) ) );
			$permissions = $request->post( 'permission', fn( $permissions ) => array_unique( array_map( fn( $id ) => (int) $id, (array) $permissions ) ) );

			if( ! $name ){

				Alert::error( 'Please provide a name for the role.' );

			}

			if( Role::exists( $name, $role->id ) ){

				Alert::error( 'This role already exists' );

			} elseif( ! Alert::has() ){

				DB::getInstance()->autocommit( false );

				$updated = Role::update( $role->id, [
					'name' => $name,
				] );

				if( $updated ){

					if( ! Permissions::exists( $permissions ) ){

						DB::getInstance()->rollback();
						DB::getInstance()->autocommit( true );

						Alert::error( 'One of the permissions does not exist.' );

					} else {

						// delete all permissions
						if( Permissions::deleteAll( $role->id ) ){

							// insert permissions
							$failedPermissions = 0;

							foreach( $permissions as $idPermission ){

								if( ! Permissions::insert( $role->id, $idPermission ) ){

									// check if not duplicate error
									if( 1062 !== Permissions::getLastErrno() ){

										$failedPermissions |= 1;

									}

								}

							}

							if( ! $failedPermissions ){

								Alert::success( 'Role updated successfully!' );

								DB::getInstance()->autocommit( true );

								$request->redirect( URL::BUILD( 'admin/settings/roles-permissions/roles' ) );

							} else {

								DB::getInstance()->rollback();
								DB::getInstance()->autocommit( true );

								Alert::error( 'We could not add one or more permissions. Please try again later.' );

							}

						} else {

							DB::getInstance()->rollback();
							DB::getInstance()->autocommit( true );

							Alert::error( 'Resetting the permissions failed. Please try again later.' );

						}

					}

				} else {

					DB::getInstance()->rollback();
					DB::getInstance()->autocommit( true );

					Alert::error( 'Action failed. Please try again later.' );

				}

			}

		} else {

			Alert::error( 'This role does not exist.' );

		}

		echo $response;

	}

	public function permissions( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Permissions' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/roles-permissions', 'Roles and permissions' ),
			new Link( '/admin/settings/roles-permissions/permissions', 'Permissions' ),
		] );

		$request->view()
			->body()
				->data( [
					'permissions' => Permissions::getPaginatedPermissions( page: $request->get( 'page', 'intval' ) ),
				] )
				->add( 'settings.roles.permissions' );

		$request->view()->render();

	}

}
