<?php

namespace App\Views;

use App\Config;
use App\Constants;
use App\Alerts\Alert;
use App\Filters\Filter;
use App\Events\Listener;
use App\Http\URL;
use App\Libraries\Nonce;
use stdClass;

class JavascriptApplication implements Constants {

	function boot(){

		Listener::on( 'post_print_styles', [ $this, 'buildObject' ] );

	}

	function buildObject(){

		$object = new stdClass;

		/**
		 * Custom data object
		 */
		$object->data = Filter::apply( 'js_custom_data', new stdClass );

		/**
		 * Internationalization for javascript
		 */
		$i18n = new stdClass;

		$i18n->loading = 'Loading...';
		$i18n->failed_to_fetch_response = 'Communication error. Please try again later.';
		$i18n->do_you_want_to_perform_action = 'Do you want to perform this action?';
		$i18n->please_wait = 'Please wait.';

		$i18n->upload = new stdClass;
		$i18n->upload->only_one_file_allowed = 'Please upload only one main photo.';
		$i18n->upload->max_ten_files_allowed = 'You can upload a maximum of 10 images.';
		$i18n->upload->unsupported_file_type = 'This file type is not supported.';

		$object->i18n = Filter::apply( 'i18n', $i18n );

		$object->nonce = new stdClass;

		$object->nonce->field  = Nonce::FIELD;
		$object->nonce->header = Nonce::HEADER;
		$object->nonce->value  = Nonce::create();

		$object->upload = new StdClass;
		$object->upload->fileTypes = array_map( fn( $image ) => 'image/' . $image, Config::Get( 'app.files.allowed.image' ) );
		$object->upload->image = [
			'min' => [
				'width'  => Config::Get( 'app.files.images.minSize.width' ),
				'height' => Config::Get( 'app.files.images.minSize.height' ),
			],
		];

		/**
		 * Alerts
		 */
		$alerts = new stdClass;

		$alerts->all = Alert::getAll();

		$alerts->TYPES = new stdClass;

		$alerts->TYPES->INFO    = self::ALERT_INFO;
		$alerts->TYPES->SUCCESS = self::ALERT_SUCCESS;
		$alerts->TYPES->WARNING = self::ALERT_WARNING;
		$alerts->TYPES->ERROR   = self::ALERT_ERROR;

		$object->alerts = Filter::apply( 'alerts', $alerts );

		/**
		 * we make a request to server from client side to clear all alerts
		 * the api for forms is using fetch, which internally follows the redirect
		 * after the redirect we manually redirect the page and on page load
		 * initiate a request to clear alerts
		 * Initially we cleared the alerts from here.
		 */
		//~ Alert::clear();

		/**
		 * JSON Encode options
		 */
		$jsonOptions = JSON_INVALID_UTF8_IGNORE;

		if( 'development' === \APP_ENVIRONMENT ){

			$jsonOptions |= \JSON_PRETTY_PRINT;

		}

		/**
		 * Output JSON
		 */
		echo '<script>
			const App = ', \json_encode( $object, $jsonOptions ), ';
			App.alerts.push = ( message, type ) => {
				App.alerts.all.push( {
					type : type || App.alerts.TYPES.INFO,
					message : message
				} );
				document.dispatchEvent( new Event( "app.alert" ) )
			},
			App.url = {
				base : "', URL::BASE(), '",
				media : "', Config::get( 'app.site.images.url' ), '",
				library : "', URL::BUILD( 'assets/' ), '",
				build : function( url ){
					return `${App.url.base}${url}`;
				},
				api : function( url ){
					return App.url.build( `api/${url}` );
				},
				user: null /* TODO */
			};
		</script>';

	}

}
