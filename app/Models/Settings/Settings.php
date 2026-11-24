<?php

namespace App\Models\Settings;

use App\Database\Connector as DB;
use App\Libraries\Model;

class Settings extends Model {

	use \App\Traits\encodeDecodeJSON;

	private static array $settings;

	protected function all() : array {

		if( ! isset( self::$settings ) ){

			self::$settings = [];

			$sel = DB::getInstance()->prepare( '
				SELECT
					`key`, `value`
				FROM `settings`
			' );
			$sel->execute();
			$sel->bind_result( $key, $value );

			while( $sel->fetch() ){

				self::$settings[ $key ] = $this->decode( $value, true );

			}

			$sel->close();

		}

		return self::$settings;

	}

	protected function save( string $key, mixed $value ) : bool {

		$encoded = $this->encode( $value );

		$ins = DB::getInstance()->prepare( '
			INSERT INTO `settings`
				( `key`, `value` )
			VALUES
				( ?, ? )
			ON DUPLICATE KEY UPDATE
				`value` = VALUES( `value` )
		' );
		$ins->bind_param( 'ss', $key, $encoded );
		$ins->execute();

		$errno = $ins->errno;

		if( ! $errno ){

			self::$settings[ $key ] = $value;

		}

		$ins->close();

		return ! $errno;

	}

}
