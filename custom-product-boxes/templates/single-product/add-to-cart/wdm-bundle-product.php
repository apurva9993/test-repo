<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}

if ( ! $product->is_purchasable() ) {
	return;
}

$addon_list_array = json_encode( $addon_list );

echo wc_get_stock_html( $product ); // WPCS: XSS ok.

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

	<form 
		class="cart cpb_form"
		action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
		method="post"
		enctype="multipart/form-data"
		data-addon_list="<?php echo esc_attr( $addon_list_array ); ?>"
	>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

		<?php
		do_action( 'cpb_before_add_to_cart_quantity', $product );

		do_action( 'cpb_add_to_cart_quantity', $product );

		do_action( 'cpb_after_add_to_cart_quantity', $product );
		?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button cpb_add_to_cart_button button alt" id="add_to_cart_button"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>
