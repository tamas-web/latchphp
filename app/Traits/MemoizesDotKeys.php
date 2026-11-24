<?php

namespace App\Traits;

trait MemoizesDotKeys {

	protected static array $dotCache = [];

	protected static function memo( string $cacheKey, callable $resolve ) : mixed {

		if( ! array_key_exists( $cacheKey, self::$dotCache ) ){

			self::$dotCache[ $cacheKey ] = $resolve();

		}

		return self::$dotCache[ $cacheKey ];

	}

	protected static function evictSubtree( string $key ) : void {

		foreach( self::$dotCache as $cached => $value ){

			if(
				$cached === $key ||
				str_starts_with( $key, $cached . '.' ) ||
				str_starts_with( $cached, $key . '.' )
			){

				unset( self::$dotCache[ $cached ] );

			}

		}

	}

	protected static function flushDotCache() : void {

		self::$dotCache = [];

	}

}
