<?php

namespace App\Traits\Html;


trait Style {

	protected string $style = '';

	public function style( string $style ) : self {

		$this->style = $style;

		return $this;

	}

	public function getStyle() : string {

		return $this->style;

	}

	public function getStyleSafe() : string {

		return esc_html( $this->style );

	}

}
