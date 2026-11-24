<?php

namespace App\Models;

use App\Config;
use App\Database\Connector as DB;
use App\Html\Paginator;
use App\Http\Request;
use App\Libraries\Model;
use App\Libraries\Password;
use App\Libraries\User;
use App\Models\Settings\Role;
use StdClass;

class Users extends Model {

	use \App\Traits\humanReadableTimePassedSince;
	use \App\Traits\randomChars;

	protected function getPaginatedUsers( int $page, bool $showActiveOnly = false ) : StdClass {

		// data
		$data = [
			'paginator' => null,
			'items'     => [],
			'limit'     => (int) Config::get( 'settings.app.pagination.limit' ) ?: 25,
			'query'     => [],
		];

		$limit  = Request::get( 'limit', fn( $limit ) => abs( intval( $limit ) ) );
		$q      = Request::get( 'q', 'strip_tags' );
		$role   = Request::get( 'role', fn( $role ) => Role::validate( (int) $role ) );
		$status = Request::get( 'status', 'strip_tags' );

		// page size ( length menu: 25 / 50 / 100 )
		if( in_array( $limit, [ 25, 50, 100 ], true ) ){
			$data['limit'] = $limit;
		}

		// extra parameters
		$params = [];

		// search ( name / surname / email / phone )
		if( $q ){

			$escaped = DB::getInstance()->real_escape_string( $q );

			$search = [
				sprintf( 'name LIKE "%%%1$s%%"', $escaped ),
				sprintf( 'surname LIKE "%%%1$s%%"', $escaped ),
				sprintf( 'email LIKE "%%%1$s%%"', $escaped ),
			];

			if( Config::get( 'settings.users.phone.enabled' ) ){

				$search[] = sprintf( 'phone LIKE "%%%1$s%%"', $escaped );

			}

			$params[] = sprintf( '( %s )', join( ' OR ', $search ) );

		}

		// role
		if( $role ){

			$params[] = sprintf( 'id_role = %u', $role );

		}

		// status ( footer column filter )
		if( 'active' === $status ){
			$params[] = sprintf( '( options & %1$u ) = %1$u', self::ACCOUNT_ENABLED );
		} elseif( 'inactive' === $status ){
			$params[] = sprintf( '( options & %1$u ) != %1$u', self::ACCOUNT_ENABLED );
		}

		// active only ( header switch, passed in by the controller )
		if( $showActiveOnly ){

			$params[] = sprintf( '( options & %1$u ) = %1$u', self::ACCOUNT_ENABLED );

		}

		// join params
		$params = join( ' AND ', $params );
		if( $params ){
			$params = sprintf( ' AND %s', $params );
		}

		// count users
		$sel = DB::getInstance()->prepare( sprintf( '
			SELECT
				COUNT( id )
			FROM users
			WHERE
				( 1 = 1 )
				%s
			LIMIT 1
		', $params ) );

		// do query
		$sel->execute();
		$sel->bind_result( $count );
		$sel->fetch();
		$sel->close();

		// create paginator
		$data['paginator'] = new Paginator( total: $count, limit: $data['limit'] );
		$data['paginator']->setCurrentPage( $page );
		$offset = $data['paginator']->getOffset();
		$limit  = $data['paginator']->getLimit();

		// do main query
		$sel = DB::getInstance()->prepare( sprintf( '
			SELECT
				id, name, surname, phone, email, options,
				(
					SELECT
						r.name
					FROM roles r
					WHERE
						r.id = users.id_role
					LIMIT 1
				) AS role_name
			FROM users
			WHERE
				( 1 = 1 )
				%s
			ORDER BY id DESC
			LIMIT ?, ?
		', $params ) );
		$sel->bind_param(
			'ii',
			$offset,
			$limit
		);
		$sel->execute();
		$sel->bind_result(
			$id, $name, $surname, $phone, $email, $options,
			$role_name
		);

		// loop through results
		while( $sel->fetch() ){

			$options = $this->newBitOperatedClass( $options );

			$data['items'][] = (object) [
				'id'          => (int) $id,
				'name'        => esc_html( $name ),
				'surname'     => esc_html( $surname ),
				'email'       => esc_html( $email ),
				'phone'       => esc_html( $phone ),
				'options'     => $options,
				'userRank'    => $role_name ? esc_html( $role_name ) : '-',
			];

		}

		$sel->close();

		return (object) $data;

	}

	protected function getAllUsers( bool $showActiveOnly = false ) : StdClass {

		// data
		$data = [
			'items' => [],
		];

		// extra parameters
		$params = [];

		// show active
		if( $showActiveOnly ){
			$params[] = sprintf( '( ( options & %1$u ) = %1$u )', self::ACCOUNT_ENABLED );
		}

		// join params
		$params = join( ' AND ', $params );
		if( $params ){
			$params = sprintf( ' AND %s', $params );
		}

		// do main query
		$sel = DB::getInstance()->prepare( $sql[] = sprintf( '
			SELECT
				id, name, surname, phone, email, options,
				( SELECT r.name FROM roles r WHERE r.id = users.id_role LIMIT 1 ) AS role_name
			FROM users
			WHERE
				( 1 = 1 )
				%s
			ORDER BY name, surname DESC
		', $params ) );
		$sel->execute();
		$sel->bind_result( $id, $name, $surname, $phone, $email, $options, $role_name );

		// loop through results
		while( $sel->fetch() ){

			$options = $this->newBitOperatedClass( $options );

			$data['items'][] = (object) [
				'id'          => (int) $id,
				'name'        => esc_html( $name ),
				'surname'     => esc_html( $surname ),
				'email'       => esc_html( $email ),
				'phone'       => esc_html( $phone ),
				'options'     => $options,
				'userRank'    => $role_name ? esc_html( $role_name ) : '-',
			];

		}

		$sel->close();

		return (object) $data;

	}

	protected function getUserIdsByIds( array $ids, bool $showActiveOnly = false ) : array {

		// data
		$data = [];

		if( $ids ){

			// extra parameters
			$params = [];

			// show active
			if( $showActiveOnly ){
				$params[] = sprintf( '( ( options & %1$u ) = %1$u )', self::ACCOUNT_ENABLED );
			}

			// ids
			$ids = array_unique( array_filter( array_map( fn( $id ) => (int) $id, $ids ) ) );
			$parameters = join( ',', array_fill( 0, count( $ids ), '?' ) );
			$params[] = sprintf( 'id IN ( %s )', $parameters );

			// join params
			$params = join( ' AND ', $params );
			if( $params ){
				$params = sprintf( ' AND %s', $params );
			}

			// do main query
			$sel = DB::getInstance()->prepare( $sql[] = sprintf( '
				SELECT
					id
				FROM users
				WHERE
					( 1 = 1 )
					%s
			', $params ) );
			$sel->bind_param( join( '', array_fill( 0, count( $ids ), 'i' ) ), ...$ids );
			$sel->execute();
			$sel->bind_result( $id );

			// loop through results
			while( $sel->fetch() ){

				$data[] = (int) $id;

			}

			$sel->close();

		}

		return $data;

	}

	protected function newBitOperatedClass( int $options ) : object {

		return new class( $options ){

			use \App\Traits\bitOptions;

			function __construct( $options ){
				$this->options = $options;
			}

		};

	}

	protected function getUserData( int $id ){

		$user = User::findById( $id );

		if( $user ){

			$options = $this->newBitOperatedClass( $user->options );

			$created_at_since = $user->created_at && strtotime( $user->created_at )
				? $this->passedTimeSince( strtotime( $user->created_at ) )
				: null;

			$updated_at_since = $user->updated_at && strtotime( $user->updated_at )
				? $this->passedTimeSince( strtotime( $user->updated_at ) )
				: null;

			$current_login_since = $user->current_login && strtotime( $user->current_login )
				? $this->passedTimeSince( strtotime( $user->current_login ) )
				: null;

			$last_login_since = $user->last_login && strtotime( $user->last_login )
				? $this->passedTimeSince( strtotime( $user->last_login ) )
				: null;

			$last_seen_since = $user->last_seen && strtotime( $user->last_seen )
				? $this->passedTimeSince( strtotime( $user->last_seen ) )
				: null;

			$data = (object) [
				'id'                  => $user->id,
				'name'                => esc_html( $user->name ),
				'surname'             => esc_html( $user->surname ),
				'phone'               => esc_html( $user->phone ),
				'email'               => esc_html( $user->email ),
				'options'             => $options,
				'created_at'          => esc_html( $user->created_at ),
				'created_at_since'    => esc_html( $created_at_since ),
				'updated_at'          => esc_html( $user->updated_at ),
				'updated_at_since'    => esc_html( $updated_at_since ),
				'current_login'       => esc_html( $user->current_login ),
				'current_login_since' => esc_html( $current_login_since ),
				'last_login'          => esc_html( $user->last_login ),
				'last_login_since'    => esc_html( $last_login_since ),
				'last_seen'           => esc_html( $user->last_seen ),
				'last_seen_since'     => esc_html( $last_seen_since ),
				'id_role'             => $user->getRoleId(),
				'role'                => esc_html( $user->roleName() ),
				'is_super_admin'      => $user->isSuperAdmin(),
			];

			return $data;

		}

		return null;

	}

	protected function checkIfPhoneExists( string $phone, int $userId ) : bool {

		$sel = DB::getInstance()->prepare( '
			SELECT
				phone
			FROM users
			WHERE
				phone = ? AND
				id != ?
			LIMIT 1
		' );
		$sel->bind_param( 'si', $phone, $userId );
		$sel->execute();

		$sel->store_result();

		$result = $sel->num_rows;

		$sel->close();

		return (bool) $result;

	}

	protected function checkIfEmailExists( string $email, int $userId ) : bool {

		$sel = DB::getInstance()->prepare( '
			SELECT
				email
			FROM users
			WHERE
				email = ? AND
				id != ?
			LIMIT 1
		' );
		$sel->bind_param( 'si', $email, $userId );
		$sel->execute();

		$sel->store_result();

		$result = $sel->num_rows;

		$sel->close();

		return (bool) $result;

	}

	protected function checkIfEmailPhoneExists( string $email, string $phone ) : array {

		$_check = [
			'email' => 0,
			'phone' => 0,
		];

		$sel = DB::getInstance()->prepare( '
			SELECT
				IF( email = ?, 1, 0 ),
				IF( phone = ?, 1, 0 )
			FROM users
			WHERE
				email = ? OR
				phone = ?
		' );
		$sel->bind_param( 'ssss', $email, $phone, $email, $phone );
		$sel->execute();
		$sel->bind_result( $has_email, $has_phone );

		while( $sel->fetch() ){

			$_check['email'] |= $has_email;
			$_check['phone'] |= $has_phone;

		}

		$sel->close();

		return $_check;

	}

	protected function createUser( string $name, ?string $surname, string $email, ?string $phone, string $password, int $options = self::ACCOUNT_ENABLED, int $idRole = 0 ) : ?User {

		// user
		$user = null;

		// generate keys
		$public_key  = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );
		$private_key = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );

		// hash password
		$hash = Password::hash( $password );

		// register
		$ins = DB::getInstance()->prepare( '
			INSERT INTO users
				(
					id_role, name, surname, phone,
					email, password,
					public_key, private_key,
					options, current_login
				)
			VALUES
				(
					?, ?, ?, ?,
					?, ?,
					?, ?,
					?, NOW()
				)
		' );

		$ins->bind_param(
			'isssssssi',
			$idRole, $name, $surname, $phone,
			$email, $hash,
			$public_key, $private_key,
			$options
		);

		$ins->execute();
		if( ! $ins->errno ){

			// get user
			$user = User::findById( $ins->insert_id );

		}

		$ins->close();

		return $user;

	}

	protected function deleteUser( int $id ) : bool {

		$del = DB::getInstance()->prepare( '
			DELETE
				u, um, nt, lg, pr
			FROM users u
			LEFT JOIN user_meta um       ON um.id_user = u.id
			LEFT JOIN notifications nt   ON nt.id_user = u.id
			LEFT JOIN logs lg            ON lg.id_user = u.id
			LEFT JOIN password_resets pr ON pr.email   = u.email
			WHERE
				u.id = ?
		' );

		$del->bind_param( 'i', $id );
		$del->execute();

		self::$errno = $del->errno;
		self::$error = $del->error;

		$del->close();

		return ! self::$errno;

	}

}
