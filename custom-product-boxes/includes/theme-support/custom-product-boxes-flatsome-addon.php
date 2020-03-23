<?php
/*
Plugin Name: Custom Product Boxes Addon for Flatsome Theme
*/

add_action('woocommerce_before_single_product', 'cpbRemoveUnwantedActions');

add_action('wp_enqueue_scripts', 'cpbShopkeeperCompatibilityCss', 999);
/**
* Remove the unwanted action to ensure theme compatibility with the CPB.
*/
function cpbRemoveUnwantedActions()
{
	if (is_singular('product')) {
		$product = wc_get_product(get_the_ID());
		if ($product->is_type('wdm_bundle_product')) {
			add_action('wdm_product_summary', 'cpbUnhookCpbActions', 1);
			remove_action('woocommerce_product_summary_thumbnails', 'woocommerce_show_product_thumbnails', 20);
			remove_action('woocommerce_before_single_product_summary_sale_flash', 'woocommerce_show_product_sale_flash', 10);
			remove_action('woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_images', 20);
			remove_action('woocommerce_single_product_summary_single_rating', 'woocommerce_template_single_rating', 10);
			remove_action('woocommerce_single_product_summary_single_title', 'woocommerce_template_single_title', 5);
			remove_action('woocommerce_single_product_summary_single_price', 'woocommerce_template_single_price', 10);
			remove_action('woocommerce_single_product_summary_single_excerpt', 'woocommerce_template_single_excerpt', 20);
			remove_action('woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30);
			remove_action('woocommerce_single_product_summary_single_meta', 'woocommerce_template_single_meta', 40);
			// remove_class_action('woocommerce_before_single_product_summary', $productDisplay, array('loadProductLayoutHtml', 'mobileListProductLayoutHtml'));
		}
	}
}

/**
* Remove some CPB actions to ensure theme compatability.
*/
function cpbUnhookCpbActions()
{
	remove_action('wdm_product_summary', 'woocommerce_template_single_meta', 40);
	remove_action('wdm_product_summary', 'woocommerce_template_single_title', 5);
	remove_action('wdm_product_summary', 'woocommerce_template_single_rating', 10);
	remove_action('wdm_product_summary', 'woocommerce_template_single_price', 10);
	remove_action('wdm_product_summary', 'woocommerce_template_single_excerpt', 20);
}
/**
* Gives theme compatibility with Flatsome theme
*/
function cpbShopkeeperCompatibilityCss()
{
	$custom_css = "
		.product-type-wdm_bundle_product .product_summary_thumbnails_wrapper {
			display: none;
		}

		.woocommerce-review-link {
			top: 5px;
		}

		div.mobile-list-layout-add-on-product-quantity div.cart div.bundled_item_wrap div.quantity_button div.quantity {
			display: inline-block !important;
		}

		#contactTrigger > div.wdm-bundle-product-product-group > div.product.bundled_product.bundled_product_summary > p.bundled_product_title.product_title {
			font-size: larger !important;
		}

		.product-type-wdm_bundle_product div.row div.large-12.xlarge-10.xxlarge-9.large-centered.columns div.product_content_wrapper div.row div.large-6.xxlarge-5.large-push-0.columns {
			clear: both;
			width: 100%;
		}

		div.row div.wdm-vertical-cpb-layout div.wdm_product_info h1.product_title.entry-title {
			text-align: center;
			margin-bottom: 0;
		}

		div.wdm-bundle-product-product-group div.product.bundled_product.bundled_product_summary div p.bundled_product_title.product_title {
			text-align: center;
		}

		div.row div.wdm-vertical-cpb-layout div.wdm_product_info .price {
			text-align: center;
		}

		div.row div.wdm-vertical-cpb-layout form#wdm_product_bundle_container_form-right div#wdm-bundle-bundle-box-right.wdm-bundle-bundle-box {
			width: 100%;
		}

		div.row div.wdm-vertical-cpb-layout form#wdm_product_bundle_container_form-right div.wdm-bundle-product-product-group {
			width: 100%;
		}

		div.mobile-list-layout-add-on-product-quantity div.cart div.bundled_item_wrap div.quantity_button div.quantity.qty_none.buttons_added div.mobile-list-layout-quantity-field input.input-number.qty.number {
			display: block;
		}

		.mobile-list-layout-plus-button input.wdm-cpb-addon-qty-plus {
			min-width: 40px;
		}

		.mobile-list-layout-minus-button input.wdm-cpb-addon-qty-minus {
			min-width: 40px;
		}

		.product-type-wdm_bundle_product div.single-product-main-image.alpha div.wdm-mobile-list-cpb-layout div.wdm-cpb-product-layout-wrapper div.mobile_list_layout.product.bundled_product.bundled_product_summary div.clear {
			clear: both;
		}

		.product-type-wdm_bundle_product div.product-container div.product-main div.row.content-row.mb-0 div.product-info.summary.col-fit.col.entry-summary.product-summary {
			display: none;
		}

		.product-type-wdm_bundle_product div.product-container div.product-main  div#product-sidebar.col.large-2.hide-for-medium.product-sidebar-small {
			display: none;
		}

		.large-6 {
			max-width: 100% !important;
			-webkit-flex-basis: 100% !important;
			-ms-flex-preferred-size: 100% !important;
			flex-basis: 100% !important;
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
