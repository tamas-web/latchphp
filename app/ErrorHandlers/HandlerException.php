<?php

namespace App\ErrorHandlers;

/**
 * Custom error exception
 */
class HandlerException extends \ErrorException {

	/**
	 * Constructor
	 */
	public function __construct( string $message, int $code = 0, int $severity = E_ERROR, string $filename = __FILE__, int $lineno = __LINE__, ?\Throwable $previous = null ){

		parent::__construct( $message, $code, $severity, $filename, $lineno, $previous );

	}

	/**
	 * To string message
	 */
	public function __toString() : string {

		return __CLASS__ . ": [ { $this->code } ]: { $this->message }\n";

	}

	/**
	 * Get the formated message
	 */
	public function getFormatedMessage() : void {

		if( 'development' == \APP_ENVIRONMENT ){

			echo '<h3>An error occurred</h3>';

			printf( '<div>Code: %s</div>', $this->getCode() );
			echo PHP_EOL;
			printf( '<div>File: %s</div>', $this->getFile() );
			echo PHP_EOL;
			printf( '<div>Line: %s</div>', $this->getLine() );
			echo PHP_EOL;
			printf( '<div>Message: %s</div>', $this->getMessage() );
			echo PHP_EOL;

			if( $this->getTrace() ){

				echo '<h4>Trace</h4>';
				echo PHP_EOL;

				echo '<ul>';

				foreach( $this->getTrace() as $trace ){

					printf(
						'<li>File: %s :%s <br>Caller: [ %s, %s ]</li>',
						$trace['file'] ?? null,
						$trace['line'] ?? null,
						$trace['class'] ?? null,
						$trace['function'] ?? null,
					);
					echo PHP_EOL;

				}

				echo '</ul>';

			}

			exit;

		} else {

			// maybe dump error into a file?!?

		}

	}

}
