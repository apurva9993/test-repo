<?php
/**
 * Template Name: Vertical New Layout
 * The template Get the CPB Product info., and display in the vertical format.
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/template/vertical/wdm-cpb-vertical-product-layout.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

global $product, $addon_list;

if ( $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}

do_action( 'cpb_before_template_starts' );
?>
<div class="<?php echo apply_filters( 'cpb_main_wrapper_classes', 'cpb-vertical-layout' ); ?>" <?php echo apply_filters( 'cpb_main_wrapper_data_attributes', '' ); ?> >
	<?php do_action( 'cpb_product_title' ); // CPB Product Title. ?>
	<?php do_action( 'cpb_short_description' ); // CPB product Short description. ?>
	<?php do_action( 'cpb_content_area', $addon_list ); // CPB Product content. ?>
</div>
<?php
do_action( 'cpb_after_template_starts' );
