<?php

namespace App\Libraries;

use App\Constants;
use App\Http\Request;
use App\Filters\Filter;

class Controller implements Constants {

	// some default stuffs

	protected $pageTitle = '';

	function __construct(){

		Filter::add( 'page_title', [ $this, '_pageTitle' ] );
		Filter::add( 'i18n', [ $this, 'i18n' ] );

	}

	public function _pageTitle(){

		return $this->pageTitle;

	}

	public function i18n( \StdClass $i18n ) : \StdClass {

		return $i18n;

	}

}
