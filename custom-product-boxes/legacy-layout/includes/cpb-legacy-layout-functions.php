<?php
/**
 * File to include helper functions for legacy layout.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Removes the deleted products data from the CPB Product
 * If the products are deleted from the Woocommerce products list and still
 * present in the CPB Product delete those products.
 * @param  array $product_field_types array of products bundled in CPB product
 * @param int $postId
 * @return array $product_field_types array of products bundled in CPB product
 * excluding the products which are deleted from DB.
 */
function unlinkDeletedProducts($addOnProducts, $postId)
{
    global $prefill_manager;
    static $allProducts = array();

    if (get_post_meta($postId, '_per_product_pricing_active', true) == 'no' && get_post_meta($postId, '_product_base_pricing_active', true) == 'no') {
        update_post_meta($postId, '_product_base_pricing_active', 'yes');
    }

    // if (get_post_meta($postId, '_product_base_pricing_active', true) == 'no' && get_post_meta($postId, '_per_product_pricing_active', true) == 'yes') {
    //     update_post_meta($postId, '_price', get_post_meta($postId, '_regular_price', true));
    // }

    if (empty($allProducts)) {
        global $wpdb;
        $postsTable = $wpdb->prefix . 'posts';
        $allProducts = $wpdb->get_col("SELECT ID FROM $postsTable WHERE post_type IN ('product', 'product_variation')");
    }

    $cpb_keys = getBundledIds($addOnProducts);
    $prefillData = $prefill_manager->get_prefilled_products($postId);

    if ($cpb_keys && $allProducts) {
        $deletedProducts = array_diff($cpb_keys, $allProducts);
        if ($deletedProducts) {
            foreach ($deletedProducts as $deletedKey) {
                $addOnProducts = unsetDeletedProduct($addOnProducts, $deletedKey);
                    unsetDeletedPrefillProducts($prefillData, $deletedKey, $postId);
            }
        }

        $addOnProducts = getModifiedTextAddOnProducts($addOnProducts);

        update_post_meta($postId, '_bundle_data', $addOnProducts);
    }

    return $addOnProducts;
}

function unsetDeletedPrefillProducts($prefillData, $deletedKey, $cpbId)
{
    global $prefillManager, $wpdb;
    if (empty($prefillData) || !is_array($prefillData) || empty($deletedKey)) {
        return $prefillData;
    }

    foreach ($prefillData as $key => $value) {
        unset($key);
        $product_id = isset($value['product_id']) ? $value['product_id'] : "";
        if ($product_id == $deletedKey) {
            $wpdb->delete(
                $prefillManager->prefillProductTable,
                array(
                    'cpb_product_id'        => $cpbId,
                    'unique_prod_id'        => $value['unique_prod_id'],
                ),
                array(
                    '%d',
                    '%s',
                )
            );
        }
    }
}

function isProductCPBSubscription( $product ) {
    $product = CPB()->get_cpb_product( $product->get_id() );

    if ( ! $product->is_type( 'wdm_bundle_product' ) ) {
        return false;
    }

    return $product->is_product_cpb_subscription();
}

function getDynamicName($productId, $item_data, $productSku)
{
    $product = wc_get_product($productId);
    $productTitle = get_the_title($productId);

    if (getProductType($product) == "variation") {
        $variationProduct = new \WC_Product_Variation($productId);
        $variationAttributes = $variationProduct->get_variation_attributes();
        if (checkAnyAnyVariation($variationAttributes)) {
            $variation = $item_data['variation_string'];
            $skuString = isset($productSku) && !empty($productSku) ? $productSku : "#".$productId;
            $productTitle .= "&#8211;". $variation . "(". $skuString . ")";
        } else {
            $skuString = isset($productSku) && !empty($productSku) ? $productSku : "#".$productId;
            $productTitle .= "(". $skuString . ")";
        }
    } else {
        $skuString = isset($productSku) && !empty($productSku) ? $productSku : "#".$productId;
        $productTitle .= "(". $skuString . ")";
    }

    return $productTitle;
}

function getModifiedTextAddOnProducts($addOnProducts)
{
    foreach ($addOnProducts as $key => $value) {
        $productSku = get_post_meta($value['product_id'], '_sku', true);
        $productId = isset($value['variation_id']) ? $value['variation_id'] : $value['product_id'];
        $value['text'] = getDynamicName($productId, $value, $productSku);
        $addOnProducts[$key] = $value;
        $product = wc_get_product($productId);
        $addOnProducts[$key]['price'] = wc_get_price_to_display($product);

        if (getProductType($product) == "variation" || getProductType($product) == "simple") {
            $addOnProducts[$key]['display_text'] = getDisplayName($productId, $value, $product);
        }
        if (getProductType($product) == "variable") {
            $addOnProducts = getTextForVariableAddonProducts($value, $addOnProducts, $key);
        }
    }

    return $addOnProducts;
}
function getDisplayName($productId, $item_data, $product)
{
    $productTitle = get_the_title($productId);

    if (getProductType($product) == "variation") {
        $variationProduct = new \WC_Product_Variation($productId);
        $variationAttributes = $variationProduct->get_variation_attributes();
        if (checkAnyAnyVariation($variationAttributes)) {
            $variation = $item_data['variation_string'];
            $productTitle .= "&#8211;". $variation;
        }
    }

    return $productTitle;
}

function checkAnyAnyVariation($attributes)
{
    foreach ($attributes as $key => $value) {
        unset($key);
        if (empty($value)) {
            return true;
        }
    }
}


function getProductType($product, $product_id = null)
{
    if ($product_id != null) {
        $product = wc_get_product($product_id);
    }

    if (!$product) {
        return false;
    }

    if (version_compare(WC_VERSION, '3.0', '<')) {
        return $product->product_type;
    } else {
        return $product->get_type();
    }
}

function getTextForVariableAddonProducts($value, $addOnProducts, $key)
{
    foreach ($value['childrens'] as $childKey => $child) {
        $childProductId = isset($child['variation_id']) ? $child['variation_id'] : $child['product_id'];
        $childProduct = wc_get_product($childProductId);

        $addOnProducts[$key]['childrens'][$childKey]['display_text'] = getDisplayName($childProductId, $child, $childProduct);
    }

    return $addOnProducts;
}

function getBundledIds($bundleData)
{
    $bundleIds = array();
    foreach ($bundleData as $key => $value) {
        unset($key);
        array_push($bundleIds, isset($value['variation_id']) ? $value['variation_id'] : $value['product_id']);
    }

    return $bundleIds;
}

function unsetDeletedProduct($addOnProducts, $deletedKey)
{
    if (empty($addOnProducts) || !is_array($addOnProducts)) {
        return $addOnProducts;
    }

    foreach ($addOnProducts as $key => $value) {
        $product_id = isset($value['variation_id']) ? $value['variation_id'] : $value['product_id'];
        if ($product_id == $deletedKey) {
            unset($addOnProducts[$key]);
        }
    }

    return $addOnProducts;
}

/**
* Returns the column field Option key name based on the desktop layout selected.
* @return string Option-key for the column field of the desktop layout selected.
*/
function get_column_field() {
    $default_classes = array(
        'vertical' => 'cpb-box-col-2',
        'vertical-right' => 'cpb-box-col-2',
        'horizontal' => 'cpb-box-row-4',
    );
    $layout = cpb_get_layout_path();
    $selected_layout = basename( $layout );
    $gridField = array_merge(
        apply_filters( 'wdm_columns_gift_layout_index', array() ),
        array(
            'vertical' => 'cpb_box_column_size',
            'vertical-right' => 'cpb_box_column_size',
            'horizontal' => 'cpb_box_row_size',
        )
    );

    return get_option( apply_filters( 'wdm_columns_gift_layout', $gridField[ $selected_layout ] ), $default_classes[ $selected_layout ] );
}

/**
* Returns the column field Option key name based on the desktop layout selected.
* @return string Option-key for the column field of the desktop layout selected.
*/
function get_product_field() {
    $default_classes = array(
        'vertical' => 'cpb-product-col-2',
        'vertical-right' => 'cpb-product-col-2',
        'horizontal' => 'cpb-product-row-4',
    );
    $layout = cpb_get_layout_path();
    $selected_layout = basename( $layout );
    $grid_field = array_merge(
        apply_filters( 'wdm_columns_product_layout_index', array() ),
        array(
            'vertical' => 'cpb_product_column_size',
            'vertical-right' => 'cpb_product_column_size',
            'horizontal' => 'cpb_product_row_size',
        )
    );
    return get_option( apply_filters( 'wdm_columns_product_layout', $grid_field[ $selected_layout ] ), $default_classes[ $selected_layout ] );
}