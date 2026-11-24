<?php

namespace App\Controllers;

use App\Libraries\Controller;
use App\Http\Request;
use App\Filters\Filter;
use App\Events\Listener;

class HttpErrorController extends Controller {

	function __construct( Request $request, int $errno = 404 ){

		Listener::off( 'post_print_scripts', 'App\Views\Footer::mainJS' );

		$request->view()
			->css( [
				'bootstrap'       => '/assets/vendors/bootstrap/bootstrap.min.css',
				'bootstrap-icons' => '/assets/vendors/bootstrap-icons/bootstrap-icons.css',
				'theme'           => '/assets/css/theme.css',
			] );

		// add parts to header
		$request->view()
			->header()
				->add( 'header' );

		// add parts to footer
		$request->view()
			->footer()
				->add( 'footer' );

		switch( $errno ){

			case 403:
				$this->_403( $request );
				break;

			case 404:
				$this->_404( $request );
				break;

			case 405:
				$this->_405( $request );
				break;

		}

		$request->view()->render();

		// stop any further script execution
		exit;

	}

	private function _403( Request $request ){

		header( "{$request->server->SERVER_PROTOCOL} 403 Forbidden" );

		Filter::add( 'body_class', fn( $className ) => [ 'login-page 403-page' ] );
		Filter::add( 'page_title', fn( $t ) => '403 - Access denied' );

		$request->view()
			->body()
				->add( 'error.403' );

	}

	private function _404( Request $request ){

		header( "{$request->server->SERVER_PROTOCOL} 404 Not Found" );

		Filter::add( 'body_class', fn( $className ) => [ 'login-page 404-page' ] );
		Filter::add( 'page_title', fn( $t ) => '404 - Page not found' );

		$request->view()
			->body()
				->add( 'error.404' );

	}

	private function _405( Request $request ){

		header( "{$request->server->SERVER_PROTOCOL} 405 Method Not Allowed" );

		Filter::add( 'body_class', fn( $className ) => [ 'login-page 405-page' ] );
		Filter::add( 'page_title', fn( $t ) => '405 - Method not allowed' );

		$request->view()
			->body()
				->add( 'error.405' );

	}

}
