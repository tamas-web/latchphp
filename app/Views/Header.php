<?php

namespace App\Views;

use App\Events\Listener;
use App\Filters\Filter;
use App\Http\{ URL, Request };
use App\Html\Link;
use App\Routing\Route;
use App\Models\Notifications;
use App\Views\View;

class Header {

	function boot(){

		Listener::on( 'pre_route_call', [ $this, 'render' ] );
		Listener::on( 'post_print_styles', [ $this, 'favicons' ] );
		Listener::on( 'post_print_styles', [ $this, 'getNotifications' ] );
		Listener::on( 'container_fluid_content_start', [ $this, 'breadcrumbs' ] );

	}

	public function render( Request $request ){

		$request->view()
			->css( [
				'bootstrap'       => '/assets/vendors/bootstrap/bootstrap.min.css',
				'bootstrap-icons' => '/assets/vendors/bootstrap-icons/bootstrap-icons.css',
				'theme'           => '/assets/css/theme.css',
			] );

		$request->view()
			->header()
				->add( 'header' )
				->add( 'wrapper_start' )
				->add( 'header.menu' )
				->add( 'header.top' )
				->add( 'content_start' );

	}

	public function getNotifications( View $view ){

		$view
			->header()
			->data( [
				'headerNotifications' => Notifications::getUserNotifications(),
			] );

	}

	public function favicons(){

		$view = new View;

		$view
			->header()
				->data( [
					'favicon-svg'              => URL::BUILD( 'assets/images/icon/favicon.svg' ),
					'apple-touch-icon'         => URL::BUILD( 'assets/images/icon/apple-touch-icon.png' ),
					'favicon-32x32'            => URL::BUILD( 'assets/images/icon/favicon-32x32.png' ),
					'favicon-16x16'            => URL::BUILD( 'assets/images/icon/favicon-16x16.png' ),
					'site.webmanifest'         => URL::BUILD( 'assets/images/icon/site.webmanifest' ),
					'mask-icon'                => URL::BUILD( 'assets/images/icon/safari-pinned-tab.svg' ),
					'mask-icon-color'          => '#84cc16',
					'shortcut-icon'            => URL::BUILD( 'assets/images/icon/favicon.ico' ),
					'msapplication-tile-color' => '#13160f',
					'msapplication-config'     => URL::BUILD( 'assets/images/icon/browserconfig.xml' ),
					'theme-color'              => '#13160f',
				] )
				->add( 'favicons' );

		$view->render();

	}

	public function breadcrumbs(){

		$view = new View();

		$breadcrumbs = Filter::apply( 'app.breadcrumbs', [
			new Link( '/', 'Home', 'bi bi-house' )
		] );

		$view
			->data( [
				'breadcrumbs' => $breadcrumbs,
			] )
			->body()
				->add( 'blocks.breadcrumbs' );

		$view->render();

	}

}
