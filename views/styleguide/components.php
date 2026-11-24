<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Buttons</h5><p>Variants, outlines, sizes and states.</p></div></div>

		<div class="d-flex flex-wrap gap-2 mb-3">
			<button class="btn btn-primary">Primary</button>
			<button class="btn btn-outline-secondary">Secondary</button>
			<button class="btn btn-success">Success</button>
			<button class="btn btn-danger">Danger</button>
			<button class="btn btn-warning">Warning</button>
			<button class="btn btn-info">Info</button>
			<button class="btn btn-link">Link</button>
		</div>

		<div class="d-flex flex-wrap gap-2 mb-3">
			<button class="btn btn-outline-primary">Primary</button>
			<button class="btn btn-outline-secondary">Secondary</button>
			<button class="btn btn-outline-success">Success</button>
			<button class="btn btn-outline-danger">Danger</button>
			<button class="btn btn-outline-warning">Warning</button>
			<button class="btn btn-outline-info">Info</button>
		</div>

		<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
			<button class="btn btn-primary btn-sm">Small</button>
			<button class="btn btn-primary">Default</button>
			<button class="btn btn-primary btn-lg">Large</button>
			<button class="btn btn-primary" disabled>Disabled</button>
			<button class="btn btn-primary"><i class="bi bi-plus-lg"></i> With icon</button>
		</div>

		<div class="btn-group" role="group">
			<button class="btn btn-outline-secondary">Left</button>
			<button class="btn btn-outline-secondary">Middle</button>
			<button class="btn btn-outline-secondary">Right</button>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Alerts</h5><p>Application alert and Bootstrap contextual alerts.</p></div></div>

		<div class="lp-alert mb-3">
			<span class="ico"><i class="bi bi-info-circle"></i></span>
			<div><strong>Heads up.</strong> This is the themed application alert.</div>
		</div>

		<div class="alert alert-success" role="alert">A success alert.</div>
		<div class="alert alert-danger" role="alert">A danger alert.</div>
		<div class="alert alert-warning mb-0" role="alert">A warning alert.</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Badges</h5><p>Status pills and Bootstrap badges.</p></div></div>

		<div class="d-flex flex-wrap align-items-center gap-2">
			<span class="badge-status badge-activ">Active</span>
			<span class="badge-status badge-inactiv">Inactive</span>
			<span class="badge text-bg-primary">Primary</span>
			<span class="badge text-bg-success">Success</span>
			<span class="badge text-bg-danger">Danger</span>
			<span class="badge rounded-pill text-bg-secondary">Pill</span>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Dropdown, modal &amp; toast</h5><p>Interactive components (Bootstrap JS).</p></div></div>

		<div class="d-flex flex-wrap gap-2">
			<div class="dropdown">
				<button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Dropdown</button>
				<ul class="dropdown-menu">
					<li><h6 class="dropdown-header">Header</h6></li>
					<li><a class="dropdown-item" href="#">Action</a></li>
					<li><a class="dropdown-item" href="#">Another action</a></li>
					<li><hr class="dropdown-divider"></li>
					<li><a class="dropdown-item" href="#">Separated</a></li>
				</ul>
			</div>

			<div class="btn-group">
				<button class="btn btn-primary">Split</button>
				<button class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown"><span class="visually-hidden">Toggle</span></button>
				<ul class="dropdown-menu">
					<li><a class="dropdown-item" href="#">Action</a></li>
					<li><a class="dropdown-item" href="#">Another</a></li>
				</ul>
			</div>

			<div class="btn-group dropup">
				<button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Dropup</button>
				<ul class="dropdown-menu"><li><a class="dropdown-item" href="#">Action</a></li></ul>
			</div>

			<div class="btn-group dropend">
				<button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">Dropend</button>
				<ul class="dropdown-menu"><li><a class="dropdown-item" href="#">Action</a></li></ul>
			</div>

			<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sg-modal">Open modal</button>

			<button class="btn btn-outline-secondary" id="sg-toast-btn">Show toast</button>
		</div>

		<div class="modal fade" id="sg-modal" tabindex="-1">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Modal title</h5>
						<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
					</div>
					<div class="modal-body">Modal body content goes here.</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Save</button>
					</div>
				</div>
			</div>
		</div>

		<div class="toast-container position-fixed bottom-0 end-0 p-3">
			<div class="toast" id="sg-toast" role="alert" aria-live="assertive" aria-atomic="true">
				<div class="toast-header">
					<span class="avatar me-2" style="width:20px;height:20px;font-size:.6rem">L</span>
					<strong class="me-auto">LatchPHP</strong>
					<button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
				</div>
				<div class="toast-body">Settings saved successfully.</div>
			</div>
		</div>

		<div class="mt-3">
			<div class="text-body-secondary small mb-2">Trigger alert toasts (via the JS <code>Toasts</code> class)</div>
			<div class="d-flex flex-wrap gap-2">
				<button class="btn btn-outline-success" data-sg-toast="SUCCESS">Success</button>
				<button class="btn btn-outline-warning" data-sg-toast="WARNING">Warning</button>
				<button class="btn btn-outline-danger" data-sg-toast="ERROR">Error</button>
				<button class="btn btn-outline-info" data-sg-toast="INFO">Notice</button>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Tabs &amp; accordion</h5></div></div>

		<ul class="nav nav-tabs mb-3" role="tablist">
			<li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sg-tab1">Overview</button></li>
			<li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#sg-tab2">Details</button></li>
		</ul>
		<div class="tab-content mb-4">
			<div class="tab-pane fade show active" id="sg-tab1"><p class="mb-0">Overview tab content.</p></div>
			<div class="tab-pane fade" id="sg-tab2"><p class="mb-0">Details tab content.</p></div>
		</div>

		<div class="accordion" id="sg-acc">
			<div class="accordion-item">
				<h2 class="accordion-header"><button class="accordion-button" data-bs-toggle="collapse" data-bs-target="#sg-acc1">First section</button></h2>
				<div id="sg-acc1" class="accordion-collapse collapse show" data-bs-parent="#sg-acc">
					<div class="accordion-body">Content of the first section.</div>
				</div>
			</div>
			<div class="accordion-item">
				<h2 class="accordion-header"><button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#sg-acc2">Second section</button></h2>
				<div id="sg-acc2" class="accordion-collapse collapse" data-bs-parent="#sg-acc">
					<div class="accordion-body">Content of the second section.</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Progress, spinners &amp; pagination</h5></div></div>

		<div class="progress mb-2"><div class="progress-bar" style="width:25%">25%</div></div>
		<div class="progress mb-3"><div class="progress-bar" style="width:70%">70%</div></div>

		<div class="d-flex align-items-center gap-3 mb-3">
			<div class="spinner-border text-primary"></div>
			<div class="spinner-grow text-primary"></div>
		</div>

		<nav>
			<ul class="pagination mb-0">
				<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
				<li class="page-item active"><a class="page-link" href="#">1</a></li>
				<li class="page-item"><a class="page-link" href="#">2</a></li>
				<li class="page-item"><a class="page-link" href="#">3</a></li>
				<li class="page-item"><a class="page-link" href="#">Next</a></li>
			</ul>
		</nav>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>List group</h5></div></div>
		<ul class="list-group">
			<li class="list-group-item d-flex justify-content-between align-items-center">Inbox <span class="badge text-bg-primary rounded-pill">14</span></li>
			<li class="list-group-item d-flex justify-content-between align-items-center">Drafts <span class="badge text-bg-secondary rounded-pill">2</span></li>
			<li class="list-group-item active">Active item</li>
			<li class="list-group-item disabled">Disabled item</li>
			<li class="list-group-item d-flex justify-content-between align-items-center">Sent <span class="badge text-bg-secondary rounded-pill">38</span></li>
		</ul>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>More badges &amp; button toolbar</h5></div></div>

		<div class="d-flex flex-wrap align-items-center gap-2 mb-3">
			<span class="badge rounded-pill text-bg-primary">Pill</span>
			<span class="badge rounded-circle text-bg-danger" style="width:24px;height:24px;display:inline-grid;place-items:center">3</span>
			<span class="badge text-bg-light border">Light</span>
			<span class="badge border text-secondary">Outline</span>
			<button class="btn btn-primary position-relative">Inbox
				<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger">9+</span>
			</button>
		</div>

		<div class="btn-toolbar gap-2">
			<div class="btn-group">
				<button class="btn btn-outline-secondary"><i class="bi bi-type-bold"></i></button>
				<button class="btn btn-outline-secondary"><i class="bi bi-type-italic"></i></button>
				<button class="btn btn-outline-secondary"><i class="bi bi-type-underline"></i></button>
			</div>
			<div class="btn-group">
				<button class="btn btn-outline-secondary"><i class="bi bi-text-left"></i></button>
				<button class="btn btn-outline-secondary"><i class="bi bi-text-center"></i></button>
				<button class="btn btn-outline-secondary"><i class="bi bi-text-right"></i></button>
			</div>
		</div>
	</div>
</div>

<div class="card-head"><div><h5>Cards</h5><p>Title, subtitle, footer and action variants.</p></div></div>
<div class="row g-3 mb-4">

	<div class="col-md-6 col-xl-4">
		<div class="card mb-0">
			<div class="card-body">
				<h5 class="card-title mb-1">Card with subtitle</h5>
				<h6 class="text-muted mb-2" style="font-size:12.5px">Supporting subtitle</h6>
				<p class="card-text">Some quick example text to build on the card title and make up the body.</p>
				<a href="#" class="btn btn-primary btn-sm">Action</a>
			</div>
		</div>
	</div>

	<div class="col-md-6 col-xl-4">
		<div class="card mb-0">
			<div class="card-body">
				<h5 class="card-title">Card with footer</h5>
				<p class="card-text">A card body sitting above a muted footer strip.</p>
			</div>
			<div class="card-footer text-muted" style="border-top:1px solid var(--lp-border);padding:14px 20px;font-size:12.5px">Updated 3 mins ago</div>
		</div>
	</div>

	<div class="col-md-6 col-xl-4">
		<div class="card mb-0">
			<div class="card-body">
				<h5 class="card-title">Two actions</h5>
				<p class="card-text">A card whose footer holds two distinct actions.</p>
			</div>
			<div class="card-footer d-flex gap-2" style="border-top:1px solid var(--lp-border);padding:14px 20px">
				<a href="#" class="btn btn-primary btn-sm">Confirm</a>
				<a href="#" class="btn btn-outline-secondary btn-sm">Dismiss</a>
			</div>
		</div>
	</div>

</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Media object</h5></div></div>

		<div class="d-flex align-items-center gap-3 mb-3">
			<span class="avatar" style="width:40px;height:40px;font-size:14px">JD</span>
			<div>
				<div class="fw-semibold">Jane Doe</div>
				<div class="text-muted" style="font-size:12.5px">Commented on your post 2 hours ago.</div>
			</div>
		</div>
		<div class="d-flex align-items-center gap-3">
			<span class="lp-link-ico"><i class="bi bi-bell"></i></span>
			<div>
				<div class="fw-semibold">New notification</div>
				<div class="text-muted" style="font-size:12.5px">A media object pairs a figure with descriptive text.</div>
			</div>
		</div>
	</div>
</div>

<script>
document.getElementById( 'sg-toast-btn' )?.addEventListener( 'click', function(){
	bootstrap.Toast.getOrCreateInstance( document.getElementById( 'sg-toast' ) ).show();
} );

document.querySelectorAll( '[data-sg-toast]' ).forEach( function( btn ){
	btn.addEventListener( 'click', function(){
		const key = btn.dataset.sgToast;
		App.alerts.push( `This is a ${ key.toLowerCase() } toast.`, App.alerts.TYPES[ key ] );
	} );
} );
</script>
