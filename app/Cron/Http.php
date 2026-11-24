<?php

namespace App\Cron;

use App\Cron\Jobs;
use App\Http\Request;
use App\Libraries\Controller;

class Http extends Controller {

	public function init( Request $request ) : void {

		Jobs::init();

	}

}
