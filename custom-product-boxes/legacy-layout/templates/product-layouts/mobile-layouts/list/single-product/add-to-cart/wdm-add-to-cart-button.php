<?php
/**
 * The template for displaying Add to cart button within loops
 *
 * This template can be overridden by copying it to yourtheme/custom-product-boxes/template/vertical/wdm-cpb-vertical-product-layout.php.
 *
 */
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

global $old_product;

if (!is_singular('product') || $product->get_type() !== 'wdm_bundle_product') {
    return;
}

$disable_class = 'single_add_to_cart_button bundle_add_to_cart_button button alt';
if ($disabled == "disabled") {
    $disable_class .= " os_pf_m";
}
//Get the CPB Product details about prices and quantities.
//Get the availability of products.
$product_id = $product->get_id();
// $bundle_price_data = $product->getBundlePriceData();
// $bundle_item_quantity = $old_product->getBundledItemQuantities();
$reg_price = wc_get_price_to_display($product, array('price' => $product->get_regular_price()));
// $quantities_array = esc_attr(json_encode($bundle_item_quantity));
// $bundle_price_data = esc_attr(json_encode($bundle_price_data));
$classes = "cart bundle_form bundle_form_{$product_id}";
$availability = $product->get_availability();
$stock_message = "";
if ($availability['availability']) {
    $stock_message = apply_filters('woocommerce_stock_html', '<p class="stock ' . $availability['class'] . '">' . $availability['availability'] . '</p>', $availability['availability']);
}
//Template to display the gift-box
//Price fields and box quantity field.
?>
<div
    class = "<?php echo $classes; ?>"
    data-bundle-price-data = ""
    data-bundle-id = "<?php echo $product_id; ?>"
>

<?php
// do_action('woocommerce_before_add_to_cart_button');
?>

    <div class="bundle_wrap">
        <div class="wdm_bundle_price">
        <?php
            do_action("wdm_mobile_product_price_html", $product);
        ?>
        </div>

        <?php
            echo $stock_message;
        ?>
        <div class="bundle_button">
        <?php
            //echo sprintf('<div class="wdm-cpb-product-quantity-label">%s</div>', __('Quantity: ', 'custom-product-boxes'));
            $quantity_html =  woocommerce_quantity_input(array('min_value' => 1), null, false);
            $domHTML = \SHD\str_get_html($quantity_html);

            $domHTML->find('input.qty')[0]->class .= " cpb_main_qty";
            $domHTML->find('input.qty')[0]->id = "cpb_main_qty_mobile";
            echo $domHTML;
                // if(!($theme->Name =='Avada' || $theme->parent_theme =='Avada'||$theme->Name =='Flatsome'|| $theme->parent_theme =='Flatsome'||$theme->Name =='Enfold'|| $theme->parent_theme =='Enfold')){


            do_action('wdm_cpb_before_add_to_cart_button', $product);

               // woocommerce_quantity_input(array('min_value' => 1));
            $button_text = apply_filters('single_add_to_cart_text', __('Add to cart', 'custom-product-boxes'), $product);
            ?>
            <!-- <div class="clear"></div> -->
            <div class='wdm-cpb-product-add-to-cart-button'>
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
        </div>
        <input
            type="hidden"
            name ="reg_price"
            id = "main_reg_price"
            value = "<?php echo $reg_price; ?>"
        >
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