<?php
/*
Plugin Name: Honeypot for Contact Form 7
Plugin URI: http://www.nocean.ca/plugins/honeypot-module-for-contact-form-7-wordpress-plugin/
Description: Add honeypot anti-spam functionality to the popular Contact Form 7 plugin.
Author: Nocean
Author URI: http://www.nocean.ca
Version: 2.1
Text Domain: contact-form-7-honeypot
Domain Path: /languages/
*/

define( 'HONEYPOT4CF7_VERSION', '2.1' );
define( 'HONEYPOT4CF7_PLUGIN', __FILE__ );
define( 'HONEYPOT4CF7_PLUGIN_BASENAME', plugin_basename( HONEYPOT4CF7_PLUGIN ) );
define( 'HONEYPOT4CF7_PLUGIN_NAME', trim( dirname( HONEYPOT4CF7_PLUGIN_BASENAME ), '/' ) );
define( 'HONEYPOT4CF7_PLUGIN_DIR', untrailingslashit( dirname( HONEYPOT4CF7_PLUGIN ) ) );
define( 'HONEYPOT4CF7_PLUGIN_DIR_URL', untrailingslashit( plugin_dir_url( HONEYPOT4CF7_PLUGIN ) ) );
define( 'HONEYPOT4CF7_DEP_PLUGIN', 'contact-form-7/wp-contact-form-7.php' );

if ( defined( 'WPCF7_VERSION' ) ) {
	define( 'HONEYPOT4CF7_WPCF7_VERSION', WPCF7_VERSION );
} else {
	$path_to_cf7 = WP_PLUGIN_DIR . '/' . HONEYPOT4CF7_DEP_PLUGIN;
	if ( file_exists( $path_to_cf7 ) ) {
		$cf7_plugin_data = get_file_data($path_to_cf7, array('Version' => 'Version'), 'plugin');
	}

	if ( ! empty( $cf7_plugin_data['Version'] ) ) {
		define( 'HONEYPOT4CF7_WPCF7_VERSION', $cf7_plugin_data['Version'] );
	} else {
		define( 'HONEYPOT4CF7_WPCF7_VERSION', '0.0.0' );
	}
}

require_once HONEYPOT4CF7_PLUGIN_DIR . '/includes/admin.php';
require_once HONEYPOT4CF7_PLUGIN_DIR . '/includes/honeypot4cf7.php';
