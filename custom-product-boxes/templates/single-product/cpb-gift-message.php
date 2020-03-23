<?php
/**
 * The template for displaying Add to cart button within loops
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/template/vertical/wdm-cpb-vertical-product-layout.php.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.6.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $gift_message;

if ( ! is_singular( 'product' ) || $product->get_type() !== 'wdm_bundle_product' ) {
	return;
}
//Template to display the gift message field before price and add-to-cart fields.
$gift_label = $gift_message->get_message_label( $product->get_id() );
do_action( 'wdm_cpb_before_gift_message' );
?>
<div class = "cpb_gift_msg" >
	<label for = "cpb_gift_message" ><p class = "price">
		<?php
			echo $gift_label;
		?>
		</p>
	</label>
	<textarea
		class = "cpb_gift_message"
		name = "wdm_gift_message"
		placeholder = "<?php _e( 'Add a gift message here', 'custom-product-boxes' ); ?>"
		data-product-id = "<?php echo $product->get_id(); ?>"
	/></textarea>
</div>
<?php
do_action( 'wdm_cpb_after_gift_message' );
