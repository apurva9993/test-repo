<?php
/**
 * Addon Product Image
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/addon-product/cpb-addon-image.php.
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
<div class="cpb-product-image">
	<span class="cpb-count"></span>
	<div class="cpb-img-overlay">
		<span>
			
		</span>
	</div>
	<?php echo wp_kses_post( $product->get_image() ); ?>
</div>
