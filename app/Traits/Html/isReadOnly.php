<?php

namespace App\Traits\Html;

trait isReadOnly {

	protected bool $readonly = false;

	public function readonly() : self {

		$this->readonly = true;

		return $this;

	}

	public function getReadonly() : bool {

		return $this->readonly;

	}

}
