<?php

/**
 * CPB Add-on Product
 * @version 3.3.0
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
//Add the classes based on the stock status of the products.
//Add classes for the pre-filled products.
global $woocommerce, $post, $addon_product;

$allowBackOrder = '';
$disableQty = 'disabled';
$product_id = $addon_product->get_id();
$parent_id = $product_id;

$quantity_min = 0;
$qty_class = "quantity";
$quantity_max = get_post_meta($product_id, '_stock', true);
$managing_stock = get_post_meta($product_id, '_manage_stock', true);
if ( $addon_product->is_type( 'variation' ) && "no" == $managing_stock ) {
    $parent_id = $addon_product->get_parent_id();
    $quantity_max = get_post_meta($product_id, '_stock', true);
    $qty_class .= " parent_stock";
}

if (!$addon_product->is_sold_individually() || $quantity_min > 1) {
    if ($quantity_min == $quantity_max) {
        if ($quantity_min == 0) {
            $qty_class .= " qty_none buttons_added";
        } else {
            $qty_class .= "";
        }
    }
    if ($addon_product->backorders_allowed()) {
        $qty_class .= " buttons_added";
        $allowBackOrder = 'yes';
    } else {
        $qty_class .= " buttons_added";
        $allowBackOrder = 'no';
    }
}

if ($addon_product->is_in_stock() && $addon_product->backorders_allowed()) {
    $quantity_max = '';
    if ($addon_product->is_sold_individually()) {
        $quantity_max = 1;
    }
}
//Based on the stock status, add the quantity field for products.
?>
<div class='bundled_item_wrap'>
<?php
if ($addon_product->is_in_stock()) {
?>
    <div class="quantity_button">
        <div class = "<?php echo $qty_class; ?>" data-parent_id = "<?php echo $parent_id; ?>">
            <div class="mobile-list-layout-plus-button">
                <input type = "button" value = "+" class = "wdm-cpb-addon-qty-plus" />
            </div>
            <div class="mobile-list-layout-quantity-field">
                <input
                    type = "number"
                    step = "1"
                    min = "<?php echo $quantity_min; ?>"
                    max = "<?php echo $quantity_max; ?>"
                    data-product-mandatory = "<?php echo isset($prefilledProducts[$product_id]) ? $prefilledProducts[$product_id]['product_mandatory'] : 0 ?>"
                    data-allow-backorder = "<?php echo $allowBackOrder; ?>"
                    data-product-quantity = "<?php echo isset($prefilledProducts[$product_id]) ? $prefilledProducts[$product_id]['product_qty'] : 0; ?>"
                    data-product-prefill-quantity = "<?php echo isset($prefilledProducts[$product_id]) ? $prefilledProducts[$product_id]['product_qty'] : 0; ?>"
                    name = "mobile_quantity_<?php echo $addon_id; ?>"
                    value = "<?php echo isset($prefilledProducts[$product_id]) ? $prefilledProducts[$product_id]['product_qty'] : $quantity_min; ?>"
                    title = "Qty"
                    class = "input-number qty number"
                    size = "<?php echo $quantity_max; ?>"
                    <?php echo $disableQty; ?>
                    autocomplete = "off"
                />
            </div>
            <div class="mobile-list-layout-minus-button">
                  <input type="button" value="-" class="wdm-cpb-addon-qty-minus" />
            </div>
            <div class="clear"></div>
        </div>
    </div>
<?php
}
?>
</div>
