/**
 * Function to show accordion
 */
function display_accordion() {

}

function isset( variable ) {
	return typeof(variable) != "undefined" && variable !== null
}

/**
 * Get addons formated data
 */
function get_addons_data() {
	var $add_ons_formated = {};
	
	if ( jQuery( '#add_on_products' ).val() != "" ) {
		$add_ons_formated = JSON.parse(jQuery( '#add_on_products' ).val());
	}

	return $add_ons_formated;
}

function update_addon_list( addon_list ) {
	if ( get_length( addon_list ) > 0 ) {
		jQuery( '#add_on_products' ).val( JSON.stringify( addon_list ) );
	}
	if (get_length( addon_list ) == 0) {
		jQuery( '#add_on_products' ).val( "" );
	}
}

function get_selected_addon( addon_id ) {
	$all_add_ons = get_addons_data();
	console.log($all_add_ons);
	if ( $all_add_ons.hasOwnProperty( addon_id ) ) {
		console.log("Has property");
		return $all_add_ons[ addon_id ];
	}

}

function get_length( addon_list ) {
	return Object.entries( addon_list ).length;
}

/**
 * This function returns a array to be posted and saved in database
 * $add_on_products object unformated data returned from ajax of cpbSelect2 search
 */
function format_cpb_addon_data( $add_on_products ) {
	var $add_ons_formated = get_addons_data();

	console.log($add_ons_formated);
	var addon_id = $add_on_products.id;

	$add_ons_formated[ addon_id.toString() ] = {
		text_name: $add_on_products.text,
		variations: $add_on_products.variations,
		product_type: $add_on_products.product_type,
	};

	console.log($add_ons_formated);

	update_addon_list( $add_ons_formated );
}

/**
 * This function adds a variation data to the add_ons_formated array list.
 * $add_on_products array of add_ons
 * $variable_id product id for variabe product whose variations need to be added
 * $selected_variation_data data of selected variation to be added
 */
function add_selected_variations( $variable_id, $selected_variation_data, $selected_variation ) {
	var $temp_add_ons = get_addons_data();

	if ( $temp_add_ons.hasOwnProperty( $variable_id.toString() ) ) {
		$temp_add_ons[ $variable_id.toString() ]['variations'][ $selected_variation ] = $selected_variation_data;
		update_addon_list( $temp_add_ons );
	}
}

function remove_selected_variations( $variable_id, $selected_variation ) {
	var $temp_add_ons = get_addons_data();

	if ( $temp_add_ons.hasOwnProperty( $variable_id.toString() ) ) {
		delete $temp_add_ons[ $variable_id.toString() ]['variations'][ $selected_variation ];
		update_addon_list( $temp_add_ons );
	}
}

/**
* Finds difference between the objects
* Returns difference like a-b
* a = {x:1, y:2, z:3, w:4};
* b = {x:1, y:2};
* res = a-b = {z:3, w:4};
*/
function object_diff(obj1, obj2) {
    var result = {};
    jQuery.each(obj1, function (key, value) {
        if (!obj2.hasOwnProperty(key)) {
            result[key] = value;
        }
    });
    return result;
}

/**
 * Update the .woocommerce div with a string of html.
 *
 * @param {String} html_str The HTML string with which to replace the div.
 * @param {bool} preserve_notices Should notices be kept? False by default.
 */
function add_error_notice( error_msg, cpb_class ) {
	console.log(html_str);
	var html_str = '<div class="woocommerce-error '+cpb_class+'" role="alert">'+error_msg+'</div>';
	var $html       = jQuery.parseHTML( html_str );
	var post_id = jQuery('body').attr('class').match(/\d+/);
	post_id = post_id[0];
	if (!jQuery('#product-'+post_id).hasClass('product-type-wdm_bundle_product')) {
		return;
	}

	jQuery(html_str).appendTo('#content > div > div.woocommerce');

	// Remove errors


	// Notify plugins that the cart was emptied.
	jQuery( document.body ).trigger( 'cpb_box_empty' );


	jQuery( document.body ).trigger( 'cpb_updated_wc_div' );
}

function remove_previous_notices() {
	jQuery('#content > div > div.woocommerce').find('.cpb_error').remove();
}

function setEqualHeight(selector) {
    if (selector.length > 0) {
        var arr = [];
        var selector_height;
        selector.css("min-height", "initial");
        selector.each(function (index, elem) {
          selector_height = elem.offsetHeight;
          arr.push(selector_height);
        });
        selector_height = Math.max.apply(null, arr);
        selector.css("min-height", selector_height);
    }
}

/**
* Get the price in proper format.
* Without trailing zeroes, correct decimal separation.
* And with the currency symbol to left or right.
* @param float new_price price.
* @return string new_price_format price in proper format.
*/
function wdm_get_price_format( new_price ) {

        var new_price = number_format( new_price );

        var remove = wdm_bundle_params.currency_format_decimal_sep;

        if ( wdm_bundle_params.currency_format_trim_zeros == 'yes' && wdm_bundle_params.currency_format_num_decimals > 0 ) {

            for ( var i = 0; i < wdm_bundle_params.currency_format_num_decimals; i++ ) {
                remove = remove + '0';
            }

            new_price = new_price.replace( remove, '' );
        }

        var new_price_format = '';

        if ( wdm_bundle_params.currency_position == 'left' ) {
            new_price_format = wdm_bundle_params.currency_symbol + new_price;
        }
        else if ( wdm_bundle_params.currency_position == 'right' ) {
            new_price_format = new_price + wdm_bundle_params.currency_symbol;
        }
        else if ( wdm_bundle_params.currency_position == 'left_space' ) {
            new_price_format = wdm_bundle_params.currency_symbol + ' ' + new_price;
        }
        else if ( wdm_bundle_params.currency_position == 'right_space' ) {
            new_price_format = new_price + ' ' + wdm_bundle_params.currency_symbol;
        }

        return new_price_format;
}

function number_format( number ) {

    var decimals = wdm_bundle_params.currency_format_num_decimals;
    var decimal_sep = wdm_bundle_params.currency_format_decimal_sep;
    var thousands_sep = wdm_bundle_params.currency_format_thousand_sep;

    var n = number, c = isNaN( decimals = Math.abs( decimals ) ) ? 2 : decimals;
    var d = decimal_sep == undefined ? "," : decimal_sep;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt( n = Math.abs( +n || 0 ).toFixed( c ) ) + "", j = ( j = i.length ) > 3 ? j % 3 : 0;

    return s + ( j ? i.substr( 0, j ) + t : "" ) + i.substr( j ).replace( /(\d{3})(?=\d)/g, "$1" + t ) + ( c ? d + Math.abs( n - i ).toFixed( c ).slice( 2 ) : "" );
}

function isOnScreen(elem) {
	// if the element doesn't exist, abort
	if( elem.length == 0 ) {
		return;
	}
	var $window = jQuery(window)
	var viewport_top = $window.scrollTop()
	var viewport_height = $window.height()
	var viewport_bottom = viewport_top + viewport_height
	var $elem = jQuery(elem)
	var top = $elem.offset().top
	var height = $elem.height()
	var bottom = top + height

	return (top >= viewport_top && top < viewport_bottom) ||
	(bottom > viewport_top && bottom <= viewport_bottom) ||
	(height > viewport_height && top <= viewport_top && bottom >= viewport_bottom)
}
