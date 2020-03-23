<?php
/**
 * CPB Admin
 *
 * @class    CPB_Admin
 * @package  CPB/Admin
 * @version  4.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * This class handles the product search for addon product
 */
class CPB_Admin_Search_Addons {
	/**
	 * Constructir for CPB_Admin_Search_Addons class.
	 */
	public function __construct() {
		if ( is_admin() ) {
			/*
			* Ajax for the Search bar for products
			*/
			add_action( 'wp_ajax_cpb_json_search_products_and_variations', array( $this, 'cpb_search_products_and_variations' ) );
			add_action( 'wp_ajax_cpb_get_variable_product_variations', array( $this, 'cpb_get_variable_product_variations' ) );
		}
	}

	/**
	 * This function is used to Search the Products in the Search bar.
	 */
	public function cpb_search_products_and_variations() {
		// We won't write at the end of this because json_search_products will automatically do that for us.
		self::json_search_products( '', array( 'product', 'product_variation' ) );
	}

	/**
	 * Search for products and echo json.
	 * It is very similar to WooCommerce's WC_AJAX::json_search_products. But it supports WPML too.
	 * Gets the products which are published having title similar to the searched term and they must not be previously included in the same Add-on list.
	 *
	 * @param string $term       (default: '').
	 * @param string $post_types (default: array('product')).
	 */
	public static function json_search_products( $term = '', $post_types = array( 'product' ) ) {
		global $wpdb;

		ob_start();

		check_ajax_referer( 'search-products', 'security' );

		$term = self::get_term( $term );

		$like_term = '%' . $wpdb->esc_like( $term ) . '%';

		$query = $wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
			WHERE posts.post_status = 'publish'
			AND (
				posts.post_title LIKE %s
				or posts.post_content LIKE %s
				OR (
					postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
				)
			)",
			$like_term,
			$like_term,
			$like_term
		);

		$query .= " AND posts.post_type IN ('" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "')";

		$query .= self::get_query_part();

		$posts = array_unique( $wpdb->get_col( $query ) ); // @codingStandardsIgnoreLine.

		$excluded_products = self::get_excluded_products();

		$found_products = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$product = wc_get_product( $post );

				if ( ! current_user_can( 'read_product', $post ) ) {
					continue;
				}
				if ( empty( $product ) ) {
					continue;
				}
				$product_type = $product->get_type();

				switch ( $product_type ) {
					case 'simple':
						$found_products = self::get_simple_product( $post, $product, $found_products, $excluded_products );
						break;
					case 'variable':
						$found_products = self::get_variable_product( $post, $product, $found_products, $excluded_products );
						break;
					default:
						break;
				}
			}
		}
		$found_products = apply_filters( 'wdm_cpb_json_search_found_products', $found_products );
		wp_send_json( $found_products );
	}


	/**
	 * This function gets the simple product details.
	 *
	 * @param array $post post having the title similar to searched term.
	 * @param array $product above posts having post-type product.
	 * @param array $found_products empty at first.
	 * @param array $excluded_products products included previously.
	 * @return array $found_products details of simple product having the similar title as searched .
	 */
	public static function get_simple_product( $post, $product, $found_products, $excluded_products = array() ) {
		if ( ! in_array( md5( $post ), $excluded_products ) ) {
			$max_val = 0;
			$bundle_item_quantity = get_post_meta( $post, '_stock', true );
			if ( ! empty( $bundle_item_quantity ) ) {
				$max_val = $bundle_item_quantity;
			}

			$found_products[ $post ] = array(
				'product_type' => $product->get_type(),
				'text_name' => rawurldecode( $product->get_formatted_name() ),
			);
		}
		return $found_products;
	}

	/**
	 * This function gets the variable product details.
	 *
	 * @param array $post post having the title similar to searched term.
	 * @param array $product above posts having post-type product.
	 * @param array $found_products empty at first.
	 * @param array $excluded_products products included previously.
	 * @return array $found_products details of simple product having the similar title as searched .
	 */
	public static function get_variable_product( $post, $product, $found_products, $excluded_products = array() ) {
		if ( ! in_array( md5( $post ), $excluded_products ) ) {
			$max_val = 0;
			$bundle_item_quantity = get_post_meta( $post, '_stock', true );
			if ( ! empty( $bundle_item_quantity ) ) {
				$max_val = $bundle_item_quantity;
			}

			$sku = $product->get_sku();
			$text_name = ! empty( $sku ) ? $product->get_title() . ' (' . $sku . ')' : $product->get_title() . ' (#' . $post . ')';
			$excluded_products = array( md5( $post ) );
			$found_products[ $post ] = array(
				'product_type' => $product->get_type(),
				'text_name' => $text_name,
			);
		}
		return $found_products;
	}

	/**
	 * This function is to fetch the products with specific variations in the search bar.
	 * If variations are not empty fetch the variable product corresponding to variation for search bar.
	 *
	 * @param array $post post having the title similar to searched term.
	 * @param array $product above posts having post-type product.
	 * @return array $found_products details of variation product having the similar title as searched .
	 */
	public static function get_variation_product( $post, $product ) {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			add_filter( 'woocommerce_get_product_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
			$attributes_array = $product->get_variation_attributes();
			remove_filter( 'woocommerce_get_product_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
		} else {
			add_filter( 'woocommerce_product_get_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
			$attributes_array = $product->get_attributes();
			remove_filter( 'woocommerce_product_get_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
		}

		// $variation_data = $product->get_variation_attributes();
		$variation_data = wc_get_product_variation_attributes( $post );

		$skip_variation = self::is_variation_skippable( $variation_data );

		if ( ! empty( $skip_variation ) ) {
			$product->variation_data = $variation_data;
			$found_products = self::add_variation_in_search_results( $product, $post, $variation_data );
		}
		return $found_products;
	}

	/**
	 * This function is to return the variation those are empty.
	 *
	 * @param array $variation_data Variation data for variable products.
	 * @return boolean $skip_variation true if variation is empty.
	 */
	public static function is_variation_skippable( $variation_data ) {
		$skip_variation = false;
		foreach ( $variation_data as $variation ) {
			if ( empty( $variation ) ) {
				$skip_variation = true;
				break;
			}
		}

		return $skip_variation;
	}


	/**
	 * Get the term similar to Product in search bar.
	 *
	 * @param string $term term in search bar.
	 */
	public static function get_term( $term ) {
		if ( empty( $term ) && isset( $_GET['term'] ) ) {
			$term = sanitize_text_field( wp_unslash( $_GET['term'] ) );
		} else {
			$term = wc_clean( $term );
		}

		if ( empty( $term ) ) {
			die();
		}

		return $term;
	}

	/**
	 * This function is used to fetch the details of variable product with corresponding variation.
	 *
	 * @param object $variation_product variations product details.
	 * @param int    $variation_id Id of variation to be fetched.
	 * @param array  $variation_data the variation combination of attributes.
	 * @param array  $excluded_products product variation which are already included.
	 * @return array $found_products variable product details of corresponding variation.
	 */
	public static function add_variation_in_search_results( $variation_product, $variation_id, $variation_data, $excluded_products ) {
		$found_products = array();
		if ( ! is_array( $variation_data ) ) {
			return false;
		}

		// Return Products if value of  any variation attribute is empty.
		foreach ( $variation_data as $variation ) {
			if ( empty( $variation ) ) {
				return false;
			}
		}

		// Creating md5 hash to check whether current combinations of attributes already exists in the array or not.
		$variation_hash = $variation_id . '_' . implode( '_', $variation_data );

		if ( in_array( $variation_hash, $excluded_products ) ) {
			return $found_products;
		}

		// Check if this variation already exists in the array.
		if ( ! isset( $found_products[ $variation_hash ] ) ) {
			$text_name = ! empty( $sku ) ? self::get_variation_name( $variation_product ) . ' (' . $sku . ')' : self::get_variation_name( $variation_product ) . ' (#' . $variation_id . ')';
			$found_products[ $variation_hash ] = array(
				'variation_id' => $variation_id,
				'text_name' => $text_name,
				'selected'  => 'no',
				'product_type' => 'variation',
			);
		}

		return $found_products;
	}

	/**
	 * Gets the Image URL of Product.
	 *
	 * @param int $post_id Post id of product.
	 */
	public static function get_img_url( $post_id ) {
		$img_url = wp_get_attachment_url( get_post_thumbnail_id( $post_id ) );

		if ( ! $img_url || '' == $img_url ) {
			$img_url = WC()->plugin_url() . '/assets/images/placeholder.png';
		}

		return $img_url;
	}

	/**
	 * Returns the Variations of Product in string format.
	 *
	 * @param array $variation_data Variation data of Product.
	 * @param int   $variation_id Variation Id.
	 */
	public static function get_variations( $variation_data, $variation_id ) {
		if ( isset( $variation_data ) && '' != $variation_data ) {
			$variation_data = maybe_unserialize( $variation_data );
			$variable_product = wc_get_product( $variation_id );
			$product_attributes = $variable_product->get_attributes();

			return get_cpb_variation_string( $variation_data, $variable_product, $product_attributes );
		}

		return '';
	}

	/**
	 * Get the Name of variation Product.
	 *
	 * @param  object $variation_product variation product object.
	 */
	public static function get_variation_name( $variation_product ) {

		if ( version_compare( WC_VERSION, '3.0.0', '>=' ) ) {
			return rawurldecode( self::generate_variation_name( $variation_product ) );
		}

		return rawurldecode( $variation_product->get_formatted_name() );
	}

	/**
	 * Generates Variation Title for WC greater than 3.0
	 *
	 * This is the copy of WooCommerce's WC_Product_Variation_Data_Store_CPT::generate_product_title(). Because
	 * direct call to this method is not possible as it is a protected method, we are creating a copy of it in plugin
	 *
	 * @param  object $product variation product object.
	 * @return string          title of variation
	 */
	public static function generate_variation_name( $product ) {
		$inc_attrs = false;
		$attributes = (array) $product->get_attributes();

		// Determine whether to include attribute names through counting the number of one-word attribute values.
		$one_word_attributes = 0;
		foreach ( $attributes as $name => $value ) {
			if ( false === strpos( $value, '-' ) ) {
				++$one_word_attributes;
			}
			if ( $one_word_attributes > 1 ) {
				$inc_attrs = true;
				break;
			}
			unset( $name );
		}

		$inc_attrs = apply_filters( 'woocommerce_product_variation_title_include_attribute_names', $inc_attrs, $product );
		$title_base_text         = get_post_field( 'post_title', $product->get_parent_id() );
		$title_attrs_text   = wc_get_formatted_variation( $product, true, $inc_attrs );
		$separator               = ! empty( $title_attrs_text ) ? ' &ndash; ' : '';

		return apply_filters(
			'woocommerce_product_variation_title',
			$title_base_text . $separator . $title_attrs_text,
			$product,
			$title_base_text,
			$title_attrs_text
		);
	}

	/**
	 * Get Parent product Id for variation id
	 *
	 * @param object $variation_product Variation Id.
	 * @return int $product_id  Parent product Id
	 */
	public static function get_parent_id( $variation_product ) {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			$product_id = $variation_product->parent->id;
		} else {
			$product_id = $variation_product->get_parent_id();
		}
		return $product_id;
	}

	/**
	 * This function is to fetch all variations of the variable Product in the search result.
	 * It first gets all the attributes of the variable product which is similar to the product which is searched.
	 * Then the variations are fetched according to the attributes combinations.
	 * It is then checked that if variation with similar attribute combination is present in the variable product list or not.
	 * If yes the variable product details for that variation is fetched and put in the search bar.
	 *
	 * @param int   $post product id of product.
	 * @param array $product product details.
	 * @param array $excluded_products products which are included previously.
	 * @return array $found_products products found with the required variations.
	 */
	public static function add_all_variations_in_search_results( $post, $product, $excluded_products = array() ) {
		$post = $post;

		/*
		 * Below we will be calling get_variation_attributes function which makes a call to get_attributes.
		 * get_variation_attributes uses name of attribute as a key in the returned array. We need to change the
		 * behavior and we need to have a 'slug' as a key in the array returned by get_variation_attributes.
		 * Therefore, we'll change the array returned by get_attributes. We'll keep name as slug in
		 * get_attributes array
		 */
		$data_to_return = array();
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			add_filter( 'woocommerce_get_product_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
			$attributes_array = $product->get_variation_attributes();
			remove_filter( 'woocommerce_get_product_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
		} else {
			add_filter( 'woocommerce_product_get_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
			$attributes_array = $product->get_attributes();
			remove_filter( 'woocommerce_product_get_attributes', 'cpb_modify_product_attribute_names', 99, 1 );
		}

		if ( empty( $attributes_array ) ) {
			return false;
		}
		$set_attribute_name = function ( $value ) {
			return 'attribute_' . $value;
		};

		// Sets attribute_ prefix to all keys of an array.
		$attributes_array = array_combine(
			array_map( $set_attribute_name, array_keys( $attributes_array ) ),
			$attributes_array
		);

		$var_combinations = self::get_array_combinations( $attributes_array );
		if ( $var_combinations ) {
			foreach ( $var_combinations as $single_variation ) {
				if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
					$variation_id = $product->get_matching_variation( $single_variation );
				} else {
					$data_store   = \WC_Data_Store::load( 'product-variable' );

					$variation_id = $data_store->find_matching_product_variation( $product, $single_variation );
				}
				if ( $variation_id ) {
					$variation_product = wc_get_product( $variation_id );
					if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
						$variation_product->variation_data = $single_variation;
					} else {
						// Setting Variation Attributes in WC 3.0 and greater.
						$variation_data = array();
						foreach ( $single_variation as $key => $value ) {
							// Remove attribute prefix which meta gets stored with.
							if ( 0 === strpos( $key, 'attribute_' ) ) {
								$key = substr( $key, 10 );
							}
							$variation_data[ $key ] = $value;
						}
						$variation_product->set_props( array( 'attributes' => $variation_data ) );
					}

					$found_products = self::add_variation_in_search_results( $variation_product, $variation_id, $single_variation, $excluded_products );
					$data_to_return = array_merge( $data_to_return, $found_products );
				}
			}
		}
		return $data_to_return;
	}

	/**
	 * This function gets the conditional query for product selection .
	 * If there are any products in exclude i.e, products already included in Add-on list do not fetch them again.
	 * If there is any limit specified for the no. of products , include the limit in query.
	 *
	 * @return string $query conditional query.
	 */
	public static function get_query_part() {
		$query = '';
		if ( ! empty( $_GET['exclude'] ) ) {
			$query .= ' AND posts.ID NOT IN (' . implode( ',', array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['exclude'] ) ) ) ) ) . ')';
		}

		if ( ! empty( $_GET['limit'] ) ) {
			$query .= ' LIMIT ' . intval( $_GET['limit'] );
		}

		return $query;
	}

	/**
	 * This function gets the variations with the combinations of the attributes.
	 *
	 * @param array $arrays array of the attributes.
	 * @return array $result Variation combinations of all the attributes.
	 */
	public static function get_array_combinations( $arrays ) {
		$result = array( array() );
		foreach ( $arrays as $property => $property_values ) {
			if ( ! is_array( $property_values ) ) {
				   continue;
			}
			$tmp = array();
			foreach ( $result as $result_item ) {
				foreach ( $property_values as $property_value ) {
					$tmp[] = array_merge( $result_item, array( $property => $property_value ) );
				}
			}
			$result = $tmp;
		}

		return $result;
	}

	/**
	 * This function is used to get the excluded products i.e, the products which are already included previously in the Add-on list.
	 *
	 * @return array $excluded_products products previously included.
	 */
	public static function get_excluded_products() {
		$excluded_products = array();

		if ( ! empty( $_GET['exclude'] ) ) {
			$excluded_products = array_filter( array_unique( explode( ',', sanitize_text_field( wp_unslash( $_GET['exclude'] ) ) ) ) );
		}
		return $excluded_products;
	}

	/**
	 * Gets variations for the accordion expanded. Ajax call back.
	 *
	 * @return void
	 */
	public function cpb_get_variable_product_variations() {
		global $product_var_data;
		check_ajax_referer( 'search-products', '_nonce' );
		$product_id = filter_input( INPUT_POST, 'product_id', FILTER_VALIDATE_INT );
		$selected_variations = isset( $_POST['selected_variations'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_variations'] ) ) : '[]';
		$selected_variations = json_decode( $selected_variations, true );

		$selected_variations = array_map( 'add_selected_tag', $selected_variations );
		$selected_variations = array_map( 'add_product_type', $selected_variations );

		if ( ! empty( $product_id ) ) {
			$product = wc_get_product( $product_id );
			$remaining_variations = self::add_all_variations_in_search_results( $product_id, $product, array_keys( $selected_variations ) );
			$product_var_data = array_merge( $selected_variations, $remaining_variations );
			include_once dirname( __FILE__ ) . '/meta-boxes/views/cpb-html-variable-variation-list.php';
		}
		die();
	}
}

new CPB_Admin_Search_Addons();
