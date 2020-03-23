<?php
/**
 * Addon Product Title
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/addon-product/cpb-addon-title.php.
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

?>
<div class="cpb-product-title">
	<span><?php echo wp_kses_post( apply_filters( 'cpb_addon_product_title', $addon_data['text_name'] ) ); ?></span>
</div>
