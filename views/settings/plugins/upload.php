<?php

use App\Http\URL;

?>

<div class="card">
	<div class="card-body">

		<div class="alert alert-info">
			Upload a <strong>.zip</strong> archive containing a single plugin folder at the root
			(with <code>meta.json</code> &mdash; <em>name, version, description, loader</em> &mdash; and the loader class).
			The package is validated before installation; if it is invalid, you receive a message with the reason.
		</div>

		<form method="post" action="<?php echo URL::BUILD( 'api/settings/plugins/upload' ); ?>" enctype="multipart/form-data">
			<?php echo nonce_field(); ?>

			<div class="mb-3">
				<input type="file" name="plugin_zip" accept=".zip" class="form-control" required>
			</div>

			<div class="d-sm-flex align-items-center justify-content-between">
				<div class="mb-2 mb-sm-0">
					<button type="submit" class="btn btn-primary">
						<span class="btn-icon-left"><i class="bi bi-plus-lg"></i></span>
						Upload and install
					</button>
				</div>
				<div>
					<a href="<?php echo URL::BUILD( 'admin/settings/plugins' ); ?>" class="btn btn-outline-light">
						<span class="btn-icon-left"><i class="bi bi-arrow-left"></i></span>
						Back
					</a>
				</div>
			</div>

		</form>

	</div>
</div>
