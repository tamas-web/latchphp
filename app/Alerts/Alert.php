<?php

namespace App\Alerts;

use App\Constants;
use App\Alerts\Interfaces\AlertInterface;

class Alert implements AlertInterface, Constants {

    /**
     * Add alert
     */
	static public function add( string $message, int $type = self::ALERT_INFO ) : void {

		$alerts = ( array ) session()->alerts;

		$alerts[] = [
			'type'    => $type,
			'message' => $message,
		];

		session()->alerts = $alerts;

	}

    /**
     * Add info alert
     */
	static public function info( string $message ) : void {

		self::add( $message, self::ALERT_INFO );

	}

    /**
     * Add success alert
     */
	static public function success( string $message ) : void {

		self::add( $message, self::ALERT_SUCCESS );

	}

    /**
     * Add warning alert
     */
	static public function warning( string $message ) : void {

		self::add( $message, self::ALERT_WARNING );

	}

    /**
     * Add error alert
     */
	static public function error( string $message ) : void {

		self::add( $message, self::ALERT_ERROR );

	}

    /**
     * Clear alerts
     */
	static public function clear(){

		unset( session()->alerts );

	}

    /**
     * Has alerts or alerts of a type
     */
	static public function has( int $type = 0 ){

		if( ! $type ){

			return !! session()->alerts;

		}

		$key = array_search( $type, array_column( ( array ) session()->alerts, 'type' ) );

		return $key !== false;

	}

    /**
     * Get all alerts
     */
	static public function getAll(){

		return ( array ) session()->alerts;

	}

    /**
     * Get alerts of a specific type
     */
	static public function getByType( int $type ){

		return array_values( array_filter( ( array ) session()->alerts, function( $value, $key ) use ( $type ){

			return $value['type'] === $type;

		}, ARRAY_FILTER_USE_BOTH ) );

	}

}
