<?php

namespace App\GD;

class Base64 {

	protected string $binary;

	protected string $mime;

	protected bool $isValid = false;

	public function __construct( protected string $data, protected array $allowedMimes ){

		$this->deconstruct();

	}

	public function get() : string {

		return $this->data;

	}

	public function getBinary() : string {

		return $this->binary;

	}

	protected function deconstruct() : bool {

		$base64    = explode( ',', $this->data );
		$mimeParts = explode( ';', $base64[0] );
		$mimeParts = explode( ':', $mimeParts[0] ?? '' );

		$this->mime = $mimeParts[1] ?? '';

		if( ! in_array( $this->mime, $this->allowedMimes ) ){

			return false;

		}

		$this->binary = base64_decode( $base64[1] ?? '' );

		$img = @imagecreatefromstring( $this->binary );

		if( ! $img ){

			return false;

		}

		$size = @getimagesizefromstring( $this->binary );

		if( ! $size || $size[0] == 0 || $size[1] == 0 || ! $size['mime'] || ! in_array( $this->mime, $this->allowedMimes ) ){

			return false;

		}

		$this->isValid = true;

		return $this->isValid;

	}

	public function isValid() : bool {

		return $this->isValid;

	}

}