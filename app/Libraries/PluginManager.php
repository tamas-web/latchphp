<?php

namespace App\Libraries;

/**
 * Plugin loading (boot) + discovery and admin operations (settings page).
 *
 * Boot path  - load() reads plugins/plugins.json and run()s each ACTIVE plugin using the
 *              loader FQCN stored in the registry. No filesystem scan, no meta.json reads.
 * Settings   - discover() globs plugins/*\/meta.json for the management UI / admin ops only.
 *
 * plugins.json holds ONLY installed plugins: { "Name": { "active": bool, "loader": "FQCN" } }.
 * Presence in the file == installed. Non-fatal throughout (a broken plugin is logged & skipped).
 */
class PluginManager {

	protected string $dir;          // APP_DIR/plugins
	protected string $registryFile; // APP_DIR/plugins/plugins.json
	protected string $assetsDir;    // APP_DIR/public/assets/plugins

	protected array $available = []; // name => meta (stdClass: name, version, description, loader)
	protected array $loaded    = []; // name => Plugin instance (booted)

	public function __construct(){
		$this->dir          = APP_DIR . '/plugins';
		$this->registryFile = $this->dir . '/plugins.json';
		$this->assetsDir    = APP_DIR . '/public/assets/plugins';
	}

	/* ------------------------------------------------------------------ boot path */

	/** Read plugins.json and run() each ACTIVE plugin. No filesystem scan. */
	public function load() : self {

		foreach( $this->state() as $name => $entry ){

			if( empty( $entry['active'] ) ){
				continue;
			}

			$plugin = $this->instantiate( $entry['loader'] ?? '' );

			if( $plugin ){
				$plugin->run();
				$this->loaded[ $name ] = $plugin;
			}

		}

		return $this;

	}

	/* -------------------------------------------------------------- settings path */

	/** Scan plugins/*\/meta.json into $available (settings page / admin ops only). */
	public function discover() : self {

		$this->available = [];

		foreach( glob( $this->dir . '/*/meta.json' ) as $file ){

			$meta = json_decode( (string) file_get_contents( $file ) );

			if( $meta && ! empty( $meta->name ) && ! empty( $meta->loader ) ){
				$this->available[ $meta->name ] = $meta;
			}

		}

		return $this;

	}

	/* ---------------------------------------------------------- admin operations */

	/** available & not installed -> install() + publish assets -> add { active:false, loader }. */
	public function install( string $name ) : bool {

		$meta = $this->available[ $name ] ?? null;

		if( ! $meta || $this->isInstalled( $name ) ){
			return false;
		}

		$plugin = $this->instantiate( $meta->loader, $meta );

		if( ! $plugin ){
			return false;
		}

		$plugin->install();
		$this->publishAssets( $name );

		$state = $this->state();
		$state[ $name ] = [
			'active' => false,
			'loader' => $meta->loader,
		];

		return $this->persist( $state );

	}

	/** installed -> (deactivated() if active) -> uninstall() + remove assets -> remove the entry. */
	public function uninstall( string $name ) : bool {

		$state = $this->state();

		if( ! isset( $state[ $name ] ) ){
			return false;
		}

		$plugin = $this->instantiate( $state[ $name ]['loader'] ?? '', $this->available[ $name ] ?? null );

		if( $plugin ){

			if( ! empty( $state[ $name ]['active'] ) ){
				$plugin->deactivated();
			}

			$plugin->uninstall();

		}

		$this->removeAssets( $name );

		unset( $state[ $name ] );

		return $this->persist( $state );

	}

	/** installed & !active -> set active:true -> activated(). */
	public function activate( string $name ) : bool {

		$state = $this->state();

		if( ! isset( $state[ $name ] ) || ! empty( $state[ $name ]['active'] ) ){
			return false;
		}

		$state[ $name ]['active'] = true;

		if( ! $this->persist( $state ) ){
			return false;
		}

		$plugin = $this->instantiate( $state[ $name ]['loader'] ?? '', $this->available[ $name ] ?? null );

		if( $plugin ){
			$plugin->activated();
		}

		return true;

	}

	/** installed & active -> set active:false -> deactivated(). */
	public function deactivate( string $name ) : bool {

		$state = $this->state();

		if( empty( $state[ $name ]['active'] ) ){
			return false;
		}

		$state[ $name ]['active'] = false;

		if( ! $this->persist( $state ) ){
			return false;
		}

		$plugin = $this->instantiate( $state[ $name ]['loader'] ?? '', $this->available[ $name ] ?? null );

		if( $plugin ){
			$plugin->deactivated();
		}

		return true;

	}

	/* ---------------------------------------------------------------- upload path */

	/**
	 * Receive a .zip upload (via App\Libraries\Upload), extract it under plugins/, validate it, and
	 * on success install() + register it (inactive). Rolls back (removes the extracted folder) on any
	 * validation failure, so an invalid upload is never registered or activated.
	 *
	 * @param array|null $file  The Request::files('plugin_zip') array ($_FILES entry).
	 * @return array            [ 'ok' => bool, 'name' => ?string, 'errors' => string[] ]
	 */
	public function installFromZip( ?array $file ) : array {

		if( ! $file || empty( $file['tmp_name'] ) ){
			return [
				'ok'     => false,
				'name'   => null,
				'errors' => [ 'No file uploaded.' ],
			];
		}

		// 0. receive + validate (.zip) + save via the framework Upload (writes only under uploads/)
		$upload = ( new Upload )->allowedExtensions( [ 'zip' ] );

		if( ! $upload->file( $file ) || ! $upload->save( 0, 'plugins-upload' ) ){
			return [
				'ok'     => false,
				'name'   => null,
				'errors' => $upload->get_errors() ?: [ 'Invalid upload.' ],
			];
		}

		$zipPath = Upload::get_file_path( 0, 'plugins-upload', $upload->get_generated_name() );

		// 1. open + find the single top-level folder (= plugin name)
		$zip = new \ZipArchive;

		if( true !== $zip->open( $zipPath ) ){
			@unlink( $zipPath );
			return [
				'ok'     => false,
				'name'   => null,
				'errors' => [ 'The ZIP archive could not be opened.' ],
			];
		}

		$name = $this->zipTopDir( $zip );

		if( ! $name ){
			$zip->close();
			@unlink( $zipPath );
			return [
				'ok'     => false,
				'name'   => null,
				'errors' => [ 'The archive must contain a single plugin folder at the root.' ],
			];
		}

		if( is_dir( $this->dir . '/' . $name ) ){
			$zip->close();
			@unlink( $zipPath );
			return [
				'ok'     => false,
				'name'   => $name,
				'errors' => [ sprintf( 'The plugin "%s" already exists on disk.', $name ) ],
			];
		}

		// 2. extract zip-slip-safe into plugins/ (entries live under <name>/)
		if( ! $this->extractSafely( $zip, $this->dir ) ){
			$zip->close();
			@unlink( $zipPath );
			$this->rmTree( $this->dir . '/' . $name );
			return [
				'ok'     => false,
				'name'   => $name,
				'errors' => [ 'Invalid archive content (unsafe path in ZIP).' ],
			];
		}

		$zip->close();
		@unlink( $zipPath );

		$pluginDir = $this->dir . '/' . $name;

		// 3. validate; roll back (remove the folder) on any error
		$errors = $this->validatePackage( $pluginDir );

		if( $errors ){
			$this->rmTree( $pluginDir );
			return [
				'ok'     => false,
				'name'   => $name,
				'errors' => $errors,
			];
		}

		// 4. install + register (inactive)
		$meta   = json_decode( (string) file_get_contents( $pluginDir . '/meta.json' ) );
		$plugin = $this->instantiate( $meta->loader, $meta );

		if( ! $plugin ){
			$this->rmTree( $pluginDir );
			return [
				'ok'     => false,
				'name'   => $name,
				'errors' => [ 'The plugin loader could not be initialized.' ],
			];
		}

		$plugin->install();
		$this->publishAssets( $name );

		$state = $this->state();
		$state[ $name ] = [
			'active' => false,
			'loader' => $meta->loader,
		];
		$this->persist( $state );

		return [
			'ok'     => true,
			'name'   => $name,
			'errors' => [],
		];

	}

	/**
	 * Validate an extracted plugin folder (already under plugins/, so its loader autoloads via
	 * PSR-4). Returns [] when valid, else a list of human-readable error messages.
	 */
	public function validatePackage( string $dir ) : array {

		$metaFile = $dir . '/meta.json';

		if( ! is_file( $metaFile ) ){
			return [ 'meta.json is missing from the package.' ];
		}

		$meta = json_decode( (string) file_get_contents( $metaFile ) );

		if( ! $meta ){
			return [ 'meta.json is not valid JSON.' ];
		}

		$errors = [];

		foreach( [ 'name', 'version', 'description', 'loader' ] as $key ){
			if( empty( $meta->{ $key } ) ){
				$errors[] = sprintf( 'meta.json: the required key "%s" is missing.', $key );
			}
		}

		if( $errors ){
			return $errors;
		}

		// loader: exists (autoloads under plugins/), extends Plugin, has run() -> "can run if activated"
		if( ! class_exists( $meta->loader ) ){
			$errors[] = sprintf( 'The loader class "%s" was not found (check the namespace / folder name).', $meta->loader );
		} elseif( ! is_subclass_of( $meta->loader, Plugin::class ) ){
			$errors[] = sprintf( 'The class "%s" does not extend App\\Libraries\\Plugin.', $meta->loader );
		} elseif( ! method_exists( $meta->loader, 'run' ) ){
			$errors[] = sprintf( 'The class "%s" does not have a run() method.', $meta->loader );
		}

		return $errors;

	}

	/* ------------------------------------------------------------------- queries */

	public function isInstalled( string $name ) : bool {
		return isset( $this->state()[ $name ] );
	}

	public function isActive( string $name ) : bool {
		return ! empty( $this->state()[ $name ]['active'] );
	}

	public function getAvailable() : array {
		return $this->available;
	}

	public function getLoaded() : array {
		return $this->loaded;
	}

	/** Merge discover() + registry for the UI. Requires discover() first. */
	public function listForUi() : array {

		$state = $this->state();
		$rows  = [];

		// plugins found on disk (discover())
		foreach( $this->available as $name => $meta ){

			$rows[ $name ] = [
				'meta'      => $meta,
				'installed' => isset( $state[ $name ] ),
				'active'    => ! empty( $state[ $name ]['active'] ),
				'missing'   => false,
			];

		}

		// registry entries with no folder on disk (orphans)
		foreach( $state as $name => $entry ){

			if( ! isset( $rows[ $name ] ) ){

				$rows[ $name ] = [
					'meta'      => (object) [
						'name'        => $name,
						'version'     => '',
						'description' => '',
						'loader'      => $entry['loader'] ?? '',
					],
					'installed' => true,
					'active'    => ! empty( $entry['active'] ),
					'missing'   => true,
				];

			}

		}

		uksort( $rows, 'strcasecmp' );

		return $rows;

	}

	/* ----------------------------------------------------------------- internals */

	protected function instantiate( string $loader, ?object $meta = null ) : ?Plugin {

		if( ! $loader || ! class_exists( $loader ) ){
			error_log( sprintf( 'PluginManager: loader class not found: %s', $loader ) );
			return null;
		}

		$plugin = new $loader;

		if( ! $plugin instanceof Plugin ){
			error_log( sprintf( 'PluginManager: %s does not extend App\\Libraries\\Plugin', $loader ) );
			return null;
		}

		if( $meta ){
			$plugin->setMeta( $meta );
		}

		return $plugin;

	}

	/** decode plugins.json -> [ name => [ active, loader ] ]. Missing/invalid -> empty. */
	protected function state() : array {

		if( ! is_file( $this->registryFile ) ){
			return [];
		}

		$data = json_decode( (string) file_get_contents( $this->registryFile ), true );

		return is_array( $data ) ? $data : [];

	}

	/** Atomic write of plugins.json (temp file + rename) so a boot read never sees a partial file. */
	protected function persist( array $state ) : bool {

		$json = json_encode( $state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );

		if( false === $json ){
			return false;
		}

		// auto-create the plugins/ dir so the first write (e.g. the first install on a fresh app
		// with no plugins.json yet) succeeds even if the directory does not exist.
		if( ! is_dir( $this->dir ) ){
			mkdir( $this->dir, 0775, true );
		}

		$tmp = $this->registryFile . '.tmp';

		if( false === file_put_contents( $tmp, $json, LOCK_EX ) ){
			return false;
		}

		return rename( $tmp, $this->registryFile );

	}

	/* -------------------------------------------------------------------- assets */

	/** Copy plugins/<name>/assets -> public/assets/plugins/<name> (if the source exists). */
	protected function publishAssets( string $name ) : void {

		$source = $this->dir . '/' . $name . '/assets';
		$target = $this->assetsDir . '/' . $name;

		if( is_dir( $source ) ){
			$this->copyDir( $source, $target );
		}

	}

	/** Delete public/assets/plugins/<name> (guarded so we only ever touch inside that base). */
	protected function removeAssets( string $name ) : void {

		$target = realpath( $this->assetsDir . '/' . $name );
		$base   = realpath( $this->assetsDir );

		if( $target && $base && str_starts_with( $target, $base . DIRECTORY_SEPARATOR ) ){
			$this->deleteDir( $target );
		}

	}

	protected function copyDir( string $src, string $dst ) : void {

		if( ! is_dir( $dst ) ){
			mkdir( $dst, 0775, true );
		}

		foreach( scandir( $src ) as $item ){

			if( '.' === $item || '..' === $item ){
				continue;
			}

			$from = $src . '/' . $item;
			$to   = $dst . '/' . $item;

			is_dir( $from ) ? $this->copyDir( $from, $to ) : copy( $from, $to );

		}

	}

	protected function deleteDir( string $dir ) : void {

		foreach( scandir( $dir ) as $item ){

			if( '.' === $item || '..' === $item ){
				continue;
			}

			$path = $dir . '/' . $item;

			is_dir( $path ) ? $this->deleteDir( $path ) : unlink( $path );

		}

		rmdir( $dir );

	}

	/* --------------------------------------------------------------- zip helpers */

	/** The single top-level folder in the zip (= plugin name), or null if not exactly one. */
	protected function zipTopDir( \ZipArchive $zip ) : ?string {

		$top = null;

		for( $i = 0; $i < $zip->numFiles; $i++ ){

			$entry = $zip->getNameIndex( $i );

			if( ! $entry || str_starts_with( $entry, '__MACOSX/' ) || str_starts_with( $entry, '.' ) ){
				continue;
			}

			$seg = explode( '/', trim( $entry, '/' ) )[0] ?? '';

			if( '' === $seg ){
				continue;
			}

			if( null === $top ){
				$top = $seg;
			} elseif( $top !== $seg ){
				return null;   // more than one top-level entry -> not a single plugin folder
			}

		}

		return $top;

	}

	/** Extract every entry under $base, rejecting zip-slip (.., backslash, absolute, escape). */
	protected function extractSafely( \ZipArchive $zip, string $base ) : bool {

		$baseReal = realpath( $base );

		for( $i = 0; $i < $zip->numFiles; $i++ ){

			$entry = $zip->getNameIndex( $i );

			if( false === $entry || str_starts_with( $entry, '__MACOSX/' ) ){
				continue;
			}

			if( str_contains( $entry, '..' ) || str_contains( $entry, '\\' ) || str_starts_with( $entry, '/' ) ){
				return false;
			}

			$target = $base . '/' . $entry;

			if( str_ends_with( $entry, '/' ) ){
				if( ! is_dir( $target ) && ! mkdir( $target, 0775, true ) ){
					return false;
				}
				continue;
			}

			$dir = dirname( $target );

			if( ! is_dir( $dir ) && ! mkdir( $dir, 0775, true ) ){
				return false;
			}

			// containment: the parent dir must resolve inside $base
			$realDir = realpath( $dir );

			if( ! $baseReal || ! $realDir || ! str_starts_with( $realDir, $baseReal ) ){
				return false;
			}

			$stream = $zip->getStream( $entry );

			if( ! $stream ){
				return false;
			}

			file_put_contents( $target, $stream );
			fclose( $stream );

		}

		return true;

	}

	/** Recursively remove a directory if it exists (used for upload rollback). */
	protected function rmTree( string $dir ) : void {

		if( is_dir( $dir ) ){
			$this->deleteDir( $dir );
		}

	}

}
