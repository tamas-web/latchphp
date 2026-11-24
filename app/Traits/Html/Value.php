<?php

namespace App\Traits\Html;


trait Value {

	protected mixed $value = '';

	public function value( mixed $value ) : self {

		$this->value = $value;

		return $this;

	}

	public function getValue() : mixed {

		return $this->value;

	}

	public function getValueSafe() : mixed {

		return esc_html( $this->value );

	}

}
