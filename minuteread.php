<?php
/**
 * Plugin Name:       MinuteRead - Reading Time for Posts
 * Plugin URI:        https://github.com/asadujjamann/minuteread
 * Description:       Displays estimated reading time on your posts, pages and custom post types. Customize words per minute, label, format, and position from Settings > MinuteRead.
 * Version:           1.1.0
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Asadujjaman
 * Author URI:        https://asadsabuj.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       minuteread
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MINUTEREAD_VERSION', '1.1.0' );
define( 'MINUTEREAD_PATH', plugin_dir_path( __FILE__ ) );
define( 'MINUTEREAD_URL', plugin_dir_url( __FILE__ ) );

require_once MINUTEREAD_PATH . 'includes/class-minuteread-core.php';
require_once MINUTEREAD_PATH . 'public/class-minuteread-frontend.php';
require_once MINUTEREAD_PATH . 'admin/class-minuteread-admin.php';


/**
 * Public post types the user can choose from in settings.
 *
 * @since  1.1.0
 * @return array Post type objects keyed by name.
 */
function minuteread_get_selectable_post_types() {
	$types = get_post_types( array( 'public' => true ), 'objects' );
	unset( $types['attachment'] );
	return $types;
}

/**
 * Post types where reading time is inserted automatically.
 *
 * Defaults to 'post' so existing installations behave exactly as before.
 *
 * @since  1.1.0
 * @return array Post type slugs.
 */
function minuteread_get_enabled_post_types() {
	$types = get_option( 'minuteread_post_types', array( 'post' ) );

	if ( ! is_array( $types ) ) {
		$types = array( 'post' );
	}

	/**
	 * Filter the post types MinuteRead runs on.
	 *
	 * @since 1.1.0
	 * @param array $types Post type slugs.
	 */
	$types = apply_filters( 'minuteread_post_types', $types );

	return array_values( array_filter( array_map( 'sanitize_key', (array) $types ) ) );
}



/**
 * Add Settings link to plugin action links.
 *
 * @param  array $links Existing action links.
 * @return array Modified action links.
 */
function minuteread_action_links( $links ) {
	$settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=minuteread-settings' ) ) . '">'
		. esc_html__( 'Settings', 'minuteread' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'minuteread_action_links' );

/**
 * Bootstrap the plugin on plugins_loaded.
 *
 * Note: load_plugin_textdomain() is omitted intentionally.
 * WordPress 4.6+ automatically loads translations for plugins
 * hosted on WordPress.org.
 */
function minuteread_init() {
	new MinuteRead_Frontend();

	if ( is_admin() ) {
		new MinuteRead_Admin();
	}
}
add_action( 'plugins_loaded', 'minuteread_init' );
