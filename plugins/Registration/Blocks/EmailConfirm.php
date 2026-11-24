<?php

namespace Plugins\Registration\Blocks;

use App\Constants;
use App\Events\Listener;
use App\Http\Request;
use App\Views\View;

class EmailConfirm implements Constants {

	use \App\Traits\randomChars;

	public function boot(){

		Listener::on( 'container_fluid_content_start', [ $this, 'renderNotice' ] );

	}

	public function renderNotice(){

		$user = Request::getInstance()->user();

		if( $user->isUser() && ! $user->hasOption( self::ACCOUNT_EMAIL_CONFIRMED ) ){

			$view = new View( dirname( __DIR__ ) . '/templates' );

			$view->body()->add( 'blocks.email-confirm' );

			$view->render();

		}

	}

}
