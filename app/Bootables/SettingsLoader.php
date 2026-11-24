<?php

namespace App\Bootables;

use App\Config;
use App\Events\Listener;
use App\Models\Settings\Settings;

class SettingsLoader {

	public function boot() : void {

		Listener::on( 'plugins_loaded', [ $this, 'hydrate' ] );

	}

	public function hydrate() : void {

		Config::set( 'settings', Settings::all() );

	}

}
