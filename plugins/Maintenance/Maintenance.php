<?php

namespace Plugins\Maintenance;

use App\Config;
use App\Events\Listener;
use App\Html\Builder;
use App\Libraries\{ Cli, Plugin };
use App\Models\Settings\Settings;
use App\Views\View;

class Maintenance extends Plugin {

	public function run(){

		Listener::on( 'pre_route_call',    [ $this, 'gate' ] );
		Listener::on( 'app_settings_form', [ $this, 'settingsField' ] );
		Listener::on( 'app_settings_save', [ $this, 'settingsSave' ] );

	}

	public function gate( $request, $route ) : void {

		if( ! Config::get( 'settings.maintenance.enabled' ) ){

			return;

		}

		if( $request->user()->isUser() && $request->user()->can( 'settings.manage' ) ){

			return;

		}

		$path = trim( (string) parse_url( $_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH ), '/' );

		if( in_array( $path, [ 'login', 'api/forms/login', 'logout' ], true ) ){

			return;

		}

		http_response_code( 503 );
		header( 'Retry-After: 3600' );

		$view = new View( __DIR__ . '/templates' );

		$view
			->body()
				->data( [
					'message' => Config::get( 'settings.maintenance.message' ) ?: 'The site is currently under maintenance. Please check back soon.',
				] )
				->add( 'maintenance' );

		$view->render();

		exit;

	}

	public function settingsField( $view ) : void {

		Builder::generate( [
			[
				'elementType' => 'fieldset',
				'fields'      => [
					[
						'elementType' => 'legend',
						'text'        => 'Maintenance',
					],
					[
						'elementType' => 'input::icheck',
						'id'          => 'maintenance_enabled',
						'class'       => 'mb-3',
						'name'        => 'maintenance_enabled',
						'value'       => '1',
						'text'        => 'Maintenance mode',
						'sub'         => 'Restrict public access to the platform.',
						'checked'     => (bool) Config::get( 'settings.maintenance.enabled' ),
					],
					[
						'elementType' => 'div',
						'class'       => 'mb-3',
						'fields'      => [
							[
								'elementType' => 'label',
								'class'       => 'form-label',
								'for'         => 'maintenance_message',
								'text'        => 'Maintenance message',
							],
							[
								'elementType' => 'textarea',
								'id'          => 'maintenance_message',
								'name'        => 'maintenance_message',
								'rows'        => 3,
								'value'       => Config::get( 'settings.maintenance.message' ),
							],
						],
					],
				],
			],
		] );

	}

	public function settingsSave( $request, $response ) : void {

		Config::set( 'settings.maintenance.enabled', (bool) $request->post( 'maintenance_enabled' ) );
		Config::set( 'settings.maintenance.message', $request->post( 'maintenance_message', fn( $m ) => sanitize_string( $m ) ) );

		Settings::save( 'maintenance', Config::get( 'settings.maintenance' ) );

	}

	public static function cli( array $argv ) : void {

		$command = $argv[1] ?? 'status';

		try {

			match( $command ){
				'on'     => self::toggle( true ),
				'off'    => self::toggle( false ),
				'status' => self::status(),
				default  => self::usage(),
			};

		} catch( \Throwable $e ){

			Cli::out( 'Error: ' . $e->getMessage(), Cli::COLOR_RED );
			Cli::out( 'Is the database reachable, and has the `settings` migration been run?', Cli::COLOR_YELLOW );

			exit( 1 );

		}

	}

	private static function toggle( bool $on ) : void {

		$settings            = Settings::all()['maintenance'] ?? [];
		$settings['enabled'] = $on;

		if( ! Settings::save( 'maintenance', $settings ) ){

			Cli::out( 'Could not update the setting.', Cli::COLOR_RED );

			exit( 1 );

		}

		if( $on ){

			Cli::out( 'Maintenance mode is now ON.', Cli::COLOR_YELLOW );

		} else {

			Cli::out( 'Maintenance mode is now OFF. The site is live.', Cli::COLOR_GREEN );

		}

	}

	private static function status() : void {

		$on = (bool) ( Settings::all()['maintenance']['enabled'] ?? false );

		Cli::out(
			sprintf( 'Maintenance mode: %s', $on ? 'ON' : 'OFF' ),
			$on ? Cli::COLOR_YELLOW : Cli::COLOR_GREEN
		);

	}

	private static function usage() : void {

		Cli::out( 'Usage: php plugins/Maintenance/cli.php [ status | on | off ]', Cli::COLOR_CYAN );
		Cli::out( '  off forces the site back online.' );

	}

}
