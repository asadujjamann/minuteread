<?php
/**
 * Plugin Name:       MinuteRead — Reading Time for Posts
 * Plugin URI:        https://github.com/asadujjamann/minuteread
 * Description:       Displays estimated reading time on your posts. Customize words per minute, label, format, and position from Settings > MinuteRead.
 * Version:           1.0.0
 * Requires at least: 5.0
 * Requires PHP:      7.2
 * Author:            Asadujjaman
 * Author URI:        https://asadujjamann.github.io
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       minuteread
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MINUTEREAD_VERSION', '1.0.0' );
define( 'MINUTEREAD_PATH', plugin_dir_path( __FILE__ ) );
define( 'MINUTEREAD_URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( MINUTEREAD_PATH . 'includes/class-minuteread-core.php' ) ) {
	require_once MINUTEREAD_PATH . 'includes/class-minuteread-core.php';
}
if ( file_exists( MINUTEREAD_PATH . 'public/class-minuteread-frontend.php' ) ) {
	require_once MINUTEREAD_PATH . 'public/class-minuteread-frontend.php';
}
if ( file_exists( MINUTEREAD_PATH . 'admin/class-minuteread-admin.php' ) ) {
	require_once MINUTEREAD_PATH . 'admin/class-minuteread-admin.php';
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function ( $links ) {
	$settings_link = '<a href="options-general.php?page=minuteread-settings">' . esc_html__( 'Settings', 'minuteread' ) . '</a>';
	array_unshift( $links, $settings_link );
	return $links;
} );

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
