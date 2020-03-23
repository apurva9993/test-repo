<?php
/**
 * Outputs accordion for variable product selected.
 *
 * @package CPB/Meta-Boxes
 */

defined( 'ABSPATH' ) || exit;

if ( ! empty( $addon_items_list ) ) {
	foreach ( $addon_items_list as $product_id => $add_on ) {
		if ( 'variable' !== $addon_items_list[ $product_id ]['product_type'] ) {
			continue;
		}
		$product = wc_get_product( $product_id );
		$add_on = esc_attr( json_encode( $add_on['variations'] ) );
		?>
		<div class="cpb-group group" id="cpb_variation_wrapper-<?php echo esc_attr( $product_id ); ?>" data-variable_id="<?php echo esc_attr( $product_id ); ?>">
			<h3><?php echo wp_kses_post( $product->get_formatted_name() ); ?></h3>
			<div>
				<div class="cpb_variation_wrapper" data-selected_variations="<?php echo esc_attr( $add_on ); ?>">
				</div>
			</div>
		</div>
		<?php
	}
}
