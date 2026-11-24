<?php
use App\Http\{ Request, URL };
?>

<div class="card">
	<div class="card-header d-md-flex justify-content-between align-items-center">

		<h4 class="card-title mb-2">
			Notifications <?php printf(
				'%s %s',
				esc_html( Request::getInstance()->user()->name ),
				esc_html( Request::getInstance()->user()->surname ),
			); ?>
		</h4>

		<div>

			<a href="<?php echo URL::BUILD( 'notifications' ); ?>" class="btn btn-primary">
				<span class="btn-icon-left">
					<i class="bi bi-bell"></i>
				</span>
				View all notifications
			</a>

		</div>

	</div><!-- end .card-header -->
	<div class="card-body">

		<h4 class="mb-1"><?php echo $this->data->notification->message; ?></h4>

		<p><?php echo $this->data->notification->created_at; ?></p>

		<?php echo $this->data->notification->details; ?>

	</div><!-- end .card-body -->
</div>

