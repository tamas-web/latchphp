<?php

namespace App\Libraries;

use App\Constants;
use App\Database\Connector as DB;
use App\Libraries\{ UserMeta, UserPermission };

class User implements Constants {

	use \App\Traits\bitOptions;

	protected int $id = 0;

	protected int $roleId = 0;

	protected array $data = [];

	protected UserMeta $meta;

	protected UserPermission $permissions;

	protected ?array $role = null;

	/**
	 * Use only findById, findByEmail, findByPhone statically
	 * Never instantiate this class publicly
	 */
	protected function __construct(){}

	public static function __callStatic( $method, $arguments ) : ?self {

		return ( new self )->$method( ...$arguments );

	}

	private function findBy( string $by, mixed $value ) : ?self {

		$sql = 'SELECT
				`id`, `name`, `surname`, `phone`, `email`, `public_key`, `private_key`,
				`password`, `options`, `created_at`, `updated_at`,
				`current_login`, `last_login`, `last_seen`, `id_role`
			FROM users
			WHERE
				%s
			LIMIT 1';

		if( 'id' === $by ){

			$sql      = sprintf( $sql, 'id = ?' );
			$bindType = 'i';

		} elseif( 'email' === $by ){

			$sql      = sprintf( $sql, 'email = ?' );
			$bindType = 's';

		} elseif( 'phone' === $by ){

			$sql      = sprintf( $sql, 'phone = ?' );
			$bindType = 's';

		}

		$sel = DB::getInstance()->prepare( $sql );
		$sel->bind_param( $bindType, $value );

		$sel->execute();
		$sel->store_result();

		if( $sel->num_rows ){

			$sel->bind_result(
				$id, $name, $surname, $phone, $email, $public_key, $private_key,
				$password, $options, $created_at, $updated_at,
				$current_login, $last_login, $last_seen, $id_role
			);

			$sel->fetch();
			$sel->close();

			// set user id
			$this->id = $id;

			// set role id
			$this->roleId = $id_role;

			// set user options
			$this->options = $options;

			// set user data
			$this->data = [
				'name'          => $name,
				'surname'       => $surname,
				'phone'         => $phone,
				'email'         => $email,
				'public_key'    => $public_key,
				'private_key'   => $private_key,
				'created_at'    => $created_at,
				'updated_at'    => $updated_at,
				'current_login' => $current_login,
				'last_login'    => $last_login,
				'last_seen'     => $last_seen,
				'password'      => $password,
			];

			return $this;

		}

		if( isset( $sel ) && $sel instanceof \mysqli_stmt ){

			$sel->close();

		}

		return null;

	}

	protected function findById( int $id ) : ?self {

		return $this->findBy( 'id', $id );

	}

	protected function findByEmail( string $email ) : ?self {

		return $this->findBy( 'email', $email );

	}

	protected function findByPhone( string $phone ) : ?self {

		return $this->findBy( 'phone', $phone );

	}

	public function getRawData() : array {

		return $this->data;

	}

	public function &__get( mixed $key ) : mixed {

		if( 'id' === $key ){

			return $this->id;

		}

		if( 'options' === $key ){

			return $this->options;

		}

		$data = $this->data[ $key ] ?? null;

		return $data;

	}

	public function __set( string $key, mixed $value ) : void {

		$restricted = [
			'id',
			'options',
			'created_at',
			'updated_at',
			'current_login',
			'last_login',
			'last_seen',
		];

		if( ! in_array( $key, $restricted ) ){

			$this->data[ $key ] = $value ? $value : null;

		}

	}

	public function updateLastSeen(){

		// update last_seen
		$upd = DB::getInstance()->prepare( '
			UPDATE users SET
				`last_seen` = NOW()
			WHERE
				id = ?
			LIMIT 1
		' );
		$upd->bind_param( 'i', $this->id );
		$upd->execute();
		$upd->close();

		return $this;

	}

	public function updateCurrentLogin(){

		// update last_seen
		$upd = DB::getInstance()->prepare( '
			UPDATE users SET
				last_login = current_login,
				current_login = NOW()
			WHERE
				id = ?
			LIMIT 1
		' );
		$upd->bind_param( 'i', $this->id );
		$upd->execute();
		$upd->close();

		return $this;

	}

	public function save(){

		// update user
		$upd = DB::getInstance()->prepare( '
			UPDATE users SET
				`name` = ?, `surname` = ?, `phone` = ?, `email` = ?,
				`public_key` = ?, `private_key` = ?, `password` = ?,
				`options` = ?, `id_role` = ?, `updated_at` = NOW()
			WHERE
				id = ?
			LIMIT 1
		' );

		$upd->bind_param(
			'sssssssiii',
			$this->data['name'],
			$this->data['surname'],
			$this->data['phone'],
			$this->data['email'],
			$this->data['public_key'],
			$this->data['private_key'],
			$this->data['password'],
			$this->options,
			$this->roleId,
			$this->id
		 );
		$upd->execute();

		$errors = ! $upd->errno;

		$upd->close();

		return $errors;

	}

	public function isUser(){

		return !! $this->id;

	}

	/**
	 * Super admin = the ACCOUNT_ADMIN bit (god mode; bypasses every permission check).
	 * This is distinct from holding the DB "Administrator" role, which is just a
	 * normal role governed by its granted permissions.
	 */
	public function isSuperAdmin() : bool {

		return $this->hasOption( self::ACCOUNT_ADMIN );

	}

	public function getRoleId() : int {

		return $this->roleId;

	}

	public function setRole( int $idRole ) : self {

		$this->roleId = $idRole;
		$this->role   = null;

		return $this;

	}

	protected function loadRole() : array {

		if( null === $this->role ){

			$this->role = [
				'name' => '',
			];

			if( $this->roleId ){

				$sel = DB::getInstance()->prepare( '
					SELECT
						`name`
					FROM `roles`
					WHERE
						`id` = ?
					LIMIT 1
				' );
				$sel->bind_param( 'i', $this->roleId );
				$sel->execute();
				$sel->bind_result( $name );

				if( $sel->fetch() ){

					$this->role = [
						'name' => $name,
					];

				}

				$sel->close();

			}

		}

		return $this->role;

	}

	public function roleName() : string {

		return $this->loadRole()['name'];

	}

	public function can( string $permission ) : bool {

		if( $this->isSuperAdmin() ){

			return true;

		}

		$permissions = $this->permissions();

		if( null !== $permissions->{ $permission } ){

			return true;

		}

		// a held parent group encapsulates its actions: 'users' satisfies 'users.edit'
		$group = explode( '.', $permission )[0];

		return $group !== $permission && null !== $permissions->{ $group };

	}

	public function canAnyGroup( string ...$groups ) : bool {

		return $this->isSuperAdmin() || $this->permissions()->hasAnyInGroup( $groups );

	}

	public function isEnabled() : bool {

		return $this->hasOption( self::ACCOUNT_ENABLED );

	}

	public function meta() : UserMeta {

		if( ! isset( $this->meta ) ){

			$this->meta = new UserMeta( $this->id );

		}

		return $this->meta;

	}

	public function permissions() : UserPermission {

		if( ! isset( $this->permissions ) ){

			$this->permissions = new UserPermission( $this->roleId );

		}

		return $this->permissions;

	}

}
