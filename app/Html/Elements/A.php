<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Views\View;

class A implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\Href,
		\App\Traits\Html\Name,
		\App\Traits\Html\Nodes,
		\App\Traits\Html\Target;

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
			->add( 'html.a' )
			->data( [
				'id'      => $this->getId(),
				'class'   => $this->getClassName(),
				'name'    => $this->getNameSafe(),
				'href'    => $this->getHrefSafe(),
				'html'    => $this->html ?? null,
				'dataset' => $this->getDatasetSafe(),
				'style'   => $this->getStyleSafe(),
				'target'  => $this->getTargetSafe(),
			] );

		return $view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
