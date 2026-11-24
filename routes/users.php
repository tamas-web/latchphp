<?php

use App\Routing\Route;
use App\Auth\{ MustBeLoggedIn, Can, VerifyNonce };
use App\Controllers\UsersController;

Route::get( '/admin/users', [ UsersController::class, 'users' ] )
	->middleware( [
		MustBeLoggedIn::class,
		[ Can::class, 'users.view' ],
	] );

Route::get( '/admin/users/add', [ UsersController::class, 'addForm' ] )
	->middleware( [
		MustBeLoggedIn::class,
		[ Can::class, 'users.add' ],
	] );

Route::get( '/admin/users/{id}', [ UsersController::class, 'user' ] )
	->middleware( [
		MustBeLoggedIn::class,
		[ Can::class, 'users.view' ],
	] );

Route::get( '/admin/users/{id}/delete', [ UsersController::class, 'deleteForm' ] )
	->middleware( [
		MustBeLoggedIn::class,
		[ Can::class, 'users.delete' ],
	] );

Route::post( '/api/users/add', [ UsersController::class, 'add' ] )
	->middleware( [
		MustBeLoggedIn::class,
		VerifyNonce::class,
		[ Can::class, 'users.add' ],
	] );

Route::post( '/api/users/{id}', [ UsersController::class, 'update' ] )
	->middleware( [
		MustBeLoggedIn::class,
		VerifyNonce::class,
		[ Can::class, 'users.edit' ],
	] );

Route::post( '/api/users/{id}/delete', [ UsersController::class, 'delete' ] )
	->middleware( [
		MustBeLoggedIn::class,
		VerifyNonce::class,
		[ Can::class, 'users.delete' ],
	] );
