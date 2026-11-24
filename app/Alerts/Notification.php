<?php

namespace App\Alerts;

use App\Constants;
use App\Database\Connector as DB;
use App\Mailer\MailQueue;
use App\Libraries\User;
use App\Models\Notifications as NotificationsModel;

class Notification implements Constants {

	use \App\Traits\bitOptions;

	protected int $id_user = 0;

	/**
	 * @type TEXT: shorter message
	 */
	protected string $message = '';

	/**
	 * @type LONGTEXT: A longtext description for the message
	 */
	protected string $details = '';

	protected bool $notifyViaEmail = false;

	/**
	 * Create notification with message
	 */
	public function __construct( string $message = '' ){

		$this->message = $message;

	}

	/**
	 * Set user id
	 */
	public function setUserId( int $id_user ) : self {

		$this->id_user = $id_user;

		return $this;

	}

	/**
	 * Set message
	 */
	public function setMessage( string $message ) : self {

		$this->message = $message;

		return $this;

	}

	/**
	 * Set details
	 */
	public function setDetails( string $details ) : self {

		$this->details = $details;

		return $this;

	}

	/**
	 * Notify via email as well
	 */
	public function notifyViaEmail( bool $notifyViaEmail = true ) : self {

		$this->notifyViaEmail = $notifyViaEmail;

		return $this;

	}

	/**
	 * Notify users only
	 */
	public function usersOnly() : self {

		$this->clearOption( self::NOTIFICATION_FOR_MODERATOR );
		$this->clearOption( self::NOTIFICATION_FOR_ADMIN );

		$this->setOption( self::NOTIFICATION_FOR_USER );

		return $this;

	}

	/**
	 * Notify moderators only
	 */
	public function moderatorsOnly() : self {

		$this->clearOption( self::NOTIFICATION_FOR_USER );
		$this->clearOption( self::NOTIFICATION_FOR_ADMIN );

		$this->setOption( self::NOTIFICATION_FOR_MODERATOR );

		return $this;

	}

	/**
	 * Notify admins only
	 */
	public function adminsOnly() : self {

		$this->clearOption( self::NOTIFICATION_FOR_USER );
		$this->clearOption( self::NOTIFICATION_FOR_MODERATOR );

		$this->setOption( self::NOTIFICATION_FOR_ADMIN );

		return $this;

	}

	/**
	 * Notify moderators and admins
	 */
	public function moderatorsAndAdmins() : self {

		$this->clearOption( self::NOTIFICATION_FOR_USER );

		$this->setOption( self::NOTIFICATION_FOR_MODERATOR );
		$this->setOption( self::NOTIFICATION_FOR_ADMIN );

		return $this;

	}

	/**
	 * Notify all users
	 */
	public function everyone() : self {

		$this->setOption( self::NOTIFICATION_FOR_USER );
		$this->setOption( self::NOTIFICATION_FOR_MODERATOR );
		$this->setOption( self::NOTIFICATION_FOR_ADMIN );

		return $this;

	}

	/**
	 * Select enabled users that hold a given ACL permission (super admins always qualify).
	 * Keyed by user id so callers can union audiences without duplicates.
	 */
	protected function selectUsersByPermission( string $permission ) : array {

		$group   = explode( '.', $permission )[0];
		$enabled = self::ACCOUNT_ENABLED;
		$admin   = self::ACCOUNT_ADMIN;

		$users = [];

		$sel = DB::getInstance()->prepare( '
			SELECT
				u.`id`, u.`email`
			FROM `users` u
			WHERE
				( u.`options` & ? ) = ? AND
				(
					( u.`options` & ? ) = ? OR
					EXISTS (
						SELECT 1
						FROM `role_permissions` rp
						JOIN `permissions` p ON p.`id` = rp.`id_permission`
						WHERE
							rp.`id_role` = u.`id_role` AND
							p.`name` IN ( ?, ? )
					)
				)
		' );
		$sel->bind_param( 'iiiiss', $enabled, $enabled, $admin, $admin, $permission, $group );
		$sel->execute();
		$sel->bind_result( $id, $email );

		while( $sel->fetch() ){

			$users[ $id ] = [
				'id'    => $id,
				'email' => $email,
			];

		}

		$sel->close();

		return $users;

	}

	/**
	 * Send notification
	 */
	public function dispatch(){

		// TODO: cache users, so less query done on server side.
		// all users to be notified
		$users = [];

		// select user(s)
		if( $this->id_user ){

			$user = User::findById( $this->id_user );

			$users[] = [
				'id'      => $user->id,
				'email'   => $user->email,
			];

		// if not for a specific user then select other user(s)
		} else {

			// select everyone
			if(
				$this->hasOption( self::NOTIFICATION_FOR_USER ) &&
				$this->hasOption( self::NOTIFICATION_FOR_MODERATOR ) &&
				$this->hasOption( self::NOTIFICATION_FOR_ADMIN )
			){

				$options = self::ACCOUNT_ENABLED;

				// select all
				$sel = DB::getInstance()->prepare( '
					SELECT
						`id`, `email`
					FROM `users`
					WHERE
						( `options` & ? ) = ?
				' );
				$sel->bind_param( 'ii', $options, $options );
				$sel->execute();
				$sel->bind_result( $id, $email );

				while( $sel->fetch() ){

					$users[] = [
						'id'    => $id,
						'email' => $email,
					];

				}

				$sel->close();

				} else {

					$recipients = [];

					// "moderators" = accounts that can manage users
					if( $this->hasOption( self::NOTIFICATION_FOR_MODERATOR ) ){

						$recipients += $this->selectUsersByPermission( 'users.view' );

					}

					// "admins" = accounts that can manage roles & permissions
					if( $this->hasOption( self::NOTIFICATION_FOR_ADMIN ) ){

						$recipients += $this->selectUsersByPermission( 'roles.view' );

					}

					$users = array_values( $recipients );

				}

		}

		// loop through all users
		foreach( $users as $user ){

			// insert into database
			NotificationsModel::add(
				id_user: $user['id'],
				message: $this->message,
				details: $this->details
			);

			// notify via email
			if( $this->notifyViaEmail ){

				// add new email to queue
				$mail = new MailQueue(
					email: $user['email'],
					subject: sprintf( 'Notification: %s', $this->message ),
					body: $this->details
				);

			}

		}

	}

}
