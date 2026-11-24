<?php

use App\Config;
use App\Html\Builder;
use App\Http\{ URL, Request };

$phoneEnabled  = (bool) Config::get( 'settings.users.phone.enabled' );
$phoneRequired = (bool) Config::get( 'settings.users.phone.required' );

?>

<div class="row">
	<div class="col-xxl-8">

		<div class="card">
			<div class="card-body">

				<form
					class="dynamic-form"
					action="<?php echo URL::BUILD( 'api/users/add' ); ?>"
					method="post"
					enctype="multipart/form-data"
					spellcheck="false"
				>

					<fieldset>

						<legend>User account details</legend>

						<?php if( $this->data->canGrantRole ): ?>
						<div class="mb-3">
							<label
								class="form-label required"
								for="role"
							>
								Role
							</label>

							<?php

							Builder::generate( [

								[
									'elementType' => 'select',
									'id'          => 'role',
									'name'        => 'id_role',
									'class'       => 'form-select',
									'options'     => $this->data->ranks,
									'dataset'     => [
										'rule'         => 'required',
										'msg-required' => 'Please select a role',
									],
								], // select#role

							] );

							?>

						</div>
						<?php endif; ?>

						<div class="mb-3">
							<label
								class="form-label required"
								for="password"
							>
								Password
							</label>
							<input
								class="form-control"

								type="text"
								id="password"
								name="password"

								autocomplete="new-password"

								data-rule="required|min:8|regexp"
								data-regexp='^(?=.*\d)(?=.*[A-Za-z])(?=.*[!@#$%^&*(),.?":{}|<>])(?!.*\s).*$'
								data-msg-required="Please enter a password"
								data-msg-min="The password must be at least 8 characters long"
								data-msg-regexp="The password must contain at least one letter, one number and one special character !@#$%^&*"
											>
						</div>

						<div class="mb-3">

							<button type="button" class="btn btn-primary light btn-sm app-generate-password" data-target="#password">
								<span class="btn-icon-left">
									<i class="bi bi-key"></i>
								</span>
								Generate a password
							</button>

						</div>


						<?php if( Request::getInstance()->user()->isSuperAdmin() ): ?>
						<div class="switch-row mb-3">
							<div class="switch-row-text">
								<label class="title" for="user_super_admin">Super administrator</label>
								<div class="sub">Full access (bypasses account permissions).</div>
							</div>
							<div class="form-check form-switch m-0">
								<input class="form-check-input" type="checkbox" role="switch" id="user_super_admin" name="user_super_admin" value="1">
							</div>
						</div>
						<?php endif; ?>
					</fieldset>

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

								data-rule="required"
								data-msg-required="Please enter the first name"
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

								data-rule="required"
								data-msg-required="Please enter the last name"
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

								data-rule="required|email"
								data-msg-required="Please enter an email address"
								data-msg-email="Please enter a valid email address"
							>
						</div>

					</fieldset>

					<div class="d-sm-flex align-items-center justify-content-between">
						<div class="mb-2 mb-sm-0">

							<button
								class="btn btn-primary"

								type="submit"
								id="newAccount"
								name="newAccount"
								value="newAccount"
							>
								<span class="btn-icon-left">
									<i class="bi bi-floppy"></i>
								</span>
								Add user
							</button>

						</div>
						<div>

							<a href="<?php echo URL::BUILD( 'admin/users' ); ?>" class="btn btn-outline-light">
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
