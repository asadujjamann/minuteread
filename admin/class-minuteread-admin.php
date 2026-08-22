<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Handles admin settings page.
 */
class MinuteRead_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'settings' ) );
	}

	/**
	 * Register all settings with sanitization callbacks.
	 *
	 * @since 1.0.0
	 */
	public function settings() {
		register_setting( 'minuteread_group', 'minuteread_enable',   array( $this, 'sanitize_enable' ) );
		register_setting( 'minuteread_group', 'minuteread_wpm',      array( $this, 'sanitize_wpm' ) );
		register_setting( 'minuteread_group', 'minuteread_label',    array( $this, 'sanitize_label' ) );
		register_setting( 'minuteread_group', 'minuteread_format',   array( $this, 'sanitize_format' ) );
		register_setting( 'minuteread_group', 'minuteread_position', array( $this, 'sanitize_position' ) );
		register_setting( 'minuteread_group', 'minuteread_post_types', array( $this, 'sanitize_post_types' ) );
	}

	/**
	 * Sanitize the enable/disable checkbox.
	 * Unchecked checkbox sends nothing - treat as 0.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input.
	 * @return int   1 or 0.
	 */
	public function sanitize_enable( $value ) {
		return ( '1' === (string) $value ) ? 1 : 0;
	}

	/**
	 * Sanitize words-per-minute (50-1000).
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input.
	 * @return int
	 */
	public function sanitize_wpm( $value ) {
		$value = absint( $value );
		if ( $value < 50 || $value > 1000 ) {
			add_settings_error(
				'minuteread_wpm',
				'minuteread_wpm_range',
				esc_html__( 'Words per minute must be between 50 and 1000. Value reset to 200.', 'minuteread' )
			);
			return 200;
		}
		return $value;
	}

	/**
	 * Sanitize the label field - plain text only, no HTML.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input.
	 * @return string
	 */
	public function sanitize_label( $value ) {
		$value = sanitize_text_field( $value );
		return mb_substr( $value, 0, 100 );
	}

	/**
	 * Sanitize the format field.
	 *
	 * Rules:
	 * - Must contain %d as the time placeholder.
	 * - Only %d and %% (escaped percent) are allowed - no %s, %f, %x, etc.
	 * - Maximum 30 characters.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input.
	 * @return string
	 */
	public function sanitize_format( $value ) {
		$value = sanitize_text_field( trim( $value ) );

		if ( empty( $value ) ) {
			return '%d min';
		}

		// After removing %% (escaped literal percent), count remaining % signs.
		// All of them must be the start of %d - nothing else is permitted.
		$stripped = str_replace( '%%', '', $value );
		$total_percent = substr_count( $stripped, '%' );
		$total_d       = substr_count( $stripped, '%d' );

		if ( $total_percent !== $total_d ) {
			add_settings_error(
				'minuteread_format',
				'minuteread_format_specifier',
				/* translators: %d is a literal PHP format specifier, not a number - keep it as-is in translation. */
				esc_html__( 'Only %d is allowed as a format specifier. Value reset to default.', 'minuteread' )
			);
			return '%d min';
		}

		if ( false === strpos( $value, '%d' ) ) {
			add_settings_error(
				'minuteread_format',
				'minuteread_format_invalid',
				/* translators: %d is a literal PHP format specifier, not a number - keep it as-is in translation. */
				esc_html__( 'Format must contain %d as the time placeholder. Value reset to default.', 'minuteread' )
			);
			return '%d min';
		}

		if ( mb_strlen( $value ) > 30 ) {
			return '%d min';
		}

		return $value;
	}

	/**
	 * Sanitize position - only 'before' or 'after' allowed.
	 *
	 * @since  1.0.0
	 * @param  mixed $value Raw input.
	 * @return string
	 */
	public function sanitize_position( $value ) {
		return in_array( $value, array( 'before', 'after' ), true ) ? $value : 'before';
	}


	/**
	 * Sanitize the post type selection.
	 * Only public, registered post types are accepted.
	 *
	 * @since  1.1.0
	 * @param  mixed $value Raw input.
	 * @return array
	 */
	public function sanitize_post_types( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		$allowed = array_keys( minuteread_get_selectable_post_types() );
		$value   = array_map( 'sanitize_key', $value );

		return array_values( array_intersect( $value, $allowed ) );
	}



	/**
	 * Register the settings page under Settings menu.
	 *
	 * @since 1.0.0
	 */
	public function menu() {
		add_options_page(
			esc_html__( 'MinuteRead', 'minuteread' ),
			esc_html__( 'MinuteRead', 'minuteread' ),
			'manage_options',
			'minuteread-settings',
			array( $this, 'settings_page' )
		);
	}

	/**
	 * Render the settings page HTML.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

			<form method="post" action="options.php">
				<?php settings_fields( 'minuteread_group' ); ?>

				<table class="form-table" role="presentation">

					<tr>
						<th scope="row">
							<label for="minuteread_enable">
								<?php esc_html_e( 'Enable Plugin', 'minuteread' ); ?>
							</label>
						</th>
						<td>
							<input
								type="checkbox"
								id="minuteread_enable"
								name="minuteread_enable"
								value="1"
								<?php checked( get_option( 'minuteread_enable', 1 ), 1 ); ?>
							>
							<p class="description">
								<?php esc_html_e( 'Uncheck to hide reading time without deactivating the plugin.', 'minuteread' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="minuteread_wpm">
								<?php esc_html_e( 'Words Per Minute', 'minuteread' ); ?>
							</label>
						</th>
						<td>
							<input
								type="number"
								id="minuteread_wpm"
								name="minuteread_wpm"
								value="<?php echo esc_attr( get_option( 'minuteread_wpm', 200 ) ); ?>"
								min="50"
								max="1000"
								step="1"
								class="small-text"
							>
							<p class="description">
								<?php esc_html_e( 'Average adult reads 200 words/min. Accepted range: 50-1000.', 'minuteread' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php esc_html_e( 'Show On', 'minuteread' ); ?>
						</th>
						<td>
							<fieldset>
								<legend class="screen-reader-text">
									<span><?php esc_html_e( 'Show On', 'minuteread' ); ?></span>
								</legend>
								<?php
								$selected = get_option( 'minuteread_post_types', array( 'post' ) );
								if ( ! is_array( $selected ) ) {
									$selected = array( 'post' );
								}

								foreach ( minuteread_get_selectable_post_types() as $type ) :
									?>
									<label style="display:block;margin-bottom:4px;">
										<input
											type="checkbox"
											name="minuteread_post_types[]"
											value="<?php echo esc_attr( $type->name ); ?>"
											<?php checked( in_array( $type->name, $selected, true ) ); ?>
										>
										<?php echo esc_html( $type->labels->singular_name ); ?>
										<code><?php echo esc_html( $type->name ); ?></code>
									</label>
									<?php
								endforeach;
								?>
								<p class="description">
									<?php esc_html_e( 'Reading time is inserted automatically on the selected post types. The shortcode works everywhere regardless.', 'minuteread' ); ?>
								</p>
							</fieldset>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="minuteread_label">
								<?php esc_html_e( 'Label', 'minuteread' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								id="minuteread_label"
								name="minuteread_label"
								value="<?php echo esc_attr( get_option( 'minuteread_label', '' ) ); ?>"
								placeholder="<?php esc_attr_e( 'Estimated Reading Time', 'minuteread' ); ?>"
								class="regular-text"
							>
							<p class="description">
								<?php esc_html_e( 'Text shown before the time. Leave empty to use default.', 'minuteread' ); ?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<label for="minuteread_format">
								<?php esc_html_e( 'Time Format', 'minuteread' ); ?>
							</label>
						</th>
						<td>
							<input
								type="text"
								id="minuteread_format"
								name="minuteread_format"
								value="<?php echo esc_attr( get_option( 'minuteread_format', '' ) ); ?>"
								placeholder="%d min"
								class="regular-text"
							>
							<p class="description">
								<?php
									/* translators: %d is a literal format token shown to the user as-is - do not translate %d. */
									esc_html_e( 'Use %d as a placeholder for the reading time number. Leave empty for default.', 'minuteread' );
								?>
								<br>
								<code>%d min</code> &rarr; 5 min &nbsp;|&nbsp;
								<code>%d mins</code> &rarr; 5 mins &nbsp;|&nbsp;
								<code>%d minutes</code> &rarr; 5 minutes
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php esc_html_e( 'Display Position', 'minuteread' ); ?>
						</th>
						<td>
							<fieldset>
								<label>
									<input
										type="radio"
										name="minuteread_position"
										value="before"
										<?php checked( get_option( 'minuteread_position', 'before' ), 'before' ); ?>
									>
									<?php esc_html_e( 'Before content', 'minuteread' ); ?>
								</label>
								<br>
								<label>
									<input
										type="radio"
										name="minuteread_position"
										value="after"
										<?php checked( get_option( 'minuteread_position', 'before' ), 'after' ); ?>
									>
									<?php esc_html_e( 'After content', 'minuteread' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>

				</table>

				<?php submit_button(); ?>
			</form>

			<hr>
			<h2><?php esc_html_e( 'Shortcode', 'minuteread' ); ?></h2>
			<p>
				<?php esc_html_e( 'Place reading time anywhere in a post or page:', 'minuteread' ); ?>
				<code>[minuteread_time]</code>
			</p>
		</div>
		<?php
	}
}
