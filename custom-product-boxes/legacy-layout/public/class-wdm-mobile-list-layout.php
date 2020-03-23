<?php
/**
* This class is responsible to display the products for mobile layout.
*/

class WdmCPBMobileListLayout extends wdm_abstract_product_display {
	/**
	 * The single instance of the class.
	 *
	 * @var WdmProductBundleTypeDisplay object.
	 *
	 * @since 5.0.0
	 */
	protected static $instance = null;
	/**
	* Action for enqueuing scripts and styles for the mobile layout of the CPB.
	* Action to display the products on mobile layout.
	*/
	public function __construct() {
		global $post;

		$product = wc_get_product( $post->ID );

		if ( ! is_singular( 'product' ) ) {
			return;
		}

		if ( ! $product->is_type( 'wdm_bundle_product' ) ) {
			return;
		}

		parent::__construct();
		add_action( 'wdm_cpb_mobile_enqueue_styles', array( $this, 'enqueueStyles' ) );
		add_action( 'wdm_cpb_mobile_main_product_info', array( $this, 'displayMainProductInfo' ) );
		add_action( 'wdm_cpb_mobile_product_layout', array( $this, 'displayAddToCartForm' ), 10, 1 );
		add_action( 'wdm_mobile_add_on_product_image', array( $this, 'displayAddOnProductImage' ), 10, 2 );
		add_action( 'wdm_mobile_add_on_product_title', array( $this, 'displayAddOnProductTitle' ), 10, 2 );
		add_action( 'wdm_mobile_add_on_product_quantity', array( $this, 'displayAddOnProductQuantity' ), 10, 2 );
		add_action( 'wdm_mobile_cpb_add_to_cart_button', array( $this, 'displayCpbProductAddToCart' ), 10, 1 );
		add_action( 'wdm_mobile_product_price_html', array( $this, 'cpbMobileTemplateSinglePrice' ), 10, 1 );
		add_action( 'wdm_cpb_mobile_enqueue_scripts', array( $this, 'enqueueScripts' ) );
	}

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	* Returns the path for selected template.
	* @param string $templateType type for selected template.
	* @return string $templateType path for selected template.
	*/
	public function __get( $templateType ) {
		$this->$templateType = 'product-layouts/mobile-layouts/list/' . parent::__get( $templateType );
		return $this->$templateType;
	}

	/**
	* Enqueue styles for mobile layouts of CPB product display.
	* Sets the layout breakpoint screen resolution for the mobile layout.
	*/
	public function enqueueStyles() {
		wp_enqueue_style( 'wdm-cpb-mobile-list-layout-css', CPB()->plugin_url() . '/legacy-layout/assets/css/mobile-templates/list-layout.css', array(), CPB_VERSION, self::setMobileLayoutBreakpoint() );
		wp_enqueue_style( 'wdm-cpb-snackbar-css', CPB()->plugin_url() . '/legacy-layout/assets/css/wdm-snackbar.css', array(), CPB_VERSION );
	}

	/**
	* Enqueue scripts for mobile layout.
	* Prepare data for localization passed to mobile list layout js.
	*/
	public function enqueueScripts() {
		wp_enqueue_script( 'wdm-cpb-mobile-list-layout-js', CPB()->plugin_url() . '/legacy-layout/assets/js/mobile-templates/list-layout.js', array( 'jquery' ), CPB_VERSION );
		wp_enqueue_script( 'wdm-cpb-snackbar-js', CPB()->plugin_url() . '/legacy-layout/assets/js/snackbar/snackbar.js', array( 'jquery' ), CPB_VERSION );
		wp_localize_script(
			'wdm-cpb-mobile-list-layout-js',
			'mobileListLayoutParams',
			array(
				'enableProductsSwap'    => $this->enableSwapping() === true ? 1 : 0,
				'giftboxFullMsg'        => __( 'Gift Box is full.', 'custom-product-boxes' ),
				'canNotAddProduct'      => __( 'Another %s can not be added to the box', 'custom-product-boxes' ),
				'productsAddedText'     => __( 'Products in Box.', 'custom-product-boxes' ),
				'totalProductPriceText' => __( 'Total Product Price', 'custom-product-boxes' ),
			)
		);
		//wp_localize_script('wdm-add-to-cart-bundle', 'wdm_bundle_params', $params);
	}
}
