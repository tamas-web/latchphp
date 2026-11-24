<?php

namespace App\Traits\Html;

trait Required {

	protected bool $required = false;

	public function required() : self {

		$this->required = true;

		return $this;

	}

	public function getRequired() : bool {

		return $this->required;

	}

}
