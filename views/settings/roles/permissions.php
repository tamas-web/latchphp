<?php

use App\Http\{ Request, URL };

$permissions = $this->data->permissions;
$paginator   = $permissions->paginator;
$limit       = (int) $permissions->limit;
$total       = $paginator->getTotal();
$page        = $paginator->getCurrentPage();
$start       = $total ? ( ( $page - 1 ) * $limit ) + 1 : 0;
$end         = min( $page * $limit, $total );

// current filters ( for display + paginator links )
$q = Request::get( 'q', 'strip_tags' );

$paginator->setClass( 'ul', 'pagination m-0' );
$paginator->setUrl(
	URL::BUILD( 'admin/settings/roles-permissions/permissions', [
		'q'     => $q,
		'limit' => $limit,
	] )
);

?>

<div class="card">
	<form method="get" action="<?php echo URL::BUILD( 'admin/settings/roles-permissions/permissions' ); ?>" class="self-submit">
		<div class="card-header">
			<div class="row g-2 align-items-center">
				<div class="col-12 col-md-4">
					<input type="search" name="q" class="form-control" placeholder="Search permissions..." aria-label="Search permissions" value="<?php echo esc_html( $q ); ?>">
				</div>
			</div>
		</div><!-- end .card-header -->
		<div class="card-body">

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
							<th>Name</th>
							<th>Code</th>
						</tr>
					</thead>
					<tbody>

						<?php

						if( $permissions->items ){

							foreach( $permissions->items as $item ){

								echo '
								<tr>';

									echo '<td>',
											$item->label,
										'</td>';

									echo '<td><code>',
											$item->name,
										'</code></td>';

								echo '</tr>';

							} // end foreach

						} else {

							echo '<tr><td colspan="2" class="text-center py-4">Sorry, nothing was found</td></tr>';

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

		</div><!-- end .card-body -->
	</form>
</div><!-- end .card -->
