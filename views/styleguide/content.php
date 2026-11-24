<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Typography</h5><p>Display and heading levels.</p></div></div>

		<h1 class="display-4">Display heading</h1>
		<h1>Heading level 1</h1>
		<h2>Heading level 2</h2>
		<h3>Heading level 3</h3>
		<h4>Heading level 4</h4>
		<h5>Heading level 5</h5>
		<h6>Heading level 6</h6>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Body text</h5><p>Paragraphs and inline elements.</p></div></div>

		<p class="lead">This is a lead paragraph - it stands out from regular body copy.</p>
		<p>A standard paragraph with <strong>bold</strong>, <em>italic</em>, <a href="#">a link</a>,
			<mark>highlighted</mark>, <del>deleted</del>, <ins>inserted</ins>, <small>small</small> and
			<code>inline code</code>, plus a shortcut <kbd>Ctrl</kbd> + <kbd>S</kbd>.</p>
		<blockquote class="blockquote">
			<p>A well-placed blockquote sets a quotation apart from the surrounding text.</p>
			<footer class="blockquote-footer">Someone famous in <cite>A Source</cite></footer>
		</blockquote>
		<pre class="border rounded p-3 mb-0"><code>$user = Request::getInstance()->user();</code></pre>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Text colours, weight &amp; style</h5></div></div>

		<div class="row">
			<div class="col-md-6">
				<p class="text-primary mb-1">.text-primary</p>
				<p class="text-success mb-1">.text-success</p>
				<p class="text-danger mb-1">.text-danger</p>
				<p class="text-warning mb-1">.text-warning</p>
				<p class="text-info mb-1">.text-info</p>
				<p class="text-muted mb-0">.text-muted</p>
			</div>
			<div class="col-md-6">
				<p class="fw-bold mb-1">.fw-bold - bold weight</p>
				<p class="fw-semibold mb-1">.fw-semibold - semibold weight</p>
				<p class="fw-normal mb-1">.fw-normal - normal weight</p>
				<p class="fw-light mb-1">.fw-light - light weight</p>
				<p class="fst-italic mb-0">.fst-italic - italic style</p>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Text alignment</h5></div></div>

		<p class="text-start">Left aligned text.</p>
		<p class="text-center">Centered text.</p>
		<p class="text-end">Right aligned text.</p>
		<p class="text-justify mb-0" style="text-align:justify">Justified text spreads each line to fill the available width, producing flush left and right edges across the paragraph for a denser, column-like appearance.</p>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Lists</h5><p>Unordered, ordered, inline, unstyled and description lists.</p></div></div>

		<div class="row">
			<div class="col-md-3">
				<ul>
					<li>First item</li>
					<li>Second item</li>
					<li>Third item</li>
				</ul>
			</div>
			<div class="col-md-3">
				<ol>
					<li>First step</li>
					<li>Second step</li>
					<li>Third step</li>
				</ol>
			</div>
			<div class="col-md-3">
				<ul class="list-unstyled">
					<li>No bullets</li>
					<li>Clean rows</li>
					<li>Plain text</li>
				</ul>
				<ul class="list-inline">
					<li class="list-inline-item">One</li>
					<li class="list-inline-item">Two</li>
					<li class="list-inline-item">Three</li>
				</ul>
			</div>
			<div class="col-md-3">
				<dl>
					<dt>Term</dt>
					<dd>The definition for the term above.</dd>
					<dt>Another term</dt>
					<dd>Its matching definition.</dd>
				</dl>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Address &amp; grid</h5></div></div>

		<div class="row">
			<div class="col-md-4">
				<address class="mb-0">
					<strong>LatchPHP Inc.</strong><br>
					1 Example Street<br>
					Bucharest, RO<br>
					<a href="mailto:hello@example.com">hello@example.com</a>
				</address>
			</div>
			<div class="col-md-8">
				<div class="row g-2 text-center">
					<div class="col"><div class="border rounded py-2">.col</div></div>
					<div class="col"><div class="border rounded py-2">.col</div></div>
					<div class="col"><div class="border rounded py-2">.col</div></div>
				</div>
				<div class="row g-2 text-center mt-1">
					<div class="col-8"><div class="border rounded py-2">.col-8</div></div>
					<div class="col-4"><div class="border rounded py-2">.col-4</div></div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="card">
	<div class="card-body">
		<div class="card-head"><div><h5>Colours</h5><p>Theme accent and neutral surfaces.</p></div></div>

		<div class="d-flex flex-wrap gap-3">
			<div class="text-center"><div class="rounded" style="width:64px;height:64px;background:var(--lp-accent)"></div><small class="text-muted">accent</small></div>
			<div class="text-center"><div class="rounded" style="width:64px;height:64px;background:var(--lp-accent-strong)"></div><small class="text-muted">accent-strong</small></div>
			<div class="text-center"><div class="rounded" style="width:64px;height:64px;background:var(--lp-surface);border:1px solid var(--lp-border)"></div><small class="text-muted">surface</small></div>
			<div class="text-center"><div class="rounded" style="width:64px;height:64px;background:var(--lp-elevated);border:1px solid var(--lp-border)"></div><small class="text-muted">elevated</small></div>
			<div class="text-center"><div class="rounded" style="width:64px;height:64px;background:var(--lp-border)"></div><small class="text-muted">border</small></div>
		</div>
	</div>
</div>
