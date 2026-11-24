<?php

namespace App\Libraries;

use App\Constants;

class Model implements Constants {

	protected static int $errno = 0;
	protected static string $error = '';

	public static function __callStatic( $method, $arguments ){

		$class = get_called_class();

		return ( new $class )->$method( ...$arguments );

	}

	protected function getLastErrno() : int {

		return self::$errno;

	}

	protected function getLastError() : string {

		return self::$error;

	}

}
