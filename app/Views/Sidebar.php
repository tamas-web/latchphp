<?php

namespace App\Views;

use App\Events\Listener;
use App\Routing\Route;
use App\Http\Request;

class Sidebar {

	function boot(){

		Listener::on( 'pre_route_call', [ $this, 'render' ] );

	}

	public function render( Request $request, Route $route ){

		$request->view()
			->sidebar()
				->add( 'sidebar' );

	}

}
