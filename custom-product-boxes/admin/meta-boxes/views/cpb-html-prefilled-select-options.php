<?php
/**
 * Outputs Prefilled products Mandatory Checkboxes.
 *
 * @package CPB/Meta-Boxes/Prefilled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( $addon_ids as $addon_id ) {
	$product_id = isset( $addon_list[ $addon_id ]['variation_id'] ) ? $addon_list[ $addon_id ]['variation_id'] : $addon_id;
	$addon_product = wc_get_product( $product_id );

	$parent_id = isset( $addon_list[ $addon_id ]['variation_id'] ) ? $addon_product->get_parent_id( $product_id ) : $addon_id;
	$product_data = $addon_list[ $addon_id ];
	?>
		<option value="<?php echo esc_attr( $addon_id ); ?>" data-id="<?php echo esc_attr( $product_id ); ?>" data-parent-id="<?php echo esc_attr( $parent_id ); ?>" <?php selected( $prefilled_data['unique_prod_id'], $addon_id ); ?>><?php echo esc_html( $product_data['text_name'] ); ?></option>
	<?php
}
