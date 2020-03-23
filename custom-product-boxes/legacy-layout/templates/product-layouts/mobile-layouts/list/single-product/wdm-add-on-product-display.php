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
//Get the add-on product details to display
$product_id = $addon_product->get_id();
$wdm_manage_stock = get_option( 'woocommerce_manage_stock' );
$hide_stock = get_setting( 'cpb_hide_stock' );
$availability_html = wc_get_stock_html( $addon_product );

$price = wc_get_price_to_display( $addon_product );

// $wdm_item_product = new stdClass();

// if (get_post_type($bundled_item_id) == 'product_variation') {
//     $wdm_item_product = new \WC_Product_Variation($bundled_item_id);
// } else {
//     $wdm_item_product = new \WC_Product($bundled_item_id);
// }

$wdmItemPriceHtml = $addon_product->get_price_html();
$isSoldIndividually = ( true == $addon_product->is_sold_individually() ) ? 1 : 0;

$classes = apply_filters(
    'wdm_cpb_addon_product_div_classes',
    array(
        'mobile_list_layout',
        'bundled_product',
        'bundled_product_summary',
    ),
    $post
);

// if ( $canBeSwapped !== false ) {
//     $classes[] = 'unpurchasable-product';
// }
// Not allowing this through filter becasuse developers should not change this 
// class name. This class is
// necessary to identify exact product code needs to work with.
$classes[] =  "mobile_bundled_product_{$addon_id}";

$classes = implode(" ", $classes);
//Display the add-on products on basis of the stock status and settings ,
//whether they should be displayed or not.
?>

<div
    class = "<?php echo $classes; ?>"
    data-product-id = "<?php echo $addon_id; ?>"
    data-product-price = "<?php echo $price; ?>"
    data-sold-individually = "<?php echo $isSoldIndividually; ?>"
>
    <?php
    //image template
    do_action( 'wdm_mobile_add_on_product_image', $addon_data, $addon_product );
?>
    <div class='mobile-list-layout-addon-product-info'>
        <?php
        //title template
        do_action( 'wdm_mobile_add_on_product_title',  $addon_data, $addon_product );
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
    </div>

    <div class='mobile-list-layout-add-on-product-quantity'>
        <div
            class = "cart"
            data-original-id = "<?php echo $product_id; ?>"
            data-bundled-item-id = "<?php echo $addon_id; ?>"
            data-product-id = "<?php echo $addon_id; ?>"
            data-bundle-id = "<?php echo $post->ID; ?>"
        >

            <?php
                do_action( 'wdm_mobile_add_on_product_quantity',  $addon_data, $addon_id );
            ?>
        </div>
    </div>
    <div class="cpb-clear"></div>
</div>