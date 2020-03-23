<?php
/**
 * CPB product quantity section before add to cart
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/add-to-cart/cpb-quantity-html.php.
 *
 * Template before the add-to-cart button and add-to-cart button.
 *
 * HOWEVER, on occasion Custom Product Boxes will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package CPB/Templates
 * @version 4.0.0
 */

defined( 'ABSPATH' ) || exit;

// Display the price fields based on the product pricing selected in
// settings.
if ( $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}

?>
<div class="cpb-box-quantity">
	<div class='cpb-d-flex cpb-align-center cpb-quantity-label'><?php esc_html_e( 'Box Quantity', 'custom-product-boxes' ); ?></div>
	<div class="cpb-d-flex cpb-quantity-val">
		<div class='cpb-box-quantity-field-input'>
	<?php
		woocommerce_quantity_input(
			array(
				'min_value'   => esc_attr( apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ) ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);
		// $domHTML = \SHD\str_get_html($quantity_html);
		// $domHTML->find('input.qty')[0]->class .= " cpb_main_qty";
		// $domHTML->find('input.qty')[0]->id = "cpb_main_qty_desk";
		// echo $domHTML;
		?>
	</div>
	</div>
</div>
