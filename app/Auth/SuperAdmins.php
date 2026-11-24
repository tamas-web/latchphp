<?php

namespace App\Auth;

use App\Http\Interfaces\MiddlewareInterface;
use App\Http\Request;

/**
 * Route gate for super admins only (the ACCOUNT_ADMIN bit / god mode).
 * For role-based access use the parameterized Can middleware instead.
 */
class SuperAdmins implements MiddlewareInterface {

	function handle( Request $request, bool &$passed ){

		if( ! $request->user()->isSuperAdmin() ){

			$passed = false;

			$request->redirect( '/' );

		}

	}

}
