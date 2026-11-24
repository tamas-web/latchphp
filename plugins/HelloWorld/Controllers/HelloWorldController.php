<?php

namespace Plugins\HelloWorld\Controllers;

use App\Http\Request;
use App\Libraries\Controller;
use App\Views\View;

/**
 * The page half of the HelloWorld example.
 *
 * Shows the whole "a plugin serves its own page" recipe in one method:
 *
 *   - build a View pointed at the plugin's OWN templates/ directory (core `views/` never sees it);
 *   - buffer that view, and hand the buffer to core's `blank_output` body template;
 *   - leave the chrome alone, so the page renders inside the normal header / rail / footer.
 *
 * (A chrome-less page - a login-style screen - is the same recipe plus `->header()->remove( ... )`;
 * see the Recovery plugin for that variant.)
 */
class HelloWorldController extends Controller {

	protected $pageTitle = 'Hello World';

	public function page( Request $request ){

		$body = new View( dirname( __DIR__ ) . '/templates' );

		$body
			->body()
				->data( [
					'user' => $request->user(),
				] )
				->add( 'hello-world.page' );

		$request->view()
			->body()
				->data( [
					'output' => $body->buffer(),
				] )
				->add( 'blank_output' );

		$request->view()->render();

	}

}
