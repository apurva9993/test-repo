<?php
/**
 * CPB Product template hooks
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB
 * @since    4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

//Product Title.
add_action( 'cpb_product_title', 'cpb_product_title' );

// Product Short Description.
add_action( 'cpb_short_description', 'cpb_short_description' );

// CPB Products Content.
add_action( 'cpb_content_area', 'cpb_content_area', 10, 1 );

// CPB Product Progress Wrap.
add_action( 'cpb_progress_wrap', 'cpb_progress_wrap' );

// CPB Products accessibility section.
add_action( 'cpb_accessibility_wrap', 'cpb_accessibility_wrap' );

// CPB Product Empty boxes Section.
add_action( 'cpb_empty_boxes_wrap', 'cpb_empty_boxes_wrap' );

//CPB product dispaly addons list.
add_action( 'cpb_product_addons_wrap', 'cpb_product_addons_wrap', 10, 2 );

//CPB product display single addon.
add_action( 'cpb_single_addon_product', 'cpb_single_addon_product', 10, 3 );


// Single product template for Product Bundles. Form location: Default.
add_action( 'cpb_wdm_bundle_product_add_to_cart', 'cpb_template_add_to_cart', 10, 2 );

// show gift message template.
add_action( 'cpb_gift_message_html', 'cpb_gift_message_html', 10, 1 );

add_action( 'cpb_after_add_to_cart_quantity', 'cpb_display_pricing_box', 10, 1 );

add_action( 'cpb_product_price_html', 'cpb_template_price', 10, 1 );
add_action( 'cpb_product_base_price_html', 'cpb_box_price_template', 10, 1 );
add_action( 'cpb_product_signup_fee_html', 'cpb_template_signup_fee', 10, 1 );
add_action( 'cpb_product_grand_total', 'cpb_template_grand_total', 10, 1 );

add_action( 'cpb_add_to_cart_quantity', 'cpb_display_add_to_cart_quantity', 10, 1 );

//CPB product Single prefilled and empty boxes
add_action( 'cpb_single_empty_and_prefilled_box', 'cpb_single_empty_and_prefilled_box', 10, 1 );

// Single Empty box
add_action( 'cpb_single_empty_box', 'cpb_single_empty_box', 10, 2 );

// Single addon image
add_action( 'cpb_addon_image', 'cpb_addon_image', 10, 2 );

// Single addon stock
add_action( 'cpb_stock_html', 'cpb_stock_html', 10, 1 );

// Single addon title
add_action( 'cpb_addon_title', 'cpb_addon_title', 10, 2 );

//Single addon price.
add_action( 'cpb_addon_price', 'cpb_addon_price', 10, 2 );
// add_filter( 'woocommerce_quantity_input_classes', 'cpb_add_quantity_class' );
