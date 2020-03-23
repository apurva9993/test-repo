<?php
/**
* This class is responsible to display the gift-box for CPB in the single product
* page.
* It is responsible for the display of the blank boxes for the CPB.
* Also, to add the pre-filled products set in the settings of that CPB Product.
*/
class wdm_cpb_product_gift_box extends wdm_abstract_product_display {

	/**
	* Adds the action for displaying the gift box layout.
	* Adds the action for enqueuing the CPB front-end scripts.
	*/
	public function __construct() {
		parent::__construct();
		// Shows the grid for the Custom Product Box. And Price and Title of the custom product box.
		add_action( 'wdm_gift_layout', array( $this, 'cpbGiftLayout' ) );
		add_action( 'wdm_cpb_enqueue_scripts', array( $this, 'cpBEnqueueScripts' ) );
	}
	/**
	* Gets the array of the pre-filled products from the database saved by the
	* admin.
	* Get the box quantity from the _wdm_grid_field key of the post meta table.
	* To display the grid on front-end.
	* If the pre-filled products are not set in settings display the blank grid.
	* If there are pre-filled products ,display the pre-filled blocks and rest
	* blank boxes.
	*/
	public function cpbGiftLayout() {
		global $prefill_manager, $post, $product;
		// Display grid at front end
		$total_clm = $product->get_box_capacity();
		if ( ! $product->has_prefilled_products() ) {
			$this->displayBlankBlocks( $total_clm, 1 );
		} else {
			$prefillProducts = $prefill_manager->get_prefilled_products( $post->ID );
			$this->displayPrefilledBlocks( $total_clm, $prefillProducts );
		}
	}

	/**
	* Get the meta-key value for column field according to the selected layout.
	* Get the meta-value of the  column field from the meta table and attach to DOM * element class of bundle products.
	* @param int $total_clm the total box quantity(or the grid size)
	* @param int $clm initially 1 then count increase as per grid size.
	*/
	public function displayBlankBlocks( $total_clm, $clm ) {
		$columnClass = get_column_field();

		if ( ! empty( $total_clm ) ) {
			for ( ; $clm <= $total_clm; $clm++ ) {
				echo '<div id="wdm_bundle_bundle_item_' . $clm . '" class = "wdm-bundle-single-product ' . $columnClass . '">';
				echo '<div class = "wdm-bundle-box-product"></div>';
				echo '</div>';
			}
		}
	}

	/**
	* Display the pre-filled products in the boxes with the quantities specified .
	* And the rest of the boxes (upto max box quantity) as blank product boxes.
	* @param int $total_clm the total box quantity(or the grid size)
	* @param array $prefillProducts  an array of prefilled products information
	*/
	public function displayPrefilledBlocks( $total_clm, $prefillProducts ) {
		global $prefill_manager, $post;
		$clm = 1;
		$prefillProducts = $prefill_manager->get_prefilled_products( $post->ID );
		if ( ! empty( $prefillProducts ) && ! empty( $total_clm ) ) {
			foreach ( $prefillProducts as $singleProduct ) {
				$clm = $this->display_prefilled_block( $clm, $singleProduct );
			}
			if ( $clm <= $total_clm ) {
				$this->displayBlankBlocks( $total_clm, $clm );
			}
		}
	}

	/**
	* Check the stock status for the pre-filled product.
	* If the remove mandatory products if they are out of stock option is checked
	* in the settings.
	* Then display the pre-filled products if they are in stock otherwise display
	* the blank boxes for the bundle products.
	* @param int $clm current no. of pre-filled products.
	* @param array $singleProduct Pre-filled single product info.
	* @return int $clm current count of pre-filled products after they have been
	* displayed(if in stock) or not displayed(if not in stock)
	*/
	public function display_prefilled_block( $clm, $singleProduct ) {
		// check stock availability
		$stockStatus = $this->checkInventoryStatus( $singleProduct );
		if ( $stockStatus || ( $singleProduct['product_mandatory'] && $this->cpb_product->get_swap_prefilled() ) ) {
			$clm = $this->addPrefilledProduct( $singleProduct, $clm );
		}
		return $clm;
	}

	/**
	* Enqueue the scripts and styles for single products page.
	* Enqueued for various layouts of the CPB Product display on single product page
	*/
	public function cpBEnqueueScripts() {
		wp_enqueue_script( 'wdm-add-to-cart-bundle' );
		wp_enqueue_script( 'wdm-product-div-height-js' );
		wp_enqueue_style( 'wdm-bundle-css' );
		wp_enqueue_style( 'wdm-bundle-grid-css' );
		if ( $this->enableScroll() ) {
			wp_enqueue_script( 'wdm-scroll-lock-js' );
		}
	}
}
