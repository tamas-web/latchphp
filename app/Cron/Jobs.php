<?php

namespace App\Cron;

use App\Constants;
use App\Cron\Scheduler;
use App\Libraries\Cli;
use ReflectionMethod;
use ReflectionException;

class Jobs extends Cli implements Constants {

	static public function init(){

		self::out( 'Quering for cron jobs.' );

		// init cron jobs
		$jobs = Scheduler::query();

		if( count( $jobs ) ){

			self::out( 'Jobs found:' );

			self::out( $jobs );

		} else {

			self::out( 'No jobs found.' );

		}

		foreach( $jobs as $job ){

			$class  = $job->callback[0] ?? '';
			$method = $job->callback[1] ?? '';

			self::out( 'Running job: ' . $job->name );

			try {

				$ref = new ReflectionMethod( $class, $method );

				if( $ref->isPublic() ){

					if( $ref->isStatic() ){

						call_user_func( $job->callback );

					} else {

						( new $class )->$method();

					}

					//~ self::out( $job );

					self::out( 'Finished job: ' . $job->name );

					Scheduler::reschedule( $job->id );

				}

			} catch( ReflectionException $e ){

				if( 'development' === APP_ENVIRONMENT ){

					echo 'Cron job "', esc_html( $job->name ), '" failed. Error: ', esc_html( $e->getMessage() ), PHP_EOL;

				}

			}

		}

	}

}
