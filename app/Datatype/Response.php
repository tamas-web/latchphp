<?php

namespace App\Datatype;

use ArrayAccess;
use ArrayIterator;
use Stringable;
use App\Alerts\Alert;

/**
 * Response data structure
 */
class Response implements ArrayAccess, Stringable {

    /**
     * Private container for all data
     * @param array
     */
	private $container = [];

	public function __construct(){

		$this->container['alerts']      = [];
		$this->container['form_errors'] = new ArrayIterator;
		$this->container['form_reset']  = false;

	}

    /**
     * Set data
     */
	public function offsetSet( $offset, $value ) : void {

		if( 'alerts' === $offset ){

			// do nothing

		} elseif( is_null( $offset ) ){

			$this->container[] = $value;

		} else {

			$this->container[ $offset ] = $value;

		}

	}

    /**
     * Check if offset/key exists
     */
	public function offsetExists( $offset ) : bool {

		return isset( $this->container[ $offset ] );

	}

    /**
     * Unset data
     */
	public function offsetUnset( $offset ) : void {

		unset( $this->container[ $offset ] );

	}

    /**
     * Get data
     */
	public function offsetGet( $offset ) : mixed {

		return $this->container[ $offset ] ?? null;

	}

	/**
	 * JSON Output
	 */
	public function __toString(){

		/**
		 * JSON Encode options
		 */
		$jsonOptions = JSON_INVALID_UTF8_IGNORE;

		if( 'development' === \APP_ENVIRONMENT ){

			$jsonOptions |= \JSON_PRETTY_PRINT;

		}

		/**
		 * ArrayIterator to array
		 */
		$this->container['form_errors'] = (array) $this->container['form_errors'];

		/**
		 * Get all alerts and clear them
		 */
		$this->container['alerts']      = Alert::getAll();

		/**
		 * Clear alerts
		 */
		Alert::clear();

		/**
		 * Set headers
		 */
		header( 'Content-type: application/json; charset=utf-8' );

		/**
		 * Encode data
		 */
		return \json_encode( $this->container, $jsonOptions );

	}

}
