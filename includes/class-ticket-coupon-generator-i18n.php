<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://vagelis.dev
 * @since      1.0.0
 *
 * @package    Ticket_Coupon_Generator
 * @subpackage Ticket_Coupon_Generator/includes
 */

/**
 * Defines the VPGC\Admin namespace for the internationalization functionality.
 */

namespace VPGC\i18n;

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Ticket_Coupon_Generator
 * @subpackage Ticket_Coupon_Generator/includes
 * @author     Vagelis Papaioannou <hello@vagelis.dev>
 */
class Ticket_Coupon_Generator_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ticket-coupon-generator',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
