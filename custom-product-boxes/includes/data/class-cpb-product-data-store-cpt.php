<?php
/**
 * CPB_Product_Data_Store_CPT class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/Data
 * @since    4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CPB Product Data Store class
 *
 * @class    CPB_Product_Data_Store_CPT
 * @version  4.0.0
 */
class CPB_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {
	/**
	 * Data stored in meta keys, but not considered "meta" for the CPB type.
	 *
	 * @var array
	 */
	protected $internal_meta = array(
		'cpb_pricing_method',
		'cpb_layout_selected',
		'cpb_box_capacity',
		'cpb_enable_message',
	);

	/**
	 * Maps extended properties to meta keys.
	 *
	 * @var array
	 */
	protected $props_to_meta_keys = array(
		'pricing_type'                => 'cpb_pricing_method',
		'layout'                      => 'cpb_layout_selected',
		'box_capacity'                => 'cpb_box_capacity',
		'partially_filled_box'        => 'cpb_allow_partially_filled',
		'enable_gift_message'         => 'cpb_enable_message',
		'gift_message_label'          => 'cpb_message_label',
		'sort_by_date'                => 'cpb_order_by_date',
		'prefilled'                   => 'cpb_enable_prefilled',
		'addon_items_list'            => 'cpb_addons_data',
		'swap_prefilled'              => 'cpb_swap_products',
		'cpb_subscription'         => 'cpb_subscription',
		'include_variations'          => 'cpb_include_variations',
	);

	/**
	 * Callback to exclude bundle-specific meta data.
	 *
	 * @param  object $meta Meta key to store.
	 * @return bool
	 */
	protected function exclude_internal_meta_keys( $meta ) {
		return parent::exclude_internal_meta_keys( $meta ) && ! in_array( $meta->meta_key, $this->internal_meta );
	}

	/**
	 * Reads all bundle-specific post meta.
	 *
	 * @param  WC_Product_Wdm_Bundle_Product $product CPB product object.
	 */
	protected function read_product_data( &$product ) {

		parent::read_product_data( $product );

		$product_id           = $product->get_id();
		$props_to_set = array();

		foreach ( $this->props_to_meta_keys as $property => $meta_key ) {

			// Get meta value.
			$meta_value = get_post_meta( $product_id, $meta_key, true );

			// Add to props array.
			$props_to_set[ $property ] = $meta_value;
		}

		$product->set_props( $props_to_set );
	}

	/**
	 * Writes all bundle-specific post meta.
	 *
	 * @param  WC_Product_Wdm_Bundle_Product $product CPB product object.
	 * @param  boolean                       $force force value.
	 * @suppresswarnings(phpmd.BooleanArgumentFlag)
	 */
	protected function update_post_meta( &$product, $force = false ) {
		parent::update_post_meta( $product, $force );

		$product_id                 = $product->get_id();
		$meta_keys_to_props = array_flip( $this->props_to_meta_keys );
		$props_to_update    = $force ? $meta_keys_to_props : $this->get_props_to_update( $product, $meta_keys_to_props );

		foreach ( $props_to_update as $meta_key => $property ) {

			$property_get_fn = 'get_' . $property;

			// Get meta value.
			$meta_value = $product->$property_get_fn( 'edit' );

			// Sanitize it for storage.
			if ( in_array( $property, array( 'prefilled', 'enable_gift_message', 'sort_by_date', 'partially_filled_box', 'swap_prefilled', 'include_variations', 'cpb_subscription' ) ) ) {
				$meta_value = wc_bool_to_string( $meta_value );
			}

			$updated = update_post_meta( $product_id, $meta_key, $meta_value );

			if ( $updated && ! in_array( $property, $this->updated_props ) ) {
				$this->updated_props[] = $property;
			}
		}
	}

}
