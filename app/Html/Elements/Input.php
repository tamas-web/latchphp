<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Views\View;

class Input implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\Icon,
		\App\Traits\Html\MinMaxLength,
		\App\Traits\Html\Name,
		\App\Traits\Html\Nodes,
		\App\Traits\Html\Type,
		\App\Traits\Html\Placeholder,
		\App\Traits\Html\Autocomplete,
		\App\Traits\Html\Required,
		\App\Traits\Html\isReadOnly,
		\App\Traits\Html\Disabled,
		\App\Traits\Html\Value,
		\App\Traits\Html\Step,
		\App\Traits\Html\Multiple;

	protected View $view;

	protected bool $checked = false;

	protected ?string $sub = null;

	public function __construct( string $id ){

		$this->id( $id );

		$this->view = new View;

	}

	public static function Create( string $id = '' ) : self {

		$instance = new self( $id );

		// set input type rendering
		$instance->view->body()->add( 'html.input' );

		return $instance;

	}

	public static function IconGroup( string $id = '' ) : self {

		$instance = new self( $id );

		// set input type rendering
		$instance->view->body()->add( 'html.input-icon-group' );

		return $instance;

	}

	public static function iCheck( string $id = '' ) : self {

		$instance = new self( $id );

		$instance->type( 'checkbox' );

		// set input type rendering
		$instance->view->body()->add( 'html.input-icheck' );

		return $instance;

	}

	public static function iRadio( string $id = '' ) : self {

		$instance = new self( $id );

		$instance->type( 'radio' );

		// set input type rendering
		$instance->view->body()->add( 'html.input-icheck' );

		return $instance;

	}

	public function checked( bool $checked ){

		$this->checked = $checked;

		return $this;

	}

	public function sub( ?string $sub ){

		$this->sub = $sub;

		return $this;

	}

	public function buffer(){

		$this->renderNodes();

		$this->view->body()
			->data( [
				'id'           => $this->getId(),
				'class'        => $this->getClassName(),
				'type'         => $this->getTypeSafe(),
				'name'         => $this->getNameSafe(),
				'value'        => $this->getValueSafe(),
				'placeholder'  => $this->getPlaceholderSafe(),
				'icon'         => $this->getIconSafe(),
				'html'         => $this->html ?? null,
				'required'     => $this->getRequired(),
				'disabled'     => $this->getDisabled(),
				'readonly'     => $this->getReadonly(),
				'autocomplete' => $this->getAutocompleteSafe(),
				'minlength'    => $this->getMinLength(),
				'maxlength'    => $this->getMaxLength(),
				'min'          => $this->getMinSafe(),
				'max'          => $this->getMaxSafe(),
				'multiple'     => $this->getMultiple(),
				'checked'      => $this->checked,
				'sub'          => $this->sub ? esc_html( $this->sub ) : null,
				'dataset'      => $this->getDatasetSafe(),
				'style'        => $this->getStyleSafe(),
				'step'         => $this->getStep(),
			] );

		return $this->view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
