<?php

namespace App\Traits\Html;


trait Rows {

	protected int $rows = 0;

	public function rows( int|string $rows ) : self {

		$this->rows = (int) $rows;

		return $this;

	}

	public function getRows() : int {

		return $this->rows;

	}

}
