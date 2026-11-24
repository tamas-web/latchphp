<?php

namespace App\Traits\Html;

trait Disabled {

	protected bool $disabled = false;

	public function disabled() : self {

		$this->disabled = true;

		return $this;

	}

	public function getDisabled() : bool {

		return $this->disabled;

	}

}
