<?php

namespace App\Mailer;

use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
//~ use PHPMailer\PHPMailer\SMTP;
//~ use PHPMailer\PHPMailer\Exception;
use App\Views\View;

class Mail extends PHPMailer {

	protected View $view;

	public function __construct( mixed $email, string $subject, string $body ){

		parent::__construct( exceptions: false );

		$this->view = new View;

		$this->IsSMTP();

		$this->CharSet    = self::CHARSET_UTF8;
		// $this->Encoding   = 'base64';

		$this->SMTPDebug  = Config::get( 'app.smtp.debug' );
		$this->SMTPAuth   = Config::get( 'app.smtp.auth' );
		$this->SMTPSecure = Config::get( 'app.smtp.protocol' );
		$this->Host       = Config::get( 'app.smtp.host' );
		$this->Port       = Config::get( 'app.smtp.port' );
		$this->Username   = Config::get( 'app.smtp.username' );
		$this->Password   = Config::get( 'app.smtp.password' );

		$this->SetFrom( Config::get( 'app.smtp.from.email' ), Config::get( 'app.smtp.from.name' ) );

		if( is_string( $email ) ){

			$this->addAddress( $email, $email );

		} elseif( is_array( $email ) && count( $email ) === 2 ){

			$this->addAddress( $email[0], $email[1] );

		}

		$this->addBCC( Config::get( 'app.smtp.bcc' ) );

		$this->Subject = sprintf(
			'%s%s%s',
			$subject,
			Config::get( 'app.email.subject.separator' ),
			Config::get( 'app.email.subject.suffix' )
		);

		$this->addTemplate();

		$this->content( $body );

	}

	public function view() : View {

		return $this->view;

	}

	public function addTemplate( string $template = null ) : self {

		// remove all previous template files
		foreach( $this->view->body()->getTemplates() as $tpl ){

			$this->view->body()->remove( $tpl );

		}

		// add template
		$this->view
			->body()
			->add( $template ? $template : 'mail.index' );

		return $this;

	}

	public function content( string $html ) : self {

		$this->view
			->body()
			->data( [
				'content' => $html,
			] );

		return $this;

	}

	public function send() {

		return parent::send();

	}

	public function preSend(){

		$this->msgHTML( $this->view->buffer() );

		return parent::preSend();

	}

}
