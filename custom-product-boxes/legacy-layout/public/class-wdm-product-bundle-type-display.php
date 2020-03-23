<?php

class WdmProductBundleTypeDisplay {
	/**
	 * The single instance of the class.
	 *
	 * @var WdmProductBundleTypeDisplay object.
	 *
	 * @since 5.0.0
	 */
	protected static $instance = null;

	public $cpb_product = null;

	/**
	* Adds the actions for :
	* Action to load the CPB Product Layout for desktop layout.
	* Action to load the CPB Product Layout for mobile layout.
	* Action to change the classname for the cart-item and order-item.
	* Action to change the text of add-to-cart text for single add-on products.
	*/
	public function __construct() {
		// Change the tr class attributes when displaying bundled items in templates
		add_filter( 'woocommerce_cart_item_class', array( $this, 'wooBundlesTableItemClass' ), 10, 3 );
		add_filter( 'woocommerce_order_item_class', array( $this, 'wooBundlesTableItemClass' ), 10, 3 );

		// add_filter('wcs_view_subscription_actions', array($this,'subscription_actions'),10,2);
		require_once( 'class-wdm-abstract-product-display.php' );

		// Front end variation select box jquery for multiple products
		// add_filter( 'woocommerce_product_add_to_cart_text', array( $this, 'wooBundlesAddToCartText' ) );
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	* Get the desktop layout (path of templates)for the CPB Product(Horizontal/
	* vertical/vertical-right)
	* Get the CPB Product gift box for the desktop selected layouts.
	* Include the selected template for CPB Product display on desktop.
	*/
	public function load_product_layout_html( $product ) {
		$this->cpb_product = $product;

		if ( is_singular( 'product' ) && $product->get_type() == 'wdm_bundle_product' && get_post_status( $product->get_id() ) == 'publish' ) {
			$this->load_desktop_layout_html( $product );
			$this->load_mobile_layout_html( $product );
		}
	}

	public function load_desktop_layout_html( $product ) {	
		$selectedLayout = cpb_get_layout_path();

		$path = substr( $selectedLayout, strpos( $selectedLayout, 'product-layouts/desktop-layouts' ) );

		require_once( 'class-wdm-cpb-product-gift-box.php' );
		new wdm_cpb_product_gift_box();

		wc_get_template(
			$path . '/index.php',
			array(
				'product' => $product,
			),
			'custom-product-boxes',
			CPB_ABSPATH . 'templates/'
		);
	}

	/**
	* Get the mobile layout (path of templates)for the CPB Product display.
	* Get the CPB Product gift box for the mobile list layout.
	* Include the template for CPB Product display on mobile.
	*/
	public function load_mobile_layout_html( $product ) {
		require_once( 'class-wdm-mobile-list-layout.php' );
		WdmCPBMobileListLayout::get_instance();

		wc_get_template(
			'list/index.php',
			array(
				'product' => $product,
				'layoutType' => 'mobile',
				'layoutName' => 'mobile_list',
			),
			'custom-product-boxes',
			CPB_ABSPATH . 'legacy-layout/templates/product-layouts/mobile-layouts/'
		);
	}

	/**
	* Show something instead of an empty price (abandoned).
	*/
	public function wooBundlesEmptyPrice( $price, $product ) {
		if ( ( $product->get_type() == 'wdm_bundle_product' ) && ( get_post_meta( $product->get_id(), '_per_product_pricing_active', true ) == 'no' ) ) {
			return __( 'Price not set', 'custom-product-boxes' );
		}

		return $price;
	}

	/**
	 * Replaces add_to_cart button url with something more appropriate.
	 */
	public function wooBundlesLoopAddToCartUrl( $url ) {
		$product;

		if ( $product->get_type() == 'wdm_bundle_product' ) {
			return $product->add_to_cart_url();
		}

		return $url;
	}

	/**
	 * Adds product_type_simple class for Ajax add to cart when all items are
	 * simple.
	 */
	public function wooBundlesAddToCartClass( $class ) {
		if ( $this->cpb_product->get_type() == 'wdm_bundle_product' ) {
			if ( $product->hasVariables() ) {
				return '';
			} else {
				return $class . ' product_type_simple';
			}
		}

		return $class;
	}

	/**
	 * Replaces add_to_cart text with something more appropriate.
	 * This is done for single product add-on.
	 * @param string $text text for add-to-cart button.
	 * @return string $text text for add-to-cart button changed like read-more,etc.
	 */
	public function wooBundlesAddToCartText( $text ) {
		if ( is_product() ) {
			if ( $this->cpb_product->get_type() == 'wdm_bundle_product' ) {
		// 		return $product->add_to_cart_text();
			}
		}
		return $text;
	}

	/**
	 * Adds QuickView support
	 */
	public function wooBundlesLoopAddToCartLink( $link, $product ) {
		if ( $this->cpb_product->get_type() == 'wdm_bundle_product' ) {
			if ( $product->is_in_stock() && $product->allItemsInStock() && ! $product->hasVariables() ) {
				return str_replace( 'product_type_bundle', 'product_type_bundle product_type_simple', $link );
			} else {
				return str_replace( 'add_to_cart_button', '', $link );
			}
		}

		return $link;
	}

	/**
	 * Change the tr class of bundled items in all templates to allow their styling.
	 * @param string $classname class name for cart-item/order-item
	 * @param array/object cart-item or order-item.
	 * @return string $classname appended classname for CPB Product.
	 */
	public function wooBundlesTableItemClass( $classname, $values ) {
		if ( isset( $values['bundled_by'] ) ) {
			return $classname . ' bundled_table_item';
		} elseif ( isset( $values['stamp'] ) ) {
			return $classname . ' bundle_table_item';
		}

		return $classname;
	}
}
