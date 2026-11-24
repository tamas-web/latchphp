<?php

namespace App\Traits\Html;


trait Icon {

	protected string $icon = '';

	public function icon( string $icon ) : self {

		$this->icon = $icon;

		return $this;

	}

	public function getIcon() : string {

		return $this->icon;

	}

	public function getIconSafe() : string {

		return esc_html( $this->icon );

	}

}
