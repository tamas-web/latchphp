<?php

namespace App\Libraries;

use App\Config;
use App\Filters\Filter;
use Cocur\Slugify\Slugify;
use finfo;

class Upload {

	use \App\Traits\randomChars;

	const FILENAME_LENGTH = 22;

	protected string $name;
	protected string $type;
	protected string $tmp_name;
	protected int    $error;
	protected int    $size;
	protected string $extension;
	protected string $new_name;
	protected array  $allowedExtensions;

	protected $error_log = [];

	public function __construct(){

		set_time_limit( 0 );

		$this->allowedExtensions(
			$reduced = array_reduce(
				Config::get( 'app.files.allowed' ),
				fn( $carry, $exts ) => array_merge( $carry, $exts ),
				[]
			)
		);

		if( ! file_exists( Config::get( 'app.files.upload_dir' ) . '.tmp/' ) ){
			mkdir( Config::get( 'app.files.upload_dir' ) . '.tmp/' );
		}

	}

	public function allowedExtensions( array $extensions ) : self {

		$this->allowedExtensions = $extensions;

		return $this;

	}

	public function file( $file ){

		$this->clear();

		if( isset( $file['name'], $file['tmp_name'], $file['error'], $file['size'] ) && $file['tmp_name'] ){

			$finfo = new finfo( FILEINFO_MIME_TYPE );

			$this->type      = $finfo->file( $file['tmp_name'] );
			$this->tmp_name  = $file['tmp_name'];
			$this->error     = $file['error'];
			$this->size      = $file['size'];

			if( $file['name'] ){

				$completeName    = explode( '.', $file['name'] );

				$this->extension = array_pop( $completeName );
				$this->extension = strtolower( $this->extension );

				$completeName    = join( '.', $completeName );
				$completeName    = ( new Slugify )->slugify( $completeName ) ?: 'file';

				$this->name      = sprintf( '%s.%s', $completeName, $this->extension );

			}

			return $this->verifyValidUpload();

		} else {

			$this->add_error( 'File processing error.' );

		}

		return false;

	}

	protected function verifyValidUpload(){

		if( $this->error !== UPLOAD_ERR_NO_FILE ){

			if( $this->name && $this->tmp_name && $this->size && $this->error === UPLOAD_ERR_OK ){

				if( ! in_array( $this->extension, $this->allowedExtensions ) ){

					$this->add_error( 'The file ' . esc_html( $this->name ) . ' is not an allowed file type.' );

				} else {

					return $this->process();

				}

			} else {

				$this->add_error( 'Upload error.' );

			}

		} else {

			$this->clear();

		}

		return false;

	}

	public function clear() : void {

		$this->name      = '';
		$this->type      = '';
		$this->tmp_name  = '';
		$this->error     = 0;
		$this->size      = 0;
		$this->extension = '';
		$this->new_name  = '';

		$this->error_log = [];

	}

	public function get_last_error() : string {

		return end( $this->error_log );

	}

	public function get_errors() : array {

		return $this->error_log;

	}

	public function has_errors() : bool {

		return isset( $this->error_log[0] );

	}

	public function add_error( $msg ) : void {

		$this->error_log[] = $msg;

	}

	public function &get_generated_name() : string {

		return $this->new_name;

	}

	public function &get_file_name() : string {

		return $this->name;

	}

	public function &get_tmp_name() : string {

		return $this->tmp_name;

	}

	public function &get_upload_error() : int {

		return $this->error;

	}

	public function &get_size() : int {

		return $this->size;

	}

	public function &get_file_extension() : string {

		return $this->extension;

	}

	protected function process() : bool {

		$this->new_name = sprintf( '%s.%s', $this->randomChars( self::FILENAME_LENGTH ), $this->extension );

		$process = Filter::apply( 'file_upload', true, $this );
		$process = Filter::apply( sprintf( 'file_upload_%s', $this->extension ), $process, $this );

		return $process;

	}

	function save( int $id, string $dir ) : bool {

		$id = abs( intval( $id ) );

		$this->create_file_path( $id, $dir );

		if( move_uploaded_file( $this->tmp_name, $this->get_file_path( $id, $dir, $this->get_generated_name() ) ) ){
			return true;
		}

		$this->add_error( 'Saving the file ' . esc_html( $this->name ) . ' failed.' );

		return false;

	}

	/**
	 * move_uploaded_file only works on uploaded file
	 * for generated local file we need to save via copy function
	 */
	function save_via_copy( $id, $dir = '' ){
		$id = abs( intval( $id ) );
		$this->create_file_path( $id, $dir );
		if( copy( $this->tmp_name, $this->get_file_path( $id, $dir, $this->get_generated_name() ) ) ){
			return true;
		}
		$this->add_error( 'Saving the file ' . esc_html( $this->name ) . ' failed.' );
		return false;
	}

	static function get_file_path( int $id, string $dir, string $filename = '' ) : string {

		$id = abs( intval( $id ) );

		return Config::get( 'app.files.upload_dir' ) . $dir . '/' . $id . '/' . $filename;

	}

	private function create_file_path( $id, $dir ) : bool {

		$fullpath = $this->get_file_path( $id, $dir );

		if( ! file_exists( $fullpath ) ){

			return mkdir( $fullpath, 0755, true );

		} else {

			return true;

		}

	}

}
