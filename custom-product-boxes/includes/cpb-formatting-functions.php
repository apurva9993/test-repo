<?php
/**
 * WooCommerce Formatting
 *
 * Functions for formatting data.
 *
 * @package WooCommerce/Functions
 * @version 2.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Formats a box capacity by running it through a filter.
 *
 * @param  int $capacity Capacity of addon products a box can hold.
 * @return int
 */
function cpb_box_capacity( $capacity ) {
	return apply_filters( 'cpb_box_capacity', $capacity );
}
