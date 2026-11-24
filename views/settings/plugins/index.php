<?php

use App\Http\{ URL, Request };

$user = Request::getInstance()->user();

?>

<div class="card">
	<div class="card-header d-sm-flex justify-content-end align-items-center">

		<?php if( $user->can( 'plugins.add' ) ): ?>
		<div>
			<a href="<?php echo URL::BUILD( 'admin/settings/plugins/upload' ); ?>" class="btn btn-primary">
				<span class="btn-icon-left"><i class="bi bi-plus-lg"></i></span>
				Upload plugin
			</a>
		</div>
		<?php endif; ?>

	</div><!-- end .card-header -->
	<div class="card-body">

		<div class="table-responsive">
			<table class="lp-table text-nowrap">
				<thead>
					<tr>
						<th>Name</th>
						<th>Version</th>
						<th>Description</th>
						<th>Status</th>
						<th class="text-end">Actions</th>
					</tr>
				</thead>
				<tbody>

					<?php

					// one tiny POST form per action (hidden plugin name + submit)
					$button = function( string $action, string $name, string $class, string $label, bool $confirm = false ) : string {
						return sprintf(
							'<form method="post" action="%s" class="d-inline me-1">
								%s
								<input type="hidden" name="plugin" value="%s">
								<button type="submit" class="btn btn-sm %s"%s>%s</button>
							</form>',
							URL::BUILD( 'api/settings/plugins/' . $action ),
							nonce_field(),
							esc_html( $name ),
							$class,
							$confirm ? ' data-confirm="Are you sure?"' : '',
							$label
						);
					};

					if( $this->data->plugins ){

						foreach( $this->data->plugins as $name => $plugin ){

							$meta      = $plugin['meta'];
							$installed = $plugin['installed'];
							$active    = $plugin['active'];
							$missing   = $plugin['missing'];

							if( $missing ){
								$badge = '<span class="badge-status badge-inactiv">Missing files</span>';
							} elseif( ! $installed ){
								$badge = '<span class="badge-status badge-inactiv">Not installed</span>';
							} elseif( $active ){
								$badge = '<span class="badge-status badge-activ">Active</span>';
							} else {
								$badge = '<span class="badge-status badge-inactiv">Inactive</span>';
							}

							$actions = '';

							if( $missing ){

								if( $user->can( 'plugins.delete' ) ){
									$actions .= $button( 'uninstall', $name, 'btn-danger', 'Uninstall', true );
								}

							} elseif( ! $installed ){

								if( $user->can( 'plugins.add' ) ){
									$actions .= $button( 'install', $name, 'btn-primary', 'Install' );
								}

							} else {

								if( $active && $user->can( 'plugins.disable' ) ){
									$actions .= $button( 'deactivate', $name, 'btn-warning', 'Deactivate' );
								}

								if( ! $active && $user->can( 'plugins.activate' ) ){
									$actions .= $button( 'activate', $name, 'btn-success', 'Activate' );
								}
								if( $user->can( 'plugins.delete' ) ){
									$actions .= $button( 'uninstall', $name, 'btn-danger', 'Uninstall', true );
								}

							}

							printf(
								'<tr>
									<td>%s</td>
									<td>%s</td>
									<td><div class="lp-desc">%s</div></td>
									<td>%s</td>
									<td class="text-end"><div class="d-flex justify-content-end">%s</div></td>
								</tr>',
								esc_html( $name ),
								esc_html( $meta->version ?? '' ),
								esc_html( $meta->description ?? '' ),
								$badge,
								$actions ?: '&mdash;'
							);

						} // end foreach

					} else {

						echo '<tr><td colspan="5" class="text-center py-4">Sorry, nothing was found</td></tr>';

					}

					?>

				</tbody>
			</table>
		</div><!-- end .table-responsive -->

	</div><!-- end .card-body -->
</div><!-- end .card -->
