<?php

namespace App\Controllers\System;

use App\Alerts\Alert;
use App\Filters\Filter;
use App\Html\Link;
use App\Http\{ Request, URL };
use App\Libraries\Controller;

class SystemController extends Controller {

	protected $pageTitle = 'System';

	const LOG_TAIL = 1000;

	public function backup( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Backups' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/system/backup', 'Backups' ),
		] );

		$request->view()
			->body()
				->data( [
					'backups' => $this->listBackups(),
				] )
				->add( 'system.backup' );

		$request->view()->render();

	}

	public function backupDownload( Request $request ) : void {

		$file = $this->backupPath( (string) $request->getParameter( 0 ) );

		if( ! $file ){

			Alert::error( 'The requested backup could not be found.' );

			$request->redirect( URL::BUILD( 'admin/system/backup' ) );

			return;

		}

		header( 'Content-Type: application/gzip' );
		header( sprintf( 'Content-Disposition: attachment; filename="%s"', basename( $file ) ) );
		header( 'Content-Length: ' . filesize( $file ) );

		readfile( $file );

	}

	public function logs( Request $request ) : void {

		Filter::add( 'page_title', fn() => 'Logs' );

		Filter::add( 'app.breadcrumbs', fn( $breadcrumbs ) => [
			...$breadcrumbs,
			new Link( '/admin', 'Administration' ),
			new Link( '/admin/system/logs', 'Logs' ),
		] );

		$channels = $this->listChannels();

		$channel = $request->get( 'channel', 'strip_tags' );

		if( ! in_array( $channel, $channels, true ) ){
			$channel = $channels[0] ?? '';
		}

		$dates = $channel ? $this->listDates( $channel ) : [];

		$date = $request->get( 'date', 'strip_tags' );

		if( ! in_array( $date, $dates, true ) ){
			$date = $dates[0] ?? '';
		}

		$request->view()
			->body()
				->data( [
					'channels' => $channels,
					'channel'  => $channel,
					'dates'    => $dates,
					'date'     => $date,
					'content'  => ( $channel && $date ) ? $this->readLog( $channel, $date ) : '',
				] )
				->add( 'system.logs' );

		$request->view()->render();

	}

	private function listBackups() : array {

		$dir = APP_DIR . '/backup';

		if( ! is_dir( $dir ) ){
			return [];
		}

		$items = [];

		foreach( glob( $dir . '/backup-*.tar.gz' ) ?: [] as $path ){

			$items[] = (object) [
				'name' => basename( $path ),
				'time' => date( 'Y-m-d H:i:s', filemtime( $path ) ),
				'size' => byteConvert( filesize( $path ) ),
			];

		}

		usort( $items, fn( $a, $b ) => strcmp( $b->name, $a->name ) );

		return $items;

	}

	private function backupPath( string $name ) : ?string {

		if( ! preg_match( '/^backup-\d{8}-\d{6}\.tar\.gz$/', $name ) ){
			return null;
		}

		$file = APP_DIR . '/backup/' . $name;

		return is_file( $file ) ? $file : null;

	}

	private function listChannels() : array {

		$dir = APP_DIR . '/logs';

		if( ! is_dir( $dir ) ){
			return [];
		}

		$channels = [];

		foreach( scandir( $dir ) ?: [] as $entry ){

			if( '.' === $entry || '..' === $entry ){
				continue;
			}

			if( is_dir( $dir . '/' . $entry ) ){
				$channels[] = $entry;
			}

		}

		sort( $channels );

		return $channels;

	}

	private function listDates( string $channel ) : array {

		$dir = APP_DIR . '/logs/' . $this->safeChannel( $channel );

		if( ! is_dir( $dir ) ){
			return [];
		}

		$dates = [];

		foreach( glob( $dir . '/*.log' ) ?: [] as $path ){

			$dates[] = basename( $path, '.log' );

		}

		rsort( $dates );

		return $dates;

	}

	private function readLog( string $channel, string $date ) : string {

		if( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $date ) ){
			return '';
		}

		$file = APP_DIR . '/logs/' . $this->safeChannel( $channel ) . '/' . $date . '.log';

		if( ! is_file( $file ) ){
			return '';
		}

		$lines = file( $file, FILE_IGNORE_NEW_LINES ) ?: [];

		if( count( $lines ) > self::LOG_TAIL ){
			$lines = array_slice( $lines, -self::LOG_TAIL );
		}

		return join( "\n", $lines );

	}

	private function safeChannel( string $channel ) : string {

		return preg_replace( '/[^a-z0-9_-]/', '', strtolower( $channel ) );

	}

}
