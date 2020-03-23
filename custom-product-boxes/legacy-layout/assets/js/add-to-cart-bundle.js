jQuery( document ).ready( function ( $ ) {
    var bundle_stock_status = [ ];
    var sld_ind = {};
    var qty_max = {};
    var max_parent = {};
    start_price = parseFloat(jQuery('.wdm-bundle-bundle-box').data('bundle-price'));  //base price
    var total_bundle_price = 0;
    start_total_bundle_price = wdm_get_price_format( total_bundle_price );

    //Change the price of the gift box to static bundle price initially.
    jQuery('div.gift-message-box div.cart.bundle_form .wdm-bundle-total p.price').find( ".amount" ).html( start_total_bundle_price );
    //When per-product pricing is enabled
    if (wdm_bundle_params.dynamic_pricing_enable == "yes") {
        //Hide the regular price for CPB product at top of the page.
        jQuery('div.wdm_product_info p.price del').hide();
        //Hide the gift box total, additionl box charges and the grand total of the CPB Product.
        jQuery('div.gift-message-box div.cart.bundle_form .wdm-grand-total p.price del').hide();
        jQuery('div.gift-message-box div.cart.bundle_form .wdm-bundle-total p.price del').hide();
        jQuery('div.gift-message-box div.cart.bundle_form .wdm-box-price p.price del').hide();
    } else {
        //Hide the bundle total price and the grand total price initially.
        //Also hide the regular price of the CPB product at top of the page.
        jQuery('div.wdm_product_info p.price del').hide();
        jQuery('div.gift-message-box div.cart.bundle_form .wdm-grand-total p.price del').hide();
        jQuery('div.gift-message-box div.cart.bundle_form .wdm-bundle-total p.price del').hide();
    }

    function setMaxParent( item_id, item_quantity_max)
    {
        if ( max_parent[ item_id ] == undefined) {
            max_parent[ item_id ] = item_quantity_max;
        }
    }

    function getMaxParent( item_id )
    {
        if ( max_parent[ item_id ] != undefined) {
            return max_parent[ item_id ];
        }
    }

    function removeNoStockClass(item_id)
    {
        var $parent_id = 0;
        $self = jQuery( "input[name^=quantity_" + item_id + "]" ).closest( ".bundled_product_summary" );
        $parent_id = $self.find( ".buttons_added" ).attr('data-parent_id');

        if ($self.find( ".buttons_added" ).hasClass('parent_stock')) {
            jQuery( '.images' ).each(function(){
                if ($parent_id != 0 && $parent_id == jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).attr('data-parent_id') && jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).hasClass('parent_stock')) {
                    var $item_id = jQuery( this ).closest( ".bundled_product_summary" ).find( ".cart" ).attr( 'data-bundled-item-id' );
                    jQuery( "input[name^=quantity_" + $item_id + "]" ).closest( ".bundled_product_summary" ).removeClass('wdm-no-stock');
                    jQuery('.mobile_bundled_product_'+$item_id+' .wdm-cpb-addon-qty-plus').attr('disabled', false);
                }
            });
        }

        if (!$self.find( ".buttons_added" ).hasClass('parent_stock')) {
            jQuery( "input[name^=quantity_" + item_id + "]" ).closest( ".bundled_product_summary" ).removeClass('wdm-no-stock');
        }
    }

    function addNoStockClass($self, $this, item_quantity_current, item_quantity_max, item_id)
    {
        var $parent_id = 0;
        if ($self.find( ".buttons_added" ).hasClass('parent_stock')) {
            $parent_id = $self.find( ".buttons_added" ).attr('data-parent_id');
            item_quantity_max = getMaxParent($parent_id);

            var current_qty = 0;
            jQuery( '.images' ).each(function(){
                if ($parent_id == jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).attr('data-parent_id') && jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).hasClass('parent_stock')) {
                    current_qty += parseInt(jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).find( ".input-text" ).val());
                }
            });

            item_quantity_current = current_qty;
        }

        
        if ($self.find( ".buttons_added" ).hasClass('parent_stock')) {
            if ( item_quantity_max != "") {
                if ( item_quantity_current == ( parseInt( item_quantity_max ) - 1 )  && (!$( $this ).closest( ".bundled_product_summary" ).hasClass('allow_notify') )) {
                    jQuery( '.images' ).each(function(){
                        if ($parent_id != 0 && $parent_id == jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).attr('data-parent_id') && jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).hasClass('parent_stock')) {
                            var item_id = jQuery( this ).closest( ".bundled_product_summary" ).find( ".cart" ).attr( 'data-bundled-item-id' );
                            $( this ).closest( ".bundled_product_summary" ).addClass( "wdm-no-stock" );
                            jQuery('.mobile_bundled_product_'+item_id+' .wdm-cpb-addon-qty-plus').attr('disabled', true);
                        }
                    });
                }
            }
        }

        if ( item_quantity_max != "") {
            if ( item_quantity_current == ( parseInt( item_quantity_max ) - 1 )  && (!$( $this ).closest( ".bundled_product_summary" ).hasClass('allow_notify') )) {
                $( $this ).closest( ".bundled_product_summary" ).addClass( "wdm-no-stock" );
            }
        }
    }

    function checkStockQty($self, $this, item_quantity_current, item_quantity_max)
    {
        if ($self.find( ".buttons_added" ).hasClass('parent_stock')) {
            $parent_id = $self.find( ".buttons_added" ).attr('data-parent_id');
            item_quantity_max = getMaxParent($parent_id);

            var current_qty = 0;
            jQuery( '.images' ).each(function(){
                if ($parent_id == jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).attr('data-parent_id') && jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).hasClass('parent_stock')) {
                    current_qty += parseInt(jQuery( this ).closest( ".bundled_product_summary" ).find( ".buttons_added" ).find( ".input-text" ).val());
                }
            });

            item_quantity_current = current_qty;
        }

        if ((parseInt( item_quantity_current ) < parseInt( item_quantity_max )) || ( (parseInt( item_quantity_current ) >= parseInt( item_quantity_max )) && $( $this ).closest( ".bundled_product_summary" ).hasClass('allow_notify') ) || (item_quantity_max == "" || item_quantity_max == 0)) {
            return true;
        }
        
        return false;
    }

    /**
     * Function for sold individual
     * @param int item_id add-on product id.
     * @return bool false if can be added else false.
     */
    function canProductBeAdded( item_id )
    {
        if ( sld_ind[ item_id ] == undefined) {
            return true;
        }
        return false;
    }

    //Show the minus icon on the bundled products when mouse-enter on products
    //Hide the same on mouse leave event listener.
    $('.wdm-bundle-single-product').on('mouseenter', function(){
        $(this).find('.cpb-plus-minus').show();
    });

    $('.wdm-bundle-single-product').on('mouseleave', function(){
        $(this).find('.cpb-plus-minus').hide();
    });

    $('.images').on('mouseenter', function(){
        $(this).find('.cpb-plus-minus').show();
    });

    $('.images').on('mouseleave', function(){
        $(this).find('.cpb-plus-minus').hide();
    });

    //When gift message is changed, modify its value in all modes (desktop as well as mobile)
    $('.cpb_gift_message').change(function(){
        $('.cpb_gift_message').val( $(this).val() );
    })

    // CPB jquery
    var per_product_pricing_active_enable = wdm_bundle_params.dynamic_pricing_enable;

    // When User clicks on the (add-on)Bundle Product Image, to add into CPB.
    $( '.wdm_simple_product_image, .plus, .images' ).on( "click", function (event) {

        if (!jQuery(this).parent().hasClass('bundled_product_summary')) {
            return;
        }

        //If the gift-box is full/half empty.
        //Take the add-on product attributes
        //Add the add-on product in the gift-box if it is in stock.
        if (isGiftBoxEmpty()) {
            var max_div_id = jQuery( '.wdm-bundle-single-product:last' ).attr( 'id' ).split( '_' );
            max_div_id = max_div_id[4];
            var isi = jQuery(this).hasClass('wdm-product-sold-individually');
            var cpb_pid = jQuery( '.bundled_product_summary' ).attr( 'data-product-id' );
            var $this = jQuery( this ).closest( ".bundled_product_summary" );
            var item_id = $this.find( ".cart" ).attr( 'data-bundled-item-id' );
            var bundle_id = $this.find( ".cart" ).attr( 'data-bundle-id' );
            var temp_max_quantity = $this.find( ".buttons_added" ).find( ".input-text" ).attr( "max" );
            // because some sites had this issue where initial 0 values is read as '' or undefined
            var item_quantity_max = (typeof temp_max_quantity === 'undefined') ? "" : temp_max_quantity;
            if ($this.find( ".buttons_added" ).hasClass('parent_stock')) {
                $parent_id = $this.find( ".buttons_added" ).attr('data-parent_id');
                setMaxParent($parent_id, item_quantity_max);
            }
            var item_quantity_current = $this.find( ".buttons_added" ).find( ".input-text" ).val();

            if (item_quantity_current == '' || item_quantity_current == undefined) {
                // because some sites had this issue where initial 0 values is 
                // read as '' or undefined
                item_quantity_current = 0; 
            }

            var stock_in_out = $this.find( ".wdm_stock" ).html();
            var counter = 0;
            if ( stock_in_out != "Out of stock" && canProductBeAdded(item_id) && !$this.hasClass('wdm-no-stock')) {
                //If sold individual set flag for first time
                if (isi) {
                    sld_ind[item_id] = 1;
                }

                if ( checkStockQty($this, this, item_quantity_current, item_quantity_max) ) {
                    for ( var i = 0; i <= max_div_id; i++ ) {
                        if ( $( "#wdm_bundle_bundle_item_" + i + " .wdm-bundle-box-product" ).html() == "" ) {

                            var product_image_div = '<div class = "wdm_box_item wdm_added_image_' + i + ' wdm_filled_product_' + item_id + '" data-bundled-item-id = ' + item_id + ' data-bundle-id = ' + bundle_id + '" data-product-price = "' + $this.data( 'product-price' ) + '" ><div class="cpb-plus-minus"><div class="cpb-card cpb-card-overlay cpb-remove-product"></div></div>';
                            product_image_div += $this.find( ".images" ).find( ".zoom" ).html();
                            product_image_div += '</div>';
                            $( "#wdm_bundle_bundle_item_" + i + " .wdm-bundle-box-product" ).append( product_image_div );
                            jQuery("#wdm_bundle_bundle_item_" + i).attr('title', jQuery( this ).siblings( ".px-15" ).find('.bundled_product_title').attr('title'));
                            $( "#wdm_bundle_bundle_item_" + i ).css( "display", "none" );
                            $( "#wdm_bundle_bundle_item_" + i ).fadeIn( 'slow' );
                            $("#wdm_bundle_bundle_item_"+i).addClass('wdm-product-added');
                            addPrices($this);

                            addNoStockClass($this, this, item_quantity_current, item_quantity_max, item_id);

                            if(!canProductBeAdded(item_id)) {
                                $( this ).closest( ".bundled_product_summary" ).addClass( "wdm-no-stock" );
                            }

                            if ( $( this ).hasClass( 'plus' ) == false ) {
                                $this.find( ".buttons_added" ).find( ".input-text" ).val( parseInt( item_quantity_current ) + 1 );
                            }
                            counter++;

                            if(event.hasOwnProperty('originalEvent')) {
                                cpbMobileListLayout.addProductInMobileBundle(item_id);
                            }

                            break;
                        }

                    }
                    // If event is binded with plus button then following condition will be true. won't affect as plus minus buttons are hidden
                    if ( counter == 0 && $( this ).hasClass( 'plus' ) == true ) {

                        $this.find( ".buttons_added" ).find( ".input-text" ).val( parseInt( item_quantity_current ) - 1 );

                    }
                }
            }
        } else {
            //if gift box is full still user tries to add product give alert in snackbar.
            snackbar(wdm_bundle_params.giftboxFullMsg);
        }

    } );

    //When the products in gift-box are clicked (they are removed)
    //Only if they are mandatory products they cannot removed
    $( '.wdm-bundle-single-product' ).on( "click", function (event) {
        $prefillOOS = $(this).find('.wdm_box_item').hasClass('prefill-out-stock');
        if (!$(this).find('.wdm_box_item').hasClass('wdm-prefill-mandatory')) {
            var item_id = $( this ).find( ".wdm_box_item" ).attr( "data-bundled-item-id" );
            var item_quantity_current = $( "input[name^=quantity_" + item_id + "]" ).val();
            var $this = $( this ).find( ".wdm_box_item" );
            if ( parseInt( item_quantity_current ) > 0 ) {
                $( "input[name^=quantity_" + item_id + "]" ).val( parseInt( item_quantity_current ) - 1 );

                if(event.hasOwnProperty('originalEvent')) {
                    cpbMobileListLayout.removeProductFromMobileBundle(item_id);
                }

            }
            if( sld_ind[ item_id ] != undefined) {
                delete sld_ind[item_id];
            }
            if ( $( this ).find( ".wdm_box_item" ).length > 0 ) {
                $( this ).find( ".wdm-bundle-box-product" ).empty();
                $( this ).css( "display", "none" );
                $( this ).fadeIn( 'slow' );
                $(this).removeClass('wdm-product-added');
                
                substractPrice($this);
                
                if (!$prefillOOS) {
                    removeNoStockClass(item_id);
                }
            }
        }
    } );

    /**
        $thisElement Bundle summary object of the selected add-on product.
    */
    function substractPrice($thisElement)
    {
        if ( per_product_pricing_active_enable == "yes" ) {
            var product_price = parseFloat( $thisElement.data( 'product-price' ) );

            var new_cpb_price = get_removed_price( product_price );
            total_bundle_price = get_total_bundle_removed_price( product_price );
            calculateAndShowPrices(new_cpb_price)
        }
    }

    /**
        $thisElement Bundle summary object of the selected add-on product.
    */

    function addPrices($thisElement)
    {
        if ( per_product_pricing_active_enable == "yes" ) {

            var product_price = parseFloat( $thisElement.data( 'product-price' ) );

            var new_cpb_price = get_added_price( product_price );
            total_bundle_price = get_total_bundle_price( product_price );
            calculateAndShowPrices(new_cpb_price)
        }
    }

    //Display the grand total of the product.
    //add-on products prices (if per-product pricing enabled)
    //base price (if base price enabled or for fixed pricing setting)
    function calculateAndShowPrices(new_cpb_price)
    {
        jQuery('.wdm-bundle-bundle-box').data('bundle-price', new_cpb_price);
        jQuery('.wdm-bundle-total').data('total-bundle-price', total_bundle_price);

        grand_total = getCurrencyFormatPrice(add_signup_fee(new_cpb_price));
        new_cpb_price = getCurrencyFormatPrice( new_cpb_price );
        total_bundle_price = getCurrencyFormatPrice( total_bundle_price );
        changePrice(new_cpb_price, total_bundle_price, grand_total);
    }

    //When the minus is clicked on products on the gift-box
    //Remove that product from the gift box
    jQuery( ".minus" ).on( "click", function () {
        var max_div_id = jQuery( '.wdm-bundle-single-product:last' ).attr( 'id' ).split( '_' );
        max_div_id = max_div_id[4];
        $this = jQuery( this ).closest( ".bundled_product_summary" );
        var item_id = $this.find( ".cart" ).attr( 'data-bundled-item-id' );
        var bundle_id = $this.find( ".cart" ).attr( 'data-bundle-id' );
        for ( var i = 0; i <= max_div_id; i++ ) {
            if ( $( "#wdm_bundle_bundle_item_" + i ).find( ".wdm_added_image_" + i ).attr( "data-bundled-item-id" ) == item_id ) {
                $( "#wdm_bundle_bundle_item_" + i ).find( ".wdm-bundle-box-product" ).empty();
                $( "#wdm_bundle_bundle_item_" + i ).css( "display", "none" );
                ;
                $( "#wdm_bundle_bundle_item_" + i ).fadeIn( 'slow' );
                $( this ).closest( ".bundled_product_summary" ).removeClass( "wdm-no-stock" );
                break;
            }
        }

    } );


    //When the main quantity of the CPB Product is changed.
    //Calculate the total of price on the basis of the pricing setting set in admin side.
    $(document).on('change','div.quantity input.cpb_main_qty', function(e){
        onMainQtyChange(this);
    });


    //When the main quantity of the CPB Product is changed using keys.
    $('.bundle_button .qty').keyup(function(){
        onMainQtyChange(this);
    });

    //Calculate the total of price on the basis of the pricing setting set in admin side.
    function onMainQtyChange(thisElement)
    {
        if (jQuery(thisElement).val() <= 0) {
            jQuery(thisElement).attr('placeholder', jQuery(thisElement).val());
            jQuery(thisElement).addClass("wdm-qty-error");
            return;
        } else if (jQuery(thisElement).val() == "" || jQuery(thisElement).val() > 0) {
            jQuery(thisElement).removeClass("wdm-qty-error");
        }

        //Syncing Main Products Quantity between different layouts viz Mobile Layout, Desktop Layout etc
        jQuery('#cpb_main_qty_mobile').val($(thisElement).val());

        if ( per_product_pricing_active_enable == "yes" ) {
            $overall_qty = $(thisElement).val();
            $total = 0;

            $('.wdm-bundle-bundle-box .wdm-product-added .wdm_box_item').each(function(){
                $total += parseFloat($(this).attr('data-product-price')) * $overall_qty;
            });

            jQuery('.wdm-bundle-total').data('total-bundle-price', $total);
            total_bundle_price = wdm_get_price_format($total);

            if (start_price > 0) {
                $total += start_price * $overall_qty;
            }

            jQuery('.wdm-bundle-bundle-box').data('bundle-price', $total);

            if ($total >= 0) {
                var new_price = getCurrencyFormatPrice($total);
                var grand_total = getCurrencyFormatPrice(add_signup_fee($total));
                changePrice(new_price, total_bundle_price, grand_total);
            }

        } else {
            $overall_qty = $(thisElement).val();
            
            var reg_price = jQuery('.wdm-box-price').data('reg-price');

            if (jQuery('div.gift-message-box div.cart.bundle_form .wdm-box-price p.price del').is(':visible')) {
                reg_price = parseFloat(jQuery('.wdm-box-price').data('reg-price')) * $overall_qty;
            }
            reg_price = wdm_get_price_format(reg_price);

            $total = start_price * $overall_qty;
            jQuery('.wdm-bundle-bundle-box').data('bundle-price', $total );
            
            total_bundle_price = parseFloat(jQuery('.wdm-bundle-total').data('total-bundle-price')) * $overall_qty;
            jQuery('.wdm-bundle-total').data('total-bundle-price', total_bundle_price);
            total_bundle_price = wdm_get_price_format(total_bundle_price);

            if ($total >= 0) {
                var new_price = getCurrencyFormatPrice($total);
                var grand_total = getCurrencyFormatPrice(add_signup_fee($total));
                changePrice(new_price, total_bundle_price, grand_total);
            }
        }
    }

    /*
     * Function to check whether the Gift box is empty
     * @return boolean emptyFlag true if the gift-box is empty.
     */
    function isGiftBoxEmpty() {
        var i = 1, emptyFlag = false;
        jQuery('.wdm-bundle-single-product').each(function(){
            if ( $( "#wdm_bundle_bundle_item_" + i + " .wdm-bundle-box-product" ).html() == "" ) {
                emptyFlag = true;
            }
            i++;
        });
        return emptyFlag;
    }

    /*
     * Function for finding out the aspect ratio of WC product thumbnail and applying it to the grid
     */
    function wdm_set_grid_aspect_ratio(){
        var size_obj = wdm_bundle_params.product_thumb_size;
        var size_width = size_obj.width;
        var size_height = size_obj.height;
        var aspect_ratio = size_height / size_width;
        var padding = aspect_ratio*100;
        jQuery('head').append('<style>.wdm-bundle-single-product::before{padding-top:'+padding+'%}</style>');
    }

    wdm_set_grid_aspect_ratio();

    if (wdm_bundle_params.allowPrefillProducts) {
        jQuery('.wdm-bundle-single-product .wdm-bundle-box-product .wdm-prefill-product').each(function(){
            $prefil_prod_id = $(this).attr('data-bundled-item-id');
            $this = $( '.images' ).closest('div[data-product-id='+$prefil_prod_id+']');
            jQuery(this).closest('.wdm-bundle-single-product').attr('title', jQuery( $this.find('.images') ).siblings( ".px-15" ).find('.bundled_product_title').attr('title'));

            var item_quantity_max = $this.find( ".buttons_added" ).find( ".input-text" ).attr( "max" );
            var item_quantity_current = $this.find( ".buttons_added" ).find( ".input-text" ).val();
            $isSoldInd = $this.find('.images').hasClass('wdm-product-sold-individually');

            if ($prefil_prod_id in qty_max) {
                qty_max[$prefil_prod_id]++;
            } else {
                qty_max[$prefil_prod_id] = 1;
            }

            if (item_quantity_max == qty_max[$prefil_prod_id]) {
                $this.addClass('wdm-no-stock');
            }

            if($isSoldInd) {
                $this.addClass('wdm-no-stock');
            }
            if (item_quantity_current == '' || item_quantity_current == undefined) {
                item_quantity_current = 0; // because some sites had this issue where initial 0 values is read as '' or undefined
            }

            var per_product_pricing_active_enable = wdm_bundle_params.dynamic_pricing_enable;

            if ( per_product_pricing_active_enable == "yes" ) {

                total_bundle_price = jQuery('.wdm-bundle-total').data('total-bundle-price');
                var product_price = parseFloat( $this.data( 'product-price' ) );
                total_bundle_price = get_total_bundle_price( product_price );
                var new_cpb_price = get_added_price( product_price );
                jQuery('.wdm-bundle-bundle-box').data('bundle-price', new_cpb_price);
                var $option_value = 0;
                
                $opt_val =   $('.product-addon-totals .amount').text();

                if ($opt_val) {
                    $option_value = parseFloat($opt_val.replace(wdm_bundle_params.currency_symbol, ""));
                }
                
                new_cpb_price += $option_value;

                
                new_cpb_price = wdm_get_price_format( new_cpb_price );
                total_bundle_price = wdm_get_price_format( total_bundle_price );
                if( wdm_bundle_params.wdm_bundle_on_sale ){
                    // $( ".price" ).find( "ins .amount" ).html( new_cpb_price );
                    jQuery('div div div.wdm_bundle_price p.price').find("ins .amount").html(new_cpb_price)
                    jQuery('div.wdm_product_info .price').find( "ins .amount" ).html( new_cpb_price );
                    jQuery('div.wdm-vertical-cpb-layout div.wdm_product_info p.price').find( "ins .amount" ).html( new_cpb_price );
                    jQuery('div.gift-message-box div.cart.bundle_form .wdm-bundle-total p.price').find( ".amount" ).html( total_bundle_price );
                    jQuery('div.gift-message-box div.cart.bundle_form .wdm-grand-total p.price').find( ".amount" ).html( new_cpb_price );
                    // jQuery('div.gift-message-box div.cart.bundle_form .wdm-box-price p.price').find( ".amount" ).html( box_price );
                }
                else{
                    // $( ".price" ).find( ".amount" ).html( new_cpb_price );
                    jQuery('div div div.wdm_bundle_price p.price').find(".amount").html(new_cpb_price)
                    jQuery('div.wdm_product_info .price').find( ".amount" ).html( new_cpb_price );
                    jQuery('div.wdm-vertical-cpb-layout div.wdm_product_info p.price').find( ".amount" ).html( new_cpb_price );
                    jQuery('div.gift-message-box div.cart.bundle_form .wdm-bundle-total p.price').find( ".amount" ).html( total_bundle_price );
                    jQuery('div.gift-message-box div.cart.bundle_form .wdm-grand-total p.price').find( ".amount" ).html( new_cpb_price );
                    // jQuery('div.gift-message-box div.cart.bundle_form .wdm-box-price p.price').find( ".amount" ).html( box_price );
                }
            }

            //if item_quantity_max is empty then (parseInt(item_quantity_max) - 1) will be NaN.
            if ( item_quantity_max != "") {
                if ( item_quantity_current == ( parseInt( item_quantity_max ) - 1 )  && (!$( this ).closest( ".bundled_product_summary" ).hasClass('allow_notify') )) {
                    $( this ).closest( ".bundled_product_summary" ).addClass( "wdm-no-stock" );
                }
            }
            // if(!canProductBeAdded(item_id)) {
            //     $( this ).closest( ".bundled_product_summary" ).addClass( "wdm-no-stock" );
            // }

            if ( $( this ).hasClass( 'plus' ) == false ) {
                $this.find( ".buttons_added" ).find( ".input-text" ).val( parseInt( item_quantity_current ) + 1 );
            }

        });
    }
} );
