<?php
use App\Config;
use App\Http\URL;
?>

<div class="container vh-100">
	<div class="row justify-content-center h-100 align-items-center">
		<div class="col-md-8 col-lg-6 col-xl-5">

			<div class="card">
				<div class="card-header d-flex justify-content-center">

					<span class="d-inline-flex align-items-center gap-2"><svg width="34" height="34" viewBox="0 0 24 24" fill="none" style="stroke:var(--lp-accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4 H5 a2 2 0 0 0 -2 2 v12 a2 2 0 0 0 2 2 h2"/><path d="M17 4 h2 a2 2 0 0 1 2 2 v12 a2 2 0 0 1 -2 2 h-2"/><path d="M9 12 h6"/></svg><span style="font-weight:700;font-size:26px;letter-spacing:-.02em;color:var(--lp-text)"><?php echo esc_html( Config::get( 'app.site.name' ) ); ?></span></span>

				</div>
				<div class="card-body">

					<div class="alert alert-outline-danger">This link is invalid or has already been confirmed.</div>

				</div>
			</div>

			<p class="text-center">
				<a href="#">
					Forgot your password?
					<i class="bi bi-arrow-right"></i>
				</a>
			</p>

		</div><!-- end .col -->
	</div><!-- end .row -->
</div><!-- end .container -->

