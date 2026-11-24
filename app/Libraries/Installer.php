<?php

namespace App\Libraries;

use App\Constants as C;
use App\Database\Connector as DB;
use App\Filters\Common as CommonFilters;
use App\Libraries\{ Cli, Model, User, Password };
use App\Models\Settings\Roles;

class Installer {

	use \App\Traits\randomChars;

	static public function createUser(){

		// string $phone, int $options = self::ACCOUNT_ENABLED
		Cli::out( 'The following script will create a user/admin account!', Cli::COLOR_RED );
		Cli::out( 'Please provide the following information for the new user account.', Cli::COLOR_RED );

		// is admin
		sectionIsAdmin:
			Cli::out( 'Account type [admin/user]: ', Cli::COLOR_CYAN, addNewLine: false );
			$type = strtolower( trim( fgets( STDIN ) ) );

			if( ! in_array( $type, [ 'admin', 'user' ] ) ){
				Cli::out( 'Unknow option', Cli::COLOR_RED );
				goto sectionIsAdmin;
			}

		// email
		sectionEmail:
			Cli::out( 'E-mail*: ', Cli::COLOR_CYAN, addNewLine: false );
			$email = validate_email( trim( fgets( STDIN ) ) );

			if( ! $email ){
				Cli::out( 'Please provide a valid email address.', Cli::COLOR_RED );
				goto sectionEmail;
			}

		// password
		sectionPassword:
			// readline_callback_handler_install( '', function(){} );
			Cli::out(
				'Password(minimum ' . CommonFilters::PASSWORD_MINIM_LENGTH . ' chars)*: ',
				Cli::COLOR_CYAN,
				addNewLine: false
			);
			$password = trim( fgets( STDIN ) );
			// readline_callback_handler_remove();

			$pLength = password_minim_length( $password );

			if( ! $pLength ){
				Cli::out( '' );
				Cli::out( 'Password doesn\'t meet the minimum length.', Cli::COLOR_RED );
				goto sectionPassword;
			}

		// print new line
		Cli::out( '' );

		// name
		Cli::out( 'Name(Super): ', Cli::COLOR_CYAN, addNewLine: false );
		$name = trim( fgets( STDIN ) );
		$name = $name ? $name : 'Super';

		// surname
		Cli::out( 'Surname(Admin): ', Cli::COLOR_CYAN, addNewLine: false );
		$surname = trim( fgets( STDIN ) );
		$surname = $surname ? $surname : 'Admin';

		// phone
		Cli::out( 'Phone(): ', Cli::COLOR_CYAN, addNewLine: false );
		$phone = trim( fgets( STDIN ) );

		Cli::out( '' );
		Cli::out( $type . ' account details:', Cli::COLOR_CYAN );
		Cli::out( '' );

		Cli::out( 'E-mail: ' . $email, Cli::COLOR_CYAN );
		Cli::out( 'Name: ' . $name, Cli::COLOR_CYAN );
		Cli::out( 'Surname: ' . $surname, Cli::COLOR_CYAN );
		Cli::out( 'Phone: ' . $phone, Cli::COLOR_CYAN );

		Cli::out( '' );

		$options = C::ACCOUNT_ENABLED;
		$idRole  = 0;

		if( 'admin' === $type ){

			// super-admin (god mode); also assign the Administrator role if seeded
			$options |= C::ACCOUNT_ADMIN;

			foreach( Roles::getAllRoles() as $role ){
				if( 'Administrator' === $role->name ){
					$idRole = $role->id;
					break;
				}
			}

		} else {

			// pick a role from the seeded list
			$roles = Roles::getAllRoles();

			if( ! $roles ){

				Cli::out( 'No roles found. Run the seeder first: php database/seed.php', Cli::COLOR_RED );

			} else {

				while( true ){

					Cli::out( 'Available roles:', Cli::COLOR_CYAN );

					foreach( $roles as $role ){
						Cli::out( sprintf( '  [%u] %s', $role->id, $role->name ), Cli::COLOR_CYAN );
					}

					Cli::out( 'Role id: ', Cli::COLOR_CYAN, addNewLine: false );
					$idRole = (int) trim( fgets( STDIN ) );

					foreach( $roles as $role ){
						if( $role->id === $idRole ){
							break 2;
						}
					}

					Cli::out( 'Unknown role id', Cli::COLOR_RED );
					$idRole = 0;

				}

			}

		}

		$installer = new self;

		sectionCreateAccount:

			if( ! $installer->createAccount(
				email: $email,
				password: $password,
				name: $name,
				surname: $surname,
				phone: $phone,
				options: $options,
				idRole: $idRole,
				error: $error,
				errno: $errno,
			) ){

				Cli::out( 'Failed to create new account.', Cli::COLOR_RED );
				Cli::out( sprintf( 'Error(%u): %s.', $errno, $error ), Cli::COLOR_RED );

				sectionRetry:

					Cli::out( 'Do you want to retry[y/n]: ', Cli::COLOR_CYAN, addNewLine: false );
					$retry = strtolower( trim( fgets( STDIN ) ) );

					if( ! in_array( $retry, [ 'y', 'n' ] ) ){
						Cli::out( 'Unknow option', Cli::COLOR_RED );
						goto sectionRetry;
					}

					if( 'y' === $retry ){
						goto sectionCreateAccount;
					}

			} else {

				Cli::out( $type . ' account created successfully.', Cli::COLOR_GREEN );

			}

		sectionAddNewUser:

			Cli::out( 'Add new user?[y/n]: ', Cli::COLOR_CYAN, addNewLine: false );
			$addNewUser = strtolower( trim( fgets( STDIN ) ) );

			if( ! in_array( $addNewUser, [ 'y', 'n' ] ) ){
				Cli::out( 'Unknow option', Cli::COLOR_RED );
				goto sectionRetry;
			}

			if( 'y' === $addNewUser ){
				goto sectionIsAdmin;
			}

		Cli::out( 'Bye!', Cli::COLOR_MAGENTA );

	}

	protected function createAccount( string $name, string $surname, string $email, string $phone, string $password, int $options = C::ACCOUNT_ENABLED, int $idRole = 0, ?string &$error = '', ?int &$errno = 0 ) : ?User {

		// user
		$user = null;

		// generate keys
		$public_key  = $this->randomChars( C::PUBLIC_PRIVATE_KEY_LENGTH );
		$private_key = $this->randomChars( C::PUBLIC_PRIVATE_KEY_LENGTH );

		// hash password
		$hash = Password::hash( $password );

		// autocommit off
		DB::getInstance()->autocommit( false );

		// either
		$phone = $phone ? $phone : null;

		// register
		$ins = DB::getInstance()->prepare( '
			INSERT INTO users
				(
					id_role, name, surname, phone,
					email, password,
					public_key, private_key,
					options
				)
			VALUES
				(
					?, ?, ?, ?,
					?, ?,
					?, ?,
					?
				)
		' );

		$ins->bind_param(
			'isssssssi',
			$idRole, $name, $surname, $phone,
			$email, $hash,
			$public_key, $private_key,
			$options
		);

		$ins->execute();

		$error = $ins->error;
		$errno = $ins->errno;

		if( $ins->errno ){

			DB::getInstance()->rollback();

		} else {

			$user = User::findById( $ins->insert_id );

			// commit
			DB::getInstance()->commit();

		}

		$ins->close();

		// autocommit on
		DB::getInstance()->autocommit( true );

		return $user;

	}

}