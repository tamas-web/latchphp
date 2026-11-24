<?php

namespace App\Controllers;

use App\Libraries\Controller;
use App\Http\Request;
use App\Html\Link;
use App\Filters\Filter;

class StyleguideController extends Controller {

	protected $pageTitle = 'Styleguide';

	public function content( Request $request ){

		$this->pageTitle = 'Content & Typography';

		$this->show( $request, 'styleguide.content' );

	}

	public function formsStacked( Request $request ){

		$this->pageTitle = 'Forms - Stacked';

		$this->show( $request, 'styleguide.forms-stacked' );

	}

	public function formsHorizontal( Request $request ){

		$this->pageTitle = 'Forms - Horizontal';

		$this->show( $request, 'styleguide.forms-horizontal' );

	}

	public function tables( Request $request ){

		$this->pageTitle = 'Tables';

		$this->show( $request, 'styleguide.tables' );

	}

	public function components( Request $request ){

		$this->pageTitle = 'Components';

		$this->show( $request, 'styleguide.components' );

	}

	public function widgets( Request $request ){

		$this->pageTitle = 'Widgets';

		$this->show( $request, 'styleguide.widgets' );

	}

	public function feedback( Request $request ){

		$this->pageTitle = 'Feedback & Overlays';

		$this->show( $request, 'styleguide.feedback' );

	}

	protected function show( Request $request, string $template ){

		$path = '/' . str_replace( '.', '/', $template );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( $path, 'Styleguide: ' . $this->pageTitle ),
		] );

		$request->view()
			->body()
				->add( $template );

		$request->view()->render();

	}

}
