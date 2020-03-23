<?php
/*
Custom Product Boxes Addon for The Salient Theme
*/

// If needed compatibility add changes here

add_action('wp_enqueue_scripts', 'cpbSalientCompatibilityCss', 999);
/**
* Gives theme compatibility with Atelier theme
*/
function cpbSalientCompatibilityCss()
{
	if (is_singular('product')) {
		$custom_css = "
			div.container.product-main > div.wdm-mobile-list-cpb-layout > div.mobile-list-layout-cpb-product-add-to-cart > div > div > div.bundle_button > div.quantity {
				width: -webkit-fill-available;
				max-width: 81px;
			}

			.product-type-wdm_bundle_product > div.single-product-main-image {
				width: 80%;
				margin-left: 10%;
				margin-right: 0;
			}

			.product-type-wdm_bundle_product > div.single-product-main-image > div.wdm-mobile-list-cpb-layout > div.mobile-list-layout-cpb-product-add-to-cart > div > div > div.bundle_button {
				width: 258px;
			}

			#wdm-horizontal-cpb-container > div.wdm_fix_div > div.wdm_product_info > h1.product_title {
				padding: 0;
			}

			div.wdm-vertical-cpb-layout > div.wdm_product_info > h1.product_title {
				padding: 0;
			}

			.wdm-bundle-product-product-group > div.product.bundled_product.bundled_product_summary > div.px-15 > p.bundled_product_title.product_title {
				padding: 12px 0;
				clear: both;
				font-size: 16px !important;
				text-transform: capitalize;
			}
			";

		wp_add_inline_style('wdm-cpb-style', $custom_css);
	}
}
