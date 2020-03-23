<?php
/**
 *
 * Template Name: Vertical Right Layout
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/template/vertical/wdm-cpb-vertical-product-layout.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Get the CPB Product info., and display in the vertical-right format.

$box_color = get_post_meta( $product->get_id(), '_wdm_gift_boxes_color', true );
if ( ! is_singular( 'product' ) || $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}
$signup_fee = $product->is_product_cpb_subscription() ? WC_Subscriptions_Product::get_sign_up_fee( $product ) : 0;

do_action( 'wdm_cpb_before_template_starts' );
do_action( 'cpb_before_template_starts' );
?>
<div class = "row">
	<div class="wdm-vertical-cpb-layout">
	<style>.wdm-bundle-single-product{background-color:<?php echo esc_html( $box_color ); ?>;}</style>
		<?php
		// Remove the CPB Product display hooks because,initially it caused template disorder.
		do_action( 'wdm_cpb_remove_wc_product_display_hooks' );

		do_action( 'woocommerce_before_add_to_cart_form' );
		?>

		<div class="wdm_product_info">
		<?php
			do_action( 'before_wdm_cpb_main_product_info' );

			do_action( 'wdm_cpb_main_product_info' );

			do_action( 'after_wdm_cpb_main_product_info' );
		?>
		</div>

		<?php
		/*
		Custom product boxes product layout hook
		*/
		do_action( 'before_wdm_product_layout' );

		do_action( 'wdm_cpb_enqueue_scripts' );
		?>
	<div class="cpb-row cpb-clear">
		<div class="wdm_product_bundle_container_form" >

			<form id = "wdm_product_bundle_container_form-right" class="cpb-flex-wrap-reverse cpb-flex-row-reverse cpb-row cpb-clear" name = "wdmBundleProduct" method="post" enctype="multipart/form-data" id="contactTrigger" novalidate>
			  <div class="cpb-col-sm-6 cpb-col-md-6 cpb-col-lg-6 cpb-col-xl-6">
				<div id = "wdm-bundle-bundle-box-right" class="wdm-bundle-bundle-box" data-bundle-price = "<?php echo esc_attr( $product->get_price() ); ?>" data-signup-fee = "<?php /*echo $signup_fee;*/ ?>">
				<?php
					do_action( 'before_wdm_gift_layout' );

					/*
					Custom product boxes gift layout hook
					*/
					do_action( 'wdm_gift_layout' );

					do_action( 'after_wdm_gift_layout' );
				?>
					<div class="gift-message-box">
					<?php
					do_action( 'wdm_cpb_before_bundle_pricing_box' );
					do_action( 'wdm_cpb_add_to_cart_button', $product );
					do_action( 'wdm_cpb_after_add_to_cart_button' );
					?>
					</div>
				</div>
			   </div>
			   <div class="cpb-col-sm-6 cpb-col-md-6 cpb-col-lg-6 cpb-col-xl-6">
				<div class = "wdm-bundle-product-product-group">

				<?php
				do_action( 'wdm_cpb_before_add_to_cart_form' );

				do_action( 'wdm_cpb_add_to_cart_form', $product );

				do_action( 'wdm_cpb_after_add_to_cart_form' );

				do_action( 'wdm_product_layout' );

				do_action( 'after_wdm_product_layout' );
				?>

				</div>
			  </div>
			</form>
		</div>
	</div>
		<div class="clear"></div>
	</div>
</div>
<?php do_action( 'wdm_cpb_after_template_ends' ); ?>
