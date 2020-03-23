<?php
/**
 * CPB_Data class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/Data
 * @since    5.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CPB Product Data crud data initailization class.
 *
 * CPB Product Data filters and includes.
 *
 * @class    CPB_Data
 * @version  4.0.0
 */
class CPB_Data {

	/**
	 * Initaites registration of data store.
	 */
	public static function init() {
		// Product CPB CPT data store.
		require_once( 'class-cpb-product-data-store-cpt.php' );

		// Register the CPB Product Custom Post Type data store.
		add_filter( 'woocommerce_data_stores', array( __CLASS__, 'register_cpb_type_data_store' ), 10 );
	}

	/**
	 * Registers the CPB Product Custom Post Type data store.
	 *
	 * @param  array $stores Array of data stores available.
	 * @return array
	 */
	public static function register_cpb_type_data_store( $stores ) {

		$stores['product-wdm_bundle_product'] = 'CPB_Product_Data_Store_CPT';

		return $stores;
	}
}

CPB_Data::init();
