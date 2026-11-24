<?php

namespace App\Traits\Html;


trait Autocomplete {

	protected string $autocomplete = '';

	public function autocomplete( string $autocomplete ) : self {

		$this->autocomplete = $autocomplete;

		return $this;

	}

	public function getAutocomplete() : string {

		return $this->autocomplete;

	}

	public function getAutocompleteSafe() : string {

		return esc_html( $this->autocomplete );

	}

}
