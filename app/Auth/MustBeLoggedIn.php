<?php

namespace App\Auth;

use App\Http\Interfaces\MiddlewareInterface;
use App\Http\Request;

class MustBeLoggedIn implements MiddlewareInterface {

	function handle( Request $request, bool &$passed ){

		if( ! $request->user()->isUser() ){

			$passed = false;

			$request->redirect( '/login' );

		} else {

			$request->user()->updateLastSeen();

		}

	}

}
