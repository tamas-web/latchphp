<?php

use App\Bootloader;
use Plugins\Maintenance\Maintenance;

if( ! version_compare( \PHP_VERSION, '8.0', '>=' ) ){

	exit( sprintf( 'To run this app, you need at least PHP 8.0 [current version: %s]' . PHP_EOL, \PHP_VERSION ) );

}

if( 'cli' !== php_sapi_name() ){

	exit( 'This script runs from the command line only.' . PHP_EOL );

}

define( 'APP_STARTUP_TIME', microtime( true ) );

define( 'APP_DIR', dirname( dirname( dirname( __FILE__ ) ) ) );

require_once APP_DIR . '/vendor/autoload.php';

define( 'APP_ENVIRONMENT', env( 'APP_ENV', 'production' ) );

$app = new Bootloader;

$app->initKernel();

Maintenance::cli( $argv );
