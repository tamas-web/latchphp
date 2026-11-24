<?php

namespace App\Traits;

use DateTime;
use DateTimeImmutable;

trait humanReadableTimePassedSince {

	public function passedTimeSince( int $timeExpire ){

		$date  = new DateTimeImmutable();
		$date2 = new DateTime();

		$date2->setTimestamp( $timeExpire );

		$diff = $date->diff( $date2 );

		if( $diff->y ){

			return 1 === $diff->y
				? 'un an'
				: sprintf( '%u ani', $diff->y );

		} elseif( $diff->m ){

			return 1 === $diff->m
				? 'o luna'
				: sprintf( '%u luni', $diff->m );

		} elseif( $diff->d ){

			return 1 === $diff->d
				? 'o zi'
				: sprintf( '%s zile', $diff->d >= 20 ? $diff->d . ' de' : $diff->d );

		} elseif( $diff->h ){

			return 1 === $diff->h
				? 'o ora'
				: sprintf( '%s ore', $diff->h >= 20 ? $diff->h . ' de' : $diff->h );

		} elseif( $diff->i ){

			return 1 === $diff->i
				? 'un minut'
				: sprintf( '%s minute', $diff->i >= 20 ? $diff->i . ' de' : $diff->i );

		} elseif( $diff->s ){

			return 1 === $diff->s
				? 'o secunda'
				: sprintf( '%s secunde', $diff->s >= 20 ? $diff->s . ' de' : $diff->s );

		} elseif( $diff->f ){

			return 'o secunda';

		} else {

			// default
			return sprintf( '%u secunde', $date2->getTimestamp() - $date->getTimestamp() );

		}

	}

}
