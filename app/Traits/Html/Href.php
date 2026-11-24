<?php

namespace App\Traits\Html;


trait Href {

	protected string $href = '';

	public function href( string $href ) : self {

		$this->href = $href;

		return $this;

	}

	public function getHref() : string {

		return $this->href;

	}

	public function getHrefSafe() : mixed {

		return esc_url( $this->href );

	}

}
