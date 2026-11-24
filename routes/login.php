<?php

use App\Routing\Route;

// login
Route::get( '/login', [ \App\Controllers\LoginController::class, 'form' ] )
	->middleware( [ \App\Auth\GuestOnly::class ] );

Route::post( '/api/forms/login', [ \App\Controllers\LoginController::class, 'processLogin' ] )
	->middleware( [ \App\Auth\GuestOnly::class, \App\Auth\VerifyNonce::class ] );

Route::get( '/logout', [ \App\Controllers\LoginController::class, 'logout' ] )
	->middleware( [ \App\Auth\MustBeLoggedIn::class ] );

