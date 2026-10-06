<?php
/**
 * Plugin Name: Temporary Access Pro
 * Plugin URI: https://github.com/Rabby67809/temporary-access-pro
 * Description: Secure, scoped and time-limited WordPress access with live session control.
 * Version: 0.1.0
 * Requires at least: 6.5
 * Requires PHP: 8.0
 * Author: Rabby67809
 * License: GPL-2.0-or-later
 * Text Domain: temporary-access-pro
 */
defined( 'ABSPATH' ) || exit;
define( 'TAP_VERSION', '0.1.0' );
define( 'TAP_FILE', __FILE__ );
define( 'TAP_DIR', plugin_dir_path( __FILE__ ) );
define( 'TAP_URL', plugin_dir_url( __FILE__ ) );
require_once TAP_DIR . 'includes/class-tap-install.php';
require_once TAP_DIR . 'includes/class-tap-plugin.php';
register_activation_hook( __FILE__, array( 'TAP_Install', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'TAP_Install', 'deactivate' ) );
add_action( 'plugins_loaded', static function () {
	TAP_Install::maybe_upgrade();
	TAP_Plugin::boot();
} );
