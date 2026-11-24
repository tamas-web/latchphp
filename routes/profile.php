<?php

use App\Routing\Route;
use App\Auth\MustBeLoggedIn;
use App\Auth\VerifyNonce;
use App\Controllers\ProfileController;

// Self-service profile - any logged-in user edits their own profile (no Can gate).
Route::get( '/profile', [ ProfileController::class, 'profileForm' ] )
	->middleware( [ MustBeLoggedIn::class ] );

Route::post( '/api/profile', [ ProfileController::class, 'profileUpdate' ] )
	->middleware( [ MustBeLoggedIn::class, VerifyNonce::class ] );
