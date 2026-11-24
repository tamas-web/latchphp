<?php

namespace App\Bootables;

use App\Config;

/**
 * Setup some common PHP runtime defaults
 */
class AppSetup {

	/**
	 * Bootup
	 */
	public function boot() : void {

		// setup date timezone
		\date_default_timezone_set( Config::get( 'app.date.timezone' ) );

		// setup mb string encoding
		\mb_internal_encoding( Config::get( 'app.encoding' ) );
		\mb_http_output( Config::get( 'app.encoding' ) );
		\mb_regex_encoding( Config::get( 'app.encoding' ) );

	}

}
