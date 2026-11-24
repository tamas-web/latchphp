<?php

use App\Config;
use App\Events\Emitter;
use App\Http\URL;

?>

<div class="row">
	<div class="col-xxl-8">

		<div class="card">
			<div class="card-body">

				<form
					class="dynamic-form"
					action="<?php echo URL::BUILD( 'api/settings/app' ); ?>"
					method="post"
					spellcheck="false"
				>

					<fieldset>

						<legend>Users</legend>

						<div class="switch-row mb-3">
							<div class="switch-row-text">
								<label class="title" for="phone_enabled">Phone field enabled</label>
								<div class="sub">Show the phone field in account forms.</div>
							</div>
							<div class="form-check form-switch m-0">
								<input class="form-check-input" type="checkbox" role="switch" id="phone_enabled" name="phone_enabled" value="1" <?php echo Config::get( 'settings.users.phone.enabled' ) ? 'checked' : ''; ?>>
							</div>
						</div>

						<div class="switch-row mb-3">
							<div class="switch-row-text">
								<label class="title" for="phone_required">Phone required</label>
								<div class="sub">Require a phone number when the field is enabled.</div>
							</div>
							<div class="form-check form-switch m-0">
								<input class="form-check-input" type="checkbox" role="switch" id="phone_required" name="phone_required" value="1" <?php echo Config::get( 'settings.users.phone.required' ) ? 'checked' : ''; ?>>
							</div>
						</div>

					</fieldset>

					<fieldset>

						<legend>General</legend>

						<div class="mb-3">
							<label class="form-label" for="pagination_limit">Default rows per page</label>
							<select class="form-select" id="pagination_limit" name="pagination_limit">
								<?php foreach( [ 25, 50, 100 ] as $opt ): ?>
								<option value="<?php echo $opt; ?>"<?php echo (int) Config::get( 'settings.app.pagination.limit' ) === $opt ? ' selected' : ''; ?>><?php echo $opt; ?></option>
								<?php endforeach; ?>
							</select>
						</div>

					</fieldset>

					<?php Emitter::broadcast( 'app_settings_form', $this ); ?>

					<div class="d-sm-flex align-items-center justify-content-between">
						<div class="mb-2 mb-sm-0">
							<button class="btn btn-primary" type="submit" name="updateSettings" value="updateSettings">
								<span class="btn-icon-left"><i class="bi bi-floppy"></i></span>
								Save
							</button>
						</div>
						<div>
							<a href="<?php echo URL::BUILD( 'admin' ); ?>" class="btn btn-outline-light">
								<span class="btn-icon-left"><i class="bi bi-arrow-left"></i></span>
								Back
							</a>
						</div>
					</div>

				</form>

			</div><!-- end .card-body -->
		</div><!-- end .card -->

	</div><!-- end .col -->
</div><!-- end .row -->
