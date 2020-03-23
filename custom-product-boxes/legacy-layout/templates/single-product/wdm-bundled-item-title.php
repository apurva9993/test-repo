<?php

/**
 * Bundled Item Title.
 * @version 4.2.0
 */

// Exit if accessed directly
//Template for single add-on product title display.
if (! defined('ABSPATH')) {
    exit;
}

if ($title === '') {
    return;
}

?>
 <p class="bundled_product_title product_title" style="margin-bottom: 0px" title="<?php echo strip_tags($alt); ?>">
    <?php
        echo $title;
    ?>
</p>
