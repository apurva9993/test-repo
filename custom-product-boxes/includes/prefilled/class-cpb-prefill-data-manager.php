<?php
/**
 * CPB_Prefill_Data_Manager class
 *
 * @author   WisdmLabs <info@wisdmlabs.com>
 * @package  CPB/Prefilled
 * @since    5.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * This class is to handle the pre-filled products data.
 * Update, delete and insert the pre-filled products as required.
 * Gets the pre-filled products data from the database.
 */
class CPB_Prefill_Data_Manager {

	/**
	 * Holds the custom table name for storing prefilled data.
	 *
	 * @var string
	 */
	public $prefill_table;

	/**
	 * The reference to *Singleton* instance of this class.
	 *
	 * @var Singleton
	 */
	private static $instance;

	/**
	 * Stores errors message.
	 *
	 * @var String
	 */
	public $errors;

	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Singleton The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}

	/**
	 * Protected constructor to prevent creating a new instance of the
	 * *Singleton* via the `new` operator from outside of this class.
	 */
	protected function __construct() {
		global $wpdb;
		$this->prefill_table = $wpdb->prefix . 'cpb_prefilled_products_data';
	}

	/**
	 * Attaches the error message field.
	 *
	 * @param string $message message to be appended.
	 */
	public function add_error( $message ) {
		$this->errors .= $message;
	}

	/**
	 * Retrives all pre-filled products information for all CPB products
	 *
	 * @return array if CPB products contain pre-filled products then returns
	 * an array of prefilled products information else  empty  array
	 */
	public function get_all_prefilled_products() {
		global $wpdb;

		$products_list = array();
		$prefill_result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$this->prefill_table}" ) );// @codingStandardsIgnoreLine.

		if ( $prefill_result ) {
			$key = 0;
			foreach ( $prefill_result as $single_result ) {
				$products_list[ $key ]['cpb_product_id'] = $single_result->cpb_product_id;
				$products_list[ $key ]['product_id'] = $single_result->prefill_product_id;
				$products_list[ $key ]['product_qty'] = $single_result->prefill_quantity;
				$products_list[ $key ]['product_type'] = $single_result->product_type;
				$products_list[ $key ]['product_mandatory'] = $single_result->prefill_mandatory;
				$key++;
			}
		}

		if ( ! empty( $products_list ) ) {
			$products_list = array_filter( $products_list );
		}

		return $products_list;
	}

	/**
	 * Retrives information of the CPB product to which the pre-filled product
	 * belongs.
	 *
	 * @param  int $product_id pre-filled product id.
	 * @return array if product id belongs to any CPB product then returns
	 * array containing information about that CPB product else  empty  array.
	 */
	public function get_cpb_products( $product_id ) {
		global $wpdb;

		$products_list = array();
		if ( get_post_status( $product_id ) == false ) { // deleted product sync.
			$wpdb->delete(
				$this->prefill_table,
				array(
					'prefill_product_id'    => $product_id,
				),
				array(
					'%s',
				)
			);
		}

		$prefill_result = $wpdb->get_results( $wpdb->prepare( "SELECT cpb_product_id, prefill_quantity, prefill_mandatory, prefill_product_id, unique_prod_id, product_type FROM {$this->prefill_table} WHERE prefill_product_id = %s", $product_id ) );// @codingStandardsIgnoreLine.

		if ( $prefill_result ) {
			$key = 0;
			foreach ( $prefill_result as $single_result ) {
				$products_list[ $key ]['unique_prod_id'] = $single_result->unique_prod_id;
				$products_list[ $key ]['product_id'] = $single_result->prefill_product_id;
				$products_list[ $key ]['cpb_product_id'] = $single_result->cpb_product_id;
				$products_list[ $key ]['product_qty'] = $single_result->prefill_quantity;
				$products_list[ $key ]['product_type'] = $single_result->product_type;
				$products_list[ $key ]['product_mandatory'] = $single_result->prefill_mandatory;
				$products_list[ $key ]['cpb_name'] = get_the_title( $single_result->cpb_product_id );
				$key++;
			}
		}

		if ( ! empty( $products_list ) ) {
			$products_list = array_filter( $products_list );
		}

		return $products_list;
	}

	/**
	 * Retrives information for all pre-filled products for a particular CPB
	 * product
	 *
	 * @param  int $cpb_id CPB product id.
	 * @return array $products_list if CPB product contains pre-filled products
	 * then returns an array of prefilled products information else  empty  array.
	 */
	public function get_prefilled_products( $cpb_id ) {
		global $wpdb;
		static $static_cpb_id = 0;
		static $products_list = array();

		if ( $static_cpb_id == $cpb_id ) {
			return $products_list;
		}

		$static_cpb_id = $cpb_id;

		$prefill_result = $wpdb->get_results( $wpdb->prepare( "SELECT id, unique_prod_id, prefill_product_id, prefill_quantity, prefill_mandatory, product_type FROM {$this->prefill_table} WHERE cpb_product_id = %d", $cpb_id ) ); // @codingStandardsIgnoreLine.

		if ( $prefill_result ) {
			$key = 0;
			foreach ( $prefill_result as $single_result ) {
				if ( get_post_status( $single_result->prefill_product_id ) != false ) {
					$products_list[ $key ]['unique_prod_id'] = $single_result->unique_prod_id;
					$products_list[ $key ]['product_id'] = $single_result->prefill_product_id;
					$products_list[ $key ]['product_qty'] = $single_result->prefill_quantity;
					$products_list[ $key ]['product_mandatory'] = $single_result->prefill_mandatory;
					$products_list[ $key ]['product_type'] = $single_result->product_type;
					$key++;
				} else {
					$wpdb->delete(
						$this->prefill_table,
						array(
							'cpb_product_id'        => $cpb_id,
							'prefill_product_id'    => $single_result->prefill_product_id,
							'unique_prod_id'        => $single_result->unique_prod_id,
						),
						array(
							'%d',
							'%d',
							'%s',
						)
					);
				}
			}
		}

		if ( ! empty( $products_list ) ) {
			$products_list = array_filter( $products_list );
		}

		return $products_list;
	}

	/**
	 * Retrives pre-filled product ids for a particular CPB product
	 *
	 * @param  int $cpb_id CPB product id.
	 * @return array $products_ids if CPB product contains pre-filled products
	 * then returns an array containing ids of prefilled products else  empty
	 * array.
	 */
	public function get_prefilled_product_ids( $cpb_id ) {
		global $wpdb;

		$products_ids = array();
		$prefill_result = $wpdb->get_results( $wpdb->prepare( "SELECT unique_prod_id, prefill_product_id FROM {$this->prefill_table} WHERE cpb_product_id = %d", $cpb_id ) ); // @codingStandardsIgnoreLine.

		if ( $prefill_result ) {
			foreach ( $prefill_result as $single_result => $value ) {
				unset( $single_result );
				if ( get_post_status( $value->prefill_product_id ) != false ) {
					$products_ids[] = $value->unique_prod_id;
				} else {
					$wpdb->delete(
						$this->prefill_table,
						array(
							'cpb_product_id'        => $cpb_id,
							'prefill_product_id'    => $value->prefill_product_id,
						),
						array(
							'%d',
							'%d',
						)
					);
				}
			}
		}

		if ( ! empty( $products_ids ) ) {
			$products_ids = array_filter( $products_ids );
		}

		return $products_ids;
	}

	/**
	 * Inserts pre-filled products data in DB
	 *
	 * @param int    $cpb_id  CPB Product id.
	 * @param array  $prefill_products Array of all pre-filled products id.
	 * @param array  $prefill_qty  Array of all pre-filled products quantity.
	 * @param array  $prefill_mandatory Array of all mandatory pre-filled
	 *  products.
	 * @param array  $bundle_data Data of addons includes with the box.
	 * @param  string $key position of value to be inserted.
	 * @param  string $value value to be inserted.
	 * @param  array  $insert_values Array of product ids to be inserted.
	 */
	public function insert_prefilled_products( $cpb_id, $prefill_products, $prefill_qty, $prefill_mandatory, $bundle_data, $key = '', $value = '', $insert_values = array() ) {
		global $wpdb;
		if ( empty( $insert_values ) ) {
			$prefill_products = array_unique( $prefill_products );
			foreach ( $prefill_products as $index => $unique_id ) {
				$prefill_id = 'variation' == $bundle_data[ $unique_id ]['product_type'] ? $bundle_data[ $unique_id ]['variation_id'] : $unique_id;
				if ( get_post_status( $prefill_id ) != false ) {
					$wpdb->insert(
						$this->prefill_table,
						array(
							'cpb_product_id'         => $cpb_id,
							'unique_prod_id'         => $unique_id,
							'prefill_product_id'     => $prefill_id,
							'prefill_quantity'       => $prefill_qty[ $index ],
							'prefill_mandatory'      => $prefill_mandatory[ $index ],
							'product_type'       => $bundle_data[ $unique_id ]['product_type'],
						),
						array(
							'%d',
							'%s',
							'%d',
							'%d',
							'%d',
							'%s',
						)
					);
				}
			}
		} else {
			$prefill_id = 'variation' == $bundle_data[ $value ]['product_type'] ? $bundle_data[ $value ]['variation_id'] : $value;
			if ( get_post_status( $prefill_id ) != false ) {
				$wpdb->insert(
					$this->prefill_table,
					array(
						'cpb_product_id'         => $cpb_id,
						'unique_prod_id'         => $value,
						'prefill_product_id'     => $prefill_id,
						'prefill_quantity'       => $prefill_qty[ $key ],
						'prefill_mandatory'      => $prefill_mandatory[ $key ],
						'product_type'       => $bundle_data[ $value ]['product_type'],
					),
					array(
						'%d',
						'%s',
						'%d',
						'%d',
						'%d',
						'%s',
					)
				);
			}
		}
	}


	/**
	 * Updates pre-filled products data in DB.
	 *
	 * @param  int    $cpb_id  CPB Product id.
	 * @param Array  $prefill_products  Array of all pre-filled products id.
	 * @param Array  $prefill_qty  Array of all pre-filled products quantity.
	 * @param Array  $prefill_mandatory Array of all mandatory pre-filled products.
	 * @param array  $bundle_data Data of addons includes with the box.
	 * @param  string $key position of value to be updated.
	 * @param  string $value  value to be updated.
	 * @param  array  $update_values Array of product ids to be updated.
	 */
	public function update_prefilled_products( $cpb_id, $prefill_products, $prefill_qty, $prefill_mandatory, $bundle_data, $key, $value, $update_values ) {
		global $wpdb;
		unset( $prefill_products );
		unset( $update_values );

		$prefill_id = 'variation' == $bundle_data[ $value ]['product_type'] ? $bundle_data[ $value ]['variation_id'] : $bundle_data[ $value ]['product_id'];
		if ( get_post_status( $prefill_id ) != false ) {
			$wpdb->update(
				$this->prefill_table,
				array(
					'prefill_quantity'  => $prefill_qty[ $key ],
					'prefill_mandatory' => $prefill_mandatory[ $key ],
				),
				array(
					'cpb_product_id'        => $cpb_id,
					'unique_prod_id'        => $value,
					'prefill_product_id'    => $prefill_id,
				),
				array(
					'%d',
					'%d',
				),
				array(
					'%d',
					'%s',
					'%d',
				)
			);
		} else { // If a product is deleted.
			$wpdb->delete(
				$this->prefill_table,
				array(
					'cpb_product_id'        => $cpb_id,
					'unique_prod_id'        => $value,
				),
				array(
					'%d',
					'%s',
				)
			);
		}
	}

	/**
	 * Deletes pre-filled products from DB which are present in DB but not in
	 * current selection.
	 * Also , delete the pre-filled products details if pre-fill option not
	 * checked
	 *
	 * @param  int   $cpb_id  CPB Product id.
	 * @param  array $deleted_values Array of product ids to be deleted which
	 * are present in DB but not in current selection.
	 */
	public function delete_prefilled_products( $cpb_id, $deleted_values = array() ) {
		global $wpdb;

		if ( empty( $deleted_values ) ) {
			$existing = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$this->prefill_table} WHERE cpb_product_id = %d", $cpb_id ) ); // @codingStandardsIgnoreLine.
			if ( ! empty( $existing ) ) {
				$existing = array_values( $existing );
				$delete_count = count( $existing );

				if ( $delete_count > 0 ) {
					$delete_placeholders = array_fill( 0, $delete_count, '%d' );

					$placeholders = implode( ',', $delete_placeholders );

					$delete_query = "DELETE FROM {$this->prefill_table} WHERE id IN ($placeholders)";
					$wpdb->query( $wpdb->prepare( $delete_query, $existing ) ); // @codingStandardsIgnoreLine.
				}
			}
		} else {
			$existing = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$this->prefill_table} WHERE cpb_product_id = %d and unique_prod_id IN ('" . implode( "', '", $deleted_values ) . "')", $cpb_id ) ); // @codingStandardsIgnoreLine.
			if ( ! empty( $existing ) ) {
				$existing = array_values( $existing );
				$delete_count = count( $existing );

				if ( $delete_count > 0 ) {
					$delete_placeholders = array_fill( 0, $delete_count, '%d' );

					$placeholders = implode( ',', $delete_placeholders );

					$delete_query = "DELETE FROM {$this->prefill_table} WHERE id IN ($placeholders)";

					$wpdb->query( $wpdb->prepare( $delete_query, $existing ) ); // @codingStandardsIgnoreLine.
				}
			}
		}
	}

	/**
	 * Processes records for insert, update or delete
	 * Get the saved pre-filled product ids from the database.
	 * If there are some existing values for pre-filled products.
	 * If the existing values are not present in the current selection delete
	 * them from the database.
	 * Update the current selected pre-filled products in the DB.
	 *
	 * @param  int   $cpb_id  CPB Product id.
	 * @param  Array $prefill_products Array of all pre-filled products ids.
	 * @param  Array $prefill_qty Array of all pre-filled products quantity.
	 * @param  Array $prefill_mandatory Array of all mandatory pre-filled
	 * products.
	 * @param  Array $bundle_data Data of addons includes with the box.
	 */
	public function save_prefilled_products( $cpb_id, $prefill_products, $prefill_qty, $prefill_mandatory, $bundle_data ) {
		$insert_values = array();
		$deleted_values = array();
		$update_values = array();
		// Get the saved pre-filled product ids form database.
		$existing = $this->get_prefilled_product_ids( $cpb_id );

		if ( ! empty( $existing ) ) {
			$insert_values = array_diff( $prefill_products, $existing );
			$deleted_values = array_diff( $existing, $prefill_products );
			$update_values = array_intersect( $existing, $prefill_products );

			if ( ! empty( $deleted_values ) ) {
				$this->delete_prefilled_products( $cpb_id, $deleted_values );
			}
			foreach ( $prefill_products as $key => $value ) {
				if ( ! empty( $insert_values ) && in_array( $value, $insert_values ) ) {
					$this->insert_prefilled_products( $cpb_id, $prefill_products, $prefill_qty, $prefill_mandatory, $bundle_data, $key, $value, $insert_values );
				}
				if ( ! empty( $update_values ) && in_array( $value, $update_values ) ) {
					$this->update_prefilled_products( $cpb_id, $prefill_products, $prefill_qty, $prefill_mandatory, $bundle_data, $key, $value, $update_values );
				}
			} // end foreach.
		} else {
			$this->insert_prefilled_products( $cpb_id, $prefill_products, $prefill_qty, $prefill_mandatory, $bundle_data );
		}
	}
}
$GLOBALS['prefill_manager'] = CPB_Prefill_Data_Manager::get_instance();
