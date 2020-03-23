<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This class is responsible for :
 * Define new product type wdm_bundle_product by extending class WC_Product
 * Overridden methods of WC_Product are not camelcased.
 * Initialize the CPB Main Product.
 * Delete the add-on products from the CPB Product which are deleted from backend.
 * Get the pricing settings of the CPB Product.
 * Initialize CPB Products price.
 * Initialize the add-on products.
 */

class WCProductWdmBundleProduct {

	public $cpb_id;
	public $wdm_custom_bndl_data;
	// public static $wdm_bundled_items = array();
	public $wdm_bundled_items = array();
	public static $cpbProductId = array();

	public $price;

	public $min_bndl_prc_exc_tax;
	public $min_bndl_prc_inc_tax;

	public $per_product_pricing;
	public $per_product_shipping;

	public $allitemssoldseparate;
	public $all_items_in_stock;
	public $hasitems_on_bkorder;
	public $on_sale;

	public $bundle_price_data;

	public $contains_nyp;
	public $isNyp;

	public $contains_sub;
	public $sub_id;
	// item with variables
	public $has_item_with_vars;
	public $all_items_visible;

	protected $enable_bndltransient;
	public $microdata_display = false;
	public $noOfBundleItems = 0;
	public $needShippingCount = 0;

	/**
	 * Main WooCommerce Instance.
	 *
	 * Ensures only one instance of WooCommerce is loaded or can be loaded.
	 *
	 * @since 2.1
	 * @static
	 * @see WC()
	 * @return WooCommerce - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public function set_id( $cpb_id ) {
		$this->cpb_id = $cpb_id;
	}

	/**
	* Get the bundle CPB Product.
	* Get the bundled add-on products.
	* Delete the add-on products from the CPB Main product if they are deleted from
	* the database.
	* Get the pricing strategy for the CPB Product.
	* Get the price of the Main products.
	* Initialize the add-on products for the CPB Main product.
	* @param object $bundle CPB Main Product object.
	*/
	public function __construct( $bundle ) {
		$GLOBALS['new_product'] = new \WC_Product_Wdm_Bundle_Product( get_the_ID() );
		$this->set_id( $bundle );
		$this->wdm_custom_bundle_data = maybe_unserialize( get_post_meta( $bundle, '_bundle_data', true ) );

		if ( $this->wdm_custom_bundle_data && ! $this->alreadyCreated() ) {
			$this->wdm_custom_bundle_data = unlinkDeletedProducts( $this->wdm_custom_bundle_data, $this->get_id() );
		}

		$reg_price = get_post_meta( $bundle, 'wdm_reg_price_field', true );
		$sale_price = get_post_meta( $bundle, 'wdm_sale_price_field', true );

		if ( ! empty( $reg_price ) ) {
			update_post_meta( $bundle, '_regular_price', $reg_price );
		}

		if ( ! empty( $sale_price ) ) {
			update_post_meta( $bundle, '_sale_price', $sale_price );
		}

		// if (get_post_meta($this->get_id(), '_product_base_pricing_active', true) == 'no' && get_post_meta($this->get_id(), '_per_product_pricing_active', true) == 'yes') {
		//     update_post_meta($this->get_id(), '_price', get_post_meta($this->get_id(), '_regular_price', true));
		// }

		$this->contains_nyp = false;
		$this->isNyp = false;

        // global $new_cpb_product;
		$this->contains_sub = $GLOBALS['new_product']->is_product_cpb_subscription();

		$this->on_sale = false;

		$this->has_item_with_vars = false;
		$this->all_items_visible = true;

		$this->allitemssoldseparate = true;
		$this->all_items_in_stock = true;
		$this->hasitems_on_bkorder = false;
		$this->is_sold_individually = false;

		$this->per_product_pricing_active = false;
		if ( get_post_meta( $bundle, '_per_product_pricing_active', true ) == 'yes' ) {
			$this->per_product_pricing_active = true;
		}

		$this->product_base_pricing = false;
		if ( get_post_meta( $bundle, '_product_base_pricing_active', true ) == 'yes' ) {
			$this->product_base_pricing = true;
		}

		$this->per_product_shipping = false;
		if ( get_post_meta( $bundle, '_per_product_shipping_active', true ) == 'yes' ) {
			$this->per_product_shipping = true;
		}

		$product_price = get_post_meta( $bundle, '_price', true );

		// $this->regular_price = get_post_meta($this->get_id(), '_regular_price', true);

		$this->sale_price = get_post_meta( $bundle, '_sale_price', true );

		if ( isset( $this->sale_price ) && $this->sale_price > 0 ) {
			$this->on_sale = true;
		}

		$this->price = $product_price;
		if ( $this->per_product_pricing_active ) {
			if ( ! $this->product_base_pricing ) {
				$this->price = 0;
			}
		}

		// Checks if the Box product object is already initialized so that we can avoid multiple calls to initialize the add-on products
		if ( ! $this->alreadyCreated() ) {
			$this->initItems();
			self::$cpbProductId[ $bundle ] = $bundle;
		}
	}


	/*
	* Checks if the Box product object is already initialized
	* returns true if already initialized and false if initialized
	*/
	public function alreadyCreated() {
		if ( empty( self::$cpbProductId ) ) {
			return false;
		}
		if ( ! isset( self::$cpbProductId[ $this->get_id() ] ) ) {
			return false;
		}

		return true;
	}


	public function get_id() {
        return $this->cpb_id;
	}

	/*
	* Checks if the Box is of subscription type
	*/
	public function contains_sub() {
		return $this->contains_sub;
	}

	public function set_enable_bndltransient( $value ) {
		if ( get_post_meta( $this->get_id(), 'enable_bndltransient', true ) == 'yes' ) {
			$this->enable_bndltransient = true;
		} elseif ( $value ) {
			$this->enable_bndltransient = isset( $value ) ? $value : false;
		}
		// $this->enable_transients = isset($value) ? $value : false;
	}

	public function getEnableBndltransient() {
		return $this->enable_bndltransient;
	}

	/**
	* Return the product type i.e, bundled product.
	* @return string wdm_bundle_product.
	*/
	public function get_type() {
		return 'wdm_bundle_product';
	}

	/**
	* Returns boolean true if shipping is required and false if shipping not required.
	* This function returns if the CPB product requires shipping or not.
	*/
	public function needs_shipping() {
		$product_id = $this->get_id();
		$needs_shipping = get_post_meta( $product_id, 'wdm_need_shipping', true );
		// return apply_filters('wdm_cpb_need_shipping', $needs_shipping);
		return true;
	}

	/**
	 * Checks if a specific addon item is included in box.
	 *
	 * @param  int     $bundled_item_id
	 * @param  string  $context
	 * @return boolean
	 */
	public function hasAddon( $bundled_item_id ) {
		$hasAddon = false;
		$addonIds = $this->getAllAddonIds();

		if ( in_array( $bundled_item_id, $addonIds ) ) {
			$hasAddon = true;
		}

		return $hasAddon;
	}

	/**
	* Delete the add-on products from the CPB Product bundle if they don't exist in
	* the database.
	* @param array $wdm_custom_bundle_data CPB Product bundle.(add-on products data)
	* @param int $postId CPB Post Id.
	* @return array $wdm_custom_bundle_data updated CPB Product bundle.
	*/
	public function unlinkAllDeletedProducts( $wdm_custom_bundle_data, $postId ) {
		global $wpdb, $post;
		$postsTable = $wpdb->prefix . 'posts';
		$allProducts = $wpdb->get_col( "SELECT ID FROM $postsTable WHERE post_type IN ('product', 'product_variation')" );
		$cpb_keys = array_keys( $wdm_custom_bundle_data );

		if ( $cpb_keys && $allProducts ) {
			$deletedProducts = array_diff( $cpb_keys, $allProducts );
			if ( $deletedProducts ) {
				foreach ( $deletedProducts as $deletedKey ) {
					unset( $wdm_custom_bundle_data[ $deletedKey ] );
				}
			}

			update_post_meta( $postId, '_bundle_data', $wdm_custom_bundle_data );
		}
		return $wdm_custom_bundle_data;
	}

	/**
	* Get the price to be applied for the product.
	* Returns the price in html format.
	* @param float $price price for the product.
	* @return string HTML Price format.
	*/
	public function get_price_html( $price = '' ) {
		$price = $this->get_price();
		if ( $this->is_on_sale() && $price > 0 ) {
			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				$price = $this->get_price_html_from_to( $this->regular_price, $price );
			} else {
				$price = wc_format_sale_price( wc_get_price_to_display( $this, array( 'price' => $this->get_regular_price() ) ), wc_get_price_to_display( $this ) ) . $this->get_price_suffix();
			}
		} elseif ( $price == 0 ) {
			$price = wc_price( $price ) . $this->get_price_suffix();

			$price = apply_filters( 'woocommerce_price_html', $price, $this );
		} else {
			$price = parent::get_price_html();
		}

		return apply_filters( 'wdm_cpb_get_price_html', $price, $this );
	}

	/**
	 * In per-product pricing mode, get_regular_price() normally returns zero, since the container item does not have a price of its own.
	 */
	public function get_regular_price( $context = 'view' ) {
		if ( ! $this->product_base_pricing && ! is_admin() ) {
			return (float) 0;
		} else {
			return parent::get_regular_price( $context );
		}
	}

	/**
	 * Override on_sale status of product bundles. If a bundled item is on sale or has a discount applied, then the bundle appears as on sale.
	 */
	public function is_on_sale( $context = 'view' ) {
		$is_on_sale = false;
		if ( $this->per_product_pricing_active && ! empty( $this->wdm_bundled_items ) ) {
			if ( $this->on_sale ) {
				$is_on_sale = true;
			}
		} else {
			if ( $this->sale_price && $this->sale_price == $this->price ) {
				$is_on_sale = true;
			}
		}

		return apply_filters( 'woocommerce_bundle_is_on_sale', $is_on_sale, $this );
	}

	/**
	 * A bundle is sold individually if it is marked as an "individually-sold" product, or if all bundled items are sold individually.
	 */
	public function is_sold_individually() {
		return false;
	}

	/**
	 * A bundle appears "on backorder" if the container is on backorder, or if a bundled item is on backorder (and requires notification).
	 */
	// public function is_on_backorder()
	public function is_on_backorder( $qty_in_cart = 0 ) {
		return parent::is_on_backorder() || $this->hasitems_on_bkorder;
	}

	/**
	 * A bundle on backorder requires notification if the container is defined like
	 * this, or a bundled item is on backorder and requires notification.
	 */
	public function backorders_require_notification() {
		return parent::backorders_require_notification() || $this->hasitems_on_bkorder;
	}

	/**
	 * Availability of bundle based on bundle stock and stock of bundled items.
	 * @return array $backend_availability availability of the products.
	 */
	public function get_availability() {
		$backend_availability = parent::get_availability();

		if ( ! is_admin() ) {
			$availability = $class = '';

			if ( ! $this->allItemsInStock() ) {
				$availability = __( 'Out of stock', 'custom-product-boxes' );
				$class = 'out-of-stock';
			} elseif ( $this->hasitems_on_bkorder ) {
				$availability = __( 'Available on backorder', 'custom-product-boxes' );
				$class = 'available-on-backorder';
			}

			if ( $backend_availability['class'] == 'out-of-stock' || $backend_availability['class'] == 'available-on-backorder' ) {
				return $backend_availability;
			} elseif ( $class == 'out-of-stock' || $class == 'available-on-backorder' ) {
				return array(
					'availability' => $availability,
					'class' => $class,
				);
			}
		}

		return $backend_availability;
	}

	/**
	 * Get the add to url used mainly in loops.
	 */
	public function add_to_cart_url() {
		$url = get_permalink( $this->get_id() );

		return apply_filters( 'bundle_add_to_cart_url', $url, $this );
	}

	/**
	 * Get the add to cart button text
	 * This is for CPB product on shop/archive pages.
	 */
	public function add_to_cart_text() {
		$text = __( 'Read more', 'custom-product-boxes' );
		if ( $this->is_purchasable() && $this->is_in_stock() && $this->allItemsInStock() ) {
			$text = __( 'Read More', 'custom-product-boxes' );
		}
		return apply_filters( 'bundle_add_to_cart_text', $text, $this );
	}

	/**
	 * Returns false if the product cannot be bought.
	 * @return bool
	 */
	public function is_purchasable() {
		$postObject = get_post( $this->get_id() );

		$purchasable = true;

		// Products must exist of course
		if ( ! $this->exists() ) {
			$purchasable = false;

			// Other products types need a price to be set
		} elseif ( $this->get_price() === '' ) {
			$purchasable = true;

			// Check the product is published
		} elseif ( $postObject->post_status !== 'publish' && ! current_user_can( 'edit_post', $this->get_id() ) ) {
			$purchasable = false;
		}

		return apply_filters( 'woocommerce_is_purchasable', $purchasable, $this );
	}

	/**
	* Initialize the add-on products.
	* Or get the details of the add-on products.
	*/
	public function initItems() {
		if ( is_array( $this->wdm_custom_bundle_data ) ) {
			foreach ( $this->wdm_custom_bundle_data as $bundled_item_id => $bundled_item_data ) {
				// If an add-on is a variable product then fetches all its variation as single add-on
				if ( $bundled_item_data['product_type'] == 'variable' ) {
					foreach ( $bundled_item_data['childrens'] as $item_data ) {
						$this->initSingleItems( $item_data );
					}
					continue;
				}
				// initialize add-on of product type simple
				$this->initSingleItems( $bundled_item_data );
			}
		}

		/*
			Will decide if all the products in the bundle has no shipping then make the CPB product as no shipping.
			This will add a meta to the custom box
		*/
		if ( $this->needShippingCount > 0 && $this->needShippingCount <= $this->noOfBundleItems ) {
			update_post_meta( $this->get_id(), 'wdm_need_shipping', true );
		} else {
			update_post_meta( $this->get_id(), 'wdm_need_shipping', false );
		}
	}

	/*
	 * Function that Initializes single add-on item in the box product object.
	 * Gets the object of WdmWcProductItem class in the add-on item list.
	*/
	public function initSingleItems( $bundled_item_data ) {
		$product_id = isset( $bundled_item_data['variation_id'] ) ? $bundled_item_data['variation_id'] : $bundled_item_data['product_id'];
		$bundled_product = wc_get_product( $product_id );
		if ( $bundled_product ) {

			$bundled_item = new WdmWcProductItem( $bundled_item_data, $product_id, $this );
			$this->wdm_bundled_items[ $bundled_item_data['id'] ] = $bundled_item;
			if ( $bundled_product->needs_shipping() ) {
				$this->needShippingCount++;
			}
			$this->noOfBundleItems++;
		}
	}

	/**
	 * Gets quantities of all bundled items.
	 * @return array $bundle_item_quantity bundled item quantities.
	 */
	public function getBundledItemQuantities() {
		$bundle_item_quantity = array();

		if ( empty( $this->wdm_bundled_items ) ) {
			return $bundle_item_quantity;
		}

		foreach ( $this->wdm_bundled_items as $bundled_item ) {
			if ( ! empty( $bundled_item->quantity ) ) {
				$bundle_item_quantity[ $bundled_item->item_id ] = $bundled_item->quantity;
			}
		}

		return $bundle_item_quantity;
	}

	/**
	 * Gets all bundled items for that product.
	 * @return array bundled items.
	 */
	public function getWdmCustomBundledItems() {
		return $this->wdm_bundled_items;
	}

	/**
	 * Gets all bundled items id of the box.
	 * @return array bundled items.
	 */
	public function getAllAddonIds() {
		$addonIds = array();

		if ( ! $this->wdm_bundled_items ) {
			return $addonIds;
		}

		foreach ( $this->wdm_bundled_items as $key => $value ) {
			array_push( $addonIds, $value->getUniqueId() );
		}

		return $addonIds;
	}

	/**
	 * Gets all bundled items ordered by date.
	 * Sort the dates of creation of the products first in an array.
	 * Then according to the sorted array sort the array of the bundle products
	 * objects.
	 * @return array $new_wdm_bundle sorted array of bundled products objects.
	 */
	public function getWdmSortedCustomBundledItems() {
		$items = array();
		$new_wdm_bundle = array();
		if ( $this->wdm_bundled_items ) {
			// CREATING NEW ARRAY OF ITEM ID AND DATE FOR SORTING
			foreach ( $this->wdm_bundled_items as $key => $value ) {
				if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					$items[ $value->item_id ] = $value->product->post->post_date;
				} else {
					$product = wc_get_product( $value->getItemId() );
					$date = $product->get_date_created();
					$items[ $value->item_id ] = $date->date_i18n();
				}
			}

			// GETTING SORTED DATE ARRAY
			uasort( $items, array( $this, 'sortByDate' ) );

			// SORTING OBJECY ARRAY ACCORDING TO SORTED ARRAY
			$this->wdm_bundled_items = $this->sortObjectByDate( $this->wdm_bundled_items, $items );

			// FIXING INDEXES OF THE SORTED OBJECT ARRAY
			/*$new_wdm_bundle = array();
				$new_wdm_bundle[$value->getItemId()] = $value;
			}

			krsort($new_wdm_bundle);*/
			$new_wdm_bundle = array_reverse( $this->wdm_bundled_items, true );
			return $new_wdm_bundle;
		}
		return $new_wdm_bundle;
	}

	public function sortByDate( $a, $b ) {
		return strtotime( $a ) - strtotime( $b );
	}

	/**
	* Return the objects of products in bundle sorted as per the dates of creation.
	* @param array $a bundled items objects array.
	* @param array $b sorted array of the dates of creation of products in bundle.
	*/
	public function sortObjectByDate( $a, $b ) {
		$newA = array();
		foreach ( $b as $key => $value ) {
			if ( isset( $a[ $key ] ) ) {
				$newA[ $key ] = $a[ $key ];
			}
		}
		return $newA;
	}

	/**
	 * True if all bundled items are in stock in the desired quantities.
	 */
	public function allItemsInStock() {
		return $this->all_items_in_stock;
	}
}
