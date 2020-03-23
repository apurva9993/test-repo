<?php
/**
 * CPB Admin
 *
 * @class    CPB_Admin
 * @package  CPB/Admin
 * @version  4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

use CPB_Settings as CPB_Settings;

/**
 * CPB_Admin class.
 */
class CPB_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->includes();
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'init', array( $this, 'init' ), 0 );

	}

	/**
	 * File Includes.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/cpb-admin-functions.php';
		include_once dirname( __FILE__ ) . '/class-cpb-admin-assets.php';
		include_once dirname( __FILE__ ) . '/class-cpb-admin-search-addons.php';

	}

	/**
	 * Initaize the classes.
	 */
	public function init() {
		new CPB_Settings();
		CPB_Meta_Box_Prefilled_Data::init();
		CPB_Meta_Box_Product_Data::init();
	}
}

return new CPB_Admin();
