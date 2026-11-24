<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Inputs with labels above</h5><p>Common input types, each with an icon input-group.</p></div></div>

		<form onsubmit="return false">
			<div class="row">

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-text">Text</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-person"></i></span>
						<input type="text" class="form-control" id="ss-text" placeholder="Jane Doe"></div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-email">Email</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-envelope"></i></span>
						<input type="email" class="form-control" id="ss-email" placeholder="jane@example.com"></div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-pass">Password</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-lock"></i></span>
						<input type="password" class="form-control" id="ss-pass" placeholder="********"></div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-phone">Phone</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-telephone"></i></span>
						<input type="tel" class="form-control" id="ss-phone" placeholder="+40 700 000 000"></div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-url">URL</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
						<input type="url" class="form-control" id="ss-url" placeholder="https://example.com"></div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-num">Number</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-hash"></i></span>
						<input type="number" class="form-control" id="ss-num" value="42"></div>
				</div>

				<div class="col-md-4 mb-3">
					<label class="form-label" for="ss-date">Date</label>
					<input type="date" class="form-control" id="ss-date">
				</div>

				<div class="col-md-4 mb-3">
					<label class="form-label" for="ss-time">Time</label>
					<input type="time" class="form-control" id="ss-time">
				</div>

				<div class="col-md-4 mb-3">
					<label class="form-label" for="ss-search">Search</label>
					<div class="input-group"><span class="input-group-text"><i class="bi bi-search"></i></span>
						<input type="search" class="form-control" id="ss-search" placeholder="Search..."></div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-select">Select</label>
					<select class="form-select" id="ss-select">
						<option selected>Administrator</option>
						<option>Editor</option>
						<option>Viewer</option>
					</select>
				</div>

				<div class="col-md-3 mb-3">
					<label class="form-label" for="ss-color">Colour</label>
					<input type="color" class="form-control form-control-color" id="ss-color" value="#b2da62">
				</div>

				<div class="col-md-3 mb-3">
					<label class="form-label" for="ss-file">File</label>
					<input type="file" class="form-control" id="ss-file">
				</div>

				<div class="col-12 mb-3">
					<label class="form-label" for="ss-bio">Textarea</label>
					<textarea class="form-control" id="ss-bio" rows="3" placeholder="A few words..."></textarea>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-range">Range</label>
					<input type="range" class="form-range" id="ss-range">
				</div>

			</div>
		</form>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Choices</h5><p>Switches, checkboxes and radios.</p></div></div>

		<div class="mb-3">
			<div class="switch-row">
				<div class="switch-row-text">
					<label class="title" for="ss-switch">Email notifications</label>
					<div class="sub">Send a message when something needs attention.</div>
				</div>
				<div class="form-check form-switch m-0"><input class="form-check-input" type="checkbox" role="switch" id="ss-switch" checked></div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-6">
				<div class="form-check"><input class="form-check-input" type="checkbox" id="ss-c1" checked><label class="form-check-label" for="ss-c1">Checked checkbox</label></div>
				<div class="form-check"><input class="form-check-input" type="checkbox" id="ss-c2"><label class="form-check-label" for="ss-c2">Unchecked checkbox</label></div>
				<div class="form-check"><input class="form-check-input" type="checkbox" id="ss-c3" disabled><label class="form-check-label" for="ss-c3">Disabled checkbox</label></div>
			</div>
			<div class="col-md-6">
				<div class="form-check"><input class="form-check-input" type="radio" name="ss-r" id="ss-r1" checked><label class="form-check-label" for="ss-r1">Option one</label></div>
				<div class="form-check"><input class="form-check-input" type="radio" name="ss-r" id="ss-r2"><label class="form-check-label" for="ss-r2">Option two</label></div>
				<div class="form-check"><input class="form-check-input" type="radio" name="ss-r" id="ss-r3" disabled><label class="form-check-label" for="ss-r3">Disabled option</label></div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Validation states</h5></div></div>

		<form onsubmit="return false">
			<div class="row">
				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-valid">Valid</label>
					<input type="text" class="form-control is-valid" id="ss-valid" value="Looks good">
					<div class="valid-feedback">Looks good.</div>
				</div>
				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-invalid">Invalid</label>
					<input type="text" class="form-control is-invalid" id="ss-invalid" value="Wrong">
					<div class="invalid-feedback">This field is required.</div>
				</div>
			</div>
			<div class="d-flex gap-2">
				<button type="submit" class="btn btn-primary">Save</button>
				<button type="button" class="btn btn-outline-secondary">Cancel</button>
			</div>
		</form>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>More controls</h5><p>Floating labels, input-group buttons, sizing, help &amp; states.</p></div></div>

		<form onsubmit="return false">
			<div class="row">

				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<input type="email" class="form-control" id="ss-float-email" placeholder="name@example.com">
						<label for="ss-float-email">Email address</label>
					</div>
				</div>

				<div class="col-md-6 mb-3">
					<div class="form-floating">
						<select class="form-select" id="ss-float-select">
							<option selected>Administrator</option>
							<option>Editor</option>
							<option>Viewer</option>
						</select>
						<label for="ss-float-select">Role</label>
					</div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label">Search with button</label>
					<div class="input-group">
						<input type="search" class="form-control" placeholder="Search...">
						<button class="btn btn-primary" type="button"><i class="bi bi-search"></i></button>
					</div>
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label">With dropdown</label>
					<div class="input-group">
						<button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">Action</button>
						<ul class="dropdown-menu">
							<li><a class="dropdown-item" href="#">Action</a></li>
							<li><a class="dropdown-item" href="#">Another</a></li>
						</ul>
						<input type="text" class="form-control" placeholder="Value">
					</div>
				</div>

				<div class="col-12 mb-3">
					<label class="form-label">Input sizing</label>
					<input type="text" class="form-control form-control-lg mb-2" placeholder="Large">
					<input type="text" class="form-control mb-2" placeholder="Default">
					<input type="text" class="form-control form-control-sm" placeholder="Small">
				</div>

				<div class="col-md-6 mb-3">
					<label class="form-label" for="ss-help">With help text</label>
					<input type="text" class="form-control" id="ss-help" aria-describedby="ss-help-text">
					<div id="ss-help-text" class="form-text">We'll never share this with anyone else.</div>
				</div>

				<div class="col-md-3 mb-3">
					<label class="form-label">Disabled</label>
					<input type="text" class="form-control" value="Disabled" disabled>
				</div>

				<div class="col-md-3 mb-3">
					<label class="form-label">Read-only</label>
					<input type="text" class="form-control" value="Read-only" readonly>
				</div>

			</div>
		</form>
	</div>
</div>
