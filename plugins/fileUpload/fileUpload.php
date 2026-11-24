<?php

namespace Plugins\fileUpload;

use App\Config;
use App\Filters\Filter;
use App\Libraries\{ User, Upload, Plugin };

class fileUpload extends Plugin {

	public function run(){

		Filter::add( 'file_upload_jpg', [ $this, 'checkImageSize' ] );
		Filter::add( 'file_upload_jpeg', [ $this, 'checkImageSize' ] );
		Filter::add( 'file_upload_png', [ $this, 'checkImageSize' ] );

	}

	public function checkImageSize( bool $passed, Upload $upload ) : bool {
		$size = @getimagesize( $upload->get_tmp_name() );

		if( ! $size || $size[0] < Config::get( 'app.files.images.minSize.width' ) || $size[1] < Config::get( 'app.files.images.minSize.height' ) ){

			$upload->add_error( sprintf(
				'The image "%s" is too small.
				Please choose a larger image
				of at least %u x %u px.',
				esc_html( $upload->get_file_name() ),
				Config::get( 'app.files.images.minSize.width' ),
				Config::get( 'app.files.images.minSize.height' )
			) );
			$passed = false;

		}

		return $passed;

	}

}
