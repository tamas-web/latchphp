<?php

use App\Http\URL;

$cards = [
	[
		'icon'  => 'bi-people',
		'title' => 'Roles',
		'desc'  => 'Create and edit account roles.',
		'url'   => 'admin/settings/roles-permissions/roles',
	],
	[
		'icon'  => 'bi-shield-check',
		'title' => 'Permissions',
		'desc'  => 'Browse the available permissions.',
		'url'   => 'admin/settings/roles-permissions/permissions',
	],
];

?>

<div class="row g-3">

	<?php foreach( $cards as $card ){ ?>
		<div class="col-12 col-sm-6 col-xl-4">
			<a class="lp-card lp-link-card d-block text-decoration-none h-100" href="<?php echo URL::BUILD( $card['url'] ); ?>">
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
