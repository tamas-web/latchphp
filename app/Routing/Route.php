<?php

namespace App\Routing;

use StdClass, Closure;
use App\Http\Request;

/**
 * @method static self any( string $route, array|callable $callback )
 * @method static self get( string $route, array|callable $callback )
 * @method static self post( string $route, array|callable $callback )
 * @method static self put( string $route, array|callable $callback )
 * @method static self patch( string $route, array|callable $callback )
 * @method static self delete( string $route, array|callable $callback )
 * @method static self options( string $route, array|callable $callback )
 */
class Route {

	/**
	 * Route of instantiated class
	 */
	protected $route;

	/**
	 * Regex formated route
	 */
	protected $regex;

	/**
	 * Middleware for current route
	 */
	protected array $middlewares = [];

	/**
	 * Callback for route
	 */
	protected $callback;

	const METHODS = [ 'get', 'post', 'put', 'patch', 'delete', 'options' ];

    /**
     * Routes collection
     */
	protected static array $routes = [];

    /**
     * Origianl routes collection
     */
	protected static array $originalRoutes = [];

	/**
	 * Only class can instantiate itself
	 */
	protected function __construct(){}

	/**
	 * Handle the verb calls and everything else
	 */
	public static function __callStatic( $method, $arguments ){

		$method = strtolower( $method );

		if( in_array( $method, [ 'any', ...self::METHODS ], true ) ){

			return ( new self )->add( type: $method, route: $arguments[0], callback: $arguments[1] );

		}

		return ( new self )->$method( ...$arguments );

	}

    /**
     * Find registered route and return it
     */
	protected function find( string $route, Request &$request ){

		$method = self::requestMethod( $request );

		foreach( [ 'any', $method ] as $type ){

			if( array_key_exists( $route, self::$routes[ $type ] ?? [] ) ){

				$request->setUrl( $route );

				return self::$routes[ $type ][ $route ];

			}

		}

		foreach( [ 'any', $method ] as $type ){

			foreach( self::$routes['regex'][ $type ] ?? [] as $the_route => $callback ){

				if( preg_match( $the_route, $route, $matches ) ){

					$request->setUrl( array_shift( $matches ) );

					$request->setParameters( $matches );

					return $callback;

				}

			}

		}

		return new self;

	}

	protected static function requestMethod( Request $request ) : string {

		$method = strtolower( (string) $request->server->REQUEST_METHOD );

		return 'head' === $method
			? 'get'
			: $method;

	}

    /**
     * Format registered route
     */
	protected function format( string $route ) : string {

		$route = str_replace(
			[ '/', '{id}', '{string}' ],
			[ '\/', '([0-9]+)', '([a-zA-Z0-9-_.]+)' ],
			$route
		);

		return sprintf( '~^%s$~', $route ); // ~ (tilda) is the delimiter

	}

    /**
     * Add route to specific collection
     */
	protected function add( string $type, string $route, $callback ){

		$this->route    = $route;
		$this->regex    = $this->format( $route );
		$this->callback = $callback;

		if( false !== mb_stripos( $route, '{id}' ) || false !== mb_stripos( $route, '{string}' ) ){

			self::$routes['regex'][ $type ][ $this->regex ] = $this;

		} else {

			self::$routes[ $type ][ $this->route ] = $this;

		}

		self::$originalRoutes[ $type ][ $this->route ] = $this;

		return $this;

	}

	public function middleware( array $middlewares = [] ){

		$this->middlewares = array_merge( $this->middlewares, $middlewares );

		return $this;

	}

	public function getCallbackController() : ?string {

		return $this->callback[0] ?? null;

	}

	public function getCallbackMethod() : ?string {

		return $this->callback[1] ?? null;

	}

	public function isCallable(){

		// check callback method on class
		if( is_array( $this->callback ) && count( $this->callback ) === 2 ){

			if( method_exists( $this->callback[0], $this->callback[1] ) ){

				$reflection = new \ReflectionMethod( $this->callback[0], $this->callback[1] );

				// php supports calling static method on instances, but we're not going to support it
				// so if we detect the current method is static, we're going to abord
				if( $reflection->isPublic() && ! $reflection->isStatic() ){

					return true;

				}

				return false;

			} else {

				return false;

			}

		}

		// for all other stuff we check via is_callable like closure and static methods
		return is_callable( $this->callback );

	}

	public function run( Request $request ){

		if( $this->runMiddleware( $request ) ){

			if( is_array( $this->callback ) && count( $this->callback ) === 2 ){

				$controller = $this->getCallbackController();
				$method     = $this->getCallbackMethod();

				( new $controller )->$method( $request, $this );

				return true;

			} elseif( is_callable( $this->callback ) ){

				call_user_func( $this->callback, $request, $this );

				return true;

			}

		}

		return false;

	}

	protected function runMiddleware( Request $request ){

		$passed = true;

		foreach( $this->middlewares as $middleware ){

			$args = [];

			// support both a class string and [ Class, ...constructorArgs ]
			if( is_array( $middleware ) ){

				$class = array_shift( $middleware );
				$args  = $middleware;

			} else {

				$class = $middleware;

			}

			if( is_callable( [ $class, 'handle' ], true ) ){

				( new $class( ...$args ) )->handle( $request, $passed );

				if( ! $passed ){

					return false;

				}

			}

		}

		return true;

	}

	public function getRegex(){

		return $this->regex;

	}

	public function getRoute(){

		return $this->route;

	}

	public function getAllOriginalRoutes() : array {

		return self::$originalRoutes + array_fill_keys( [ 'any', ...self::METHODS ], [] );

	}

}
