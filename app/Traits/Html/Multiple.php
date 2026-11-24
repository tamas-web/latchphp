<?php

namespace App\Traits\Html;

trait Multiple {

	protected bool $multiple = false;

	public function multiple() : self {

		$this->multiple = true;

		return $this;

	}

	public function getMultiple() : bool {

		return $this->multiple;

	}

}
