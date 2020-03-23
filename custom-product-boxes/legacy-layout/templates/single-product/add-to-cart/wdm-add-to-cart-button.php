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

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $old_product;

if (!is_singular('product') || $product->get_type() !== 'wdm_bundle_product') {
    return;
}
//If some mandatory products are out of stock and they cannot be swapped
// then add the disable class
$disable_class = 'single_add_to_cart_button bundle_add_to_cart_button button alt';
if ($disabled == "disabled") {
    $disable_class .= " os_pf_m";
}

$reg_price = wc_get_price_to_display($product, array('price' => $product->get_regular_price()));
$price = wc_get_price_to_display($product);
$basePrice = get_post_meta($product->get_id(), '_product_base_pricing_active', true);
$dynamicPrice = get_post_meta($product->get_id(), '_per_product_pricing_active', true);
$product_id = $product->get_id();
// $bundle_price_data = $product->getBundlePriceData();
// $bundle_item_quantity = $old_product->getBundledItemQuantities();

// $quantities_array = esc_attr(json_encode($bundle_item_quantity));
// $bundle_price_data = esc_attr(json_encode($bundle_price_data));
$classes = "cart bundle_form bundle_form_{$product_id}";
$availability = $product->get_availability();
$stock_message = "";
if ($availability['availability']) {
    $stock_message = apply_filters('woocommerce_stock_html', '<p class="stock ' . $availability['class'] . '">' . $availability['availability'] . '</p>', $availability['availability']);
}


$grandTotal = !get_option("_grand_total_label") ? __("Grand Total", 'custom-product-boxes') : get_option("_grand_total_label");
$enableBoxTotal = get_option('_wdm_enable_giftbox_total');
$giftBoxTotal = !get_option("_giftbox_total_label") ? __("Gift Box Total", 'custom-product-boxes') : get_option("_giftbox_total_label");
$enabledAddBox = get_option('_wdm_enable_addbox_total');
$addBoxTotal = !get_option("_addbox_total_label") ? __("Aditional Box Charges", 'custom-product-boxes') : get_option("_addbox_total_label");

if (isProductCPBSubscription($product)) {
    $signupLabel = __("Sign-up Fee", 'custom-product-boxes');
    $signUpFee = get_post_meta($product->get_id(), '_subscription_sign_up_fee', true);
    // $grandTotal += $signUpFee;
}

?>
<div
    class = "<?php echo $classes; ?>"
    data-bundle-price-data = ""
    data-bundle-id = "<?php echo $product_id; ?>"
>

<?php
do_action('woocommerce_before_add_to_cart_button');
?>

    <div class="bundle_wrap">
        <?php
            echo $stock_message;
            //Template before the add-to-cart button and add-to-cart button.
            //Display the price fields based on the product pricing selected in
            //settings.
            //Enable or disable add-to-cart button on basis of availability of some //products.
        ?>

        <div class="bundle_button">
        <div class="cpb-row cpb-clear cpb-quantity-box--assets cpb-align-items-center cpb-justify-content-center">
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6'><?php _e('Box Quantity', 'custom-product-boxes'); ?></div>
            <div class="cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle_button--qtyBtn">
                <div class='cpb-col-xl-7 cpb-col-lg-7 cpb-col-md-7 cpb-col-sm-7 cpb-box-quantity-field-input'>
            <?php
                $quantity_html =  woocommerce_quantity_input(array('min_value' => 1), null, false);
                $domHTML = \SHD\str_get_html($quantity_html);
                $domHTML->find('input.qty')[0]->class .= " cpb_main_qty";
                $domHTML->find('input.qty')[0]->id = "cpb_main_qty_desk";
                echo $domHTML;
            ?>
            </div>
            </div>
        </div>
        <?php if ($enableBoxTotal == 'on') { ?>
        <div class="cpb-row cpb-clear cpb-quantity-box--assets cpb-align-items-center cpb-justify-content-center">
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price-label'><?php echo $giftBoxTotal; ?></div>
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price wdm-bundle-total wdm_bundle_price' data-dynamic-price = "<?php echo $dynamicPrice ?>" data-total-bundle-price = "0"><div class='cpb-col-xl-12 cpb-col-lg-12 cpb-col-md-12 cpb-col-sm-12'><?php do_action("wdm_product_price_html", $product); ?></div></div>
        </div>
        <?php }
if ($enabledAddBox == 'on' && ($dynamicPrice == 'no' || ($basePrice == 'yes' && $dynamicPrice == 'yes'))) { ?>
        <div class="cpb-row cpb-clear cpb-quantity-box--assets cpb-align-items-center cpb-justify-content-center">
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price-label'><?php echo $addBoxTotal; ?></div>
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price wdm-box-price' data-reg-price = "<?php echo $price; ?>" data-total-price = "<?php echo $price; ?>"><div class='cpb-col-xl-12 cpb-col-lg-12 cpb-col-md-12 cpb-col-sm-12'><?php do_action("wdm_product_base_price_html", $product); ?></div></div>
        </div>
<?php }
if ($product->is_product_cpb_subscription()) { ?>
        <div class="cpb-row cpb-clear cpb-quantity-box--assets cpb-align-items-center cpb-justify-content-center">
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price-label'><?php echo $signupLabel; ?></div>
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price wdm-signup-price' data-signup-fee = "<?php echo $signUpFee; ?>"><div class='cpb-col-xl-12 cpb-col-lg-12 cpb-col-md-12 cpb-col-sm-12'><?php do_action("wdm_product_signup_fee_html", $product); ?></div></div>
        </div>
<?php }
?>
        <div class="cpb-row cpb-clear cpb-quantity-box--assets bundle-product--grand-total cpb-align-items-center cpb-justify-content-center">
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price-label'><?php echo $grandTotal; ?></div>
            <div class='cpb-col-xl-6 cpb-col-lg-6 cpb-col-md-6 cpb-col-sm-6 bundle-product--price wdm-grand-total wdm_bundle_price' data-total-price = "<?php echo $dynamicPrice ?>"><div class='cpb-col-xl-12 cpb-col-lg-12 cpb-col-md-12 cpb-col-sm-12'><?php do_action("wdm_product_grand_total", $product); ?></div></div>
        </div>

        <?php
            do_action('wdm_cpb_before_add_to_cart_button', $product);
            $button_text = apply_filters('single_add_to_cart_text', __('Add to cart', 'custom-product-boxes'), $product);
            ?>
            <button
                type='submit'
                class= "<?php echo $disable_class; ?>"
                <?php echo $disabled; ?>
            >
                <?php
                    echo $button_text;
                ?>
            </button>
        </div>

        <input
            type = "hidden"
            name = "add-to-cart"
            value = "<?php echo $product_id; ?>"
        />
        <input
            type = "hidden"
            name = "prefill_os_m"
            value = "<?php echo $disabled; ?>"
        />
    </div>
</div>
