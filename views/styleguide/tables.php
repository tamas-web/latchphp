<div class="card">
	<div class="card-body">

		<div class="card-head">
			<div>
				<h5>Themed table</h5>
				<p>The application table style (<code>.lp-table</code>) with status badges.</p>
			</div>
		</div>

		<div class="table-responsive">
			<table class="lp-table">
				<thead>
					<tr>
						<th>Name</th>
						<th class="d-none d-lg-table-cell">Email</th>
						<th>Role</th>
						<th>Status</th>
						<th class="text-end">Actions</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="u-cell"><span class="avatar">JD</span> <span>Jane Doe</span></div></td>
						<td class="u-email d-none d-lg-table-cell">jane@example.com</td>
						<td><span class="role-tag">Administrator</span></td>
						<td><span class="badge-status badge-activ">Active</span></td>
						<td class="text-end">
							<a href="#" class="btn btn-sm btn-outline-secondary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
							<a href="#" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></a>
						</td>
					</tr>
					<tr>
						<td><div class="u-cell"><span class="avatar">MS</span> <span>Mark Smith</span></div></td>
						<td class="u-email d-none d-lg-table-cell">mark@example.com</td>
						<td><span class="role-tag">Editor</span></td>
						<td><span class="badge-status badge-inactiv">Inactive</span></td>
						<td class="text-end">
							<a href="#" class="btn btn-sm btn-outline-secondary me-1" title="Edit"><i class="bi bi-pencil"></i></a>
							<a href="#" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>
</div>

<div class="card">
	<div class="card-body">

		<div class="card-head">
			<div>
				<h5>Bootstrap tables</h5>
				<p>Default variants: striped, bordered, hover and small.</p>
			</div>
		</div>

		<table class="table table-striped table-hover">
			<thead>
				<tr><th>#</th><th>Product</th><th>Price</th><th>Stock</th></tr>
			</thead>
			<tbody>
				<tr><td>1</td><td>Widget</td><td>$19.00</td><td>120</td></tr>
				<tr><td>2</td><td>Gadget</td><td>$29.00</td><td>64</td></tr>
				<tr><td>3</td><td>Gizmo</td><td>$9.00</td><td>0</td></tr>
			</tbody>
		</table>

		<table class="table table-bordered table-sm mt-4">
			<thead>
				<tr><th>Key</th><th>Value</th></tr>
			</thead>
			<tbody>
				<tr><td>Environment</td><td>production</td></tr>
				<tr><td>Version</td><td>1.0.0</td></tr>
			</tbody>
		</table>

	</div>
</div>
