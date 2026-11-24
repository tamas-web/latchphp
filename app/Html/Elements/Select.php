<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Views\View;

class Select implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\Name,
		\App\Traits\Html\Nodes,
		\App\Traits\Html\Required,
		\App\Traits\Html\isReadOnly,
		\App\Traits\Html\Disabled,
		\App\Traits\Html\Placeholder,
		\App\Traits\Html\Multiple;

	protected array $options = [];
	protected mixed $selectedValue = '';

	public function __construct( string $id = '' ){

		$this->id( $id );

	}

	public static function Create( string $id = '' ) : self {

		return new self( $id );

	}

	public function options( array $options ) : self {

		$this->options = $options;

		return $this;

	}

	public function selected( mixed $selectedValue ) : self {

		$this->selectedValue = $selectedValue;

		return $this;

	}

	public function buffer(){

		$view = new View();

		foreach( $this->options as $option ){

			$key   = $option['id'] ?? null;
			$value = $option['name'] ?? null;

			if( is_array( $this->selectedValue ) ){
				$selected = in_array( $key, $this->selectedValue );
			} else {
				$selected = $key === $this->selectedValue;
			}

			$this->append(
				sprintf(
					'<option value="%s"%s>%s</option>',
					esc_html( $key ),
					( $selected ? ' selected' : '' ),
					esc_html( $value )
				)
			);

		}

		$this->renderNodes();

		$view->body()
			->add( 'html.select' )
			->data( [
				'id'          => $this->getId(),
				'class'       => $this->getClassName(),
				'name'        => $this->getNameSafe(),
				'placeholder' => $this->getPlaceholderSafe(),
				'required'    => $this->getRequired(),
				'disabled'    => $this->getDisabled(),
				'multiple'    => $this->getMultiple(),
				'readonly'    => $this->getReadonly(),
				'required'    => $this->getRequired(),
				'html'        => $this->html ?? null,
				'dataset'     => $this->getDatasetSafe(),
				'style'       => $this->getStyleSafe(),
			] );

		return $view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
