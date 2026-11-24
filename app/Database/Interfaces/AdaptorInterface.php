<?php

namespace App\Database\Interfaces;

interface AdaptorInterface {

    /**
     * Class constructor
     */
	function __construct();

    /**
     * Connect to database
     *
     * @return sql connection
     */
	function connect();

}
