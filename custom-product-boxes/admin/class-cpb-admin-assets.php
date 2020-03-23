<?php
/**
 * Load assets
 *
 * @author      WisdmLabs
 * @package     CPB/Admin
 * @version     4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


if ( ! class_exists( 'CPB_Admin_Assets' ) ) :

	/**
	 * CPB_Admin_Assets Class is responsible for enqueuing scripts and styles for
	 * CPB admin side.
	 */
	class CPB_Admin_Assets {
		/**
		 * Adds action for enqueuing scripts and styles for CPB Admin side.
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'cpb_load_admin_styles' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'cpb_load_admin_scripts' ), 9 );
		}

		/**
		 * Enqueue the required styles for admin side of CPB.
		 */
		public function cpb_load_admin_styles() {
			$screen       = get_current_screen();
			$screen_id    = $screen ? $screen->id : '';
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			if ( ! empty( $_GET['tab'] ) && 'other_extensions' == $_GET['tab'] ) {
				wp_register_style( 'cpb_promotion', CPB()->plugin_url() . '/promotion/assets/css/extension' . $suffix . '.css', array(), CPB_VERSION );

				// Enqueue admin styles.
				wp_enqueue_style( 'cpb_promotion' );
			}

			wp_register_style( 'cpb-jquery-ui', CPB()->plugin_url() . '/assets/common/css/jquery-ui' . $suffix . '.css', array(), CPB_VERSION );
			wp_register_style( 'cpb_select2', CPB()->plugin_url() . '/assets/admin/css/cpb-select2' . $suffix . '.css', array(), CPB_VERSION );
			wp_register_style( 'cpb_edit_css', CPB()->plugin_url() . '/assets/admin/css/cpb-meta-box-edit-page' . $suffix . '.css', array(), CPB_VERSION );
			wp_register_style( 'cpb_color_css', CPB()->plugin_url() . '/assets/admin/css/alpha-color-picker' . $suffix . '.css', array(), CPB_VERSION );

			if ( in_array( $screen_id, array( 'product', 'edit-product' ) ) ) {
				wp_enqueue_style( 'cpb-jquery-ui' );
				wp_enqueue_style( 'cpb_select2' );
				wp_enqueue_style( 'cpb_edit_css' );
				wp_enqueue_style( 'cpb_color_css' );
			}
		}

		/**
		 * Gets the current screen if it is edit/create product.
		 * Enqueue the scripts required for the admin side of CPB
		 */
		public function cpb_load_admin_scripts() {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'cpb-jquery-ui', CPB()->plugin_url() . '/assets/common/js/jquery-ui' . $suffix . '.js', array( 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core' ), CPB_VERSION );
			wp_register_script( 'cpb-functions', CPB()->plugin_url() . '/assets/common/js/cpb-functions' . $suffix . '.js', array(), CPB_VERSION );
			wp_register_script( 'cpb-admin-product-page', CPB()->plugin_url() . '/assets/admin/js/cpb-meta-box-edit-page' . $suffix . '.js', array( 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core' ), CPB_VERSION );
			wp_register_script( 'cpb-select2-js', CPB()->plugin_url() . '/assets/admin/js/cpb-select2' . $suffix . '.js', array(), CPB_VERSION );
			wp_register_script( 'cpb-alpha-color-picker-js', CPB()->plugin_url() . '/assets/admin/js/alpha-color-picker' . $suffix . '.js', array( 'jquery', 'wp-color-picker' ), CPB_VERSION );
			wp_register_script( 'cpb-extended-select', CPB()->plugin_url() . '/assets/admin/js/enhanced-select-extended' . $suffix . '.js', array( 'jquery', 'wp-color-picker' ), CPB_VERSION );
			wp_register_script( 'cpb-prefilled-js', CPB()->plugin_url() . '/assets/admin/js/cpb-prefilled-products' . $suffix . '.js', array( 'jquery', 'wp-color-picker' ), CPB_VERSION );
			// Get admin screen ID.
			$screen    = get_current_screen();
			$screen_id = $screen ? $screen->id : '';

			/*
			 * Enqueue scripts.
			 */
			if ( 'product' === $screen_id ) {
				wp_enqueue_script( 'cpb-jquery-ui' );
				wp_enqueue_script( 'cpb-functions' );
				wp_enqueue_script( 'cpb-admin-product-page' );
				wp_enqueue_script( 'cpb-alpha-color-picker-js' );
				wp_enqueue_script( 'cpb-select2-js' );
				wp_enqueue_script( 'cpb-extended-select' );
				wp_enqueue_script( 'cpb-prefilled-js' );
			}

			wp_localize_script(
				'cpb-extended-select',
				'enhanced_select_params',
				array(
					'i18n_matches_1' => _x( 'One result is available, press enter to select it.', 'enhanced select', 'woocommerce' ),
					'i18n_matches_n' => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'woocommerce' ),
					'i18n_no_matches' => _x( 'No matches found', 'enhanced select', 'woocommerce' ),
					'i18n_ajax_error' => _x( 'Loading failed', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_1' => _x( 'Please enter 1 or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_short_n' => _x( 'Please enter %qty% or more characters', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_1' => _x( 'Please delete 1 character', 'enhanced select', 'woocommerce' ),
					'i18n_input_too_long_n' => _x( 'Please delete %qty% characters', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'woocommerce' ),
					'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'woocommerce' ),
					'i18n_load_more' => _x( 'Loading more results&hellip;', 'enhanced select', 'woocommerce' ),
					'i18n_searching' => _x( 'Searching&hellip;', 'enhanced select', 'woocommerce' ),
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'search_products_nonce' => wp_create_nonce( 'search-products' ),
				)
			);

			// Localization data for pre-filled products section of CPB.
			wp_localize_script(
				'cpb-prefilled-js',
				'cpb_prefilled_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'add_image'       => CPB()->plugin_url() . '/assets/admin/images/plus-icon.png',
					'remove_image'    => CPB()->plugin_url() . '/assets/admin/images/minus-icon.png',
					'total_prefill_qty_text' => __( 'Total quantity of products selected for pre filled boxes should be lesser than or equal to CPB box quantity', 'custom-product-boxes' ),
					'sld_ind_text'  => __( 'Quantity of product(s) sold individually cannot be more than 1. Please change the quantity of the following product(s): ', 'custom-product-boxes' ),
					'qty_greater_zero'  => __( 'Quantity of prefilled product should be greater than 1. Please change the quantity of products', 'custom-product-boxes' ),
				)
			);
			// Localization data for cpb-admin-product-page js.
			wp_localize_script(
				'cpb-admin-product-page',
				'cpb_admin_product_page_object',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'qty_greater_zero'  => __( 'Please enter the Box quantity greater or equal to 2', 'custom-product-boxes' ),
					'select_addon_product'  => __( 'Please select the addon product to create the box', 'custom-product-boxes' ),
				)
			);
		}
	}

endif;

return new CPB_Admin_Assets();
