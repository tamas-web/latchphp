<?php

namespace App\Alerts\Interfaces;

use App\Constants;

/**
 * Alert interface
 */
interface AlertInterface {

    /**
     * Add alert
     */
	static public function add( string $message );

    /**
     * Clear alerts
     */
	static public function clear();

    /**
     * Has specific alert type
     */
	static public function has();

    /**
     * Get all alerts
     */
	static public function getAll();

    /**
     * Get alerts of a specific type
     */
	static public function getByType( int $type );

}
