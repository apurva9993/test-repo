<?php
/**
 * Template Name: Horizontal Layout
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/template/horizontal/wdm-cpb-horizontal-product-layout.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
// Get the CPB Product info., and display in the horizontal format.
global $addon_list;
$box_color = get_option( 'cpb_gift_boxes_color', '#f2f2f2' );
$bg_color = get_option( 'cpb_gift_bgcolor', '#faebd7' );

if ( ! is_singular( 'product' ) || $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}
$signup_fee = $product->is_product_cpb_subscription() ? WC_Subscriptions_Product::get_sign_up_fee( $product ) : 0;
do_action( 'wdm_cpb_before_template_starts' );
do_action( 'cpb_before_template_starts' );
?>

<div id = "wdm-horizontal-cpb-container" class="wdm-horizontal-cpb-layout wdm-cpb-product-size">
<style>.wdm-bundle-single-product{background-color:<?php echo esc_attr( $box_color ); ?>;}</style>
	<?php
	// Remove the CPB Product display hooks because,initially it caused template disorder.
	do_action( 'wdm_cpb_remove_wc_product_display_hooks' );

	do_action( 'woocommerce_before_add_to_cart_form' );
	?>

	<div class = "wdm_fix_div">
		<div class="wdm_product_info">
		<?php
			do_action( 'before_wdm_cpb_main_product_info' );

			do_action( 'wdm_cpb_main_product_info' );

			do_action( 'after_wdm_cpb_main_product_info' );
		?>
		</div>
		<div class="cpb-row cpb-clear">
		<div style = "background-color:<?php echo esc_attr( $bg_color ); ?>;" class ="wdm-bundle-bundle-box cpb-col-sm-12 cpb-col-md-12 cpb-col-lg-12 cpb-col-xl-12" data-bundle-price = "<?php echo esc_attr( $product->get_price() ); ?>" data-signup-fee = "<?php /*echo $signup_fee;*/ ?>">
		<div class ="gift_box_wrap cpb-container-fluid">

		<?php
			do_action( 'before_wdm_gift_layout' );

			/*
			Custom product boxes gift layout hook
			*/
			do_action( 'wdm_gift_layout' );

			do_action( 'after_wdm_gift_layout' );
		?>
		</div>
	</div>
</div>
	<?php
	/*
	Custom product boxes product layout hook
	*/
	do_action( 'before_wdm_product_layout' );

	do_action( 'wdm_cpb_enqueue_scripts' );
	?>

	<div class="wdm_product_bundle_container_form" >
		<?php
			do_action( 'wdm_cpb_before_add_to_cart_form' );
		?>
		<form name = "wdmBundleProduct" method="post" enctype="multipart/form-data" id="contactTrigger" novalidate>
			<div style = "width:100%;" class = "wdm-bundle-product-product-group">

			<?php
				do_action( 'wdm_cpb_add_to_cart_form', $product );
			?>

			</div>
			<div class="cpb-col-sm-12 cpb-col-md-12 cpb-col-lg-12 cpb-col-xl-12 gift-message-box--wrapper">
				<div class="gift-message-box">
					<?php
						do_action( 'wdm_cpb_before_bundle_pricing_box' );

						do_action( 'wdm_cpb_add_to_cart_button', $product );

						do_action( 'wdm_cpb_after_add_to_cart_button' );
					?>
				</div>
			</div>
		</form>
		<?php

			do_action( 'wdm_cpb_after_add_to_cart_form' );

			do_action( 'after_wdm_product_layout' );

		?>
	</div>
	</div>
	<div class="clear"></div>

</div>
