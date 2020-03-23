<?php
/**
 * CPB Product Subscription
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/Subscription
 * @since    4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Subscription for CPB product type.
 *
 * @class    CPB_Subscriptions_Product
 * @version  5.9.0
 */
class CPB_Subscriptions_Product {
		/**
		 * The single instance of the class.
		 *
		 * @var CPB_Subscriptions_Product
		 *
		 * @since 5.0.0
		 */
	protected static $_instance = null;

		/**
		 * Main CPB_Subscriptions_Product instance. Ensures only one instance of CPB_Subscriptions_Product is loaded or can be loaded.
		 *
		 * @since  5.0.0
		 *
		 * @return CPB_Subscriptions_Product
		 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 5.0.0
		 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'custom-product-boxes' ), '5.0.0' );
	}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 5.0.0
		 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'custom-product-boxes' ), '5.0.0' );
	}

		/**
		 * Constructor.
		 */
	protected function __construct() {
		add_filter( 'woocommerce_is_subscription', array( $this, 'is_subscription' ), 10, 3 );
	}

	/**
	 * Callback Function which allows subscription module to work on the wdm_bundle_product.
	 *
	 * @param boolean $is_subscription value returned to filter.
	 * @param int     $product_id Product id.
	 * @param object  $product Product object.
	 * @return boolean true if wdm_bundle_product is a subscription product or not.
	 */
	public function is_subscription( $is_subscription, $product_id, $product ) {
		unset( $product_id ); // unused.
		if ( ! $product->is_type( 'wdm_bundle_product' ) ) {
			return $is_subscription;
		}

		return $product->is_product_cpb_subscription();
	}
}
$cpb_subscription = CPB_Subscriptions_Product::instance();
