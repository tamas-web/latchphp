<?php

use App\Html\Builder;
use App\Http\URL;

Builder::generate( [
	[
		'elementType' => 'div',
		'class'       => 'row',
		'fields'      => [
			[
				'elementType' => 'div',
				'class'       => 'col-md-12',
				'fields'      => [
					[
						'elementType' => 'form',
						'action'      => URL::BUILD( sprintf( 'api/settings/roles/%u/delete', $this->data->role->id ) ),
						'method'      => 'post',
						'fields'      => [
							[
								'elementType' => 'div',
								'class'       => 'card card-secondary',
								'fields'      => [
									[
										'elementType' => 'div',
										'class'       => 'card-body',
										'fields'      => [
											[
												'elementType' => 'div',
												'class'       => 'form-group',
												'fields'      => [
													[
														'elementType' => 'label',
														'html'        => sprintf( 'Do you want to delete the role <strong>#%u - %s</strong>?',
															$this->data->role->id,
															$this->data->role->name,
														),
													],
												],
											],
										],
									],
									[
										'elementType' => 'div',
										'class'       => 'card-footer d-flex align-items-center justify-content-between',
										'fields'      => [
											[
												'elementType' => 'button',
												'type'        => 'submit',
												'class'       => 'btn btn-danger',
												'text'        => 'Delete',
												'icon'        => 'bi bi-trash',
											],
											[
												'elementType' => 'a',
												'href'        => URL::BUILD( sprintf( 'admin/settings/roles-permissions/roles' ) ),
												'class'       => 'btn btn-outline-light',
												'html'        => '<span class="btn-icon-left"><i class="bi bi-arrow-left"></i></span> Back',
											],
										],
									],
								],
							],
						],
					],
				],
			],
		],
	],
] );
