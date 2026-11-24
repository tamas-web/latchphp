<?php

use App\Routing\Route;
use App\Controllers\StyleguideController;
use App\Auth\MustBeLoggedIn;

Route::get( '/styleguide/content', [ StyleguideController::class, 'content' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::get( '/styleguide/forms-stacked', [ StyleguideController::class, 'formsStacked' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::get( '/styleguide/forms-horizontal', [ StyleguideController::class, 'formsHorizontal' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::get( '/styleguide/tables', [ StyleguideController::class, 'tables' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::get( '/styleguide/components', [ StyleguideController::class, 'components' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::get( '/styleguide/widgets', [ StyleguideController::class, 'widgets' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::get( '/styleguide/feedback', [ StyleguideController::class, 'feedback' ] )
	->middleware( [ MustBeLoggedIn::class ] );
