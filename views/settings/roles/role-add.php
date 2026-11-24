<?php

use App\Http\URL;

?>
<div class="row">
	<div class="col-xxl-8">

		<div class="card">
			<div class="card-body">

				<form
					class="dynamic-form"
					action="<?php echo URL::BUILD( 'api/settings/roles/add' ); ?>"
					method="post"
					enctype="multipart/form-data"
					spellcheck="false"
				>

					<fieldset>

						<legend>Details</legend>

						<div class="mb-3">
							<label
								class="form-label required"
								for="name"
							>
								Name
							</label>
							<input
								class="form-control"

								type="text"
								id="name"
								name="name"

								maxlength="255"

								data-rule="required"
								data-msg-required="Please enter the role name"
							>
						</div>

					</fieldset>

					<div class="d-sm-flex align-items-center justify-content-between">
						<div class="mb-2 mb-sm-0">

							<button
								class="btn btn-primary"

								type="submit"
								id="doAction"
								name="doAction"
								value="doAction"
							>
								<span class="btn-icon-left">
									<i class="bi bi-floppy"></i>
								</span>
								Add role
							</button>

						</div>
						<div>

							<a href="<?php echo URL::BUILD( 'admin/settings/roles-permissions/roles' ); ?>" class="btn btn-outline-light">
								<span class="btn-icon-left">
									<i class="bi bi-arrow-left"></i>
								</span>
								Back
							</a>

						</div>
					</div>

				</form>

			</div><!-- end .card-body -->
		</div><!-- end .card -->

	</div><!-- end .col -->
</div><!-- end .row -->