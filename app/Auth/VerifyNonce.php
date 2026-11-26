<?php

namespace App\Auth;

use App\Alerts\Alert;
use App\Datatype\Response;
use App\Http\Interfaces\MiddlewareInterface;
use App\Http\Request;
use App\Http\URL;
use App\Libraries\Nonce;

class VerifyNonce implements MiddlewareInterface {

	public function __construct( protected string $action = '', protected string $field = Nonce::FIELD ){}

	public function handle( Request $request, bool &$passed ){

		if( Nonce::verify( $this->action, $this->token( $request ) ) ){

			return;

		}

		$passed = false;

		Alert::error( 'Your security token has expired. Please reload the page and try again.' );

		if( str_contains( (string) Request::server( 'http_accept' ), 'text/html' ) ){

			$referer = (string) Request::server( 'http_referer' );

			$request->redirect( str_starts_with( $referer, URL::BASE() ) ? $referer : '/' );

		}

		http_response_code( 403 );

		echo new Response;

		exit;

	}

	protected function token( Request $request ) : string {

		return (string) ( $request->post( $this->field ) ?: Request::server( 'http_x_csrf_token' ) );

	}

}
