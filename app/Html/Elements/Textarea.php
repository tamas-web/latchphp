<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Views\View;

class Textarea implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\MinMaxLength,
		\App\Traits\Html\Name,
		\App\Traits\Html\Required,
		\App\Traits\Html\isReadOnly,
		\App\Traits\Html\Disabled,
		\App\Traits\Html\Value,
		\App\Traits\Html\Nodes,
		\App\Traits\Html\Rows;

	public function __construct( string $id ){

		$this->id( $id );

	}

	public static function Create( string $id = '' ) : self {

		return new self( $id );

	}

	public function buffer(){

		$view = new View();

		$this->renderNodes();

		$view->body()
			->add( 'html.textarea' )
			->data( [
				'id'        => $this->getId(),
				'class'     => $this->getClassName(),
				'name'      => $this->getNameSafe(),
				'value'     => $this->getValueSafe(),
				'html'      => $this->html ?? null,
				'required'  => $this->getRequired(),
				'disabled'  => $this->getDisabled(),
				'readonly'  => $this->getReadonly(),
				'minlength' => $this->getMinLength(),
				'maxlength' => $this->getMaxLength(),
				'dataset'   => $this->getDatasetSafe(),
				'style'     => $this->getStyleSafe(),
				'rows'      => $this->getRows(),
			] );

		return $view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
