<?php
use App\Config;
use App\Http\URL;
use App\Html\Builder;

if( $this->data->validRequest ){

	Builder::Generate( [

		[
			'elementType' => 'div',
			'class'       => 'container vh-100',
			'fields'      => [

				[
					'elementType' => 'div',
					'class'       => 'row justify-content-center h-100 align-items-center',
					'fields'      => [

						[
							'elementType' => 'div',
							'class'       => 'col-md-8 col-lg-6 col-xl-5',
							'fields'      => [

								[
									'elementType' => 'div',
									'class'       => 'card',
									'fields'      => [

										[
											'elementType' => 'div',
											'class'       => 'card-header d-flex justify-content-center',
											'fields'      => [

												[
													'elementType' => 'raw',
													'html'        => '<span class="d-inline-flex align-items-center gap-2"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" style="stroke:var(--lp-accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4 H5 a2 2 0 0 0 -2 2 v12 a2 2 0 0 0 2 2 h2"/><path d="M17 4 h2 a2 2 0 0 1 2 2 v12 a2 2 0 0 1 -2 2 h-2"/><path d="M9 12 h6"/></svg><span style="font-weight:700;font-size:26px;letter-spacing:-.02em;color:var(--lp-text)">' . esc_html( Config::get( 'app.site.name' ) ) . '</span></span>',
												], // raw

											], // div.card-header fields
										], // div.card-header

										[
											'elementType' => 'div',
											'class'       => 'card-body',
											'fields'      => [

												[
													'elementType' => 'div',
													'class'       => 'alert alert-info',
													'text'        => 'Please enter a new password. The password must be at least 8 characters long and contain a letter, a number and a special character !@#$%^&* '
												],

												[
													'elementType' => 'form',
													'id'          => 'password-change',
													'action'      => URL::BUILD( 'api/forms/password-reset/change-password' ),
													'method'      => 'post',
													'fields'      => [

														[
															'elementType' => 'div',
															'class'       => 'mb-3',
															'fields'      => [

																[
																	'elementType' => 'div',
																	'class'       => 'input-group mb-3',
																	'fields'      => [

																		[
																			'elementType'  => 'input',
																			'type'         => 'password',
																			'name'         => 'password',
																			'placeholder'  => 'Account password',
																			'required'     => true,
																			'autocomplete' => 'new-password',
																			'dataset'      => [

																				'rule'         => 'required|min:8|regexp',
																				'regexp'       => '^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\s).*$',
																				'msg-required' => 'Please enter a password',
																				'msg-min'      => 'The password must be at least 8 characters long',
																				'msg-regexp'   => 'The password must contain at least one letter, one number and one special character !@#$%^&*',

																			], // dataset
																		], // input#password

																		[
																			'elementType' => 'raw',
																			'html'        => '<span class="input-group-text">
																				<i class="bi bi-envelope"></i>
																			</span>',
																		],

																	], // div.input-group fields
																], // div.input-group

																[
																	'elementType' => 'div',
																	'class'       => 'input-group mb-3',
																	'fields'      => [

																		[
																			'elementType'  => 'input',
																			'type'         => 'password',
																			'name'         => 'password_confirm',
																			'placeholder'  => 'Confirm password',
																			'required'     => true,
																			'autocomplete' => 'new-password',
																			'dataset'      => [

																				'rule'         => 'required|matches:password',
																				'msg-required' => 'Please confirm the password',
																				'msg-matches'  => 'The passwords entered do not match',

																			], // dataset
																		], // input#password_confirm

																		[
																			'elementType' => 'raw',
																			'html'        => '<span class="input-group-text">
																				<i class="bi bi-envelope"></i>
																			</span>',
																		],

																	], // div.input-group fields
																], // div.input-group

															], // div.mb3 fields
														], // div.mb3

														[
															'elementType' => 'div',
															'class'       => 'mb-3',
															'fields'      => [

																[
																	'elementType' => 'input::icheck',
																	'id'          => 'deauthorize',
																	'name'        => 'deauthorize',
																	'value'       => 'deauthorize',
																	'text'        => 'log out all devices',
																],

															], // div.mb-3 fields
														], // div.mb-3

														[
															'elementType' => 'input',
															'name'        => 'token',
															'type'        => 'hidden',
															'value'       => $this->data->token,
														], // token

														[
															'elementType' => 'div',
															'class'       => 'mb-3',
															'fields'      => [

																[
																	'elementType' => 'button',
																	'name'        => 'changePassword',
																	'value'       => 'changePassword',
																	'type'        => 'submit',
																	'class'       => 'btn btn-primary w-100',
																	'text'        => 'Get new password',
																], // input#remember

															], // div.mb-3 fields
														], // div.mb-3

													], // form#sign-in fields
												], // form#sign-in

											], // div.card-body fields
										], // div.card-body

									], // div.card fields
								], // div.card

								[
									'elementType' => 'p',
									'class'       => 'text-center',
									'fields'      => [

										[
											'elementType' => 'a',
											'href'        => URL::BUILD( 'login' ),
											'html'        => '<i class="bi bi-arrow-left"></i> Log in',
										], // a login

									], // p fields
								], // p

							], // div.col-md-8 col-lg-6 col-xl-5 fields
						], // div.col-md-8 col-lg-6 col-xl-5

					], // div.row fields
				], // div.row

			], // div.container fields
		], // div.container

	] );

} else {

	Builder::Generate( [

		[
			'elementType' => 'div',
			'class'       => 'container vh-100',
			'fields'      => [

				[
					'elementType' => 'div',
					'class'       => 'row justify-content-center h-100 align-items-center',
					'fields'      => [

						[
							'elementType' => 'div',
							'class'       => 'col-md-8 col-lg-6 col-xl-5',
							'fields'      => [

								[
									'elementType' => 'div',
									'class'       => 'card',
									'fields'      => [

										[
											'elementType' => 'div',
											'class'       => 'card-header d-flex justify-content-center',
											'fields'      => [

												[
													'elementType' => 'raw',
													'html'        => '<span class="d-inline-flex align-items-center gap-2"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" style="stroke:var(--lp-accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4 H5 a2 2 0 0 0 -2 2 v12 a2 2 0 0 0 2 2 h2"/><path d="M17 4 h2 a2 2 0 0 1 2 2 v12 a2 2 0 0 1 -2 2 h-2"/><path d="M9 12 h6"/></svg><span style="font-weight:700;font-size:26px;letter-spacing:-.02em;color:var(--lp-text)">' . esc_html( Config::get( 'app.site.name' ) ) . '</span></span>',
												], // raw

											], // div.card-header fields
										], // div.card-header

										[
											'elementType' => 'div',
											'class'       => 'card-body',
											'fields'      => [

												[
													'elementType' => 'div',
													'class'       => 'alert alert-danger',
													'html'        => sprintf( 'This link is invalid or has expired.
														To reset your password, please visit this
														<a href="%s">link</a>.',
														URL::BUILD( 'password-reset' )
													),
												],

											], // div.card-body fields
										], // div.card-body

									], // div.card fields
								], // div.card

								[
									'elementType' => 'p',
									'class'       => 'text-center',
									'fields'      => [

										[
											'elementType' => 'a',
											'href'        => URL::BUILD( 'login' ),
											'html'        => '<i class="bi bi-arrow-left"></i> Log in',
										], // a login

									], // p fields
								], // p

							], // div.col-md-8 col-lg-6 col-xl-5 fields
						], // div.col-md-8 col-lg-6 col-xl-5

					], // div.row fields
				], // div.row

			], // div.container fields
		], // div.container

	] );

}
