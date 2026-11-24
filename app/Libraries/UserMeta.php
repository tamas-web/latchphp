<?php

namespace App\Libraries;

use App\Constants;
use App\Database\Connector as DB;

class UserMeta implements Constants {

	use \App\Traits\encodeDecodeJSON;

	protected int $id_user = 0;

	protected array $meta_ids  = [];
	protected array $meta_data = [];

	public function __construct( int $id_user ){

		$this->id_user = $id_user;

		$this->preload_meta();

	}

	protected function preload_meta(){

		$sel = DB::getInstance()->prepare( '
			SELECT
				id, meta_key, meta_value
			FROM user_meta
			WHERE
				id_user = ?
		' );
		$sel->bind_param( 'i', $this->id_user );
		$sel->execute();

		$sel->bind_result( $meta_id, $meta_key, $meta_value );

		while( $sel->fetch() ){

			$this->meta_ids[ $meta_key ]  = $meta_id;
			$this->meta_data[ $meta_key ] = $this->decode( $meta_value );

		}

		$sel->close();

	}

	public function __get( string $meta_key ){

		// we could disable the preloading of meta and make here queries
		// for each meta_key and cache it, this method will result in many query executions
		// hence why it's more efficent to preload it for small amount of data

		return $this->meta_data[ $meta_key ] ?? null;

	}

	public function delete( string $meta_key ) : bool {

		if( ! isset( $this->meta_ids[ $meta_key ] ) ){

			return false;

		}

		$del = DB::getInstance()->prepare( '
			DELETE FROM `user_meta`
			WHERE
				`id` = ? AND
				`id_user` = ?
			LIMIT 1
		' );
		$del->bind_param( 'ii', $this->meta_ids[ $meta_key ], $this->id_user );
		$del->execute();

		$errno = $del->errno;

		$del->close();

		if( ! $errno ){

			unset( $this->meta_data[ $meta_key ], $this->meta_ids[ $meta_key ] );

		}

		// negate
		return ! $errno;

	}

	public function save( string $meta_key, mixed $meta_value ) : bool {

		$encoded = $this->encode( $meta_value );

		$errno   = 0;

		// we have meta id, so we update
		if( isset( $this->meta_ids[ $meta_key ] ) ){

			$upd = DB::getInstance()->prepare( '
				UPDATE `user_meta` SET
					`meta_value` = ?
				WHERE
					`id` = ? AND
					`id_user` = ?
				LIMIT 1
			' );
			$upd->bind_param( 'sii', $encoded, $this->meta_ids[ $meta_key ], $this->id_user );
			$upd->execute();

			$errno = $upd->errno;

			$upd->close();

		// we need to insert new value
		} else {

			$ins = DB::getInstance()->prepare( '
				INSERT INTO `user_meta`
					( `id_user`, `meta_key`, `meta_value` )
				VALUES
					( ?, ?, ? )
			' );
			$ins->bind_param( 'iss', $this->id_user, $meta_key, $encoded );
			$ins->execute();

			$errno = $ins->errno;

			if( ! $errno ){

				$this->meta_ids[ $meta_key ]  = $ins->insert_id;

			}

			$ins->close();

		}

		if( ! $errno ){

			$this->meta_data[ $meta_key ] = $meta_value;

		}

		// negate
		return ! $errno;

	}

}
