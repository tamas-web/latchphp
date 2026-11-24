<?php

namespace App\Models\Settings;

use App\Config;
use App\Constants;
use App\Database\Connector as DB;
use App\Html\Paginator;
use App\Http\Request;
use App\Libraries\Model;
use StdClass;

class Permissions extends Model {

	protected function getPaginatedPermissions( int $page ) : StdClass {

		// data
		$data = [
			'paginator' => null,
			'items'     => [],
			'limit'     => (int) Config::get( 'settings.app.pagination.limit' ) ?: 25,
			'query'     => [],
		];

		$limit = Request::get( 'limit', fn( $limit ) => abs( intval( $limit ) ) );
		$q     = Request::get( 'q', 'strip_tags' );

		// page size ( length menu: 25 / 50 / 100 )
		if( in_array( $limit, [ 25, 50, 100 ], true ) ){
			$data['limit'] = $limit;
		}

		// extra parameters
		$params = [];

		// search ( name / label )
		if( $q ){

			$params[] = sprintf( '
				(
					`name` LIKE "%%%1$s%%" OR
					`label` LIKE "%%%1$s%%"
				)', DB::getInstance()->real_escape_string( $q ) );

		}

		// join params
		$params = join( ' AND ', $params );
		if( $params ){
			$params = sprintf( ' AND %s', $params );
		}

		// count permissions
		$sel = DB::getInstance()->prepare( sprintf( '
			SELECT
				COUNT( `id` )
			FROM `permissions`
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
				`id`, `id_parent`, `name`, `label`
			FROM `permissions`
			WHERE
				( 1 = 1 )
				%s
			ORDER BY `name` ASC
			LIMIT ?, ?
		', $params ) );
		$sel->bind_param(
			'ii',
			$offset,
			$limit
		);
		$sel->execute();
		$sel->bind_result( $id, $idParent, $name, $label );

		// loop through results
		while( $sel->fetch() ){

			$data['items'][] = (object) [
				'id'       => (int) $id,
				'idParent' => (int) $idParent,
				'name'     => esc_html( $name ),
				'label'    => esc_html( $label ?: $name ),
			];

		}

		$sel->close();

		return (object) $data;

	}

	protected function getAllPermissions() : array {

		$data = [];

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`, `id_parent`, `name`, `label`
			FROM `permissions`
			ORDER BY `name` ASC
		' );

		$sel->execute();
		$sel->bind_result( $id, $idParent, $name, $label );

		while( $sel->fetch() ){

			$data[] = (object) [
				'id'       => (int) $id,
				'idParent' => (int) $idParent,
				'name'     => esc_html( $name ),
				'label'    => esc_html( $label ?: $name ),
			];

		}

		return $data;

	}

	protected function get( int $roleId ) : object {

		$data = [
			'permissionIds' => [],
			'permissions'   => [],
		];

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`, `id_role`, `id_permission`
			FROM `role_permissions`
			WHERE
				`id_role` = ?
		' );
		$sel->bind_param( 'i', $roleId );
		$sel->execute();
		$sel->bind_result( $id, $idRole, $idPermission );

		while( $sel->fetch() ){
			$data['permissionIds'][] = (int) $idPermission;
			$data['permissions'][] = (object) [
				'id'           => (int) $id,
				'idRole'       => (int) $idRole,
				'idPermission' => (int) $idPermission,
			];
		}

		$sel->close();

		return (object) $data;

	}

	protected function exists( array $permissions ) : bool {

		$permissions = array_values( array_unique( array_map( 'intval', $permissions ) ) );

		if( ! $permissions ){

			return true;

		}

		$in  = implode( ',', array_fill( 0, count( $permissions ), '?' ) );
		$sel = DB::getInstance()->prepare( "
			SELECT
				COUNT(*)
			FROM `permissions`
			WHERE
				`id` IN ( $in )
		" );
		$sel->bind_param( str_repeat( 'i', count( $permissions ) ), ...$permissions );
		$sel->execute();
		$sel->bind_result( $count );
		$sel->fetch();
		$sel->close();

		return (int) $count === count( $permissions );

	}

	protected function insert( int $idRole, int $idPermission ) : bool {

		$ins = DB::getInstance()->prepare( '
			INSERT INTO `role_permissions`
				( `id_role`, `id_permission` )
			VALUES
				( ?, ? )
		' );
		$ins->bind_param( 'ii', $idRole, $idPermission );
		$ins->execute();

		self::$errno = $ins->errno;
		self::$error = $ins->error;

		$ins->close();

		return ! self::$errno;

	}

	protected function deleteAll( int $idRole ) : bool {

		$del = DB::getInstance()->prepare( '
			DELETE FROM `role_permissions`
			WHERE
				id_role = ?
		' );
		$del->bind_param( 'i', $idRole );
		$del->execute();

		self::$errno = $del->errno;
		self::$error = $del->error;

		$del->close();

		return ! self::$errno;

	}

}