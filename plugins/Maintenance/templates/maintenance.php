<?php use App\Filters\Filter; ?>
<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Maintenance</title>
	<style>
		* { box-sizing: border-box; }
		body {
			margin: 0;
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			background: #0f1219;
			color: #e6e9ef;
			font: 16px/1.6 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
			padding: 24px;
		}
		.box { max-width: 520px; text-align: center; }
		.icon { font-size: 48px; margin-bottom: 16px; }
		h1 { font-size: 22px; margin: 0 0 12px; font-weight: 600; }
		p { margin: 0; color: #aab2c5; }
	</style>
</head>
<body>
	<div class="box">
		<div class="icon">&#128679;</div>
		<h1>Maintenance</h1>
		<p><?php echo esc_html( $this->data->message ); ?></p>
	</div>
</body>
</html>
