<?php use App\Http\URL; ?>
<div class="lp-error">
	<div class="lp-error-card">

		<div class="lp-error-badge"><i class="bi bi-compass"></i></div>

		<div class="lp-error-code">404</div>

		<h1 class="lp-error-title">Page not found</h1>

		<p class="lp-error-text">
			We could not find the page you are looking for. It may have been moved, renamed or removed.
		</p>

		<a class="btn btn-primary" href="<?php echo URL::BUILD(); ?>">
			<i class="bi bi-arrow-left"></i> Back to home
		</a>

	</div>
</div>
