<?php

namespace App\Libraries;

use App\Config;
use App\Http\Request;

class Nonce {

	const FIELD  = '_nonce';
	const HEADER = 'X-CSRF-Token';

	static function create( string $action = '' ) : string {

		$time = time();

		return $time . ':' . self::hash( $action, $time );

	}

	static function verify( string $action = '', string $nonce = '' ) : bool {

		if( ! $nonce || ! str_contains( $nonce, ':' ) ){

			return false;

		}

		[ $time, $hash ] = explode( ':', $nonce, 2 );

		if( ! ctype_digit( $time ) ){

			return false;

		}

		$time = (int) $time;

		if( ( time() - $time ) >= (int) Config::get( 'app.nonce.ttl' ) ){

			return false;

		}

		return hash_equals( self::hash( $action, $time ), $hash );

	}

	protected static function hash( string $action, int $time ) : string {

		$user = Request::getInstance()->user();

		$binding = $user->isUser()
			? sprintf( 'u%u:%s', $user->id, $user->private_key )
			: sprintf( 's:%s', session_id() );

		$payload = implode( '|', [ $time, $binding, $action ] );

		return hash_hmac( 'sha256', $payload, (string) Config::get( 'app.nonce.salt' ) );

	}

}
