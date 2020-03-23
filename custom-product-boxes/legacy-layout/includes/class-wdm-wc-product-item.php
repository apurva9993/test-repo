<?php
// Exit if accessed directly
if (! defined('ABSPATH')) {
    exit;
}
/**
* This class is used to get the details of the add-on products.
*/
class WdmWcProductItem
{
    protected $enable_transients;
    /**
    * Gets the details of the bundled add-on product.
    * Initialize the product by getting the product prices and the stock status of * the add-on product.
    * @param int $bundled_item_id add-on product id.
    * @param object $parent CPB Product object.
    */
    public function __construct($bundled_item_data, $product_id, $parent)
    {
        $this->item_id    = $bundled_item_data['id'];
        $this->product_id = $product_id;
        $this->bundle_id  = $parent->get_id();

        $this->item_data   = $bundled_item_data;

        $this->purchasable = true;

        $bundled_product = wc_get_product($this->product_id);
        $postObject = get_post($this->product_id);
                // if not present, item cannot be purchased
        if (!empty($bundled_product)) {
            $this->product = $bundled_product;
            if ($this->purchasable) {
                // $this->title = get_the_title($this->product->get_id());
                $this->setTitle($this->item_data);
                $this->description = $postObject->post_excerpt;

                //Get the quantity for the add-on products if it is set for the products in stock.
                if (! empty($_POST['quantity_'.$bundled_item_data['id']])) {
                    $this->quantity = ( int )$_POST['quantity_'.$bundled_item_data['id']];
                } else {
                    $this->quantity = 0;
                }

                $custom_stk_per_item = get_post_meta($this->product_id, '_stock', true);
                $this->setMaxVal($custom_stk_per_item);

                $this->sold_individually    = false;
                $this->on_sale                = false;
                $this->nyp                    = false;
                // $this->enable_transients    = false;
                $this->enable_stock = get_post_meta($this->product_id, '_manage_stock', true);
                $this->stock_status = get_post_meta($this->product_id, '_stock_status', true);
                if ($parent->getEnableBndltransient()) {
                    $this->setEnableTransients(true);
                }
                $this->wdmInit();
            }
        }
    }

    /**
    * Returns the bundled item id.
    */
    public function getItemId()
    {
        return $this->product_id;
    }

    public function getUniqueId()
    {
        return $this->item_id;
    }

    public function setTitle($item_data)
    {
        $this->title = $item_data['text'];
    }

    /**
    * Set the maximum stock quantity for the add-on products if not set- and only
    * if wc settings for manage-stock
    * @param int/null $custom_stk_per_item stock quantity for that product.
    */
    public function setMaxVal($custom_stk_per_item)
    {
        $this->max_val = !empty($custom_stk_per_item) ? ( int )$custom_stk_per_item : 1;
    }

    /**
    * Returns the maximum value for the add-on product for the manage stock.
    * @return int max quantity in stock for the add-on product.
    */
    public function getMaxVal()
    {
        return $this->max_val;
    }

    public function setEnableTransients($value)
    {
        $this->enable_transients = isset($value) ? $value : false;
    }

    public function getEnableTransients()
    {
        return $this->enable_transients;
    }

    /**
    * Get the add-on product's regular,sales and the active prices.
    * Gets the stock status for the product and also the sold individually
    * status.
    * After getting the prices (maybe discounted prices) remove the filters for the * prices
    */
    public function wdmInit()
    {
        //global $woo_wdm_bundle;

        //$product_id        = $this->product_id;
        $bundled_product    = $this->product;

        // $this->addPriceFilters();
        // if ($bundled_product->product_type == 'simple') {
        if ($bundled_product->is_sold_individually()) {
            $this->sold_individually = true;
        }

        if (! $bundled_product->is_in_stock() || ! $bundled_product->has_enough_stock($this->quantity)) {
            $this->stock_status = 'out-of-stock';
        }

        if ($bundled_product->is_on_backorder() && $bundled_product->backorders_require_notification()) {
            $this->stock_status = 'available-on-backorder';
        }

        $regular_price    = $this->getRegularPrice($bundled_product->get_regular_price(), $bundled_product);
        $price            = $this->getPrice($bundled_product->get_price(), $bundled_product);

        if ($regular_price > $price) {
            $this->on_sale = true;
        }
        // }
        $this->removePriceFilters();
    }
    /**
     * Bundled item sale status.
     */
    public function isOnSale()
    {
        return $this->on_sale;
    }

    /**
     * Bundled item purchasable status.
     */
    public function isPurchasable()
    {
        return $this->purchasable;
    }

    /**
     * Bundled item out of stock status.
     */
    public function isOutOfStock()
    {
        if ($this->stock_status == 'out-of-stock') {
            return true;
        }

        return false;
    }

    /**
     * Bundled item backorder status.
     */
    public function isOnBackorder()
    {
        if ($this->stock_status == 'available-on-backorder') {
            return true;
        }

        return false;
    }

    /**
     * Bundled item sold individually status.
     */
    public function isSoldIndividually()
    {
        return false;
    }

    /**
     * Bundled item name-your-price status.
     */
    public function isNyp()
    {
        return $this->nyp;
    }

    /**
     * Check if the product has variables to adjust before adding to cart.
     */
    public function hasVariables()
    {
        global $woocommerce_bundles;

        if ($this->isNyp() || $woocommerce_bundles->helpers->has_required_addons($this->product_id) || $this->product->get_type() == 'variable') {
            return true;
        }

        return false;
    }

    /**
     * Check if the item is a subscription.
     */
    public function isSub()
    {
        return false;
    }

    /**
    * Adds the filters for getting regular, sales and active price of the add-on
    *  products.
    */
    public function addPriceFilters()
    {
        if (version_compare(WC_VERSION, '3.0.0', '<')) {
            add_filter('woocommerce_get_price', array( $this, 'getPrice' ), 15, 2);
            add_filter('woocommerce_get_regular_price', array( $this, 'getRegularPrice' ), 15, 2);
        } else {
            add_filter('woocommerce_product_get_price', array( $this, 'getPrice' ), 15, 2);
            add_filter('woocommerce_product_get_regular_price', array( $this, 'getRegularPrice' ), 15, 2);
        }

        add_filter('woocommerce_get_sale_price', array( $this, 'getSalePrice' ), 15, 2);
        add_filter('woocommerce_get_price_html', array( $this, 'getPriceHtml' ), 10, 2);
        add_filter('woocommerce_get_variation_price_html', array( $this, 'getPriceHtml' ), 10, 2);
    }

    /**
     * Removes discount and price filters.
     */
    public function removePriceFilters()
    {
        if (version_compare(WC_VERSION, '3.0.0', '<')) {
            remove_filter('woocommerce_get_price', array( $this, 'getPrice' ), 15, 2);
            remove_filter('woocommerce_get_regular_price', array( $this, 'getRegularPrice' ), 15, 2);
        } else {
            remove_filter('woocommerce_product_get_price', array( $this, 'getPrice' ), 15, 2);
            remove_filter('woocommerce_product_get_regular_price', array( $this, 'getRegularPrice' ), 15, 2);
        }

        remove_filter('woocommerce_get_sale_price', array( $this, 'getSalePrice' ), 15, 2);
        remove_filter('woocommerce_get_price_html', array( $this, 'getPriceHtml' ), 10, 2);
        remove_filter('woocommerce_get_variation_price_html', array( $this, 'getPriceHtml' ), 10, 2);
    }

    /**
     * Filter get_price() calls for bundled products to include discounts.
     * @param float $price price for add-on product.
     * @param object $product add-on product object.
     * @return float $price
     */
    public function getPrice($price, $product)
    {
        if ($product->get_id() !== $this->product->get_id()) {
            return $price;
        }

        return $price;
    }

    /**
     * Filter get_sale_price() calls for bundled products to include discounts.
     */
    public function getSalePrice($sale_price, $product)
    {
        if ($product->get_id() !== $this->product->get_id()) {
            return $sale_price;
        }
        //return empty($discount) ? $sale_price : $product->get_price();
        return $sale_price;
    }

    /**
     * Filter get_regular_price() calls for bundled products to include discounts.
     * If the regular price is not set get the active price of the bundled product.
     * @param float $regular_price regular price of bundled product.
     * @param object $product bundled product object.
     * @return double regular price/active price.
     */
    public function getRegularPrice($regular_price, $product)
    {
        if ($product->get_id() !== $this->product->get_id()) {
            return $regular_price;
        }

        $price = $product->get_price();

        return empty($regular_price) ? ( double ) $price : ( double ) $regular_price;
    }

    /**
     * Filter the html price string of bundled items to show the correct price with * discount and tax - needs to be hidden in per-product pricing mode.
     * @param string $price_html HTML String for the price.
     * @param object $product bundled item product object.
     * @return string html string for price of add-on products.
     */
    public function getPriceHtml($price_html, $product)
    {
        //global $woocommerce_bundles;

        if (! isset($product->is_filtered_price_html)) {
            if (! $this->per_product_pricing_active) {
                return '';
            }
        }
        /* translators: for quantity use %2$s */
        return apply_filters('woocommerce_bundled_item_price_html', $this->quantity > 1 ? sprintf(__('%1$s <span class="bundled_item_price_quantity">/ pc.</span>', 'custom-product-boxes'), $price_html, $this->quantity) : $price_html, $price_html, $this);
    }

    /**
     * Filter get_sign_up_fee() calls for bundled subs to include discounts.
     */
    public function getSignUpFee($sign_up_fee, $product)
    {
        if ($product->get_id() !== $this->product->get_id()) {
            return;
        }

        if (! $this->per_product_pricing_active) {
            return 0;
        }

        $discount = $this->sign_up_discount;

        return empty($discount) ? ( double ) $sign_up_fee : ( double ) $sign_up_fee * (100 - $discount) / 100;
    }

    /**
     * Returns the title of bundled item, apply filters on that.
     * @return string title of the bundled product.
     */
    public function getTitle()
    {
        return apply_filters('woocommerce_bundled_item_title', $this->title, $this);
    }

    /**
     * Returns the description of bundled item, apply filters on that.
     * @return string description of the bundled product.
     */
    public function getDescription()
    {
        return apply_filters('woocommerce_bundled_item_description', $this->description, $this);
    }

    public function setItemData($data)
    {
        if ($data) {
            $this->item_data = $data;
        }
    }
}
