<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles reading time calculation logic.
 */
class MinuteRead_Core {



	/**
	 * Count words in a Unicode-aware way.
	 *
	 * str_word_count() is not multibyte safe: on text that mixes a non-Latin
	 * script with a few Latin words it counts only the Latin ones, which made
	 * long Bengali/Arabic posts report "1 min". Scripts that separate words
	 * with spaces are counted by token; CJK scripts, which do not use spaces,
	 * are counted by character and converted at a separate rate.
	 *
	 * @since  1.1.0
	 * @param  string $text Plain text, tags and shortcodes already removed.
	 * @return int    Word count (never negative).
	 */
	protected static function count_words( $text ) {

		// Non-breaking spaces behave as separators.
		$text = preg_replace( '/\x{00A0}/u', ' ', $text );

		/*
		 * Chinese ideographs and Japanese kana only. Korean Hangul is left out
		 * on purpose: Korean separates words with spaces, so it counts correctly
		 * as a normal token-based script.
		 */
		$cjk_pattern = '/[\x{3040}-\x{30FF}\x{3400}-\x{4DBF}\x{4E00}-\x{9FFF}\x{F900}-\x{FAFF}]/u';

		$cjk_count = (int) preg_match_all( $cjk_pattern, $text );

		if ( $cjk_count > 0 ) {
			$text = preg_replace( $cjk_pattern, ' ', $text );
		}

		$tokens = preg_split( '/[\s\p{Z}]+/u', (string) $text, -1, PREG_SPLIT_NO_EMPTY );
		$words  = is_array( $tokens ) ? count( $tokens ) : 0;

		/*
		 * CJK is read at roughly 500 characters per minute against 200 words
		 * per minute for spaced scripts, so 2.5 characters count as one word.
		 */
		if ( $cjk_count > 0 ) {
			$words += (int) ceil( $cjk_count / 2.5 );
		}

		return max( 0, $words );
	}




	/**
	 * Calculate reading time for given content.
	 *
	 * @since  1.0.0
	 * @param  string $content Post content.
	 * @return int    Reading time in minutes (minimum 1).
	 */
	public static function get_reading_time( $content ) {

		// Remove shortcodes like [gallery].
		$content = strip_shortcodes( $content );

		// Remove HTML tags.
		$clean = wp_strip_all_tags( $content );


		// Unicode-aware word count (see count_words()).
		$word_count = self::count_words( $clean );


		// Allow developers to modify word count.
		// absint() guards against a filter callback returning a negative or non-numeric value.
		$word_count = max( 1, absint( apply_filters( 'minuteread_word_count', $word_count, $content ) ) );

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
		// absint() + max(1,...) guards against a filter callback returning a negative or non-numeric value.
		return max( 1, absint( apply_filters( 'minuteread_reading_time_output', $reading_time, $word_count ) ) );
	}
}
