<?php

use App\Routing\Route;

// homepage
Route::get( '/', [ \App\Controllers\DashboardController::class, 'index' ] )
	->middleware( [
		\App\Auth\MustBeLoggedIn::class
	] );
