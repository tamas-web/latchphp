<?php

use App\Http\URL;

$backups = $this->data->backups;

?>

<div class="card">
	<div class="card-body">

		<?php if( $backups ){ ?>

			<div class="table-responsive">
				<table class="lp-table text-nowrap">
					<thead>
						<tr>
							<th>Archive</th>
							<th>Created</th>
							<th>Size</th>
							<th class="text-end">Actions</th>
						</tr>
					</thead>
					<tbody>

						<?php foreach( $backups as $backup ){ ?>

							<tr>
								<td><?php echo esc_html( $backup->name ); ?></td>
								<td><?php echo esc_html( $backup->time ); ?></td>
								<td><?php echo esc_html( $backup->size ); ?></td>
								<td class="text-end">
									<a class="btn btn-sm btn-outline-secondary" href="<?php echo esc_url( URL::BUILD( 'admin/system/backup/download/' . $backup->name ) ); ?>">
										<span class="btn-icon-left"><i class="bi bi-download"></i></span>
										Download
									</a>
								</td>
							</tr>

						<?php } ?>

					</tbody>
				</table>
			</div>

		<?php } else { ?>

			<p class="text-muted m-0">No backups found yet. Backups are produced by the backup service ( CLI / cron ).</p>

		<?php } ?>

	</div>
</div>
