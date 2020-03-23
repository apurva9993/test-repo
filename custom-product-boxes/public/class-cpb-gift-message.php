<?php
/**
 * Loads products display
 *
 * @author      WisdmLabs
 * @package     CPB/Public
 * @version     4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'CPB_Gift_Message' ) ) {
	/**
	* This class is responsible for handling the custom gift message data if it is
	* enabled for the product.
	*/
	class CPB_Gift_Message {

		/**
		* Adds Actions :
		* 1: Action to maintain a session for custom gift message data.
		* 2: Action to put session data to woocommerce session.
		* 3: Action to get the cart-item data from session.
		* 4: Action to display the custom data.
		* 5: Action to add the custom data to order meta table.
		* 6: Action to remove the custom data when the product is removed from cart.
		*/
		public function __construct() {
			add_action( 'wp_ajax_wdm_add_gift_message_session', array( $this, 'add_gift_messsage_session' ) );
			add_action( 'wp_ajax_nopriv_wdm_add_gift_message_session', array( $this, 'add_gift_messsage_session' ) );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_message_data' ), 1, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_gift_message_from_session' ), 1, 3 );
			// add_filter('woocommerce_checkout_cart_item_quantity', array($this, 'checkoutGiftMessage'), 1, 3);
			// add_filter('woocommerce_cart_item_name', array($this, 'checkoutGiftMessage'), 1, 3);
			add_filter( 'woocommerce_get_item_data', array( $this, 'display_gift_message' ), 10, 2 );
			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				add_action( 'woocommerce_add_order_item_meta', array( $this, 'add_gift_message_to_order_meta' ), 1, 2 );
			} else {
				add_action( 'woocommerce_new_order_item', array( $this, 'add_gift_message_to_order_meta' ), 1, 2 );
			}
			add_action( 'woocommerce_cart_item_removed', array( $this, 'remove_message_on_product_remove' ), 1, 1 );
		}

		/**
		* Gets the gift message label from the database which is stored by admin
		* through the CPB Product settings
		* @param int $product_id CPB Product Id
		* @return string gift message label.
		*/
		public static function get_message_label( $product_id ) {
			$gift_label = get_post_meta( $product_id, '_wdm_gift_message_label', true );

			if ( empty( $gift_label ) ) {
				$gift_label = __( 'Gift Message', 'custom-product-boxes' );
			}

			return apply_filters( 'wdm_cpb_gift_msg_label', $gift_label );
		}

		/**
		* Sets a session for gift message data for the cart.
		*/
		public function add_gift_messsage_session() {
			//Gift Message data - Sent Via AJAX post method
			if ( ! isset( $_POST['product_id'] ) ) {
				die();
			}

			$product_id = $_POST['product_id'];
			$msg_data = $_POST['msg_data'];
			// session_start();
			$_SESSION[ 'wdm_gift_message_' . $product_id ] = $msg_data;
			die();
		}

		/**
		* Adding the custom gift message session data to the woocommerce session.
		* Attach with cart item (session) data the gift message data.
		* Unset the custom gift message session after adding data to woocommerce
		* session.
		* @param array $cart_item_data Cart-Item-Data.
		* @param int $product_id Custom product for which session is set.
		*/
		public function add_message_data( $cart_item_data, $product_id ) {
			/*Here, We are adding item in WooCommerce session with, wdm_user_custom_data_value name*/
			// session_start();
			$enable_gift_message = get_post_meta( $product_id, 'cpb_enable_message', true );
			if ( 'yes' != $enable_gift_message ) {
				return $cart_item_data;
			}

			if ( isset( $_SESSION[ 'wdm_gift_message_' . $product_id ] ) ) {
				$gift_message = $_SESSION[ 'wdm_gift_message_' . $product_id ];
				$new_gift_message = array( 'wdm_gift_message_' . $product_id => $gift_message );
			}

			if ( empty( $gift_message ) ) {
				return $cart_item_data;
			} else {
				if ( empty( $cart_item_data ) ) {
					return $new_gift_message;
				} else {
					return array_merge( $cart_item_data, $new_gift_message );
				}
			}
			unset( $_SESSION[ 'wdm_gift_message_' . $product_id ] );
		}

		/**
		* Extract the custom gift message from woocommerce session and add to cart * object.
		* @param array $item cart-item-object data.
		* @param array $values values in the woocommerce session.
		* @param string $key key for session variable of gift message.
		* @return array $item cart-item-object data with custom gift message
		* attached.
		*/
		public function get_gift_message_from_session( $item, $values, $key ) {
			$product_id = $item['product_id'];
			$enable_gift_message = get_post_meta( $product_id, 'cpb_enable_message', true );
			if ( 'yes' != $enable_gift_message ) {
				return $item;
			}

			if ( array_key_exists( 'wdm_gift_message_' . $product_id, $values ) ) {
				$item[ 'wdm_gift_message_' . $product_id ] = $values[ 'wdm_gift_message_' . $product_id ];
			}
			unset( $key ); // unsed while pushing to git
			return $item;
		}

		// public function checkoutGiftMessage($product_name, $values, $cart_item_key)
		// {
		//     unset($cart_item_key); // unsed while pushing to git
		//     $product_id = $values['product_id'];
		//     $enable_gift_message = get_post_meta($product_id, 'cpb_enable_message', true);
		//     if ($enable_gift_message != 'yes') {
		//         return $product_name;
		//     }


		//     if (!isset($values['wdm_gift_message_'.$product_id])) {
		//         return $product_name;
		//     }

		//     if (count($values['wdm_gift_message_'.$product_id]) > 0) {
		//         $gift_label = self::get_message_label($product_id);
		//         $return_string = $product_name . "</a><dl class='variation'>";
		//         $return_string .= "<p class = 'msg_title'>" .$gift_label.": </p><p class = 'msg_cart'>". $values['wdm_gift_message_'.$product_id] . "</p>";
		//         $return_string .= "</dl>";
		//         return $return_string;
		//     } else {
		//         return $product_name;
		//     }
		// }

		/**
		* Add Custom gift message Data as Metadata to the Order Items
		* @param int $item_id Order item id.
		* @param array $values meta values.
		*/
		public function add_gift_message_to_order_meta( $item_id, $values ) {
			$product_id = $values['product_id'];
			$enable_gift_message = get_post_meta( $product_id, 'cpb_enable_message', true );
			if ( 'yes' != $enable_gift_message ) {
				return;
			}

			if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
				$user_custom_values = isset( $values[ 'wdm_gift_message_' . $product_id ] ) ? $values[ 'wdm_gift_message_' . $product_id ] : '';
			} else {
				$user_custom_values = isset( $values->legacy_values[ 'wdm_gift_message_' . $product_id ] ) ? $values->legacy_values[ 'wdm_gift_message_' . $product_id ] : '';
			}

			if ( ! empty( $user_custom_values ) ) {
				wc_add_order_item_meta( $item_id, 'wdm_giftmsg_key', $user_custom_values );
			}
		}

		/**
		* Add custom gift message data to Order array
		* Gets and formats a list of cart item data + variations for display on the * frontend.
		* @param array $data item data.
		* @param array $cart_item cart-item data.
		* @return array $data item data with the variations of custom data included.
		*/
		public function display_gift_message( $data, $cart_item ) {
			$product_id = $cart_item['product_id'];
			if ( isset( $cart_item[ 'wdm_gift_message_' . $product_id ] ) ) {
				$enable_gift_message = get_post_meta( $product_id, 'cpb_enable_message', true );
				if ( 'yes' != $enable_gift_message ) {
					return $data;
				}
				$gift_label = self::get_message_label( $product_id );
				$value = $cart_item[ 'wdm_gift_message_' . $product_id ];

				$data[] = array(
					'key' => $gift_label,
					'name' => 'wdm_giftmsg_key',
					'value' => $value,
				);
			}
			return $data;
		}

		/**
		* Remove the custom gift message,when the product is removed from cart.
		* @param string $cart_item_key cart-item-key for custom data.
		*/
		public function remove_message_on_product_remove( $cart_item_key ) {
			global $woocommerce;
			// Get cart
			$cart = $woocommerce->cart->get_cart();
			// For each item in cart, if item is upsell of deleted product, delete it
			foreach ( $cart as $key => $values ) {
				$product_id = $values['product_id'];
				if ( isset( $values[ 'wdm_gift_message_' . $product_id ] ) && $values[ 'wdm_gift_message_' . $product_id ] == $cart_item_key ) {
					unset( $woocommerce->cart->cart_contents[ $key ] );
				}
			}
		}
	}
}

$GLOBALS['gift_message'] = new CPB_Gift_Message();
