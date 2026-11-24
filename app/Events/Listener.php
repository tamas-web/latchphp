<?php

namespace App\Events;

use App\Events\Interfaces\ListenerInterface;

/**
 * Listener class for data manipulation.
 */
class Listener implements ListenerInterface {

    /**
     * Private container for all callbacks
     * @param array
     */
	static private array $events = [];

    /**
     * Add a listener for an event
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function on( string $event, $callback, int $priority = 10 ) : bool {
		if( is_callable( $callback, true, $id ) ){
			if( ! isset( self::$events[ $event ][ $priority ][ $id ] ) ){
				self::$events[ $event ][ $priority ][ $id ] = $callback;
				return true;
			} elseif( 'Closure::__invoke' === $id ){
				self::$events[ $event ][ $priority ][] = $callback;
				return true;
			}
		}
		return false;
	}

    /**
     * Remove callback from an event
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function off( string $event, $callback, int $priority = 10 ) : bool {
		$removed = 0;
		if( is_callable( $callback, true, $id ) ){
			foreach( self::getListenersForEvent( $event ) as $order => $callbacks ){
				if( $priority === $order ){
					foreach( $callbacks as $cb_id => $cb ){
						if( $id == $cb_id ){
							unset( self::$events[ $event ][ $priority ][ $id ] );
							if( ! self::$events[ $event ][ $priority ] ){
								unset( self::$events[ $event ][ $priority ] );
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
     * Get all listeners for an event
     *
     * @return array
     *   List of listener callbacks for an event
     */
	static public function getListenersForEvent( $event ) : array {
		foreach( self::$events as $evt => $callbacks ){
			if( $event == $evt ){
				ksort( $callbacks, SORT_NUMERIC );
				return $callbacks;
			}
		}
		return [];
	}

}
