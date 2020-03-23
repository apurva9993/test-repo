<?php

/**
 * Bundles WC 2.1 Compatibility Functions
 * @version 4.0.0
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

/**
* Get the template for the selected file.
* @param string $file file path for the template.
* @param array $data data associated with the post.
* @param boolean $empty empty or not.
* @param string $path path for template directory.
*/
function wc_wdm_product_bundles_get_template($file, $data, $empty, $path)
{
    return wc_get_template($file, $data, $empty, $path);
}
