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
		add_action( 'wp_enqueue_scripts', array( $this, 'register_style' ) );
	}

	/**
	 * Register the frontend stylesheet, and enqueue it up front on views where
	 * the automatic output will run so the CSS lands in the document head.
	 *
	 * Anywhere else - shortcode in a widget, a template, a block - the style is
	 * enqueued on demand from build_output().
	 *
	 * @since 1.0.0
	 * @since 1.1.0 Registers first; enqueues on demand.
	 */
	public function register_style() {
		wp_register_style(
			'minuteread-style',
			MINUTEREAD_URL . 'assets/css/minuteread-frontend.css',
			array(),
			MINUTEREAD_VERSION
		);

		$types = minuteread_get_enabled_post_types();

		if ( ! empty( $types ) && is_singular( $types ) && get_option( 'minuteread_enable', 1 ) ) {
			wp_enqueue_style( 'minuteread-style' );
		}
	}


	/**
	 * Build the reading time HTML string.
	 * Reused by both the_content filter and the shortcode.
	 *
	 * @since  1.0.0
	 * @param  int    $time    Reading time in minutes.
	 * @param  string $wrapper 'p' for block context, 'span' for inline.
	 * @return string Escaped HTML.
	 */
	private function build_output( $time, $wrapper = 'p' ) {
		$wrapper = in_array( $wrapper, array( 'p', 'span' ), true ) ? $wrapper : 'p';

		// Load the stylesheet wherever the output actually appears.
		wp_enqueue_style( 'minuteread-style' );

		// Label from settings; translatable default as fallback.
		$label = get_option( 'minuteread_label', '' );
		if ( '' === $label ) {
			/* translators: Default label shown before the reading time value. */
			$label = esc_html__( 'Estimated Reading Time', 'minuteread' );
		} else {
			$label = esc_html( $label );
		}

		// Format string - %d is replaced with the minute count.
		// Use __() (not esc_html__()) so sprintf() receives the raw string;
		// esc_html() is applied to the final sprintf() result instead.
		$format = get_option( 'minuteread_format', '' );
		if ( '' === $format ) {
			/* translators: %d = number of minutes. Example output: "5 min". */
			$format = __( '%d min', 'minuteread' );
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
		 * @since 1.0.0
		 * @param string $output  The HTML string.
		 * @param int    $time    Reading time in minutes.
		 * @param string $wrapper HTML wrapper tag used.
		 */
		return wp_kses_post( apply_filters( 'minuteread_output_html', $output, $time, $wrapper ) );
	}

	/**
	 * Prepend or append reading time to single post content.
	 *
	 * @since  1.0.0
	 * @param  string $content Post content.
	 * @return string Modified content.
	 */
	public function append_reading_time( $content ) {
		
		$types = minuteread_get_enabled_post_types();
		if ( empty( $types ) || ! is_singular( $types ) || ! in_the_loop() || ! is_main_query() || is_feed() || wp_doing_ajax() || is_admin() ) {
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
	 * Shortcode handler - [minuteread_time] or [minuteread_time id="123"]
	 *
	 * @since  1.0.0
	 * @since  1.1.0 Accepts an "id" attribute so the shortcode can be used
	 *               outside the loop, e.g. in a template or a widget.
	 * @param  array|string $atts Shortcode attributes.
	 * @return string HTML output.
	 */
	public function reading_time_shortcode( $atts = array() ) {
		if ( ! get_option( 'minuteread_enable', 1 ) ) {
			return '';
		}

		$atts = shortcode_atts(
			array( 'id' => 0 ),
			$atts,
			'minuteread_time'
		);

		$post_id = absint( $atts['id'] );
		if ( ! $post_id ) {
			$post_id = get_the_ID();
		}
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
