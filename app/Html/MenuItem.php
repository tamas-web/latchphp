<?php

namespace App\Html;

use App\Http\URL;
use App\Http\Request;
use App\Views\View;

class MenuItem {

	const DEFAULTS = [
		'path'  => '#',
		'title' => '(no title)',
		'icon'  => '',
		'class' => '',
		'items' => [],
		'args'  => [],
	];

	protected static $currentUrl;

	protected bool $active = false;

	protected int $depth = 0;

	protected string $path;

	protected string $title;

	protected string $icon;

	protected string $class;

	protected array $items = [];

	protected array $args = [];

	public function __construct( array $options, int $depth = 0 ){

		$this->depth = $depth;

		$this->setCurrentUrl();

		$this->parseOptions( $options );

	}

	protected function setCurrentUrl() : void {

		if( is_null( self::$currentUrl ) ){

			self::$currentUrl = Request::getURL();

		}

	}

	protected function parseOptions( array $options ) : void {

		$opt = array_replace( self::DEFAULTS, $options );

		$path = explode( '?', $opt['path'] );

		$url  = array_shift( $path );

		$args = (string) array_shift( $path );

		$args = html_entity_decode( $args, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5 );

		parse_str( $args, $args );

		$this->path    = $url;
		$this->args    = $args;
		$this->title   = $opt['title'];
		$this->icon    = $opt['icon'];
		$this->class   = $opt['class'];

		$this->checkIsActive();

		$this->parseItems( $opt['items'] );

	}

	protected function checkIsActive() : void {

		if( self::$currentUrl == $this->path ){

			$this->active = true;

		}

	}

	protected function parseItems( $items ) : void {

		foreach( $items as $item ){

			$new_item = new self( $item, ( $this->depth + 1 ) );

			$this->active |= $new_item->isActive();

			$this->items[] = $new_item;

		}

	}

	public function isActive() : bool {

		return $this->active;

	}

	public function __toString() : string {

		$view = new View();

		$view->body()
			->add( 'menu.menuItem' )
			->data( [
				'path'   => $this->path !== '#'
					? esc_url( URL::BUILD( $this->path, $this->args ) )
					: '#',
				'title'  => esc_html( $this->title ),
				'icon'   => esc_html( $this->icon ),
				'class'  => esc_html( $this->class ),
				'active' => $this->isActive(),
				'items'  => $this->items,
				'depth'  => $this->depth,
			] );

		return $view->buffer();

	}

}
