<?php

namespace App\Libraries;

use App\Constants;
use App\Database\Connector as DB;
use App\Libraries\Upload;

class Attachment extends Upload {

	protected int $id = 0;
	protected int $id_object = 0;
	protected string $relationship = '';

	public function setId( int $id ){

		$this->id = abs( $id );

		return $this;

	}

	public function setIdObject( int $id_object ){

		$this->id_object = abs( $id_object );

		return $this;

	}

	public function setRelationship( string $relationship ){

		$this->relationship = $relationship;

		return $this;

	}

	public function &getId() : int {

		return $this->id;

	}

	public function &getIdObject() : int {

		return $this->id_object;

	}

	public function &getRelationship() : string {

		return $this->relationship;

	}

	public function upload( array $file, int $id_object, string $relationship, int $id_parent = 0 ){

		$this->setIdObject( $id_object );
		$this->setRelationship( $relationship );
		$this->setId( 0 );

		if( $this->file( $file ) && ! $this->has_errors() ){

			// save to database
			$ins = DB::getInstance()->prepare( '
				INSERT INTO attachments
					( `id_parent`, `id_object`, `relationship`, `filetype`, `filename`, `name` )
				VALUES
					( ?, ?, ?, ?, ?, ? )
			' );
			$ins->bind_param(
				'iissss',
				$id_parent, $this->getIdObject(), $this->getRelationship(),
				$this->get_file_extension(), $this->get_generated_name(),
				$this->get_file_name()
			);
			$ins->execute();

			if( $ins->errno ){

				$this->add_error( sprintf( 'Error saving file. Error number: %s', $ins->errno ) );
				$this->delete_upload();
				$this->delete_if_empty_dir();

			// save upload file
			} elseif( $this->save( $this->getIdObject(), $this->getRelationship() ) ){

				$this->setId( $ins->insert_id );

			}

			$ins->close();

		}

		return $this->getId();

	}

	public function sideload( array $file, int $id_object, string $relationship, int $id_parent = 0 ){

		$this->setIdObject( $id_object );
		$this->setRelationship( $relationship );
		$this->setId( 0 );

		if( $this->file( $file ) && ! $this->has_errors() ){

			// save to database
			$ins = DB::getInstance()->prepare( '
				INSERT INTO attachments
					( `id_parent`, `id_object`, `relationship`, `filetype`, `filename`, `name` )
				VALUES
					( ?, ?, ?, ?, ?, ? )
			' );
			$ins->bind_param(
				'iissss',
				$id_parent, $this->getIdObject(), $this->getRelationship(),
				$this->get_file_extension(), $this->get_generated_name(),
				$this->get_file_name()
			);
			$ins->execute();

			if( $ins->errno ){

				$this->add_error( sprintf( 'Error saving file. Error number: %s', $ins->errno ) );
				$this->delete_upload();
				$this->delete_if_empty_dir();

			// save upload file
			} elseif( $this->save_via_copy( $this->getIdObject(), $this->getRelationship() ) ){

				$this->setId( $ins->insert_id );

			}

			$ins->close();

		}

		return $this->getId();

	}

	public function delete_entry() : bool {

		$del = DB::getInstance()->prepare( '
			DELETE FROM attachments
			WHERE
				id = ? AND
				id_object = ? AND
				relationship = ?
			LIMIT 1
		' );

		$del->bind_param( 'iis', $this->getId(), $this->getIdObject(), $this->getRelationship() );

		$del->execute();

		$errno = $del->errno;

		$del->close();

		return ! $errno; // errno if zero (no errors) return success, else return false

	}

	public function delete_if_empty_dir() : bool {

		$dir =  $this->get_file_path( $this->getIdObject(), $this->getRelationship() );

		if( is_dir( $dir ) ){

			$scan = scandir( $dir );

			$scan = array_filter( $scan, fn( $file ) => '.' !== $file && '..' !== $file );

			if( ! $scan ){

				return @rmdir( $dir );

			}

		}

		return false;

	}

	public function delete_upload( ?string $filename = null ) : bool {

		$file = $this->get_file_path( $this->getIdObject(), $this->getRelationship(), ( $filename ? $filename : $this->get_generated_name() ) );

		if( file_exists( $file ) ){

			return @unlink( $file );

		}

		return false;

	}

}
