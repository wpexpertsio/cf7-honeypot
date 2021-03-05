<?php
/*
Plugin Name: Honeypot for Contact Form 7
Plugin URI: http://www.nocean.ca/plugins/honeypot-module-for-contact-form-7-wordpress-plugin/
Description: Add honeypot anti-spam functionality to the popular Contact Form 7 plugin.
Author: Nocean
Author URI: http://www.nocean.ca
Version: 2.0.2
Text Domain: contact-form-7-honeypot
Domain Path: /languages/
*/

define( 'HONEYPOT4CF7_VERSION', '2.0.2' );
define( 'HONEYPOT4CF7_PLUGIN', __FILE__ );
define( 'HONEYPOT4CF7_PLUGIN_BASENAME', plugin_basename( HONEYPOT4CF7_PLUGIN ) );
define( 'HONEYPOT4CF7_PLUGIN_NAME', trim( dirname( HONEYPOT4CF7_PLUGIN_BASENAME ), '/' ) );
define( 'HONEYPOT4CF7_PLUGIN_DIR', untrailingslashit( dirname( HONEYPOT4CF7_PLUGIN ) ) );
define( 'HONEYPOT4CF7_PLUGIN_DIR_URL', untrailingslashit( plugin_dir_url( HONEYPOT4CF7_PLUGIN ) ) );
define( 'HONEYPOT4CF7_DEP_PLUGIN', 'contact-form-7/wp-contact-form-7.php' );

require_once HONEYPOT4CF7_PLUGIN_DIR . '/includes/admin.php';
require_once HONEYPOT4CF7_PLUGIN_DIR . '/includes/honeypot4cf7.php';
