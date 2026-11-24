<?php

namespace App\Http;

use App\Config;
use App\Constants;

class Cookie implements Constants {

	static public function set( string $key, string $value = '', int $expiration = self::DAY ) : void {

		setcookie( Config::get( 'app.cookie.prefix' ) . $key, $value, [
			'expires'  => time() + $expiration,
			'path'     => '/',
			'domain'   => Config::get( 'app.cookie.domain' ),
			'secure'   => true,
			'httponly' => true,
			'samesite' => 'Lax',
		] );

	}

	static public function delete( string $key ) : void {

		self::set( $key, '', - self::DAY );

	}

}
