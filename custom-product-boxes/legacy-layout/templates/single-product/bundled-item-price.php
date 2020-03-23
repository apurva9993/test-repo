<?php

/**
 * Bundled Item Price.
 * @version 4.2.0
 */

// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}

?>
<p itemprop="price" class="price">10<?php echo $bundled_item->product->getPriceHtml(); ?></p>
