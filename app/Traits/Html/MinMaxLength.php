<?php

namespace App\Traits\Html;


trait MinMaxLength {

	protected int $minlength;
	protected int $maxlength;

	protected string $min;
	protected string $max;

	public function minlength( int $minlength ) : self {

		$this->minlength = $minlength;

		return $this;

	}

	public function maxlength( int $maxlength ) : self {

		$this->maxlength = $maxlength;

		return $this;

	}

	public function min( int | string $min ) : self {

		$this->min = (string) $min;

		return $this;

	}

	public function max( string $max ) : self {

		$this->max = (string) $max;

		return $this;

	}

	public function getMinLength() : ?int {

		return $this->minlength ?? null;

	}

	public function getMaxLength() : ?int {

		return $this->maxlength ?? null;

	}

	public function getMinSafe() : ?string {

		return alphanumeric_extended( $this->min ?? null );

	}

	public function getMaxSafe() : ?string {

		return alphanumeric_extended( $this->max ?? null );

	}

}
