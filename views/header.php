<!DOCTYPE html>
<html <?php echo $this->htmlAttributes(); ?>>

	<head>

		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<?php $this->app_header(); ?>

		<script>
			( () => {
				let d = document.documentElement;
				d.setAttribute( 'data-bs-theme', localStorage.getItem( 'lp-theme' ) || 'dark' );
				d.setAttribute( 'data-accent', localStorage.getItem( 'lp-accent' ) || 'lime' );
			} )();
		</script>

	</head>

<body class="<?php echo $this->bodyClass(); ?>">
