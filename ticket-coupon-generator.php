<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://vagelis.dev
 * @since             1.0.0
 * @package           Ticket_Coupon_Generator
 *
 * @wordpress-plugin
 * Plugin Name:       Ticket Coupon Generator
 * Plugin URI:        https://github.com/vagelisp
 * Description:       This plugin generates CampTix coupons in bulk using CSV files.
 * Version:           1.0.5
 * Author:            Vagelis Papaioannou
 * Author URI:        https://vagelis.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ticket-coupon-generator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'VPGC_TICKET_COUPON_GENERATOR_VERSION', '1.0.5' );

/**
 * Log file path & path.
 */
define('VPCG_LOG_FILE_NAME', 'vpcg-generated-coupons.txt');
define('VPCG_LOG_FILE_PATH', WP_CONTENT_DIR . '/' . VPCG_LOG_FILE_NAME);


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ticket-coupon-generator-activator.php
 */
function vpgc_activate_ticket_coupon_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ticket-coupon-generator-activator.php';
	VPGC\Activator\Ticket_Coupon_Generator_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ticket-coupon-generator-deactivator.php
 */
function vpgc_deactivate_ticket_coupon_generator() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ticket-coupon-generator-deactivator.php';
	VPGC\Deactivator\Ticket_Coupon_Generator_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'vpgc_activate_ticket_coupon_generator' );
register_deactivation_hook( __FILE__, 'vpgc_deactivate_ticket_coupon_generator' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vpgc-ticket-coupon-generator.php';

/**
 * Initializes the Ticket Coupon Generator plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function vpgc_run_ticket_coupon_generator() {

	$plugin = new VPGC_Ticket_Coupon_Generator();
	$plugin->run();

}

/**
 * Registers the 'run_ticket_coupon_generator' function to be executed
 * after CampTix has loaded.
 */
add_action('camptix_load_addons', 'vpgc_run_ticket_coupon_generator' );
