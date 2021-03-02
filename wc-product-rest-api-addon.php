<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/Orinwebsolutions/wc-product-rest-api-addon
 * @since             1.0.0
 * @package           Wc_Product_Rest_Api_Addon
 *
 * @wordpress-plugin
 * Plugin Name:       WC Product rest API addon
 * Plugin URI:        https://github.com/Orinwebsolutions/wc-product-rest-api-addon
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Amila
 * Author URI:        https://github.com/Orinwebsolutions
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc-product-rest-api-addon
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
define( 'WC_PRODUCT_REST_API_ADDON_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wc-product-rest-api-addon-activator.php
 */
function activate_wc_product_rest_api_addon() {
	if (!is_plugin_active('woocommerce-tm-extra-product-options/tm-woo-extra-product-options.php')){
		die('Plugin NOT activated, because WooCommerce TM Extra Product Options is not activated in your site, Please activate WooCommerce TM Extra Product Options plugin!!');
	}
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-product-rest-api-addon-activator.php';
	Wc_Product_Rest_Api_Addon_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wc-product-rest-api-addon-deactivator.php
 */
function deactivate_wc_product_rest_api_addon() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wc-product-rest-api-addon-deactivator.php';
	Wc_Product_Rest_Api_Addon_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wc_product_rest_api_addon' );
register_deactivation_hook( __FILE__, 'deactivate_wc_product_rest_api_addon' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wc-product-rest-api-addon.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wc_product_rest_api_addon() {

	$plugin = new Wc_Product_Rest_Api_Addon();
	$plugin->run();

}
run_wc_product_rest_api_addon();
