<?php

namespace App;

interface Constants {

	const

		// TIME DEFAULTS
		SECOND                           = 1,
		MINUTE                           = 60  * self::SECOND,
		HOUR                             = 60  * self::MINUTE,
		DAY                              = 24  * self::HOUR,
		WEEK                             = 7   * self::DAY,
		MONTH                            = 30  * self::DAY,
		YEAR                             = 365 * self::DAY,

		// ENCRYPTION SETTINGS
		PASSWORD_ENCRYPTION_TYPE         = PASSWORD_ARGON2ID,
		PASSWORD_ENCRYPTION_SETTINGS     = [
			'memory_cost' => PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
			'time_cost'   => PASSWORD_ARGON2_DEFAULT_TIME_COST,
			'threads'     => PASSWORD_ARGON2_DEFAULT_THREADS,
		],
		PUBLIC_PRIVATE_KEY_LENGTH        = 32,

		// ALERT
		ALERT_INFO                       = 1,
		ALERT_SUCCESS                    = 2,
		ALERT_WARNING                    = 3,
		ALERT_ERROR                      = 4,

		// ACCOUNT (ACCOUNT_ADMIN is the super-admin / god-mode bit; other roles live in the DB)
		ACCOUNT_ENABLED                  = 0x1,
		ACCOUNT_EMAIL_CONFIRMED          = 0x2,
		ACCOUNT_PHONE_CONFIRMED          = 0x4,
		ACCOUNT_ADMIN                    = 0x10,

		// NOTIFICATIONS
		NOTIFICATION_SEEN                = 0x1,
		NOTIFICATION_STICKY              = 0x2,
		NOTIFICATION_FOR_USER            = 0x4,
		NOTIFICATION_FOR_MODERATOR       = 0x8,
		NOTIFICATION_FOR_ADMIN           = 0x10;

}
