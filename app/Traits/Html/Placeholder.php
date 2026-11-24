<?php

namespace App\Traits\Html;


trait Placeholder {

	protected string $placeholder = '';

	public function placeholder( string $placeholder ) : self {

		$this->placeholder = $placeholder;

		return $this;

	}

	public function getPlaceholder() : string {

		return $this->placeholder;

	}

	public function getPlaceholderSafe() : string {

		return esc_html( $this->placeholder );

	}

}
