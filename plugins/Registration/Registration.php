<?php

namespace Plugins\Registration;

use App\Auth\{ GuestOnly, MustBeLoggedIn, VerifyNonce };
use App\Config;
use App\Events\Listener;
use App\Filters\Filter;
use App\Http\URL;
use App\Libraries\Plugin;
use App\Models\Settings\Settings;
use App\Routing\Route;
use Plugins\Registration\Blocks\EmailConfirm;
use Plugins\Registration\Controllers\RegisterController;

class Registration extends Plugin {

	public function run(){

		Listener::on( 'routes_loaded', [ $this, 'addRoutes' ] );

		// email-confirmation nag for logged-in, unconfirmed users
		( new EmailConfirm )->boot();

		Listener::on( 'app_settings_form', [ $this, 'settingsField' ] );
		Listener::on( 'app_settings_save', [ $this, 'settingsSave' ] );

		Filter::add( 'login_form_links', [ $this, 'loginLink' ] );

	}

	public function settingsField( $view ) : void {

		echo '
		<fieldset>
			<legend>Registration</legend>
			<div class="switch-row mb-3">
				<div class="switch-row-text">
					<label class="title" for="registration_enabled">Allow account registration</label>
					<div class="sub">Enable the public account registration page.</div>
				</div>
				<div class="form-check form-switch m-0">
					<input class="form-check-input" type="checkbox" role="switch" id="registration_enabled" name="registration_enabled" value="1" ', ( Config::get( 'settings.users.registration.enabled' ) ? 'checked' : '' ), '>
				</div>
			</div>
		</fieldset>';

	}

	public function settingsSave( $request, $response ) : void {

		Config::set( 'settings.users.registration.enabled', (bool) $request->post( 'registration_enabled' ) );

		Settings::save( 'users', Config::get( 'settings.users' ) );

	}

	public function addRoutes(){

		Route::get( '/register', [ RegisterController::class, 'form' ] )
			->middleware( [ GuestOnly::class ] );

		Route::post( '/api/forms/register', [ RegisterController::class, 'processRegister' ] )
			->middleware( [ GuestOnly::class, VerifyNonce::class ] );

		Route::get( '/register/confirm/email/{string}', [ RegisterController::class, 'confirmAccount' ] );

		Route::post( '/api/forms/register/resend-confirmation-email', [ RegisterController::class, 'resendConfirmationEmail' ] )
			->middleware( [ MustBeLoggedIn::class, VerifyNonce::class ] );

	}

	public function loginLink( string $html ) : string {

		if( ! Config::get( 'settings.users.registration.enabled' ) ){

			return $html;

		}

		return $html . sprintf(
			'<p class="text-center"><a class="register-link" href="%s">Create an account</a></p>',
			URL::BUILD( 'register' )
		);

	}

}
