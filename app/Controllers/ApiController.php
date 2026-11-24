<?php

namespace App\Controllers;

use App\Alerts\Alert;
use App\Libraries\Controller;

class ApiController extends Controller {

	public function clearAlerts() : void {

		Alert::clear();

	}

}
