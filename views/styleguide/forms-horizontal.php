<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Inputs with labels beside</h5><p>Label / field columns on the same row.</p></div></div>

		<form onsubmit="return false">

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-name">Full name</label>
				<div class="col-sm-9"><div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span>
					<input type="text" class="form-control" id="hf-name" placeholder="Jane Doe"></div></div>
			</div>

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-email">Email</label>
				<div class="col-sm-9"><div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span>
					<input type="email" class="form-control" id="hf-email" placeholder="jane@example.com"></div></div>
			</div>

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-pass">Password</label>
				<div class="col-sm-9"><div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>
					<input type="password" class="form-control" id="hf-pass" placeholder="********"></div></div>
			</div>

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-phone">Phone</label>
				<div class="col-sm-9"><div class="input-group"><span class="input-group-text"><i class="bi bi-telephone"></i></span>
					<input type="tel" class="form-control" id="hf-phone" placeholder="+40 700 000 000"></div></div>
			</div>

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-date">Date</label>
				<div class="col-sm-9"><input type="date" class="form-control" id="hf-date"></div>
			</div>

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-role">Role</label>
				<div class="col-sm-9">
					<select class="form-select" id="hf-role">
						<option selected>Administrator</option>
						<option>Editor</option>
						<option>Viewer</option>
					</select>
				</div>
			</div>

			<div class="row mb-3">
				<label class="col-sm-3 col-form-label" for="hf-bio">Biography</label>
				<div class="col-sm-9"><textarea class="form-control" id="hf-bio" rows="3" placeholder="A few words..."></textarea></div>
			</div>

			<div class="row mb-3">
				<span class="col-sm-3 col-form-label">Notifications</span>
				<div class="col-sm-9">
					<div class="switch-row">
						<div class="switch-row-text">
							<label class="title" for="hf-switch">Email notifications</label>
							<div class="sub">Send a message when something needs attention.</div>
						</div>
						<div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" role="switch" id="hf-switch" checked></div>
					</div>
				</div>
			</div>

			<div class="row mb-3">
				<span class="col-sm-3 col-form-label pt-0">Visibility</span>
				<div class="col-sm-9">
					<div class="form-check"><input class="form-check-input" type="radio" name="hf-radio" id="hf-r1" checked><label class="form-check-label" for="hf-r1">Public</label></div>
					<div class="form-check"><input class="form-check-input" type="radio" name="hf-radio" id="hf-r2"><label class="form-check-label" for="hf-r2">Private</label></div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-9 offset-sm-3 d-flex gap-2">
					<button type="submit" class="btn btn-primary">Save</button>
					<button type="button" class="btn btn-outline-secondary">Cancel</button>
				</div>
			</div>

		</form>
	</div>
</div>
