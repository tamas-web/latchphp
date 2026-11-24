<?php

namespace App\Auth;

use App\Config;
use App\Database\Connector as DB;
use App\Events\Emitter;
use App\Http\Cookie;
use App\Http\Request;
use App\Libraries\User;

/**
 * My mind is all over the place, I'm still confused about how I should
 * handle all this stuff
 */

class Auth extends User {

	protected Request $request;

	public function __construct(){

		$user_loggedin = Request::cookie( 'user_loggedin', [ $this, 'deconstructUserCookie' ] );

		if( $user_loggedin ){

			$user = self::findById( $user_loggedin['id'] );

			if( $user && $user->isEnabled() ){

				if( hash_equals(
					$this->generateUserCookieValue( $user->id, $user->public_key, $user->private_key, $user_loggedin['time'] ),
					(string) Request::cookie( 'user_loggedin' )
				) ){

					// set user id
					$this->id = $user->id;

					// set user options
					$this->options = $user->options;

					// set user data
					$this->data = $user->getRawData();

					// update user last seen
					$this->updateLastSeen();

					// emit successfull auth
					Emitter::broadcast( 'auth_valid_user', $this );

				} else {

					// delete cookie
					Cookie::delete( 'user_loggedin' );

					// redirect to home page
					Request::getInstance()->redirect( '/login' );

				}

			} else {

				// delete cookie
				Cookie::delete( 'user_loggedin' );

				// redirect to home page
				Request::getInstance()->redirect( '/login' );

			}

		}

		return $this;

	}

	public function deconstructUserCookie( $cookie ) : ?array {

		$cookie = explode( ':', $cookie );

		if(
			// if all elements are set
			isset( $cookie[0], $cookie[1], $cookie[2], $cookie[3] ) &&
			// if 1st is int and > 0
			ctype_digit( $cookie[0] ) && $cookie[0] > 0 &&
			// if 2nd is alphanumeric and length == 32
			ctype_alnum( $cookie[1] ) && strlen( $cookie[1] ) === self::PUBLIC_PRIVATE_KEY_LENGTH &&
			// if 3rd is int and > 0
			ctype_digit( $cookie[2] ) && $cookie[2] > 0 &&
			// if 4th is alphanumeric
			! empty( $cookie[3] )
		){

			return [
				'id'         => $cookie[0],
				'public_key' => $cookie[1],
				'time'       => $cookie[2],
				'hash'       => $cookie[3],
			];

		}

		return null;

	}

	/**
	 * Generate user cookie
	 */
	public function generateUserCookieValue( $id, $public_key, $private_key, $time = null ) : string {

		if( ! $time ){
			$time = time();
		}

		$ip = Request::server( 'remote_addr' );
		//~ $browser = Request::server( 'http_user_agent' );
		$browser = '__NOTHING__';

		return (
			$id . ':' .
			$public_key . ':' .
			$time . ':' .
			base64_encode(
				hash_hmac(
					'sha512',
					(
						$time .
						Config::get( 'app.salt.first' ) .
						$id .
						$ip .
						$browser .
						sha1(
							Config::get( 'app.salt.second' ) .
							$time .
							$browser .
							sha1(
								$private_key .
								Config::get( 'app.salt.third' ) .
								$public_key .
								$ip
							)
						) .
						$time .
						$public_key .
						Config::get( 'app.salt.fourth' )
					),
					Config::get( 'app.salt.second' ),
					true
				)
			)
		);

	}

}
