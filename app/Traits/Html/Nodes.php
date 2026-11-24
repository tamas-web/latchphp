<?php

namespace App\Traits\Html;


trait Nodes {

	protected mixed $html = '';
	protected array $nodes = [];

	public function text( string $text ) : self {

		$this->html = esc_html( $text );

		return $this;

	}

	public function textNode( string $text ) : self {

		$this->nodes[] = esc_html( $text );

		return $this;

	}

	public function html( mixed $html ) : self {

		$this->html = $html;

		return $this;

	}

	public function htmlNode( mixed $element ) : self {

		$this->append( $element );

		return $this;

	}

	function append( $element ) : self {

		$this->nodes[] = $element;

		return $this;

	}

	function prepend( $element ) : self {

		\array_unshift( $this->nodes, $element );

		return $this;

	}

	public function renderNodes(){

		foreach( $this->nodes as $node ){

			if( \is_string( $node ) ){

				$this->html.= $node;

			} elseif( \is_object( $node ) && \method_exists( $node, 'buffer' ) ){

				$this->html.= $node->buffer();

			}

		}

	}

}
