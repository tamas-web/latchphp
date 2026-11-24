<?php

use App\Http\URL;

?>
<div class="row">
	<div class="col-xxl-8">

		<div class="card">
			<div class="card-body">

				<form
					class="dynamic-form"
					action="<?php echo URL::BUILD( sprintf( 'api/settings/roles/%u', $this->data->role->id ) ); ?>"
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

								value="<?php echo $this->data->role->name; ?>"

								data-rule="required"
								data-msg-required="Please enter the role name"
							>
						</div>

					</fieldset>

					<fieldset>

						<legend>Permissions</legend>

						<?php

						$all       = $this->data->permissions->all;
						$allocated = $this->data->permissions->allocated->permissionIds;

						// partition the catalog into groups (id_parent = 0) and their children
						$groups   = [];
						$children = [];

						foreach( $all as $permission ){

							if( 0 === $permission->idParent ){
								$groups[ $permission->id ] = $permission;
							} else {
								$children[ $permission->idParent ][] = $permission;
							}

						}

						foreach( $groups as $group ){

							$groupChecked = in_array( $group->id, $allocated, true );

							echo '<div class="acl-group mb-3" data-group="', (int) $group->id, '">';

								// the group/parent permission acts as a "select all" for its children
								printf(
									'<div class="form-check mb-1"><input type="checkbox" class="form-check-input acl-group-toggle" id="perm-%1$u" name="permission[%1$u]" value="%1$u"%2$s><label class="form-check-label fw-bold" for="perm-%1$u">%3$s</label></div>',
									$group->id,
									$groupChecked ? ' checked="checked"' : '',
									$group->label
								);

								foreach( $children[ $group->id ] ?? [] as $permission ){

									$checked = $groupChecked || in_array( $permission->id, $allocated, true );

									printf(
										'<div class="form-check ms-4 mb-1"><input type="checkbox" class="form-check-input acl-child" id="perm-%1$u" name="permission[%1$u]" value="%1$u"%2$s%3$s><label class="form-check-label" for="perm-%1$u">%4$s</label></div>',
										$permission->id,
										$checked ? ' checked="checked"' : '',
										$groupChecked ? ' disabled="disabled"' : '',
										$permission->label
									);

								}

							echo '</div>';

						}

						?>

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
								Update
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

			</div><!-- end .card-body -->
		</div><!-- end .card -->

	</div><!-- end .col -->
</div><!-- end .row -->
