<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://zeleri.com/
 * @since      1.0.2
 *
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.2
 * @package    Zeleri Pay
 * @subpackage Zeleri Pay/includes
 * @author     Zeleri <jose.rodriguez.externo@ionix.cl>
 */
class Zeleri_Pay_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.2
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'zeleri-woo-oficial',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
