<?php

namespace App\Auth;

use App\Alerts\Alert;
use App\Http\Interfaces\MiddlewareInterface;
use App\Http\Request;

class Can implements MiddlewareInterface {

	public function __construct( protected string $permission ){}

	public function handle( Request $request, bool &$passed ){

		if( ! $request->user()->can( $this->permission ) ){

			$passed = false;

			Alert::error( 'You do not have the required permission for this action.' );

			$request->redirect( '/' );

		}

	}

}
