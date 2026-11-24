<?php

use App\Config;
use App\Http\{ Request, URL };
use App\Html\MenuItem;
use App\Filters\Filter;

$user  = Request::getInstance()->user();
$brand = esc_html( Config::get( 'app.site.name' ) );

Filter::add( 'app.use_simple_footer', fn() => true );

$settingsItems = [];

if( $user->can( 'settings.manage' ) ){

	$settingsItems[] = [
		'path'  => '/admin/settings/application',
		'title' => 'Application settings',
	];

}

if( $user->can( 'roles.view' ) ){

	$settingsItems[] = [
		'path'  => '/admin/settings/roles-permissions',
		'title' => 'Roles and permissions',
	];

}

if( $user->can( 'plugins.view' ) ){

	$settingsItems[] = [
		'path'  => '/admin/settings/plugins',
		'title' => 'Plugins',
	];

}

$systemItems = [];

if( $user->can( 'system.backup' ) ){

	$systemItems[] = [
		'path'  => '/admin/system/backup',
		'title' => 'Backups',
	];

}

if( $user->can( 'system.logs' ) ){

	$systemItems[] = [
		'path'  => '/admin/system/logs',
		'title' => 'Logs',
	];

}

$adminItems = [];

if( $settingsItems ){

	$adminItems[] = [
		'title' => 'Settings',
		'items' => $settingsItems,
	];

}

if( $systemItems ){

	$adminItems[] = [
		'title' => 'System',
		'items' => $systemItems,
	];

}

$rail = [
	20 => [
		[
			'path'  => '/',
			'title' => 'Home',
			'icon'  => 'bi bi-house',
		],
	],
	40 => [
		[
			'path'  => '/admin/users',
			'title' => 'Users',
			'icon'  => 'bi bi-people',
			'can'   => 'users.view',
			'items' => [
				[
					'title' => 'Users',
					'items' => [
						[
							'path'  => '/admin/users',
							'title' => 'All users',
						],
						[
							'path'  => '/admin/users/add',
							'title' => 'Add user',
						],
					],
				],
			],
		],
	],
];

if( $adminItems ){

	$rail[60] = [
		[
			'path'  => '/admin',
			'title' => 'Administration',
			'icon'  => 'bi bi-gear',
			'items' => $adminItems,
		],
	];

}

$rail = Filter::apply( 'app.menu', $rail );

ksort( $rail );

?>

<!-- ICON RAIL -->
<nav class="icon-rail">

	<a class="rail-logo" href="<?php echo URL::BUILD(); ?>" title="<?php echo $brand; ?>">
		<svg width="22" height="22" viewBox="0 0 24 24" fill="none" style="stroke:var(--lp-accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M7 4 H5 a2 2 0 0 0 -2 2 v12 a2 2 0 0 0 2 2 h2"/>
			<path d="M17 4 h2 a2 2 0 0 1 2 2 v12 a2 2 0 0 1 -2 2 h-2"/>
			<path d="M9 12 h6"/>
			<circle cx="12" cy="12" r="1.3" style="fill:var(--lp-accent)" stroke="none"/>
		</svg>
	</a>

	<div class="rail-nav">

		<?php

		foreach( $rail as $group ){

			foreach( $group as $item ){

				if( ! empty( $item['can'] ) && ! $user->can( $item['can'] ) ){
					continue;
				}

				echo new MenuItem( $item );

			}

		}

		?>

	</div>

	<div class="rail-spacer"></div>

	<?php
	echo new MenuItem( [
		'path'  => '/notifications',
		'title' => 'Notifications',
		'icon'  => 'bi bi-bell',
	] );
	?>

</nav>

<!-- MAIN -->
<div class="main-area">
