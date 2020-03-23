<?php
/**
 * CPB Template
 *
 * Functions for the templating system.
 *
 * @package  CPB\Functions
 * @version  4.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Output the CPB product title area.
 */
function cpb_product_title() {
	wc_get_template(
		'single-product/title.php',
		array(),
		'',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Output the CPB product short description area.
 */
function cpb_short_description() {
	wc_get_template(
		'single-product/short-description.php',
		array(),
		'',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Output the CPB product Content area.
 */
function cpb_content_area( $addon_list ) {
	wc_get_template(
		'single-product/cpb-content-single-product.php',
		array(
			'addon_list' => $addon_list,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Outputs the CPB product progress content.
 */
function cpb_progress_wrap() {
	wc_get_template(
		'single-product/cpb-progress-wrap.php',
		array(),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Outputs the CPB product accessibility content.
 */
function cpb_accessibility_wrap() {
	wc_get_template(
		'single-product/cpb-accessibility-wrap.php',
		array(),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Outputs the CPB product Empty boxes section.
 */
function cpb_empty_boxes_wrap() {
	global $product;
	// Display grid at front end
	$total_capacity = $product->get_box_capacity();

	wc_get_template(
		'single-product/cpb-empty-boxes-wrap.php',
		array(
			'total_capacity' => $total_capacity,
			'product'       => $product,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Outputs the CPB product Empty boxes section.
 */
function cpb_product_addons_wrap( $product, $addon_list ) {
	if ( $product->get_sort_by_date() ) {
		$addon_products = get_sorted_list_of_bundled_items( $addon_list );
	}

	if ( ! is_array( $addon_products ) ) {
		return;
	}

	wc_get_template(
		'single-product/cpb-product-addons-wrap.php',
		array(
			'addon_products' => $addon_products,
			'cpb_product' => $product,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * CPB Display prefilled and empty boxes
 * @param  int $total_capacity Capacity of box.
 * @return void
 */
function cpb_single_empty_and_prefilled_box( $total_capacity ) {
	global $prefill_manager, $post;
	$position = 1;
	$prefill_products = $prefill_manager->get_prefilled_products( $post->ID );

	if ( empty( $prefill_products ) || empty( $total_capacity ) ) {
		return;
	}

	foreach ( $prefill_products as $single_product ) {
		$position = display_prefilled_block( $position, $single_product );
	}
	if ( $position <= $total_capacity ) {
		cpb_single_empty_box( $total_capacity, $position );
	}
}

/**
* Check the stock status for the pre-filled product.
* If the remove mandatory products if they are out of stock option is checked
* in the settings.
* Then display the pre-filled products if they are in stock otherwise display
* the blank boxes for the bundle products.
* @param int $position current no. of pre-filled products.
* @param array $single_product Pre-filled single product info.
* @return int $position current count of pre-filled products after they have been
* displayed(if in stock) or not displayed(if not in stock)
*/
function display_prefilled_block( $position, $single_product ) {
	global $post;

	$cpb_product = CPB()->get_cpb_product( $post->ID );
	// check stock availability
	$stock_status = check_inventory_status( $single_product['product_id'], $single_product['product_qty'] );
	if ( ! $stock_status && ( ! $single_product['product_mandatory'] || ! $cpb_product->get_swap_prefilled() ) ) {
		return $position;
	}
	return add_prefilled_product( $single_product, $position );
}

/**
* If the product for pre-fill is in stock display the single pre-filled product
* And if it is not in stock do not display the product in the bundle.
* It is because we can remove mandatory pre-filled products when they are out
* of stock.
* @param  array $single_product Pre-filled single product info.
* @param int $position current no. of pre-filled products.
* @return int $position current no. of pre-filled products.
*/
function add_prefilled_product( $single_product, $position ) {
	for ( $pre = 1; $pre <= $single_product['product_qty']; $pre++ ) {
		$classes = array();
		// Mandatory Removable Product (Because it is out of Stock)
		if ( ! empty( $single_product['product_mandatory'] ) && ! check_inventory_status( $single_product['product_id'], $single_product['product_qty'] ) ) {
			$classes = array( 'wdm-prefill-out-stock' );
			continue;
		} elseif ( ! empty( $single_product['product_mandatory'] ) ) { // Mandatory Prefilled Product
			$classes = array( 'wdm-prefill-mandatory' );
		}

		display_single_prefilled_product( $single_product, $position, $pre, $classes );
		$position++;
	}
	return $position;
}

/**
* Get the pre-filled products details from the DB.
* Display the pre-filled product on front-end.
* @param int $prefill_product Pre-filled single product.
* @param int $position current no. of pre-filled products.
* @param int $product_count count of same prefilled product added.
* @param array $classes array of the classes for pre-filled products display.
*/
function display_single_prefilled_product( $prefill_product, $position, $product_count, $classes = array() ) {
	$prefill_product_id = $prefill_product['product_id'];
	if ( 'variation' == $prefill_product['product_type'] ) {
		$pre_product = new \WC_Product_Variation( $prefill_product_id );
	} else {
		$pre_product = new \WC_Product( $prefill_product_id );
	}

	$pre_price = wc_get_price_to_display( $pre_product );

	if ( is_array( $classes ) ) {
		$classes = implode( ' ', $classes );
	}

	$classes .= " cpb-product-inner wdm-prefill-product wdm_box_item wdm_added_image_{$position} wdm_filled_product_{$prefill_product_id}";
	wc_get_template(
		'addon-product/cpb-single-empty-box.php',
		array(
			'classes'         => $classes,
			'pre_product'     => $pre_product,
			'prefill_product' => $prefill_product,
			'product_count'   => $product_count,
			'position'        => $position,
			'pre_price'       => $pre_price,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Get HTML to show single empty box.
 *
 * @since  4.0.0
 * @param  WC_Product $single_product Product Object.
 * @param  int        $total_capacity Capacity of addons in box.
 * @param  int        $position Counter for item id.
 * @return string
 */
function cpb_single_empty_box( $total_capacity, $position ) {
	for ( $ctr = $position; $ctr <= $total_capacity; $ctr++ ) {
		wc_get_template(
			'addon-product/cpb-single-empty-box.php',
			array(
				'total_capacity' => $total_capacity,
				'position'       => $position,
			),
			'custom-product-boxes/',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
		);
	}
}

/**
 * Outputs the CPB product Empty boxes section.
 */
function cpb_single_addon_product( $addon_id, $addon_data, $product ) {
	global $addon_product;
	$wc_outofstock = get_option( 'woocommerce_hide_out_of_stock_items' );

	$single_addon_id = empty( $addon_data['variation_id'] ) ? $addon_id : $addon_data['variation_id'];

	$addon_product = wc_get_product( $single_addon_id );
	/*$single_addon_id = get_id_from_string( $addon_id );*/

	if ( get_post_status( $addon_product->get_id() ) !== 'publish' || ! $addon_product->is_purchasable() ) {
		return;
	}

	if ( $addon_product->is_in_stock() || ( 'no' == $wc_outofstock && ! $addon_product->is_in_stock() ) ) {
		wc_get_template(
			'single-product/cpb-single-addon-product.php',
			array(
				'addon_id' => $addon_id,
				'addon_data' => $addon_data,
				'cpb_product' => $product,
				'single_addon_id' => $single_addon_id,
			),
			'custom-product-boxes/',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
		);
	}
}

/**
 * Get HTML to show product addon image.
 *
 * @since  4.0.0
 * @param  int        $addon_id      Single addon id.
 * @return string
 */
function cpb_addon_image( $addon_id, $addon_product ) {
	$image_tag = get_product_image( $addon_id );

	wc_get_template(
		'addon-product/cpb-addon-image.php',
		array(
			'image_tag' => $image_tag,
			'product'   => $addon_product,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Get HTML to show product addon stock.
 *
 * @since  4.0.0
 * @param  WC_Product $product Product Object.
 * @return string
 */
function cpb_stock_html( $product ) {
	$hide_stock = get_cpb_setting( 'cpb_hide_stock' );
	if ( 'on' == $hide_stock ) {
		return;
	}

	error_log( 'hide stock :: ' . $hide_stock );
	$availability_html = wc_get_stock_html( $product );

	wc_get_template(
		'addon-product/cpb-addon-stock.php',
		array(
			'product'           => $product,
			'availability_html' => $availability_html,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);

	return apply_filters( 'woocommerce_get_stock_html', $availability_html, $product );
}

/**
 * Get HTML to show product addon title.
 *
 * @since  4.0.0
 * @param  array $addon_data Addon Product Data.
 * @return string
 */
function cpb_addon_title( $addon_data ) {
	wc_get_template(
		'addon-product/cpb-addon-title.php',
		array(
			'addon_data'           => $addon_data,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Get HTML to show product addon stock.
 *
 * @since  4.0.0
 * @param  WC_Product $cpb_product   CPB Box Product Object.
 * @param  WC_Product $addon_product addon product object.
 * @return string
 */
function cpb_addon_price( $cpb_product, $addon_product ) {
	if ( ! $cpb_product->is_dynamic_price() ) {
		return;
	}

	wc_get_template(
		'addon-product/cpb-addon-price.php',
		array(
			'single_product' => $addon_product,
		),
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Output the CPB product add to cart area.
 */
function cpb_template_add_to_cart( $product, $addon_list ) {
	$disabled = '';
	if ( $product->can_be_swapped() ) {
		$disabled = 'disabled';
	}
	// If some mandatory products are out of stock and they cannot be swapped
	// then add the disable class.
	$disable_class = 'single_add_to_cart_button bundle_add_to_cart_button button alt';
	if ( 'disabled' == $disabled ) {
		$disable_class .= ' os_pf_m';
	}

	wc_get_template(
		'single-product/add-to-cart/wdm-bundle-product.php',
		array(
			'product'    => $product,
			'addon_list' => $addon_list,
		),
		'',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Output the CPB product gift message area.
 */
function cpb_gift_message_html( $product ) {
	wc_get_template(
		'single-product/cpb-gift-message.php',
		array(
			'product' => $product,
		),
		'',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Load Quantity template for CPB product
 *
 * @return void
 */
function cpb_display_add_to_cart_quantity( $product ) {
	wc_get_template(
		'single-product/add-to-cart/cpb-quantity-html.php',
		array(
			'product' => $product,
		),
		'',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Loads Template for displaying pricing section of CPB product.
 *
 * @return void
 */
function cpb_display_pricing_box( $product ) {
	$args = array();
	$args = array(
		'product'  => $product,
		'reg_price' => wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) ),
		'price' => wc_get_price_to_display( $product ),
		'base_price' => $product->is_base_price(),
		'dynamic_price' => $product->is_dynamic_price(),
		'product_id' => $product->get_id(),
		'grand_total' => ! get_option( 'cpb_grand_total_label' ) ? __( 'Grand Total', 'custom-product-boxes' ) : get_option( 'cpb_grand_total_label' ),
		'enable_box_total' => get_option( 'cpb_enable_giftbox_total' ),
		'gift_box_total' => ! get_option( 'cpb_giftbox_total_label' ) ? __( 'Gift Box Total', 'custom-product-boxes' ) : get_option( 'cpb_giftbox_total_label' ),
		'enabled_add_box' => get_option( 'cpb_enable_addbox_total' ),
		'add_box_total' => ! get_option( 'cpb_addbox_total_label' ) ? __( 'Aditional Box Charges', 'custom-product-boxes' ) : get_option( 'cpb_addbox_total_label' ),
	);

	if ( $product->is_product_cpb_subscription() ) {
		$subscription_agrs = array(
			'signup_label' => __( 'Sign-up Fee', 'custom-product-boxes' ),
			'sign_up_fee' => get_post_meta( $product->get_id(), '_subscription_sign_up_fee', true ),
		);

		$args = array_merge( $subscription_agrs, $args );
	}

	wc_get_template(
		'single-product/add-to-cart/cpb-pricing-box.php',
		$args,
		'custom-product-boxes/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
	);
}

/**
 * Function to display CPB product 'Base' price html
 */
function cpb_template_price( $product ) {
	if ( $product->is_product_cpb_subscription() ) {
		wc_get_template(
			'cpb-base-price-template.php',
			array(
				'product' => $product,
			),
			'custom-product-boxes/templates/',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/single-product/'
		);
		return;
	}

	cpb_template_single_price( $product );
}

function cpb_box_price_template( $product ) {
	if ( $product->is_product_cpb_subscription() ) {
		wc_get_template(
			'cpb-base-price-template.php',
			array(
				'product' => $product,
			),
			'custom-product-boxes/templates/',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/single-product/'
		);
		return;
	}

	cpb_template_single_price_with_sales( $product );
}

/**
 * Function to display CPB product 'SingUp fee' html
 */
function cpb_template_signup_fee( $product ) {
	if ( $product->is_product_cpb_subscription() ) {
		wc_get_template(
			'cpb-signup-fee-template.php',
			array(
				'product' => $product,
			),
			'custom-product-boxes/templates/',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/single-product/'
		);
		return;
	}

	cpb_template_single_price( $product );
}

/**
 * Function to display CPB product 'Grand Total' html
 */
function cpb_template_grand_total( $product ) {
	if ( $product->is_product_cpb_subscription() ) {
		wc_get_template(
			'cpb-grand-total-template.php',
			array(
				'product' => $product,
			),
			'custom-product-boxes/templates/',
			plugin_dir_path( dirname( __FILE__ ) ) . 'templates/single-product/'
		);
		return;
	}

	cpb_template_single_price( $product );
}

function cpb_template_single_price_with_sales( $product ) {
	wc_get_template(
		'price.php',
		array(
			'product' => $product,
		),
		'custom-product-boxes/templates/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/single-product/'
	);
}

function cpb_template_single_price( $product ) {
	wc_get_template(
		'cpb-price.php',
		array(
			'product' => $product,
		),
		'custom-product-boxes/templates/',
		plugin_dir_path( dirname( __FILE__ ) ) . 'templates/single-product/'
	);
}
