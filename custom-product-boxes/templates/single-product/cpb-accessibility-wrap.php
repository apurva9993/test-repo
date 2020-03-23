<?php
/**
 * Accessibility Section.
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/single-product/cpb-accessibility-wrap.php
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
<div class="cpb-accessibility">
	<span class="cpb-refresh" title="Reset Box">
		<img src="<?php echo CPB()->plugin_url(); ?>/assets/public/images/reset.png">
	</span>
	<?php
	if ( ! get_option( 'cpb_vertical_hide_expand' ) ) {
		?>
			<span class="cpb-expand" title="Expand/Collapse Box">
				<img class="cpb-expand cpb-show" src="<?php echo CPB()->plugin_url(); ?>/assets/public/images/expand.png">
				<img class="cpb-compress cpb-hide" src="<?php echo CPB()->plugin_url(); ?>/assets/public/images/collapse.png">
			</span>
		<?php
	}
	?>
</div>
