<?php

use App\Html\Elements\{ Form, Button };
use App\Http\URL;
use App\Http\Request;

?>
<div class="alert alert-warning">
	<p>
		The email address <strong><em><?php echo Request::getInstance()->user()->email; ?></em></strong> is not confirmed.
		Go to <strong>Settings > General settings</strong> to change your email address or
		click the button below to resend the confirmation email.
	</p>

	<?php
	( new Form( 'resend-email-confirm' ) )
		->class( 'd-inline' )
		->method( 'post' )
		->action( URL::BUILD( '/api/forms/register/resend-confirmation-email' ) )
		->append(
			Button::Create( 'resendEmail' )
				->type( 'submit' )
				->name( 'resendEmail' )
				->value( 'resendEmail' )
				->text( 'Resend confirmation email' )
				->class( 'btn btn-primary' )
		)
		->render();
	?>

</div>
