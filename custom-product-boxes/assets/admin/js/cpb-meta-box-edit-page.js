/**
 * File to manage edit product page configuration data of CPB type.
 */
jQuery( function($) {
    // Simple type options are valid for bundles.
    $( '.show_if_simple:not(.hide_if_bundle)' ).addClass( 'show_if_wdm_bundle_product' );

    var cpb_product = {};
    
    cpb_product.gift_message = {
        selector: '#cpb_enable_message',
        is_message_enabled: function() {
            return cpb_product.is_product_cpb() && jQuery('#cpb_enable_message').is(":checked")
        },
        message_label: jQuery('.cpb_message_label_field'),
    }

    cpb_product.is_product_cpb = () => {
        return jQuery('#product-type').val() == 'wdm_bundle_product' ? true : false;
    }

    cpb_product.regular_price_field = jQuery( '.options_group.pricing ._regular_price_field' );
    
    var subscription = {
        subscription_selector:             '#cpb_subscription',
        has_subscription:   function() { 
            return cpb_product.is_product_cpb() && jQuery('#cpb_subscription').is(":checked")
        },
        subscription_fields:  jQuery( '.options_group.subscription_pricing ' ),
    };
    
    cpb_product.product_data_selector = jQuery( '#woocommerce-product-data' );


    cpb_product.addColorPicker = ( $pickerField ) => {
        jQuery( $pickerField ).alphaColorPicker();
    }

    cpb_product.disableFields = ( $field ) => {
        jQuery( $field ).addClass("disableddiv");
    }

    cpb_product.enableFields = ( $field ) => {
        jQuery( $field ).removeClass("disableddiv");
    }

    cpb_product.enableSubscriptionField = () => {
        subscription.subscription_fields.addClass( 'show_if_wdm_bundle_product' );
        cpb_product.disableFields( "div.options_group.subscription_pricing.subscription_sync" );
        subscription.subscription_fields.show();
    }

    cpb_product.disableSubsriptionField = () => {
        subscription.subscription_fields.removeClass( 'show_if_wdm_bundle_product' );
        subscription.subscription_fields.hide();
    }

    cpb_product.cpbEnableDisableProductSubscription = () => {
        if ( subscription.has_subscription() ) {
            cpb_product.enableSubscriptionField();
            cpb_product.regular_price_field.hide();
        } else {
            cpb_product.disableSubsriptionField();
            cpb_product.regular_price_field.show();
        }
    }

    cpb_product.cpbEnableDisableGiftMessage = () => {
        if ( cpb_product.gift_message.is_message_enabled() ) {            
            cpb_product.enableMessageField();
        } else {
            cpb_product.disableMessageField();
        }
    }

    cpb_product.enableMessageField = () => {
        cpb_product.gift_message.message_label.show();
    }

    cpb_product.disableMessageField = () => {
        cpb_product.gift_message.message_label.hide();
    }

    cpb_product.disableAutoComplete = ( $field ) => {
        jQuery( $field ).attr( 'autocomplete', 'off' );
    }

    cpb_product.bindCPBEvents = () => {
        console.log(cpb_product.product_data_selector);
        cpb_product.product_data_selector.on( 'change', subscription.subscription_selector, cpb_product, cpb_product.cpbEnableDisableProductSubscription );
        cpb_product.product_data_selector.on( 'change', cpb_product.gift_message.selector, cpb_product, cpb_product.cpbEnableDisableGiftMessage );
    }
    // Bundle type specific options.
    $( 'body' ).on( 'woocommerce-product-type-change', function( event, select_val ) {

        if ( 'wdm_bundle_product' === select_val ) {

            $( '.show_if_external' ).hide();
            $( '.show_if_wdm_bundle_product' ).show();

            $( 'input#_manage_stock' ).change();

        }

    } );

// $( ':input.wc_products_selections' ).select2Cpb( select2_args ).addClass( 'enhanced' );
    
    cpb_product.bindCPBEvents();

    // Show subscription pricing options when subscription is enabled (On page load)
    cpb_product.cpbEnableDisableProductSubscription();
    cpb_product.cpbEnableDisableGiftMessage();
    // cpb_product.addColorPicker( '#cpb_gift_boxes_color' );
    // cpb_product.addColorPicker( '#cpb_gift_bgcolor' );
    cpb_product.disableFields( '.cpb_layout_selected_field' );
    jQuery('#cpb_product_data').on( 'click', '.include_variations_field span.description', function(){
        jQuery("#include_variations").trigger('click');
        changeAccordionState();
    });
    jQuery('#cpb_product_data').on( 'click', '#include_variations', function(){
        changeAccordionState();
    });

    function changeAccordionState()
    {
        if ( jQuery( '#include_variations' ).is( ':checked' ) ) {
            jQuery( "#cpb-variation-selection" ).show();
        } else {
            jQuery( "#cpb-variation-selection" ).hide();
        }
    }

    $( "#cpb-accordion" )
      .accordion({
        header: "> div > h3",
        collapsible: true,
        active: false,
        activate: function( event, ui ) {
            if ( $(this).find('.ui-state-active').length ) {
                var id = '#' + $(ui.newPanel).attr('id') + ' .cpb_variation_wrapper';
                console.log('ui');
                console.log(id);
                var product_id = $(ui.newHeader).parents('.cpb-group').attr('data-variable_id');
                $(document).trigger( 'expand_accordion',  [product_id, id]);
            }
        }
    });

    jQuery(document).on('expand_accordion', function( event, product_id, newPanel ){
        if ( ! jQuery( newPanel ).hasClass('variations_fetched') ) {
            $( '#woocommerce-product-data' ).block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });

            var selected_variations = "[]";

            if (jQuery(newPanel).attr('data-selected_variations') !== undefined) {
                selected_variations = jQuery(newPanel).attr('data-selected_variations');
            }

            jQuery.ajax({
                url : enhanced_select_params.ajax_url,
                type : 'POST',
                data : {
                    action: 'cpb_get_variable_product_variations',
                    _nonce: enhanced_select_params.search_products_nonce,
                    product_id: product_id,
                    selected_variations: selected_variations,
                },
                success : function( dynamic_response ) {
                    jQuery(newPanel).html(dynamic_response);
                    jQuery(newPanel).addClass('variations_fetched');
                    $( "#cpb-accordion" ).accordion('refresh');
                    $( '#woocommerce-product-data' ).unblock();
                }
            });
        }
    });

    jQuery(document).on('on_addon_added', function(event, data, this_data) {
        if ( jQuery( '#add_on_products' ).val() !== undefined ) {
            format_cpb_addon_data( data );
        }

        if ( data.product_type == 'variable' ) {
            add_accordion( data );
        }
    });

    jQuery(document).on('select2Cpb-removed', function(event) {
        remove_addon( event.choice.id );
    });

    function add_accordion( data  ) {
        var accordion_html = '<div class="cpb-group group" id="cpb_variation_wrapper-'+data.id+'" data-variable_id="'+data.id+'"><h3>'+data.text+'</h3><div><div class="cpb_variation_wrapper"></div></div></div>';
        $( accordion_html ).appendTo( '#cpb-accordion' );
        $( "#cpb-accordion" ).accordion('refresh');
        changeAccordionState();
    }

    function remove_addon( addon_id ) {
        $selected_addon = get_selected_addon( addon_id );
        
        if ( $selected_addon.product_type == 'variable' ) {
            remove_accordion( addon_id );
        }

        remove_addon_from_list( addon_id );
    }

    function remove_addon_from_list( addon_id ) {
        $addons_list = get_addons_data();
        if ( $addons_list.hasOwnProperty( addon_id ) ) {
            delete $addons_list[ addon_id ];
        }

        update_addon_list( $addons_list );
    }

    function remove_accordion( addon_id ) {
        jQuery( "#cpb_variation_wrapper-" + addon_id ).remove();
    }

    jQuery( "#post" ).submit(function(event){
        if ( jQuery( '#product-type' ).val() == 'wdm_bundle_product' ) {
            
            if (jQuery( '#add_on_products' ).val() == '') {
                event.preventDefault();
                alert(cpb_admin_product_page_object.select_addon_product);
            }
            if ( jQuery( '#cpb_box_capacity' ).val() < 2 ) {
                event.preventDefault();
                alert(cpb_admin_product_page_object.qty_greater_zero);
            }
        }
    });
});


