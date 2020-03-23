<?php
/**
 * Admin View: Custom Notices
 *
 * @package CPB/Meta-Boxes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// preparing variables to use in the HTML we are parsing.
$json_ids_imploded = implode( ',', array_keys( $json_ids ) );
$json_ids_json = esc_attr( json_encode( $json_ids ) );
$add_on_products = ! empty( $addon_items_list ) ? json_encode( $addon_items_list ) : '';

$helptip_image = WC()->plugin_url() . '/assets/images/help.png';
$helptip = __( 'Select the products which can be added to the Custom Product Box. Kindly select more than one product. If you add main variable product as an add-on then all its variations will be included as CPB add-ons', 'custom-product-boxes' );
?>
<div class="wdm_bundle_products_selector">

	<p class="form-field wdm_product_selector">
		<span class="product_field_type_title">
			<?php esc_html_e( 'Add-On Products', 'custom-product-boxes' ); ?>
			<img class="help_tip" data-tip="<?php echo esc_html( $helptip ); ?>" src="<?php echo esc_html( $helptip_image ); ?>" height="16" width="16" />
		</span>
		<input type="hidden" id="current_variable" name="current_variable" />
		<input type="hidden" id="add_on_products" name="add_on_products" value="<?php echo esc_attr( $add_on_products ); // @codingStandardsIgnoreLine. ?>" />
		<input type="hidden" id="product_field_type" name="product_field_type" class="wc_products_selections" style="width: 50%;" data-placeholder="Search for a product&hellip;" data-action="cpb_json_search_products_and_variations" data-multiple="true" data-selected="<?php echo $json_ids_json; // @codingStandardsIgnoreLine. ?>" value ="<?php echo $json_ids_imploded; // @codingStandardsIgnoreLine. ?>" data-excluded ="<?php echo $json_ids_json; // @codingStandardsIgnoreLine. ?>"/>

	</p>
</div>
