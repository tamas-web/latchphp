<?php

use App\Routing\Route;
use App\Auth\{ MustBeLoggedIn, Can };
use App\Controllers\System\SystemController;

Route::get( '/admin/system/backup', [ SystemController::class, 'backup' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'system.backup' ] ] );

Route::get( '/admin/system/backup/download/{string}', [ SystemController::class, 'backupDownload' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'system.backup' ] ] );

Route::get( '/admin/system/logs', [ SystemController::class, 'logs' ] )
	->middleware( [ MustBeLoggedIn::class, [ Can::class, 'system.logs' ] ] );
