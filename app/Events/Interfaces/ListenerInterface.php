<?php

namespace App\Events\Interfaces;

/**
 * Listener interface for event listening
 */
interface ListenerInterface {

    /**
     * Add a listener for an event
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function on( string $event, $callback, int $priority = 10 ) : bool;

    /**
     * Remove callback from an event
     *
     * @return bool
     *   True if added successfully.
     *   False if callback is not callable.
     */
	static public function off( string $event, $callback, int $priority = 10 ) : bool;

	// idk, if we should implement at this moment
	//~ static public function hasListenersForEvent( string $event ) : bool;

    /**
     * Get all listeners for an event
     *
     * @return array
     *   List of listener callbacks for an event
     */
	static public function getListenersForEvent(string $event ) : array;

}
