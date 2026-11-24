<?php

namespace App\Models\Settings;

use App\Constants;
use App\Database\Connector as DB;
use App\Libraries\Model;
use StdClass;

class Role extends Model {

	protected function exists( ?string $name, int $id = 0 ) : bool {

		if( $id ){

			$sel = DB::getInstance()->prepare( '
				SELECT
					`id`, `name`
				FROM `roles`
				WHERE
					`name` = ? AND
					`id` != ?
			' );

			$sel->bind_param( 'si', $name, $id );

		} else {

			$sel = DB::getInstance()->prepare( '
				SELECT
					`id`, `name`
				FROM `roles`
				WHERE
					`name` = ?
			' );

			$sel->bind_param( 's', $name );

		}


		$sel->execute();
		$sel->store_result();

		$rowsFound = $sel->num_rows;

		$sel->close();

		return (bool) $rowsFound;

	}

	protected function add( string $name ) : int {

		$ins = DB::getInstance()->prepare( '
			INSERT INTO `roles`
				( `name` )
			VALUES
				( ? )
		' );
		$ins->bind_param( 's', $name );
		$ins->execute();
		$ins->store_result();

		$id = $ins->insert_id;

		$ins->close();

		return $id;

	}

	protected function get( int $id ) : ?stdClass {

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`, `name`
			FROM `roles`
			WHERE
				`id` = ?
			LIMIT 1
		' );
		$sel->bind_param( 'i', $id );
		$sel->execute();

		$sel->bind_result( $tid, $name );
		$sel->fetch();

		$sel->close();

		return $tid
			? (object)[
				'id'   => (int) $tid,
				'name' => esc_html( $name ),
			]
			: null;

	}

	protected function validate( int $id ) : int {

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`
			FROM `roles`
			WHERE
				`id` = ?
			LIMIT 1
		' );
		$sel->bind_param( 'i', $id );
		$sel->execute();

		$sel->bind_result( $tid );
		$sel->fetch();

		$sel->close();

		return (int) $tid;

	}

	protected function idByName( string $name ) : int {

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`
			FROM `roles`
			WHERE
				`name` = ?
			LIMIT 1
		' );
		$sel->bind_param( 's', $name );
		$sel->execute();

		$sel->bind_result( $id );
		$sel->fetch();

		$sel->close();

		return (int) $id;

	}

	protected function isAssigned( int $id ) : bool {

		$sel = DB::getInstance()->prepare( '
			SELECT
				COUNT(*)
			FROM `users`
			WHERE
				`id_role` = ?
			LIMIT 1
		' );
		$sel->bind_param( 'i', $id );
		$sel->execute();

		$sel->bind_result( $count );
		$sel->fetch();

		$sel->close();

		return !! $count;

	}

	protected function delete( int $id ) : bool {

		$del = DB::getInstance()->prepare( '
			DELETE FROM `roles`
			WHERE
				`id` = ?
		' );
		$del->bind_param( 'i', $id );
		$del->execute();

		self::$errno = $del->errno;
		self::$error = $del->error;

		$del->close();

		return ! self::$errno;

	}

	protected function update( int $id, array $data ) : bool {

		$name = $data['name'] ?? null;

		// update role name
		$upd = DB::getInstance()->prepare( '
			UPDATE `roles` SET
				`name` = ?
			WHERE
				id = ?
			LIMIT 1
		' );
		$upd->bind_param( 'si', $name, $id );
		$upd->execute();

		self::$errno = $upd->errno;
		self::$error = $upd->error;

		$upd->close();

		return ! self::$errno;

	}

}
