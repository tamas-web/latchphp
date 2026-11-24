<?php

namespace App\Models;

use App\Config;
use App\Database\Connector as DB;
use App\Html\Paginator;
use App\Http\{ URL, Request };
use App\Libraries\Model;
use DateTimeImmutable;
use StdClass;

class Notifications extends Model {

	use \App\Traits\bitOptions;
	use \App\Traits\humanReadableTimePassedSince;

	protected function add( int $id_user, string $message, string $details = '', bool $sticky = false ) : bool {

		// set sticky or not
		$options = 0;

		if( $sticky ){

			$options |= self::NOTIFICATION_STICKY;

		}

		$ins = DB::getInstance()->prepare( '
			INSERT INTO notifications
				( `id_user`, `message`, `details`, `options` )
			VALUES
				( ?, ?, ?, ? )
		' );
		$ins->bind_param( 'issi', $id_user, $message, $details, $options );
		$ins->execute();

		$errno = $ins->errno;

		$ins->close();

		return ! $errno;

	}

	protected function getUserNotifications() : StdClass {

		$data = [
			'new'   => 0,
			'items' => [],
		];

		$user = Request::getInstance()->user();

		$seen   = self::NOTIFICATION_SEEN;
		$sticky = self::NOTIFICATION_STICKY;

		$sel = DB::getInstance()->prepare( '
			SELECT
				COUNT( id )
			FROM notifications
			WHERE
				id_user = ? AND
				( options & ? ) != ?
			LIMIT 1
		' );

		$sel->bind_param( 'iii', $user->id, $seen, $seen );
		$sel->execute();
		$sel->bind_result( $count_new );
		$sel->fetch();
		$sel->close();

		$data['new'] = (int) $count_new;

		$sel = DB::getInstance()->prepare( '
			SELECT
				id, options, message, created_at,
				( ( options & ? ) = ? ) AS sticky, ( ( options & ? ) != ? ) AS unseen
			FROM notifications
			WHERE
				id_user = ?
			ORDER BY sticky DESC, created_at DESC
			LIMIT 3
		' );

		$sel->bind_param( 'iiiii', $sticky, $sticky, $seen, $seen, $user->id );
		$sel->execute();

		$sel->bind_result( $id, $options, $message, $created_at, $isSticky, $isUnseen );

		while( $sel->fetch() ){

			$data['items'][] = (object) [
				'id'          => (int) $id,
				'url'         => URL::BUILD( sprintf( 'notifications/%u', $id ) ),
				'options'     => (int) $options,
				'sticky'      => (bool) $isSticky,
				'unseen'      => (bool) $isUnseen,
				'message'     => esc_html( $message ),
				'time_passed' => $this->passedTimeSince( strtotime( $created_at ) ),
				'created_at'  => esc_html( ( new DateTimeImmutable )
					->modify( $created_at )
					->format( 'd.m.Y - H:i' )
				),
			];

		}

		return (object) $data;

	}

	protected function getPaginatedNotifications( int $page ) : StdClass {

		// data
		$data = [
			'paginator' => null,
			'items'     => [],
			'limit'     => (int) Config::get( 'settings.app.pagination.limit' ) ?: 25,
			'query'     => [],
		];

		// extra parameters
		$params = [];

		if( 'unseen' === Request::get( 'filter' ) ){

			$params[] = sprintf( '( options & %1$u ) != %1$u', self::NOTIFICATION_SEEN );

		}

		// user
		$user = Request::getInstance()->user();

		// sticky and seen
		$seen   = self::NOTIFICATION_SEEN;
		$sticky = self::NOTIFICATION_STICKY;

		// join params
		$params = join( ' AND ', $params );
		if( $params ){
			$params = sprintf( ' AND ' . $params );
		}

		// count notifications
		$sel = DB::getInstance()->prepare( $sql[] = sprintf( '
			SELECT
				COUNT( id )
			FROM notifications
			WHERE
				id_user = ?
				%s
			LIMIT 1
		', $params ) );

		// do query
		$sel->bind_param( 'i', $user->id );
		$sel->execute();
		$sel->bind_result( $count );
		$sel->fetch();
		$sel->close();

		// create paginator
		$data['paginator'] = new Paginator( total: $count, limit: $data['limit'] );
		$data['paginator']->setCurrentPage( $page );
		$offset = $data['paginator']->getOffset();
		$limit  = $data['paginator']->getLimit();

		// do main query
		$sel = DB::getInstance()->prepare( $sql[] = sprintf( '
			SELECT
				id, message, options, created_at,
				( ( options & ? ) = ? ) AS sticky, ( ( options & ? ) != ? ) AS unseen
			FROM notifications
			WHERE
				id_user = ?
				%s
			ORDER BY sticky DESC, created_at DESC, id DESC
			LIMIT ?, ?
		', $params ) );
		$sel->bind_param(
			'iiiiiii',
			$sticky,
			$sticky,
			$seen,
			$seen,
			$user->id,
			$offset,
			$limit
		);
		$sel->execute();
		$sel->bind_result( $id, $message, $options, $created_at, $isSticky, $isUnseen );
		$sel->store_result();

		// loop through results
		while( $sel->fetch() ){

			$data['items'][] = (object) [
				'id'          => (int) $id,
				'url'         => URL::BUILD( sprintf( 'notifications/%u', $id ) ),
				'options'     => (int) $options,
				'sticky'      => (bool) $isSticky,
				'unseen'      => (bool) $isUnseen,
				'message'     => esc_html( $message ),
				'time_passed' => $this->passedTimeSince( strtotime( $created_at ) ),
				'created_at'  => esc_html( ( new DateTimeImmutable )
					->modify( $created_at )
					->format( 'd.m.Y H:i' )
				),
			];

		}

		$sel->close();

		return (object) $data;

	}

	protected function getNotification( int $id ) : stdClass {

		$data = [];

		$user = Request::getInstance()->user();

		$sel = DB::getInstance()->prepare( '
			SELECT
				id, options, message, details, created_at
			FROM notifications
			WHERE
				id = ? AND
				id_user = ?
			LIMIT 1
		' );

		$sel->bind_param( 'ii', $id, $user->id );
		$sel->execute();

		$sel->store_result();

		if( $sel->num_rows ){

			$sel->bind_result( $nid, $options, $message, $details, $created_at );

			$sel->fetch();

			$data = [
				'id'          => (int) $nid,
				'options'     => (int) $options,
				'message'     => esc_html( $message ),
				'details'     => $details,
				'time_passed' => $this->passedTimeSince( strtotime( $created_at ) ),
				'created_at'  => esc_html( ( new DateTimeImmutable )
					->modify( $created_at )
					->format( 'd.m.Y H:i' )
				),
			];

		}

		$sel->close();

		if( $id === (int) $nid ){

			// prep vars
			$this->options = $options;

			// update seen
			if( ! $this->hasOption( self::NOTIFICATION_SEEN ) ){

				$this->setOption( self::NOTIFICATION_SEEN );

				$options = $this->options;

				// update
				$upd = DB::getInstance()->prepare( '
					UPDATE notifications
					SET
						options = ?
					WHERE
						id = ? AND
						id_user = ?
					LIMIT 1
				' );
				$upd->bind_param( 'iii', $this->options, $id, $user->id );
				$upd->execute();
				$upd->close();

			}

		}

		return (object) $data;

	}

	protected function deleteNotification( int $id ) : bool {

		$user = Request::getInstance()->user();

		$del = DB::getInstance()->prepare( '
			DELETE FROM notifications
			WHERE
				id = ? AND
				id_user = ?
			LIMIT 1
		' );
		$del->bind_param( 'ii', $id, $user->id );
		$del->execute();

		$errno = $del->errno;

		$del->close();

		return ! $errno;

	}

	protected function deleteAllNotifications() : bool {

		$user = Request::getInstance()->user();

		$del = DB::getInstance()->prepare( '
			DELETE FROM notifications
			WHERE
				id_user = ?
		' );
		$del->bind_param( 'i', $user->id );
		$del->execute();

		$errno = $del->errno;

		$del->close();

		return ! $errno;

	}

}
