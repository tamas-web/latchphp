<?php

/**
 * Application container accessors.
 * Resolve the booted Bootloader (the service container) or one of its booted services by key,
 * without importing the concrete class.
 */

use App\Bootloader;

if( ! function_exists( 'app' ) ){

	/**
	 * The Bootloader itself, or a booted service by key.
	 *
	 *   app()            -> the Bootloader (container)
	 *   app( 'session' ) -> the booted SessionManager
	 *   app( 'router' )  -> the booted Router, ...
	 */
	function app( ?string $key = null ) : ?object {

		$app = Bootloader::getInstance();

		return is_null( $key ) ? $app : $app->{ $key };

	}

}

if( ! function_exists( 'session' ) ){

	/**
	 * The booted SessionManager.
	 */
	function session() : ?object {

		return app( 'session' );

	}

}
