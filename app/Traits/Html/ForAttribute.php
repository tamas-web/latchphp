<?php

namespace App\Traits\Html;


trait ForAttribute {

	protected string $for = '';

	public function for( string $for ) : self {

		$this->for = alphanumeric_extended_no_space( $for );

		return $this;

	}

	public function getFor() : string {

		return $this->for;

	}

}
