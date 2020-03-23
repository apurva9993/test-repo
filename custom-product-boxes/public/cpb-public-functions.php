<?php
/**
 * CPB Public
 *
 * Functions for the helper of public.
 *
 * @package  CPB\Functions
 * @version  4.0.0
 */

if ( ! function_exists( 'cpb_get_layout_path' ) ) {
	/**
	 * Gets the selected layout.
	 * @return string
	 */
	function cpb_get_layout_path() {
		if ( get_option( 'cpb_layout_type' ) ) {
			$layout_setting = get_option( 'cpb_layout_type' );
			$layouts = CPB_Layouts::get_layouts();
			$layout = $layouts[ $layout_setting ]['path'];
		} else {
			$layout = CPB_ABSPATH . 'templates/product-layouts/desktop-layouts/cpb_new_layout_vertical';
		}

		return $layout;
	}
}

if ( ! function_exists( 'get_setting' ) ) {
	function get_cpb_setting( $setting_key ) {
		$cpb_settings = CPB_Settings::cpb_get_settings();

		if ( isset( $cpb_settings[ $setting_key ] ) ) {
			return $cpb_settings[ $setting_key ];
		}
	}
}

function is_legacy_layout( $layout_type ) {
	return in_array( $layout_type, apply_filters( 'cpb_load_from_legacy_layout_type_array', array( 'horizontal_legacy', 'vertical_left_legacy', 'vertical_right_legacy' ) ) );
}

function include_legacy_display_class() {
	require_once( CPB_ABSPATH . 'legacy-layout/public/class-wdm-abstract-product-display.php' );
	require_once( CPB_ABSPATH . 'legacy-layout/public/class-wdm-mobile-list-layout.php' );
}

/**
 * Gets all bundled items ordered by date.
 * Sort the dates of creation of the products first in an array.
 * Then according to the sorted array sort the array of the bundle products
 * objects.
 * @return array $new_bundle_list sorted array of bundled products objects.
 */
function get_sorted_list_of_bundled_items( $bundle_addon_list ) {
	global $product;
	$items = array();
	$new_bundle_list = array();

	if ( $bundle_addon_list ) {
		// CREATING NEW ARRAY OF ITEM ID AND DATE FOR SORTING
		foreach ( $bundle_addon_list as $addon_id => $addon_data ) {
			$product = wc_get_product( empty( $addon_data['variation_id'] ) ? $addon_id : $addon_data['variation_id'] );
			$date = $product->get_date_created();
			$items[ $addon_id ] = $date->date_i18n();
		}

		// GETTING SORTED DATE ARRAY
		uasort( $items, 'sort_by_date' );

		// SORTING OBJECY ARRAY ACCORDING TO SORTED ARRAY
		$bundle_addon_list = sort_object_by_date( $bundle_addon_list, $items );

		// FIXING INDEXES OF THE SORTED OBJECT ARRAY
		/*$new_bundle_list = array();
			$new_bundle_list[$value->getItemId()] = $value;
		}

		krsort($new_bundle_list);*/
		$new_bundle_list = array_reverse( $bundle_addon_list, true );
		return $new_bundle_list;
	}
	return $new_bundle_list;
}

function sort_by_date( $item1, $item2 ) {
	return strtotime( $item1 ) - strtotime( $item2 );
}

/**
* Return the objects of products in bundle sorted as per the dates of creation.
* @param array $item1 bundled items objects array.
* @param array $item2 sorted array of the dates of creation of products in bundle.
*/
function sort_object_by_date( $item1, $item2 ) {
	$new_item = array();
	foreach ( $item2 as $key => $value ) {
		if ( isset( $item1[ $key ] ) ) {
			$new_item[ $key ] = $item1[ $key ];
		}
		unset( $value ); // unset because unused while commiting on git.
	}
	return $new_item;
}
