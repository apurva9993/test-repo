<?php
/**
 * CPB Admin Functions
 *
 * @package  CPB/Admin/Functions
 * @version  4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Displays an admin notice.
 *
 * @param [HTML string] $message message to display.
 * @param string|css    $class   CSS class for message.
 */
function cpb_print_admin_notices( $message, $class ) {
	if ( current_user_can( 'activate_plugins' ) ) {
		$classes = implode( ' ', $class );
		?>
		<div id="message" class="notice <?php echo esc_attr( $classes ); ?>">
		<?php echo '<p>' . esc_html( $message ) . '</p>'; ?>
		</div>
		<?php
	}
}

/**
 * Displays an admin notice if the woocommerce plugin is not active.
 */
function cpb_base_plugin_inactive_notice() {
	// Notice html for base plugin missing. %2$s is Custom Product Boxes and %6$s is Custom Product Boxes again.
	$message = sprintf( esc_html_e( '%1$s %2$s is inactive.%3$s Install and activate %4$sWooCommerce%5$s for %6$s to work.', 'custom-product-boxes' ), '<strong>', EDD_WCPB_ITEM_NAME, '</strong>', '<a href="http://wordpress.org/extend/plugins/woocommerce/">', '</a>', EDD_WCPB_ITEM_NAME );
	cpb_print_admin_notices( $message, 'error' );
}

/**
 * Displays an admin notice if the subscription plugin is not active.
 */
function cpb_subscription_not_active() {
	$message = sprintf( esc_html_e( '%1$s %2$s is inactive.%3$s Install and activate %4$sWooCommerce%5$s for %6$s to work with %7$s.', 'custom-product-boxes' ), '<strong>', 'WooCommerce Subscriptions', '</strong>', '<a href="http://woocommerce.com/products/woocommerce-subscriptions/">', '</a>', 'Subscription Model', EDD_WCPB_ITEM_NAME );
	cpb_print_admin_notices( $message, 'notice-warning' );
}

/**
 * Gets screen id for Custom menu pages
 *
 * @param string $slug slug for page.
 */
function cpb_get_screen_id( $slug ) {
	global $_parent_pages;
	$parent = array_key_exists( $slug, $_parent_pages ) ? $_parent_pages[ $slug ] : '';
	return get_plugin_page_hookname( $slug, $parent );
}

/**
 * This function is used to display helptip.
 *
 * @param String $helptip  Help tip to be displayed.
 * @param bool   $settings if it is a setting.
 * @param string $image    alternate for image.
 * @param string $title    title for help tip.
 *
 * @return [HTML string] [helptip]
 */
function cpb_help_tip( $helptip, $settings = false, $image = '', $title = '' ) {
	if ( true === $settings ) {
		$value = '';

		if ( version_compare( WC_VERSION, '2.5', '<' ) ) {
			$value = '<img class="help_tip" data-tip="' . esc_attr( $helptip ) . '" src="' . WC()->plugin_url() . '/assets/images/help.png" height="16" width="16" />';
		} else {
			$value = \wc_help_tip( $helptip );
		}

		return $value;
	}
	if ( ! empty( $image ) ) {
		return '<img class="help_tip tips" alt="' . esc_attr( $title ) . '" data-tip="' . esc_attr( $helptip ) . '" src="' . $image . '" height="25" width="25" />';
	}

	return '<span class="help_tip tips" data-tip="' . esc_attr( $helptip ) . '">' . esc_attr( $title ) . '</span>';
}

/**
 * Gets last order number of CPB product before GDPR implementation
 */
function get_last_order_number() {
	if ( get_option( 'wdmcpb_last_order_updated' ) == 'no' ) {
		return false;
	}

	global $wpdb;
	$order_number = get_option( 'wdmcpb_last_order_id' );

	$query = 'SELECT * FROM 
	`' . $wpdb->prefix . 'woocommerce_order_items` ORDER BY `' . $wpdb->prefix . 'woocommerce_order_items`.`order_id` DESC LIMIT 1';

	$order_result = $wpdb->get_row( $query ); // @codingStandardsIgnoreLine.

	if ( ! $order_result ) {
		return false;
	}

	$order_id = $order_result->order_id;

	return ! empty( $order_number ) ? $order_number : $order_id;
}

/**
 * Below we will be calling get_variation_attributes function which makes a call to get_attributes.
 * get_variation_attributes uses name of attribute as a key in the returned array We need to change the
 * behavior and we need to have a 'slug' as a key in the array returned by get_variation_attributes.
 * Therefore, we'll change the array returned by get_attributes. We'll keep name as slug in get_attributes array.
 *
 * @param array $attributes Array of attributes for the product.
 */
function cpb_modify_product_attribute_names( $attributes ) {

	if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $attribute_slug => $attribute_data ) {
				$attributes[ $attribute_slug ]['name'] = $attribute_slug;
				unset( $attribute_data );
			}
		}
	} else {
		// For 3.0 and greater.
		if ( is_array( $attributes ) ) {
			foreach ( $attributes as $attribute_slug => $attribute_object ) {
				$attribute_data = $attribute_object->get_data();
				if ( $attribute_data['is_variation'] ) {
					$attributes[ $attribute_slug ] = $attribute_object->get_slugs();
				} else {
					unset( $attributes[ $attribute_slug ] );
				}
			}
		}
	}

	return $attributes;
}

/**
 * This function return the variation details appended in a string format.
 *
 * @param array $variation_data Variation details.
 * @param array $variable_product Variable product details.
 * @param array $product_attributes Product variation attributes.
 * @return string $variation_to_be_sent Variation to be sent.
 */
function get_cpb_variation_string( $variation_data, $variable_product, $product_attributes ) {
	$variation_to_be_sent = '';
	foreach ( $variation_data as $name => $value ) {
		$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $name ) ) );

			// If this is a term slug, get the term's nice name.
		if ( taxonomy_exists( $taxonomy ) ) {
			$term = get_term_by( 'slug', $value, $taxonomy );
			if ( ! is_wp_error( $term ) && $term && $term->name ) {
				$value = $term->name;
			}
			$label = wc_attribute_label( $taxonomy );
			// If this is a custom option slug, get the options name.
		} else {
			$label = cpb_variation_attribute_label( $variable_product, $name, $product_attributes );
		}

		if ( ! empty( $variation_to_be_sent ) ) {
			$variation_to_be_sent .= '';
		}

		$variation_to_be_sent .= $label . ': ' . stripcslashes( $value ) . ', ';
	}

	return $variation_to_be_sent;
}

/**
 * Returns Attribute Name for variations which are not Taxonomies.
 *
 * @param string $variable_product Variable Product.
 * @param string $variation_attribute Variation attribute.
 * @param array  $all_attributes ALL variation attributes.
 * @return string $label label for variation.
 */
function cpb_variation_attribute_label( $variable_product, $variation_attribute, $all_attributes ) {
	if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
		if ( isset( $all_attributes[ str_replace( 'attribute_', '', $variation_attribute ) ] ) ) {
			$label = wc_attribute_label( $all_attributes[ str_replace( 'attribute_', '', $variation_attribute ) ]['name'] );
		} else {
			$label = $variation_attribute;
		}
	} else {
		$label = wc_attribute_label( str_replace( 'attribute_', '', $variation_attribute ), $variable_product );
	}
	return $label;
}

