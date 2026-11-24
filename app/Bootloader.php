<?php

namespace App;

use App\Database\Connector as DB;
use App\Datatype\Collection;
use App\Events\Emitter;
use App\Libraries\PluginManager;

class Bootloader {

    /**
     * Set all pre-bootables
     * Initialize configurations and other low level setups
     */
	protected array $pre_bootup = [
		'errorHandler' => \App\ErrorHandlers\Handler::class,
		'config'       => \App\Config::class,
		'appSetup'     => \App\Bootables\AppSetup::class,
		'settings'     => \App\Bootables\SettingsLoader::class,
		'session'      => \App\Bootables\SessionManager::class,
	];

    /**
     * Set all bootables
     */
	protected array $bootup = [
		\App\Filters\Common::class,
		'router' => \App\Routing\Router::class,
		\App\Views\Header::class,
		\App\Views\Footer::class,
		\App\Views\Sidebar::class,
		\App\Views\JavascriptApplication::class,
		// \App\Blocks\EmailConfirm::class moved into the Registration plugin
		// we need to implement this if we will be installing on multiple sites
		// there are some default tables defined in app root under migrate
		// but we need to create the migrate operation as well for that :)
		//~ '_installer'   => \App\Controllers\Installer::class,
	];

    /**
     * Collection of initiated bootables
     */
	protected Collection $booted;

    /**
     * The booted application instance
     */
	protected static $instance;

    /**
     * Register the instance once
     */
	public function __construct(){

		if( is_null( self::$instance ) ){

			self::$instance = $this;

		}

	}

    /**
     * Get the application instance ( create it if needed )
     */
	public static function getInstance() : self {

		if( is_null( self::$instance ) ){

			self::$instance = new self;

		}

		return self::$instance;

	}

    /**
     * Initiate application
     */
	public function init() : void {

		$this->initKernel();

		/**
		 * Dispatch
		 */
		$this->router->dispatch();

	}

    /**
     * Kernel
     */
	public function initKernel() : void {

		/**
		 * Instantiate the collection of booted objects
		 */
		$this->booted = new Collection;

		/**
		 * Boot pre bootables ( basic framework setup )
		 */
		$this->boot( $this->pre_bootup );

		/**
		 * Load all active plugins
		 */
		$this->booted['plugins'] = ( new PluginManager )->load();

		/**
		 * @param: App\Bootloader instance
		 */
		Emitter::broadcast( 'plugins_loaded', $this );

		/**
		 * Boot rest of the bootables
		 */
		$this->boot( $this->bootup );

		/**
		 * @param: App\Bootloader instance
		 */
		Emitter::broadcast( 'init', $this );

	}

    /**
     * Initiate bootables
     */
	protected function boot( array $bootables = [] ) : void {

		foreach( $bootables as $id => $class ){

			$object = ( new $class() );

			if( is_callable( [ $object, 'boot' ] ) ){
				$object->boot();
			}

			if( $id ){
				$this->booted[ $id ] = $object;
			} else {
				$this->booted[] = $object;
			}

		}

	}

    /**
     * Get bootable instance by key
     */
	public function __get( $key ) : ?object {

		return $this->booted[ $key ] ?? null;

	}

}
