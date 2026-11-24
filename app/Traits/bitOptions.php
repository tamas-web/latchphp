<?php

namespace App\Traits;

trait bitOptions {

	protected int $options = 0;

	public function hasOption( int $option ) : bool {

		return ( $this->options & $option ) === $option;

	}

	public function setOption( int $option ) : self {

		$this->options |= $option;

		return $this;

	}

	public function clearOption( int $option ) : self {

		$this->options &= ~$option;

		return $this;

	}

	public function clearAllOptions() : self {

		$this->options = 0;

		return $this;

	}

	public function getAllOptions() : int {

		return $this->options;

	}

}
