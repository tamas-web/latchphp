<?php

namespace App\Models;

use App\Database\Connector as DB;
use App\Http\URL;
use App\Libraries\Model;
use StdClass;

class Attachment extends Model {

	protected function get( int $id, int $id_object, string $relationship, string $filename ) : ?StdClass {

		$data = [
			'id'           => 0,
			'id_object'    => 0,
			'relationship' => null,
			'filetype'     => null,
			'filename'     => null,
			'name'         => null,
			'created_at'   => null,
			'url'          => null,
		];

		$sel = DB::getInstance()->prepare( '
			SELECT
				id, id_object, relationship,
				filetype, filename, name, created_at
			FROM attachments
			WHERE
				id = ? AND
				id_object = ? AND
				relationship = ? AND
				filename = ?
			LIMIT 1
		' );

		$sel->bind_param( 'iiss', $id, $id_object, $relationship, $filename );
		$sel->execute();

		unset( $id, $id_object, $relationship, $filename );

		$sel->bind_result(
			$id, $id_object, $relationship,
			$filetype, $filename, $name, $created_at
		);

		$sel->fetch();

		$data = [
			'id'           => (int) $id,
			'id_object'    => esc_html( $id_object ),
			'relationship' => esc_html( $relationship ),
			'filetype'     => esc_html( $filetype ),
			'filename'     => esc_html( $filename ),
			'name'         => esc_html( $name ),
			'created_at'   => esc_html( $created_at ),
		];
		$data['url'] = URL::BUILD( sprintf( 'attachments/%u/%u/%s/%s', $data['id'], $data['id_object'], $data['relationship'], $data['filename'] ) );

		$sel->close();

		return $id ? (object) $data : null;

	}

}
