<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles reading time calculation logic.
 */
class MinuteRead_Core {

	/**
	 * Calculate reading time for given content.
	 *
	 * @param  string $content Post content.
	 * @return int    Reading time in minutes (minimum 1).
	 */
	public static function get_reading_time( $content ) {

		// Remove shortcodes like [gallery].
		$content = strip_shortcodes( $content );

		// Remove HTML tags.
		$clean = wp_strip_all_tags( $content );

		// Default word count (works for Latin scripts).
		$word_count = str_word_count( $clean );

		// Fallback for non-Latin languages (Bangla, Arabic, CJK etc.).
		if ( 0 === $word_count ) {
			$word_count = count( preg_split( '/[\s\p{Z}]+/u', $clean, -1, PREG_SPLIT_NO_EMPTY ) );
		}

		// Allow developers to modify word count.
		$word_count = apply_filters( 'minuteread_word_count', $word_count, $content );

		// Get WPM from settings.
		$wpm = absint( get_option( 'minuteread_wpm', 200 ) );
		if ( $wpm <= 0 ) {
			$wpm = 200;
		}

		// Calculate reading time.
		$reading_time = (int) ceil( $word_count / $wpm );

		// Ensure minimum 1 minute.
		$reading_time = max( 1, $reading_time );

		// Allow modification of final time.
		return apply_filters( 'minuteread_reading_time_output', $reading_time, $word_count );
	}
}
