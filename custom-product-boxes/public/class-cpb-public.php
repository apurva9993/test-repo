<?php
/**
 * CPB Public
 *
 * @class    CPB_Public
 * @package  CPB/Public
 * @version  4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CPB_Public class.
 */
class CPB_Public {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		add_action( 'init', array( $this, 'include_woocommerce_depended_classes' ) );
		add_action( 'init', array( $this, 'init' ), 0 );

	}

	/**
	 * Includes file for frontend use.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/cpb-public-functions.php';
		include_once CPB_ABSPATH . 'public/cpb-template-hooks.php';
		include_once dirname( __FILE__ ) . '/class-cpb-public-assets.php';
		include_once dirname( __FILE__ ) . '/class-cpb-display-product.php';
		include_once dirname( __FILE__ ) . '/class-cpb-cart-process.php';
	}

	/**
	 * Function to include files which are dependend on woocommerce plugins load
	 */
	public function include_woocommerce_depended_classes() {
		include_once dirname( __FILE__ ) . '/class-cpb-gift-message.php';
	}

	/**
	 * Initialize frontend classes
	 */
	public function init() {
		// new CPB_Settings();
		// Loads the CPB products display.
		CPB_Display_Product::instance();
		// Load cart process instance.
		CPB_Cart_Process::instance();
	}
}

return new CPB_Public();
