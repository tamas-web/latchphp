<?php

namespace App\Controllers\Settings;

use App\Alerts\Alert;
use App\Datatype\Response;
use App\Http\Request;
use App\Html\Link;
use App\Filters\Filter;
use App\Libraries\{ Controller, PluginManager };

class PluginsController extends Controller {

	public function index( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Plugins' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/plugins', 'Plugins' ),
		] );

		$request->view()
			->body()
				->data( [
					'plugins' => ( new PluginManager )->discover()->listForUi(),
				] )
				->add( 'settings.plugins.index' );

		$request->view()->render();

	}

	public function uploadForm( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Upload plugin' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/settings/plugins', 'Plugins' ),
			new Link( '/admin/settings/plugins/upload', 'Upload plugin' ),
		] );

		$request->view()
			->body()
				->add( 'settings.plugins.upload' );

		$request->view()->render();

	}

	public function upload( Request $request ) : void {

		$response = new Response;

		$result = ( new PluginManager )->installFromZip( $request->files( 'plugin_zip' ) );

		if( $result['ok'] ){

			Alert::success( sprintf( 'The plugin "%s" was uploaded and installed (inactive).', $result['name'] ?? '' ) );

		} else {

			foreach( $result['errors'] as $error ){

				Alert::error( $error );

			}

		}

		$request->redirect( '/admin/settings/plugins' );

		echo $response;

	}

	public function install( Request $request )    : void { $this->op( $request, 'install',    'Plugin installed.' ); }
	public function uninstall( Request $request )  : void { $this->op( $request, 'uninstall',  'Plugin uninstalled.' ); }
	public function activate( Request $request )   : void { $this->op( $request, 'activate',   'Plugin activated.' ); }
	public function deactivate( Request $request ) : void { $this->op( $request, 'deactivate', 'Plugin deactivated.' ); }

	/**
	 * Shared handler for the four state ops. Validates the name against the known set
	 * (discover() + registry), runs the manager op, and reports via an Alert.
	 */
	protected function op( Request $request, string $action, string $ok ) : void {

		$response = new Response;

		$manager = ( new PluginManager )->discover();

		$name = (string) $request->post( 'plugin', fn( $value ) => sanitize_string( $value ) );

		if( ! $name || ! isset( $manager->listForUi()[ $name ] ) ){

			Alert::error( 'Unknown plugin.' );

		} elseif( $manager->{ $action }( $name ) ){

			Alert::success( $ok );

		} else {

			Alert::error( 'The operation could not be completed.' );

		}

		$request->redirect( '/admin/settings/plugins' );

		echo $response;

	}

}
