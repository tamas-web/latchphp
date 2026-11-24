<?php

namespace App\Html;

class Link {

	public function __construct( protected string $link, protected string $name, protected string $icon = '' ){}

	public function __get( string $key ) : ?string {

		return $this->{ $key } ?? null;

	}

}