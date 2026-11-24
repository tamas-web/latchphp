<?php

namespace App\Controllers;

use App\Libraries\Controller;
use App\Http\Request;
use App\Models\Users;

class DashboardController extends Controller {

	protected $pageTitle = 'Dashboard';

	public function index( Request $request ){

		$users = Users::getAllUsers( showActiveOnly: true );

		$request->view()
			->body()
				->data( [
					'user'      => $request->user(),
					'userCount' => count( $users->items ),
				] )
				->add( 'dashboard.index' );

		$request->view()->render();

	}

}
