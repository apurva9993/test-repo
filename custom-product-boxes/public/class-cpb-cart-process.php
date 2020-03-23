<?php
/**
 * Process cart functionalities for CPB product.
 *
 * @author      WisdmLabs
 * @package     CPB/Cart
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CPB_Cart_Process' ) ) :

	/**
	 * Cart process for CPB Product type.
	 *
	 * @class    CPB_Cart_Process
	 * @version  4.0.0
	 */
	class CPB_Cart_Process {

		/**
		 * The single instance of the class.
		 *
		 * @var CPB_Cart_Process
		 *
		 * @since 5.0.0
		 */
		protected static $_instance = null;

		/**
		 * Main CPB_Cart_Process instance. Ensures only one instance of CPB_Cart_Process is loaded or can be loaded.
		 *
		 * @since  5.0.0
		 *
		 * @return CPB_Cart_Process
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
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
			// display errors.
			add_action( 'init', array( $this, 'cpb_show_add_to_cart' ) );
			add_filter( 'woocommerce_cart_item_class', array( $this, 'cpb_addon_cart_item_class' ), 10, 3 );
			add_filter( 'woocommerce_cart_item_remove_link', array( $this, 'cpb_disable_addons_remove_link' ), 10, 2 );
			add_filter( 'woocommerce_get_item_data', array( $this, 'cpb_display_item_data' ), 10, 2 );

			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'cpb_add_tag_to_order_item_meta' ), 1, 2 );
			} else {
				add_action( 'woocommerce_new_order_item', array( $this, 'cpb_add_tag_to_order_item_meta' ), 1, 2 );
			}
			// Sync quantities of bundled items with bundle quantity
			// add_filter( 'woocommerce_cart_item_quantity', array( $this, 'cpb_bundles_cart_item_quantity' ), 10, 2 );

			add_action( 'woocommerce_cart_item_removed', array( $this, 'cpb_remove_child_products' ), 1, 2 );
		}

		public function cpb_show_add_to_cart() {

		}

		/**
		* Adds the CPB Item in cart to the order meta table.
		*
		* @param int   $item_id Order Item Id.
		* @param array $values values in the cart session for custom box data.
		*/
		public function cpb_add_tag_to_order_item_meta( $item_id, $values ) {
			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				if ( isset( $values['cpb_bundled_in'] ) ) {
					$post_object = get_post( $values['cpb_bundled_in'] );
					$cpb_name = $post_object->post_title;
					wc_add_order_item_meta( $item_id, __( 'Included with', 'custom-product-boxes' ), $cpb_name );
				}
			} else {
				if ( isset( $values->legacy_values['cpb_bundled_in'] ) ) {
					$post_object = get_post( $values->legacy_values['cpb_bundled_in'] );
					$cpb_name = $post_object->post_title;
					wc_add_order_item_meta( $item_id, __( 'Included with', 'custom-product-boxes' ), $cpb_name );
				}
			}
		}

		public function cpb_addon_cart_item_class( $class, $cart_item ) {
			if ( isset( $cart_item['cpb_bundled_in'] ) ) {
				return $class . ' cpb_addon_item';
			} else {
				return $class;
			}
		}

		public function cpb_display_item_data( $data, $cart_item ) {
			/*global $woocommerce;

			$cart_contents = $woocommerce->cart->cart_contents;*/

			if ( isset( $cart_item['cpb_bundled_in'] )/* && isset( $cart_contents[ $cart_item['cpb_bundled_in'] ] )*/ ) {
				$post_object = get_post( $cart_item['cpb_bundled_in'] );
				$cpb_name = $post_object->post_title;

				$data[] = array(
					'key' => __( 'Included with', 'custom-product-boxes' ),
					'value' => $cpb_name,
				);
			}
			return $data;
		}

		public function cpb_disable_addons_remove_link( $link, $cart_item_key ) {
			global $woocommerce;
			$cart_contents = $woocommerce->cart->cart_contents;
			if ( isset( $cart_contents[ $cart_item_key ]['cpb_bundled_in'] ) /*&& isset( $cart_contents[ $cart_contents[ $cart_item_key ]['cpb_bundled_in'] ] )*/ ) {
				return '';
			}

			return $link;
		}

		/**
		 * Bundled item quantities can't be changed individually. When adjusting
		 * quantity for the container item, the bundled products must follow.
		 *
		 * @param int $quantity cart-items quantity.
		 * @param int quantity for the CPB Product in cart.
		 */
		public function cpb_bundles_cart_item_quantity( $quantity, $cart_item_key ) {
			global $woocommerce;

			$cart_contents = $woocommerce->cart->cart_contents;

			if ( ! isset( $cart_contents[ $cart_item_key ]['cpb_bundled_in'] ) ) {
				return $quantity;
			}

			if ( ! isset( $cart_contents[ $cart_contents[ $cart_item_key ]['wdm_custom_bundled_by'] ] ) && isset( $cart_contents[ $cart_item_key ]['wdm_custom_bundled_by'] ) ) {
				WC()->cart->remove_cart_item( $cart_item_key );
				return 0;
			}

			// if ( isset( $woocommerce->cart->cart_contents[ $cart_item_key ]['wdm_custom_stamp'] ) ) {
			if ( isset( $woocommerce->cart->cart_contents[ $cart_item_key ]['cpb_bundled_in'] ) ) {
				return $woocommerce->cart->cart_contents[ $cart_item_key ]['quantity'];
			}
			// }

			return $quantity;
		}

		/**
		 * Remove child products when custom product box is removed from the cart.
		 * @param string $cart_item_key cart-item-key.
		 * @param array $cart_item cart-item data.
		 */
		/*		public function cpb_remove_child_products( $cart_item_key, $cart_item ) {
			global $woocommerce;
			if ( ! isset( $cart_item->cart_contents ) ) {
				return;
			}*/
		/*				foreach ( $cart_item->cart_contents as $wdm_remove_key => $wdm_rmv_key_content ) {
					if ( isset( $wdm_rmv_key_content['wdm_custom_bundled_by'] ) ) {
						if ( $wdm_rmv_key_content['wdm_custom_bundled_by'] == $cart_item_key ) {
							$child_key = $wdm_remove_key;
							if ( ! empty( $child_key ) ) {
								$woocommerce->cart->remove_cart_item( $child_key );
								unset( $woocommerce->cart->cart_contents[ $child_key ] );
							}
						}
					}
				}*/

		// }
	}
endif;
