<?php

use App\Http\URL;

$user = $this->data->user;

/* every seam this one example plugin touches - rendered as the page's own content */
$seams = [
	[
		'icon'  => 'bi-signpost-2',
		'title' => 'routes_loaded',
		'desc'  => 'The router is not booted during run(), so routes are registered on this event.',
		'code'  => "Route::get( '/hello-world', [ HelloWorldController::class, 'page' ] )",
	],
	[
		'icon'  => 'bi-folder2-open',
		'title' => 'Plugin-owned templates',
		'desc'  => 'new View( plugindir . \'/templates\' ) resolves this file; core views/ is the fallback.',
		'code'  => "\$body = new View( dirname( __DIR__ ) . '/templates' );",
	],
	[
		'icon'  => 'bi-layers',
		'title' => 'blank_output',
		'desc'  => 'The buffered plugin view is handed to core as body data - chrome untouched.',
		'code'  => "->data( [ 'output' => \$body->buffer() ] )->add( 'blank_output' )",
	],
	[
		'icon'  => 'bi-list-ul',
		'title' => 'app.menu',
		'desc'  => 'A filter contributes the rail entry that brought you to this page.',
		'code'  => "Filter::add( 'app.menu', [ \$this, 'menuItem' ] );",
	],
	[
		'icon'  => 'bi-tags',
		'title' => 'body_class',
		'desc'  => 'A filter appends plugin-hello-world to the <body> class list - inspect it.',
		'code'  => "Filter::add( 'body_class', [ \$this, 'bodyClass' ] );",
	],
	[
		'icon'  => 'bi-broadcast',
		'title' => 'container_fluid_content_start',
		'desc'  => 'An event prints the greeting banner above this card on every page.',
		'code'  => "Listener::on( 'container_fluid_content_start', [ \$this, 'greet' ] );",
	],
];

?>

<div class="mb-4">

	<h5 class="mb-1">Hello, <?php echo esc_html( $user->name ); ?>.</h5>

	<p class="text-muted mb-0">
		This page is served entirely by the <strong>HelloWorld</strong> plugin - its own route,
		controller and template. No core file was edited to put it here.
	</p>

</div>

<div class="row g-3">

	<?php foreach( $seams as $seam ){ ?>

		<div class="col-12 col-lg-6">

			<div class="lp-card h-100">

				<div class="d-flex align-items-start gap-3">

					<span class="lp-link-ico"><i class="bi <?php echo esc_html( $seam['icon'] ); ?>"></i></span>

					<div class="w-100">

						<div class="lp-link-title"><?php echo esc_html( $seam['title'] ); ?></div>
						<div class="lp-link-desc mb-2"><?php echo esc_html( $seam['desc'] ); ?></div>

						<pre class="mb-0"><code><?php echo esc_html( $seam['code'] ); ?></code></pre>

					</div>

				</div>

			</div>

		</div>

	<?php } ?>

</div>

<p class="text-muted mt-4 mb-0">
	The source lives in <code>plugins/HelloWorld/</code> - four files, and the smallest complete
	example to copy when writing your own. Deactivate the plugin on the
	<a href="<?php echo esc_url( URL::BUILD( 'admin/settings/plugins' ) ); ?>">plugins settings page</a>
	and this route disappears with it.
</p>
