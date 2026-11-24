<?php

use App\Http\URL;

$channels = $this->data->channels;
$channel  = $this->data->channel;
$dates    = $this->data->dates;
$date     = $this->data->date;
$content  = $this->data->content;

?>

<div class="card">
	<div class="card-body">

		<?php if( $channels ){ ?>

			<form method="get" action="<?php echo URL::BUILD( 'admin/system/logs' ); ?>" class="self-submit">

				<div class="row g-2 align-items-end mb-3">

					<div class="col-12 col-sm-6 col-md-4">
						<label class="form-label" for="channel">Channel</label>
						<select class="form-select" id="channel" name="channel">
							<?php foreach( $channels as $c ){ ?>
								<option value="<?php echo esc_html( $c ); ?>"<?php echo $c === $channel ? ' selected' : ''; ?>><?php echo esc_html( $c ); ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="col-12 col-sm-6 col-md-4">
						<label class="form-label" for="date">Date</label>
						<select class="form-select" id="date" name="date">
							<?php foreach( $dates as $d ){ ?>
								<option value="<?php echo esc_html( $d ); ?>"<?php echo $d === $date ? ' selected' : ''; ?>><?php echo esc_html( $d ); ?></option>
							<?php } ?>
						</select>
					</div>

				</div>

			</form>

			<?php if( '' !== $content ){ ?>
				<pre class="mb-0 p-3 rounded border bg-body-tertiary small" style="max-height:60vh;overflow:auto;"><?php echo esc_html( $content ); ?></pre>
			<?php } else { ?>
				<p class="text-muted m-0">This log is empty.</p>
			<?php } ?>

		<?php } else { ?>

			<p class="text-muted m-0">No logs found yet.</p>

		<?php } ?>

	</div>
</div>
