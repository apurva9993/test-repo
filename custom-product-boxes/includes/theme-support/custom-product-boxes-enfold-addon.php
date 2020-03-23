<?php
/*
Custom Product Boxes Addon for The Retailer Theme
*/

add_action('wp_enqueue_scripts', 'cpbEnfoldCompatibilityCss', 999);
/**
* Gives theme compatibility with Enfold theme
*/
function cpbEnfoldCompatibilityCss()
{
	if (is_singular('product')) {
		$custom_css = "
			div.single-product-main-image.alpha {
				width: 80% !important;
			}

			div.single-product-summary {
				box-sizing: border-box !important;
				display: block;
				clear: both !important;
			}
			.cpb_main_qty {
				width: 30% !important;
			}

			div.mobile-list-layout-add-on-product-quantity div.cart div.bundled_item_wrap div.quantity_button div.quantity div.mobile-list-layout-quantity-field input.minus {
				display: none !important;
			}

			div.mobile-list-layout-add-on-product-quantity div.cart div.bundled_item_wrap div.quantity_button div.quantity div.mobile-list-layout-quantity-field input.plus {
				display: none !important;
			}

			div.mobile-list-layout-add-on-product-quantity div.cart div.bundled_item_wrap div.quantity_button div.quantity.qty_none.buttons_added div.clear {
				clear: both !important;
			}

			@media ( max-width: 768px ) {
				div.single-product-main-image.alpha {
					width: auto !important;
				}
			}

			div.cpb-row.cpb-clear div.wdm_product_bundle_container_form form#wdm_product_bundle_container_form-right.cpb-flex-wrap-reverse.cpb-flex-row-reverse.cpb-row.cpb-clear div.cpb-col-sm-6.cpb-col-md-6.cpb-col-lg-6.cpb-col-xl-6 div.wdm-bundle-product-product-group {
				width: 100% !important;
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
