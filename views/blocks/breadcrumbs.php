<?php


$render = fn( $crumb ) => $crumb->icon
	? sprintf(
		'<i class="%s" aria-hidden="true"></i><span class="visually-hidden">%s</span>',
		esc_html( $crumb->icon ),
		esc_html( $crumb->name )
	)
	: esc_html( $crumb->name );

?>
<nav aria-label="breadcrumb">
	<ol class="breadcrumb">
		<?php
		$breadcrumbs = $this->data->breadcrumbs;
		$last = array_pop( $breadcrumbs );
		foreach( $breadcrumbs as $breadcrumb ){
			printf(
				'<li class="breadcrumb-item"><a href="%s">%s</a></li>',
				esc_url( $breadcrumb->link ),
				$render( $breadcrumb )
			);
		}
		printf(
			'<li class="breadcrumb-item active" aria-current="page">%s</li>',
			$render( $last )
		);
		?>
	</ol>
</nav>
