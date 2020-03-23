<?php
/**
 * Product Empty Box section.
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

global $product;
?>
<div class="cpb-product-box-wrap-container">
	<!-- <div class="cpb-dynamic-price">
		<span>$50</span>
	</div> -->
	<?php do_action( 'cpb_progress_wrap' ); ?>
	
	<div class="<?php echo apply_filters( 'cpb_empty_boxes_wraps_classes', 'cpb-product-box-wrap' ); ?>" <?php echo apply_filters( 'cpb_empty_boxes_wrap_data_attributes', '' ); ?> >
		<?php
		// for ( $i = 1; $i <= $product->get_box_capacity(); $i++ ) {
		if ( ! $product->has_prefilled_products() ) {
			do_action( 'cpb_single_empty_box', $total_capacity, 1 );
			// $this->displayBlankBlocks( $total_clm, 1 );
		} else {
			do_action( 'cpb_single_empty_and_prefilled_box', $total_capacity );
		}
		// }
		?>
	</div>
	<?php if ( ! get_option( 'cpb_vertical_hide_scroll_indicator' ) ) { ?>
		<div class="scroll-indicator">
			<img src="<?php echo CPB()->plugin_url(); // @codingStandardsIgnoreLine. ?>/assets/public/images/up-arrow.png">
		</div> 
	<?php } ?>
	
</div>
