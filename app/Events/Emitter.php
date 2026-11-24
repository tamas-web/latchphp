<?php

namespace App\Events;

use App\Events\Interfaces\EmitterInterface;
use App\Events\Listener;

/**
 * Emitter class to trigger events
 */
class Emitter implements EmitterInterface {

    /**
     * Trigger an event
     *
     * @return void
     */
	static public function broadcast( string $event, ...$args ) : void {

		foreach( Listener::getListenersForEvent( $event ) as $priority => $callbacks ){

			foreach( $callbacks as $callback ){

				call_user_func_array( $callback, $args );

			}

		}

	}

}
