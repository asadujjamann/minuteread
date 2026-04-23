<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles frontend output and hooks.
 */
class MinuteRead_Frontend {

	public function __construct() {
		add_filter( 'the_content', array( $this, 'append_reading_time' ) );
		add_shortcode( 'minuteread_time', array( $this, 'reading_time_shortcode' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue frontend stylesheet only where reading time will be shown:
	 * - Single posts (automatic output via the_content filter), OR
	 * - Any singular page/CPT that contains the [minuteread_time] shortcode.
	 */
	public function enqueue_scripts() {
		if ( ! is_singular() ) {
			return;
		}

		// Always load on single posts — automatic output is shown here.
		if ( is_singular( 'post' ) ) {
			$this->enqueue_style();
			return;
		}

		// For pages and CPTs, only load if the shortcode is present.
		$post = get_post();
		if ( $post && has_shortcode( $post->post_content, 'minuteread_time' ) ) {
			$this->enqueue_style();
		}
	}

	/**
	 * Register and enqueue the frontend stylesheet.
	 */
	private function enqueue_style() {
		wp_enqueue_style(
			'minuteread-style',
			MINUTEREAD_URL . 'assets/css/minuteread-frontend.css',
			array(),
			MINUTEREAD_VERSION
		);
	}

	/**
	 * Build the reading time HTML string.
	 * Reused by both the_content filter and the shortcode.
	 *
	 * @param  int    $time    Reading time in minutes.
	 * @param  string $wrapper 'p' for block context, 'span' for inline.
	 * @return string Escaped HTML.
	 */
	private function build_output( $time, $wrapper = 'p' ) {
		$wrapper = in_array( $wrapper, array( 'p', 'span' ), true ) ? $wrapper : 'p';

		// Label from settings; translatable default as fallback.
		$label = get_option( 'minuteread_label', '' );
		if ( '' === $label ) {
			/* translators: Default label shown before the reading time value. */
			$label = esc_html__( 'Estimated Reading Time', 'minuteread' );
		} else {
			$label = esc_html( $label );
		}

		// Format string — %d is replaced with the minute count.
		$format = get_option( 'minuteread_format', '' );
		if ( '' === $format ) {
			/* translators: %d = number of minutes. Example output: "5 min". */
			$format = esc_html__( '%d min', 'minuteread' );
		}

		$time_text = esc_html( sprintf( $format, absint( $time ) ) );

		$output = sprintf(
			'<%1$s class="minuteread-reading-time">%2$s: %3$s</%1$s>',
			tag_escape( $wrapper ),
			$label,
			$time_text
		);

		/**
		 * Filter the final reading time HTML.
		 *
		 * @param string $output  The HTML string.
		 * @param int    $time    Reading time in minutes.
		 * @param string $wrapper HTML wrapper tag used.
		 */
		return apply_filters( 'minuteread_output_html', $output, $time, $wrapper );
	}

	/**
	 * Prepend or append reading time to single post content.
	 *
	 * @param  string $content Post content.
	 * @return string Modified content.
	 */
	public function append_reading_time( $content ) {
		if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() || is_feed() || wp_doing_ajax() || is_admin() ) {
			return $content;
		}

		if ( ! get_option( 'minuteread_enable', 1 ) ) {
			return $content;
		}

		$time     = MinuteRead_Core::get_reading_time( $content );
		$output   = $this->build_output( $time, 'p' );
		$position = get_option( 'minuteread_position', 'before' );

		if ( 'after' === $position ) {
			return $content . $output;
		}

		return $output . $content;
	}

	/**
	 * Shortcode handler — [minuteread_time]
	 *
	 * @return string HTML output.
	 */
	public function reading_time_shortcode() {
		if ( ! get_option( 'minuteread_enable', 1 ) ) {
			return '';
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		$content = get_post_field( 'post_content', $post_id );
		if ( ! $content ) {
			return '';
		}

		$time = MinuteRead_Core::get_reading_time( $content );

		return $this->build_output( $time, 'span' );
	}
}
