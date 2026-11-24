<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Views\View;

class Button implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\Icon,
		\App\Traits\Html\Name,
		\App\Traits\Html\Type,
		\App\Traits\Html\Value,
		\App\Traits\Html\isReadOnly,
		\App\Traits\Html\Disabled,
		\App\Traits\Html\Nodes;

	public function __construct( string $id ){

		$this->id( $id );
		$this->type( 'button' );

	}

	public static function Create( string $id = '' ) : self {

		return new self( $id );

	}

	public function buffer(){

		$view = new View();

		$view->body()
			->add( 'html.button' )
			->data( [
				'id'       => $this->getId(),
				'class'    => $this->getClassName(),
				'type'     => $this->getTypeSafe(),
				'name'     => $this->getNameSafe(),
				'value'    => $this->getValueSafe(),
				'icon'     => $this->getIconSafe(),
				'html'     => $this->html ?? null,
				'dataset'  => $this->getDatasetSafe(),
				'style'    => $this->getStyleSafe(),
				'disabled' => $this->getDisabled(),
				'readonly' => $this->getReadonly(),
			] );

		return $view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
