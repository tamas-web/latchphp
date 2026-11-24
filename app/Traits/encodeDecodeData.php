<?php

namespace App\Traits;

trait encodeDecodeData {

	protected function encode( array|string $string ) : string {

		$string = is_array( $string )
			? join( '|', $string )
			: $string;

		return unpack( 'H*', $string )[1];

	}

	protected function decode( string $string ) : string {

		return pack( 'H*', $string );

	}

}
