export default class cpbNewLayout{

	constructor() {
		this.cpb_calculations = {};
		this.cpbOnLoad();
	}

	getBundlePrice() {
		return this.cpb_calculations.total_bundle_price;
	}

	getBoxTotal() {
		return this.cpb_calculations.box_total;
	}

	cpbOnLoad() {
		var self = this;
	    self.cpb_calculations.cpbProductData = {
	        productId: wdm_bundle_params.cpb_product_id,
	        boxQuantity: parseInt( wdm_bundle_params.box_quantity ),
	        isDynamicPricingEnabled: wdm_bundle_params.dynamic_pricing_enable,
	        pricing_type: wdm_bundle_params.pricing_type,
	        productPrice: wdm_bundle_params.product_price,
	        enableProductsSwap: wdm_bundle_params.enableProductsSwap,
	    };
		self.cpb_calculations.box_total = 0;
		self.cpb_calculations.addonsProductData = {};
		self.cpb_calculations.addons_stock = {};
		self.cpb_calculations.cpbProductData.numberOfProductsInBox = 0;

		jQuery( document ).ready(function(){
	    	self.cpb_calculations.addonsProductData = JSON.parse( jQuery( 'form.cpb_form' ).attr('data-addon_list') );
	    	self.cpb_calculations.cpbProductData.numberOfProductsInBox = parseInt((jQuery('.cpb-product-box-wrap .cpb-empty-box-inner.filled').length <= 0) ? 0 : jQuery('.cpb-product-box-wrap .cpb-empty-box-inner.filled').length);
		});

		self.cpb_calculations.total_bundle_price = parseInt( self.cpb_calculations.cpbProductData.productPrice );
		if (self.cpb_calculations.cpbProductData.pricing_type == 'cpb-dynamic-nobase' ) {
			self.cpb_calculations.total_bundle_price = 0;
		}
		self.displayBoxTotalPrice();
		self.displayBundleTotalPrice();
	}

	setAddonsData( addon_id  ) {
		var index, self = this;
		self.cpb_calculations.addons = self.cpb_calculations.addonsProductData;
	}

	displayBoxTotalPrice() {
		var self = this;
		jQuery( document ).ready(function(){
			jQuery('div.cpb-cart-wrap .cpb-box-total-val div.price').find( ".amount" ).html( wdm_get_price_format( self.getBoxTotal() ) );
		});
	}

	expandCollapseToggle(){
		jQuery(document).on('click', '.cpb-show', function(){ // Expand the box
			jQuery(this).parent().addClass('cpb-expanded');
			jQuery('.cpb-product-box-wrap').addClass('cpb-expanded');
			var height = jQuery('.cpb-product-box-wrap')[0].scrollHeight;
			jQuery('.cpb-product-box-wrap').css('height', height+'px');
		})
		jQuery(document).on('click', '.cpb-hide', function(){ // Collapse the box
			jQuery(this).parent().removeClass('cpb-expanded');
			jQuery('.cpb-product-box-wrap').removeClass('cpb-expanded');
			jQuery('.cpb-product-box-wrap').css('height', '');
		})
	}

	equalHeight(){
		jQuery(window).load(function(){
			setEqualHeight(jQuery('.cpb-products-wrap .cpb-product-info'));
		})

		jQuery(window).resize(function(){
			setEqualHeight(jQuery('.cpb-products-wrap .cpb-product-info'));
		})
	}

	fillProgressBar(){ // Fill progress and adds count above progress bar
		var total_boxes = jQuery('.cpb-product-box-wrap .cpb-empty-box-inner').length;
		var filled = jQuery('.cpb-product-box-wrap .cpb-empty-box-inner.filled').length
		jQuery('.cpb-box-count').css('left', 'calc('+(filled/total_boxes)*100+'% - 25px)');
  		jQuery('.cpb-filled-part').css('width', (filled/total_boxes)*100+'%');
  		jQuery('.cpb-filled-count b').text(filled);
	}

	showAccessibilityOnHover(){
		jQuery(document).ready(function(){
			jQuery('.cpb-product-box-wrap, .cpb-accessibility').hover(function(){
				jQuery('.cpb-accessibility').css('display', 'flex');
			},
			function(){
				jQuery('.cpb-accessibility').css('display', 'none');
			}); 
		}); 
	}

	getAddonStock() {
		return this.availableInBox( this.cpb_calculations.addonsProductData, addon_id ) ? parseInt( this.cpb_calculations.addonsProductData[ addon_id ]['stock_quantity'] ) : 0;
	}

	availableInBox( arrayObject, arrayIndex ) {
		return ! jQuery.isEmptyObject( arrayObject ) && isset( arrayObject[ arrayIndex ] );
	}

	getAddonPrice( addon_id ) {
		return this.availableInBox( this.cpb_calculations.addonsProductData, addon_id ) ? parseInt( this.cpb_calculations.addonsProductData[ addon_id ]['wc_price'] ) : 0;
	}

	addQunatity(current, count) {
		var addon_id = jQuery(current).parents('.cpb-product-inner').attr('data-id');
		this.format_cpb_addon_quantities( addon_id, count );

	}

	removeQuantity(current, count) {
		var addon_id = jQuery(current).parents('.cpb-product-inner').attr('data-id');
		this.remove_quantity_from_list( addon_id, count );
	}

	addCount(current, total_boxes){ //Add count to top right corner
		var addedCount = this.getAddedCount();
		var current_count = parseInt(jQuery(current).parents('.cpb-product-inner').attr('data-count'));
		if(addedCount < total_boxes){
			jQuery(current).parents('.cpb-product-inner').attr('data-count', current_count+1);
			var count = jQuery(current).parents('.cpb-product-inner').attr('data-count')
			if(count == 0){
				jQuery(current).parents('.cpb-product-inner').find('.cpb-count').hide();
			}
			else{
				jQuery(current).parents('.cpb-product-inner').find('.cpb-count').show();
			}
			jQuery(current).parents('.cpb-product-inner').find('.cpb-count').text(count);
			this.addQunatity(current, count);
		}
		else{
			var msg_box_height = jQuery('.cpb-product-box-wrap').outerHeight();
			jQuery('.box-full-msg').css('height', msg_box_height);
			jQuery('.box-full-msg').css('transform', 'scale(1)');
			jQuery('.box-full-msg')[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
			setTimeout(function(){
				jQuery('.box-full-msg').css('transform', 'scale(0)');
			}, 2000)
		}
	}

	substractCount(current){ // substract count to top right corner
		var minus = jQuery(current).parents('.cpb-product-inner').attr('data-id');
		var total = parseInt(jQuery('.cpb-products-wrap .cpb-product-inner[data-id='+minus+']').attr('data-count'));
		jQuery('.cpb-products-wrap .cpb-product-inner[data-id='+minus+']').attr('data-count', total-1);
		var count = jQuery('.cpb-products-wrap .cpb-product-inner[data-id='+minus+']').attr('data-count');
		if(count == 0){
			jQuery('.cpb-products-wrap .cpb-product-inner[data-id='+minus+']').find('.cpb-count').hide();
		}
		else{
			jQuery('.cpb-products-wrap .cpb-product-inner[data-id='+minus+']').find('.cpb-count').show();
		}
		jQuery('.cpb-products-wrap .cpb-product-inner[data-id='+minus+']').find('.cpb-count').text(count);
		this.removeQuantity(current, count);
	}

	getAddedCount(){ // Returns total products added
		var total_items = 0;
		jQuery( ".cpb-products-wrap .cpb-product-inner" ).each(function( index ) {
		  total_items = total_items + parseInt(jQuery( this ).attr('data-count') );
		});
		return total_items;
	}

	addProductInBox(){
		var self = this;
		jQuery(document).on('click', '.cpb-products-wrap .cpb-img-overlay', function(){ // Adds product in empty box
			var total_boxes = jQuery('.cpb-product-box-wrap .cpb-empty-box-inner').length;
			var first = jQuery(jQuery('.cpb-empty-box-inner').not('.filled')[0]);
			jQuery(first).addClass('filled').append(jQuery(this).parents('.cpb-product-inner').clone()).find('.cpb-stock-status').remove();
			jQuery(first).find('.cpb-img-overlay span img').text('-');
			if(jQuery(first)[0] != undefined){
	  			jQuery(first)[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
			}

			//Update Total Price
			if (self.cpb_calculations.cpbProductData.isDynamicPricingEnabled) {
				self.addTotalPrice( jQuery(this).parents('.cpb-product-inner').attr( 'data-id' ) );
			}

	  		self.fillProgressBar();
	  		self.addCount(this, total_boxes);
		});
	}

	addTotalPrice( addon_id ) {
		this.addBoxTotal( addon_id );
		this.addBundleTotal( addon_id );
		this.displayBundleTotalPrice( addon_id );
		this.displayBoxTotalPrice( addon_id );
	}

	addBundleTotal( addon_id ) {
		this.cpb_calculations.total_bundle_price = this.cpb_calculations.total_bundle_price + this.getAddonPrice( addon_id );
	}

	addBoxTotal( addon_id ) {
		this.cpb_calculations.box_total = this.cpb_calculations.box_total + this.getAddonPrice( addon_id );
	}

	substractBundleTotal( addon_id ) {
		this.cpb_calculations.total_bundle_price = this.cpb_calculations.total_bundle_price - this.getAddonPrice( addon_id );
	}

	substractBoxTotal( addon_id ) {
		this.cpb_calculations.box_total = this.cpb_calculations.box_total - this.getAddonPrice( addon_id );
	}

	substractTotalPrice( addon_id ) {
		this.substractBoxTotal( addon_id );
		this.substractBundleTotal( addon_id );
		this.displayBundleTotalPrice( addon_id );
		this.displayBoxTotalPrice( addon_id );
	}

	displayBundleTotalPrice() {
		var self = this;
		var bundle_price = wdm_get_price_format(self.getBundlePrice());
		console.log(bundle_price);
		jQuery(document).ready(function(){
			jQuery('.cpb-calculated-price').html( bundle_price );
			jQuery('div.cpb-cart-wrap .cpb-grand-total-val div.price').find( ".amount" ).html( bundle_price );
		});
	}

	removeProductFromBox(){
		var self = this;
		jQuery(document).on('click', '.cpb-product-box-wrap .cpb-img-overlay', function(){ //Removes product from box
			if (jQuery(this).parents('.wdm-prefill-mandatory').length) {
				return;
			}
			if(jQuery(this).parents('.cpb-empty-box').prev()[0] != undefined){
				jQuery(this).parents('.cpb-empty-box').prev()[0].scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
			}
			jQuery(this).parents('.cpb-empty-box-inner').removeClass('filled');
			jQuery(this).parents('.cpb-product-inner').remove();

			//Update Total Price
			if (self.cpb_calculations.cpbProductData.isDynamicPricingEnabled) {
				self.substractTotalPrice( jQuery(this).parents('.cpb-product-inner').attr( 'data-id' ) );
			}

			self.fillProgressBar();
			self.substractCount(this);
		})
	}

	remove_quantity_from_list( addon_id, count ) {
		var $add_on_quantities = this.get_addons_quantities();
        if ( $add_on_quantities.hasOwnProperty( addon_id ) && count == 0 ) {
            delete $add_on_quantities[ addon_id ];
        } else {
        	this.format_cpb_addon_quantities( addon_id, count );
        }

        this.update_qunatity_list( $add_on_quantities );
    }

	/**
	 * Get addons formated quantities
	 */
	get_addons_quantities() {
		var $quantities_formated = {};
		if ( jQuery( '#addon_quantities_list' ).val() !== undefined && jQuery( '#addon_quantities_list' ).val() != "" ) {
			$quantities_formated = JSON.parse(jQuery( '#addon_quantities_list' ).val());
		}
		return $quantities_formated;
	}

	/**
	 * This function returns a array to be posted and added in the cart
	 * 
	 */
	format_cpb_addon_quantities( addon_id, count ) {
		var $add_on_quantities = this.get_addons_quantities();
		$add_on_quantities[ addon_id.toString() ] = count;
		this.update_qunatity_list( $add_on_quantities );
	}

	update_qunatity_list( qunatity_list ) {
		if ( get_length( qunatity_list ) > 0 ) {
			jQuery( '#addon_quantities_list' ).val( JSON.stringify( qunatity_list ) );
		}
		if (get_length( qunatity_list ) == 0) {
			jQuery( '#addon_quantities_list' ).val( "" );
		}
	}

	hideScrollIndicator(){
		jQuery(document).ready(function(){
			jQuery(".cpb-product-box-wrap, .cpb-products-wrap").scroll(function(){ // Hide scroll Indicator on scroll
		  		jQuery(".scroll-indicator").hide();
			});
		});
	}

	addNoStockClass( addon_id ) {

	}

	resetBox(){
		var self = this;
		jQuery(document).on('click', '.cpb-refresh', function(){ // Resets the box
			jQuery('.cpb-empty-box .filled .cpb-product-inner').remove();
			jQuery('.cpb-empty-box-inner').removeClass('filled');
			self.fillProgressBar();
			jQuery('.cpb-count').hide();
			jQuery('.cpb-product-inner').attr('data-count', '0');
		});
	}

	loadPrefilledProducts() {
		var self = this;

		jQuery(document).ready(function(){
			var total_boxes = jQuery('.cpb-product-box-wrap .cpb-empty-box-inner').length;
			jQuery('.cpb-product-box-wrap .cpb-product-inner.wdm-prefill-product').each(function(index, value) {
				var addon_id = jQuery(value).attr('data-id');
				var addon_element = jQuery('#cpb-product-' + addon_id).find('.cpb-img-overlay');
		  		self.addCount(addon_element, total_boxes);
			});
	  		self.fillProgressBar();	
		})
	}

	ajaxAddToCart() {
	    jQuery(document).on('click', '.single_add_to_cart_button', function (e) {
	    	var post_id = jQuery('body').attr('class').match(/\d+/);
			post_id = post_id[0];
			if (jQuery('#product-'+post_id).hasClass('product-type-wdm_bundle_product')) {
		        e.preventDefault();

		        var $thisbutton = jQuery(this),
		                $form = $thisbutton.closest('form.cart'),
		                id = $thisbutton.val(),
		                cpb_product_qty = $form.find('input[name=quantity]').val() || 1,
		                cpb_product_id = $form.find('input[name=product_id]').val() || id,
		                cpb_addon_ids = $form.find('input[name=addon_quantities_list]').val() || {};
		                cpb_addon_ids = cpb_addon_ids;
		        var data = {
		            action: 'woocommerce_ajax_add_to_cart',
		            cpb_product_id: cpb_product_id,
		            product_sku: '',
		            cpb_product_qty: cpb_product_qty,
		            cpb_addon_ids: cpb_addon_ids,
		        };
				$thisbutton.removeClass( 'added' );

		        jQuery(document.body).trigger('adding_to_cart', [$thisbutton, data]);

		        jQuery.ajax({
		            type: 'post',
		            url: wc_add_to_cart_params.ajax_url,
		            data: data,
		            beforeSend: function (response) {
		            	remove_previous_notices();
		                $thisbutton.removeClass('added').addClass('loading');
		                $thisbutton.block({
			                message: null,
			                overlayCSS: {
			                    background: '#fff',
			                    opacity: 0.6
			                }
			            });
		            },
		            complete: function (response) {
		                $thisbutton.addClass('added').removeClass('loading');
		                $thisbutton.unblock();
		            },
		            success: function (response) {

		                if (response.error && response.error_msg) {
		                    // window.location = response.product_url;
		                    add_error_notice(response.error_msg, 'cpb_error');
		                    jQuery("html, body").animate({
				                 scrollTop: 0
				            }, "slow");
		                    return;
		                } 
						if ( response.error && response.product_url ) {
							window.location = response.product_url;
							return;
						} else {
		                    jQuery(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisbutton]);
		                    $thisbutton.unblock();
		                }
		            },
		        });
		    	return false;
		    }//end of cpb product type
	    });
	}

}