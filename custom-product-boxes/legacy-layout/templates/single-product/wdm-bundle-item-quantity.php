<?php

/**
 * CPB Add-on Product
 * @version 3.3.0
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
//When the product is in stock display the quantity field
//Include the class for the various conditions like sold individually, backorders.
global $woocommerce, $post, $addon_product;

$allowBackOrder = '';
$disableQty = '';
$product_id = $addon_product->get_id();
$parent_id = $product_id;
$quantity_min = 0;
$quantity_max = get_post_meta($product_id, '_stock', true);
$managing_stock = get_post_meta($product_id, '_manage_stock', true);
$qty_class = "quantity buttons_added";
if ( $addon_product->is_type( 'variation' ) && "no" == $managing_stock ) {
    $parent_id = $addon_product->get_parent_id();
    $quantity_max = get_post_meta($product_id, '_stock', true);
    $qty_class .= " parent_stock";
}
if ( ! $addon_product->is_sold_individually() || $quantity_min > 1) {
    if ($quantity_min == $quantity_max) {
        if ( 0 == $quantity_min ) {
            $qty_class .= " qty_none";
        } else {
            $qty_class .= "";
            $disableQty = "disabled";
        }
    }
    if ( $addon_product->backorders_allowed() ) {
        $qty_class .= " buttons_added";
        $allowBackOrder = 'yes';
    } else {
        $qty_class .= " buttons_added";
        $allowBackOrder = 'no';
    }
}
?>
<div class='bundled_item_wrap'>
<?php
if ( $addon_product->is_in_stock() ) {
?>
    <div class="quantity_button">
        <div class = "<?php echo $qty_class; ?>" data-parent_id = "<?php echo $parent_id; ?>">
            <input type="button" value="-" class="minus" />
            <input 
                type = "number"
                step = "1"
                min = "<?php echo $quantity_min; ?>"
                max = "<?php echo $quantity_max; ?>"
                data-allow-backorder = "<?php echo $allowBackOrder; ?>"
                name = "quantity_<?php echo $addon_id; ?>"
                value = "<?php echo $quantity_min; ?>"
                title = "Qty"
                class = "input-text qty text"
                size = "<?php echo $quantity_max; ?>"
                <?php echo $disableQty; ?>
                autocomplete = "off"
            />
            <input type = "button" value = "+" class = "plus" />
        </div>
    </div>
<?php
}
?>
</div>
