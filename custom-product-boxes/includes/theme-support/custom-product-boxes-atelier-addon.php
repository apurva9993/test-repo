<?php
/*
Custom Product Boxes Addon for The Atelier Theme
*/

// If needed compatibility add changes here

add_action('wp_enqueue_scripts', 'cpbAvadaCompatibilityCss', 999);
/**
* Gives theme compatibility with Atelier theme
*/
function cpbAtelierCompatibilityCss()
{
	if (is_singular('product')) {
		$custom_css = "
			div.container.product-main > div.wdm-mobile-list-cpb-layout > div.mobile-list-layout-cpb-product-add-to-cart > div > div > div.bundle_button > div.quantity {
				width: -webkit-fill-available;
				max-width: 81px;
			}
					";

		wp_add_inline_style('wdm-cpb-style', $custom_css);
	}
}
