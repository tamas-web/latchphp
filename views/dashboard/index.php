<?php

use App\Http\URL;

$userCount = (int) $this->data->userCount;

$groups = [
	[
		'title' => 'Styleguide',
		'sub'   => 'Foundations - typography, colours and content elements.',
		'cards' => [
			[
				'icon'  => 'bi-type',
				'title' => 'Content & Typography',
				'desc'  => 'Headings, text, lists, quotes.',
				'url'   => '/styleguide/content',
			],
		],
	],
	[
		'title' => 'UI Elements',
		'sub'   => 'Form layouts and data tables.',
		'cards' => [
			[
				'icon'  => 'bi-input-cursor-text',
				'title' => 'Forms - Stacked',
				'desc'  => 'Vertical form controls.',
				'url'   => '/styleguide/forms-stacked',
			],
			[
				'icon'  => 'bi-distribute-horizontal',
				'title' => 'Forms - Horizontal',
				'desc'  => 'Label / field columns.',
				'url'   => '/styleguide/forms-horizontal',
			],
			[
				'icon'  => 'bi-table',
				'title' => 'Tables',
				'desc'  => 'Table styles and states.',
				'url'   => '/styleguide/tables',
			],
		],
	],
	[
		'title' => 'Components',
		'sub'   => 'Reusable interface building blocks.',
		'cards' => [
			[
				'icon'  => 'bi-ui-checks-grid',
				'title' => 'Components',
				'desc'  => 'Buttons, alerts, modals, tabs.',
				'url'   => '/styleguide/components',
			],
			[
				'icon'  => 'bi-grid-1x2',
				'title' => 'Widgets',
				'desc'  => 'Stat cards and widget tiles.',
				'url'   => '/styleguide/widgets',
			],
			[
				'icon'  => 'bi-chat-square-dots',
				'title' => 'Feedback & Overlays',
				'desc'  => 'Callouts, tooltips, offcanvas, more.',
				'url'   => '/styleguide/feedback',
			],
		],
	],
];

?>

<div class="row g-3 mb-4">

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-people"></i> Active users</div>
			<div class="value"><?php echo number_format( $userCount, 0, '.', ',' ); ?></div>
			<div class="delta">Total active accounts</div>
		</div>
	</div>

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-clock-history"></i> Active sessions</div>
			<div class="value">&mdash;</div>
			<div class="delta down">Coming soon</div>
		</div>
	</div>

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-lightning-charge"></i> Requests / min</div>
			<div class="value">&mdash;</div>
			<div class="delta down">Coming soon</div>
		</div>
	</div>

	<div class="col-6 col-xl-3">
		<div class="lp-card stat-card">
			<div class="label"><i class="bi bi-hdd-stack"></i> Storage used</div>
			<div class="value">&mdash;</div>
			<div class="delta down">Coming soon</div>
		</div>
	</div>

</div>

<?php foreach( $groups as $group ){ ?>

	<div class="card-head mt-2">
		<div>
			<h5><?php echo $group['title']; ?></h5>
			<p><?php echo $group['sub']; ?></p>
		</div>
	</div>

	<div class="row g-3 mb-4">
		<?php foreach( $group['cards'] as $card ){ ?>
			<div class="col-12 col-md-6 col-xl-4">
				<a class="lp-card lp-link-card d-block text-decoration-none" href="<?php echo URL::BUILD( $card['url'] ); ?>">
					<div class="d-flex align-items-start gap-3">
						<span class="lp-link-ico"><i class="bi <?php echo $card['icon']; ?>"></i></span>
						<div>
							<div class="lp-link-title"><?php echo $card['title']; ?></div>
							<div class="lp-link-desc"><?php echo $card['desc']; ?></div>
						</div>
						<i class="bi bi-arrow-right ms-auto lp-link-arrow"></i>
					</div>
				</a>
			</div>
		<?php } ?>
	</div>

<?php } ?>
