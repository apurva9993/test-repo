<?php
/**
* This class is responsible for the display of the CPB Product layout.
* that comprises of, CPB Main product info, display of add-on-products
* available,gift-box with the pre-filled and other products, gift message field,
* price field and add-to-cart button.
* This class is also responsible to check the stock status of the mandatory and
* other add-on products and display the notices based on the conditions of stock
* availability they satisfy.
*/

class wdm_abstract_product_display {

	protected $priceMessageType = 'html';

	protected $cpb_product;

	/**
	* Add the action for CPB Template hooks for the Product page.
	*/
	public function __construct() {
		global $post;
		$product = CPB()->get_cpb_product( $post->ID );

		if ( $product->is_type( 'wdm_bundle_product' ) ) {
			$this->cpb_product = $product;
		}
		add_action( 'wdm_cpb_before_template_starts', array( $this, 'addTemplateHooksActions' ) );
	}

	/**
	* Remove the actions for the single add-on products display on the single
	* product page for CPB display.
	* This is done because actions for CPB Product display for add-ons were loaded
	* on the page previously for some reasons leading to the template disorder.
	* Next, Add the actions for the display of single add-on products on the CPB
	* product page according to the template suitable for CPB display.
	* 1: Action for removing the hooks for woocommerce single product page for
	* display of main CPB product.
	* 2: Action to check the stock availability of the mandatory pre-filled
	* products and the option to remove them if run out of stock is not checked
	* then, add the notice for the same and make add-to-cart disable.
	* 3: Action to display the CPB main product info, on single product page
	* suitable to CPB Template.
	* 4: Action to display the add-on products available for display.
	* 5: Actions to display templates for display of add-on products image,
	* quantity,and title.
	* 6: Action to display the price fields and the add-to-cart button for the gift
	* box.
	* 7: Action to display the gift message box field if it is enabled in the
	* settings of that CPB Product.
	*/
	public function addTemplateHooksActions() {
		global $post;

		if ( is_singular( 'product' ) && $this->cpb_product->get_type() == 'wdm_bundle_product' ) {
			remove_all_actions( 'wdm_cpb_remove_wc_product_display_hooks' );
			remove_all_actions( 'wdm_cpb_main_product_info' );
			remove_all_actions( 'wdm_cpb_add_to_cart_form' );
			remove_all_actions( 'wdm_add_on_product_image' );
			remove_all_actions( 'wdm_add_on_product_title' );
			remove_all_actions( 'wdm_add_on_product_quantity' );
			remove_all_actions( 'wdm_cpb_add_to_cart_button' );
			remove_all_actions( 'wdm_cpb_before_bundle_pricing_box' );

			add_action( 'wdm_cpb_remove_wc_product_display_hooks', array( $this, 'removeHooks' ) );
			add_action( 'wdm_cpb_main_product_info', array( $this, 'addOutOfStockNotice' ) );
			add_action( 'wdm_cpb_main_product_info', array( $this, 'displayMainProductInfo' ) );
			add_action( 'wdm_cpb_add_to_cart_form', array( $this, 'displayAddToCartForm' ), 10, 1 );
			add_action( 'wdm_add_on_product_image', array( $this, 'displayAddOnProductImage' ), 10, 2 );
			add_action( 'wdm_add_on_product_title', array( $this, 'displayAddOnProductTitle' ), 10, 2 );
			add_action( 'wdm_add_on_product_description', array( $this, 'displayAddOnProductDescription' ), 10, 2 );
			add_action( 'wdm_add_on_product_quantity', array( $this, 'displayAddOnProductQuantity' ), 10, 2 );
			add_action( 'wdm_cpb_add_to_cart_button', array( $this, 'displayCpbProductAddToCart' ), 10, 1 );
			add_action( 'wdm_cpb_before_bundle_pricing_box', array( $this, 'displayGiftMessage' ) );
		}
	}

	/**
	* Apply filters for the mobile layout responsiveness breakpoint.
	*/
	public function setMobileLayoutBreakpoint() {
		return apply_filters(
			'wdm_cpb_mobile_layout_breakpoint',
			'only screen and (max-width: 760px),
(min-device-width: 768px) and (max-device-width: 1024px)'
		);
	}

	/**
	* Returns the path for selected template.
	* @param string $templateType type for selected template.
	* @return string $templateType path for selected template.
	*/
	public function __get( $templateType ) {
		switch ( $templateType ) {
			case 'add_on_product_display_template':
				return $this->add_on_product_display_template       = 'single-product/wdm-add-on-product-display.php';
				break;
			case 'add_on_product_title_template':
				return $this->add_on_product_title_template         = 'single-product/wdm-bundled-item-title.php';
				break;
			case 'add_on_product_description_template':
				return $this->add_on_product_description_template   = 'single-product/wdm-bundled-item-description.php';
				break;
			case 'add_on_product_quantity_template':
				return $this->add_on_product_quantity_template      = 'single-product/wdm-bundle-item-quantity.php';
				break;
			case 'add_on_product_image_template':
				return $this->add_on_product_image_template         = 'single-product/wdm-bundled-item-image.php';
				break;
			case 'single_product_add_to_cart_template':
				$this->single_product_add_to_cart_template   = 'single-product/add-to-cart/wdm-add-to-cart-button.php';
				return $this->single_product_add_to_cart_template;
				break;
			case 'single_product_gift_message_template':
				return $this->single_product_gift_message_template  = 'single-product/add-to-cart/wdm-gift-message.php';
				break;
		}
	}

	/**
	* Get the setting of order products by date.
	* If the order by date setting is checked in settings, get the sorted array of * bundle(add-on) product objects , else get the array of bundle product object
	* as they are added.
	* If the products are in stock display the products.
	* Or if they are not in stock and the hide if out of stock setting by
	* woocommerce is not checked then also display the add-on products.
	*/
	public function displayAddToCartForm( $product ) {
		global $addon_list;
		$addon_list = get_addon_including_variation_product( $product->get_addon_items_list() );
		if ( $product->get_sort_by_date() ) {
			$addon_products = get_sorted_list_of_bundled_items( $addon_list );
		}

		if ( ! is_array( $addon_products ) ) {
			return;
		}

		foreach ( $addon_products as $addon_id => $addon_data ) {
			$single_addon_id = empty( $addon_data['variation_id'] ) ? $addon_id : $addon_data['variation_id'];

			$this->display_single_product( $single_addon_id, $addon_data, $addon_id );
		}
	}

	/**
	* Display the messages related to the stock for the product.
	* Includes the template to display single add-on product on single CPB Product Page.
	* @param object $addon_product single add-on product object to be displayed.
	*/
	public function display_single_product( $single_addon_id, $addon_data, $addon_id ) {
		global $addon_product;
		$addon_product = wc_get_product( $single_addon_id );

		$wc_outofstock = get_option( 'woocommerce_hide_out_of_stock_items' );

		if ( get_post_status( $addon_product->get_id() ) !== 'publish' || ! $addon_product->is_purchasable() ) {
			return;
		}

		if ( $addon_product->is_in_stock() || ( 'no' == $wc_outofstock && ! $addon_product->is_in_stock() ) ) {
			wc_get_template(
				$this->add_on_product_display_template,
				array(
					'addon_data' => $addon_data,
					'addon_id' => $addon_id,
				),
				'',
				CPB_ABSPATH . 'legacy-layout/templates/'
			);
		}
	}

	/**
	* Display the single product add-on image.
	* Check if the product is sold individually if yes add appropriate class.
	* @param int $bundled_item_id single add-on product id.
	*/
	public function displayAddOnProductImage( $addon_data, $addon_product ) {
		// $product_id = $addon_data['product_type'] == 'variation' ? $addon_data['variation_id'] : $item_data['product_id'];
		$sld_ind = get_post_meta( $addon_product->get_id(), '_sold_individually', true );

		if ( $sld_ind == 'yes' ) {
			wc_get_template(
				$this->add_on_product_image_template,
				array(
					'post_id' => $addon_product->get_id(),
					'sld_ind' => $sld_ind,
					'addon_product' => $addon_product,
				),
				'',
				CPB_ABSPATH . 'legacy-layout/templates/'
			);
		} else {
			wc_get_template(
				$this->add_on_product_image_template,
				array(
					'post_id' => $addon_product->get_id(),
					'product' => $addon_product,
				),
				'',
				CPB_ABSPATH . 'legacy-layout/templates/'
			);
		}
	}

	/**
	* Display the title of single add-on product in the CPB.
	* @param string $bundled_item_title single product add-on title.
	* @param object $bundle_item single product add-on object.
	*/
	public function displayAddOnProductTitle( $addon_data, $addon_product ) {
		// if (is_singular('product')) {
		wc_get_template(
			$this->add_on_product_title_template,
			array(
				'title' => $addon_data['text_name'],
				'alt'   => $addon_data['text_name'],
				'href'  => get_permalink( $addon_product->get_id() ),
			),
			'',
			CPB_ABSPATH . 'legacy-layout/templates/'
		);
	}

	/**
	* Displays description of the add-on product
	* @param object $item_data product add--on object
	*/
	public function displayAddOnProductDescription( $item_data ) {
		wc_get_template(
			$this->add_on_product_description_template,
			array(
				'addon_id'    => $item_data['product_id'],
				'description' => get_post( $item_data['product_id'] )->post_content,
			),
			'',
			CPB_ABSPATH . 'legacy-layout/templates/'
		);
	}

	/**
	* Display the quantity field if product is in stock.
	* @param object $addOnItem single add-on product object.
	*/
	public function displayAddOnProductQuantity( $addon_data, $addon_id ) {
		global $prefill_manager, $post;
		static $prefilledProducts = false;

		if ( $prefilledProducts === false ) {
			$prefilledProductsData = $prefill_manager->get_prefilled_products( $post->ID );
			if ( $prefilledProductsData ) {
				foreach ( $prefilledProductsData as $singlePrefillProduct ) {
					$stockStatus = $this->checkInventoryStatus( $singlePrefillProduct );
					if ( ! $stockStatus ) {
						continue;
					}

					$prefilledProducts[ $singlePrefillProduct['product_id'] ] = array(
						'product_qty' => $singlePrefillProduct['product_qty'],
						'product_mandatory' => $singlePrefillProduct['product_mandatory'],
					);
				}
			} else {
				$prefilledProducts = array();
			}
		}

		wc_get_template(
			$this->add_on_product_quantity_template,
			array(
				'addon_data' => $addon_data,
				'prefilledProducts'  => $prefilledProducts,
				'addon_id' => $addon_id,
			),
			'',
			CPB_ABSPATH . 'legacy-layout/templates/'
		);
	}

	/**
	* If some mandatory products are out of stock and they cannot be swapped
	* Disable the add-to-cart button if the above condition becomes true.
	* Display the add-to-cart  and price fields
	*/
	public function displayCpbProductAddToCart( $product ) {
		global $post;
		$disabled = '';
		if ( $this->canBeSwapped() ) {
			$disabled = 'disabled';
		}

		wc_get_template(
			$this->single_product_add_to_cart_template,
			array(
				'post_id'               => $post->ID,
				'product'               => $product,
				'disabled'              => $disabled,
			),
			'',
			CPB_ABSPATH . 'legacy-layout/templates/'
		);
	}

	/**
	* Remove the woocommerce hooks for the single product page for display of CPB
	* Product.
	*/
	public function removeHooks() {
		//removed actions and added later to add single product display at product image
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
		//removed filter only for this product to remove product image
		remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		remove_action( 'woocommerce_before_single_product_summary', array( $this, 'woocommerce_show_product_sale_flash' ), 10 );
	}

	/**
	* Display the main CPB Product Info.
	* Except the price which are changed based on CPB Settings.
	*/
	public function displayMainProductInfo() {
		woocommerce_template_single_title();
		woocommerce_template_single_rating();
		woocommerce_template_single_excerpt();
		woocommerce_template_single_add_to_cart();
		woocommerce_template_single_meta();
		woocommerce_template_single_sharing();
		// woocommerce_template_single_price();
		// add_filter('woocommerce_get_price_html', array($this, 'cpbMainProductPrice'), 10, 2);
		add_action( 'wdm_product_price_html', array( $this, 'cpbTemplateSinglePrice' ), 10, 1 );
		add_action( 'wdm_product_base_price_html', array( $this, 'cpbTemplateSinglePrice' ), 10, 1 );
		add_action( 'wdm_product_signup_fee_html', array( $this, 'cpbTemplateSignupFee' ), 10, 1 );
		add_action( 'wdm_product_grand_total', array( $this, 'cpbTemplateGrandTotal' ), 10, 1 );
	}


	public function cpbMainProductPrice( $price, $product ) {
		if ( $product->is_type( 'wdm_bundle_product' ) && $product->is_product_cpb_subscription() ) {
			$signupFee = \WC_Subscriptions_Product::get_sign_up_fee( $product );
			$reg_price = wc_get_price_to_display( $product, array( 'price' => $product->get_regular_price() ) );

			return $signupFee + $reg_price;
		}
		return $price;
	}

	/*
	 * Function to display CPB product 'Base' price html
	 */

	public function cpbTemplateSinglePrice( $product ) {
		if ( $product->is_product_cpb_subscription() ) {
			wc_get_template(
				'wdm-base-price-template.php',
				array(),
				'custom-product-boxes/templates/',
				CPB_ABSPATH . 'legacy-layout/templates/'
			);
			return;
		}
		woocommerce_template_single_price();
	}

	/*
	 * Function to display CPB(mobile layout) product price html
	 */

	public function cpbMobileTemplateSinglePrice( $product ) {
		if ( $product->is_product_cpb_subscription() ) {
			wc_get_template(
				'wdm-mobile-total-template.php',
				array(),
				'custom-product-boxes/legacy-layout/templates/product-layouts/mobile-layouts/list/',
				CPB_ABSPATH . 'legacy-layout/templates/product-layouts/mobile-layouts/list/'
			);
			return;
		}

		woocommerce_template_single_price();
	}


	public function cpbTemplateSignupFee( $product ) {
		if ( $product->is_product_cpb_subscription() ) {
			wc_get_template(
				'wdm-signup-fee-template.php',
				array(),
				'custom-product-boxes/templates/',
				CPB_ABSPATH . 'legacy-layout/templates/'
			);
			return;
		}

		woocommerce_template_single_price();
	}

	public function cpbTemplateGrandTotal( $product ) {
		if ( $product->is_product_cpb_subscription() ) {
			wc_get_template(
				'wdm-grand-total-template.php',
				array(),
				'custom-product-boxes/templates/',
				CPB_ABSPATH . 'legacy-layout/templates/'
			);
			return;
		}

		woocommerce_template_single_price();
	}

	/**
	* If the pre-filled products are allowed then return true else false.
	* @return bool true if pre-filled boxes allowed else false.
	*/
	protected function allowPrefillProducts() {
		$allowPrefillProducts = get_post_meta( get_the_ID(), 'cpb_enable_prefilled', true );
		if ( $allowPrefillProducts == 'yes' ) {
			return true;
		}
		return false;
	}
	/**
	* Add the notice when the manadatory pre-filled product is out of stock.
	* Allow the removal of mandatory products if out of stock is not checked in the * settings of the CPB Product.
	*/
	public function addOutOfStockNotice() {
		if ( $productId = $this->canBeSwapped() ) {
			wc_print_notice( sprintf( __( 'This Custom Box cannot be purchased as mandatory product #%1$d - %2$s has run out of stock.', 'custom-product-boxes' ), $productId, get_the_title( $productId ) ), 'error' );
		}
	}

	public function getShortTitle( $title ) {
		if ( strlen( $title ) > 20 ) {
			mb_internal_encoding( 'UTF-8' );
			$title = mb_substr( $title, 0, 20 ) . '...';  // change 50 to the number of characters you want to show
		}
		return $title;
	}

	/**
	* Check if the product is in stock or not if not return false else true.
	* @param array $singleProduct pre-filled Single bundle product details.
	* @return boolean true if product in stock else false.
	*/
	protected function checkInventoryStatus( $singleProduct ) {
		$status = true;
		if ( $singleProduct['product_type'] == 'variation' ) {
			$prefillProduct = new \WC_Product_Variation( $singleProduct['product_id'] );
		} else {
			$prefillProduct = new \WC_Product( $singleProduct['product_id'] );
		}

		if ( $prefillProduct->is_sold_individually() && $singleProduct['product_qty'] > 1 ) {
			$status = false;
		}

		if ( ! $prefillProduct->is_purchasable() ) {
			$status = false;
		}

		if ( ! $prefillProduct->is_in_stock() ) {
			$status = false;
		}

		if ( ! $prefillProduct->has_enough_stock( $singleProduct['product_qty'] ) ) {
			$status = false;
		}

		return $status;
	}

	/**
	* Get the messages to be displayed on front-end as per the stock status,
	* sold-individually or allow backorders conditions.
	* @param object $singleProduct single add-on product in bundle.
	* @return array $availabilityText product availability status.(Filter on the
	* same)
	*/
	public function getStockMessage( $singleProduct ) {
		// $wdm_manage_stock = get_option('woocommerce_manage_stock');
		$availabilityText = array();
		if ( ! $singleProduct->is_in_stock() ) {
			$message = __( 'Out of stock', 'custom-product-boxes' );
			$availabilityText = $this->setAvailabilityInfo(
				$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message, array( 'stock_warning', 'wdm_stock' ) ) : $message,
				'',
				'wdm-no-stock'
			);
		} elseif ( $singleProduct->is_in_stock() && $singleProduct->is_sold_individually() ) {
			$message = __( 'Only 1 allowed per order', 'custom-product-boxes' );
			if ( $singleProduct->is_on_backorder( 1 ) && $singleProduct->backorders_require_notification() ) {
				$message .= __( ' (Available on backorder)', 'custom-product-boxes' );
			} elseif ( $singleProduct->get_stock_quantity() == 0 ) {
				$message .= __( ' (Available on backorder)', 'custom-product-boxes' );
				$availabilityText = $this->setAvailabilityInfo(
					$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message,
					'allow_notify'
				);
				return apply_filters( 'wdm_get_availability_text', $availabilityText, $singleProduct );
			}

			$availabilityText = $this->setAvailabilityInfo(
				$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message,
				''
			);
		} elseif ( $singleProduct->managing_stock() && $singleProduct->is_on_backorder( 1 ) ) {
			$message = $singleProduct->backorders_require_notification() ? __( 'Available on backorder', 'custom-product-boxes' ) : __( 'In stock', 'custom-product-boxes' );
			$availabilityText = $this->setAvailabilityInfo(
				$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message
			);
		} elseif ( $singleProduct->managing_stock() ) {
			$availabilityText = $this->stockFormat( $singleProduct );
		} else {
			$message = __( 'In stock', 'custom-product-boxes' );
			$availabilityText = $this->setAvailabilityInfo(
				$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message,
				''
			);
		}

		return apply_filters( 'wdm_get_availability_text', $availabilityText, $singleProduct );
	}

	/**
	* Display of the messages on the single add-on products based on the stock
	* format.
	* Gets the stock format of the woocommerce.
	* Based on the stock format, get the stock availability and the back-orders
	* settings for that product.
	* Based on those settings,decide the message to be displayed for the product on * the front-end (i.e, the single product page of CPB Product.)
	* @param object $singleProduct single add-on product object
	* @return array $availabilityText availability text to be displayed for that
	*  single add-on product.
	*/
	protected function stockFormat( $singleProduct ) {
		switch ( get_option( 'woocommerce_stock_format' ) ) {
			case 'no_amount':
				$backorderClass = '';
				$message = __( 'In stock', 'custom-product-boxes' );
				if ( $singleProduct->backorders_allowed() && ! $singleProduct->backorders_require_notification() ) {
					$backorderClass = 'allow_notify';
				}
				$availabilityText = $this->setAvailabilityInfo(
					$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message,
					$backorderClass
				);
				break;
			case 'low_amount':
				$backorderClass = '';
				$message = __( 'In stock', 'custom-product-boxes' );
				if ( $singleProduct->get_stock_quantity() <= get_option( 'woocommerce_notify_low_stock_amount' ) ) {
					$message = sprintf( __( 'Only %s left in stock', 'custom-product-boxes' ), $singleProduct->get_stock_quantity() );

					if ( $singleProduct->backorders_allowed() && $singleProduct->backorders_require_notification() ) {
						$backorderClass = 'allow_notify';
						$message .= __( ' (also available on backorder)', 'custom-product-boxes' );
					} elseif ( $singleProduct->backorders_allowed() && ! $singleProduct->backorders_require_notification() ) {
						$backorderClass = 'allow_notify';
					}
				}
				$availabilityText = $this->setAvailabilityInfo(
					$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message,
					$backorderClass
				);
				break;
			default:
				$backorderClass = '';
				$message = sprintf( __( '%s in stock', 'custom-product-boxes' ), $singleProduct->get_stock_quantity() );

				if ( $singleProduct->backorders_allowed() && $singleProduct->backorders_require_notification() ) {
					$backorderClass = 'allow_notify';
					$message .= __( ' (also available on backorder)', 'custom-product-boxes' );
				} elseif ( $singleProduct->backorders_allowed() && ! $singleProduct->backorders_require_notification() ) {
					$backorderClass = 'allow_notify';
				}
				$availabilityText = $this->setAvailabilityInfo(
					$this->priceMessageType == 'html' ? $this->getHtmlPriceMessage( $message ) : $message,
					$backorderClass
				);
				break;
		}
		return $availabilityText;
	}
	/**
	* Return the DOM element of error message to be displayed on front-end.
	* @param string $message message to be displayed.
	* @param array $classes classes to be attached to the DOM element of error
	* message.
	* @return string $message error message HTML string.
	*/
	protected function getHtmlPriceMessage( $message, $classes = array( 'wdm_stock', 'stock' ) ) {
		if ( is_array( $classes ) && ! empty( $classes ) ) {
			$classes = implode( ' ', $classes );
			$message = "<p class='{$classes}'>$message</p>";
		}
		return $message;
	}

	/**
	* Return the array of product availability.
	* @param string $priceMessage HTML string for the message on front-end.
	* @param string $backorderClass if backorder attach the class.
	* @param string $stockStatus stock status class.
	* @return array array of all three parameters attached
	*/
	protected function setAvailabilityInfo( $priceMessage, $backorderClass = 'allow_notify', $stockStatus = '' ) {
		return array(
			'wdm_no_stock'      => $stockStatus,
			'backorderClass'    => $backorderClass,
			'price_message'     => $priceMessage,
		);
	}

	/**
	* Get the enable scroll lock is set in the settings of that CPB.
	* Get the data from the post meta table.
	* If scroll lock enabled return true else return false.
	* @return boolean If scroll lock enabled return true else return false.
	*/
	protected function enableScroll() {
		$enable_scroll = get_option( 'cpb_disable_scroll' );
		if ( $enable_scroll == 'yes' ) {
			return true;
		}
		return false;
	}
	/**
	* Allows user to remove mandatory products only if they run out of stock
	* If the above option is set in settings return true else false.
	* @param boolean true if swapping enabled else false.
	*/
	public function enableSwapping() {
		return $this->cpb_product->get_swap_prefilled();
	}

	/**
	* Allows user to remove mandatory products only if they run out of stock
	* If the above option is set in settings return true else false.
	* Product-id is returned when cannot be swapped and product out of stock.
	* Else return false.
	* @return mixed product-id or boolean false
	*/
	public function canBeSwapped() {
		global $post, $prefill_manager;
		$oosFlag = false;
		$productId = '';
		$prefillProducts = $prefill_manager->get_prefilled_products( $post->ID );

		foreach ( $prefillProducts as $singleProduct ) {
			// $stckSts = '';
			if ( ! empty( $singleProduct['product_mandatory'] ) && ! $this->checkInventoryStatus( $singleProduct ) ) {
				$oosFlag = true;
				$productId = $singleProduct['product_id'];
				break;
			}
		}

		if ( ! $this->enableSwapping() && $oosFlag ) {
			return $productId;
		}
		return false;
	}

	/**
	* If the product for pre-fill is in stock display the single pre-filled product
	* And if it is not in stock do not display the product in the bundle.
	* It is because we can remove mandatory pre-filled products when they are out
	* of stock.
	* @param  array $singleProduct Pre-filled single product info.
	* @param int $position current no. of pre-filled products.
	* @return int $position current no. of pre-filled products.
	*/
	protected function addPrefilledProduct( $singleProduct, $position ) {

		$mainProductId = get_the_ID();

		for ( $pre = 1; $pre <= $singleProduct['product_qty']; $pre++ ) {
			$classes = array();
			// Mandatory Removable Product (Because it is out of Stock)
			if ( ! empty( $singleProduct['product_mandatory'] ) && ! $this->checkInventoryStatus( $singleProduct ) ) {
				continue;
				//$classes = array('wdm-prefill-out-stock');
			} elseif ( ! empty( $singleProduct['product_mandatory'] ) ) { // Mandatory Prefilled Product
				$classes = array( 'wdm-prefill-mandatory' );
			}
			$this->displaySinglePrefilledProduct( $singleProduct, $mainProductId, $position, $classes );
			$position++;
		}
		return $position;
	}

	/**
	* Get the product image for the add-on or pre-filled products.
	* If there is no image set for the product then place a thumbnail for that
	* product.
	* @param int $productId Product Id
	* @return string $image The post thumbnail image tag.
	*/
	protected function getProductImage( $productId ) {

		$image = get_the_post_thumbnail(
			$productId,
			apply_filters( 'bundled_product_large_thumbnail_size', 'shop_thumbnail' ),
			array(
				'title'    => get_the_title( get_post_thumbnail_id( $productId ) ),
			)
		);
		if ( ! isset( $image ) || empty( $image ) ) {
			$image = '<img width="180" height="180" src="' . wc_placeholder_img_src() . '" class="attachment-shop_thumbnail size-shop_thumbnail wp-post-image" alt="poster_5_up" title="poster_5_up" sizes="(max-width: 180px) 100vw, 180px">';
		}

		return $image;
	}

	/**
	* Get the pre-filled products details from the DB.
	* Display the pre-filled product on front-end.
	* @param int $prefillProductId Pre-filled single product Id.
	* @param int $mainProductId main CPB Product Id.
	* @param int $position current no. of pre-filled products.
	* @param array $classes array of the classes for pre-filled products display.
	*/
	protected function displaySinglePrefilledProduct( $prefillProduct, $mainProductId, $position, $classes = array() ) {
		$prefillProductId = $prefillProduct['product_id'];
		if ( $prefillProduct['product_type'] == 'variation' ) {
			$preProduct = new \WC_Product_Variation( $prefillProductId );
		} else {
			$preProduct = new \WC_Product( $prefillProductId );
		}

		$prePrice = wc_get_price_to_display( $preProduct );

		if ( is_array( $classes ) ) {
			$classes = implode( ' ', $classes );
		}

		$classes .= " wdm-prefill-product wdm_box_item wdm_added_image_{$position} wdm_filled_product_{$prefillProductId}";
		$columnData = get_column_field();
		?>
		<div id = "wdm_bundle_bundle_item_<?php echo $position; ?>"
			 class = "wdm-product-added wdm-bundle-single-product <?php echo $columnData; ?>"
		>
			<div class = "wdm-bundle-box-product">
				<div    class = "<?php echo $classes; ?>"
						data-bundled-item-id = "<?php echo $prefillProduct['unique_prod_id']; ?>"
						data-bundle-id = "<?php echo $mainProductId; ?>"
						data-product-price = "<?php echo $prePrice; ?>"
				>
					<div class="cpb-plus-minus">
<!--                         <div class="cpb-circle">
							<div class="cpb-horizontal"></div>
							<div class="cpb-slantline"></div>
						</div> -->
						  <div class='cpb-card cpb-card-overlay cpb-remove-product'></div>
						<div class='cpb-card cpb-card-overlay cpb-block-product'></div>
					</div>
					<?php echo $this->getProductImage( $prefillProductId ); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	* Add the gift message field before the price fields and add-to-cart button,
	* If it is enabled in the settings for the CPB Product.
	*/
	public function displayGiftMessage() {
		$enableGiftMessage = get_post_meta( $this->cpb_product->get_id(), '_wdm_enable_gift_message', true );

		if ( $enableGiftMessage != 'yes' ) {
			return;
		}
		// Gift Message field
		$this->addGiftMesssagefield();
	}

	/**
	* Includes template of the gift message field.
	*/
	public function addGiftMesssagefield() {
		wc_get_template(
			$this->single_product_gift_message_template,
			array(
				'product' => $this->cpb_product,
			),
			'',
			CPB_ABSPATH . 'legacy-layout/templates/'
		);
	}
}
