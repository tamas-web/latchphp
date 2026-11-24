<?php
use App\Config;
use App\Http\URL;
use App\Html\Builder;

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
												'elementType' => 'p',
												'class'       => 'text-center',
												'text'        => 'Create a new account',
											],

											[
												'elementType' => 'form',
												'id'          => 'register',
												'action'      => URL::BUILD( 'api/forms/register' ),
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
																		'type'         => 'text',
																		'name'         => 'name',
																		'class'        => 'form-control',
																		'placeholder'  => 'First name',
																		'required'     => true,
																		'autocomplete' => false,
																		'dataset'      => [
																			'rule'         => 'required',
																			'msg-required' => 'Please enter your first name',
																		],
																	], // input#name

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text"><i class="bi bi-person"></i></span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb-3 fields
													], // div.mb-3

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
																		'type'         => 'text',
																		'name'         => 'surname',
																		'class'        => 'form-control',
																		'placeholder'  => 'Last name',
																		'required'     => true,
																		'autocomplete' => false,
																		'dataset'      => [
																			'rule'         => 'required',
																			'msg-required' => 'Please enter your last name',
																		],
																	], // input#surname

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text"><i class="bi bi-person"></i></span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb-3 fields
													], // div.mb-3

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
																		'type'         => 'text',
																		'name'         => 'phone',
																		'class'        => 'form-control',
																		'placeholder'  => 'Phone number',
																		'required'     => true,
																		'autocomplete' => false,
																		'dataset'      => [
																			'rule'         => 'required|phone',
																			'msg-required' => 'Please enter your phone number',
																			'msg-phone'    => 'The phone number is not valid',
																		],
																	], // input#phone

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text"><i class="bi bi-telephone"></i></span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb-3 fields
													], // div.mb-3

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
																		'type'         => 'email',
																		'name'         => 'email',
																		'class'        => 'form-control',
																		'placeholder'  => 'E-mail',
																		'required'     => true,
																		'autocomplete' => false,
																		'dataset'      => [
																			'rule'         => 'required|email',
																			'msg-required' => 'Please enter an email address',
																			'msg-email'    => 'Please enter a valid email address',
																		],
																	], // input#email

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text"><i class="bi bi-envelope"></i></span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb-3 fields
													], // div.mb-3

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
																		'class'        => 'form-control',
																		'placeholder'  => 'Password',
																		'required'     => true,
																		'autocomplete' => 'new-password',
																		'dataset'      => [
																			'rule'         => 'required|min:8|regexp',
																			'regexp'       => '^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\s).*$',
																			'msg-required' => 'Please enter a password',
																			'msg-min'      => 'The password must be at least 8 characters long',
																			'msg-regexp'   => 'The password must contain at least one letter, one number and one special character !@#$%^&*',
																		],
																	], // input#password

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text"><i class="bi bi-lock"></i></span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb-3 fields
													], // div.mb-3

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
																		'name'         => 'password_confirm',
																		'class'        => 'form-control',
																		'placeholder'  => 'Confirm password',
																		'required'     => true,
																		'autocomplete' => 'new-password',
																		'dataset'      => [
																			'rule'         => 'required|matches:password',
																			'msg-required' => 'Please confirm the password',
																			'msg-matches'  => 'The passwords entered do not match',
																		],
																	], // input#password_confirm

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text"><i class="bi bi-lock"></i></span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb-3 fields
													], // div.mb-3

													[
														'elementType' => 'div',
														'class'       => 'mb-3',
														'fields'      => [

															[
																'elementType' => 'input::icheck',
																'id'          => 'terms',
																'name'        => 'terms',
																'value'       => 'agree',
																'text'        => 'I agree to the terms and conditions',
																'sub'         => 'Confirm that you have read and accept the terms of use.',
																'dataset'     => [
																	'rule'         => 'required',
																	'msg-required' => 'You must accept the terms and conditions',
																],
															], // input#terms

														], // div.mb-3 fields
													], // div.mb-3

													[
														'elementType' => 'button',
														'type'        => 'submit',
														'class'       => 'btn btn-primary w-100',
														'name'        => 'registerBtn',
														'value'       => 'register',
														'text'        => 'Register',
													], // button#registerBtn

												], // form#register fields
											], // form#register

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
										'html'        => '<i class="bi bi-arrow-left"></i> Already have an account?',
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
