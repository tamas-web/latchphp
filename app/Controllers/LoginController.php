<?php

namespace App\Controllers;

use App\Alerts\Alert;
use App\Config;
use App\Database\Connector as DB;
use App\Datatype\Response;
use App\Http\{ Cookie, Request, URL };
use App\Filters\Filter;
use App\Libraries\{ Controller, Password, User };

class LoginController extends Controller {

	protected $pageTitle = 'Login';

	public function form( Request $request ){

		Filter::add( 'body_class', fn( $classList ) => array_merge( $classList, [ 'login-page' ] ) );

		$request->view()
			->header()
				->remove( 'wrapper_start' )
				->remove( 'header.top' )
				->remove( 'header.menu' )
				->remove( 'content_start' );

		$request->view()
			->body()
				->add( 'login.form' );

		$request->view()
			->footer()
				->remove( 'wrapper_end' )
				->remove( 'content_end' );

		$request->view()->render();

	}

	public function processLogin( Request $request ){

		$response = new Response;

		$signin = $request->post( 'signin', 'trim' );

		if( $signin ){

			$email    = $request->post( 'email', fn( $email ) => validate_email( $email ) );
			$password = $request->post( 'password', 'trim' );

			if( ! $email || ! $password ){

				Alert::error( 'Incorrect details. Please check your email address or password.' );

			} else {

				// query user and check pass
				$user = User::findByEmail( $email );


				if( $user ){

					if( $user->isEnabled() ){

						if( Password::verify( $password, $user->password ) ){

							if( Password::needsRehash( $user->password ) ){

								$user->password = Password::hash( $password );
								$user->save();

							}

							// new session id on login (fixation hardening)
							session()->regenerate();

							// create user cookie
							$cookie = $request->user()->generateUserCookieValue( id: $user->id, public_key: $user->public_key, private_key: $user->private_key );

							// remember me
							$remember = $request->post( 'remember', 'trim' );

							// set user cookie
							Cookie::set( key: 'user_loggedin', value: $cookie, expiration: $remember ? self::YEAR : self::DAY );

							// update
							$user->updateCurrentLogin()
								 ->updateLastSeen();

							// set redirect url
							$request->redirect( '/' );

						} else {

							Alert::error( 'Incorrect details. Please check your email address or password.' );

						}

					} else {

						Alert::error( 'This user account is disabled.' );

					}

				} else {

					Alert::error( 'Incorrect details. Please check your email address or password.' );

				}

			}

		} else {

			Alert::error( 'Authentication error.' );

		}

		echo $response;

	}

	public function logout( Request $request ){

		Cookie::delete( 'user_loggedin' );

		$request->redirect( '/login' );

	}

}
