<?php

namespace App\Filters;

use App\Http\URL;

class Common {

	const PASSWORD_MINIM_LENGTH = 8;

	public function boot() : void {

		Filter::add( 'esc_url', [ $this, 'esc_url' ] );
		Filter::add( 'esc_html', [ $this, 'esc_html' ] );
		Filter::add( 'byteConvert', [ $this, 'byteConvert' ] );
		Filter::add( 'alphanumeric', [ $this, 'alphanumeric' ] );
		Filter::add( 'alphanumeric_extended', [ $this, 'alphanumeric_extended' ] );
		Filter::add( 'alphanumeric_extended_no_space', [ $this, 'alphanumeric_extended_no_space' ] );
		Filter::add( 'sanitize_phone_number', [ $this, 'sanitize_phone_number' ] );
		Filter::add( 'sanitize_string', [ $this, 'sanitize_string' ] );
		Filter::add( 'validate_email', [ $this, 'validate_email' ] );
		Filter::add( 'is_strong_password', [ $this, 'is_strong_password' ] );
		Filter::add( 'password_minim_length', [ $this, 'password_minim_length' ] );
		Filter::add( 'password_has_special_char', [ $this, 'password_has_special_char' ] );
		Filter::add( 'password_has_letter', [ $this, 'password_has_letter' ] );
		Filter::add( 'short_description', [ $this, 'short_description' ] );
		Filter::add( 'format_description', [ $this, 'format_description' ] );

	}

	public function esc_url( $str ){
		if( '' === $str ){
			return '';
		}
		if( ! URL::isAbsolute( $str ) ){
			$str = URL::BUILD( $str );
		}
		$url = filter_var( $str, FILTER_VALIDATE_URL );
		if( false === $url ){
			return '';
		}
		return $this->esc_html( $url );
	}

	public function esc_html( $str ){
		return filter_var( $str, FILTER_SANITIZE_FULL_SPECIAL_CHARS );
	}

	public function byteConvert( $bytes ) : string {
		$s = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB' );
		$e = floor( log( $bytes ) / log( 1024 ) );
		return sprintf( '%.2f ' . $s[ $e ], ( $bytes / pow( 1024, floor( $e ) ) ) );
	}

	public function alphanumeric( string $str ) : string {

		return preg_replace( '/[^a-zA-Z0-9]/', '', $str );

	}

	public function alphanumeric_extended( string $str ) : string {

		return preg_replace( '/[^a-zA-Z0-9\-_\s\[\]]/', '', $str );

	}

	public function alphanumeric_extended_no_space( string $str ) : string {

		return preg_replace( '/[^a-zA-Z0-9\-_\[\]]/', '', $str );

	}

	public function sanitize_phone_number( string $string ) : string {

		// sanitize phone number
		$string = preg_replace( '/[^0-9+]/', '', $string );

		// normalize the international prefix: + -> 00
		$string = preg_replace( '/^\+/', '00', $string );

		// further sanitize
		$string = preg_replace( '/[^0-9]/', '', $string );

		// return phone number if length is between 8 and 15
		$length = strlen( $string );
		if( $length >= 8 && $length <= 15 ){
			return $string;
		}

		// else return empty string
		return '';

	}

	public function validate_email( $email ){

		$email = strtolower( $email );

		if( preg_match( "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,6})$/", $email ) ){

			if( defined( 'MAIL_DOMAIN_LOOKUP' ) && \MAIL_DOMAIN_LOOKUP ){

				list( $username, $domain ) = preg_split( '/@/', $email );

				if( ! checkdnsrr( $domain, 'MX' ) ){

					return false;

				}

			}

			return $email;

		}

		return false;

	}

	public function is_strong_password( string $password ) : bool {

		return
			$this->password_minim_length( $password ) &&
			$this->password_has_special_char( $password ) &&
			$this->password_has_letter( $password );

	}

	public function password_minim_length( string $password ) : bool {

		return strlen( $password ) >= self::PASSWORD_MINIM_LENGTH;

	}

	public function password_has_special_char( string $password ) : bool {

		return preg_match( '/[!-@\[-`{-~]+/', $password );

	}

	public function password_has_letter( string $password ) : bool {

		return preg_match( '/[a-zA-Z]+/', $password );

	}

	public function sanitize_string( string $string ) : string {

		$string = strip_tags( $string );

		$string = htmlspecialchars( $string, ENT_QUOTES | ENT_SUBSTITUTE );

		$string = str_replace(
			[ '&quot;', '&#039;' ],
			[ '&#34;', '&#39;' ],
			$string
		);

		$string = html_entity_decode( $string, ENT_QUOTES | ENT_SUBSTITUTE );

		return trim( filter_var( $string, FILTER_UNSAFE_RAW, FILTER_FLAG_NO_ENCODE_QUOTES ) );

	}

	// this is just a plain version, need to improve to take into account maybe word count as well
	public function short_description( string $string, int $max_length = 250 ) : string {

		if( mb_strlen( $string ) > $max_length ){

			$string = mb_substr( $string, 0, $max_length );

			$string = trim( $string );

			$string.= '...';

		}

		return $string;

	}

	public function format_description( string $string ) : string {

		$string = trim( $string );
		$string = $this->esc_html( $string );
		$string = nl2br( $string );

		// encapsulate everything in p tags
		$parts  = explode( '<br />', $string );
		$parts  = array_map( 'trim', $parts );
		$parts  = array_filter( $parts );
		$parts  = array_map( fn( $part ) => sprintf( '<p>%s</p>', $part ), $parts );

		// remove all breaks and add single break
		// $string = preg_replace( "/<br \/>[\n\r]+<br \/>/", '<br />', $string );

		// return $string;

		return join( '', $parts );

	}

}
