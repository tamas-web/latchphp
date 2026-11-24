<?php

namespace App\Datatype;

use ArrayIterator;
use Stringable;

/**
 * Collection data structure
 */
class Collection extends ArrayIterator implements Stringable {

    /**
     * Private container for all data
     * @param array
     */
	private $container = [];

	public function __construct( array $array = [] ){

		//~ parent::__construct( $array );

		$this->container = $array;

	}

    /**
     * Set data
     */
	public function offsetSet( $offset, $value ) : void {
		if( is_null( $offset ) ){
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
     * Get data count
     */
	public function count() : int {
		return count( $this->container );
	}

    /**
     * Get array iterator
     */
	public function getIterator() : ArrayIterator {
		return new ArrayIterator( $this->container );
	}

    /**
     * Set data
     */
	public function __set( $offset, $value ) : void {
		if( is_null( $offset ) ){
			$this->container[] = $value;
		} else {
			$this->container[ $offset ] = $value;
		}
	}

    /**
     * Check if offset/key exists
     */
	public function __isset( $offset ) : bool {
		return isset( $this->container[ $offset ] );
	}

    /**
     * Unset data
     */
	public function __unset( $offset ) : void {
		unset( $this->container[ $offset ] );
	}

    /**
     * Get data
     */
	public function __get( $offset ) : mixed {
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
		 * Encode data
		 */
		return \json_encode( $this->container, $jsonOptions );

	}

}
