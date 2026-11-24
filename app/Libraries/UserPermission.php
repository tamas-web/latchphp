<?php

namespace App\Libraries;

use App\Constants;
use App\Database\Connector as DB;

class UserPermission implements Constants {

	protected int $roleId = 0;

	protected array $permissionIds   = [];
	protected array $permissionNames = [];

	public function __construct( int $roleId ){

		$this->roleId = $roleId;

		$this->preloadPermissions();

	}

	protected function preloadPermissions(){

		$sel = DB::getInstance()->prepare( '
			SELECT
				p.`id`, p.`name`
			FROM `role_permissions` rp
            LEFT JOIN `permissions` p ON p.`id` = rp.`id_permission`
			WHERE
				rp.`id_role` = ?
		' );
		$sel->bind_param( 'i', $this->roleId );
		$sel->execute();

		$sel->bind_result( $permissionId, $permissionName );

		while( $sel->fetch() ){

			$this->permissionIds[ $permissionId ]   = $permissionName;
			$this->permissionNames[ $permissionName ] = $permissionId;

		}

		$sel->close();

	}

	public function __get( string $permissionName ){

		// we could disable the preloading of meta and make here queries
		// for each meta_key and cache it, this method will result in many query executions
		// hence why it's more efficient to preload it for small amount of data

		return $this->permissionNames[ $permissionName ] ?? null;

	}

	public function hasAnyInGroup( array $groups ) : bool {

		foreach( $this->permissionIds as $name ){

			if( in_array( explode( '.', $name )[0], $groups, true ) ){

				return true;

			}

		}

		return false;

	}

}
