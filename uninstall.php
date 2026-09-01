<?php
/**
 * Uninstall handler for Curly Alert Bar.
 *
 * @package CurlyAlertBar
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'alert_bar_enabled' );
delete_option( 'alert_bar_text' );