<?php

namespace App\Models\Settings;

use App\Config;
use App\Constants;
use App\Database\Connector as DB;
use App\Html\Paginator;
use App\Http\Request;
use App\Libraries\Model;
use StdClass;

class Roles extends Model {

	protected function getPaginatedRoles( int $page ) : StdClass {

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

		// search ( name )
		if( $q ){

			$params[] = sprintf( 'r.`name` LIKE "%%%1$s%%"', DB::getInstance()->real_escape_string( $q ) );

		}

		// join params
		$params = join( ' AND ', $params );
		if( $params ){
			$params = sprintf( ' AND %s', $params );
		}

		// count roles
		$sel = DB::getInstance()->prepare( sprintf( '
			SELECT
				COUNT( r.`id` )
			FROM `roles` r
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
				r.`id`, r.`name`,
				(
					SELECT
						COUNT(*)
					FROM `role_permissions` rp
					WHERE
						rp.`id_role` = r.`id`
				) AS permission_count
			FROM `roles` r
			WHERE
				( 1 = 1 )
				%s
			ORDER BY r.`name` ASC
			LIMIT ?, ?
		', $params ) );
		$sel->bind_param(
			'ii',
			$offset,
			$limit
		);
		$sel->execute();
		$sel->bind_result( $id, $name, $permissionCount );

		// loop through results
		while( $sel->fetch() ){

			$data['items'][] = (object) [
				'id'              => (int) $id,
				'name'            => esc_html( $name ),
				'permissionCount' => (int) $permissionCount,
			];

		}

		$sel->close();

		return (object) $data;

	}

	protected function getAllRoles() : array {

		$data = [];

		$sel = DB::getInstance()->prepare( '
			SELECT
				r.`id`, r.`name`,
				(
					SELECT
						COUNT(*)
					FROM `role_permissions` rp
					WHERE
						rp.`id_role` = r.`id`
				) as permission_count
			FROM `roles` r
			ORDER BY r.`name` ASC
		' );

		$sel->execute();
		$sel->bind_result( $id, $name, $permissionCount );

		while( $sel->fetch() ){

			$data[] = (object) [
				'id'              => (int) $id,
				'name'            => esc_html( $name ),
				'permissionCount' => (int) $permissionCount,
			];

		}

		return $data;

	}

}