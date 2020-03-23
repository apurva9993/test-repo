<?php
/**
 * Outputs Prefilled products table.
 *
 * @package CPB/Meta-Boxes/Prefilled
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $cpb_product;

$mandatory_tip = __( 'Check to make the selected pre-filled products mandatory in the custom box. (Products which are marked mandatory cannot be removed from the box)', 'custom-product-boxes' );
?>
	<div class="prefill_div">
		<table class="prefill_table" id="prefill_table_id">
			<thead>
				<tr>
					<th>
						<span class="help_tip tips" data-tip="<?php echo esc_attr( $mandatory_tip ); ?>"><?php esc_html_e( 'Mandatory', 'custom-product-boxes' ); ?>
						</span>
					</th>
					<th><?php esc_html_e( 'Product Name', 'custom-product-boxes' ); ?></th>
					<th><?php esc_html_e( 'Quantity', 'custom-product-boxes' ); ?></th>
					<th class = 'cpb_blank'></th>
				</tr>
			</thead>
			<tbody>
				<?php
				// Code for prefilled rows from php.
				do_action( 'cpb_prefill_rows', $cpb_product, $addon_list );
				?>
				  
			</tbody>
		</table>

	</div>
