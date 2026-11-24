<?php

namespace App\Auth;

use App\Http\Request;

class GuestOnly {

	public function handle( Request $request, bool &$passed ){

		if( $request->user()->isUser() ){

			$passed = false;

			$request->redirect( '/' );

		}

	}

}
