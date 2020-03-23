<?php
/**
 * CPB Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package CPB\Functions
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require CPB_ABSPATH . 'includes/cpb-formatting-functions.php';

add_filter( 'cpb_box_capacity', 'intval' ); // Box capacity is integer by default.

/**
 * Get label string for new message labels.
 *
 * @return string
 */
function get_label_string() {
	$existing_labels = get_existing_msg_labels();

	if ( ! empty( $existing_labels ) ) {
		update_option( 'wdmcpb_old_order_labels', $existing_labels );
	}

	return implode( ', ', $existing_labels );
}

/**
 * Get label string for old CPB orders message labels.
 *
 * @return string
 */
function get_existing_msg_labels() {
	global $cpb_settings;
	if ( $cpb_settings['wdmcpb_old_order_labels'] ) {
		return maybe_unserialize(
			$cpb_settings['wdmcpb_old_order_labels']
		);
	}

	$cpb_ids = get_cpb_product_ids();
	$existing_labels = array();
	if ( empty( $cpb_ids ) ) {
		return array();
	}

	foreach ( $cpb_ids as $id ) {
		if ( 'yes' != get_post_meta( $id, 'cpb_enable_message', true ) ) {
			continue;
		}

		array_push( $existing_labels, get_post_meta( $id, '_wdm_gift_message_label', true ) );
	}

	return $existing_labels;
}

/**
 * Displays notice.
 *
 * @param  string $message     HTMLstring to display as message.
 * @param  string $notice_type type of notice.
 * @return String
 */
function wdm_pb_bundles_add_notice( $message, $notice_type ) {
	return wc_add_notice( $message, $notice_type );
}

/**
 * Get the Admin Template
 *
 * @param string $slug slug name for template.
 * @param string $name name of the template file.
 * @param array  $args parameters to be passed if any.
 */
function cpb_get_admin_template_part( $slug, $name = '', $args = array() ) {
	cpb_get_template_part( $args, $slug, $name, 'admin' );
}
/**
 * Get the Public Template
 *
 * @param string $slug slug name for template.
 * @param string $name name of the template file.
 * @param array  $args parameters to be passed if any.
 */
function cpb_get_public_template_part( $slug, $name = '', $args = array() ) {
	cpb_get_template_part( $args, $slug, $name, 'public' );
}

/**
 * Get the  Template for the page
 *
 * @param array  $args parameters to be passed if any.
 * @param string $slug slug name for template.
 * @param string $name name of the template file.
 * @param string $template_type admin or public.
 */
function cpb_get_template_part( $args, $slug, $name = '', $template_type = 'public' ) {
	$template = '';
	extract( $args );  // @codingStandardsIgnoreLine. 
	// Look in yourtheme/custom-product-boxes/slug-name.php
	if ( $name ) {
		$template = locate_template( "custom-product-boxes/{$template_type}/{$slug}-{$name}.php" );
	}

	// Get default slug-name.php.
	if ( ! $template && $name && file_exists( CPB_ABSPATH . "templates/{$template_type}/{$slug}-{$name}.php" ) ) {
		$template = CPB_ABSPATH . "templates/{$template_type}/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, yourtheme/quoteup/slug.php.
	if ( ! $template ) {
		$template = locate_template( "custom-product-boxes/{$template_type}/{$slug}.php" );
	}

	// Get default slug.php.
	if ( ! $template && file_exists( CPB_ABSPATH . "templates/{$template_type}/{$slug}.php" ) ) {
		$template = CPB_ABSPATH . "templates/{$template_type}/{$slug}.php";
	}

	// Allow 3rd party plugin filter template file from their plugin.
	$template = apply_filters( "cpb_get_{$template_type}_template_part", $template, $slug, $name, $args );

	if ( $template ) {
		include $template;
	}
}

/**
 * What type of request is this?
 *
 * @param  string $type admin, ajax, cron or frontend.
 * @return bool
 */
function is_request( $type ) {
	switch ( $type ) {
		case 'admin':
			return is_admin();
		case 'ajax':
			return defined( 'DOING_AJAX' );
		case 'cron':
			return defined( 'DOING_CRON' );
		case 'frontend':
			return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
	}
}

/**
 * Checks if php session is already started.
 *
 * @return boolean
 */
function is_session_started() {
	if ( hp_sapi_name() !== 'cli' ) {
		if ( version_compare( phpversion(), '5.4.0', '>=' ) ) {
			return session_status() === PHP_SESSION_ACTIVE ? true : false;
		} else {
			return session_id() === '' ? false : true;
		}
	}
	return false;
}

/**
 * Returns array of id's for all the available CPB product type products on the site
 */
function get_cpb_product_ids() {
	global $wpdb;

	$table_name = $wpdb->prefix . 'postmeta';

	$post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$table_name} WHERE meta_key = %s", '_bundle_data' ) ); // @codingStandardsIgnoreLine.

	return $post_ids;
}

/**
 * Checks if plugin is installed first time on the site.
 *
 * @return boolean
 */
function is_cpb_fresh_install() {
	return version_compare( CPB_VERSION, get_option( 'wdmcpb_start_version' ), '==' );
}

/**
 * If site has any orders placed.
 *
 * @return boolean
 */
function has_wc_orders() {
	global $wpdb;
	$table_name = $wpdb->prefix . 'woocommerce_order_items';

	$order_result = $wpdb->get_row( "SELECT * FROM {$table_name} ORDER BY {$table_name}.`order_id` DESC LIMIT 1" ); // @codingStandardsIgnoreLine.

	if ( $order_result ) {
		return true;
	}

	return false;
}

/**
 * Is Subscription plugin active.
 *
 * @return boolean
 */
function is_subscription_active() {
	return class_exists( 'WC_Subscriptions_Product' );
}

/**
 * Display hr tag.
 *
 * @param  string $text text to display.
 * @return void
 */
function cpb_hr_element( $text ) {
	echo '<div class="hr-section hr-section-components">' . esc_html( $text ) . '</div>';
}

/**
 * Adds tag 'selected' to element of array
 *
 * @param object $variation_object Variation object.
 */
function add_selected_tag( $variation_object ) {
	$variation_array = $variation_object;
	$variation_array['selected'] = 'yes';
	return $variation_array;
}

/**
 * Adds tag 'selected' to element of array
 *
 * @param object $variation_object Variation object.
 */
function add_product_type( $variation_object ) {
	$variation_array = $variation_object;
	$variation_array['product_type'] = 'variation';
	return $variation_array;
}

/**
 * Gets variable products ids whoes variations are not selected on publish or update of product.
 *
 * @param object $addons Addon added to the box(Variable Product).
 */
function get_unselected_variable_ids( $addons ) {
	$empty_variable_ids = array();
	if ( empty( $addons ) ) {
		return $empty_variable_ids;
	}

	$empty_variable_ids = array_filter( $addons, 'variable_has_variations', ARRAY_FILTER_USE_BOTH );
}

/**
 * Vafriable product has variations?
 *
 * @param  array $add_on addon product of CPB box.
 * @param  index $key    variable product id mostly.
 * @return boolean
 */
function variable_has_variations( $add_on, $key ) {
	if ( 'variable' == $add_on['product_type'] && empty( $add_on['variations'] ) ) {
		return $key;
	}

	return false;
}

/**
 * Returns addons list including selected variations of the variable product.
 *
 * @param  Array $bundle_data List of addons.
 * @return Array
 */
function get_addon_including_variation_product( $bundle_data ) {
	if ( empty( $bundle_data ) || ! is_array( $bundle_data ) ) {
		return $bundle_data;
	}

	foreach ( $bundle_data as $single_key => $single_data ) {
		$bundle_data = modify_bundle_data( $bundle_data, $single_key, $single_data );
	}
	return $bundle_data;
}

function modify_bundle_data( $bundle_data, $single_key, $single_data ) {
	if ( 'variable' == $single_data['product_type'] ) {
		foreach ( $single_data['variations'] as $variation_key => $variation_data ) {
			$addon_product = wc_get_product( $variation_data['variation_id'] );
			$bundle_data[ $variation_key ] = $variation_data;
			$bundle_data[ $variation_key ]['product_id'] = $single_key;
			$bundle_data[ $variation_key ]['product'] = $addon_product;
			$bundle_data[ $variation_key ]['stock_quantity'] = $addon_product->get_stock_quantity();
			$bundle_data[ $variation_key ]['wc_price'] = wc_get_price_to_display( $addon_product );
			$bundle_data[ $variation_key ]['product_type'] = 'variation';
		}
		unset( $bundle_data[ $single_key ] );
	} else {
		$addon_product = wc_get_product( $single_key );
		$bundle_data[ $single_key ]['product_id'] = $single_key;
		$bundle_data[ $single_key ]['product'] = $addon_product;
		$bundle_data[ $single_key ]['text_name'] = $addon_product->get_formatted_name();
		$bundle_data[ $single_key ]['stock_quantity'] = $addon_product->get_stock_quantity();
		$bundle_data[ $single_key ]['wc_price'] = wc_get_price_to_display( $addon_product );
		$bundle_data[ $variation_key ]['product_type'] = $single_data['product_type'];
	}

	return $bundle_data;
}

/**
 * Function to get the attributes array. i.e; variation data for unique key provided.
 *
 * @param  [type] $key Unique key (ex: '8756_blue_Yes').
 * @return array Array of attributes
 */
function get_variation_data_from_key( $key ) {
	$values_array = explode( '_', $key );
	$variation_id = array_shift( $values_array );

	$product = wc_get_product( $variation_id );
	$all_attribites = $product->get_attributes();

	if ( empty( $all_attribites ) ) {
		return false;
	}

	$set_attribute_name = function ( $value ) {
		return 'attribute_' . $value;
	};

	// Sets attribute_ prefix to all keys of an array.
	$all_attribites = array_combine(
		array_map( $set_attribute_name, array_keys( $all_attribites ) ),
		$values_array
	);

	return apply_filters(
		'get_variation_data_from_key',
		array(
			'variation_id' => $variation_id,
			'variation' => $all_attribites,
			'product_id' => $product->get_parent_id(),
		)
	);
}


/**
 * Returns id from the addons unique string.
 * @param  string $addon_id Unique id/string for addon product.
 * @return int
 */
function get_id_from_string( $addon_id ) {
	if ( is_unique_id( $addon_id ) ) {
		$values_array = explode( '_', $addon_id );
		return array_shift( $values_array );
	}
	return $addon_id;
}

/**
 * Checks whether the product id is unique od generated by CPB.
 *
 * @param  string|int  $product_id [description]
 * @return boolean
 */
function is_unique_id( $product_id ) {
	if ( strpos( $product_id, '_' ) ) {
		return true;
	}

	return false;
}

function cpb_get_quantities( $value ) {
	return $value['addon_quantity'];
}

/**
 * Get the product image for the add-on or pre-filled products.
 * If there is no image set for the product then place a thumbnail for that
 * product.
 *
 * @param int $product_id Product Id.
 * @return string $image The post thumbnail image tag.
 */
function get_product_image( $product_id ) {

	$image = get_the_post_thumbnail(
		$product_id,
		apply_filters( 'bundled_product_large_thumbnail_size', 'post_thumbnail' ),
		array(
			'title'    => get_the_title( get_post_thumbnail_id( $product_id ) ),
		)
	);
	if ( ! isset( $image ) || empty( $image ) ) {
		$image = '<img width="180" height="180" src="' . wc_placeholder_img_src() . '" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="poster_5_up" title="poster_5_up" sizes="(max-width: 180px) 100vw, 180px">';
	}

	return $image;
}

/**
 * Get CPB product
 * @param  [type] $product [description]
 * @return [type]          [description]
 */
function cpb_get_product( $product ) {
	if ( is_numeric( $product ) ) {
		$product = wc_get_product( $product );
	}

	return $product;
}

function check_inventory_status( $product, $qty = 1 ) {
	$product = cpb_get_product( $product );

	if ( ! $product->is_purchasable() ) {
		return false;
	}

	if ( $product->is_sold_individually() && $qty > 1 ) {
		return false;
	}

	if ( ! $product->is_in_stock() ) {
		return false;
	}

	if ( ! $product->has_enough_stock( $qty ) ) {
		return false;
	}

	return true;
}
