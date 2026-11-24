<?php

use App\Routing\Route;

Route::get( '/notifications', [ \App\Controllers\NotificationsController::class, 'notifications' ] )
->middleware( [
	\App\Auth\MustBeLoggedIn::class,
] );

Route::get( '/notifications/{id}', [ \App\Controllers\NotificationsController::class, 'notification' ] )
->middleware( [
	\App\Auth\MustBeLoggedIn::class,
] );

Route::post( '/api/notifications/delete', [ \App\Controllers\NotificationsController::class, 'deleteAllNotifications' ] )
	->middleware( [
		\App\Auth\MustBeLoggedIn::class,
		\App\Auth\VerifyNonce::class,
	] );

Route::post( '/api/notifications/delete/{id}', [ \App\Controllers\NotificationsController::class, 'deleteNotification' ] )
	->middleware( [
		\App\Auth\MustBeLoggedIn::class,
		\App\Auth\VerifyNonce::class,
	] );