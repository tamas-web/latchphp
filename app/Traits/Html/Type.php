<?php

namespace App\Traits\Html;


trait Type {

	protected string $type = '';

	public function type( string $type ) : self {

		$this->type = $type;

		return $this;

	}

	public function getType() : string {

		return $this->type;

	}

	public function getTypeSafe() : string {

		return esc_html( $this->type );

	}

}
