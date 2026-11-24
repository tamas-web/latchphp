<?php

namespace App\ErrorHandlers;

use App\ErrorHandlers\Logger;
use App\Events\Listener;
use App\Libraries\Cli;

/**
 * Error & exception handler.
 *
 * Captures every PHP error, uncaught throwable and fatal. EVERY one is logged to
 * logs/errors/<Y-m-d>.log via Logger - in development AND production alike. The only
 * environment difference is presentation: in development a rich, styled report is shown
 * (a full-screen overlay for fatals/exceptions, a dismissable toast for non-fatal
 * notices/warnings); in production nothing is rendered to the visitor.
 */
class Handler {

	/**
	 * error level => [ label, colour ]
	 */
	protected const LEVELS = [
		\E_ERROR             => [ 'E_ERROR',             '#e74c3c' ],
		\E_WARNING           => [ 'E_WARNING',           '#f39c12' ],
		\E_PARSE             => [ 'E_PARSE',             '#e74c3c' ],
		\E_NOTICE            => [ 'E_NOTICE',            '#3498db' ],
		\E_CORE_ERROR        => [ 'E_CORE_ERROR',        '#e74c3c' ],
		\E_CORE_WARNING      => [ 'E_CORE_WARNING',      '#f39c12' ],
		\E_COMPILE_ERROR     => [ 'E_COMPILE_ERROR',     '#e74c3c' ],
		\E_COMPILE_WARNING   => [ 'E_COMPILE_WARNING',   '#f39c12' ],
		\E_USER_ERROR        => [ 'E_USER_ERROR',        '#e74c3c' ],
		\E_USER_WARNING      => [ 'E_USER_WARNING',      '#f39c12' ],
		\E_USER_NOTICE       => [ 'E_USER_NOTICE',       '#3498db' ],
		\E_USER_DEPRECATED   => [ 'E_USER_DEPRECATED',   '#95a5a6' ],
		\E_DEPRECATED        => [ 'E_DEPRECATED',        '#95a5a6' ],
		\E_RECOVERABLE_ERROR => [ 'E_RECOVERABLE_ERROR', '#e74c3c' ],
	];

	/**
	 * Shared CSS/JS printed once
	 */
	protected bool $assetsPrinted = false;

	/**
	 * Guard so a terminal report is rendered only once (exception + shutdown can both fire)
	 */
	protected bool $terminated = false;

	/**
	 * Buffered non-fatal errors - flushed into the page at render time (not at the point
	 * of error, which could be before any HTML exists)
	 */
	protected array $errors = [];

	/**
	 * Bootup - report everything, log everything, display only in development
	 */
	public function boot() : void {

		\error_reporting( \E_ALL );
		\ini_set( 'display_errors', $this->isDev() ? '1' : '0' );

		\set_error_handler( [ $this, 'onError' ] );
		\set_exception_handler( [ $this, 'onException' ] );
		\register_shutdown_function( [ $this, 'onShutdown' ] );

		// flush buffered notices/warnings into the page at the proper point (inside <body>,
		// after the scripts) - the same lifecycle hook Footer::mainJS uses - so we never emit
		// markup before the document starts
		Listener::on( 'post_print_scripts', [ $this, 'flushToasts' ] );

	}

	/**
	 * Are we in development?
	 */
	protected function isDev() : bool {

		return 'development' === \APP_ENVIRONMENT;

	}

	protected function isCli() : bool {

		return 'cli' === \PHP_SAPI;

	}

	/**
	 * Non-fatal errors (notices, warnings, deprecations, ...)
	 */
	public function onError( int $errno, string $errstr, string $errfile = '', int $errline = 0 ) : bool {

		// respect the @-operator / current reporting mask
		if( 0 === ( \error_reporting() & $errno ) ){

			return false;

		}

		[ $label, $color ] = self::LEVELS[ $errno ] ?? [ 'E_' . $errno, '#3498db' ];

		// logged in every environment
		Logger::write( 'errors', sprintf( '%s: %s in %s:%d', $label, $errstr, $errfile, $errline ) );

		// buffered in development - flushed into the page during render (see flushToasts)
		if( $this->isDev() ){

			if( $this->isCli() ){

				Cli::out( sprintf( '%s: %s in %s:%d', $label, $errstr, $errfile, $errline ), Cli::COLOR_YELLOW );

			} else {

				$this->errors[] = [ $label, $color, $errstr, $errfile, $errline ];

			}

		}

		// handled ( logged + buffered ) - suppress PHP's own output
		return true;

	}

	/**
	 * Uncaught throwables
	 */
	public function onException( \Throwable $e ) : void {

		// logged in every environment
		Logger::write( 'errors', sprintf(
			'Uncaught %s: %s in %s:%d%s%s',
			$e::class,
			$e->getMessage(),
			$e->getFile(),
			$e->getLine(),
			\PHP_EOL,
			$e->getTraceAsString()
		) );

		if( $this->isCli() ){

			$this->renderCliReport( $e::class, $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace() );

			return;

		}

		if( ! \headers_sent() ){

			\http_response_code( 500 );

		}

		if( $this->isDev() ){

			$this->renderOverlay( $e::class, '#e74c3c', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTrace() );

		} else {

			$this->renderProductionNotice();

		}

	}

	/**
	 * Fatals that bypass set_error_handler ( E_ERROR / E_PARSE / E_CORE_ERROR / E_COMPILE_ERROR )
	 */
	public function onShutdown() : void {

		$e = \error_get_last();

		$fatal = [ \E_ERROR, \E_PARSE, \E_CORE_ERROR, \E_COMPILE_ERROR ];

		if( $e && \in_array( $e['type'], $fatal, true ) ){

			[ $label, $color ] = self::LEVELS[ $e['type'] ] ?? [ 'FATAL', '#e74c3c' ];

			// logged in every environment
			Logger::write( 'errors', sprintf( 'FATAL %s: %s in %s:%d', $label, $e['message'], $e['file'], $e['line'] ) );

			if( $this->isCli() ){

				$this->renderCliReport( $label, $e['message'], $e['file'], $e['line'] );

			} elseif( $this->isDev() ){

				$this->renderOverlay( $label, $color, $e['message'], $e['file'], $e['line'] );

			} else {

				$this->renderProductionNotice();

			}

		}

		// late notices/warnings that never reached `post_print_scripts` (e.g. error after the
		// footer, or a page that renders no footer). Only when the response is HTML, so we never
		// corrupt a JSON / redirect response, and not after a terminal overlay.
		if( $this->isDev() && ! $this->isCli() && ! $this->terminated && $this->errors && $this->isHtmlResponse() ){

			$this->flushToasts();

		}

	}

	/**
	 * Is the current response HTML? (so we never inject markup into JSON / other responses)
	 */
	protected function isHtmlResponse() : bool {

		foreach( headers_list() as $header ){

			if( 0 === stripos( $header, 'content-type:' ) ){

				return false !== stripos( $header, 'text/html' );

			}

		}

		// no explicit content-type -> PHP's default is text/html
		return true;

	}

	/**
	 * Full-screen styled report for fatals / uncaught exceptions (development)
	 */
	protected function renderOverlay( string $label, string $color, string $message, string $file, int $line, array $trace = [] ) : void {

		if( $this->terminated ){

			return;

		}

		$this->terminated = true;

		$this->renderAssets();

		printf(
			'<div class="eh-overlay"><div class="eh-card">
				<div class="eh-head" style="border-color:%1$s">
					<span class="eh-badge" style="background:%1$s">%2$s</span>
					<span class="eh-msg">%3$s</span>
				</div>
				<div class="eh-loc">%4$s<span class="eh-colon">:</span>%5$d</div>
				%6$s
				%7$s
			</div></div>',
			$color,
			$this->e( $label ),
			$this->e( $message ),
			$this->e( $file ),
			$line,
			$this->excerpt( $file, $line ),
			$this->renderTrace( $trace )
		);

	}

	/**
	 * Flush all buffered non-fatal errors into the page as dismissable toasts.
	 * Hooked to the framework's `post_print_scripts` event, so the output lands inside
	 * <body> (valid HTML) rather than at the arbitrary point each error occurred.
	 */
	public function flushToasts() : void {

		if( ! $this->isDev() || ! $this->errors ){

			return;

		}

		$this->renderAssets();

		foreach( $this->errors as [ $label, $color, $message, $file, $line ] ){

			$html = sprintf(
				'<div class="eh-toast" style="border-left-color:%1$s" onclick="this.remove()">
					<div class="eh-toast-head"><span class="eh-badge" style="background:%1$s">%2$s</span></div>
					<div class="eh-toast-msg">%3$s</div>
					<div class="eh-loc">%4$s<span class="eh-colon">:</span>%5$d</div>
				</div>',
				$color,
				$this->e( $label ),
				$this->e( $message ),
				$this->e( basename( $file ) ),
				$line
			);

			printf(
				'<script>window.__ehToast && window.__ehToast(%s);</script>',
				json_encode( $html, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES )
			);

		}

		// flushed - clear so a later call (shutdown fallback) won't re-emit these
		$this->errors = [];

	}

	/**
	 * Minimal generic message in production ( no internals leaked )
	 */
	protected function renderProductionNotice() : void {

		if( $this->terminated ){

			return;

		}

		$this->terminated = true;

		if( ! \headers_sent() ){

			echo 'An error occurred. Please try again later.';

		}

	}

	protected function renderCliReport( string $label, string $message, string $file, int $line, array $trace = [] ) : void {

		if( $this->terminated ){

			return;

		}

		$this->terminated = true;

		$lines = [
			sprintf( '%s: %s', $label, $message ),
			sprintf( '  at %s:%d', $file, $line ),
		];

		foreach( $trace as $i => $frame ){

			$where = ( $frame['file'] ?? '[internal]' ) . ( isset( $frame['line'] ) ? ':' . $frame['line'] : '' );
			$call  = ( $frame['class'] ?? '' ) . ( $frame['type'] ?? '' ) . ( $frame['function'] ?? '' ) . '()';

			$lines[] = sprintf( '  #%d %s %s', $i, $call, $where );

		}

		Cli::out( join( \PHP_EOL, $lines ), Cli::COLOR_RED );

	}

	/**
	 * Source excerpt around the offending line
	 */
	protected function excerpt( string $file, int $line, int $pad = 6 ) : string {

		if( ! $file || ! is_readable( $file ) ){

			return '';

		}

		$lines = @file( $file, FILE_IGNORE_NEW_LINES );

		if( ! $lines ){

			return '';

		}

		$start = max( 0, $line - $pad - 1 );
		$end   = min( count( $lines ), $line + $pad );

		$rows = '';

		for( $i = $start; $i < $end; ++$i ){

			$n = $i + 1;

			$rows .= sprintf(
				'<div class="eh-row%s"><span class="eh-ln">%d</span><span class="eh-src">%s</span></div>',
				$n === $line ? ' eh-row-err' : '',
				$n,
				$this->e( $lines[ $i ] ?? '' )
			);

		}

		return '<div class="eh-excerpt">' . $rows . '</div>';

	}

	/**
	 * Render a throwable's stack trace
	 */
	protected function renderTrace( array $trace ) : string {

		if( ! $trace ){

			return '';

		}

		$rows = '';

		foreach( $trace as $i => $f ){

			$where = ( $f['file'] ?? '[internal]' ) . ( isset( $f['line'] ) ? ':' . $f['line'] : '' );
			$call  = ( $f['class'] ?? '' ) . ( $f['type'] ?? '' ) . ( $f['function'] ?? '' ) . '()';

			$rows .= sprintf(
				'<div class="eh-tr"><span class="eh-tr-i">#%d</span><span class="eh-tr-call">%s</span><span class="eh-tr-where">%s</span></div>',
				$i,
				$this->e( $call ),
				$this->e( $where )
			);

		}

		return '<div class="eh-trace"><div class="eh-trace-h">Stack trace</div>' . $rows . '</div>';

	}

	/**
	 * Escape for HTML
	 */
	protected function e( string $value ) : string {

		return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );

	}

	/**
	 * Shared CSS + the toast helper, printed once
	 */
	protected function renderAssets() : void {

		if( $this->assetsPrinted ){

			return;

		}

		$this->assetsPrinted = true;

		echo <<<'HTML'
		<style>
		.eh-overlay{position:fixed;inset:0;z-index:2147483647;background:rgba(15,18,25,.92);overflow:auto;padding:40px 16px;font:14px/1.5 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;-webkit-font-smoothing:antialiased}
		.eh-card{max-width:980px;margin:0 auto;background:#1b1f2a;color:#e6e9ef;border:1px solid #2c3242;border-radius:12px;box-shadow:0 20px 60px rgba(0,0,0,.5);overflow:hidden}
		.eh-head{display:flex;align-items:flex-start;gap:12px;padding:20px 22px;border-left:5px solid #e74c3c;background:#222736}
		.eh-msg{font-size:16px;font-weight:600;color:#fff;word-break:break-word}
		.eh-badge{display:inline-block;padding:3px 9px;border-radius:6px;color:#fff;font-size:11px;font-weight:700;letter-spacing:.04em;white-space:nowrap}
		.eh-loc{padding:12px 22px;color:#8b93a7;border-bottom:1px solid #2c3242;word-break:break-all}
		.eh-colon{color:#5b6276;margin:0 1px}
		.eh-excerpt{padding:14px 0;background:#14171f;overflow:auto}
		.eh-row{display:flex;white-space:pre;padding:0 22px}
		.eh-row-err{background:rgba(231,76,60,.16)}
		.eh-row-err .eh-ln{color:#ff8a7a}
		.eh-ln{display:inline-block;width:48px;color:#525a70;text-align:right;padding-right:16px;user-select:none}
		.eh-src{color:#cdd3e0}
		.eh-trace{padding:8px 22px 20px}
		.eh-trace-h{color:#8b93a7;text-transform:uppercase;letter-spacing:.08em;font-size:11px;margin:12px 0 8px}
		.eh-tr{display:flex;gap:12px;padding:4px 0;border-top:1px solid #232838;flex-wrap:wrap}
		.eh-tr-i{color:#525a70;width:36px}
		.eh-tr-call{color:#e6e9ef}
		.eh-tr-where{color:#8b93a7;margin-left:auto}
		#eh-toasts{position:fixed;bottom:16px;right:16px;z-index:2147483647;display:flex;flex-direction:column;gap:10px;max-width:420px}
		.eh-toast{background:#1b1f2a;color:#e6e9ef;border:1px solid #2c3242;border-left:4px solid #f39c12;border-radius:10px;padding:12px 14px;box-shadow:0 10px 30px rgba(0,0,0,.45);font:13px/1.45 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;cursor:pointer;animation:eh-in .18s ease-out}
		.eh-toast-head{margin-bottom:6px}
		.eh-toast-msg{color:#fff;word-break:break-word}
		.eh-toast .eh-loc{padding:0;margin-top:6px;border:0;color:#8b93a7;font-size:12px}
		@keyframes eh-in{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
		</style>
		<script>
		window.__ehToast=function(html){
			var c=document.getElementById('eh-toasts');
			if(!c){c=document.createElement('div');c.id='eh-toasts';(document.body||document.documentElement).appendChild(c);}
			var d=document.createElement('div');d.innerHTML=html;var t=d.firstElementChild;if(t)c.appendChild(t);
		};
		</script>
		HTML;

	}

}
