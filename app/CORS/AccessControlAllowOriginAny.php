<?php

namespace App\CORS;

use App\Http\Request;

class AccessControlAllowOriginAny {

	public function handle( Request $request ){

		header( 'Access-Control-Allow-Origin: *' );
		header( 'Access-Control-Max-Age: 86400' );

	}

}
