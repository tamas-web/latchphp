<?php

namespace App\ErrorHandlers;

/**
 * Minimal append-only file logger.
 * One sub-folder per channel (errors, cron, mail, ...), one file per day:
 *   logs/<channel>/<Y-m-d>.log
 */
class Logger {

	/**
	 * Append a line to the channel's log for today
	 */
	public static function write( string $channel, string $message ) : void {

		$channel = preg_replace( '/[^a-z0-9_-]/', '', strtolower( $channel ) );

		if( '' === $channel ){
			$channel = 'app';
		}

		$dir = APP_DIR . '/logs/' . $channel;

		if( ! is_dir( $dir ) ){

			mkdir( $dir, 0755, true );

		}

		$line = sprintf( '[%s] %s%s', date( 'Y-m-d H:i:s' ), $message, PHP_EOL );

		file_put_contents( $dir . '/' . date( 'Y-m-d' ) . '.log', $line, FILE_APPEND | LOCK_EX );

	}

}
