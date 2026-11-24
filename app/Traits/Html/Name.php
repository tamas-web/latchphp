<?php

namespace App\Traits\Html;


trait Name {

	protected string $name = '';

	public function name( string $name ) : self {

		$this->name = $name;

		return $this;

	}

	public function getName() : string {

		return $this->name;

	}

	public function getNameSafe() : string {

		return alphanumeric_extended_no_space( $this->name );

	}

}
