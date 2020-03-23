<?php
/*
Custom Product Boxes Addon for The Avada Theme
*/

// If needed compatibility add changes here

add_action('wp_enqueue_scripts', 'cpbAvadaCompatibilityCss', 999);
/**
* Gives theme compatibility with Avada theme
*/
function cpbAvadaCompatibilityCss()
{
	if (is_singular('product')) {
		$custom_css = "
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
