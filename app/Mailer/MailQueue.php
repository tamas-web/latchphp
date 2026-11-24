<?php

namespace App\Mailer;

use App\Database\Connector as DB;

class MailQueue {

	private int $errno;

	public function __construct( string $email, string $subject, string $body ){

		$ins = DB::getInstance()->prepare( '
			INSERT INTO `mailqueue`
				( `email`, `subject`, `body` )
			VALUES
				( ?, ?, ? )
		' );
		$ins->bind_param( 'sss', $email, $subject, $body );
		$ins->execute();

		$this->errno = $ins->errno;

		$ins->close();

	}

	public function hasError() : bool {

		return ! $this->errno;

	}

}
