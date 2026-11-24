<?php

namespace App\Http;

use App\Auth\Auth;
use App\Config;
use App\Datatype\Collection;
use App\Views\View;

/**
 * Handle requests
 */
class Request {

	/**
	 * Use dot notation to access arrays
	 */
	use \App\Traits\DotKeys;
	use \App\Traits\MemoizesDotKeys;

	/**
	 * Request instance
	 */
	protected static Request $instance;

	/**
	 * Request parameters
	 */
	protected array $parameters = [];

	/**
	 * User data
	 */
	protected array $userData = [];

	/**
	 * User object
	 */
	protected Auth $user;

	/**
	 * View
	 */
	protected View $view;

	/**
	 * Current url
	 */
	protected static ?string $url = null;

	/**
	 * Constructor
	 */
	public function __construct(){

		self::$instance = $this;

		$this->user = new Auth;

		$this->view = new View;

	}

	/**
	 * Get request instance
	 */
	public static function getInstance(){

		if( is_null( self::$instance ) ){
			self::$instance = new self;
		}

		return self::$instance;

	}

	/**
	 * Retrieve cookies values
	 */
	public static function cookie( $key = null, $callback = null, ...$params ) : mixed {

		if( ! is_null( $key ) ){

			$key = Config::get( 'app.cookie.prefix' ) . $key;

		}

		return self::call(
			self::memo( 'cookie:' . $key, fn() => self::getDotKeyValue( $key, $_COOKIE ) ),
			$callback,
			$params
		);

	}

	/**
	 * Retrieve get values
	 */
	public static function get( $key = null, $callback = null, ...$params ) : mixed {

		return self::call(
			self::memo( 'get:' . $key, fn() => self::getDotKeyValue( $key, $_GET ) ),
			$callback,
			$params
		);

	}

	/**
	 * Retrieve post values
	 */
	public static function post( $key = null, $callback = null, ...$params ) : mixed {

		return self::call(
			self::memo( 'post:' . $key, fn() => self::getDotKeyValue( $key, $_POST ) ),
			$callback,
			$params
		);

	}

	/**
	 * Retrieve request values
	 */
	public static function request( $key = null, $callback = null, ...$params ) : mixed {

		return self::call(
			self::memo( 'request:' . $key, fn() => self::getDotKeyValue( $key, $_REQUEST ) ),
			$callback,
			$params
		);

	}

	/**
	 * Retrieve file values
	 */
	public static function files( $key = null, $callback = null, ...$params ) : mixed {

		return self::call(
			self::memo( 'files:' . $key, fn() => self::getDotKeyValue( $key, $_FILES ) ),
			$callback,
			$params
		);

	}

	/**
	 * Retrieve global values
	 */
	public static function globals( $key = null, $callback = null, ...$params ) : mixed {

		return self::call( self::getDotKeyValue( $key, $GLOBALS ), $callback, $params );

	}

	/**
	 * Retrieve server values
	 */
	public static function server( $key = null, $callback = null, ...$params ) : mixed {

		return self::call(
			self::memo( 'server:' . strtoupper( $key ), fn() => self::getDotKeyValue( strtoupper( $key ), $_SERVER ) ),
			$callback,
			$params
		);

	}

	/**
	 * Retrieve different type of variables
	 */
	protected static function getVariable( $which, $key = null ) : mixed {

		switch( $which ){

			case 'cookie':
				return is_null( $key ) ? $_COOKIE : ( $_COOKIE[ Config::get( 'app.cookie.prefix' ) . $key ]  ?? null );

			case 'get':
				return is_null( $key ) ? $_GET     : ( $_GET[ $key ]     ?? null );

			case 'post':
				return is_null( $key ) ? $_POST    : ( $_POST[ $key ]    ?? null );

			case 'request':
				return is_null( $key ) ? $_REQUEST : ( $_REQUEST[ $key ] ?? null );

			case 'files':
				return is_null( $key ) ? $_FILES   : ( $_FILES[ $key ]   ?? null );

			case 'server':
				return is_null( $key ) ? $_SERVER  : ( $_SERVER[ strtoupper( $key ) ]  ?? null );

			case 'globals':
				return is_null( $key ) ? $GLOBALS  : ( $GLOBALS[ $key ]  ?? null );

			default:
				return null;

		}
	}

	/**
	 * Call user callback for retrieved value
	 */
	protected static function call( $value, $callback, $params ) : mixed {

		if( is_callable( $callback ) ){

			array_unshift( $params, $value );

			return @call_user_func_array( $callback, $params );

		}

		return $value;

	}

	/**
	 * Retrieve variables object style
	 */
	public function __get( $what ) : mixed {

		if( 'url' === $what ){

			return self::$url;

		}

		return self::arrayToObject( self::getVariable( $what ) );

	}

	/**
	 * Prevent setting variables dynamically
	 */
	public function __set( $key, $value ) : void {}

	/**
	 * Convert array to object
	 */
	protected function arrayToObject( $array ) : ?object {

		return json_decode(
			json: json_encode(
				value: $array,
				flags: JSON_FORCE_OBJECT | JSON_INVALID_UTF8_SUBSTITUTE,
				depth: 32
			),
			flags: JSON_BIGINT_AS_STRING | JSON_INVALID_UTF8_SUBSTITUTE,
			associative: false,
			depth: 32
		);

	}

	/**
	 * Set current url once
	 */
	public function setUrl( string $url ) : self {

		// set only once
		if( is_null( self::$url ) ){

			self::$url = $url;

		}

		return $this;

	}

	/**
	 * Get request url
	 */
	public static function getURL() : ?string {

		return self::$url;

	}

	/**
	 * Set request parameters
	 */
	public function setParameters( array $params = [] ) : self {

		$this->parameters = $params;

		return $this;

	}

	/**
	 * Get request parameters
	 */
	public function getParameters() : array {

		return $this->parameters;

	}

	/**
	 * Get request parameter by index
	 */
	public function getParameter( int $index ) : mixed {

		return $this->parameters[ $index ] ?? null;

	}

	/**
	 * Check if has parameters
	 */
	public function hasParameters() : bool {

		return !! $this->parameters;

	}

	/**
	 * Redirect app
	 */
	public function redirect( $url, int $code = 302 ) : void {

		http_response_code( $code );

		header( 'Location: ' . $url );

		exit;

	}

	/**
	 * Get user
	 */
	public function user(){

		return $this->user;

	}

	/**
	 * Get view
	 */
	public function view(){

		return $this->view;

	}

}
