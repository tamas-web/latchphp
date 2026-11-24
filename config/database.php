<?php

return [

	/* DB Credentials (set real values in .env) */
	'hostname' => env( 'DB_HOST', 'database' ), // yaml services name
	'username' => env( 'DB_USER', 'starter' ),
	'password' => env( 'DB_PASS', '' ),
	'database' => env( 'DB_NAME', 'starter' ),

	/* Admin DB Credentials */
	'admin'    => [
		'hostname' => env( 'DB_ADMIN_HOST', env( 'DB_HOST', 'database' ) ),
		'username' => env( 'DB_ADMIN_USER', env( 'DB_USER', 'starter' ) ),
		'password' => env( 'DB_ADMIN_PASS', env( 'DB_PASS', '' ) ),
		'database' => env( 'DB_ADMIN_NAME', env( 'DB_NAME', 'starter' ) ),
	],

];
