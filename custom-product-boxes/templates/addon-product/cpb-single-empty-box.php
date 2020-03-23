<?php
/**
 * Single Empty Box Template.
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/addon-product/cpb-single-empty-box.php.
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

defined( 'ABSPATH' ) || exit;
global $product;
$empty_inner_classes = isset( $pre_product ) ? 'cpb-empty-box-inner filled' : 'cpb-empty-box-inner';
?>
<div class="cpb-empty-box">
	<div class="<?php echo apply_filters( 'cpb_empty_inner_classes', $empty_inner_classes ); ?>">
		<div class="cpb-image">
			
		</div>
		<div class="cpb-title">
			
		</div>
		<?php if ( isset( $pre_product ) ) { ?>
			<div 	class="<?php echo esc_attr( $classes ); ?>"
				id="<?php echo $prefill_product['unique_prod_id']; ?>"
				data-count="<?php echo esc_attr( $product_count ); ?>"
				data-id="<?php echo $prefill_product['unique_prod_id']; ?>"
				data-stock=""
			>
				<div class="cpb-product-image">
					<span class="cpb-count"></span>
					<div class="cpb-img-overlay">
						<span></span>
					</div>
					<?php echo get_product_image( $prefill_product['product_id'] ); ?>
				</div>
				<div class="cpb-product-info" style="min-height: 35px;">
					<div class="cpb-product-title">
					<span><?php echo esc_html( $pre_product->get_formatted_name() ); ?></span>
					</div>
					<?php // do_action( 'cpb_stock_html', $pre_product ); ?>
					<?php do_action( 'cpb_addon_price', $product, $pre_product ); ?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
