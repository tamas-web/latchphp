<?php

use App\Config;
use App\Http\{ URL, Request };

$brand   = esc_html( Config::get( 'app.site.name' ) );
$user    = Request::getInstance()->user();
$name    = esc_html( $user->name );
$surname = esc_html( $user->surname );
$initials = strtoupper( mb_substr( (string) $user->name, 0, 1 ) . mb_substr( (string) $user->surname, 0, 1 ) );

?>

<header class="topbar">

	<a class="brand-wordmark" href="<?php echo URL::BUILD(); ?>">
		<svg width="20" height="20" viewBox="0 0 24 24" fill="none" style="stroke:var(--lp-accent)" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
			<path d="M7 4 H5 a2 2 0 0 0 -2 2 v12 a2 2 0 0 0 2 2 h2"/>
			<path d="M17 4 h2 a2 2 0 0 1 2 2 v12 a2 2 0 0 1 -2 2 h-2"/>
			<path d="M9 12 h6"/>
		</svg>
		<?php echo $brand; ?>
	</a>

	<div class="search-wrap">
		<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
		<input type="text" placeholder="Search...">
	</div>

	<div class="topbar-right">

		<button class="icon-btn" type="button" data-theme-toggle aria-label="Toggle theme">
			<svg class="lp-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
			<svg class="lp-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42"/></svg>
		</button>

		<div class="dropdown">
			<button class="icon-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-label="Color scheme">
				<span class="swatch-dot"></span>
			</button>
			<div class="dropdown-menu dropdown-menu-end p-2">
				<div class="accent-grid">
					<?php

					$accents = [
						'lime'       => [ '#b2da62', 'Light lime' ],
						'rosu'       => [ '#da6262', 'Light red' ],
						'albastru'   => [ '#6290da', 'Light blue' ],
						'violet'     => [ '#8e62da', 'Light purple' ],
						'roz'        => [ '#da6294', 'Light pink' ],
						'portocaliu' => [ '#da9662', 'Light orange' ],
					];

					foreach( $accents as $key => $accent ){

						printf(
							'<button class="accent-opt" type="button" data-accent-set="%s" title="%s">'
								. '<span class="sw" style="background:%s"></span>'
								. '<svg class="check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>'
								. '</button>',
							$key,
							esc_html( $accent[1] ),
							$accent[0]
						);

					}

					?>
				</div>
			</div>
		</div>

		<div class="dropdown">
			<button class="icon-btn" type="button" data-bs-toggle="dropdown" aria-label="Notifications">
				<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
				<?php if( $this->data->headerNotifications->new ){ ?>
					<span class="lp-notif-badge"><?php echo (int) $this->data->headerNotifications->new; ?></span>
				<?php } ?>
			</button>
			<div class="dropdown-menu dropdown-menu-end" style="min-width:18rem">
				<h6 class="dropdown-header">Notifications</h6>
				<?php if( $this->data->headerNotifications->items ){ ?>
					<?php foreach( $this->data->headerNotifications->items as $item ){

						$message = $item->message;

						if( $item->unseen ){
							$message = '<span class="badge text-bg-primary me-1">New</span> ' . $message;
						}

						if( $item->sticky ){
							$message = '<i class="bi bi-paperclip me-1"></i> ' . $message;
						}

						printf(
							'<a class="dropdown-item text-wrap" href="%s" data-id="%s">%s<br><small class="text-muted">%s</small></a>',
							$item->url, $item->id, $message, esc_html( $item->created_at )
						);

					} ?>
					<div class="dropdown-divider"></div>
				<?php } else { ?>
					<span class="dropdown-item-text text-muted">No notifications.</span>
				<?php } ?>
				<a class="dropdown-item text-center" href="<?php echo URL::BUILD( 'notifications' ); ?>">View all notifications</a>
			</div>
		</div>

		<div class="dropdown">
			<button class="user-chip" type="button" data-bs-toggle="dropdown" aria-expanded="false">
				<span class="avatar"><?php echo esc_html( $initials ); ?></span>
				<span style="font-weight:600;font-size:13px;"><?php printf( '%s %s', $name, $surname ); ?></span>
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
			</button>
			<ul class="dropdown-menu dropdown-menu-end">
				<li><span class="dropdown-item-text small text-muted"><?php echo $user->isSuperAdmin() ? 'Super administrator' : esc_html( $user->roleName() ); ?></span></li>
				<li><hr class="dropdown-divider"></li>
				<li><a class="dropdown-item" href="<?php echo URL::BUILD( 'profile' ); ?>">Profile</a></li>
				<li><a class="dropdown-item" href="<?php echo URL::BUILD( 'logout' ); ?>" data-confirm="Do you want to log out?">Log out</a></li>
			</ul>
		</div>

	</div>

</header>
