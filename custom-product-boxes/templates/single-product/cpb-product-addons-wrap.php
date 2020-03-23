<?php
/**
 * Addon Products section.
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/cpb-empty-boxes-wrap.php
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

?>
<div class="cpb-products-wrap-container" id="addon-box">
	<div class="<?php echo apply_filters( 'cpb_product_addons_wrap_class', 'cpb-products-wrap' ); ?>" <?php echo apply_filters( 'cpb_product_addons_wrap_data_attr', '' ); ?> >
		<?php
		foreach ( $addon_products as $addon_id => $addon_data ) {
			do_action( 'before_cpb_single_addon_product', $addon_id, $addon_data );

			do_action( 'cpb_single_addon_product', $addon_id, $addon_data, $cpb_product );

			do_action( 'after_cpb_single_addon_product', $addon_id, $addon_data );
		}
		?>
	</div>
	<?php if ( ! get_option( 'cpb_vertical_hide_scroll_indicator' ) ) { ?>
		<div class="scroll-indicator">
			<img src="<?php echo CPB()->plugin_url(); // @codingStandardsIgnoreLine. ?>/assets/public/images/up-arrow.png">
		</div> 
	<?php } ?>
	
</div>
