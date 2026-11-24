<?php

namespace App\Auth;

use App\Alerts\Alert;
use App\Http\Interfaces\MiddlewareInterface;
use App\Http\Request;

class CanAnyGroup implements MiddlewareInterface {

	protected array $groups;

	public function __construct( string ...$groups ){

		$this->groups = $groups;

	}

	public function handle( Request $request, bool &$passed ){

		if( ! $request->user()->canAnyGroup( ...$this->groups ) ){

			$passed = false;

			Alert::error( 'You do not have the required permission for this action.' );

			$request->redirect( '/' );

		}

	}

}
