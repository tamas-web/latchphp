<?php

namespace App\Views;

use App\Config;
use App\Constants;
use App\Datatype\Collection;
use App\Http\URL;
use App\Views\Component;
use App\Filters\Filter;
use App\Events\Emitter;

class View implements Constants {

	protected string $path;

	protected Component $header;
	protected Component $body;
	protected Component $footer;
	protected Component $sidebar;

	protected Collection $css;

	protected Collection $js;

	protected Collection $data;

	public function __construct( string $path = '' ){

		$this->path    = rtrim( $path, '/' );

		$this->css     = new Collection;
		$this->js      = new Collection;
		$this->data    = new Collection;

		$this->header  = new Component( $this->data );
		$this->body    = new Component( $this->data );
		$this->footer  = new Component( $this->data );
		$this->sidebar = new Component( $this->data );

	}

	public function header() : Component {

		return $this->header;

	}

	public function body() : Component {

		return $this->body;

	}

	public function sidebar() : Component {

		return $this->sidebar;

	}

	public function footer() : Component {

		return $this->footer;

	}

	public function css( array $files ) : self {

		foreach( $files as $index => $file ){

			$this->css[ $index ] = $file;

		}

		return $this;

	}

	public function js( array $files ) : self {

		foreach( $files as $index => $file ){

			$this->js[ $index ] = $file;

		}

		return $this;

	}

	public function data( array $datas ) : self {

		foreach( $datas as $key => $value ){

			$this->data[ $key ] = $value;

		}

		return $this;

	}

	public function render(){

		// render header
		foreach( $this->header->getTemplates() as $tpl ){

			$this->include( $tpl );

		}

		// render body
		foreach( $this->body->getTemplates() as $tpl ){

			$this->include( $tpl );

		}

		// render footer
		foreach( $this->footer->getTemplates() as $tpl ){

			$this->include( $tpl );

		}

	}

	public function buffer(){

		ob_start();

		$this->render();

		return ob_get_clean();

	}

	public function include( $tpl ){

		$tpl = str_replace( '.', '/', $tpl );

		if( $this->path && file_exists( $this->path . '/' . $tpl . '.php' ) ){

			include $this->path . '/' . $tpl . '.php';

		} elseif( file_exists( APP_DIR . '/views/' . $tpl . '.php' ) ){

			include APP_DIR . '/views/' . $tpl . '.php';

		} elseif( 'development' === APP_ENVIRONMENT ){

			echo '<div style="font-weight:bold;background:pink;">file missing: ',
				$this->path
					? $this->path . '/' . $tpl . '.php'
					: APP_DIR . '/views/' . $tpl . '.php',
				'</div>';

		}

	}

	public function bodyClass( array $classList = [] ){

		return esc_html( join( ' ', Filter::apply( 'body_class', $classList ) ) );

	}

	public function htmlAttributes(){

		$attributes = Filter::apply( 'html_attributes', [
			'lang' => Config::get( 'app.site.language' ),
		] );

		$atts = [];

		foreach( $attributes as $name => $value ){

			$atts[] = sprintf(
				'%s="%s"',
				alphanumeric_extended_no_space( $name ),
				esc_html( $value )
			);

		}

		return join( ' ', $atts );

	}

	public function fullTitle(){

		return join(
			esc_html( Filter::apply( 'title_separator', Config::get( 'app.site.title.separator' ) ) ),
			[
				$this->title(),
				$this->titleSuffix(),
			]
		);

	}

	public function title(){

		return esc_html( Filter::apply( 'page_title', Config::get( 'app.site.title.page' ) ) );

	}

	public function titleSuffix(){

		return esc_html( Filter::apply( 'page_title_suffix', Config::get( 'app.site.title.suffix' ) ) );

	}

	public function app_header(){

		printf( '<title>%s</title>', $this->fullTitle() );

		Emitter::broadcast( 'head_start', $this );

		foreach( $this->css->getIterator() as $index => $css ){

			if( ! URL::isAbsolute( $css ) ){

				$css = URL::BUILD( $css, [
					'v' => Config::get( 'app.version' ),
				] );

			}

			printf( '<link id="css-%s" href="%s" rel="stylesheet">%s', $index, $css, "\n\t\t" );

		}

		Emitter::broadcast( 'post_print_styles', $this );

	}

	public function app_footer(){

		Emitter::broadcast( 'print_scripts', $this );

		foreach( $this->js->getIterator() as $index => $js ){

			$attributes = [];

			if( is_array( $js ) ){

				$_js = array_shift( $js );
				$attributes = $js;
				$js = $_js;

			}

			if( ! URL::isAbsolute( $js ) ){

				$js = URL::BUILD( $js, [
					'v' => Config::get( 'app.version' ),
				] );

			}

			printf( '<script id="js-%s" src="%s" %s></script>%s', $index, $js, join( ' ', $attributes ), "\n\t\t" );

		}

		Emitter::broadcast( 'post_print_scripts', $this );

	}

}
