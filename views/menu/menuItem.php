<?php

$active = $this->data->active ? ' active' : '';

if( $this->data->depth > 0 && $this->data->items ){

	printf( '<h6>%s</h6>', $this->data->title );

	foreach( $this->data->items as $item ){
		echo $item;
	}

} elseif( $this->data->depth > 0 ){

	printf(
		'<a href="%s" class="ctx-link%s"><span class="dot"></span>%s</a>',
		$this->data->path,
		$active,
		$this->data->title
	);

} elseif( ! $this->data->items ){

	printf(
		'<a href="%s" class="rail-btn%s"><i class="%s"></i><span class="rail-tip">%s</span></a>',
		$this->data->path,
		$active,
		$this->data->icon,
		$this->data->title
	);

} else {

	$panelId = 'lp-panel-' . strtolower( preg_replace( '/[^a-z0-9]+/i', '-', $this->data->title ) );

	printf(
		'<button type="button" class="rail-btn%s" data-panel="%s"><i class="%s"></i><span class="rail-tip">%s</span></button>',
		$active,
		$panelId,
		$this->data->icon,
		$this->data->title
	);

	printf( '<aside class="context-panel" id="%s" data-panel-for="%s">', $panelId, $panelId );

	foreach( $this->data->items as $item ){
		echo $item;
	}

	echo '</aside>';

}
