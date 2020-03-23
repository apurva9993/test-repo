<?php
/**
 * Loads products display
 *
 * @author      WisdmLabs
 * @package     CPB/Public
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WdmProductBundleTypeDisplay as WdmProductBundleTypeDisplay;

if ( ! class_exists( 'CPB_Display_Product' ) ) :

	/**
	 * Display for the CPB Product type.
	 *
	 * @class    CPB_Display_Product
	 * @version  4.0.0
	 */
	class CPB_Display_Product {

		/**
		 * The single instance of the class.
		 *
		 * @var CPB_Display_Product
		 *
		 * @since 5.0.0
		 */
		protected static $instance = null;

		/**
		 * Main CPB_Display_Product instance. Ensures only one instance of CPB_Display_Product is loaded or can be loaded.
		 *
		 * @since  5.0.0
		 *
		 * @return CPB_Display_Product
		 */
		public static function instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Cloning is forbidden.
		 *
		 * @since 5.0.0
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'custom-product-boxes' ), '5.0.0' );
		}

		/**
		 * Unserializing instances of this class is forbidden.
		 *
		 * @since 5.0.0
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( 'Foul!', 'custom-product-boxes' ), '5.0.0' );
		}

		/**
		 * [__construct description]
		 */
		protected function __construct() {
			$this->cpb_display_legacy_layouts();
			$this->display_cpb_layouts();
		}

		/**
		 * Initiates Display of new CPB product layout.
		 * @return [type] [description]
		 */
		public function display_cpb_layouts() {
			add_action( 'cpb_before_template_starts', array( $this, 'cpb_remove_woocommerce_hooks' ) );

			add_action( 'woocommerce_before_single_product_summary', array( $this, 'load_cpb_product_layout_html' ) );

			add_action( 'cpb_before_add_to_cart_quantity', array( $this, 'cpb_add_addon_quantities_div' ) );
		}

		/**
		 * Initiates Display of Old/legacy CPB product layout.
		 * @return [type] [description]
		 */
		public function cpb_display_legacy_layouts() {
			add_action( 'woocommerce_before_single_product_summary', array( $this, 'load_legacy_layout' ) );
		}

		public function load_legacy_layout() {
			global $post;

			$cpb_product = CPB()->get_cpb_product( $post->ID );

			if ( ! is_legacy_layout( get_option( 'cpb_layout_type' ) ) ) {
				return;
			}

			if ( is_singular( 'product' ) && $cpb_product->get_type() == 'wdm_bundle_product' && get_post_status( $cpb_product->get_id() ) == 'publish' ) {
				include_once CPB_ABSPATH . 'legacy-layout/includes/cpb-legacy-layout-functions.php';
				// include_once CPB_ABSPATH . 'legacy-layout/includes/class-wdm-wc-product-item.php';
				// include_once CPB_ABSPATH . 'legacy-layout/includes/class-wc-product-wdm-product-bundle.php';
				include_once CPB_ABSPATH . 'legacy-layout/includes/simple_html_dom.php';
				include_once CPB_ABSPATH . 'legacy-layout/public/class-wdm-product-bundle-type-display.php';
				// WdmProductBundleTypeDisplay::load_product_layout_html();
				$legacy_display = WdmProductBundleTypeDisplay::get_instance();
				$legacy_display->load_product_layout_html( $cpb_product );
			}
		}

		/**
		 * Get the desktop layout (path of templates)for the CPB Product(Horizontal/
		 * vertical/vertical-right)
		 * Get the CPB Product gift box for the desktop selected layouts.
		 * Include the selected template for CPB Product display on desktop.
		 */
		public function load_cpb_product_layout_html() {
			global $post, $addon_list;

			$cpb_product = CPB()->get_cpb_product( $post->ID );

			if ( is_singular( 'product' ) && $cpb_product->get_type() == 'wdm_bundle_product' && get_post_status( $cpb_product->get_id() ) == 'publish' ) {

				$layout = cpb_get_layout_path();

				$addon_list = get_addon_including_variation_product( $cpb_product->get_addon_items_list() );
				if ( ! in_array( get_option( 'cpb_layout_type' ), apply_filters( 'cpb_load_from_layout_type_array', array( 'horizontal', 'vertical' ) ) ) ) {
					return;
				}
				// error_log( 'Selected layout :: ' . print_r( $addon_list, 1 ) );

				if ( ! empty( $addon_list ) ) {
					include_once $layout . '/index.php';
				}
			}
		}

		/**
		 * Remove the woocommerce hooks for the single product page for display of CPB
		 * Product.
		 */
		public function cpb_remove_woocommerce_hooks() {
			global $product;

			if ( is_singular( 'product' ) && $product->get_type() == 'wdm_bundle_product' ) {
				// removed actions and added later to add single product display at product image.
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
				// removed filter only for this product to remove product image.
				remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
				remove_action( 'woocommerce_before_single_product_summary', array( $product, 'woocommerce_show_product_sale_flash' ), 10 );
			}
		}

		/**
		 * Display hidden field for quantities.
		 *
		 * @return void
		 */
		public function cpb_add_addon_quantities_div() {
			global $post;

			$cpb_product = CPB()->get_cpb_product( $post->ID );

			// $cpb_product = CPB()->get_cpb_product( $post->ID );
			wc_get_template(
				'addon-product/cpb-addon-quantities.php',
				array(
					// 'post_id' => $product_id,
					'product' => $cpb_product,
				),
				'',
				plugin_dir_path( dirname( __FILE__ ) ) . 'templates/'
			);
		}
	}

endif;
