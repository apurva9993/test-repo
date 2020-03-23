<?php
/**
 * CPB Add-on Product
 *
 * @version 4.0.0
 * @package CPB/Templates/Addons
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}

?>
<div class='cpb_quantities_wrap'>
	<input type="hidden" name="addon_quantities_list" id="addon_quantities_list" value="" />
</div>
