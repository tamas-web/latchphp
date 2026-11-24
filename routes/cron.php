<?php

use App\Routing\Route;

Route::get( '/cron', [ \App\Cron\Http::class, 'init' ] );
