<?php

namespace App\Html\Elements;

use App\Html\Elements\Interfaces\ElementInterface;
use App\Datatype\Collection;
use App\Views\View;

class Form implements ElementInterface {

	use \App\Traits\Html\Id,
		\App\Traits\Html\ClassList,
		\App\Traits\Html\Dataset,
		\App\Traits\Html\Style,
		\App\Traits\Html\Nodes;

	protected string $method = 'get';
	protected string $action = '';

	function __construct( string $id ){

		$this->id( $id );

	}

	public static function Create( string $id = '' ) : self {

		return new self( $id );

	}

	function action( string $url ) : self {

		$this->action = $url;

		return $this;

	}

	function method( string $method ) : self {

		$method = strtolower( $method );

		$this->method = ( 'post' == $method )
			? 'post'
			: 'get';

		return $this;

	}

	public function buffer(){

		$view = new View;

		$view->header()
			->add( 'html.form.start' )
			->data( [
				'id'      => $this->getId(),
				'class'   => $this->getClassName(),
				'action'  => esc_url( $this->action ),
				'method'  => $this->method,
				'dataset' => $this->getDatasetSafe(),
				'style'   => $this->getStyleSafe(),
			] );

		$this->renderNodes();

		if( 'post' === $this->method ){

			$this->html .= nonce_field();

		}

		$view->body()->data( [
			'html' => $this->html,
		] );

		$view->footer()->add( 'html.form.end' );

		return $view->buffer();

	}

	public function render(){

		echo $this->buffer();

	}

}
