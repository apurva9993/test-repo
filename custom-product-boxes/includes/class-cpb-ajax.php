<?php
/**
 * CPB Ajax
 *
 * @class    CPB_Ajax
 * @package  CPB/Ajax
 * @version  4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * CPB_Ajax class.
 */
class CPB_Ajax {

	/**
	 * Stores array of error code and its message.
	 *
	 * @var array
	 */
	public $error_messages;

	/**
	 * Constructor for ajax class
	 */
	public function __construct() {
		$this->error_messages = $this->get_error_message();
		add_action( 'wp_ajax_woocommerce_ajax_add_to_cart', array( $this, 'cpb_ajax_add_to_cart' ) );
		add_action( 'wp_ajax_nopriv_woocommerce_ajax_add_to_cart', array( $this, 'cpb_ajax_add_to_cart' ) );
	}

	/**
	 * Ajax call back function. Responsible for initiating addition of box product to cart
	 *
	 * @return void
	 */
	public function cpb_ajax_add_to_cart() {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		global $cpb_bundled_data;
		$cpb_product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['cpb_product_id'] ) );
		$cpb_quantity = empty( $_POST['cpb_product_qty'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['cpb_product_qty'] ) );
		$addon_list = empty( $_POST['cpb_addon_ids'] ) ? '' : $_POST['cpb_addon_ids'];
		$addon_list = json_decode( stripslashes( $addon_list ), true );

		$cpb_bundled_data = array();

		if ( empty( $addon_list ) ) {
			$data = array(
				'error_msg' => $this->error_messages['empty_box'],
				'error' => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);
			echo wp_send_json( $data );
		}

		foreach ( $addon_list as $addon_id => $addon_quantity ) {
			$product_id = $addon_id;
			$variation = array();

			if ( is_unique_id( $addon_id ) ) {
				$variation_data = get_variation_data_from_key( $addon_id );
				$variation_id = isset( $variation_data['variation_id'] ) ? $variation_data['variation_id'] : 0;
				$variation = isset( $variation_data['variation'] ) ? $variation_data['variation'] : array();
				$product_id = isset( $variation_data['product_id'] ) ? $variation_data['product_id'] : $product_id;
			}

			$this->cpb_build_bundle_data( $product_id, $addon_quantity, $variation_id, $variation, array( 'cpb_bundled_in' => $cpb_product_id ), $addon_id, $cpb_product_id );
		}

		if ( ! empty( $cpb_bundled_data ) ) {
			$this->cpb_add_box_to_cart( $cpb_product_id, $cpb_quantity );
		}

		//
		WC_AJAX::get_refreshed_fragments();

		wp_die();
	}

	/**
	 * This function adds CPB addons data to the $cpb_bundled_data.
	 * @param int   $product_id contains the id of the product to add to the cart.
	 * @param int   $quantity contains the quantity of the item to add.
	 * @param int   $variation_id ID of the variation being added to the cart.
	 * @param array $variation attribute values.
	 * @param array $cart_item_data extra cart item data we want to pass into the item.
	 * @param int   $cpb_product_id CPB box product Id
	 * @return boolean
	 */
	public function cpb_build_bundle_data( $product_id, $addon_quantity, $variation_id, $variation, $cart_item_data, $addon_id, $cpb_product_id ) {
		global $cpb_bundled_data;
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $variation_id ? $variation_id : $product_id, $addon_quantity );
		error_log( 'Build Bundle :: ' . $passed_validation );
		error_log( 'addon_quantity Bundle :: ' . $addon_quantity );
		$product_status = get_post_status( $variation_id ? $variation_id : $product_id );

		if ( $passed_validation && 'publish' === $product_status ) {
			$cpb_bundled_data = apply_filters(
				'cpb_cart_added_bundled_ids',
				array_merge(
					$cpb_bundled_data,
					array(
						$addon_id => array(
							'product_id'        => $product_id,
							'addon_quantity'    => $addon_quantity,
							'variation_id'      => $variation_id,
							'variation'         => $variation,
							'cart_item_data'    => $cart_item_data,
							'passed_validation' => $passed_validation,
							'product_status'    => $product_status,
						),
					)
				)
			);
		} else {
			$data = array(
				'error' => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $cpb_product_id ), $cpb_product_id ),
			);

			echo wp_send_json( $data );
		}

	}

	public function add_addons_to_cart() {
		global $cpb_bundled_data;

		if ( empty( $cpb_bundled_data ) ) {
			return;
		}

		foreach ( $cpb_bundled_data as $addon_data ) {
			$this->cpb_add_addons_to_cart( $addon_data );
		}
	}

	/**
	 * This function adds CPB addons to the cart.
	 *
	 * @param string $addon_id selected addon id.
	 * @param array  $data     data to be added
	 * @return boolean
	 */
	public function cpb_add_addons_to_cart( $addon_data ) {
		$added_to_cart = WC()->cart->add_to_cart( $addon_data['product_id'], $addon_data['addon_quantity'], $addon_data['variation_id'], $addon_data['variation'], $addon_data['cart_item_data'] );

		if ( $addon_data['passed_validation'] && $added_to_cart && 'publish' === $addon_data['product_status'] ) {

			do_action( 'woocommerce_ajax_added_to_cart', $addon_data['product_id'] );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $addon_data['product_id'] => $addon_data['addon_quantity'] ), true );
			}
		} else {
			$data = array(
				'error' => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $addon_data['product_id'] ), $addon_data['product_id'] ),
			);

			echo wp_send_json( $data );
		}
	}

	public function cpb_add_box_to_cart( $cpb_product_id, $cpb_quantity ) {
		global $cpb_bundled_data;
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $cpb_product_id, $cpb_quantity );
		$product_status    = get_post_status( $cpb_product_id );

		$cpb_get_quantities = function( $value ) {
			return $value['addon_quantity'];
		};

		$bundled_ids = array_combine(
			array_keys( $cpb_bundled_data ),
			array_map( $cpb_get_quantities, array_values( $cpb_bundled_data ) )
		);

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $cpb_product_id, $cpb_quantity, 0, array(), array( 'cpb_bundled_ids' => $bundled_ids ) ) && 'publish' === $product_status ) {
			do_action( 'woocommerce_ajax_added_to_cart', $cpb_product_id );

			$this->add_addons_to_cart();

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $cpb_product_id => $cpb_quantity ), true );
			}
		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $cpb_product_id ), $cpb_product_id ),
			);

			wp_send_json( $data );
		}
	}

	public function get_error_message() {
		return apply_filters(
			'cpb_error_messages',
			array(
				'empty_box' => __( 'Please fill the box before adding it to cart.', 'custom-product-boxes' ),
				'other' => __( 'Please fill the box before adding it to cart.', 'custom-product-boxes' ),
			)
		);
	}
}

return new CPB_Ajax();
