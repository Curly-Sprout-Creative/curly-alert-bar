<?php
/**
 * Plugin Name:       Curly Alert Bar
 * Plugin URI:        https://github.com/Curly-Sprout-Creative/curly-alert-bar
 * Description:       Admin-controlled announcement bar. Output the text with the [alert_bar_text] shortcode. Requires Oxygen 6: the bar itself is an Oxygen element with class "alert-bar" and a close button with class "alert-bar-close"; this plugin drives its content and front-end visibility.
 * Version:           1.0.1
 * Author:            Curly Sprout Creative
 * License:           GPL-2.0-or-later
 * Text Domain:       curly-alert-bar
 *
 * @package CurlyAlertBar
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CURLY_ALERT_BAR_VERSION', '1.0.1' );
define( 'CURLY_ALERT_BAR_FILE', __FILE__ );
define( 'CURLY_ALERT_BAR_DIR', plugin_dir_path( __FILE__ ) );
define( 'CURLY_ALERT_BAR_URL', plugin_dir_url( __FILE__ ) );

require_once CURLY_ALERT_BAR_DIR . 'includes/class-curly-alert-bar.php';

/**
 * Bootstrap the plugin.
 */
function curly_alert_bar() {
	return Curly_Alert_Bar::instance();
}

curly_alert_bar();

/**
 * Plugin update checker (GitHub Releases, public repo — no auth needed).
 */
require_once CURLY_ALERT_BAR_DIR . 'vendor/plugin-update-checker/plugin-update-checker.php';
if ( class_exists( '\YahnisElsts\PluginUpdateChecker\v5\PucFactory' ) ) {
	$curly_alert_bar_updater = \YahnisElsts\PluginUpdateChecker\v5\PucFactory::buildUpdateChecker(
		'https://github.com/Curly-Sprout-Creative/curly-alert-bar/',
		__FILE__,
		'curly-alert-bar'
	);
}