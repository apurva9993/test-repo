<?php
/*
Custom Product Boxes Addon for Depot Theme
*/

add_action('wp_enqueue_scripts', 'cpbDepotCompatibilityCss', 999);

add_action('woocommerce_before_single_product', 'cpbRemoveUnwantedActions');

/**
* Remove the unwanted action to ensure theme compatibility with the CPB.
*/
function cpbRemoveUnwantedActions()
{
	if (is_singular('product')) {
		$product = wc_get_product(get_the_ID());
		if ($product->is_type('wdm_bundle_product')) {
			remove_action('woocommerce_single_product_summary', 'depot_mikado_woocommerce_template_single_title', 5);
			remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 8);
		}
	}
}

function cpbDepotCompatibilityCss()
{
	if (is_singular('product')) {
		$custom_css = "
			
			div.mkd-single-product-content {
				width: initial !important;
			}

			div.wdm-vertical-cpb-layout {
				width: 1028px !important;
			}

			#wdm_product_bundle_container_form-right > div > div.wdm-bundle-product-product-group {
				width: 100%;
			}

			div.wdm-horizontal-cpb-layout {
				width: 1100px;
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
