<?php
/**
 * Idempotent ACL + demo-data seeder.
 *
 *   php database/seed.php
 *
 * Seeds: the 3 base roles (with rank levels), the permission catalog
 * (view/add/edit/delete per resource), the role -> permission matrix,
 * 1 dedicated super-admin + 10 random-role users, and 1-5 random
 * notifications per account.
 *
 * Default password for every seeded account: "Password123!"
 * Re-running is safe (roles upsert, permissions/role_permissions INSERT IGNORE,
 * users skip existing emails, notifications skip accounts that already have some).
 */

use App\Bootloader;
use App\Constants;
use App\Database\Connector as DB;
use App\Libraries\Password;

if( ! version_compare( PHP_VERSION, '8.0', '>=' ) ){

	exit( 'PHP >= 8.0 required.' . PHP_EOL );

}

if( 'cli' !== PHP_SAPI ){

	exit( 'This seeder must be run from the command line.' . PHP_EOL );

}

define( 'APP_STARTUP_TIME', microtime( true ) );
define( 'APP_DIR', dirname( __DIR__ ) );

require_once APP_DIR . '/vendor/autoload.php';

define( 'APP_ENVIRONMENT', env( 'APP_ENV', 'production' ) );

( new Bootloader )->initKernel();

$db = DB::getInstance();

const DEFAULT_PASSWORD = 'Password123!';

/* ---- roles ---- */
$roleNames = [ 'Administrator', 'Moderator', 'User' ];
$roleIds   = [];

foreach( $roleNames as $name ){

	$db->query( sprintf(
		"INSERT IGNORE INTO roles ( name, created_at ) VALUES ( '%s', NOW() )",
		$db->real_escape_string( $name )
	) );

	$res = $db->query( sprintf( "SELECT id FROM roles WHERE name = '%s' LIMIT 1", $db->real_escape_string( $name ) ) );

	$roleIds[ $name ] = (int) ( $res->fetch_row()[0] ?? 0 );

}

echo 'roles: ', json_encode( $roleIds ), PHP_EOL;

/* ---- permission catalog: group => label + [ action => label ] ---- */
$catalog = [
	'dashboard' => [
		'label'    => 'Dashboard',
		'children' => [
			'dashboard.view' => 'View dashboard',
		],
	],
	'users' => [
		'label'    => 'Users',
		'children' => [
			'users.view'       => 'View users',
			'users.add'        => 'Add users',
			'users.edit'       => 'Edit users',
			'users.delete'     => 'Delete users',
			'users.grant.role' => 'Assign role to users',
		],
	],
	'roles' => [
		'label'    => 'Roles and permissions',
		'children' => [
			'roles.view'   => 'View roles',
			'roles.add'    => 'Add roles',
			'roles.edit'   => 'Edit roles',
			'roles.delete' => 'Delete roles',
		],
	],
	'plugins' => [
		'label'    => 'Plugins',
		'children' => [
			'plugins.view'     => 'View plugins',
			'plugins.add'      => 'Install plugins',
			'plugins.delete'   => 'Uninstall plugins',
			'plugins.activate' => 'Activate plugins',
			'plugins.disable'  => 'Deactivate plugins',
		],
	],
	'settings' => [
		'label'    => 'Application settings',
		'children' => [
			'settings.manage' => 'Manage application settings',
		],
	],
	'system' => [
		'label'    => 'System',
		'children' => [
			'system.backup' => 'View backups',
			'system.logs'   => 'View logs',
		],
	],
	// Notifications are per-user (each user manages their own) - not ACL-gated.
];

// upsert a permission by name (idempotent): refresh its label/parent if it exists, else insert
$permId = function( string $name, string $label, int $parent = 0 ) use ( $db ) : int {

	$res = $db->query( sprintf( "SELECT id FROM permissions WHERE name = '%s' LIMIT 1", $db->real_escape_string( $name ) ) );

	if( $row = $res->fetch_row() ){

		$id = (int) $row[0];

		$db->query( sprintf(
			"UPDATE permissions SET label = '%s', id_parent = %d WHERE id = %d",
			$db->real_escape_string( $label ), $parent, $id
		) );

		return $id;

	}

	$db->query( sprintf(
		"INSERT INTO permissions ( id_parent, name, label, created_at ) VALUES ( %d, '%s', '%s', NOW() )",
		$parent, $db->real_escape_string( $name ), $db->real_escape_string( $label )
	) );

	return (int) $db->insert_id;

};

$perm = [];   // name => id (groups AND actions)

foreach( $catalog as $group => $data ){

	$perm[ $group ] = $permId( $group, $data['label'] );   // parent/resource row (id_parent = 0)

	foreach( $data['children'] as $name => $label ){

		$perm[ $name ] = $permId( $name, $label, $perm[ $group ] );

	}

}

echo 'permissions: ', count( $perm ), ' (', count( $catalog ), ' groups + ', count( $perm ) - count( $catalog ), ' actions)', PHP_EOL;

/* ---- role -> permission matrix ---- */
// A granted group encapsulates all its actions; granting individual leaves is finer-grained.
// roles.* is a delegatable permission: grant it to any role (e.g. a manager) to let it
// manage roles/permissions - it is not reserved to the super-admin bit.
$matrix = [
	'Administrator' => [ 'dashboard', 'users', 'roles', 'plugins', 'settings', 'system' ],   // full user + role management
	'Moderator'     => [ 'dashboard', 'users.view', 'users.add', 'users.edit' ],  // manage users, but not delete
	'User'          => [ 'dashboard' ],                                           // dashboard only
];

foreach( $matrix as $role => $perms ){

	$rid = $roleIds[ $role ];

	foreach( $perms as $p ){

		$db->query( sprintf(
			"INSERT IGNORE INTO role_permissions ( id_role, id_permission, created_at ) VALUES ( %d, %d, NOW() )",
			$rid, $perm[ $p ]
		) );

	}

}

echo 'role_permissions seeded', PHP_EOL;

$db->query( "INSERT INTO settings ( `key`, `value` ) VALUES
	( 'app',   '{\"pagination\":{\"limit\":25}}' ),
	( 'users', '{\"phone\":{\"enabled\":false,\"required\":false}}' )
	ON DUPLICATE KEY UPDATE `key` = `key`" );

echo 'settings seeded', PHP_EOL;

/* ---- users ---- */
$hash    = Password::hash( DEFAULT_PASSWORD );
$keyset  = fn() => bin2hex( random_bytes( (int) ( Constants::PUBLIC_PRIVATE_KEY_LENGTH / 2 ) ) );   // 32 hex chars
$baseOpt = Constants::ACCOUNT_ENABLED | Constants::ACCOUNT_EMAIL_CONFIRMED | Constants::ACCOUNT_PHONE_CONFIRMED;

$createUser = function( string $name, string $surname, string $email, string $phone, int $idRole, int $options )
	use ( $db, $hash, $keyset ) : int {

	// skip if the email already exists (idempotent)
	$res = $db->query( sprintf( "SELECT id FROM users WHERE email = '%s' LIMIT 1", $db->real_escape_string( $email ) ) );

	if( $row = $res->fetch_row() ){

		return (int) $row[0];

	}

	$db->query( sprintf(
		"INSERT INTO users ( id_role, name, surname, phone, email, public_key, private_key, password, options, created_at )
		 VALUES ( %d, '%s', '%s', '%s', '%s', '%s', '%s', '%s', %d, NOW() )",
		$idRole,
		$db->real_escape_string( $name ),
		$db->real_escape_string( $surname ),
		$db->real_escape_string( $phone ),
		$db->real_escape_string( $email ),
		$keyset(), $keyset(),
		$db->real_escape_string( $hash ),
		$options
	) );

	return (int) $db->insert_id;

};

$userIds = [];

// 1 dedicated super-admin (god mode + Administrator role)
$userIds[] = $createUser( 'Super', 'Admin', 'superadmin@example.com', '0700000000',
	$roleIds['Administrator'], $baseOpt | Constants::ACCOUNT_ADMIN );

// 10 random-role users; the first 3 guarantee at least one of each role
$assignable = array_keys( $roleIds );

for( $i = 1; $i <= 10; $i++ ){

	$role = $i <= count( $assignable )
		? $assignable[ $i - 1 ]
		: $assignable[ random_int( 0, count( $assignable ) - 1 ) ];

	$userIds[] = $createUser(
		sprintf( 'User%02d', $i ), 'Demo',
		sprintf( 'user%02d@example.com', $i ),
		sprintf( '07%08d', random_int( 0, 99999999 ) ),
		$roleIds[ $role ], $baseOpt
	);

}

echo 'users: ', count( $userIds ), ' (default password: ', DEFAULT_PASSWORD, ')', PHP_EOL;

/* ---- random notifications per user ---- */
$messages = [
	'Welcome to the application!',
	'Your profile has been updated.',
	'A new session was started on your account.',
	'You have a new task to review.',
	'Your password will expire soon.',
	'A new report is available.',
];

$notifTotal = 0;

foreach( $userIds as $uid ){

	if( ! $uid ){

		continue;

	}

	// skip accounts that already have notifications (idempotent re-runs)
	$res = $db->query( sprintf( "SELECT COUNT(*) FROM notifications WHERE id_user = %d", $uid ) );

	if( (int) ( $res->fetch_row()[0] ?? 0 ) > 0 ){

		continue;

	}

	$n = random_int( 1, 5 );

	for( $k = 0; $k < $n; $k++ ){

		$opts = Constants::NOTIFICATION_FOR_USER;

		if( random_int( 0, 1 ) ){ $opts |= Constants::NOTIFICATION_SEEN; }
		if( random_int( 0, 4 ) === 0 ){ $opts |= Constants::NOTIFICATION_STICKY; }

		$daysAgo = random_int( 0, 30 );
		$msg     = $messages[ random_int( 0, count( $messages ) - 1 ) ];

		$db->query( sprintf(
			"INSERT INTO notifications ( id_user, message, details, options, created_at )
			 VALUES ( %d, '%s', '%s', %d, DATE_SUB( NOW(), INTERVAL %d DAY ) )",
			$uid, $db->real_escape_string( $msg ),
			$db->real_escape_string( 'Details for: ' . $msg ),
			$opts, $daysAgo
		) );

		$notifTotal++;

	}

}

echo 'notifications: ', $notifTotal, PHP_EOL;
echo 'DONE.', PHP_EOL;
