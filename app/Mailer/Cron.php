<?php

namespace App\Mailer;

use App\Database\Connector as DB;
use App\Libraries\Cli;
use App\Mailer\Mail;

class Cron extends Cli {

	public function run(){

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`, `email`, `subject`, `body`
			FROM `mailqueue`
			WHERE
				`sent` = 0
			LIMIT 1
		' );
		$sel->execute();
		$sel->store_result();

		if( $sel->num_rows ){

			$sel->bind_result( $id, $email, $subject, $body );

			$sel->fetch();

			self::out( sprintf( 'Preparing email to "%s" with subject "%s"', $email, $subject ) );

			$mail = new Mail( $email, $subject, $body );

			if( $mail->send() ){

				self::out( sprintf( 'Successfully sent email to "%s" with subject "%s"', $email, $subject ) );

				$upd = DB::getInstance()->prepare( '
					UPDATE `mailqueue` SET
						`sent` = 1, `updated_at` = NOW()
					WHERE
						`id` = ?
					LIMIT 1
				' );
				$upd->bind_param( 'i', $id );
				$upd->execute();
				$errno = $upd->errno;
				$upd->close();

				if( $errno ){

					self::out( sprintf( 'Failed to update email queue entry #%u.', $id ) );

				}

			} else {

				self::out( sprintf( 'Failed to send email to "%s" with subject "".', $email, $subject ) );

			}

		} else {

			self::out( 'The email queue is empty.' );

		}

	}

}
