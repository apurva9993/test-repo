<?php
/**
 * Load assets
 *
 * @author      WisdmLabs
 * @package     CPB/Admin
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use CPB_Layouts as CPB_Layouts;
use WdmCPBMobileListLayout as WdmCPBMobileListLayout;

if ( ! class_exists( 'CPB_Public_Assets' ) ) :

	/**
	 * CPB_Public_Assets Class is responsible for enqueuing scripts and styles for
	 * CPB admin side.
	 */
	class CPB_Public_Assets {
		/**
		 * Layout type selected
		 */
		public $layout_type;

		/**
		 * Layouts array
		 */
		public $layout_array;

		/**
		 * Adds action for enqueuing scripts and styles for CPB Admin side.
		 */
		public function __construct() {
			// $this->cpb_init();
			add_action( 'wp', array( $this, 'cpb_init' ) );
			// add_action( 'wp_enqueue_scripts', array( $this, 'cpb_load_public_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'cpb_load_public_assets' ) );
		}

		public function cpb_load_public_assets() {
			if ( ! is_request( 'frontend' ) ) {
				return;
			}

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$this->cpb_load_public_styles( $suffix, $this->layout_type );
			$this->cpb_load_public_scripts( $suffix, $this->layout_type );
		}

		public function cpb_init() {
			$this->layout_type = get_option( 'cpb_layout_type' );
			$this->layout_array = array_keys( CPB_Layouts::get_layouts() );
			$this->add_layout_scripts();
		}

		/**
		 * Function to add enqueue script actions for different layouts.
		 */
		public function add_layout_scripts() {
			if ( ! is_singular( 'product' ) ) {
				return;
			}

			global $cpb_product, $post;
			$cpb_product = CPB()->get_cpb_product( $post->ID );

			if ( $cpb_product && ! $cpb_product->is_type( 'wdm_bundle_product' ) ) {
				return;
			}
			// Load scripts of selected layout type.
			if ( ! empty( $this->layout_array ) && in_array( $this->layout_type, $this->layout_array ) && is_request( 'frontend' ) ) {

				$style_function = 'cpb_layout_styles_' . $this->layout_type;
				$script_function = 'cpb_layout_scripts_' . $this->layout_type;

				if ( is_callable( array( $this, $style_function ) ) || is_callable( array( $this, $script_function ) ) ) {
					add_action( 'cpb_layout_styles_' . $this->layout_type, array( $this, 'cpb_layout_styles_' . $this->layout_type ), 10, 2 );
					add_action( 'cpb_layout_scripts_' . $this->layout_type, array( $this, 'cpb_layout_scripts_' . $this->layout_type ), 10, 2 );
				}
			}
		}

		/**
		 * Loading styles for frontend operations.
		 *
		 * @return void
		 */
		public static function cpb_load_public_styles( $suffix, $layout_type ) {
			do_action( 'cpb_layout_styles_' . $layout_type, $suffix, $layout_type );
		}

		/**
		 * Loading Java Script files for frontend operations.
		 *
		 * @return void
		 */
		public static function cpb_load_public_scripts( $suffix, $layout_type ) {
			wp_register_script( 'cpb-functions', CPB()->plugin_url() . '/assets/common/js/cpb-functions' . $suffix . '.js', array(), CPB_VERSION );

			wp_enqueue_script( 'cpb-functions' );

			do_action( 'cpb_layout_scripts_' . $layout_type, $suffix, $layout_type );
		}

		public function cpb_layout_scripts_vertical( $suffix, $layout_type ) {
			$this->cpb_vertical_horizontal_new_scripts( $suffix, $layout_type );
		}

		public function cpb_layout_scripts_horizontal( $suffix, $layout_type ) {
			$this->cpb_vertical_horizontal_new_scripts( $suffix, $layout_type );
		}

		public function cpb_layout_styles_vertical( $suffix, $layout_type ) {
			$this->cpb_vertical_horizontal_new_styles( $suffix, $layout_type );
		}

		public function cpb_layout_styles_horizontal( $suffix, $layout_type ) {
			$this->cpb_vertical_horizontal_new_styles( $suffix, $layout_type );
		}

		public function cpb_vertical_horizontal_new_styles( $suffix, $layout_type ) {
			unset( $layout_type );
			wp_register_style( 'cpb-public-css', CPB()->plugin_url() . '/assets/public/css/cpb-styles' . $suffix . '.css', false, CPB_VERSION );
			wp_register_style( 'cpb-slick-slider-css', CPB()->plugin_url() . '/assets/public/css/slick-slider.min.css', false, CPB_VERSION );
			// wp_register_style( 'cpb-grid-css', CPB()->plugin_url() . '/assets/public/css/bundles-grid.min.css', false, CPB_VERSION );

			wp_enqueue_style( 'cpb-public-css' );
			wp_enqueue_style( 'cpb-slick-slider-css' );
			// wp_enqueue_style( 'cpb-grid-css' );
		}

		public function cpb_vertical_horizontal_new_scripts( $suffix, $layout_type ) {
			unset( $layout_type );

			wp_register_script( 'cpb-frontend-js', CPB()->plugin_url() . '/assets/public/js/dist/cpb-script' . $suffix . '.js', array( 'jquery' ), CPB_VERSION );
			wp_register_script( 'cpb-slick-slider-js', CPB()->plugin_url() . '/assets/public/js/slick-slider.min.js', array( 'jquery' ), CPB_VERSION );

			wp_enqueue_script( 'cpb-frontend-js' );
			wp_enqueue_script( 'cpb-slick-slider-js' );

			$this->cpb_localize_scripts( 'cpb-frontend-js' );
		}

		public function cpb_layout_scripts_horizontal_legacy( $suffix, $layout_type ) {
			// error_log( 'Scripts' );
			$this->cpb_legacy_layout_scripts( $suffix, $layout_type );
		}

		public function cpb_layout_styles_horizontal_legacy( $suffix, $layout_type ) {
			$this->cpb_legacy_layout_style( $suffix, $layout_type );
		}

		public function cpb_layout_scripts_vertical_left_legacy( $suffix, $layout_type ) {
			// error_log( 'Scripts' );
			$this->cpb_legacy_layout_scripts( $suffix, $layout_type );
		}

		public function cpb_layout_styles_vertical_left_legacy( $suffix, $layout_type ) {
			$this->cpb_legacy_layout_style( $suffix, $layout_type );
		}

		public function cpb_layout_scripts_vertical_right_legacy( $suffix, $layout_type ) {
			// error_log( 'Scripts' );
			$this->cpb_legacy_layout_scripts( $suffix, $layout_type );
		}

		public function cpb_layout_styles_vertical_right_legacy( $suffix, $layout_type ) {
			$this->cpb_legacy_layout_style( $suffix, $layout_type );
		}

		/**
		 * Load legacy layout styles
		 * @param  string $suffix File extension whether min or .css
		 * @param  string $layout_type selected layout type
		 * @return void
		 * @SuppressWarnings("unused")
		 */
		public function cpb_legacy_layout_style( $suffix, $layout_type ) {
			include_legacy_display_class();
			$mobile_layout = WdmCPBMobileListLayout::get_instance();
			// error_log( 'Legacy layout' . $layout_type );
			wp_register_style( 'cpb-bundles-grid', CPB()->plugin_url() . '/legacy-layout/assets/css/bundles-grid.min.css', false, CPB_VERSION );
			wp_register_style( 'cpb-bundles-frontend', CPB()->plugin_url() . '/legacy-layout/assets/css/bundles-frontend' . $suffix . '.css', false, CPB_VERSION );
			wp_register_style( 'cpb-bundles-style', CPB()->plugin_url() . '/legacy-layout/assets/css/bundles-style' . $suffix . '.css', false, CPB_VERSION );
			wp_enqueue_style( 'wdm-cpb-mobile-list-layout-css', CPB()->plugin_url() . '/legacy-layout/assets/css/mobile-templates/list-layout.css', array(), CPB_VERSION, $mobile_layout->setMobileLayoutBreakpoint() );
			wp_enqueue_style( 'wdm-cpb-snackbar-css', CPB()->plugin_url() . '/legacy-layout/assets/css/wdm-snackbar.css', array(), CPB_VERSION );
			wp_enqueue_style( 'cpb-bundles-grid' );
			wp_enqueue_style( 'cpb-bundles-frontend' );
			wp_enqueue_style( 'cpb-bundles-style' );
		}

		/**
		 * Load legacy layout scripts
		 * @param  string $suffix File extension whether min or .js
		 * @param  string $layout_type selected layout type
		 * @return void
		 * @SuppressWarnings("unused")
		 */
		public function cpb_legacy_layout_scripts( $suffix, $layout_type ) {
			include_legacy_display_class();
			$mobile_layout = WdmCPBMobileListLayout::get_instance();
			wp_register_script( 'wdm-add-to-cart-bundle', CPB()->plugin_url() . '/legacy-layout/assets/js/add-to-cart-bundle' . $suffix . '.js', array( 'wc-add-to-cart-variation' ), CPB_VERSION );
			wp_register_script( 'wdm-add-to-cart-js', CPB()->plugin_url() . '/legacy-layout/assets/js/add-to-cart' . $suffix . '.js', array(), CPB_VERSION );
			wp_register_script( 'wdm-product-div-height-js', CPB()->plugin_url() . '/legacy-layout/assets/js/wdm-cpb-product-height' . $suffix . '.js', array( 'wc-add-to-cart-variation' ), CPB_VERSION );
			wp_enqueue_script( 'wdm-cpb-mobile-list-layout-js', CPB()->plugin_url() . '/legacy-layout/assets/js/mobile-templates/list-layout.js', array( 'jquery' ), CPB_VERSION );
			wp_enqueue_script( 'wdm-cpb-snackbar-js', CPB()->plugin_url() . '/legacy-layout/assets/js/snackbar.js', array( 'jquery' ), CPB_VERSION );
			wp_localize_script(
				'wdm-cpb-mobile-list-layout-js',
				'mobileListLayoutParams',
				array(
					'enableProductsSwap'    => $mobile_layout->enableSwapping() === true ? 1 : 0,
					'giftboxFullMsg'        => __( 'Gift Box is full.', 'custom-product-boxes' ),
					'canNotAddProduct'      => __( 'Another %s can not be added to the box', 'custom-product-boxes' ),
					'productsAddedText'     => __( 'Products in Box.', 'custom-product-boxes' ),
					'totalProductPriceText' => __( 'Total Product Price', 'custom-product-boxes' ),
				)
			);
			$this->cpb_localize_scripts( 'wdm-cpb-mobile-list-layout-js' );
			wp_enqueue_script( 'wdm-add-to-cart-bundle' );
			wp_enqueue_script( 'wdm-add-to-cart-js' );
			wp_enqueue_script( 'wdm-product-div-height-js' );
			$this->cpb_localize_scripts( 'wdm-add-to-cart-bundle' );
		}

		/**
		 * Localizes variable for sending data to JS.
		 * @param  string $script JS script handle loading in frontend.
		 * @return [type]         [description]
		 */
		public function cpb_localize_scripts( $script ) {
			if ( ! wp_script_is( $script ) ) {
				return;
			}

			$params = $this->get_legacy_localize_data();

			wp_localize_script( $script, 'wdm_bundle_params', $params );
		}

		public function get_legacy_localize_data() {
			global $woocommerce, $cpb_product;

			// $cpb_sale_price = $cpb_product->get_sale_price();
			// $cpb_price = $cpb_product->get_price();
			// $wdm_bundle_on_sale = false;
			// $product_price = $cpb_price;
			// if ( ! empty( $cpb_sale_price ) && $cpb_sale_price > 0 && $cpb_price > 0 ) {
			// 	$wdm_bundle_on_sale = true;
			// 	$product_price = $cpb_sale_price;
			// }

			$box_quantity = get_post_meta( get_the_ID(), 'cpb_box_capacity', true );

			return apply_filters(
				'cpb_legacy_localized_data',
				array(
					'i18n_free'                     => __( 'Free!', 'custom-product-boxes' ),
					'i18n_total'                    => __( 'Total', 'custom-product-boxes' ) . ': ',
					'i18n_partially_out_of_stock'   => __( 'Out of stock', 'custom-product-boxes' ),
					'i18n_partially_on_backorder'   => __( 'Available on backorder', 'custom-product-boxes' ),
					'currency_symbol'               => get_woocommerce_currency_symbol(),
					'currency_position'             => esc_attr( stripslashes( get_option( 'woocommerce_currency_pos' ) ) ),
					'currency_format_num_decimals'  => absint( get_option( 'woocommerce_price_num_decimals' ) ),
					'currency_format_decimal_sep'   => esc_attr( stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ) ),
					'currency_format_thousand_sep'  => esc_attr( stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) ),
					'currency_format_trim_zeros'    => false == apply_filters( 'woocommerce_price_trim_zeros', false ) ? 'no' : 'yes',
					'dynamic_pricing_enable'        => $cpb_product->is_dynamic_price(),
					'pricing_type'                  => $cpb_product->get_pricing_type(),
					'wdm_bundle_on_sale'            => $cpb_product->is_on_sale(),
					'product_thumb_size'            => get_option( 'shop_thumbnail_image_size' ),
					'box_quantity'                  => $box_quantity,
					'enableProductsSwap'            => $cpb_product->get_swap_prefilled(),
					'cpb_product_id'                => get_the_ID(),
					'woocommerce_version'           => $woocommerce->version,
					'product_price'                 => wc_get_price_to_display( $cpb_product ),
					'giftboxFullMsg'                => __( 'Gift Box is full.', 'custom-product-boxes' ),
					'isProductCPBSubscription'      => $cpb_product->is_product_cpb_subscription(),
					'allowPrefillProducts'          => $cpb_product->has_prefilled_products(),
					'vertical_empty_boxes_height' => get_option( 'cpb_vertical_empty_boxes_height' ),
				)
			);
		}
	}

endif;

return new CPB_Public_Assets();
