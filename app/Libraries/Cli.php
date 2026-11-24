<?php

namespace App\Libraries;

class Cli {

	const COLOR_BLACK   = "\033[30m";
	const COLOR_RED     = "\033[31m";
	const COLOR_GREEN   = "\033[32m";
	const COLOR_YELLOW  = "\033[33m";
	const COLOR_BLUE    = "\033[34m";
	const COLOR_MAGENTA = "\033[35m";
	const COLOR_CYAN    = "\033[36m";
	const COLOR_WHITE   = "\033[37m";
	const COLOR_RESET   = "\033[0m";

	static function out( mixed $data, string $color = '', bool $addNewLine = true ) : void {

		if( 'cli' === php_sapi_name() ){

			if( $color ){
				echo $color;
			}

			if( is_array( $data ) || is_object( $data ) ){

				print_r( $data );

			} else {

				echo $data;

				if( $addNewLine ){

					echo PHP_EOL;

				}

			}

			if( $color ){
				echo self::COLOR_RESET;
			}

		}

	}

}
