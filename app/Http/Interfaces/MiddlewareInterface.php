<?php

namespace App\Http\Interfaces;

use App\Http\Request;

interface MiddlewareInterface {

	public function handle( Request $request, bool &$passed );

}
