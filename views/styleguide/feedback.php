<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Callouts</h5><p>Bordered note blocks for contextual messages.</p></div></div>

		<div class="callout callout-primary">
			<div class="callout-title">Primary</div>
			<p class="callout-text">A callout uses a coloured left border to flag a block of related information.</p>
		</div>
		<div class="callout callout-success">
			<div class="callout-title">Success</div>
			<p class="callout-text">The operation completed without any problems.</p>
		</div>
		<div class="callout callout-danger">
			<div class="callout-title">Danger</div>
			<p class="callout-text">Something went wrong and needs your attention.</p>
		</div>
		<div class="callout callout-warning">
			<div class="callout-title">Warning</div>
			<p class="callout-text">Double-check this before continuing.</p>
		</div>
		<div class="callout callout-info">
			<div class="callout-title">Info</div>
			<p class="callout-text">A neutral, informational note.</p>
		</div>
		<div class="callout">
			<div class="callout-title">Default</div>
			<p class="callout-text">A plain callout with no contextual colour.</p>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Tooltips &amp; popovers</h5><p>Hover/focus overlays (Bootstrap JS).</p></div></div>

		<div class="d-flex flex-wrap gap-2">
			<button class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="top" title="Tooltip on top">Top</button>
			<button class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Tooltip on bottom">Bottom</button>
			<button class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="left" title="Tooltip on left">Left</button>
			<button class="btn btn-outline-secondary" data-bs-toggle="tooltip" data-bs-placement="right" title="Tooltip on right">Right</button>
			<button class="btn btn-primary" data-bs-toggle="popover" data-bs-title="Popover title" data-bs-content="Popovers can hold a title and a longer body of text.">Popover</button>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Offcanvas &amp; collapse</h5></div></div>

		<div class="d-flex flex-wrap gap-2">
			<button class="btn btn-outline-secondary" type="button" data-bs-toggle="offcanvas" data-bs-target="#sg-offcanvas">Open offcanvas</button>
			<button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#sg-collapse">Toggle collapse</button>
		</div>

		<div class="collapse mt-3" id="sg-collapse">
			<div class="callout mb-0">
				<p class="callout-text">This content is revealed and hidden with the collapse plugin.</p>
			</div>
		</div>

		<div class="offcanvas offcanvas-end" tabindex="-1" id="sg-offcanvas">
			<div class="offcanvas-header">
				<h5 class="offcanvas-title">Offcanvas</h5>
				<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
			</div>
			<div class="offcanvas-body">
				<p>An offcanvas panel slides in from the edge of the viewport - handy for filters, carts or secondary navigation.</p>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Placeholders</h5><p>Loading skeletons.</p></div></div>

		<p class="placeholder-glow mb-2">
			<span class="placeholder col-7"></span>
			<span class="placeholder col-4"></span>
			<span class="placeholder col-6"></span>
			<span class="placeholder col-8"></span>
		</p>
		<a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-3"></a>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Spinners &amp; close button</h5></div></div>

		<div class="d-flex flex-wrap align-items-center gap-3">
			<div class="spinner-border text-primary"></div>
			<div class="spinner-border spinner-border-sm text-primary"></div>
			<div class="spinner-grow text-primary"></div>
			<div class="spinner-grow spinner-grow-sm text-primary"></div>
			<button class="btn btn-primary" type="button" disabled>
				<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
				Loading...
			</button>
			<span class="vr"></span>
			<button type="button" class="btn-close" aria-label="Close"></button>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Button toggles</h5><p>Checkbox/radio rendered as buttons (<code>btn-check</code>).</p></div></div>

		<input type="checkbox" class="btn-check" id="sg-bc1" autocomplete="off">
		<label class="btn btn-outline-primary" for="sg-bc1">Single toggle</label>

		<div class="btn-group ms-2" role="group">
			<input type="radio" class="btn-check" name="sg-bcr" id="sg-bcr1" autocomplete="off" checked>
			<label class="btn btn-outline-secondary" for="sg-bcr1">Left</label>
			<input type="radio" class="btn-check" name="sg-bcr" id="sg-bcr2" autocomplete="off">
			<label class="btn btn-outline-secondary" for="sg-bcr2">Middle</label>
			<input type="radio" class="btn-check" name="sg-bcr" id="sg-bcr3" autocomplete="off">
			<label class="btn btn-outline-secondary" for="sg-bcr3">Right</label>
		</div>
	</div>
</div>

<script>
document.querySelectorAll( '[data-bs-toggle="tooltip"]' ).forEach( el => new bootstrap.Tooltip( el ) );
document.querySelectorAll( '[data-bs-toggle="popover"]' ).forEach( el => new bootstrap.Popover( el ) );
</script>
