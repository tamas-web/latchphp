<?php

use App\Bootloader;

/**
 * Check for PHP 8 >
 */
if( ! version_compare( \PHP_VERSION, '8.0', '>=' ) ){

	exit( sprintf( 'To run this app, you need at least PHP 8.0 [current version: %s]' . PHP_EOL, \PHP_VERSION ) );

}

define( 'APP_STARTUP_TIME', microtime( true ) );

// app directory
define( 'APP_DIR', dirname( dirname( __FILE__ ) ) );

require_once __DIR__ . '/../vendor/autoload.php';

// development || production (env-driven, safe default)
define( 'APP_ENVIRONMENT', env( 'APP_ENV', 'production' ) );

$app = new Bootloader;

$app->init();
