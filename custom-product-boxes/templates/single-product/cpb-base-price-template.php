<?php
/**
 * Base Price Template
 *
 * @package CPB/Single-Product
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Subscriptions_Product' ) ) {
	return;
}

if ( $product->is_type( 'wdm_bundle_product' ) && $product->is_product_cpb_subscription() ) {
	$subs_price_string = WC_Subscriptions_Product::get_price_string(
		$product,
		array(
			'price' => wc_price( $product->get_price() ),
			'subscription_length' => true,
			'sign_up_fee' => false,
			'trial_length' => true,
		)
	);

	?>
<p class="price"><?php echo $subs_price_string; // @codingStandardsIgnoreLine. ?></p>
	<?php
}
?>
