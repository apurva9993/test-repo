<?php
/**
 * CPB product pricing section before add to cart
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/add-to-cart/cpb-pricing-box.php.
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
?>
<?php if ( 'on' == $enable_box_total ) { ?>
<div class="cpb-box-total">
	<div class='cpb-d-flex cpb-align-center cpb-box-total-label'><?php echo esc_html( $gift_box_total ); ?></div>
	<div class='cpb-d-flex cpb-box-total-val' data-dynamic-price = "<?php echo esc_attr( $product->get_price() ); ?>" data-total-bundle-price = "0"><div><?php do_action( 'cpb_product_price_html', $product ); ?></div></div>
</div>
	<?php
}

if ( 'on' == $enabled_add_box && $base_price ) {
	?>
<div class="cpb-box-charges">
	<div class='cpb-d-flex cpb-align-center cpb-box-charges-label'><?php echo esc_html( $add_box_total ); ?></div>
	<div class='cpb-d-flex cpb-box-charges-val' data-reg-price = "<?php echo esc_attr( $price ); ?>" data-total-price = "<?php echo esc_attr( $price ); ?>"><?php do_action( 'cpb_product_base_price_html', $product ); ?></div>
</div>
	<?php
}
if ( $product->is_product_cpb_subscription() ) {
	?>
<div class="cpb-row cpb-clear cpb-quantity-box--assets cpb-align-items-center cpb-justify-content-center">
	<div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price-label'><?php echo esc_html( $signup_label ); ?></div>
	<div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price wdm-signup-price' data-signup-fee = "<?php echo esc_attr( $sign_up_fee ); ?>"><div class='cpb-col-xl-12 cpb-col-lg-12 cpb-col-md-12 cpb-col-sm-12'><?php do_action( 'cpb_product_signup_fee_html', $product ); ?></div></div>
</div>
	<?php
}
?>
<div class="cpb-grand-total">
	<div class='cpb-d-flex cpb-align-center cpb-grand-total-label'><?php echo esc_html( $grand_total ); ?></div>
	<div class='cpb-d-flex cpb-grand-total-val' data-total-price = "<?php echo esc_attr( $dynamic_price ); ?>"><div><?php do_action( 'cpb_product_grand_total', $product ); ?></div></div>
</div>
