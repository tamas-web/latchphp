<?php

use App\Config;
use App\Filters\Filter;

$brand = esc_html( Config::get( 'app.site.name' ) );

if( ! Filter::apply( 'app.use_simple_footer', false ) ){

	?>
	<footer class="lp-auth-footer">
		&copy; <?php echo date( 'Y' ); ?> <strong><?php echo $brand; ?></strong>. All rights reserved.
	</footer>
	<?php

	return;

}

?>

<footer class="lp-footer">
	<div class="lp-footer-copy">
		&copy; <?php echo date( 'Y' ); ?> <strong><?php echo $brand; ?></strong>. All rights reserved.
	</div>
	<div class="lp-footer-meta">
		<?php
		printf(
			'Runtime %.2fs &middot; Memory %s &middot; v%s',
			microtime( true ) - APP_STARTUP_TIME,
			byteConvert( memory_get_usage() ),
			Config::get( 'app.version' )
		);
		?>
	</div>
</footer>
