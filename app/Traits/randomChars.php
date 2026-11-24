<?php

namespace App\Traits;

trait randomChars {

	public function randomChars( int $length, bool $useSpecialChars = false ) : string {

		if( $useSpecialChars ){

			$chars  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()_+-=|<>,.;:';

		} else {

			$chars  = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

		}

		$str    = '';

		for( $i = 0; $i < $length; $i++ ){

			$str .= $chars[ \random_int( 0, ( \strlen( $chars ) - 1 ) ) ];

		}

		return $str;

	}

}
