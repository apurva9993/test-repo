<?php
/**
 * Outputs Prefilled products rows.
 *
 * @package CPB/Meta-Boxes/Prefilled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $cpb_product;

$max_val = $cpb_product->get_box_capacity();
$add_image = CPB()->plugin_url() . '/assets/admin/images/plus-icon.png';
$remove_image = CPB()->plugin_url() . '/assets/admin/images/minus-icon.png';
$ctr = 0;

if ( ! empty( $prefill_list ) ) {
	foreach ( $prefill_list as $prefilled_data ) {
		?>
		<tr>
			<?php do_action( 'cpb_mandatory_checkboxes', $addon_ids, $prefilled_data ); ?>
			<td>
				<select name="wdm_cpb_products[]" class="prefill_products_holder">
					<?php do_action( 'cpb_prefilled_select_options', $addon_ids, $prefilled_data, $addon_list ); ?>
				</select>
			</td>

		<td class="prefill_qty">
			<input type="number" name="wdm_prefill_qty[]" min="1" max="<?php echo esc_attr( $max_val ); ?> " class="prefill_qty_id" value="<?php echo esc_attr( $prefilled_data['product_qty'] ); ?>" />
		</td>
		<td>
			<a class="wdm_cpb_rem" href="#" id="">
				<img class="add_new_row_image" src="<?php echo wp_kses_post( $remove_image ); ?>" />
			</a>
			<?php if ( count( $prefill_list ) - 1 == $ctr ) { ?>
				<a class='wdm_cpb_add' href='#' id=''>
					<img class='add_new_row_image' src="<?php echo wp_kses_post( $add_image ); ?>" />
				</a>
			<?php } ?>
		</td></tr>
		<?php
		$ctr++;
	} //end of for
} // end if
