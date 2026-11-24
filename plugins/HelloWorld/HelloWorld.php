<?php

namespace Plugins\HelloWorld;

use App\Auth\MustBeLoggedIn;
use App\Events\Listener;
use App\Filters\Filter;
use App\Http\Request;
use App\Libraries\Plugin;
use App\Routing\Route;
use Plugins\HelloWorld\Controllers\HelloWorldController;

/**
 * HelloWorld - the reference plugin. Every seam a plugin can use, in the smallest form that
 * still runs:
 *
 *   - a FILTER that transforms a value       (`body_class`)
 *   - an EVENT that prints something         (`container_fluid_content_start`)
 *   - a ROUTE + controller + own template    (`/hello-world`, registered on `routes_loaded`)
 *   - a MENU entry contributed by filter     (`app.menu`)
 *
 * No database, no ACL, no core edits - deactivate it and every trace above disappears.
 */
class HelloWorld extends Plugin {

	public function run(){

		Filter::add( 'body_class', [ $this, 'bodyClass' ] );
		Listener::on( 'container_fluid_content_start', [ $this, 'greet' ] );

		// the router is NOT booted during run() - routes go on this event
		Listener::on( 'routes_loaded', [ $this, 'addRoutes' ] );

		Filter::add( 'app.menu', [ $this, 'menuItem' ] );

	}

	public function addRoutes() : void {

		Route::get( '/hello-world', [ HelloWorldController::class, 'page' ] )
			->middleware( [ MustBeLoggedIn::class ] );

	}

	public function menuItem( array $rail ) : array {

		$rail[90][] = [
			'path'  => '/hello-world',
			'title' => 'Hello World',
			'icon'  => 'bi bi-emoji-smile',
		];

		return $rail;

	}

	public function bodyClass( array $classes ) : array {

		$classes[] = 'plugin-hello-world';

		return $classes;

	}

	public function greet(){

		if( Request::getInstance()->user()->isUser() ){

			echo '<div class="alert alert-info">Hello! HelloWorld plugin active.</div>';

		}

	}

}
