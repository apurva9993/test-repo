<?php
/**
 * WC_Product_Wdm_Bundle_Product class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/Product
 * @since 4.0.00.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CPB Product Class.
 *
 * @class    WC_Product_Wdm_Bundle_Product
 * @version  5.13.0
 */
class WC_Product_Wdm_Bundle_Product extends WC_Product {
	/**
	 * Array of CPB products data fields used in CRUD and runtime operations.
	 *
	 * @var array
	 */
	private $extended_data = array();

	/**
	 * The type of data store to use.
	 *
	 * @var string
	 */
	private $data_store_type = 'wdm_bundle_product';

	/**
	 * Constructor.
	 *
	 * @param  mixed $product Can be product id or object.
	 */
	public function __construct( $product ) {
		// Initialize the data store type. Yes, WC 3.0 decouples the data store from the product class.
		if ( ( $product instanceof WC_Product ) && false === $product->is_type( 'wdm_bundle_product' ) ) {
			$this->data_store_type = $product->get_type();
		}

		// Initialize private properties.
		$this->load_defaults( $product );

		// Define/load type-specific data.
		$this->load_extended_data();

		// Load product data.
		parent::__construct( $product );
	}

	/**
	 * Load property and runtime cache defaults to trigger a re-sync.
	 *
	 * @param bool $reset_objects Whether to rest the object or not.
	 * @since 4.0.0
	 */
	public function load_defaults( $reset_objects = false ) {
		$this->extended_data = array(
			'pricing_type'                => 'cpb-fixed-price',
			'layout'                      => CPB()->plugin_path() . '/templates/product-layouts/desktop-layouts/vertical',
			'box_capacity'                => 2,
			'partially_filled_box'        => false,
			'enable_gift_message'         => false,
			'gift_message_label'          => __( 'Add Note', 'custom-product-boxes' ),
			'sort_by_date'                => false,
			'prefilled'                   => false,
			'addon_items_list'            => array(),
			'swap_prefilled'              => false,
			'cpb_subscription'         => false,
			'include_variations'          => false,
		);

		$this->swap_prefilled = false;

		// $this->addon_items_list = array();

		if ( $reset_objects ) {
			$this->extended_data['addon_items_list'] = null;
		}
	}

	/**
	 * Define type-specific data.
	 *
	 * @since 4.0.00
	 */
	private function load_extended_data() {

		// Back-compat.
		$this->product_type = 'wdm_bundle_product';

		// Define type-specific fields and let WC use our data store to read the data.
		$this->data = array_merge( $this->data, $this->extended_data );
	}

	/**
	 * Get internal type.
	 *
	 * @since 4.0.00
	 *
	 * @return string
	 */
	public function get_type() {
		return 'wdm_bundle_product';
	}

	/**
	 * In per-product pricing mode, get_regular_price() normally returns zero, since the container item does not have a price of its own.
	 *
	 * @param  string $context property is accessed in which mode.
	 */
	// public function get_regular_price( $context = 'view' ) {
	// 	if ( ! $this->is_base_price() && ! is_admin() ) {
	// 		return (float) 0;
	// 	} else {
	// 		return parent::get_regular_price( $context );
	// 	}
	// }

	// public function get_box_total() {
	// 	if ( $this->get_pricing_type() ==  ) {
	// 	}
	// }

	/**
	 * Checks if base price is active
	 *
	 * @return boolean
	 */
	public function is_base_price() {
		if ( in_array( $this->get_pricing_type(), array( 'cpb-dynamic-base', 'cpb-fixed-price' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if base price is active
	 *
	 * @return boolean
	 */
	public function is_dynamic_price() {
		if ( in_array( $this->get_pricing_type(), array( 'cpb-dynamic-base', 'cpb-dynamic-nobase' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get data store type.
	 *
	 * @since 4.0.00
	 * @return string
	 */
	public function get_data_store_type() {
		return $this->data_store_type;
	}

	/**
	 * Getter for pricing_type.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_pricing_type( $context = 'view' ) {
		return $this->get_prop( 'pricing_type', $context );
	}

	/**
	 * Getter for partially_filled_box.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_partially_filled_box( $context = 'view' ) {
		return $this->get_prop( 'partially_filled_box', $context );
	}

	/**
	 * Getter for include_variations.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_include_variations( $context = 'view' ) {
		return $this->get_prop( 'include_variations', $context );
	}

	/**
	 * Getter for box_capacity.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_box_capacity( $context = 'view' ) {
		return $this->get_prop( 'box_capacity', $context );
	}

	/**
	 * Getter for sort_by_date.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_sort_by_date( $context = 'view' ) {
		return $this->get_prop( 'sort_by_date', $context );
	}

	/**
	 * Getter for enable_gift_message.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_enable_gift_message( $context = 'view' ) {
		return $this->get_prop( 'enable_gift_message', $context );
	}

	/**
	 * Getter for gift_message_label.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_gift_message_label( $context = 'view' ) {
		return $this->get_prop( 'gift_message_label', $context );
	}

	/**
	 * Getter for prefilled.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_prefilled( $context = 'view' ) {
		return $this->get_prop( 'prefilled', $context );
	}

	/**
	 * Gets the desktop layout from meta table selected for the CPB.
	 *
	 * @return string Desktop template layout path selected for CPB.
	 */
	public function get_layout() {
		$layout = get_post_meta( $this->get_id(), 'cpb_layout_selected', true );

		// Set Vertical layout as a default layout.
		if ( empty( $layout ) ) {
			$layout = CPB()->plugin_path() . '/templates/product-layouts/desktop-layouts/vertical';
		}

		return apply_filters( 'wdm_cpb_desktop_layout', $layout, $this->get_id() );
	}

	/**
	 * Getter for addon_items_list.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_addon_items_list( $context = 'view' ) {
		return $this->get_prop( 'addon_items_list', $context );
	}

	/**
	 * Getter for swap_prefilled.
	 *
	 * @since 4.0.00
	 * @param  string $context property is accessed in which mode.
	 * @return string
	 */
	public function get_swap_prefilled( $context = 'view' ) {
		return $this->get_prop( 'swap_prefilled', $context );
	}

	/**
	 * Getter method for cpb_subscription.
	 *
	 * @since 4.0.0
	 * @param  string $context property is accessed in which mode.
	 * @return bool
	 */
	public function get_cpb_subscription( $context = 'view' ) {
		return $this->get_prop( 'cpb_subscription', $context );
	}

	/**
	 * Alias for 'set_props'.
	 *
	 * @param array $properties propertied to be set.
	 * @since 4.0.0
	 */
	public function set( $properties ) {
		return $this->set_props( $properties );
	}

	/**
	 * Setter for pricing_type.
	 *
	 * @since 4.0.0
	 * @param  string $pricing_type pricing type of product.
	 */
	public function set_pricing_type( $pricing_type ) {
		$this->set_prop( 'pricing_type', $pricing_type );
	}

	/**
	 * Setter for include_variations.
	 *
	 * @since 4.0.0
	 * @param  bool $include_variations include_variations of product.
	 */
	public function set_include_variations( $include_variations ) {
		$this->set_prop( 'include_variations', $include_variations );
	}

	/**
	 * Setter for layout..
	 *
	 * @since 4.0.0
	 * @param  string $layout value to set to the propery.
	 */
	public function set_layout( $layout ) {
		$this->set_prop( 'layout', $layout );
	}

	/**
	 * Setter for box_capacity.
	 *
	 * @since 4.0.0
	 * @param  string $box_capacity value to set to the propery.
	 */
	public function set_box_capacity( $box_capacity ) {
		$this->set_prop( 'box_capacity', $box_capacity );
	}

	/**
	 * Setter for partially_filled_box.
	 *
	 * @since 4.0.0
	 * @param  string $partially_filled_box value to set to the propery.
	 */
	public function set_partially_filled_box( $partially_filled_box ) {
		$this->set_prop( 'partially_filled_box', $partially_filled_box );
	}

	/**
	 * Setter for box_capacity..
	 *
	 * @since 4.0.0
	 * @param  string $enable_gift_message value to set to the propery.
	 */
	public function set_enable_gift_message( $enable_gift_message ) {
		$this->set_prop( 'enable_gift_message', $enable_gift_message );
	}

	/**
	 * Setter for box_capacity..
	 *
	 * @since 4.0.0
	 * @param  string $sort_by_date value to set to the propery.
	 */
	public function set_sort_by_date( $sort_by_date ) {
		$this->set_prop( 'sort_by_date', $sort_by_date );
	}

	/**
	 * Setter for box_capacity..
	 *
	 * @since 4.0.0
	 * @param  string $gift_message_label value to set to the propery.
	 */
	public function set_gift_message_label( $gift_message_label ) {
		$this->set_prop( 'gift_message_label', $gift_message_label );
	}

	/**
	 * Setter for box_capacity..
	 *
	 * @since 4.0.0
	 * @param  string $prefilled value to set to the propery.
	 */
	public function set_prefilled( $prefilled ) {
		$this->set_prop( 'prefilled', $prefilled );
	}

	/**
	 * Setter for box_capacity.
	 *
	 * @since 4.0.0
	 * @param  string $swap_prefilled value to set to the propery.
	 */
	public function set_swap_prefilled( $swap_prefilled ) {
		$this->set_prop( 'swap_prefilled', $swap_prefilled );
	}

	/**
	 * Setter for box_capacity.
	 *
	 * @since 4.0.0
	 * @param  string $addon_items_list value to set to the propery.
	 */
	public function set_addon_items_list( $addon_items_list ) {
		$this->set_prop( 'addon_items_list', $addon_items_list );
	}

	/**
	 * Setter for cpb_subscription.
	 *
	 * @since 4.0.0
	 * @param  string $cpb_subscription value to set to the propery.
	 */
	public function set_cpb_subscription( $cpb_subscription ) {
		$this->set_prop( 'cpb_subscription', $cpb_subscription );
	}

	/**
	 * Function to check if the prefilled products can be swapped with other products.
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function can_be_swapped() {
		return $this->has_prefilled_products() && 'yes' == $this->get_swap_prefilled();
	}

	/**
	 * Function to check if the product has subscription enabled.
	 *
	 * @since 4.0.0
	 * @return bool
	 */
	public function is_product_cpb_subscription() {
		// Subscription plugin is active?
		if ( ! class_exists( 'WC_Subscriptions_Product' ) ) {
			return false;
		}
		// CPB product setting for subscription is enabled?
		if ( 'no' == $this->get_cpb_subscription() ) {
			return false;
		}

		return true;
	}

	/**
	 * Checks if prefilled is enabled
	 * @return boolean
	 */
	public function has_prefilled_products() {
		global $prefill_manager;
		if ( false === $this->get_prefilled() ) {
			return false;
		}

		$prefill_products = $prefill_manager->get_prefilled_products( $this->get_id() );

		if ( empty( $prefill_products ) ) {
			return false;
		}

		return true;
	}
}
