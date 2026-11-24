<?php

namespace App\Libraries;

use App\Constants;
use App\Http\URL;
use ReflectionClass;

abstract class Plugin implements Constants {

	/**
	 * Metadata from the plugin's meta.json, attached by the PluginManager after
	 * instantiation (null on the boot path, which is scan-free).
	 */
	protected ?object $meta = null;

	/**
	 * Register the plugin's hooks / routes. Called for every ACTIVE plugin on boot.
	 */
	abstract public function run();

	/**
	 * Lifecycle hooks - overridable, no-op by default. Override one in a child class
	 * only when the plugin needs to do something specific on that event.
	 */
	public function install() : void {}      // one-time setup on install (create tables, defaults)
	public function uninstall() : void {}     // teardown on uninstall (drop tables, remove data)
	public function activated() : void {}     // fired when the plugin is activated (enabled)
	public function deactivated() : void {}   // fired when the plugin is deactivated (disabled)

	public function setMeta( object $meta ) : void {
		$this->meta = $meta;
	}

	public function meta() : ?object {
		return $this->meta;
	}

	public function name() : string {
		return $this->meta->name ?? '';
	}

	public function version() : string {
		return $this->meta->version ?? '';
	}

	public function description() : string {
		return $this->meta->description ?? '';
	}

	final protected function getPublicAssetsDir(){

		$reflect = new ReflectionClass( $this );

		return URL::BUILD( sprintf( 'assets/plugins/%s/', sanitize_string( $reflect->getShortName() ) ) );

	}

}
