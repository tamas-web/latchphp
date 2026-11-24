<?php

namespace Plugins\Recovery\Models;

use App\Libraries\Model;
use App\Database\Connector as DB;

class Recovery extends Model {

	use \App\Traits\randomChars;
	use \App\Traits\encodeDecodeData;

	protected function newToken( string $email ) : ?string {

		$token = $this->randomChars( 255 );

		$ins = DB::getInstance()->prepare( '
			INSERT INTO password_resets
				( `email`, `token` )
			VALUES
				( ?, ? )
			ON DUPLICATE KEY UPDATE
				`token` = ?, `created_at` = NOW()
		' );
		$ins->bind_param( 'sss', $email, $token, $token );
		$ins->execute();

		if( $ins->errno ){

			return null;

		}

		$ins->close();

		return $token;

	}

	protected function userAndToken( string $key ) : ?array {

		if( ! ctype_xdigit( $key ) ){

			return null;

		}

		$data = explode( '|', self::decode( $key ) );

		if( 2 !== count( $data ) ){

			return null;

		}

		list( $email, $token ) = $data;

		$now = date( 'Y-m-d H:i:s' );

		$sel = DB::getInstance()->prepare( '
			SELECT
				id, email, token
			FROM password_resets
			WHERE
				email = ? AND
				token = ? AND
				created_at + INTERVAL 1 DAY >= STR_TO_DATE( ?, "%Y-%m-%d %H:%i:%s" )
			LIMIT 1
		' );

		$sel->bind_param( 'sss', $email, $token, $now );
		$sel->execute();
		$sel->bind_result( $id, $email, $token );
		$sel->store_result();
		$sel->fetch();

		$count = $sel->num_rows;

		$sel->close();

		if( ! $count ){

			return null;

		}

		return [
			'id'    => $id,
			'email' => $email,
			'token' => $token,
		];

	}

	protected function deleteToken( int $id ) : bool {

		$del = DB::getInstance()->prepare( '
			DELETE FROM password_resets
			WHERE
				id = ?
			LIMIT 1
		' );
		$del->bind_param( 'i', $id );
		$del->execute();

		$errors = $del->errno;

		$del->close();

		return ! $errors;

	}

}
