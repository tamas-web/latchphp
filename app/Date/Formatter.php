<?php

namespace App\Date;

use App\Config;
use Datetime;

class Formatter {

	/**
	 * Format a localized date
	 * @pattern string https://unicode-org.github.io/icu/userguide/format_parse/datetime/#date-field-symbol-table
	 */
	public static function format( $datetime, string $pattern = '' ) : mixed {

		$formatter = new \IntlDateFormatter(
			Config::get( 'app.date.locale' ),
			\IntlDateFormatter::LONG,
			\IntlDateFormatter::MEDIUM,
			Config::get( 'app.date.timezone' ),
			\IntlDateFormatter::GREGORIAN,
			$pattern
		);

		if( is_string( $datetime ) ){

			$datetime = new DateTime( $datetime );

		}

		return $formatter->format( $datetime );

	}

}
