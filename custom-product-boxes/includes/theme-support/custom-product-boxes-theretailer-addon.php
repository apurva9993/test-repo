<?php
/*
Custom Product Boxes Addon for The Retailer Theme
*/

// add_action('woocommerce_before_single_product', 'cpRemoveUnwantedActions');
add_action('wp_enqueue_scripts', 'cpbTheretailerCompatibilityCss', 999);
// function cpRemoveUnwantedActions()
// {
//     global $productDisplay;
//     if (is_singular('product')) {
//         $product = wc_get_product(get_the_ID());
//         if ($product->is_type('wdm_bundle_product')) {
//             add_action('wdm_product_summary', 'cpbUnhookCpbActions', 1);
//             remove_action('woocommerce_product_summary_thumbnails', 'woocommerce_show_product_thumbnails', 20);
//             remove_action('woocommerce_before_single_product_summary_sale_flash', 'woocommerce_show_product_sale_flash', 10);
//             remove_action('woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_images', 20);
//             remove_action('woocommerce_single_product_summary_single_rating', 'woocommerce_template_single_rating', 10);
//             remove_action('woocommerce_single_product_summary_single_title', 'woocommerce_template_single_title', 5);
//             remove_action('woocommerce_single_product_summary_single_price', 'woocommerce_template_single_price', 10);
//             remove_action('woocommerce_single_product_summary_single_excerpt', 'woocommerce_template_single_excerpt', 20);
//             remove_action('woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30);
//             remove_action('woocommerce_single_product_summary_single_meta', 'woocommerce_template_single_meta', 40);
//             // removeClassAction('woocommerce_before_single_product_summary', $productDisplay, array('loadProductLayoutHtml', 'mobileListProductLayoutHtml'));
//         }
//     }
// }

// function cpbUnhookCpbActions()
// {
//     remove_action('wdm_product_summary', 'woocommerce_template_single_meta', 40);
//     remove_action('wdm_product_summary', 'woocommerce_template_single_title', 5);
//     remove_action('wdm_product_summary', 'woocommerce_template_single_rating', 10);
//     remove_action('wdm_product_summary', 'woocommerce_template_single_price', 10);
//     remove_action('wdm_product_summary', 'woocommerce_template_single_excerpt', 20);
// }

/**
* Gives theme compatibility with Flatsome theme
*/
function cpbTheretailerCompatibilityCss()
{
	if (is_singular('product')) {
		$custom_css = "
			div.gbtr_poduct_details_left_col {
				width: 100%;
			}

			div.gbtr_poduct_details_right_col {
				display: none;
			}

			.product-type-wdm_bundle_product img.attachment-shop_thumbnail.wp-post-image{
				width: 100%;
			}
			div.cpb-row.cpb-clear div.wdm_product_bundle_container_form form#wdm_product_bundle_container_form-right.cpb-flex-wrap-reverse.cpb-flex-row-reverse.cpb-row.cpb-clear div.cpb-col-sm-6.cpb-col-md-6.cpb-col-lg-6.cpb-col-xl-6 div.wdm-bundle-product-product-group {
				width: 100% !important;
			}

			.cpb_gift_message {
				padding-left: 1px;
				margin-left: 5px;
			}

			.grtr_product_price_desktops, .product_infos.summary .after_title_reviews {
				display: block;
			}

			.gift-message-box {
				margin-top: 5%;
			}

			.grtr_product_header_mobiles .price {
				display: none;
			}
		";

		$cart_css = "
			td.product-name {
				padding-bottom: 10% !important;
			}
		";
		wp_add_inline_style('wdm-cpb-style', $custom_css);
		wp_add_inline_style('wdm-cpb-cart-css', $cart_css);
	}
}
