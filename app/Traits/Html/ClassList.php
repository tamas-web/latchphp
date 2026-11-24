<?php

namespace App\Traits\Html;


trait ClassList {

	protected $class = [];

	public function class( $class ) : self {

		if( is_string( $class ) ){

			$this->class[] = alphanumeric_extended( $class );

		} elseif( is_array( $class ) ){

			foreach( $class as $cls ){

				$this->class[] = alphanumeric_extended( $cls );

			}

		}

		return $this;

	}

	public function getClassList(){

		return $this->class;

	}

	public function getClassName(){

		return join( ' ', $this->class );

	}

}
