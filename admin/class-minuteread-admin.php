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
	 */
	public function settings() {
		register_setting( 'minuteread_group', 'minuteread_enable',   array( $this, 'sanitize_enable' ) );
		register_setting( 'minuteread_group', 'minuteread_wpm',      array( $this, 'sanitize_wpm' ) );
		register_setting( 'minuteread_group', 'minuteread_label',    array( $this, 'sanitize_label' ) );
		register_setting( 'minuteread_group', 'minuteread_format',   array( $this, 'sanitize_format' ) );
		register_setting( 'minuteread_group', 'minuteread_position', array( $this, 'sanitize_position' ) );
	}

	/**
	 * Sanitize the enable/disable checkbox.
	 * Unchecked checkbox sends nothing — treat as 0.
	 *
	 * @param  mixed $value Raw input.
	 * @return int   1 or 0.
	 */
	public function sanitize_enable( $value ) {
		return ( '1' === (string) $value ) ? 1 : 0;
	}

	/**
	 * Sanitize words-per-minute (50–1000).
	 *
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
	 * Sanitize the label field — plain text only, no HTML.
	 *
	 * @param  mixed $value Raw input.
	 * @return string
	 */
	public function sanitize_label( $value ) {
		$value = sanitize_text_field( $value );
		return mb_substr( $value, 0, 100 );
	}

	/**
	 * Sanitize the format field — must contain %d, max 30 chars.
	 *
	 * @param  mixed $value Raw input.
	 * @return string
	 */
	public function sanitize_format( $value ) {
		$value = sanitize_text_field( trim( $value ) );

		if ( empty( $value ) ) {
			return '%d min';
		}

		if ( false === strpos( $value, '%d' ) ) {
			add_settings_error(
				'minuteread_format',
				'minuteread_format_invalid',
				sprintf( esc_html__( 'Format must contain %%d as the time placeholder. Example: %%d min. Value reset to default.', 'minuteread' ) )
			);
			return '%d min';
		}

		if ( mb_strlen( $value ) > 30 ) {
			return '%d min';
		}

		return $value;
	}

	/**
	 * Sanitize position — only 'before' or 'after' allowed.
	 *
	 * @param  mixed $value Raw input.
	 * @return string
	 */
	public function sanitize_position( $value ) {
		return in_array( $value, array( 'before', 'after' ), true ) ? $value : 'before';
	}

	/**
	 * Register the settings page under Settings menu.
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
								<?php esc_html_e( 'Average adult reads 200 words/min. Accepted range: 50–1000.', 'minuteread' ); ?>
							</p>
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
								<?php printf( esc_html__( 'Use %%d as the time placeholder. Example: %%d min — or — %%d minutes. Leave empty for default.', 'minuteread' ) ); ?>
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

