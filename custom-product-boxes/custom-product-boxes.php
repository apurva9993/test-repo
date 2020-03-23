<?php
/**
 * Plugin Name: Custom Product Boxes
 * Plugin URI: https://www.wisdmlabs.com/assorted-bundles-woocommerce-custom-product-boxes-plugin/
 * Description: The Custom Product Boxes is an extension for your WooCommerce store, using which, your customers will be able to select products, and create and purchase their own personalized bundles.
 * Version: 4.0
 * Author: WisdmLabs
 * Author URI: http://www.wisdmlabs.com
 * Text Domain: custom-product-boxes
 * Domain Path: /languages/
 * License: GPL
 * WC requires at least: 3.0.0
 * WC tested up to: 3.6.4
 *
 * @package CPB
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

// Define plugin constants.
// constant version.
if ( ! defined( 'CPB_PLUGIN_FILE' ) ) {
	define( 'CPB_PLUGIN_FILE', __FILE__ );
}

register_activation_hook( CPB_PLUGIN_FILE, 'cpb_install' );

function cpb_install() {
	global $wpdb;

	$wpdb->hide_errors();

	$prefilled_table = $wpdb->prefix . 'cpb_prefilled_products_data';
	if ( ! $wpdb->get_var( "SHOW TABLES LIKE '$prefilled_table';" ) ) {  // @codingStandardsIgnoreLine.
		add_option( 'cpb_run_install', true );
	} else {
		update_option( 'cpb_run_install', false );
	}
}

/**
 * Returns the main instance of CPB.
 *
 * @since  2.1
 * @return WooCommerce
 */
function CPB() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	// Include the main CPB class.
	if ( ! class_exists( 'Custom_Product_Boxes', false ) ) {
		include_once dirname( __FILE__ ) . '/includes/class-custom-product-boxes.php';
	}
	return Custom_Product_Boxes::instance();
}


add_action( 'plugins_loaded', 'load_cpb_plugin' );

/**
 * Starts the instantiation of CPB plugin.
 * @return void
 */
function load_cpb_plugin() {
	// Global for backwards compatibility.
	$GLOBALS['cpb_instance'] = CPB();
}
