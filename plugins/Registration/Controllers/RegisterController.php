<?php

namespace Plugins\Registration\Controllers;

use App\Alerts\Alert;
use App\Config;
use App\Datatype\Response;
use App\Html\Elements\{ Div, A, P };
use App\Http\{ Request, URL };
use App\Filters\Filter;
use App\Libraries\Controller;
use App\Mailer\Mail;
use App\Models\Settings\Role;
use App\Views\View;
use Plugins\Registration\Models\Register;

class RegisterController extends Controller {

	use \App\Traits\randomChars;
	use \App\Traits\humanReadableTimePassedSince;

	const EMAIL_SEND_TIMEOUT = 60; // 60 seconds

	protected $pageTitle = 'Registration';

	public function form( Request $request ){

		if( ! Config::get( 'settings.users.registration.enabled' ) ){

			$request->redirect( '/login' );

		}

		$body = new View( dirname( __DIR__ ) . '/templates' );
		$body->body()->add( 'register.form' );

		$request->view()
			->body()
				->data( [
					'output' => $body->buffer(),
				] )
				->add( 'blank_output' );

		$request->view()
			->header()
				->remove( 'wrapper_start' )
				->remove( 'header.top' )
				->remove( 'header.menu' )
				->remove( 'content_start' );

		$request->view()
			->footer()
				->remove( 'wrapper_end' )
				->remove( 'content_end' );

		$request->view()->render();

	}

	public function processRegister( Request $request ){

		$response = new Response;

		if( ! Config::get( 'settings.users.registration.enabled' ) ){

			Alert::error( 'Registration is disabled.' );

			$request->redirect( '/login' );

		}

		$register = $request->post( 'registerBtn', 'trim' );

		if( $register ){

			$name             = $request->post( 'name', 'trim' );
			$surname          = $request->post( 'surname', 'trim' );
			$phone            = $request->post( 'phone', fn( $phone ) => sanitize_phone_number( $phone ) );
			$email            = $request->post( 'email', fn( $email ) => validate_email( $email ) );
			$password         = $request->post( 'password', 'trim' );
			$password_confirm = $request->post( 'password_confirm', 'trim' );
			$terms_conditions = $request->post( 'terms' );

			$is_required = Filter::apply( 'register_required', [
				'name'             => &$name,
				'surname'          => &$surname,
				'phone'            => &$phone,
				'email'            => &$email,
				'terms_conditions' => &$terms_conditions,
			] );

			$messages = Filter::apply( 'register_required_messages', [
				'name'             => 'First name',
				'surname'          => 'Last name',
				'phone'            => 'Phone',
				'email'            => 'E-mail',
				'terms_conditions' => 'Terms and conditions',
			] );

			$missing_values = [];

			foreach( $is_required as $key => $value ){

				if( ! $value ){

					$missing_values[] = sprintf( '<li>%s</li>', $messages[ $key ] ?? $key );
					$response['form_errors'][] = $key;

				}

			}

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

					Alert::error( 'The passwords do not match. Please try again.' );

				}

			}

			if( $missing_values ){

				Alert::error( sprintf( 'Please check the following fields:<ul>%s</ul>', join( '', $missing_values ) ) );

			}

			if( ! Alert::has() ){

				// check if email or phone exists
				$_check = Register::checkIfEmailPhoneExists( email: $email, phone: $phone );

				if( $_check['email'] ){

					Alert::error( 'This email address is already in use.' );

				}

				if( $_check['phone'] ){

					Alert::error( 'This phone number is already in use.' );

				}

				if( ! Alert::has() ){

					$user = Register::createUser(
						name: $name,
						surname: $surname,
						phone: $phone,
						email: $email,
						password: $password,
						idRole: Role::idByName( 'User' ),
					);

					if( $user ){

						// create confirmation token
						$token = $this->randomChars( 255 );

						if( ! $user->meta()->save( 'confirmAccountToken', $token ) ){

							Alert::info( 'Registration succeeded, but we could not generate the account confirmation code.
								Please log in to regenerate the code.' );

							// redirect
							$request->redirect( '/login' );

							$response['form_reset'] = true;

						} else {

							$hex = Register::encode( [ $user->email, $token ] );

							$url = URL::BUILD( sprintf( '/register/confirm/email/%s', $hex ) );

							// new email
							$content = Div::Create()
								->append(
									P::Create()
										->html( '<br><br>' )
								)
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
										->text( 'You received this email because you created a user account on
												our platform. To confirm your account, click
												the following link:' )
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
										->html( '<br><br>' )
								)
								->buffer();

							// new mail
							$mail = new Mail(
								email: $user->email,
								subject: 'Account confirmation',
								body: $content
							);

							if( $mail->send() ){

								Alert::success( 'You will shortly receive an email with instructions on how to confirm your account.' );

							} else {

								Alert::info( 'We could not send the confirmation email. Please log in to resend the confirmation email.' );

							}

							// redirect
							$request->redirect( '/login' );

							$response['form_reset'] = true;

						}

					} else {

						Alert::error( 'Registration failed. Please try again later.' );

					}

				}

			}

		} else {

			Alert::error( 'An error occurred while submitting the form. Please reload the page and try again.' );

		}

		echo $response;

	}

	public function confirmAccount( Request $request ){

		if( Register::confirmEmail( $request->getParameter( 0 ) ) ){

			$request->redirect( '/login' );

		} else {

			$this->pageTitle = 'Invalid link';

			$body = new View( dirname( __DIR__ ) . '/templates' );
			$body->body()->add( 'register.invalid-token' );

			$request->view()
				->body()
					->data( [
						'output' => $body->buffer(),
					] )
					->add( 'blank_output' );

			$request->view()
				->header()
					->remove( 'wrapper_start' )
					->remove( 'header.top' )
					->remove( 'header.menu' )
					->remove( 'content_start' );

			$request->view()
				->footer()
					->remove( 'wrapper_end' )
					->remove( 'content_end' );

			$request->view()->render();

		}

	}

	public function resendConfirmationEmail( Request $request ){

		$response = new Response;

		$user = $request->user();

		if( $user->hasOption( self::ACCOUNT_EMAIL_CONFIRMED ) ){

			Alert::info( 'This email address is already confirmed.' );

			$request->redirect( '/' );

		} else {


			// create recovery token, if doesn't exists
			if( ! $user->meta()->confirmAccountToken ){

				$token = $this->randomChars( 255 );

				if( ! $user->meta()->save( 'confirmAccountToken', $token ) ){

					Alert::info( 'We could not generate the confirmation code for your account. Please try again later.' );

				}

				$user->meta()->save( 'confirmAccountNextInterval', time() + self::EMAIL_SEND_TIMEOUT );

			}

			$expires = (int) $user->meta()->confirmAccountNextInterval ?? time();

			if( time() >= $expires ){

				if( $user->meta()->confirmAccountToken ){

					$hex = Register::encode( [ $user->email, $user->meta()->confirmAccountToken ] );

					$url = URL::BUILD( sprintf( '/register/confirm/email/%s', $hex ) );

					// new email
					$content = Div::Create()
						->append(
							P::Create()
								->html( '<br><br>' )
						)
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
								->text( 'You received this email because you created a user account on
										our platform. To confirm your account, click
										the following link:' )
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
								->html( '<br><br>' )
						)
						->buffer();

					// new mail
					$mail = new Mail(
						email: $user->email,
						subject: 'Account confirmation',
						body: $content
					);

					if( $mail->send() ){

						Alert::success( 'You will shortly receive an email with instructions on how to confirm your account.' );

						$expires = time() + self::EMAIL_SEND_TIMEOUT;

						$user->meta()->save( 'confirmAccountNextInterval', $expires );

						$response['timeout'] = $expires - time();

					} else {

						Alert::info( 'We could not send the confirmation email. Please try again later.' );

					}

				}

			} else {

				Alert::error( 'You must wait ' . $this->passedTimeSince( $expires ) . ' before resending the account confirmation email.' );

				$response['timeout'] = $expires - time();

			}

		}

		echo $response;

	}

}
