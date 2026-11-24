<?php use App\Http\URL; ?>
<div class="lp-error">
	<div class="lp-error-card">

		<div class="lp-error-badge"><i class="bi bi-shield-lock"></i></div>

		<div class="lp-error-code">403</div>

		<h1 class="lp-error-title">Access denied</h1>

		<p class="lp-error-text">
			You do not have permission to view this resource. If you think this is a mistake, contact an administrator.
		</p>

		<a class="btn btn-primary" href="<?php echo URL::BUILD(); ?>">
			<i class="bi bi-arrow-left"></i> Back to home
		</a>

	</div>
</div>
