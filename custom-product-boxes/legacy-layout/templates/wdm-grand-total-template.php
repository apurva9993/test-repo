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

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $product;

$price = wc_get_price_to_display($product);
$signupFee = WC_Subscriptions_Product::get_sign_up_fee($product);

$grandTotal = $price + $signupFee;

if ($product->is_type('wdm_bundle_product') && $product->is_product_cpb_subscription()) {
?><p class="price"><?php echo wc_price($grandTotal); ?></p>
<?php
}
?>
