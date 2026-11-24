<?php

namespace App\Datatype;

use App\Datatype;

/**
 * Event data type, maybe further implement or not... don't know at this point
 */
class Event {

	public string $type;

	public array $args = [];

	public function __construct( $type = 'default', ...$args ){
		$this->type = $type;
		$this->args = $args;
		return $this;
	}

	public function isPropagationStopped() : bool {
		return false;
	}

}
