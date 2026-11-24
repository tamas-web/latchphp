<?php

namespace App\Controllers;

use App\Config;
use App\Controllers\HttpErrorController;
use App\Http\Request;
use App\Filters\Filter;
use App\Libraries\Controller;
use App\Models\Attachment;

class AttachmentsController extends Controller {

	public function get( Request $request ){

		$a_id           = (int) $request->getParameter( 0 );
		$a_id_object    = (int) $request->getParameter( 1 );
		$a_relationship = (string) $request->getParameter( 2 );
		$a_filename     = (string) $request->getParameter( 3 );

		$attachment = Attachment::get( id: $a_id, id_object: $a_id_object, relationship: $a_relationship, filename: $a_filename );

		if( $attachment && ! Filter::apply( 'attachment.authorize', false, $attachment, $request->user() ) ){

			$attachment = null;

		}

		if( $attachment ){

			$filepath = sprintf( '%s%s/%s/%s', Config::get( 'app.files.upload_dir' ), $attachment->relationship, $attachment->id_object, $attachment->filename );

			if( file_exists( $filepath ) ){

				$contentType = $this->_getContentType( filetype: $attachment->filetype );

				if( $contentType ){

					header( sprintf( 'Content-Type: %s', $contentType ) );

				} else {

					header( 'Content-Type: application/octet-stream' );

				}

				header( 'X-Content-Type-Options: nosniff' );

				// force attachment download
				if( 'attachment' === $request->get( 'mode' ) ){

					header( 'Content-Description: File Transfer' );
					header( 'Content-Type: application/octet-stream' );
					header( 'Content-Transfer-Encoding: binary' );
					header( 'Connection: Keep-Alive' );
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
					header( 'Pragma: public' );
					header( 'Content-Length: ' . filesize( $filepath ) );

					header( sprintf( 'Content-Disposition: attachment; filename="%s"', $attachment->name ) );

				// let browser decide how to deal with file
				} else {

					$etag = sprintf( '"%s"', md5( $attachment->filename . $attachment->created_at ) );

					header( sprintf(
						'Cache-Control: %s',
						Filter::apply( 'attachment.cache_control', 'private, no-cache', $attachment )
					) );

					header( 'Etag: ' . $etag );

					header( sprintf( 'Last-Modified: %s GMT', gmdate( 'D, d M Y H:i:s', strtotime( $attachment->created_at ) ) ) );

					// Check whether browser had sent a HTTP_IF_NONE_MATCH request header
					if( isset( $_SERVER['HTTP_IF_NONE_MATCH'] ) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag ){

						header( 'HTTP/1.1 304 Not Modified', true, 304 );
						exit();

					}

				}

				readfile( $filepath );

				exit; // just to make sure nothing gets executed after reading and outputing the file

			} else {

				// throw 404: file doesn't exists
				new HttpErrorController( $request, 404 );

			}

		} else {

			// throw 404: entry doesn't exists
			new HttpErrorController( $request, 404 );

		}

	}

	protected function _getContentType( string $filetype ) : string {

		$contentType = Filter::apply( 'mime_types', [

			// Text Files
			'doc'  => 'application/msword',
			'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'odt'  => 'application/vnd.oasis.opendocument.text',
			'rtf'  => 'application/rtf',
			'txt'  => 'text/plain',

			// Data Files
			'csv'  => 'text/csv',
			'pps'  => 'application/vnd.ms-powerpoint',
			'ppt'  => 'application/vnd.ms-powerpoint',
			'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'xml'  => 'application/xml',
			'odp'  => 'application/vnd.oasis.opendocument.presentation',

			// Spreadsheet Files
			'xls'  => 'application/vnd.ms-excel',
			'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',

			// Image Files
			'jpg'  => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'png'  => 'image/png',
			'gif'  => 'image/gif',
			'bmp'  => 'image/bmp',

			// Page Layout Files
			'pdf'  => 'application/pdf',

		] );

		return (string) ( $contentType[ $filetype ] ?? '' );

	}

}
