<?php

namespace App;

/**
 * Get configurations for application.
 */
final class Config {

	/**
	 * Use dot notation to access arrays
	 */
	use \App\Traits\DotKeys;
	use \App\Traits\MemoizesDotKeys;

	/**
	 * Configuration storage
	 */
	protected static array $storage = [];

	/**
	 * Bootup
	 */
	public function boot() : void {
		$this->loadConfiguration();
	}

	/**
	 * Load all configuration files
	 */
	private function loadConfiguration() : void {

		self::flushDotCache();

		foreach( glob( APP_DIR . '/config/*.php' ) as $file ){
			$filename = str_replace( '.php', '', basename( $file ) );
			self::$storage[ $filename ] = require $file;
		}

	}

	/**
	 * Get config
	 */
    static public function get( $key ) : mixed {

		return self::memo( $key, fn() => self::getDotKeyValue( $key, self::$storage ) );

    }

	/**
	 * Set config
	 */
    static public function set( $key, $item ) : void {

		self::setDotKeyValue( $key, $item, self::$storage );

		self::evictSubtree( $key );

    }

}
