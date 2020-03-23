/**
 * This is a replica of WooCommerce's wc-enhanced-select.js. Here we are also sending language so that we get only language specfic products
 */
 /*global enhanced_select_params */
 jQuery( function( $ ) {
    function getEnhancedSelectFormatString() {
        var formatString = {
            formatMatches: function( matches ) {
                if ( 1 === matches ) {
                    return enhanced_select_params.i18n_matches_1;
                }

                return enhanced_select_params.i18n_matches_n.replace( '%qty%', matches );
            },
            formatNoMatches: function() {
                return enhanced_select_params.i18n_no_matches;
            },
            formatAjaxError: function() {
                return enhanced_select_params.i18n_ajax_error;
            },
            formatInputTooShort: function( input, min ) {
                var number = min - input.length;

                if ( 1 === number ) {
                    return enhanced_select_params.i18n_input_too_short_1;
                }

                return enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', number );
            },
            formatInputTooLong: function( input, max ) {
                var number = input.length - max;

                if ( 1 === number ) {
                    return enhanced_select_params.i18n_input_too_long_1;
                }

                return enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', number );
            },
            formatSelectionTooBig: function( limit ) {
                if ( 1 === limit ) {
                    return enhanced_select_params.i18n_selection_too_long_1;
                }

                return enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', limit );
            },
            formatLoadMore: function() {
                return enhanced_select_params.i18n_load_more;
            },
            formatSearching: function() {
                return enhanced_select_params.i18n_searching;
            }
        };

        return formatString;
    }

    $( document.body )

    .on( 'wc-enhanced-select-init', function() {

            // Ajax product search box
            $( ':input.wc_products_selections' ).filter( ':not(.enhanced)' ).each( function() {
                var select2_args = {
                    allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
                    placeholder: $( this ).data( 'placeholder' ),
                    minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
                    escapeMarkup: function( m ) {
                        return m;
                    },
                    ajax: {
                        url:         enhanced_select_params.ajax_url,
                        dataType:    'json',
                        quietMillis: 250,
                        data: function( term ) {
                            return {
                                term:     term,
                                action:   $( this ).data( 'action' ) || 'cpb_json_search_products_and_variations',
                                security: enhanced_select_params.search_products_nonce,
                                exclude:  $( this ).data( 'exclude' ),
                                limit:    $( this ).data( 'limit' )
                            };
                        },
                        results: function( data ) {
                            var terms = [];
                            if ( data ) {
                                // console.log(data);
                                var current_element = {};
                                $.each( data, function( id, data ) {                                
                                    current_element = {
                                        id: id,
                                        text: data.text_name,
                                        product_type: data.product_type,
                                        variations: {}
                                    };
                                    terms.push( current_element );
                                });
                            }
                            return {
                                results: terms
                            };
                        },
                        cache: true
                    }
                };

                if ( $( this ).data( 'multiple' ) === true ) {
                    console.log('in_mu');
                    select2_args.multiple = true;
                    select2_args.initSelection = function( element, callback ) {
                        var data     = $.parseJSON( element.attr( 'data-selected' ) );
                        var selected = [];

                        $( element.val().split( ',' ) ).each( function( i, val ) {
                            selected.push({
                                id: val,
                                text: data[ val ]
                            });
                        });
                        return callback( selected );
                    };
                    select2_args.formatSelection = function( data ) {
                        return '<div class="selected-option" data-id="' + data.id + '">' + data.text + '</div>';
                    };
                } else {
                    console.log('not_in_mu');
                    select2_args.multiple = false;
                    select2_args.initSelection = function( element, callback ) {
                        var data = {
                            id: element.val(),
                            text: element.attr( 'data-selected' )
                        };
                        return callback( data );
                    };
                }

                select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

                $( this ).select2Cpb( select2_args ).addClass( 'enhanced' );
            });

})

        // WooCommerce Backbone Modal
        .on( 'wc_backbone_modal_before_remove', function() {
            $( ':input.wc_products_selections' ).select2Cpb( 'close' );
        })

        // Get Ajax data
        .on( 'wc_get_variation_data', function() {
        })
        .trigger( 'wc-enhanced-select-init' )
        .trigger( 'wc_get_variation_data' );

        function getParameterByName(name, url) {
            if (!url) {
              url = window.location.href;
          }
          name = name.replace(/[\[\]]/g, "\\$&");
          var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
          results = regex.exec(url);
          if (!results) return null;
          if (!results[2]) return '';
          return decodeURIComponent(results[2].replace(/\+/g, " "));
      }

  
    // jQuery( ':input.wc_products_selections' ).on( 'change', function(){
    //     var input_data = jQuery( this ).select2Cpb('data');
    //     if ( jQuery( '#add_on_products' ).val() !== undefined ) {
    //         $add_on_data = format_cpb_addon_data( input_data );
    //     }
    // });

});
