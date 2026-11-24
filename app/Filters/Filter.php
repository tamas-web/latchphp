<?php

namespace App\Filters;

use App\Filters\Interfaces\FilterInterface;

/**
 * Filter class for data manipulation.
 */
class Filter implements FilterInterface {

    /**
     * Private container for all callbacks
     * @param array
     */
	static private array $callbacks = [];

    /**
     * Add filter to a tag
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function add( string $tag, $callback, int $priority = 10 ) : bool {
		if( is_callable( $callback, true, $id ) ){
			if( ! isset( self::$callbacks[ $tag ][ $priority ][ $id ] ) ){
				self::$callbacks[ $tag ][ $priority ][ $id ] = $callback;
				return true;
			} elseif( 'Closure::__invoke' === $id ){
				self::$callbacks[ $tag ][ $priority ][] = $callback;
				return true;
			}
		}
		return false;
	}

    /**
     * Apply all filters to a tag
     *
     * @return mixed
     */
	static public function apply( string $tag, mixed $value, ...$args ) : mixed {
		foreach( self::getFiltersForTag( $tag ) as $priority => $callbacks ){
			foreach( $callbacks as $callback ){
				if( null !== $value ){
					$value = call_user_func_array( $callback, array_merge( [ $value ], $args ) );
				}
			}
		}
		return $value;
	}

    /**
     * Remove filter from a tag
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function remove( string $tag, $callback, int $priority = 10 ) : bool {
		$removed = 0;
		if( is_callable( $callback, true, $id ) ){
			foreach( self::getFiltersForTag( $tag ) as $order => $callbacks ){
				if( $priority === $order ){
					foreach( $callbacks as $cb_id => $cb ){
						if( $id == $cb_id ){
							unset( self::$callbacks[ $tag ][ $priority ][ $id ] );
							if( ! self::$callbacks[ $tag ][ $priority ] ){
								unset( self::$callbacks[ $tag ][ $priority ] );
							}
							$removed |= 1;
						}
					}
				}
			}
		}
		return (bool) $removed;
	}

    /**
     * Remove all filters from a tag
     *
     * @return void
     */
	static public function removeAll( string $tag ) : void {
		unset( self::$callbacks[ $tag ] );
	}

    /**
     * Get all filters for a tag
     *
     * @return array
     *   List of filter callbacks for a tag
     */
	static public function getFiltersForTag( $tag ) : array {
		foreach( self::$callbacks as $_tag => $callbacks ){
			if( $tag == $_tag ){
				ksort( $callbacks, SORT_NUMERIC );
				return $callbacks;
			}
		}
		return [];
	}

}
