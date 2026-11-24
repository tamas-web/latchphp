<?php

namespace Plugins\Recovery;

use App\Auth\GuestOnly;
use App\Auth\VerifyNonce;
use App\Events\Listener;
use App\Filters\Filter;
use App\Http\URL;
use App\Libraries\Plugin;
use App\Routing\Route;
use Plugins\Recovery\Controllers\RecoveryController;

class Recovery extends Plugin {

	public function run(){

		Listener::on( 'routes_loaded', [ $this, 'addRoutes' ] );

		Filter::add( 'login_form_links', [ $this, 'loginLink' ] );

	}

	public function addRoutes(){

		Route::get( '/password-reset', [ RecoveryController::class, 'form' ] )
			->middleware( [ GuestOnly::class ] );

		Route::get( '/password-reset/{string}', [ RecoveryController::class, 'changePassword' ] )
			->middleware( [ GuestOnly::class ] );

		Route::post( '/api/forms/password-reset', [ RecoveryController::class, 'processRecover' ] )
			->middleware( [ GuestOnly::class, VerifyNonce::class ] );

		Route::post( '/api/forms/password-reset/change-password', [ RecoveryController::class, 'processChangePassword' ] )
			->middleware( [ GuestOnly::class, VerifyNonce::class ] );

	}

	public function loginLink( string $html ) : string {

		return $html . sprintf(
			'<p class="text-center"><a class="forgot-password" href="%s">Forgot your password? <i class="bi bi-arrow-right"></i></a></p>',
			URL::BUILD( 'password-reset' )
		);

	}

}
