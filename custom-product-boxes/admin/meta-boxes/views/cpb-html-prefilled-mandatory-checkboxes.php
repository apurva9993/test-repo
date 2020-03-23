<?php
/**
 * Outputs Prefilled products Mandatory Checkboxes.
 *
 * @package CPB/Meta-Boxes/Prefilled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $cpb_product;
?>
<td>
	<input type="checkbox" class="prefill_checkbox" name="prod_mandatory[]" value="<?php echo in_array( $prefilled_data['unique_prod_id'], $addon_ids ) ? esc_attr( $prefilled_data['unique_prod_id'] ) : '0'; ?>" <?php checked( $prefilled_data['product_mandatory'], 1 ); ?>/>
</td>

