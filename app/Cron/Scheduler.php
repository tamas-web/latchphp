<?php

namespace App\Cron;

use App\Constants;
use App\Database\Connector as DB;

class Scheduler implements Constants {

	static public function add( string $name, array $callback, int $interval, ?string &$error = null ) : bool {

		$cron_callback = json_encode( $callback );

		$ins = DB::getInstance()->prepare( '
			INSERT INTO `cron_jobs`
				( `cron_name`, `cron_callback`, `cron_interval`, `cron_next_run` )
			VALUES
				( ?, ?, ?, NOW() )
		' );
		$ins->bind_param( 'ssi', $name, $cron_callback, $interval );
		$ins->execute();

		$errno = $ins->errno;
		$error = $ins->error;

		$ins->close();

		if( 1062 === $errno ){

			$error = sprintf( 'Duplicate cron job "%s"', $name );

		}

		return ! $errno;

	}

	static public function delete( string $name, ?string &$error = null ) : bool {

		$del = DB::getInstance()->prepare( '
			DELETE FROM `cron_jobs`
			WHERE
				`cron_name` = ?
			LIMIT 1
		' );
		$del->bind_param( 's', $name );
		$del->execute();

		$errno = $del->errno;
		$error = $del->error;

		$del->close();

		if( $errno ){

			$error = sprintf( 'Unable to delete cron job. Error: %s', $error );

		}

		return ! $errno;

	}

	static public function query() : array {

		$tasks = [];

		$sel = DB::getInstance()->prepare( '
			SELECT
				`id`, `cron_name`, `cron_callback`,
				`cron_last_run`, `cron_next_run`,
				`cron_interval`, `created_at`, `updated_at`
			FROM `cron_jobs`
			WHERE
				`cron_next_run` < NOW()
		' );

		$sel->execute();

		$sel->bind_result(
			$id, $name, $callback,
			$last_run, $next_run,
			$interval, $created_at, $updated_at
		);

		while( $sel->fetch() ){

			$tasks[] = (object) [
				'id'         => $id,
				'name'       => $name,
				'callback'   => json_decode( $callback ),
				'last_run'   => $last_run,
				'next_run'   => $next_run,
				'interval'   => $interval,
				'created_at' => $created_at,
				'updated_at' => $updated_at,
			];

		}

		return $tasks;

	}

	static public function reschedule( int $id_task, ?string &$error = null ) : bool {

		$upd = DB::getInstance()->prepare( '
			UPDATE `cron_jobs` SET
				`cron_last_run` = `cron_next_run`,
				`cron_next_run` = TIMESTAMPADD( SECOND, `cron_interval`, NOW() ),
				`updated_at` = NOW()
			WHERE
				`id` = ?
			LIMIT 1
		' );
		$upd->bind_param( 'i', $id_task );
		$upd->execute();

		$errno = $upd->errno;
		$error = $upd->error;

		$upd->close();

		if( $errno ){

			$error = sprintf( 'Unable to reschedule cron job. Error: %s', $error );

		}

		return ! $errno;

	}

}
