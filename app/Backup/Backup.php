<?php

namespace App\Backup;

use App\Config;
use DateTimeImmutable;
use Exception;
use FtpClient\FtpClient;

class Backup {

	private DateTimeImmutable $date;

	private string $dump = '';

	public function __construct(){

		$this->date = new DateTimeImmutable;
		$this->dump = sprintf( '%s/%s-%s.sql', APP_DIR, Config::get( 'database.database' ), $this->date->format( 'Ymd-His' ) );

		$this->mkBackupDir();

		try {

			$this->backupDb();
			$this->backupSource();
			$this->uploadBackup();

		} finally {

			@unlink( $this->dump );
			@unlink( $this->dump . '.gz' );

		}

	}

	public function mkBackupDir(){

		if( ! file_exists( APP_DIR . '/backup' ) ){
			mkdir( APP_DIR . '/backup' );
		}

	}

	public function backupDb(){

		$tmp = tempnam( sys_get_temp_dir(), 'bk' );

		try {

			file_put_contents( $tmp, sprintf(
				"[client]\nuser=%s\npassword=%s\n",
				Config::get( 'database.username' ),
				Config::get( 'database.password' )
			) );

			chmod( $tmp, 0600 );

			$this->execute(
				'%s --defaults-extra-file=%s --single-transaction %s > %s',
				'Error generating SQL backup.',
				$this->dumpBinary(),
				$tmp,
				Config::get( 'database.database' ),
				$this->dump
			);

		} finally {

			unlink( $tmp );

		}

		$this->execute(
			'gzip -f %s',
			'Error compressing SQL backup.',
			$this->dump
		);

	}

	private function dumpBinary() : string {

		if( $this->hasCommand( 'mariadb-dump' ) ){

			return 'mariadb-dump';

		}

		if( $this->hasCommand( 'mysqldump' ) ){

			return 'mysqldump';

		}

		throw new Exception( 'No SQL dump tool found ( mariadb-dump or mysqldump ).' );

	}

	public function backupSource(){

		$this->execute(
			'tar --exclude="./backup" --exclude-backups --exclude-vcs -czf %1$s/backup/backup-%2$s.tar.gz -C %1$s .',
			'Error generating archive.',
			APP_DIR,
			$this->date->format( 'Ymd-His' )
		);

	}

    public function uploadBackup(){

		$ftp = new FtpClient;

		$ftp->connect( Config::get( 'app.ftp.hostname' ) );

		$ftp->login( Config::get( 'app.ftp.username' ), Config::get( 'app.ftp.password' ) );

		$ftp->mkdir( Config::get( 'app.cookie.domain' ), true );

		$ftp->chdir( Config::get( 'app.cookie.domain' ) );

		$ftp->putFromPath( APP_DIR . sprintf( '/backup/backup-%s.tar.gz', $this->date->format( 'Ymd-His' ) ) );

    }

	private function shell( string $command, string ...$params ) : object {

		exec( vsprintf( $command, array_map( fn( $param ) => escapeshellarg( $param ), $params ) ), $output, $code );

		return (object) [
			'output' => $output,
			'code'   => $code,
		];

	}

	private function execute( string $command, string $error, string ...$params ) : array {

		$result = $this->shell( $command, ...$params );

		if( $result->code ){

			throw new Exception( $error, $result->code );

		}

		return $result->output;

	}

	private function hasCommand( string $binary ) : bool {

		return 0 === $this->shell( 'command -v %s', $binary )->code;

	}

}