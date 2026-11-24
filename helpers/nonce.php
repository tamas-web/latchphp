<?php

use App\Libraries\Nonce;

if( ! function_exists( 'create_nonce' ) ){

	function create_nonce( string $action = '' ) : string {

		return Nonce::create( $action );

	}

}

if( ! function_exists( 'verify_nonce' ) ){

	function verify_nonce( string $action = '', string $nonce = '' ) : bool {

		return Nonce::verify( $action, $nonce );

	}

}

if( ! function_exists( 'nonce_field' ) ){

	function nonce_field( string $action = '' ) : string {

		return sprintf(
			'<input type="hidden" name="%s" value="%s">',
			Nonce::FIELD,
			esc_html( create_nonce( $action ) )
		);

	}

}
