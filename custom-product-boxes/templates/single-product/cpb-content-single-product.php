<?php
/**
 * The template for displaying CPB products content area
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/cpb-content-single-product.php
 *
 * HOWEVER, on occasion Custom Product Boxes will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WisdmLabs
 * @package CPB/Templates
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $post;

$cpb_product = CPB()->get_cpb_product( $post->ID );
?>

<div class="<?php echo apply_filters( 'cpb_content_classes', 'cpb-content cpb-text-center' ); ?>" <?php echo apply_filters( 'cpb_content_data_attributes', '' ); ?> >
	<div class="cpb-content-wrap">
		<?php do_action( 'before_cpb_empty_boxes_wrap' ); ?>

		<?php do_action( 'cpb_empty_boxes_wrap' ); ?>

		<?php do_action( 'after_cpb_empty_boxes_wrap' ); ?>

		<?php do_action( 'before_cpb_product_addons_wrap' ); ?>

		<?php do_action( 'cpb_product_addons_wrap', $cpb_product, $addon_list ); ?>

		<?php do_action( 'after_cpb_product_addons_wrap' ); ?>

		<?php if ( 'yes' == $cpb_product->get_enable_gift_message() ) { ?>
			<div class="cpb-gift-message-wrap">
				<?php do_action( 'cpb_gift_message_html', $cpb_product ); ?>
			</div>
		<?php } ?>
		<div class="cpb-cart-wrap">
			<?php do_action( 'cpb_wdm_bundle_product_add_to_cart', $cpb_product, $addon_list ); ?>
		</div>
	</div>
</div>
