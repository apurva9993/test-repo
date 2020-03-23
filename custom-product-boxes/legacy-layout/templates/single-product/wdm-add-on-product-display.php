<?php
/**
 * CPB Add-on Product
 * @version 3.3.0
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

global $woocommerce, $post, $addon_product;
$product = wc_get_product( $post->ID );
$wdm_manage_stock = get_option('woocommerce_manage_stock');
$hide_stock = get_setting( 'cpb_hide_stock' );
$availability_html = wc_get_stock_html( $addon_product );

//gets the option key for product box for the selected layout
//get the class for the layout from the option table.
$productFieldClass = get_product_field();
$classes = apply_filters(
    'wdm_cpb_addon_product_div_classes',
    array(
        'product',
        'bundled_product',
        'bundled_product_summary',
        $productFieldClass,
    ),
    $post
);

// Not allowing this through filter becasuse developers should not change this class name. This class is
// necessary to identify exact product code needs to work with.
$classes[] =  "desktop_bundled_product_{$addon_id}";

$classes = implode(" ", $classes);

$price = wc_get_price_to_display($addon_product);

$wdm_item_product = new stdClass();

// if (get_post_type($bundled_item_id) == 'product_variation') {
//     $wdm_item_product = new \WC_Product_Variation($bundled_item_id);
// } else {
//     $wdm_item_product = new \WC_Product($bundled_item_id);
// }

$wdmItemPriceHtml = $addon_product->get_price_html();

//Get the details for the add-on product
//Add the template for the single add-on products
//add all the class associated with the product based on product grid or the
//stock availability.
?>
<div
    class = "<?php echo $classes; ?>"
    data-product-id = "<?php echo $addon_id; ?>"
    data-product-price = "<?php echo $price; ?>"
>
    <?php
    //image template
    do_action( 'wdm_add_on_product_image', $addon_data, $addon_product ); ?>
   <div class="px-15"><!--WDM Frontend- starting wrapper with inner padding-->
    <?php

    //title template
    do_action( 'wdm_add_on_product_title', $addon_data, $addon_product );

    if ( 'yes' !== $hide_stock ) {
        echo $availability_html;
    }

    if ( $product->is_dynamic_price() ) {
        ?>
        <p class="wdm_price">
            <?php echo $wdmItemPriceHtml; ?>
        </p>
        <?php
    }

    ?>
    <div class='details'>
        <div
            class = "cart"
            data-bundled-item-id = "<?php echo $addon_id; ?>"
            data-product-id = "<?php echo $addon_id; ?>"
            data-bundle-id = "<?php echo $post->ID; ?>"
        >

            <?php
                do_action( 'wdm_add_on_product_quantity', $addon_data, $addon_id );
            ?>

        </div>
    </div>
   </div>
</div>
