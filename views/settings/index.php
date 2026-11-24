<?php

use App\Http\Request;
use App\Filters\Filter;

$user = Request::getInstance()->user();

$sections = Filter::apply( 'admin.sections', [
	[
		'title' => 'Settings',
		'cards' => [
			[
				'icon'  => 'bi-sliders',
				'title' => 'Application settings',
				'desc'  => 'General platform configuration.',
				'url'   => '/admin/settings/application',
				'can'   => 'settings.manage',
			],
			[
				'icon'  => 'bi-plugin',
				'title' => 'Plugins',
				'desc'  => 'Install, activate and remove plugins.',
				'url'   => '/admin/settings/plugins',
				'can'   => 'plugins.view',
			],
			[
				'icon'  => 'bi-shield-lock',
				'title' => 'Roles and permissions',
				'desc'  => 'Manage roles and what each can access.',
				'url'   => '/admin/settings/roles-permissions',
				'can'   => 'roles.view',
			],
		],
	],
	[
		'title' => 'System',
		'cards' => [
			[
				'icon'  => 'bi-archive',
				'title' => 'Backups',
				'desc'  => 'Create and download database backups.',
				'url'   => '/admin/system/backup',
				'can'   => 'system.backup',
			],
			[
				'icon'  => 'bi-card-text',
				'title' => 'Logs',
				'desc'  => 'Browse the application log channels.',
				'url'   => '/admin/system/logs',
				'can'   => 'system.logs',
			],
		],
	],
] );

usort( $sections, fn( $a, $b ) => strcasecmp( $a['title'], $b['title'] ) );

?>

<?php foreach( $sections as $section ){ ?>

	<?php

	$cards = array_filter( $section['cards'], fn( $card ) => empty( $card['can'] ) || $user->can( $card['can'] ) );

	if( ! $cards ){
		continue;
	}

	usort( $cards, fn( $a, $b ) => strcasecmp( $a['title'], $b['title'] ) );

	?>

	<div class="mb-4">

		<h5 class="mb-3"><?php echo esc_html( $section['title'] ); ?></h5>

		<div class="row g-3">

			<?php foreach( $cards as $card ){ ?>

				<div class="col-12 col-sm-6 col-xl-3">
					<a class="lp-card lp-link-card d-block text-decoration-none h-100" href="<?php echo esc_url( $card['url'] ); ?>">
						<div class="d-flex align-items-start gap-3">
							<span class="lp-link-ico"><i class="bi <?php echo esc_html( $card['icon'] ); ?>"></i></span>
							<div>
								<div class="lp-link-title"><?php echo esc_html( $card['title'] ); ?></div>
								<div class="lp-link-desc"><?php echo esc_html( $card['desc'] ); ?></div>
							</div>
							<i class="bi bi-arrow-right ms-auto lp-link-arrow"></i>
						</div>
					</a>
				</div>

			<?php } ?>

		</div>

	</div>

<?php } ?>
