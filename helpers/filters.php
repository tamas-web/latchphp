<?php

/**
 * Callable wrappers for the filters registered by App\Filters\Common.
 * Call esc_html( $x ) instead of Filter::apply( 'esc_html', $x ) - no import, no string tag to typo.
 * Each forwards any extra arguments through to Filter::apply().
 */

use App\Filters\Filter;

if( ! function_exists( 'esc_url' ) ){

	function esc_url( $value, ...$args ){

		return Filter::apply( 'esc_url', $value, ...$args );

	}

}

if( ! function_exists( 'esc_html' ) ){

	function esc_html( $value, ...$args ){

		return Filter::apply( 'esc_html', $value, ...$args );

	}

}

if( ! function_exists( 'byteConvert' ) ){

	function byteConvert( $value, ...$args ){

		return Filter::apply( 'byteConvert', $value, ...$args );

	}

}

if( ! function_exists( 'alphanumeric' ) ){

	function alphanumeric( $value, ...$args ){

		return Filter::apply( 'alphanumeric', $value, ...$args );

	}

}

if( ! function_exists( 'alphanumeric_extended' ) ){

	function alphanumeric_extended( $value, ...$args ){

		return Filter::apply( 'alphanumeric_extended', $value, ...$args );

	}

}

if( ! function_exists( 'alphanumeric_extended_no_space' ) ){

	function alphanumeric_extended_no_space( $value, ...$args ){

		return Filter::apply( 'alphanumeric_extended_no_space', $value, ...$args );

	}

}

if( ! function_exists( 'sanitize_phone_number' ) ){

	function sanitize_phone_number( $value, ...$args ){

		return Filter::apply( 'sanitize_phone_number', $value, ...$args );

	}

}

if( ! function_exists( 'sanitize_string' ) ){

	function sanitize_string( $value, ...$args ){

		return Filter::apply( 'sanitize_string', $value, ...$args );

	}

}

if( ! function_exists( 'validate_email' ) ){

	function validate_email( $value, ...$args ){

		return Filter::apply( 'validate_email', $value, ...$args );

	}

}

if( ! function_exists( 'is_strong_password' ) ){

	function is_strong_password( $value, ...$args ){

		return Filter::apply( 'is_strong_password', $value, ...$args );

	}

}

if( ! function_exists( 'password_minim_length' ) ){

	function password_minim_length( $value, ...$args ){

		return Filter::apply( 'password_minim_length', $value, ...$args );

	}

}

if( ! function_exists( 'password_has_special_char' ) ){

	function password_has_special_char( $value, ...$args ){

		return Filter::apply( 'password_has_special_char', $value, ...$args );

	}

}

if( ! function_exists( 'password_has_letter' ) ){

	function password_has_letter( $value, ...$args ){

		return Filter::apply( 'password_has_letter', $value, ...$args );

	}

}

if( ! function_exists( 'short_description' ) ){

	function short_description( $value, ...$args ){

		return Filter::apply( 'short_description', $value, ...$args );

	}

}

if( ! function_exists( 'format_description' ) ){

	function format_description( $value, ...$args ){

		return Filter::apply( 'format_description', $value, ...$args );

	}

}
