<?php use App\Http\URL; ?>
<div class="lp-error">
	<div class="lp-error-card">

		<div class="lp-error-badge"><i class="bi bi-exclamation-octagon"></i></div>

		<div class="lp-error-code">405</div>

		<h1 class="lp-error-title">Method not allowed</h1>

		<p class="lp-error-text">
			This request method is not supported for the resource you tried to reach.
		</p>

		<a class="btn btn-primary" href="<?php echo URL::BUILD(); ?>">
			<i class="bi bi-arrow-left"></i> Back to home
		</a>

	</div>
</div>
