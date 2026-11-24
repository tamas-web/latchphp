<?php
use App\Http\{ Request, URL };
use App\Html\Builder;

?>

<div class="card">
	<div class="card-header d-sm-flex justify-content-between align-items-center">

		<div class="mb-2 mb-sm-0">

			<?php

			Builder::generate( [
				[
					'elementType' => 'form',
					'action'      => URL::BUILD( 'notifications' ),
					'method'      => 'get',
					'class'       => 'self-submit',
					'fields'      => [
						[
							'elementType' => 'select',
							'name'        => 'filter',
							'class'       => 'form-select w-auto',
							'selected'    => Request::get( 'filter' ),
							'options'     => [
								[
									'id'   => '',
									'name' => 'All notifications',
								],
								[
									'id'   => 'unseen',
									'name' => 'New notifications',
								],
							],
						],
					],
				],
			] );

			?>

		</div>

		<div>

			<?php

			if( $this->data->notifications->items ){

				Builder::generate( [
					[
						'elementType' => 'form',
						'action'      => URL::BUILD( 'api/notifications/delete' ),
						'method'      => 'post',
						'fields'      => [
							[
								'elementType' => 'button',
								'type'        => 'submit',
								'class'       => 'btn btn-danger',
								'html'        => '
									<span class="btn-icon-left">
										<i class="bi bi-trash"></i>
									</span>
									Delete all notifications
								',
								'name'        => 'deleteAllNotifications',
								'value'       => 'deleteAllNotifications',
								'dataset'     => [
									'confirm' => 'Do you want to delete all notifications?',
								],
							],
						],
					],
				] );

			}

			?>

		</div>

	</div><!-- end .card-header -->
	<div class="card-body">

		<div class="table-responsive">
			<table class="lp-table">
				<thead>
					<tr>
						<th class="text-wrap">Notification title</th>
						<th>Date</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>

					<?php

					if( $this->data->notifications->items ){

						foreach( $this->data->notifications->items as $item ){

							echo '
							<tr class="', ( $item->unseen ? 'table-active' : '' ),'">
								<td>
									',
									(
										$item->sticky
											? '<i class="bi bi-paperclip text-success me-2"></i>'
											: ''
									),
									$item->message,
								'</td>
								<td>
									' , $item->created_at, '
								</td>
								<td>',
									(
										$item->unseen
											? '<span class="badge-status badge-activ">Unread</span>'
											: '<span class="badge-status badge-inactiv">Read</span>'
									),
								'</td>
								<td>
									<div class="d-flex justify-content-end">

										<a href="', $item->url, '" class="btn btn-sm btn-outline-secondary me-1" title="View notification">
											<i class="bi bi-eye"></i>
										</a>';

										Builder::generate( [
											[
												'elementType' => 'form',
												'action'      => URL::BUILD( sprintf( 'api/notifications/delete/%s', $item->id ) ),
												'method'      => 'post',
												'fields'      => [
													[
														'elementType' => 'button',
														'type'        => 'submit',
														'class'       => 'btn btn-sm btn-outline-danger',
														'icon'        => 'bi bi-trash',
														'name'        => 'deleteNotification',
														'value'       => $item->id,
														'dataset'     => [
															'confirm' => 'Do you want to delete this notification?',
														],
													],
												],
											],
										] );

							echo '
									</div>
								</td>
							</tr>';

						}

					} else {

						echo '<tr>
							<td colspan="4" class="text-center py-4">
								Sorry, no results were found.
							</td>
						</tr>';

					}

					?>

				</tbody>
			</table>
		</div><!-- end .table-responsive -->

		<div class="d-flex justify-content-end pt-3">

			<?php

			$paginator = $this->data->notifications->paginator;

			$paginator->setClass( 'ul', 'pagination m-0' );
			$paginator->setPrevEntity( '<i class="bi bi-arrow-left"></i>' );
			$paginator->setNextEntity( '<i class="bi bi-arrow-right"></i>' );
			$paginator->setMidSize( 1 );
			$paginator->setUrl(
				URL::BUILD(
					'notifications',
					[
						'filter' => Request::get( 'filter' ),
					]
				)
			);

			$paginator->render();

			?>

		</div>

	</div><!-- end .card-body -->
</div><!-- end .card -->
