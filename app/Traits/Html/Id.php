<?php

namespace App\Traits\Html;


trait Id {

	protected string $id = '';

	public function id( string $id ) : self {

		$this->id = alphanumeric_extended_no_space( $id );

		return $this;

	}

	public function getId() : string {

		return $this->id;

	}

}
