<?php

namespace App\Bootables;

use App\Config;
use App\ErrorHandlers\HandlerException;
//~ use App\ErrorHandlers\Messages;

/**
 * Application session manager
 */
class SessionManager {

	/**
	 * Use dot notation to access arrays
	 */
	use \App\Traits\DotKeys;

	/**
	 * Prevent application from initializing session multiple times
	 */
	protected static $is_initiated = false;

	/**
	 * Instance of session
	 */
	protected static $_instance;

	/**
	 * Bootup
	 */
	public function boot() : void {
		try {
			$this->initSession();
		} catch( HandlerException $e ){
			echo $e->getMessage();
			exit;
		}
	}

	/**
	 * Get session instance
	 */

	static public function getInstance(){
		return self::$_instance;
	}

	/**
	 * Initiate session
	 */
	public function initSession() : ?self {

		if( self::$is_initiated ){

			return $this;

		}

		$settings = Config::get( 'app.session' );

		if( isset( $settings['save_path'] ) ){

			if( ! file_exists( $settings['save_path'] ) ){

				mkdir( directory: $settings['save_path'], recursive: true );

			}

			if( ! is_writable( $settings['save_path'] ) ){

				throw new HandlerException( 'Session unable to write to save path.' );

			}

		}

		session_start( $settings );

		if( ! isset( $_SESSION['user'] ) ){
			$_SESSION['user'] = [];
		}

		self::$_instance = $this;

		return $this;

	}

	/**
	 * Regenerate the session id (call on login / privilege change)
	 */
	public function regenerate( bool $deleteOld = true ) : self {

		if( \PHP_SESSION_ACTIVE === session_status() ){

			session_regenerate_id( $deleteOld );

		}

		return $this;

	}

	public function add( $key, $value ){

		self::setDotKeyValue( $key, $value, $_SESSION['user'] );

	}

	public function get( string $key ){

		return self::getDotKeyValue( $key, $_SESSION['user'] );

	}

	public function remove( string $key ){

		unset( $_SESSION['user'][ $key ] );

	}

	public function exists( string $key ){

		return ! is_null( self::get( $key ) );

	}

	public function error(){

		//~ return Messages::getInstance();

	}

	/**
	 * Get session value
	 */
	public function __get( $key ) : mixed {
		return $_SESSION['user'][ $key ] ?? null;
	}

	/**
	 * Set session value
	 */
	public function __set( $key, $value ) : void {
		$_SESSION['user'][ $key ] = $value;
	}

	/**
	 * Set session value
	 */
	public function __unset( $key ) : void {
		unset( $_SESSION['user'][ $key ] );
	}

	/**
	 * Is session value set
	 */
	public function __isset( $key ) : bool {
		return array_key_exists( $key, $_SESSION['user'] );
	}

}
