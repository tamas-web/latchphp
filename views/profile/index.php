<?php

use App\Config;
use App\Http\URL;

$phoneEnabled  = (bool) Config::get( 'settings.users.phone.enabled' );
$phoneRequired = (bool) Config::get( 'settings.users.phone.required' );

?>

<div class="row">
	<div class="col-xxl-8">

		<div class="card">
			<div class="card-body">

				<form
					class="dynamic-form"
					action="<?php echo URL::BUILD( 'api/profile' ); ?>"
					method="post"
					enctype="multipart/form-data"
					spellcheck="false"
				>

					<fieldset>

						<legend>Personal details</legend>

						<div class="mb-3">
							<label
								class="form-label required"
								for="name"
							>
								First name
							</label>
							<input
								class="form-control"

								type="text"
								id="name"
								name="name"
								value="<?php echo $this->data->user->name; ?>"

								data-rule="required"
								data-msg-required="Please enter your first name"
							>
						</div>

						<div class="mb-3">
							<label
								class="form-label required"
								for="surname"
							>
								Last name
							</label>
							<input
								class="form-control"

								type="text"
								id="surname"
								name="surname"
								value="<?php echo $this->data->user->surname; ?>"

								data-rule="required"
								data-msg-required="Please enter your last name"
							>
						</div>

						<?php if( $phoneEnabled ): ?>
						<div class="mb-3">
							<label
								class="form-label <?php echo $phoneRequired ? 'required' : ''; ?>"
								for="phone"
							>
								Phone number
							</label>
							<input
								class="form-control"

								type="tel"
								id="phone"
								name="phone"
								value="<?php echo $this->data->user->phone; ?>"

								data-rule="<?php echo $phoneRequired ? 'required|phone' : 'phone'; ?>"
								<?php if( $phoneRequired ): ?>
								data-msg-required="Please enter a phone number"
								<?php endif; ?>
								data-msg-phone="The phone number is not valid"
							>
						</div>
						<?php endif; ?>

						<div class="mb-3">
							<label
								class="form-label required"
								for="email"
							>
								E-mail
							</label>
							<input
								class="form-control"

								type="email"
								id="email"
								name="email"
								value="<?php echo $this->data->user->email; ?>"

								data-rule="required|email"
								data-msg-required="Please enter an email address"
								data-msg-email="Please enter a valid email address"
							>
						</div>

					</fieldset>

					<fieldset>

						<legend>Change password</legend>

						<p><strong>Note: Fill in only if you want to change the current password!</strong></p>

						<div class="mb-3">
							<label
								class="form-label required"
								for="password"
							>
								New password
							</label>
							<div class="input-group mb-3">
								<input
									class="form-control password-fields"

									type="text"
									id="password"
									name="password"

									autocomplete="off"

									data-rule="min:8|regexp"
									data-regexp='^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\s).*$'
									data-msg-min="The password must be at least 8 characters long"
									data-msg-regexp="The password must contain at least one letter, one number and one special character !@#$%^&*"
								>
								<span class="input-group-text">
									<i class="bi bi-lock"></i>
								</span>
							</div>
						</div>

						<div class="mb-3">

							<button type="button" class="btn btn-primary light btn-sm app-generate-password" data-target=".password-fields">
								<span class="btn-icon-left">
									<i class="bi bi-key"></i>
								</span>
								Generate a password
							</button>

						</div>

						<div class="mb-3">
							<label
								class="form-label required"
								for="password_confirm"
							>
								Confirm new password
							</label>
							<div class="input-group mb-3">
								<input
									class="form-control password-fields"

									type="text"
									id="password_confirm"
									name="password_confirm"

									data-rule="matches:password"
									data-msg-matches="The passwords do not match"

									autocomplete="off"

								>
								<span class="input-group-text">
									<i class="bi bi-lock"></i>
								</span>
							</div>
						</div>

						<div class="switch-row mb-3">
							<div class="switch-row-text">
								<label class="title" for="deauthorizeAllDevices">Log out all devices</label>
								<div class="sub">Close active sessions on other devices.</div>
							</div>
							<div class="form-check form-switch m-0">
								<input class="form-check-input" type="checkbox" role="switch" id="deauthorizeAllDevices" name="deauthorizeAllDevices" value="deauthorizeAllDevices">
							</div>
						</div>

					</fieldset>

					<div class="d-sm-flex align-items-center justify-content-between">
						<div class="mb-2 mb-sm-0">

							<button
								class="btn btn-primary"

								type="submit"
								id="updateProfile"
								name="updateProfile"
								value="updateProfile"
							>
								<span class="btn-icon-left">
									<i class="bi bi-floppy"></i>
								</span>
								Save changes
							</button>

						</div>
						<div>

							<a href="<?php echo URL::BUILD(); ?>" class="btn btn-outline-light">
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
