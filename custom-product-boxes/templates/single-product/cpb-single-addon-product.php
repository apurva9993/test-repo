<?php
/**
 * Addon Products section.
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/cpb-single-addon-product.php
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

global $addon_product;

?>
<div class="cpb-product">
	<div class="cpb-product-inner" id="cpb-product-<?php echo esc_attr( $addon_id ); ?>" data-count="0" data-id="<?php echo esc_attr( $addon_id ); ?>" data-stock = "<?php echo esc_attr( $addon_product->get_stock_quantity() ); ?>">
		<?php do_action( 'cpb_addon_image', $single_addon_id, $addon_product ); ?>
		<div class="cpb-product-info">
			<?php do_action( 'cpb_addon_title', $addon_data, $addon_product ); ?>
			<?php do_action( 'cpb_stock_html', $addon_product ); ?>
			<?php do_action( 'cpb_addon_price', $cpb_product, $addon_product ); ?>
		</div>
	</div>
</div>
