<?php
/*
Plugin Name: Custom Product Boxes Addon for Shopkeeper Theme
*/

add_action('woocommerce_before_single_product', 'cpRemoveUnwantedActions');

add_action('wp_enqueue_scripts', 'cpbShopkeeperCompatibilityCss', 999);
/**
* Remove the unwanted action to ensure theme compatibility with the CPB.
*/
function cpRemoveUnwantedActions()
{
	global $productDisplay;
	if (is_singular('product')) {
		$product = wc_get_product(get_the_ID());
		if ($product->is_type('wdm_bundle_product')) {
			add_action('wdm_product_summary', 'cpbUnhookCpbActions', 1);
			remove_action('woocommerce_product_summary_thumbnails', 'woocommerce_show_product_thumbnails', 20);
			remove_action('woocommerce_before_single_product_summary_sale_flash', 'woocommerce_show_product_sale_flash', 10);
			// remove_action('woocommerce_before_single_product_summary_product_images', 'woocommerce_show_product_images', 20);
			remove_action('woocommerce_single_product_summary_single_rating', 'woocommerce_template_single_rating', 10);
			remove_action('woocommerce_single_product_summary_single_title', 'woocommerce_template_single_title', 5);
			remove_action('woocommerce_single_product_summary_single_price', 'woocommerce_template_single_price', 10);
			remove_action('woocommerce_single_product_summary_single_excerpt', 'woocommerce_template_single_excerpt', 20);
			remove_action('woocommerce_single_product_summary_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30);
			remove_action('woocommerce_single_product_summary_single_meta', 'woocommerce_template_single_meta', 40);
			removeClassAction('woocommerce_before_single_product_summary', $productDisplay, array('loadProductLayoutHtml', 'mobileListProductLayoutHtml'));
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

		.wdm-vertical-cpb-layout {
			width: 100%;
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

		div.row div.wdm-vertical-cpb-layout form.wdm_product_bundle_container_form div#wdm-bundle-bundle-box-right.wdm-bundle-bundle-box {
			width: 100%;
		}

		div.row div.wdm-vertical-cpb-layout form.wdm_product_bundle_container_form div.wdm-bundle-product-product-group {
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
		div.cpb-row.cpb-clear div.wdm_product_bundle_container_form form#wdm_product_bundle_container_form-right.cpb-flex-wrap-reverse.cpb-flex-row-reverse.cpb-row.cpb-clear div.cpb-col-sm-6.cpb-col-md-6.cpb-col-lg-6.cpb-col-xl-6 div.wdm-bundle-product-product-group {
			width: 100% !important;
		}

		#cpb_main_qty_mobile {
			width: 100% !important;
			display: inline-block !important;
		}

		#cpb_main_qty_desk {
			width: 100% !important;
			display: inline-block !important;
		}

		.quantity {
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
/**
 * Remove Class Filter Without Access to Class Object
 *
 * In order to use the core WordPress remove_filter() on a filter added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove filters with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 *
 * @param string $tag         Filter to remove
 * @param string $className  Class name for the filter's callback
 * @param string $methodName Method name for the filter's callback
 * @param int    $priority    Priority of the filter (default 10)
 *
 * @return bool Whether the function is removed.
 */
function removeClassFilter( $tag, $classObject = '', $method_name = '', $priority = 10 ) {
	global $wp_filter;
	$class_name = get_class($classObject);

	// Check that filter actually exists first
	if (! isset($wp_filter[ $tag ])) {
		return FALSE;
	}

	/**
	 * If filter config is an object, means we're using WordPress 4.7+ and the config is no longer
	 * a simple array, rather it is an object that implements the ArrayAccess interface.
	 *
	 * To be backwards compatible, we set $callbacks equal to the correct array as a reference (so $wp_filter is updated)
	 *
	 * @see https://make.wordpress.org/core/2016/09/08/wp_hook-next-generation-actions-and-filters/
	 */
	if (is_object( $wp_filter[ $tag ]) && isset($wp_filter[ $tag ]->callbacks)) {
		$callbacks = &$wp_filter[ $tag ]->callbacks;
	} else {
		$callbacks = &$wp_filter[ $tag ];
	}

	// Exit if there aren't any callbacks for specified priority
	if (! isset($callbacks[ $priority ]) || empty($callbacks[ $priority ])) return FALSE;

	// Loop through each filter for the specified priority, looking for our class & method
	foreach ((array) $callbacks[ $priority ] as $filter_id => $filter) {

		// Filter should always be an array - array( $this, 'method' ), if not goto next
		if (! isset($filter[ 'function' ]) || ! is_array($filter[ 'function' ])) continue;

		// If first value in array is not an object, it can't be a class
		if (! is_object($filter[ 'function' ][ 0 ])) continue;

		// Method doesn't match the one we're looking for, goto next
		if (!in_array($filter[ 'function' ][ 1 ], $method_name)) continue;

		// Method matched, now let's check the Class
		if (get_class( $filter[ 'function' ][ 0 ]) === $class_name) {
			// Now let's remove it from the array
			unset($callbacks[ $priority ][ $filter_id ]);

			// and if it was the only filter in that priority, unset that priority
			if (empty($callbacks[ $priority ])) unset($callbacks[ $priority ]);

			// and if the only filter for that tag, set the tag to an empty array
			if (empty($callbacks)) $callbacks = array();

			// If using WordPress older than 4.7
			if (! is_object($wp_filter[ $tag ])) {
				// Remove this filter from merged_filters, which specifies if filters have been sorted
				unset($GLOBALS[ 'merged_filters' ][ $tag ]);
			}

			return TRUE;
		}
	}

	return FALSE;
}

function loopThroughFilters($callbacks, $priority, $methodName, $className, $tag)
{
	foreach ((array) $callbacks[ $priority ] as $filterId => $filter) {
		// Filter should always be an array - array( $this, 'method' ), if not goto next
		if (! isset($filter[ 'function' ]) || ! is_array($filter[ 'function' ])) {
			continue;
		}

		// If first value in array is not an object, it can't be a class
		if (! is_object($filter[ 'function' ][ 0 ])) {
			continue;
		}

		// Method doesn't match the one we're looking for, goto next
		if (!in_array($filter[ 'function' ][ 1 ], $methodName)) {
			continue;
		}

		// Method matched, now let's check the Class
		removeFilterFromArray($callbacks, $priority, $className, $filterId, $filter, $tag);
	}
}

function removeFilterFromArray($callbacks, $priority, $className, $filterId, $filter, $tag)
{
	global $wp_filter;
	if (get_class($filter[ 'function' ][ 0 ]) === $className) {
		// Now let's remove it from the array
		unset($callbacks[ $priority ][ $filterId ]);

		// and if it was the only filter in that priority, unset that priority
		if (empty($callbacks[ $priority ])) {
			unset($callbacks[ $priority ]);
		}

		// and if the only filter for that tag, set the tag to an empty array
		if (empty($callbacks)) {
			$callbacks = array();
		}

		// If using WordPress older than 4.7
		if (! is_object($wp_filter[ $tag ])) {
			// Remove this filter from merged_filters, which specifies if filters have been sorted
			unset($GLOBALS[ 'merged_filters' ][ $tag ]);
		}
	}
}


/**
 * Remove Class Action
 *
 * In order to use the core WordPress remove_action() on an action added with the callback
 * to a class, you either have to have access to that class object, or it has to be a call
 * to a static method.  This method allows you to remove actions with a callback to a class
 * you don't have access to.
 *
 * Works with WordPress 1.2+ (4.7+ support added 9-19-2016)
 *
 * @param string $tag         Action to remove
 * @param string $className  Class name for the action's callback
 * @param string $methodName Method name for the action's callback
 * @param int    $priority    Priority of the action (default 10)
 *
 * @return bool               Whether the function is removed.
 */
function removeClassAction($tag, $classObject, $methodName = '')
{
	foreach ($methodName as $value) {
		removeClassFilter($tag, $classObject, $methodName);
		add_action('woocommerce_single_product_summary', array($classObject, $value), 99);
	}
}
