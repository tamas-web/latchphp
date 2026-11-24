<?php

use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\PHPMailer;

$https = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ? 'https://' : 'http://';

if( ! isset( $_SERVER['HTTP_HOST'] ) ){
    $_SERVER['HTTP_HOST'] = 'localhost';
}

// canonical base URL ( prefer APP_URL; fall back to the request-derived value )
$appUrl    = env( 'APP_URL' ) ?: sprintf( '%s%s/', $https, $_SERVER['HTTP_HOST'] );
$appImages = env( 'APP_URL' )
	? rtrim( $appUrl, '/' ) . '/assets/images/'
	: sprintf( '%s%s/assets/images/', $https, $_SERVER['HTTP_HOST'] );

// SMTP transport encryption ( ssl | tls | none )
$smtpEncryption = match( strtolower( (string) env( 'SMTP_ENCRYPTION', 'ssl' ) ) ){
	'tls', 'starttls' => PHPMailer::ENCRYPTION_STARTTLS,
	'none', ''        => '',
	default           => PHPMailer::ENCRYPTION_SMTPS,
};

return [

	// version
	'version' => '1.0.0',

	// some site settings
	'site' => [
		'name'   => env( 'APP_NAME', 'LatchPHP' ),
		'url'    => $appUrl,
		'images' => [
			'url' => $appImages,
		],
		'title'  => [
			'page'      => 'App',
			'suffix'    => env( 'APP_NAME', 'LatchPHP' ),
			'separator' => ' &ndash; ',
		],
		'language' => env( 'APP_LANG', 'en' ),
	],

	// date setting
	'date' => [
		'locale'   => env( 'APP_LOCALE', 'ro_RO' ),
		'timezone' => env( 'APP_TIMEZONE', 'Europe/Bucharest' ),
	],

	// encoding
	'encoding' => 'UTF-8',

	// cookie settings
	'cookie' => [
		'prefix' => env( 'COOKIE_PREFIX', '' ),
		'domain' => env( 'COOKIE_DOMAIN' ) ?: preg_replace( '/:\d+$/', '', $_SERVER['HTTP_HOST'] ),
	],

	// session settings
	'session' => [
		'name'      => env( 'SESSION_NAME' ) ?: 'SID',
		//~ 'save_path' => APP_DIR . '/cache/sessions',
	],

	// hash salts (set real values in .env)
	'salt' => [
		'first'   => env( 'APP_SALT_FIRST',  'change-me-salt-1' ),
		'second'  => env( 'APP_SALT_SECOND', 'change-me-salt-2' ),
		'third'   => env( 'APP_SALT_THIRD',  'change-me-salt-3' ),
		'fourth'  => env( 'APP_SALT_FOURTH', 'change-me-salt-4' ),
	],

	// smtp
	'smtp' => [
		'debug'     => filter_var( env( 'SMTP_DEBUG', false ), FILTER_VALIDATE_BOOLEAN ) ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF,
		'auth'      => filter_var( env( 'SMTP_AUTH', true ), FILTER_VALIDATE_BOOLEAN ),
		'host'      => env( 'SMTP_HOST', 'localhost' ),
		'port'      => (int) env( 'SMTP_PORT', 465 ),
		'protocol'  => $smtpEncryption, // SMTP_ENCRYPTION: ssl | tls | none
		'username'  => env( 'SMTP_USER', '' ),
		'password'  => env( 'SMTP_PASS', '' ),
		'from'      => [
			'email' => env( 'SMTP_FROM_EMAIL', 'no-reply@example.com' ),
			'name'  => env( 'SMTP_FROM_NAME' ) ?: env( 'APP_NAME', 'LatchPHP' ),
		],
		'bcc'       => env( 'SMTP_BCC', '' ),
	],
	'email' => [
		'subject' => [
			'separator' => env( 'EMAIL_SUBJECT_SEPARATOR', '' ),
			'suffix'    => env( 'EMAIL_SUBJECT_SUFFIX', '' ),
		],
	],

	// // accepted file types
	'files' => [
		'images' => [
			'minSize' => [
				'width'  => 1,
				'height' => 1,
			],
			'maxSize' => [
				'width'  => (int) env( 'UPLOAD_IMAGE_MAX_WIDTH', 1024 ),
				'height' => (int) env( 'UPLOAD_IMAGE_MAX_HEIGHT', 1024 ),
			],
		],
		'upload_dir' => rtrim( env( 'UPLOAD_DIR' ) ?: APP_DIR . '/uploads', '/' ) . '/',
		'allowed'    => [

			// Text Files
			'text'        => [ 'doc', 'docx', 'odt', 'rtf', 'txt' ],

			// Data Files
			'data'        => [ 'csv', 'pps', 'ppt', 'pptx', 'xml', 'odp' ],

			// Spreadsheet Files
			'spreadsheet' => [ 'xls', 'xlsx', 'ods' ],

			// Image Files
			'image'       => [ 'bmp', 'gif', 'jpg', 'jpeg', 'png' ],

			// Page Layout Files
			'pageLayout'  => [ 'pdf' ],

		],
	],

	// nonce
	'nonce' => [
		'salt' => env( 'NONCE_SALT', 'change-me-nonce-salt' ),
		'ttl'  => (int) env( 'NONCE_TTL', 3600 ), // 1 hour
	],

	// ftp backup
	'ftp' => [
		'hostname' => env( 'FTP_HOST', '' ),
		'username' => env( 'FTP_USER', '' ),
		'password' => env( 'FTP_PASS', '' ),
	],

];
