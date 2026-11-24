<?php

namespace App\Traits;

/**
 * Trait to use dot notation array access
 */
trait DotKeys {

	/**
	 * Resolve the dot notation and return array value
	 */
	static protected function getDotKeyValue( $key, array $storage ) : mixed {

		if( false !== strpos( $key, '.' ) ){

			$segments = explode( '.', $key );
			$_items    = $storage;

			foreach( $segments as $segment ){

				if( ! is_array( $_items ) || ! array_key_exists( $segment, $_items ) ){

					return null;

				}

				$_items = $_items[ $segment ];

			}

			return $_items;

		}

		return $storage[ $key ] ?? null;

	}

	/**
	 * Set the array value via the dot notation
	 */
	static protected function setDotKeyValue( $key, $item, array &$storage ) : void {
		if( false !== strpos( $key, '.' ) ){
			$segments = explode( '.', $key );
			$array =& $storage;
			for( $i = 1, $c = count( $segments ); $i < $c; $i++ ){
				$segment = array_shift( $segments );
				if( ! isset( $array[ $segment ] ) || ! is_array( $array[ $segment ] ) ){
					$array[ $segment ] = [];
				}
				$array =& $array[ $segment ];
			}
			$array[ array_shift( $segments ) ] = $item;
		} else {
			$storage[ $key ] = $item;
		}
	}

}
