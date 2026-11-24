<?php

namespace App\Traits\Html;

trait Step {

	protected int|float $step = 0;

	public function step( int|float|string $step ) : self {

		$this->step = $step * 1;

		return $this;

	}

	public function getStep() : int|float {

		return $this->step;

	}

}
