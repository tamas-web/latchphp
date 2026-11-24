<?php

use App\Routing\Route;

Route::get( '/api/alerts/clear', [ \App\Controllers\ApiController::class, 'clearAlerts' ] );
