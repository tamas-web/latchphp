<?php

namespace App\Http;

use App\Config;
use App\Filters\Filter;

class URL {

	static function BUILD( ?string $url = null, array $args = [], string $ampersand = '&amp;' ) : string {
		$get = $args
			? '?' . http_build_query( $args, '', $ampersand )
			: '';
		$path = ( is_null( $url ) || '' === $url ) ? '/' : $url;
		$path = Filter::apply( 'url.build', $path, $args );
		return self::BASE() . ltrim( $path, '/' ) . $get;
	}

	static function isAbsolute( string $url ) : bool {

		return (bool) preg_match( '~^(?:[a-z][a-z0-9+.\-]*:)?//~i', $url );

	}

	static function BASE() : string {

		return rtrim( Config::get( 'app.site.url' ), '/' ) . '/';

	}

}
