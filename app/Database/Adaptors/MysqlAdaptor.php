<?php

namespace App\Database\Adaptors;

use App\Database\Interfaces\AdaptorInterface;
use App\ErrorHandlers\HandlerException;
use mysqli;
use mysqli_stmt;
use mysqli_result;

class MysqlAdaptor extends mysqli implements AdaptorInterface {

	protected $__queries = [];

	function __construct( $hostname = null, $username = null, $password = null, $database = null ){

		parent::__construct();

		try {

			parent::real_connect( hostname: $hostname, username: $username, password: $password, database: $database );

			$this->set_charset( 'utf8mb4' );

		} catch( HandlerException $error ){

			$error->getFormatedMessage();

		}

	}

	function query( string $query, int $result_mode = MYSQLI_STORE_RESULT ) : mysqli_result|bool {
		$this->__queries[] = $query;
		return parent::query( $query, $result_mode );
	}

	function prepare( $sql ) : mysqli_stmt|false {
		$this->__queries[] = $sql;
		return parent::prepare( $sql );
	}

	function getQueriesList() : array {
		return $this->__queries;
	}

}
