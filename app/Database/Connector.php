<?php

namespace App\Database;

use App\Config;
use App\Database\Adaptors\MysqlAdaptor;
use App\Database\Interfaces\AdaptorInterface;
use DateTime;
use DateTimeZone;

class Connector {

	/**
	 * SQL Adaptor
	 */
	static private array $connections = [
		'admin'   => null,
		'default' => null
	];

	/**
	 * Database configuration
	 */
	static private $config = [];

	/**
	 * Prevent instantiating
	 */
	private function __construct(){}

	/**
	 * Get adaptor instance
	 */
	static public function getInstance( string $database = null ) : AdaptorInterface {

		/* activate reporting */
		$driver = new \mysqli_driver();
		$driver->report_mode = MYSQLI_REPORT_OFF;

		if( 'admin' == $database ){

			if( is_null( self::$connections['admin'] ) ){

				self::$connections['admin'] = new MysqlAdaptor(
					hostname: Config::get( 'database.admin.hostname' ),
					username: Config::get( 'database.admin.username' ),
					password: Config::get( 'database.admin.password' ),
					database: Config::get( 'database.admin.database' )
				);

				$tz = ( new DateTime( 'now', new DateTimeZone( Config::get( 'app.date.timezone' ) ) ) )->format( 'P' );

				self::$connections['admin']->query( "SET time_zone='$tz';" );

				self::$connections['admin']->query( 'SET SESSION sql_mode = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' );
				self::$connections['admin']->query( 'SET @@SESSION.sql_mode = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' );
				self::$connections['admin']->query( 'SET @@sql_mode = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' );

			}

			return self::$connections['admin'];

		} else {

			if( is_null( self::$connections['default'] ) ){

				self::$connections['default'] = new MysqlAdaptor(
					hostname: Config::get( 'database.hostname' ),
					username: Config::get( 'database.username' ),
					password: Config::get( 'database.password' ),
					database: Config::get( 'database.database' )
				);

				$tz = ( new DateTime( 'now', new DateTimeZone( Config::get( 'app.date.timezone' ) ) ) )->format( 'P' );

				self::$connections['default']->query( "SET time_zone='$tz';" );

				self::$connections['default']->query( 'SET SESSION sql_mode = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' );
				self::$connections['default']->query( 'SET @@SESSION.sql_mode = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' );
				self::$connections['default']->query( 'SET @@sql_mode = "STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION"' );

			}

			return self::$connections['default'];

		}

	}

}
