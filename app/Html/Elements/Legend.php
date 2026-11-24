<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Views\View;

class Legend implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\Nodes;

	public function __construct( string $id = '' ){

		$this->id( $id );

	}

	public static function Create( string $id = '' ) : self {

		return new self( $id );

	}

	public function buffer(){

		$view = new View();

		$this->renderNodes();

		$view->body()
			->add( 'html.legend' )
			->data( [
				'id'      => $this->getId(),
				'class'   => $this->getClassName(),
				'html'    => $this->html ?? null,
				'dataset' => $this->getDatasetSafe(),
				'style'   => $this->getStyleSafe(),
			] );

		return $view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
