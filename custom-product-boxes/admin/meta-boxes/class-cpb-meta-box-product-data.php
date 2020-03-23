<?php
/**
 * CPB_Meta_Box_Product_Data class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/MetaBox
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WC_Product_Wdm_Bundle_Product as CPB_Product;

/**
 * Product meta-box data for the CPB Product type.
 *
 * @class    CPB_Meta_Box_Product_Data
 * @version  5.9.0
 */
class CPB_Meta_Box_Product_Data {

	/**
	 * Hook in.
	 */
	public static function init() {
		// Configuration Model.

		// Adds support for the Product type wdm_bundle_product (Custom Product Boxes).
		add_filter( 'product_type_selector', array( __CLASS__, 'cpb_add_product_type' ) );
		// Creates the CPB configuration tab on edit page.
		add_action( 'woocommerce_product_data_tabs', array( __CLASS__, 'cpb_product_data_tabs' ) );

		// Creates the panel for configuring custom box product options.
		add_action( 'woocommerce_product_data_panels', array( __CLASS__, 'cpb_product_data_panel' ) );

		// Add type-specific options.
		add_filter( 'product_type_options', array( __CLASS__, 'cpb_type_options' ) );

		// Configuration View.
		add_action( 'cpb_type_general_admin_config', array( __CLASS__, 'cpb_display_general_configurations' ), 10, 1 );
		add_action( 'cpb_addon_list_container', array( __CLASS__, 'cpb_addon_list_selector_container' ), 10, 2 );
		add_action( 'cpb_show_variation_selection', array( __CLASS__, 'cpb_variable_accordion' ), 10, 2 );

		// Configuration Controller.

		// Processes and saves type-specific data.
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'cpb_process_data' ) );

	}

	/**
	 * Add support for the 'wdm_bundle_product'(Custom Product Boxes) product type.
	 *
	 * @param  array $options Product types array.
	 * @return array
	 */
	public static function cpb_add_product_type( $options ) {

		$options['wdm_bundle_product'] = __( 'Custom Product Boxes', 'custom-product-boxes' );

		return $options;
	}

	/**
	 * Product bundle type-specific options.
	 *
	 * @param  array $product_type_options .
	 * @return array
	 */
	public static function cpb_type_options( $product_type_options ) {
		$product_type_options['downloadable']['wrapper_class'] .= ' show_if_wdm_bundle_product';
		$product_type_options['virtual']['wrapper_class']      .= ' show_if_wdm_bundle_product';

		if ( ! is_subscription_active() ) {
			return $product_type_options;
		}

		$product_type_options['cpb_subscription'] = array(
			'id'            => 'cpb_subscription',
			'wrapper_class' => 'show_if_wdm_bundle_product',
			'label'         => __( 'Enable subscription', 'custom-product-boxes' ),
			'description'   => __( 'Allows admin to enable subscription for this specific product.', 'custom-product-boxes' ),
			'default'       => esc_attr( get_post_meta( get_the_ID(), 'cpb_subscription', true ) ),
		);
		return $product_type_options;
	}


	/**
	 * Adds the Custom Box settings tab Product data section
	 *
	 * @param array $tabs Array of tabs to show.
	 *
	 * @return array
	 */
	public static function cpb_product_data_tabs( $tabs ) {

		global $post, $product_object, $cpb_product;

		/*
		 * Create a global bundle-type object to use for populating fields.
		 */

		$post_id = $post->ID;

		if ( empty( $product_object ) || false === $product_object->is_type( 'wdm_bundle_product' ) ) {
			$cpb_product = $post_id ? new CPB_Product( $post_id ) : new CPB_Product();
		} else {
			$cpb_product = $product_object;
		}

		$tabs['wdm_bundle_product'] = array(
			'label'    => __( 'Custom Box Settings', 'custom-product-boxes' ),
			'target'   => 'cpb_product_data',
			'class'    => array( 'show_if_wdm_bundle_product', 'cpb_product_options', 'cpb_product_tab' ),
			'priority' => 49,
		);

		// $tabs['general']['class'][] = "show_if_wdm_bundle_product";

		$tabs['inventory']['class'][] = 'show_if_wdm_bundle_product';

		return $tabs;
	}

	/**
	 * Displays the configuration options for the CPB product type
	 */
	public static function cpb_product_data_panel() {

		global $cpb_product;

		if ( $cpb_product->get_type() !== 'wdm_bundle_product' ) {
			return;
		}

		?><div id="cpb_product_data" class="panel woocommerce_options_panel">
			<?php cpb_hr_element( __( 'Custom Products Settings', 'custom-product-boxes' ) ); ?>
			<div class="cpb_general_options options_group show_if_wdm_bundle_product">
				<?php
				/**
				 * 'cpb_type_general_admin_config' action.
				 *
				 * @param  CPB_Product  $cpb_product
				 */
				do_action( 'cpb_type_general_admin_config', $cpb_product );
				?>
			</div>
			<div class="cpb_other_options options_group">
				<?php
				/**
				 * 'cpb_type_other_admin_config' action.
				 *
				 * @since  5.8.0
				 * @param  CPB_Product  $cpb_product
				 */
				do_action( 'cpb_type_other_admin_config', $cpb_product );
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Display CPB's general configuration settings
	 *
	 * @param  object $cpb_product CPB product object.
	 * @return void
	 */
	public static function cpb_display_general_configurations( $cpb_product ) {
		$addon_items_list = $cpb_product->get_addon_items_list( 'view' );
		woocommerce_wp_select(
			array(
				'id' => 'cpb_pricing_method',
				'label' => __( 'Pricing Type', 'custom-product-boxes' ),
				'desc_tip' => 'true',
				'description' => __( 'Select the pricing type.', 'custom-product-boxes' ),
				'options' => array(
					'cpb-fixed-price' => __( 'Fixed Pricing', 'custom-product-boxes' ),
					'cpb-dynamic-base' => __( 'Per Product Pricing with Base Price', 'custom-product-boxes' ),
					'cpb-dynamic-nobase' => __( 'Per Product Pricing without Base Price', 'custom-product-boxes' ),
				),
				'value' => $cpb_product->get_pricing_type( 'edit' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id' => 'cpb_box_capacity',
				'label' => __( 'Box Quantity', 'custom-product-boxes' ),
				'placeholder' => '',
				'desc_tip' => 'true',
				'description' => __( 'Set the number of items which can be added to the box.', 'custom-product-boxes' ),
				'type' => 'number',
				'custom_attributes' => array(
					'step' => 'any',
					'min' => '2',
				),
				'value' => $cpb_product->get_box_capacity( 'edit' ),
			)
		);

		cpb_hr_element( __( 'Layout Settings', 'custom-product-boxes' ) );

		$all_layouts = CPB_Layouts::get_available_layouts();

		woocommerce_wp_select(
			array(
				'id' => 'cpb_layout_selected',
				'label' => __( 'Select box layout', 'custom-product-boxes' ),
				'desc_tip' => 'true',
				'description' => __( 'Select the box layout. You can also create a custom layout by overriding our default template. To know more, please refer our user guide.', 'custom-product-boxes' ),
				'options' => $all_layouts,
				'value' => $cpb_product->get_layout(),
				// 'value' => $woo_wdm_bundle->getDesktopLayout($post->ID),
			)
		);

		cpb_hr_element( __( 'Other Settings', 'custom-product-boxes' ) );

		woocommerce_wp_checkbox(
			array(
				'id' => 'cpb_allow_partially_filled',
				'label' => __( 'Allow Partially-Filled Box', 'custom-product-boxes' ),
				'description' => __( 'Allow the purchase of box which has not been filled to its full capacity', 'custom-product-boxes' ),
				'value' => $cpb_product->get_partially_filled_box( 'edit' ),
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id' => 'cpb_order_by_date',
				'label' => __( 'Sort Products by Date', 'custom-product-boxes' ),
				'description' => __( 'Adds newly added product to the top', 'custom-product-boxes' ),
				'value' => $cpb_product->get_sort_by_date( 'edit' ),
			)
		);

		woocommerce_wp_checkbox(
			array(
				'id' => 'cpb_enable_message',
				'label' => __( 'Enable Gift Message', 'custom-product-boxes' ),
				'desc_tip' => 'true',
				'description' => __( 'Allows Customers to send a message along with the Gift Box', 'custom-product-boxes' ),
				'value' => $cpb_product->get_enable_gift_message( 'edit' ),
			)
		);

		woocommerce_wp_text_input(
			array(
				'id' => 'cpb_message_label',
				'label' => __( 'Gift Message Label', 'custom-product-boxes' ),
				'placeholder' => '',
				'desc_tip' => 'true',
				'description' => __( "Set a label for 'Gift Message' field", 'custom-product-boxes' ),
				'type' => 'text',
				'value' => $cpb_product->get_gift_message_label( 'edit' ),
			)
		);

		cpb_hr_element( __( 'Add-ons', 'custom-product-boxes' ) );

		?>
		<div>
			<p class='form-field wdm_addon_note'>
				<label>
					<?php esc_html_e( 'NOTE:', 'custom-product-boxes' ); ?>
				</label>
				<ol class = 'wdm_note_list'>
					<li>
						<?php esc_html_e( "When you add a main variable product to the Add-On Product list, the list includes all of its variations. To specify individual variations check the 'Include Your Specific Variations' checkbox.", 'custom-product-boxes' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'If you add a main variable product as well as its variation to the Add-On Product list, then the variation will be shown only once.', 'custom-product-boxes' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'For prefilled products add quantity that is available in stock.', 'custom-product-boxes' ); ?>
					</li>
					<li>
						<?php esc_html_e( 'Once you update the individual product deatils you need to update the product again in the Add-On Product list.', 'custom-product-boxes' ); ?>
					</li>
				</ol>
			</p>
		</div>
		<?php

		do_action( 'cpb_addon_list_container', $cpb_product, $addon_items_list );

		do_action( 'cpb_prefilled_settings', $cpb_product, $addon_items_list );
	}

	/**
	 * CPB addon selector.
	 *
	 * @param  object $cpb_product      CPB product object.
	 * @return void
	 */
	public static function cpb_addon_list_selector_container( $cpb_product, $addon_items_list ) {
		$json_ids = array();

		if ( ! empty( $addon_items_list ) ) {
			foreach ( $addon_items_list as $product_id => $product_data ) {
				$product = wc_get_product( $product_id );
				$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );

				unset( $product_data );
			}
		}

		include dirname( __FILE__ ) . '/views/cpb-addon-select-search.php';

		woocommerce_wp_checkbox(
			array(
				'id' => 'include_variations',
				'label' => __( 'Include Your Specific Variations?', 'custom-product-boxes' ),
				// 'desc_tip' => 'true',
				'description' => __( 'Include individual variations you want. Else we will include all associated variations.', 'custom-product-boxes' ),
				'value' => $cpb_product->get_include_variations( 'edit' ),
			)
		);

		do_action( 'cpb_show_variation_selection', $cpb_product, $addon_items_list );
	}

	/**
	 * Displaying Accordion for Variable addons.
	 *
	 * @param  object $cpb_product      CPB Product object.
	 * @return void
	 * @SuppressWarnings("unused")
	 */
	public static function cpb_variable_accordion( $cpb_product, $addon_items_list ) {
		$display_prop = 'block';

		if ( ! $cpb_product->get_include_variations() ) {
			$display_prop = 'none';
		}
		?>
		<div id="cpb-variation-selection" style="display: <?php echo esc_attr( $display_prop ); ?>">
			<div id="cpb-accordion">
				<?php
				include dirname( __FILE__ ) . '/views/cpb-html-variable-accordion.php';
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Process and save products data in DB.
	 *
	 * @param  object $product CPB product object.
	 * @return void
	 */
	public static function cpb_process_data( $product ) {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		update_option( 'cpb_data_400', true );
		if ( ! $product->is_type( 'wdm_bundle_product' ) ) {
			return;
		}

		if ( ! defined( 'CPB_UPDATING' ) && get_option( 'cpb_data_400' ) ) {

			$add_ons_post_data    = isset( $_POST['add_on_products'] ) && ! empty( $_POST['add_on_products'] ) ? json_decode( sanitize_text_field( wp_unslash( $_POST['add_on_products'] ) ), true ) : array();
			self::save_product_data( $product, $add_ons_post_data );

			// subscription code.
			if ( isset( $_POST['cpb_subscription'] ) && isset( $_POST['_subscription_price'] ) && sanitize_text_field( wp_unslash( $_POST['_subscription_price'] ) ) != 0 ) {
				$post_id = $product->get_id();
				wp_set_object_terms( $post_id, 'subscription', 'wdm_bundle_product' );
				update_post_meta( $post_id, '_subscription_price', sanitize_text_field( wp_unslash( $_POST['_subscription_price'] ) ) );
				update_post_meta( $post_id, '_subscription_sign_up_fee', sanitize_text_field( wp_unslash( $_POST['_subscription_sign_up_fee'] ) ) );
				update_post_meta( $post_id, '_subscription_period', sanitize_text_field( wp_unslash( $_POST['_subscription_period'] ) ) );
				update_post_meta( $post_id, '_subscription_period_interval', sanitize_text_field( wp_unslash( $_POST['_subscription_period_interval'] ) ) );
				update_post_meta( $post_id, '_subscription_length', sanitize_text_field( wp_unslash( $_POST['_subscription_length'] ) ) );
				update_post_meta( $post_id, '_subscription_trial_period', sanitize_text_field( wp_unslash( $_POST['_subscription_trial_period'] ) ) );
				update_post_meta( $post_id, '_subscription_trial_length', sanitize_text_field( wp_unslash( $_POST['_subscription_trial_length'] ) ) );
				// update_post_meta( $post_id, 'cpb_subscription', sanitize_text_field( $_POST['cpb_subscription'] ) );
			} else {
				$post_id = $product->get_id();
				wp_remove_object_terms( $post_id, 'subscription', 'wdm_bundle_product' );
				delete_post_meta( $post_id, '_subscription_price' );
				delete_post_meta( $post_id, '_subscription_sign_up_fee' );
				delete_post_meta( $post_id, '_subscription_period' );
				delete_post_meta( $post_id, '_subscription_period_interval' );
				delete_post_meta( $post_id, '_subscription_length' );
				delete_post_meta( $post_id, '_subscription_trial_period' );
				delete_post_meta( $post_id, '_subscription_trial_length' );
				// update_post_meta( $post_id, 'cpb_subscription', 'no' );
			}
		} else {
			CPB_Admin_Notices::add_notice( 'cpb-update' );
			// self::add_php_notice( __( 'Your changes have not been saved &ndash; please wait for the <strong>WooCommerce Product Bundles Data Update</strong> routine to complete before creating new bundles or making changes to existing ones.', 'woocommerce-product-bundles' ) );.
		}
	}

	/**
	 * Process data for addons submited while publish and update
	 *
	 * @param  object $product CPB product object.
	 * @param  array  $add_on_data  Data of bundles addons.
	 * @return array processed add_on_data
	 */
	public static function process_add_on_data( $product, $add_on_data ) {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		// $add_on_data = json_decode( json_decode( $add_on_data ), true );
		foreach ( $add_on_data as $product_id => $product_data ) {
			if ( 'variable' !== $add_on_data[ $product_id ]['product_type'] ) {
				continue;
			}

			if ( ! empty( $_POST['include_variations'] ) && ! empty( $product_data['variations'] ) ) {
				continue;
			}

			$product = wc_get_product( $product_id );
			$selected_variations = ! empty( $product_data['variations'] ) ? $product_data['variations'] : array();
			$selected_variations = array_map( 'add_selected_tag', $selected_variations );
			$selected_variations = array_map( 'add_product_type', $selected_variations );
			$remaining_variations = CPB_Admin_Search_Addons::add_all_variations_in_search_results( $product_id, $product, array_keys( $selected_variations ) );

			$add_on_data[ $product_id ]['variations'] = array_merge( $selected_variations, $remaining_variations );
		}

		// storing prefilled data.
		if ( ! empty( $_POST['cpb_enable_prefilled'] ) ) {
			$prefill_products = isset( $_POST['wdm_cpb_products'] ) ? $_POST['wdm_cpb_products'] : array();
			$prefill_qty = isset( $_POST['wdm_prefill_qty'] ) ? $_POST['wdm_prefill_qty'] : array();
			$prefill_mandatory = isset( $_POST['prod_mandatory'] ) ? $_POST['prod_mandatory'] : array();

			// actions to process or save prefilled data in custom table.
			do_action( 'cpb_process_prefilled_products_data', $product, $add_on_data, $prefill_products, $prefill_qty, $prefill_mandatory );
		} else {
			// actions to delete prefilled products if prefilled is disabled.
			do_action( 'cpb_delete_prefilled_products', $product );
		}

		return $add_on_data;
	}

	/**
	 * Save products settings and Properties here
	 *
	 * @param  object $product CPB product object.
	 * @param  array  $add_ons_post_data Data of bundles addons.
	 * @return void                    [description]
	 */
	public static function save_product_data( $product, $add_ons_post_data ) {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification

		if ( isset( $_POST['cpb_box_capacity'] ) && ! empty( $_POST['cpb_box_capacity'] ) ) {
			$capacity = cpb_box_capacity( sanitize_text_field( wp_unslash( $_POST['cpb_box_capacity'] ) ) );
		}

		if ( empty( $add_ons_post_data ) ) {

			// add a notice __( 'Please add at least one product to the bundle before publishing. To add products, click on the <strong>Bundled Products</strong> tab.', 'custom-product-boxes' );.

			// $props['addon_items_list'] = array();
		} else {
			$add_ons_post_data = self::process_add_on_data( $product, $add_ons_post_data );
			// $props['addon_items_list'] = $add_ons_post_data;
		}

		// Other plugins or developers can add their properties to CPB props array.
		$product->set(
			apply_filters(
				'cpb_product_props_array',
				array(
					'pricing_type' => isset( $_POST['cpb_pricing_method'] ) ? sanitize_text_field( wp_unslash( $_POST['cpb_pricing_method'] ) ) : 'cpb-fixed-price',
					'box_capacity'           => $capacity,
					'partially_filled_box'   => isset( $_POST['cpb_allow_partially_filled'] ),
					'sort_by_date'           => isset( $_POST['cpb_order_by_date'] ),
					'enable_gift_message'    => isset( $_POST['cpb_enable_message'] ),
					'gift_message_label'     => isset( $_POST['cpb_message_label'] ) ? sanitize_text_field( wp_unslash( $_POST['cpb_message_label'] ) ) : __( 'Add Note', 'custom-product-boxes' ),
					'prefilled'              => isset( $_POST['cpb_enable_prefilled'] ),
					'include_variations' => isset( $_POST['include_variations'] ),
					'swap_prefilled' => isset( $_POST['cpb_swap_products'] ),
					'cpb_subscription' => isset( $_POST['cpb_subscription'] ) && isset( $_POST['_subscription_price'] ) && sanitize_text_field( wp_unslash( $_POST['_subscription_price'] ) ) != 0,
					'addon_items_list'       => $add_ons_post_data,
				)
			)
		);
	}
}
