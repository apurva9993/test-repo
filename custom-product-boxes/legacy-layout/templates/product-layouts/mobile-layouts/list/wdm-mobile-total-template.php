<?php
/**
 * CPB Mobile Total Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 3.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

global $product;
if ( $product->is_type( 'wdm_bundle_product' ) && $product->is_product_cpb_subscription() ) {
	$subs_price_string = WC_Subscriptions_Product::get_price_string(
		$product,
		array(
			'price' => wc_price( $product->get_price() ),
			'subscription_length' => true,
			'sign_up_fee' => true,
			'trial_length' => true,
		)
	);
	?>
<p class="price"><?php echo $subs_price_string; // @codingStandardsIgnoreLine. ?></p>
	<?php
}
?>
