<?php

namespace App\GD;

use App\Config;
use App\GD\Base64;

class DataImage extends Base64 {

	public function __construct( ?string $data ){

		$allowed = Config::get( 'app.files.allowed.image' );
		$allowed = array_map( fn( $item ) => sprintf( 'image/%s', $item ), $allowed );

		parent::__construct( data: $data, allowedMimes: $allowed );

	}

}