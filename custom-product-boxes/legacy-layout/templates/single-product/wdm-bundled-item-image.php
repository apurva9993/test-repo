<?php

/**
 * Bundled Product Image
 * @version 3.3.0
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

global $woocommerce;
//Template to display the single product add-on image.
//If the product is sold individually add the appropriate class on the image div.
// get thumbnail title
$thumbnail_title = get_the_title(get_post_thumbnail_id($post_id));
if (isset($sld_ind)) {
    echo '<div class="images wdm-product-sold-individually">';
} else {
    echo '<div class="images">';
}
?>

<div class="cpb-plus-minus">
    <div class='cpb-card cpb-card-overlay cpb-add-product'></div>
    <div class='cpb-card cpb-card-overlay cpb-block-product'></div>
</div>


<div itemprop="image" class="zoom" rel="thumbnails" title="<?php echo $thumbnail_title; ?>">
<?php $image_url=get_the_post_thumbnail(
    $post_id,
    apply_filters('bundled_product_large_thumbnail_size', 'shop_thumbnail'),
    array(
    'title'    => get_the_title(get_post_thumbnail_id($post_id)),
    )
);

if (!isset($image_url) || empty($image_url) && getProductType($product) == "variation") {
    $parentId = wp_get_post_parent_id($post_id);
    $image_url = get_the_post_thumbnail(
        $parentId,
        apply_filters('bundled_product_small_thumbnail_size', 'shop_thumbnail'),
        array(
            'title'    => get_the_title(get_post_thumbnail_id($parentId)),
        )
    );

    if (!isset($image_url) || empty($image_url)) {
        $image_url = '<img width="180" height="180" src="'.wc_placeholder_img_src().'" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="poster_5_up" title="poster_5_up" sizes="(max-width: 180px) 100vw, 180px">';
    }
} elseif (!isset($image_url) || empty($image_url)) {
    $image_url='<img width="180" height="180" src="'.wc_placeholder_img_src().'" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="poster_5_up" title="poster_5_up" sizes="(max-width: 180px) 100vw, 180px">';
}
echo $image_url;
?>
</div>
</div>

