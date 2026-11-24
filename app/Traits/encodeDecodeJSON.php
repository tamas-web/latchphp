<?php

namespace App\Traits;

trait encodeDecodeJSON {

	protected function encode( mixed $var ) : mixed {

		if( is_array( $var ) || is_object( $var ) ){

			$var = json_encode( $var, JSON_INVALID_UTF8_IGNORE );

		} elseif( is_bool( $var ) ){

			$var = $var ? 'true' : 'false';

		} elseif( is_null( $var ) ){

			$var = 'null';

		}

		return $var;

	}

	protected function decode( mixed $var, bool $associative = false ) : mixed {

		if( is_numeric( $var ) ){
			return $var;
		}

		if( ! is_string( $var ) ){
			return $var;
		}

		if( strlen( $var ) < 2 ){
			return $var;
		}

		if( 'null' === $var ){
			return null;
		}

		if( 'true' === $var ){
			return true;
		}

		if( 'false' === $var ){
			return false;
		}

		if( '{' != $var[0] && '[' != $var[0] && '"' != $var[0] ){
			return $var;
		}

		$json_data = json_decode( json: $var, associative: $associative, flags:  JSON_INVALID_UTF8_IGNORE );

		if( is_null( $json_data ) ){
			return $var;
		}

		return $json_data;

	}

}
