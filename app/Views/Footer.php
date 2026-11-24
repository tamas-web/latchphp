<?php

namespace App\Views;

use App\Config;
use App\Events\Listener;
use App\Http\{ Request, URL };
use App\Routing\Route;

class Footer {

	function boot(){

		Listener::on( 'pre_route_call', [ $this, 'render' ] );
		Listener::on( 'post_print_scripts', [ $this, 'mainJS' ] );

	}

	public function render( Request $request, Route $route ){

		$request->view()
			->js( [
				'bootstrap' => '/assets/vendors/bootstrap/bootstrap.bundle.min.js',
			] );

		$request->view()
			->footer()
				->add( 'content_end' )
				->add( 'footer.footer' )
				->add( 'wrapper_end' )
				->add( 'footer' );

	}

	public function mainJS(){

		echo '<script type="module" src="', URL::BUILD( '/assets/js/main.js', [
			'v' => Config::get( 'app.version' ),
		] ), '"></script>';

	}

}
