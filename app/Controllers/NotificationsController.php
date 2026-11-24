<?php

namespace App\Controllers;

use App\Alerts\Alert;
use App\Libraries\Controller;
use App\Http\Request;
use App\Datatype\Response;
use App\Html\Link;
use App\Filters\Filter;
use App\Models\Notifications;

class NotificationsController extends Controller {

	protected $pageTitle = 'Notifications';

	function notifications( Request $request ) : void {

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/notifications', 'Notifications' ),
		] );

		$request->view()
			->body()
				->data( [
					'notifications' => Notifications::getPaginatedNotifications( $request->get( 'page', 'intval' ) ),
				] )
				->add( 'notifications.index' );

		$request->view()->render();

	}

	function notification( Request $request ) : void {

		$id = (int) $request->getParameter( 0 );

		$data = Notifications::getNotification( $id );

		if( ! $data->id ){

			$request->redirect( '/notifications' );

		}

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/notifications', 'Notifications' ),
			new Link( sprintf( '/notifications/%u', $data->id ), $data->message ),
		] );

		$request->view()
			->body()
				->data( [
					'notification' => $data,
				] )
				->add( 'notifications.view' );

		$request->view()->render();

	}

	function deleteNotification( Request $request ) : void {

		$response = new Response;

		$id = (int) $request->getParameter( 0 );

		$data = Notifications::getNotification( $id );

		if( ! $data->id ){

			Alert::error( 'We could not identify the notification. Reload the page and try again.' );

		} elseif( ! Notifications::deleteNotification( $data->id ) ){

			Alert::error( 'Deleting the notification failed. Please try again later.' );

		} else {

			Alert::success( 'The notification was deleted successfully.' );

			$request->redirect( '/notifications' );

		}

		echo $response;

	}

	function deleteAllNotifications( Request $request ) : void {

		$response = new Response;

		if( ! Notifications::deleteAllNotifications() ){

			Alert::error( 'Deleting the notifications failed. Please try again later.' );

		} else {

			Alert::success( 'The notifications were deleted successfully.' );

			$request->redirect( '/notifications' );

		}

		echo $response;

	}

}
