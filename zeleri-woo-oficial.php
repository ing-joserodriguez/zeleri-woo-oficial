<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require __DIR__ . '/autoload.php';
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://zeleri.com
 * @since             1.0.2
 * @package           Zeleri Pay
 *
 * @wordpress-plugin
 * Plugin Name:       Zeleri Pay
 * Plugin URI:        https://zeleri.com
 * Description:       Permite el pago de productos y/o servicios, con tarjetas de crédito, débito, prepago y transferencias electrónicas.
 * Version:           1.0.2
 * Author:            Zeleri
 * Requires Plugins:  woocommerce
 * WC requires at least: 8.0
 * WC tested up to: 9.5.2
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zeleri-woo-oficial
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.2 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ZELERI_VERSION', '1.0.2' );

function zeleri_root_dir() {
	return plugin_dir_path( __FILE__ );
}

function zeleri_root_url() {
	return plugin_dir_url( __FILE__ );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zeleri-pay-activator.php
 */
function zeleri_activate() {
	require_once zeleri_root_dir() . 'includes/class-zeleri-pay-activator.php';
	Zeleri_Pay_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zeleri-pay-deactivator.php
 */
function zeleri_deactivate() {
	require_once zeleri_root_dir() . 'includes/class-zeleri-pay-deactivator.php';
	Zeleri_Pay_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'zeleri_activate' );
register_deactivation_hook( __FILE__, 'zeleri_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require zeleri_root_dir() . 'includes/class-zeleri-pay.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.2
 */
function zeleri_run() {

	$plugin = new Zeleri_Pay();
	$plugin->run();

}
zeleri_run();
