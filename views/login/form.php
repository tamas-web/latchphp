<?php
use App\Config;
use App\Http\URL;
use App\Html\Builder;
use App\Filters\Filter;

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
												'elementType' => 'form',
												'id'          => 'sign-in',
												'action'      => URL::BUILD( 'api/forms/login' ),
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
																		'name'         => 'email',
																		'class'        => 'form-control',
																		'placeholder'  => 'E-mail',
																		'required'     => true,
																		'autocomplete' => false,
																		'dataset'      => [

																			'rule'         => 'required|email',
																			'msg-required' => "Please enter an email address",
																			'msg-email'    => "Please enter a valid email address",

																		], // dataset
																	], // input#email

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
																'elementType' => 'div',
																'class'       => 'input-group mb-3',
																'fields'      => [

																	[
																		'elementType'  => 'input',
																		'type'         => 'password',
																		'name'         => 'password',
																		'class'        => 'form-control',
																		'placeholder'  => 'Password',
																		'autocomplete' => 'off',
																		'required'     => true,
																		'dataset'      => [

																			'rule'         => 'required',
																			'msg-required' => "Please enter a password",

																		], // dataset
																	], // input#password

																	[
																		'elementType' => 'raw',
																		'html'        => '<span class="input-group-text">
																			<i class="bi bi-lock"></i>
																		</span>',
																	],

																], // div.input-group fields
															], // div.input-group

														], // div.mb3 fields
													], // div.mb3

													[
														'elementType' => 'div',
														'class'       => 'row d-flex align-items-center',
														'fields'      => [

															[
																'elementType' => 'div',
																'class'       => 'col-md-6 mb-3',
																'fields'      => [

																	[
																		'elementType' => 'input::icheck',
																		'id'          => 'remember',
																		'name'        => 'remember',
																		'value'       => 'remember',
																		'text'        => 'Remember me',
																	], // input#remember

																], // div.col-md-6 fields
															], // div.col-md-6

															[
																'elementType' => 'div',
																'class'       => 'col-md-6 mb-3 d-flex justify-content-end',
																'fields'      => [

																	[
																		'elementType' => 'button',
																		'type'        => 'submit',
																		'class'       => 'btn btn-primary',
																		'name'        => 'signin',
																		'value'       => 'signin',
																		'text'        => 'Sign in',
																	], // input#remember

																], // div.col-md-6 fields
															], // div.col-md-6

														], // div.row fields
													], // div.row

												], // form#sign-in fields
											], // form#sign-in

										], // div.card-body fields
									], // div.card-body

								], // div.card fields
							], // div.card

							[
								'elementType' => 'raw',
								'html'        => Filter::apply( 'login_form_links', '' ),
							], // login_form_links (plugin-contributed)

						], // div.col-md-8 col-lg-6 col-xl-5 fields
					], // div.col-md-8 col-lg-6 col-xl-5

				], // div.row fields
			], // div.row

		], // div.container fields
	], // div.container

] );