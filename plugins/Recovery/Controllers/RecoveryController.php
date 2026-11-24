<?php

namespace Plugins\Recovery\Controllers;

use App\Alerts\Alert;
use App\Config;
use App\Database\Connector as DB;
use App\Datatype\Response;
use App\Html\Elements\{ Div, A, P };
use App\Http\{ Request, URL };
use App\Libraries\{ Controller, User, Password };
use App\Mailer\Mail;
use App\Views\View;
use Plugins\Recovery\Models\Recovery;

class RecoveryController extends Controller {

	use \App\Traits\randomChars;

	protected $pageTitle = 'Password reset';

	public function form( Request $request ){

		$request->view()
			->header()
				->remove( 'wrapper_start' )
				->remove( 'header.top' )
				->remove( 'header.menu' )
				->remove( 'content_start' );

		$body = new View( dirname( __DIR__ ) . '/templates' );
		$body->body()->add( 'recovery.form' );

		$request->view()
			->body()
				->data( [
					'output' => $body->buffer(),
				] )
				->add( 'blank_output' );

		$request->view()
			->footer()
				->remove( 'wrapper_end' )
				->remove( 'content_end' );

		$request->view()->render();

	}

	public function changePassword( Request $request ){

		$request->view()
			->header()
				->remove( 'wrapper_start' )
				->remove( 'header.top' )
				->remove( 'header.menu' )
				->remove( 'content_start' );

		$body = new View( dirname( __DIR__ ) . '/templates' );
		$body->body()
			->add( 'recovery.change-password' )
			->data( [
				'token'        => esc_html( $request->getParameter( 0 ) ),
				'validRequest' => !! Recovery::userAndToken( $request->getParameter( 0 ) ), // cast to boolean
			] );

		$request->view()
			->body()
				->data( [
					'output' => $body->buffer(),
				] )
				->add( 'blank_output' );

		$request->view()
			->footer()
				->remove( 'wrapper_end' )
				->remove( 'content_end' );

		$request->view()->render();

	}

	public function processRecover( Request $request ){

		$response = new Response;

		$recover = $request->post( 'recover', 'trim' );

		if( $recover ){

			$email     = $request->post( 'email', fn( $email ) => validate_email( $email ) );

			if( ! $email ){

				Alert::error( 'Please check that the email address is correct' );

			} else {

				// query user and check pass
				$user = User::findByEmail( $email );

				if( $user ){

					if( $user->isEnabled() ){

						// create recovery token
						$token = Recovery::newToken( $user->email );

						if( ! $token ){

							Alert::error( 'The password reset process failed. Please try again later.' );

						}

						$hex = Recovery::encode( [ $user->email, $token ] );

						$url = URL::BUILD( sprintf( 'password-reset/%s', $hex ) );

						// mail content
						$content = Div::Create()
							->append(
								P::Create()
									->text( sprintf(
										'Hello, %s %s',
										esc_html( $user->name ),
										esc_html( $user->surname )
									) )
							)
							->append(
								P::Create()
									->text( 'To reset your password click the link below:' )
							)
							->append(
								P::Create()
									->append(
										A::Create()
											->href( $url )
											->text( 'Click here' )
									)
							)
							->append(
								P::Create()
									->text( 'If you did not request a password reset, please ignore this email.' )
							)
							->buffer();

						// new mail
						$mail = new Mail(
							email: $user->email,
							subject: 'Password reset',
							body: $content
						);

						if( $mail->send() ){

							$response['form_reset'] = true;

							Alert::success( 'You will soon receive an email with instructions for resetting your password.' );

						} else {

							Alert::error( 'We could not send the email. Please try again later.' );

						}

					} else {

						Alert::error( 'This user account is disabled.' );

					}

				} else {

					Alert::error( 'Please check that the email address is correct.' );

				}

			}

		} else {

			Alert::error( 'An error occurred during the password reset process.' );

		}

		echo $response;

	}

	public function processChangePassword( Request $request ){

		$response = new Response;

		$changePassword = $request->post( 'changePassword' );
		$token          = $request->post( 'token' );

		if( $changePassword && $token ){

			$password         = $request->post( 'password', 'trim' );
			$password_confirm = $request->post( 'password_confirm', 'trim' );
			$deauthorize      = $request->post( 'deauthorize' );
			// get recovery parameters
			$reset = Recovery::userAndToken( $token );

			if( $reset ){

				if( ! is_strong_password( $password ) ){

					$response['form_errors'][] = 'password';

					$has_minimum_length = password_minim_length( $password );
					$has_special_char   = password_has_special_char( $password );
					$has_letter         = password_has_letter( $password );

					$pass_errors = [];

					if( ! $has_minimum_length ){

						$pass_errors[] = '<li>at least 8 characters</li>';

					}

					if( ! $has_special_char ){

						$pass_errors[] = '<li>at least one number or special character</li>';

					}

					if( ! $has_letter ){

						$pass_errors[] = '<li>at least one letter</li>';

					}

					if( $pass_errors ){

						Alert::error( sprintf( 'The password does not meet the following requirements:<ul>%s</ul>', join( '', $pass_errors ) ) );

					}

				} else {

					if( 0 !== strcmp( $password, $password_confirm ) ){

						$response['form_errors'][] = 'password';
						$response['form_errors'][] = 'password_confirm';

						Alert::error( 'The passwords entered do not match. Please try again.' );

					}

				}

				if( ! Alert::has() ){

					// query user and check pass
					$user = User::findByEmail( (string)$reset['email'] );

					if( $user ){

						if( $user->isEnabled() ){

							// autocommit off
							DB::getInstance()->autocommit( false );

							// delete token
							Recovery::deleteToken( $reset['id'] );

							if( $deauthorize ){

								// generate keys
								$user->public_key  = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );
								$user->private_key = $this->randomChars( self::PUBLIC_PRIVATE_KEY_LENGTH );

							}

							// hash password
							$user->password = Password::hash( $password );

							if( $user->save() ){

								// commit
								DB::getInstance()->commit();

								Alert::success( 'The password was changed successfully.' );

								// stop further execution and redirect with message to login page
								$request->redirect( '/login' );

							} else {

								DB::getInstance()->rollback();

								Alert::error( 'Changing the password failed. Please try again later.' );

							}

							// autocommit on
							DB::getInstance()->autocommit( true );

						} else {

							Alert::error( 'This user account is disabled.' );

						}

					} else {

						Alert::error( 'Please check that the email address is correct.' );

					}

				}

			} else {

				Alert::error( 'The password reset link has expired or is invalid.' );

				$request->redirect( '/password-reset' );

			}

		} else {

			Alert::error( 'An error occurred while submitting the form. Reload the page and try again.' );

		}

		echo $response;

	}

}
