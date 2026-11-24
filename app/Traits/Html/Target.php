<?php

namespace App\Traits\Html;


trait Target {

	protected mixed $target = '';

	public function target( mixed $target ) : self {

		$this->target = $target;

		return $this;

	}

	public function getTarget() : mixed {

		return $this->target;

	}

	public function getTargetSafe() : mixed {

		return esc_html( $this->target );

	}

}
