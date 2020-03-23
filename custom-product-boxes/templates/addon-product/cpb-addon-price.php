<?php
/**
 * Addon Product Price
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/addon-product/cpb-addon-price.php.
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
// global $addon_product;
?>
<div class="<?php echo esc_attr( apply_filters( 'cpb_product_price_class', 'cpb-product-price price' ) ); ?>"><?php echo $single_product->get_price_html(); ?></div>
