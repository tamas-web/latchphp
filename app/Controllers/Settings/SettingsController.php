<?php

namespace App\Controllers\Settings;

use App\Alerts\Alert;
use App\Config;
use App\Datatype\Response;
use App\Events\Emitter;
use App\Filters\Filter;
use App\Html\Link;
use App\Http\{ Request, URL };
use App\Libraries\Controller;
use App\Models\Settings\Settings;

class SettingsController extends Controller {

	protected $pageTitle = 'Settings';

	public function index( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Administration' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
		] );

		$request->view()
			->body()
				->add( 'settings.index' );

		$request->view()->render();

	}

	public function appForm( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Application settings' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/application', 'Application settings' ),
		] );

		$request->view()
			->body()
				->add( 'settings.app' );

		$request->view()->render();

	}

	public function appUpdate( Request $request ) : void {

		$response = new Response;

		if( ! $request->post( 'updateSettings' ) ){

			echo $response;
			return;

		}

		$limit = (int) $request->post( 'pagination_limit' );

		Config::set( 'settings.app.pagination.limit', in_array( $limit, [ 25, 50, 100 ], true ) ? $limit : 25 );
		Config::set( 'settings.users.phone.enabled',  (bool) $request->post( 'phone_enabled' ) );
		Config::set( 'settings.users.phone.required', (bool) $request->post( 'phone_required' ) );

		Settings::save( 'app',   Config::get( 'settings.app' ) );
		Settings::save( 'users', Config::get( 'settings.users' ) );

		Emitter::broadcast( 'app_settings_save', $request, $response );

		Alert::success( 'The settings have been saved.' );

		$request->redirect( URL::BUILD( 'admin/settings/application' ) );

		echo $response;

	}

}
