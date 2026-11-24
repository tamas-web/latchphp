<?php

namespace App\Views;

use App\Datatype\Collection;

class Component {

	protected array $templates = []; // maybe convert to Collection

	protected Collection $data;

	public function __construct( Collection &$data ){

		$this->data = &$data;

	}

	public function add( string $template ) : self {

		$this->templates[] = $template;

		return $this;

	}

	public function remove( string $template ) : self {

		if( false !== array_search( $template, $this->templates ) ){

			unset( $this->templates[ array_search( $template, $this->templates ) ] );

		}

		return $this;

	}

	public function getTemplates() : array {

		return $this->templates;

	}

	public function data( array $datas ) : self {

		foreach( $datas as $key => $value ){

			$this->data[ $key ] = $value;

		}

		return $this;

	}

}
