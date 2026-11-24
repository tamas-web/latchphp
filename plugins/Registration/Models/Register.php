<?php

namespace Plugins\Registration\Models;

use App\Libraries\{ Model, User };
use App\Models\Users;

class Register extends Model {

	use \App\Traits\encodeDecodeData;

	/**
	 * User creation lives in the core Users model now; the registration domain
	 * model just references it, so the controller keeps a single registration API.
	 */
	protected function createUser( string $name, ?string $surname, string $email, ?string $phone, string $password, int $options = self::ACCOUNT_ENABLED, int $idRole = 0 ) : ?User {

		return Users::createUser(
			name: $name,
			surname: $surname,
			email: $email,
			phone: $phone,
			password: $password,
			options: $options,
			idRole: $idRole
		);

	}

	protected function checkIfEmailPhoneExists( string $email, string $phone ) : array {

		return Users::checkIfEmailPhoneExists( email: $email, phone: $phone );

	}

	protected function confirmEmail( string $key ) : bool {

		if( ! ctype_xdigit( $key ) ){

			return false;

		}

		$data = explode( '|', self::decode( $key ) );

		if( 2 !== count( $data ) ){

			return false;

		}

		list( $email, $token ) = $data;

		$user = User::findByEmail( $email );

		if( $user && $user->meta()->confirmAccountToken && ( 0 === strcmp( $token, $user->meta()->confirmAccountToken ) ) ){

			if( $user->setOption( self::ACCOUNT_EMAIL_CONFIRMED )->save() ){

				$user->meta()->delete( 'confirmAccountToken' );
				$user->meta()->delete( 'confirmAccountNextInterval' );

			}

			return true;

		}

		return false;

	}

}
