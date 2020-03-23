<?php
/**
 * Outputs variation data withing an accordion.
 *
 * @package CPB/Meta-Boxes
 */

// accodion_data.
foreach ( $product_var_data as $index => $product_data ) {
	?>
	<div class="cpb_variation_main" id="cpb_variation_main_<?php echo esc_attr( $product_data['variation_id'] ); ?>" data-variation_id="<?php echo esc_attr( $product_data['variation_id'] ); ?>">
		<span class = "cpb_variation_input">
			<input type="checkbox" class="cpb_variation_checkboxes" name="var1" value="<?php echo esc_attr( $index ); ?>" data-variable_id="<?php echo esc_attr( $product_id ); ?>" <?php checked( $product_data['selected'], 'yes' ); ?>/>
		</span>
		<span class = "cpb_variation_label">
			<label><?php echo esc_html( $product_data['text_name'] ); ?></label>
		</span>
	</div>
	<?php
}
$product_json_data = json_encode( $product_var_data );
?>
<input type="hidden" name="product_variation_data_<?php echo esc_attr( $product_id ); ?>" value = '<?php echo esc_attr( $product_json_data ); ?>'>
