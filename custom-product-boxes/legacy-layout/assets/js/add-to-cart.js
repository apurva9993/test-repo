jQuery( document ).ready( function () {
    gravityFormMobileCompatibility();
    //Unbind the on click handlers for the single products in the bundle.
    jQuery( '.zoom' ).unbind( 'click' );
    jQuery( 'div.wdm-mobile-list-cpb-layout div.mobile-list-layout-cpb-product-add-to-cart div.bundle_wrap div.wdm_bundle_price p.price' ).prepend('Total: ');

    //When the gift-box quantities (gift message or the CPB box quantity) is changed
    jQuery('div.gift-message-box div.cart.bundle_form input.input-text.qty.text.cpb_main_qty').change(function(){
        jQuery(this).css({"border-color": ""});
        if (jQuery(this).val() <= 0) {
            jQuery(this).val("");
            jQuery(this).css({"border-color": "#E35152"});
            return;
        } else if (jQuery(this).val() == "" || jQuery(this).val() > 0) {
            jQuery(this).css({"border-color": ""});
        }
    });

    // Main Product's 'Add to Cart' button is clicked
    jQuery( 'form' ).delegate( '.bundle_add_to_cart_button', 'click', function ( e ) {
        if (jQuery(this).hasClass('os_pf_m')) {
            jQuery("html, body").animate({
                scrollTop: 0
            }, "slow");
            return false;
        }
        //If the box quantity is less than 0 then alert error message.
        if (jQuery('.bundle_button .qty').val() <= 0 ) {
            // alert("Please select a value that is no less than 1.");
            alert('Quantity must be greater than or equal to 1');
            return false;
        }
        //Enable gift message is checked , get the gift message value from font-end
        if (wdm_add_to_cart.enableGiftMessage == 'yes') {
            var msgData = jQuery('.cpb_gift_message').val();
            var product_id = jQuery('.cpb_gift_message').attr('data-product-id');


            // Ajax Request to add gift message to the cart.
            jQuery.ajax({
                url: wdm_add_to_cart.ajax_url,
                type: "POST",
                data: {
                        action:'wdm_add_gift_message_session',
                        msgData: msgData, //send request data
                        product_id: product_id,
                      },
                async : false,
                success: function(data){
                    return true;
                },
            });
        }

        var max_div_id = jQuery( '.wdm-bundle-single-product:last' ).attr( 'id' ).split( '_' );
        max_div_id = max_div_id[4];
        //For each add-on bundle products.
        //Check the quantity of the bundle products added multiplied with the main quantity product.
        //if the quantity so calculated is more than available stock of that product.
        // Alert message for the same.
        jQuery( '.bundled_product_summary' ).each( function () {
            var curr_product_id = jQuery( this ).find( '.cart' ).attr( 'data-bundled-item-id' );
            // alert(curr_product_id);
            var curr_product_max_quantity = jQuery( 'input[name^=quantity_' + curr_product_id + ']' ).attr( 'max' );
            var data_allow_backorder = jQuery( 'input[name^=quantity_' + curr_product_id + ']' ).attr('data-allow-backorder');

            var total_bundle_quantity = jQuery( '.bundle_button' ).find( '.buttons_added input:nth-child(2)' ).val();
            var added_product_quantity = 0;
            for ( var k = 1; k <= max_div_id; k++ ) {
                var added_product_id = jQuery( '.wdm_added_image_' + k ).attr( 'data-bundled-item-id' );
                if ( curr_product_id == added_product_id ) {
                    added_product_quantity++;
                }
            }
            if ( curr_product_max_quantity != '' && curr_product_max_quantity != 0 && (curr_product_max_quantity <= 0 && data_allow_backorder != 'yes') ) {
                if ( (added_product_quantity * total_bundle_quantity > curr_product_max_quantity)) {
                    alert(wdm_add_to_cart.quantity_text);
                    e.preventDefault();
                    return false;
                }
            }
        } );

        var wdm_box_empty_count = 0;
        var wdm_box_filled_count = 0;
        for ( var i = 1; i <= max_div_id; i++ ) {
            if ( jQuery( '#wdm_bundle_bundle_item_' + i + ' .wdm-bundle-box-product' ).html() == '' ) {
                wdm_box_empty_count++;
            }
            else{
                wdm_box_filled_count++;
            }
        }
        validate = wdm_add_to_cart.check_bundle_validation;

        if ( (wdm_box_empty_count == 0 && (validate == 'no' || validate == '')) || (wdm_box_filled_count >= 1 && validate == 'yes' )) {
            gravityFormAddToCart();
            return true;
        }
        else {
            alert( wdm_add_to_cart.fill_box_text );
            e.preventDefault();
            return false;
        }
    } );

    /**
    * Gives the compatibility of the mobile display for the gravity forms plugin.
    */
    function gravityFormMobileCompatibility()
    {
        if (screen.width <= 768) {
            jQuery('div.gform_variation_wrapper.gform_wrapper').clone(true).prependTo('.product-type-wdm_bundle_product div.wdm-mobile-list-cpb-layout div.mobile-list-layout-cpb-product-add-to-cart div.cart.bundle_form');
            jQuery('.product-type-wdm_bundle_product div.wdm_product_bundle_container_form form#contactTrigger div.cart.bundle_form div.gform_variation_wrapper.gform_wrapper').remove();
        }
    }
    /**
    * Gives the compatibility of the add-to-cart display for the gravity forms plugin.
    */
    function gravityFormAddToCart()
    {
        if (screen.width <= 768) {
            jQuery('div.gform_variation_wrapper.gform_wrapper').clone(true).prependTo('.product-type-wdm_bundle_product div.wdm_product_bundle_container_form form#contactTrigger div.cart.bundle_form');
            jQuery('.product-type-wdm_bundle_product div.wdm-mobile-list-cpb-layout div.mobile-list-layout-cpb-product-add-to-cart div.cart.bundle_form div.gform_variation_wrapper.gform_wrapper').remove();
        }
    }

} );
