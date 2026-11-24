<?php

namespace App\Routing;

use App\Controllers\HttpErrorController;
use App\Events\Emitter;
use App\Filters\Filter;
use App\Http\Request;
use App\Routing\Route;
/**
 * Load all defined routes
 */
class Router {

	/**
	 * Current request container
	 */
	protected Request $request;

	/**
	 * Supported HTTP methods
	 */
	protected array $allowed_methods = [ 'GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS' ];

	/**
	 * Boot up
	 */
	public function boot() : void {

		$this->initRequest();

		$this->load();

	}

	/**
	 * Initiate request
	 */
	protected function initRequest() : void {

		$this->request = new Request;

		Emitter::broadcast( 'request_initiated', $this->request );

	}

	/**
	 * Load all routes
	 */
	protected function load() : void {

		foreach( glob( APP_DIR . '/routes/*.php' ) as $route ){

			require_once $route;

		}

		Emitter::broadcast( 'routes_loaded' );

	}

	/**
	 * Dispatch current request
	 */
	public function dispatch(){

		if( ! in_array( $this->request->server->REQUEST_METHOD, $this->allowed_methods, true ) ){

			new HttpErrorController( $this->request, 405 );

		}

		$this->call( $this->getRoute(), $this->request );

	}

	/**
	 * Call controller method
	 */
	protected function call( string $route, Request $request ){

		$route = Route::find( $route, $request );

		if( ! $route->isCallable() ){

			new HttpErrorController( $request, 404 );

		}

		Emitter::broadcast( 'pre_route_call', $request, $route );

		if( ! $route->run( $request ) ){

			new HttpErrorController( $request, 404 );

		}

		Emitter::broadcast( 'post_route_call', $request, $route );

	}

	/**
	 * Get current route
	 */
	protected function getRoute() : string {

		$uri = explode( '?', $this->request->server->REQUEST_URI )[0];

		$uri = rtrim( $uri, '/' );

		return Filter::apply( 'get_route', ( $uri ? $uri : '/' ) );

	}

	/**
	 * Get current route
	 */
	public function request() : Request {

		return $this->request;

	}

}
