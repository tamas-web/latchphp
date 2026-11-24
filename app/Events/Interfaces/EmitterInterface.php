<?php

namespace App\Events\Interfaces;

/**
 * Emitter interface to trigger events
 */
interface EmitterInterface {

    /**
     * Trigger an event
     *
     * @return void
     */
	static public function broadcast( string $event, ...$args ) : void;

}
