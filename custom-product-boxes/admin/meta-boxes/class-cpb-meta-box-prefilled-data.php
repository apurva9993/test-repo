<?php
/**
 * CPB_Meta_Box_Prefilled_Data class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/MetaBox
 * @since    5.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Prefilled Products meta-box data for the CPB Product type.
 *
 * @class    CPB_Meta_Box_Prefilled_Data
 * @version  5.9.0
 */
class CPB_Meta_Box_Prefilled_Data {
	/**
	 * Hook in.
	 */
	public static function init() {
		// view.
		add_action( 'cpb_prefilled_settings', array( __CLASS__, 'display_prefilled_settings' ), 10, 2 );
		add_action( 'cpb_prefill_table', array( __CLASS__, 'display_prefill_table' ), 10, 2 );
		add_action( 'cpb_prefill_rows', array( __CLASS__, 'display_prefill_rows' ), 10, 2 );
		add_action( 'cpb_mandatory_checkboxes', array( __CLASS__, 'display_madatory_checkboxes' ), 10, 2 );
		add_action( 'cpb_prefilled_select_options', array( __CLASS__, 'display_select_options' ), 10, 3 );
		// controller.
		add_action( 'cpb_process_prefilled_products_data', array( __CLASS__, 'cpb_process_prefilled_products_data' ), 10, 5 );
		add_action( 'cpb_delete_prefilled_products', array( __CLASS__, 'cpb_delete_prefilled_products' ) );

		add_action( 'wp_ajax_cpb_get_prefilled', array( __CLASS__, 'cpb_return_prefilled_data_to_js' ) );
		add_action( 'wp_ajax_cpb_is_sold_individual', array( __CLASS__, 'check_if_sold_individual' ) );
	}

	/**
	 * Function for displaying Prefilled settings
	 *
	 * @param  object $cpb_product CPB Product object.
	 * @param  array $addon_items_list list of addon items.
	 * @return void
	 */
	public static function display_prefilled_settings( $cpb_product, $addon_items_list ) {
		global $cpb_product, $addon_list;

		$addon_list = get_addon_including_variation_product( $addon_items_list );

		woocommerce_wp_checkbox(
			array(
				'id' => 'cpb_enable_prefilled',
				'label' => __( 'Pre-Filled Box', 'custom-product-boxes' ),
				'description' => __( 'Allow pre-filled box', 'custom-product-boxes' ),
				'value' => $cpb_product->get_prefilled( 'edit' ),
			)
		);

		do_action( 'cpb_prefill_table', $cpb_product, $addon_list );

		woocommerce_wp_checkbox(
			array(
				'id' => 'cpb_swap_products',
				'label' => __( 'Remove Mandatory Products', 'custom-product-boxes' ),
				'description' => __( 'Allows user to remove mandatory products only if they run out of stock', 'custom-product-boxes' ),
				'value' => $cpb_product->get_swap_prefilled( 'edit' ),
			)
		);

	}

	/**
	 * Function to display prefilled products table
	 * @return void
	 * @SuppressWarnings("unused")
	 */
	public static function display_prefill_table( $cpb_product, $addon_list ) {
		include dirname( __FILE__ ) . '/views/cpb-html-prefilled-table.php';
	}

	/**
	 * Displays rows of Prefilled table
	 * @param  array $prefill_data addon items list
	 * @param  array $prefill_list prefilled products
	 * @return void
	 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
	 */
	public static function display_prefill_rows( $cpb_product, $addon_list ) {
		global $prefill_manager;
		$prefill_list = $prefill_manager->get_prefilled_products( $cpb_product->get_id() );
		if ( ! empty( $addon_list ) ) {
			$addon_ids = array_keys( $addon_list );
			include dirname( __FILE__ ) . '/views/cpb-html-prefilled-rows.php';
			unset( $addon_ids );
		}
	}

	/**
	 * Displays the checkboxes of mandatory prefilled items.
	 *
	 * @param  array $addon_ids      Ids of addon products.
	 * @param  array $prefilled_data Prefilled product data.
	 * @return void
	 * @SuppressWarnings("unused")
	 */
	public static function display_madatory_checkboxes( $addon_ids, $prefilled_data ) { // @codingStandardsIgnoreLine.
		include dirname( __FILE__ ) . '/views/cpb-html-prefilled-mandatory-checkboxes.php';
	}

	/**
	 * Displays Options of prefiled rows select dropdown.
	 *
	 * @param  array $addon_ids      Ids of addon products.
	 * @param  array $prefilled_data Prefilled product data.
	 * @param  array $addon_list     Addon Items list.
	 * @SuppressWarnings("unused")
	 */
	public static function display_select_options( $addon_ids, $prefilled_data, $addon_list ) {
		include dirname( __FILE__ ) . '/views/cpb-html-prefilled-select-options.php';
	}

	/**
	 * This function returns prefilled data to JS.
	 *
	 * @return void
	 */
	public static function cpb_return_prefilled_data_to_js() {
		$product_id = filter_input( INPUT_POST, 'cpb_id', FILTER_VALIDATE_INT );
		$cpb_product = ! empty( $product_id ) ? wc_get_product( $product_id ) : null;

		if ( ! $cpb_product ) {
			wp_send_json( array() );
		}

		$addon_items_list = $cpb_product->get_addon_items_list();
		$prefill_data = get_addon_including_variation_product( $addon_items_list );
		$cpb_prefilled_data = array(
			'enablePrefillProducts' => $cpb_product->get_prefilled(),
			'prefillBundleData' => $prefill_data,
		);

		wp_send_json( $cpb_prefilled_data );
	}

	/**
	 * Processes the prefilled data to be stored in DB
	 * Update the pre-filled products data in DB with the pre-filled products
	 * present in the current selection;.
	 *
	 * @param  Object $product CPB product.
	 * @param  array  $bundle_data CPB addon data.
	 * @param  array $prefill_products array of prefilled products id.
	 * @param  array $prefill_qty array of prefilled products quantity.
	 * @param  array $prefill_mandatory array of prefilled products if the are compulsory.
	 */
	public static function cpb_process_prefilled_products_data( $product, $bundle_data, $prefill_products, $prefill_qty, $prefill_mandatory ) {
		global $prefill_manager;
		$bundle_data = get_addon_including_variation_product( $bundle_data );

		$unformatted_data = compact( 'prefill_qty', 'prefill_mandatory' );

		$formatted_data = array(
			'new_prefill_products' => array(),
			'new_prefill_qty'      => array(),
			'new_mandatory'        => array(),
			'position'             => 0,
			'prefill_checkbox'     => array(),
		);

		foreach ( $prefill_products as $prefilled_id => $value ) { // filters unique product so that duplicate records do not get added.
			$formatted_data = self::get_formated_prefilled_data( $unformatted_data, $formatted_data, $value, $prefilled_id );
		}

		if ( $formatted_data['new_prefill_products'] && $formatted_data['new_prefill_qty'] ) {
			$prefill_manager->save_prefilled_products( $product->get_id(), $formatted_data['new_prefill_products'], $formatted_data['new_prefill_qty'], $formatted_data['prefill_checkbox'], $bundle_data );
		}
	}


	/**
	 * Gets formated prefilled data to save.
	 *
	 * @param array  $unformatted_data unformated prefilled data.
	 * @param array  $formatted_data Formatted prefilled data.
	 * @param string $value Value for the field submitted.
	 * @param string $prefilled_id Id of preffiled product.
	 */
	public static function get_formated_prefilled_data( $unformatted_data, $formatted_data, $value, $prefilled_id ) {
		$position = $formatted_data['position'];

		$index = array_search( $value, $formatted_data['new_prefill_products'] );

		if ( false === $index ) {
			$formatted_data['new_prefill_products'][ $position ] = $value;
			$formatted_data['new_prefill_qty'][ $position ] = $unformatted_data['prefill_qty'][ $prefilled_id ];
			$formatted_data['new_mandatory'][ $position ] = isset( $unformatted_data['prefill_mandatory'][ $prefilled_id ] ) ? $unformatted_data['prefill_mandatory'][ $prefilled_id ] : '';
			$position++;
		} elseif ( false !== $index && $formatted_data['new_prefill_qty'][ $index ] !== $unformatted_data['prefill_qty'][ $prefilled_id ] ) {
			$formatted_data['new_prefill_products'][ $position ] = $value;
			$formatted_data['new_prefill_qty'][ $position ] = $unformatted_data['prefill_qty'][ $prefilled_id ];
			$formatted_data['new_mandatory'][ $position ] = $unformatted_data['prefill_mandatory'][ $prefilled_id ];
			$position++;
		}

		$formatted_data['position'] = $position;

		foreach ( $formatted_data['new_prefill_products'] as $prefilled_id => $value ) {
			$formatted_data['prefill_checkbox'][ $prefilled_id ] = in_array( $value, $formatted_data['new_mandatory'] ) ? 1 : 0;
		}

		return $formatted_data;
	}

	/**
	 * CPB Delete prefilled products.
	 *
	 * @param  object $product product object.
	 * @return void
	 */
	public static function cpb_delete_prefilled_products( $product ) {
		global $prefill_manager;

		$prefill_manager->delete_prefilled_products( $product->get_id() );
	}


	/**
	 * Checks whether the products are sold individually & have prefilled
	 * quantity set greater than 1. Invoked by ajax call
	 * return to ajax json array returns array containing produt ids of sold
	 * individual products whoes quantity is set greater than 1
	 */
	public static function check_if_sold_individual() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification

		if ( ! isset( $_POST['product_ids'] ) ) {
			return;
		}

		$product_ids = isset( $_POST['product_ids'] ) ? sanitize_text_field( wp_unslash( $_POST['product_ids'] ) ) : null;
		$product_data = isset( $_POST['product_data'] ) ? sanitize_text_field( wp_unslash( $_POST['product_data'] ) ) : null;

		$sldind_ids = array();
		foreach ( $product_ids as $product_id => $qty ) {
			$product = wc_get_product( $product_data[ $product_id ] );
			if ( $product->is_sold_individually() && $qty > 1 ) {
				$sldind_ids[] = $product_data[ $product_id ];
			}
		}
		$sldind_ids = array_unique( $sldind_ids );
		echo json_encode( $sldind_ids );
		die();
	}

}
