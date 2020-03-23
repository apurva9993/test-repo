<?php
/**
 * Progress Section.
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/cpb-progress-wrap.php
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
<div class="<?php echo apply_filters( 'cpb_progress_wrap_classes', 'progress-wrap' ); ?>" <?php echo apply_filters( 'cpb_progress_wrap_data_attributes', '' ); ?> >
	<div class="cpb-filled-progress">
		<div class="cpb-box-count">
			<span class="cpb-filled-count"><b>0</b></span>
			<span class="cpb-slash">/</span>
			<span class="cpb-total-count"><?php echo esc_html( $product->get_box_capacity() ); ?></span>
		</div>
		<div class="cpb-progress-bar">
			<div class="cpb-filled-part">
				
			</div>
		</div>
	</div>
	<div class="cpb-calculated-price-wrap">
		<span class="cpb-calculated-label">Grand Total</span>
		<span class="cpb-calculated-price"><?php echo strip_tags( wc_price( wc_get_price_to_display( $product ) ) ); ?></span>
	</div>
	<?php do_action( 'cpb_accessibility_wrap' ); ?>
	<div class="box-full-msg">
		<span>Box is Full.</span>
	</div>
</div>
