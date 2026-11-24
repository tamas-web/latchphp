<?php

use App\Events\Emitter;
use App\Http\Request;

?>

<?php Emitter::broadcast( 'section_content_start', $this ); ?>

<div class="page-body">

	<?php Emitter::broadcast( 'container_fluid_content_start', $this ); ?>

	<?php $pageTitle = Request::getInstance()->view()->title(); ?>

	<?php if( $pageTitle ){ ?>
		<h1 class="page-title"><?php echo $pageTitle; ?></h1>
	<?php } ?>

	<?php Emitter::broadcast( 'content_start', $this ); ?>

