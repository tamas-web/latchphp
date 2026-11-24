<?php

namespace App\Libraries;

use App\Config;
use App\Constants;

class Password implements Constants {

	static public function hash( string $password ) : string {

		return password_hash(
			self::salt_and_pepper( $password ),
			self::PASSWORD_ENCRYPTION_TYPE,
			self::PASSWORD_ENCRYPTION_SETTINGS
		);

	}

	static public function verify( string $password, string $hash ) : string {

		return password_verify(
			self::salt_and_pepper( $password ),
			$hash
		);

	}

	static public function needsRehash( string $hash ) : bool {

		return password_needs_rehash(
			$hash,
			self::PASSWORD_ENCRYPTION_TYPE,
			self::PASSWORD_ENCRYPTION_SETTINGS
		);

	}

	static public function salt_and_pepper( string $password ) : string {

		$salty_password = hash_hmac( algo: 'sha256', data: $password, key: Config::get( 'app.salt.first' ) );
		$salty_password = hash_hmac( algo: 'sha256', data: Config::get( 'app.salt.second' ), key: $salty_password );

		return $salty_password;

	}

}
