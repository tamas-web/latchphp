<?php

/**
 * Minimal dependency-free .env loader + env() accessor.
 * Loaded by every entry point before the kernel boots, so config/*.php can read
 * secrets/settings via env(). Real environment variables always take precedence
 * over values defined in the .env file.
 */

if( ! function_exists( 'env' ) ){

	function env( string $key, mixed $default = null ) : mixed {

		$value = $_ENV[ $key ] ?? getenv( $key );

		if( false === $value || null === $value ){
			return $default;
		}

		return match( strtolower( (string) $value ) ){
			'true'  => true,
			'false' => false,
			'null'  => null,
			'empty' => '',
			default => $value,
		};

	}

}

if( ! function_exists( 'load_env_file' ) ){

	function load_env_file( string $path ) : void {

		if( ! is_readable( $path ) ){
			return;
		}

		foreach( file( $path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES ) as $line ){

			$line = trim( $line );

			if( '' === $line || str_starts_with( $line, '#' ) ){
				continue;
			}

			if( false === strpos( $line, '=' ) ){
				continue;
			}

			[ $key, $value ] = explode( '=', $line, 2 );

			$key   = trim( $key );
			$value = trim( $value );

			// strip a single layer of matching quotes
			if( strlen( $value ) >= 2 && ( $value[0] === '"' || $value[0] === "'" ) && $value[ -1 ] === $value[0] ){
				$value = substr( $value, 1, -1 );
			}

			// real environment wins; do not override
			if( false !== getenv( $key ) || isset( $_ENV[ $key ] ) ){
				continue;
			}

			putenv( sprintf( '%s=%s', $key, $value ) );
			$_ENV[ $key ] = $value;

		}

	}

}

load_env_file( ( defined( 'APP_DIR' ) ? APP_DIR : __DIR__ . '/..' ) . '/.env' );
