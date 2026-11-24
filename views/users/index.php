<?php

use App\Config;
use App\Html\Builder;
use App\Http\{ Request, URL };
use App\Events\Emitter;

$currentUser  = Request::getInstance()->user();
$phoneEnabled = (bool) Config::get( 'settings.users.phone.enabled' );

$users     = $this->data->users;
$paginator = $users->paginator;
$limit     = (int) $users->limit;
$total     = $paginator->getTotal();
$page      = $paginator->getCurrentPage();
$start     = $total ? ( ( $page - 1 ) * $limit ) + 1 : 0;
$end       = min( $page * $limit, $total );

// current filters ( for display + paginator links )
$q      = Request::get( 'q', 'strip_tags' );
$role   = Request::get( 'role', fn( $role ) => abs( intval( $role ) ) );
$status = Request::get( 'status', 'strip_tags' );

// role footer-filter options ( blank first, then every role )
$roleOpts = [
	[
		'id'   => '',
		'name' => 'All roles',
	],
];

foreach( $this->data->roles as $r ){
	$roleOpts[] = [
		'id'   => $r->id,
		'name' => $r->name,
	];
}

$paginator->setClass( 'ul', 'pagination m-0' );
$paginator->setUrl(
	URL::BUILD( 'admin/users', [
		'q'      => $q,
		'role'   => $role,
		'status' => $status,
		'limit'  => $limit,
	] )
);

?>

<div class="card">
	<div class="card-header d-sm-flex justify-content-between align-items-center">

		<div>

			<?php

			Builder::generate( [
				[
					'elementType' => 'form',
					'action'      => URL::BUILD( 'admin/users' ),
					'method'      => 'get',
					'class'       => 'mb-2 self-submit',
					'fields'      => [
						[
							'elementType' => 'input::icheck',
							'name'        => 'showActive',
							'value'       => 'showActive',
							'text'        => 'Show only active users',
							'sub'         => 'Hide disabled accounts from the list.',
							'checked'     => $this->data->showActiveOnly,
						], // input#showActive
						[
							'elementType' => 'input',
							'type'        => 'hidden',
							'name'        => 'form',
							'value'       => 'update',
						],
						// preserve the table filters when toggling the switch
						[
							'elementType' => 'input',
							'type'        => 'hidden',
							'name'        => 'q',
							'value'       => $q,
						],
						[
							'elementType' => 'input',
							'type'        => 'hidden',
							'name'        => 'role',
							'value'       => $role,
						],
						[
							'elementType' => 'input',
							'type'        => 'hidden',
							'name'        => 'status',
							'value'       => $status,
						],
						[
							'elementType' => 'input',
							'type'        => 'hidden',
							'name'        => 'limit',
							'value'       => $limit,
						],
					],
				],
			] );

			?>

		</div>

		<div>
			<a href="<?php echo URL::BUILD( 'admin/users/add' ); ?>" class="btn btn-primary">
				<span class="btn-icon-left"><i class="bi bi-person-plus"></i></span>
				Add new
			</a>
		</div>

	</div><!-- end .card-header -->
	<div class="card-body">

		<form method="get" action="<?php echo URL::BUILD( 'admin/users' ); ?>" class="self-submit">

			<div class="row g-2 align-items-center mb-3">

				<div class="col-12 col-sm-6 col-md-4">
					<input type="search" name="q" class="form-control" placeholder="Search users..." aria-label="Search users" value="<?php echo esc_html( $q ); ?>">
				</div>

				<div class="col-6 col-md-3">
					<?php
					Builder::generate( [
						[
							'elementType' => 'select',
							'name'        => 'role',
							'class'       => 'form-select',
							'selected'    => $role,
							'options'     => $roleOpts,
						],
					] );
					?>
				</div>

				<div class="col-6 col-md-3">
					<?php
					Builder::generate( [
						[
							'elementType' => 'select',
							'name'        => 'status',
							'class'       => 'form-select',
							'selected'    => $status,
							'options'     => [
								[
									'id'   => '',
									'name' => 'All statuses',
								],
								[
									'id'   => 'active',
									'name' => 'Active',
								],
								[
									'id'   => 'inactive',
									'name' => 'Inactive',
								],
							],
						],
					] );
					?>
				</div>

			</div>

			<div class="d-xl-flex justify-content-between align-items-center mb-2">
				<div class="dataTables_info mb-2 mb-xl-0">
					<?php
					echo $total
						? sprintf( 'Showing %d to %d of %d entries', $start, $end, $total )
						: 'Showing 0 to 0 of 0 entries';
					?>
				</div>
				<?php $paginator->render(); ?>
			</div>

			<div class="table-responsive">
				<table class="lp-table text-nowrap">
					<thead>
						<tr>
							<th>First and last name</th>
							<th class="d-none d-lg-table-cell">Email</th>
							<th>Role</th>
							<?php if( $phoneEnabled ): ?><th class="d-none d-lg-table-cell">Phone</th><?php endif; ?>
							<th>Status</th>
							<th class="text-end">Actions</th>
						</tr>
					</thead>
					<tbody>

						<?php

						if( $users->items ){

							foreach( $users->items as $item ){

								$initials = strtoupper( mb_substr( (string) $item->name, 0, 1 ) . mb_substr( (string) $item->surname, 0, 1 ) );

								echo '
								<tr>';

									echo '<td data-title="Name">
											<div class="u-cell">
												<span class="avatar">', esc_html( $initials ), '</span>
												<span>', $item->name, ' ', $item->surname, '</span>
											</div>
										</td>';

									echo '<td data-title="Email" class="u-email d-none d-lg-table-cell">
											', $item->email,'
										</td>';

									echo '<td data-title="Level">',
											(
												$item->options->hasOption( self::ACCOUNT_ADMIN )
												? '<span class="badge text-bg-primary">Super administrator</span>'
												: '<span class="role-tag">' . $item->userRank . '</span>'
											),
										'</td>';

									if( $phoneEnabled ){
										echo '<td class="d-none d-lg-table-cell">
												', $item->phone,'
											</td>';
									}

									echo '<td data-title="Status">',
										(
											$item->options->hasOption( self::ACCOUNT_ENABLED )
											? '<span class="badge-status badge-activ">Active</span>'
											: '<span class="badge-status badge-inactiv">Inactive</span>'
										),
										'</td>';

									echo '<td data-title="Actions" class="text-end">';

										if(
											$currentUser->can( 'users.edit' ) &&
											(
												! $item->options->hasOption( self::ACCOUNT_ADMIN ) ||
												$currentUser->isSuperAdmin()
											)
										){

											echo '<a href="', URL::BUILD( sprintf( 'admin/users/%s', $item->id ) ), '" class="btn btn-sm btn-outline-secondary me-1" title="Edit">
													<i class="bi bi-pencil"></i>
												</a>';

										}

										if(
											$currentUser->can( 'users.delete' ) &&
											$item->id !== $currentUser->id &&
											(
												! $item->options->hasOption( self::ACCOUNT_ADMIN ) ||
												$currentUser->isSuperAdmin()
											)
										){

											echo '<a href="', URL::BUILD( sprintf( 'admin/users/%s/delete', $item->id ) ), '" class="btn btn-sm btn-outline-danger" title="Delete">
													<i class="bi bi-trash"></i>
												</a>';

										}

									echo '</td>';

								echo '</tr>';

							} // end foreach

						} else {

							echo '<tr><td colspan="', ( $phoneEnabled ? 6 : 5 ), '" class="text-center py-4">Sorry, nothing was found</td></tr>';

						}

						?>

					</tbody>
				</table>
			</div><!-- end .table-responsive -->

			<div class="d-xl-flex justify-content-between align-items-center mt-2">
				<label class="d-flex align-items-center gap-2 mb-2 mb-xl-0">
					Show
					<select name="limit" class="form-select w-auto">
						<?php foreach( [ 25, 50, 100 ] as $opt ): ?>
							<option value="<?php echo $opt; ?>"<?php echo $opt === $limit ? ' selected' : ''; ?>><?php echo $opt; ?></option>
						<?php endforeach; ?>
					</select>
					entries per page
				</label>
				<?php $paginator->render(); ?>
			</div>

		</form>

	</div><!-- end .card-body -->
</div><!-- end .card -->
