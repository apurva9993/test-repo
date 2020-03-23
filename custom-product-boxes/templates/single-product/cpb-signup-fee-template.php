<?php
/**
 * Single Product Price
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

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Subscriptions_Product' ) ) {
	return;
}

$signup_fee = WC_Subscriptions_Product::get_sign_up_fee( $product );
?>
<p class="price" data-signup-fee = <?php echo esc_attr( $signup_fee ); ?>><?php echo wc_price( $signup_fee ); // @codingStandardsIgnoreLine. ?></p>
<?php
