<div class="card-head">
	<div>
		<h5>Stat widgets</h5>
		<p>Compact metric tiles using <code>.lp-card.stat-card</code>.</p>
	</div>
</div>

<div class="row g-3 mb-4">

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-people"></i> Users</div>
			<div class="value">8,412</div>
			<div class="delta">+5.2% this month</div>
		</div>
	</div>

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-cart"></i> Orders</div>
			<div class="value">1,207</div>
			<div class="delta">+2.1% today</div>
		</div>
	</div>

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-graph-up"></i> Revenue</div>
			<div class="value">$24.8k</div>
			<div class="delta down">-0.8% vs last week</div>
		</div>
	</div>

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-hdd-stack"></i> Storage</div>
			<div class="value">64%</div>
			<div class="delta">+1.4% this week</div>
		</div>
	</div>

</div>

<div class="card-head">
	<div>
		<h5>Icon &amp; progress widgets</h5>
		<p>Card tiles combining an icon, a figure and progress.</p>
	</div>
</div>

<div class="row g-3">

	<div class="col-md-6 col-xl-4">
		<div class="lp-card">
			<div class="d-flex align-items-center gap-3">
				<span class="lp-link-ico"><i class="bi bi-check2-circle"></i></span>
				<div>
					<div class="lp-link-title">Completed tasks</div>
					<div class="lp-link-desc">128 of 150 done</div>
				</div>
			</div>
			<div class="progress mt-3"><div class="progress-bar" style="width:85%"></div></div>
		</div>
	</div>

	<div class="col-md-6 col-xl-4">
		<div class="lp-card">
			<div class="d-flex align-items-center gap-3">
				<span class="lp-link-ico"><i class="bi bi-hdd"></i></span>
				<div>
					<div class="lp-link-title">Disk usage</div>
					<div class="lp-link-desc">320 GB of 500 GB</div>
				</div>
			</div>
			<div class="progress mt-3"><div class="progress-bar" style="width:64%"></div></div>
		</div>
	</div>

	<div class="col-md-6 col-xl-4">
		<div class="lp-card">
			<div class="d-flex align-items-center justify-content-between">
				<div>
					<div class="lp-link-desc">Active now</div>
					<div class="value" style="font-size:28px;font-weight:700">342</div>
				</div>
				<span class="badge-status badge-activ">Live</span>
			</div>
		</div>
	</div>

</div>

<div class="card-head mt-2">
	<div>
		<h5>List &amp; table widgets</h5>
		<p>Notification feed and a recent-activity table.</p>
	</div>
</div>

<div class="row g-3">

	<div class="col-lg-5">
		<div class="lp-card">
			<div class="card-head">
				<div><h5>Notifications</h5></div>
				<span class="badge-status badge-activ">3 new</span>
			</div>
			<div class="d-flex align-items-start gap-3 mb-3">
				<span class="lp-link-ico"><i class="bi bi-person-plus"></i></span>
				<div><div class="fw-semibold">New user registered</div><div class="text-muted" style="font-size:12.5px">2 minutes ago</div></div>
			</div>
			<div class="d-flex align-items-start gap-3 mb-3">
				<span class="lp-link-ico"><i class="bi bi-chat-dots"></i></span>
				<div><div class="fw-semibold">New comment received</div><div class="text-muted" style="font-size:12.5px">1 hour ago</div></div>
			</div>
			<div class="d-flex align-items-start gap-3">
				<span class="lp-link-ico"><i class="bi bi-exclamation-triangle"></i></span>
				<div><div class="fw-semibold">Storage almost full</div><div class="text-muted" style="font-size:12.5px">Yesterday</div></div>
			</div>
		</div>
	</div>

	<div class="col-lg-7">
		<div class="lp-card">
			<div class="card-head"><div><h5>Recent activity</h5><p>Latest account events.</p></div></div>
			<div class="table-responsive">
				<table class="lp-table">
					<thead>
						<tr><th>User</th><th>Event</th><th>Status</th></tr>
					</thead>
					<tbody>
						<tr><td><div class="u-cell"><span class="avatar">JD</span> <span>Jane Doe</span></div></td><td>Signed in</td><td><span class="badge-status badge-activ">OK</span></td></tr>
						<tr><td><div class="u-cell"><span class="avatar">MS</span> <span>Mark Smith</span></div></td><td>Updated profile</td><td><span class="badge-status badge-activ">OK</span></td></tr>
						<tr><td><div class="u-cell"><span class="avatar">AL</span> <span>Ana Lup</span></div></td><td>Failed login</td><td><span class="badge-status badge-inactiv">Blocked</span></td></tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>

</div>
