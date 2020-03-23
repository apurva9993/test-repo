<?php
/**
 * Custom Product Boxes Updates
 *
 * Functions for updating data, used by the background updater.
 *
 * @package CPB/Functions
 * @version 3.3.0
 */

defined( 'ABSPATH' ) || exit;


/**
 * Updates settings key on migration.
 *
 * @return void
 */
function cpb_update_400_cpb_settings_data() {
	$new_options_keys = array(
		'_wdm_enable_giftbox_total' => 'cpb_enable_giftbox_total',
		'_wdm_enable_addbox_total'  => 'cpb_enable_addbox_total',
		'_wdmcpb_anonymize_mgs'     => 'cpb_anonymize_msg',
		'_wdm_hide_stock'           => 'cpb_hide_stock',
		'wdmcpb_old_order_labels'   => 'cpb_old_order_labels',
		'_grand_total_label'        => 'cpb_grand_total_label',
		'_giftbox_total_label'      => 'cpb_giftbox_total_label',
		'_addbox_total_label'       => 'cpb_addbox_total_label',
	);

	$cpb_settings = array();

	foreach ( $new_options_keys as $key => $value ) {
		if ( get_option( $key ) ) {
			$cpb_settings[ $value ] = get_option( $key );
		}
	}

	add_option( 'cpb_settings', $cpb_settings );
}


/**
 * Updates 4.0.0 versions data on migration.
 *
 * @return void
 */
function cpb_update_400_wdm_data_to_cpb() {
	$GLOBALS['old_postmeta_keys'] = array(
		'_wdm_desktop_layout'               => 'cpb_layout_selected',
		'wdm_enable_product_subscription'   => 'cpb_subscription',
		// '_wdm_cpb_pricing_type_field'  => 'cpb_pricing_type_field',
		'_wdm_grid_field'                   => 'cpb_box_capacity',
		'_wdm_gift_bgcolor'                 => 'cpb_gift_bgcolor',
		'_wdm_gift_boxes_color'             => 'cpb_gift_boxes_color',
		'_wdm_column_field'                 => 'cpb_box_column_size', // number of columns in box section (vertical).
		'_wdm_product_grid'                 => 'cpb_product_column_size', // number of columns in product section (vertical).
		'_wdm_item_field'                   => 'cpb_box_row_size', // number of rows in box section (horizontal).
		'_wdm_product_item_grid'            => 'cpb_product_row_size', // number of rows in product section (horizontal).
		'wdm_boxes_selection'               => 'cpb_allow_partially_filled',
		'wdm_order_by_date'                 => 'cpb_order_by_date',
		'wdm_disable_scroll'                => 'cpb_disable_scroll',
		'_wdm_enable_gift_message'          => 'cpb_enable_message',
		'_wdm_gift_message_label'           => 'cpb_message_label',
		'wdm_prefilled_box'                 => 'cpb_enable_prefilled',
		'wdm_swap_products'                 => 'cpb_swap_products',
		'_per_product_pricing_active'       => 'cpb_per_product_pricing_active',
		'_product_base_pricing_active'      => 'cpb_base_pricing_active',
		'_per_product_shipping_active'      => 'cpb_per_product_shipping_active',
		'add_on_products'                    => 'cpb_add_on_products',
		'wdm_need_shipping'                 => 'cpb_need_shipping',
		'wdm_reg_price_field'               => 'cpb_reg_price_field',
		'wdm_sale_price_field'              => 'cpb_sale_price_field',
	);

	$cpb_ids = get_cpb_product_ids();

	if ( ! $cpb_ids ) {
		return;
	}

	foreach ( $cpb_ids as $id ) {
		cpb_update_post_meta_key( $id );
	}

}

/**
 * Updates meta data keys on migration.
 *
 * @param  int $cpb_id Product id for Box Product.
 * @return void
 */
function cpb_update_post_meta_key( $cpb_id ) {
	global $old_postmeta_keys;
	foreach ( $old_postmeta_keys as $old_key => $new_key ) {
		if ( get_post_meta( $cpb_id, $new_key, true ) ) {
			// If meta already updated.
			continue;
		}

		$value = get_post_meta( $cpb_id, $old_key, true );

		if ( $value ) {
			cpb_update_meta( $cpb_id, $new_key, $old_key, $value );
		}
	}
}

/**
 * CPB update meta values for new keys
 *
 * @param    int    $cpb_id Product id for Box Product.
 * @param    string $new_key new meta key.
 * @param    string $old_key old meta key.
 * @param    mixed  $value   values for the meta key.
 * @return   void
 */
function cpb_update_meta( $cpb_id, $new_key, $old_key, $value ) {
	$classes_keys = array(
		'_wdm_product_grid',
		'_wdm_product_item_grid',
		'_wdm_item_field',
		'_wdm_column_field',
	);

	if ( in_array( $old_key, $classes_keys ) ) {
		update_class_value( $cpb_id, $old_key, $new_key );
	} else {
		update_post_meta( $cpb_id, $new_key, $value );
	}
}

/**
 * This functions processes the data for updating the addons data bundled in Custom Product Boxes
 *
 * @return void
 */
function cpb_update_400_bundled_data() {
	if ( ! get_option( 'cpb_data_400' ) ) {
		$cpb_ids = get_cpb_product_ids();

		if ( $cpb_ids && empty( $cpb_ids ) ) {
			update_option( 'cpb_data_400', true );
			return;
		}

		if ( update_new_arrays( $cpb_ids ) ) {
			update_option( 'cpb_data_400', true );
		}
	}
}

/**
 * Update old class keys for CPB products layouts settings
 *
 * @param    int    $cpb_id Product id for Box Product.
 * @param    string $old_key old meta key.
 * @param    string $new_key new meta key.
 * @return   void
 */
function update_class_value( $cpb_id, $old_key, $new_key ) {
	$classes_values = array(
		'_wdm_product_grid'         => array(
			'bundled_product-col-2' => 'cpb-product-col-2',
			'bundled_product-col-3' => 'cpb-product-col-3',
		),
		'_wdm_column_field'         => array(
			'wdm-bundle-single-product-col-2' => 'cpb-box-col-2',
			'wdm-bundle-single-product-col-3' => 'cpb-box-col-3',
		),
		'_wdm_product_item_grid'    => array(
			'bundled_product-col-4' => 'cpb-product-row-4',
			'bundled_product-col-5' => 'cpb-product-row-5',
			'bundled_product-col-6' => 'cpb-product-row-6',
			'bundled_product-col-7' => 'cpb-product-row-7',
			'bundled_product-col-8' => 'cpb-product-row-8',
		),
		'_wdm_item_field'           => array(
			'wdm-bundle-single-product-col-4' => 'cpb-box-row-4',
			'wdm-bundle-single-product-col-5' => 'cpb-box-row-5',
			'wdm-bundle-single-product-col-6' => 'cpb-box-row-6',
			'wdm-bundle-single-product-col-7' => 'cpb-box-row-7',
			'wdm-bundle-single-product-col-8' => 'cpb-box-row-8',
		),
	);

	$old_classes = array_keys( $classes_values[ $old_key ] );

	$old_value = get_post_meta( $cpb_id, $old_key, true );

	if ( in_array( $old_value, $old_classes ) ) {
		update_post_meta( $cpb_id, $new_key, $classes_values[ $old_key ][ $old_value ] );
	}
}

/**
 * Update new value for addons
 *
 * @param int $cpb_ids Product ids for Box Product.
 * @return   void
 */
function update_new_arrays( $cpb_ids ) {
	$updated = false;
	// $GLOBALS['new_meta'] = array();
	foreach ( $cpb_ids as $cpb_id ) {
		$bundle_meta = get_post_meta( $cpb_id, '_bundle_data', true );
		if ( ! $bundle_meta ) {
			continue;
		}
		$updated = update_bundle_data( $bundle_meta, $cpb_id );
	}
	return $updated;
}

/**
 * Updates addons data bundled in Custom Product Boxes
 *
 * @param  object|array $bundle_meta old meta for the box.
 * @param  int          $cpb_id Product id for Box Product.
 * @return void
 */
function update_bundle_data( $bundle_meta, $cpb_id ) {
	$updated = false;
	$new_meta = array();
	foreach ( $bundle_meta as $key => $value ) {
		unset( $key );
		if ( ! isset( $value['product_type'] ) ) {
			continue;
		}

		switch ( $value['product_type'] ) {
			case 'simple':
				$new_meta[ $value['product_id'] ]['variations'] = array();
				$new_meta[ $value['product_id'] ]['text_name'] = $value['display_text'];
				$new_meta[ $value['product_id'] ]['product_type'] = $value['product_type'];
				break;
			case 'variation':
				$new_variation_key = $value['variation_id'] . '_' . implode( '_', (array) $value['variation_attributes'] );
				$new_meta[ $value['product_id'] ]['variations'][ $new_variation_key ] = array(
					'variation_id' => $value['variation_id'],
					'text_name' => $value['display_text'],
				);
				$new_meta[ $value['product_id'] ]['product_type'] = 'variable';
				$new_meta[ $value['product_id'] ]['text_name'] = $value['text'];
				break;
			case 'variable':
				$childrens_meta = get_childrens_meta( $value, $new_meta );
				$new_meta = $new_meta + $childrens_meta;
		}
	}

	$updated = update_post_meta( $cpb_id, 'cpb_addons_data', $new_meta );

	return $updated;
}

function get_childrens_meta( $variable_meta, $new_meta ) {
	foreach ( $variable_meta['childrens'] as $key => $value ) {
		unset( $key );
		$new_variation_key = $value['variation_id'] . '_' . implode( '_', (array) $value['variation_attributes'] );
		$new_meta[ $variable_meta['product_id'] ]['variations'][ $new_variation_key ] = array(
			'variation_id' => $value['variation_id'],
			'text_name' => $value['display_text'],
		);
	}
	$new_meta[ $variable_meta['product_id'] ]['product_type'] = $variable_meta['product_type'];
	$new_meta[ $variable_meta['product_id'] ]['text_name'] = $variable_meta['text'];

	return $new_meta;
}
