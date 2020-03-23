jQuery(document).ready(function($){
    var plist_size = $('ul.select2Cpb-choices > *').size();
    var maxVal = parseInt($('#cpb_box_capacity').val(), 10);
    var product_list = {};
    var productsData = [];
    var enablePrefillProducts = 'no';
    
    if ( jQuery('#product-type').val() == 'wdm_bundle_product' ) {
        $.ajax({
            type : "POST",
            url : cpb_prefilled_object.ajax_url,  
            data : {
                'action' : "cpb_get_prefilled", 
                'cpb_id': jQuery('#post_ID').val(),
            },
            dataType: "json",
            async: false,
            success : function(response){
                if(!$.isEmptyObject(response)) {
                    console.log(response);
                    productData = response.prefillBundleData;
                    enablePrefillProducts = response.enablePrefillProducts;
                }
            },
            // error : function(){alert ("Sorry :( ");}
        });
    }

    function enableDisablePrefilledOptions() {
        if(enablePrefillProducts == 'yes') {
            if ($('.prefill_table tbody tr').length > 0) {
                $('.prefill_table').show();
                $('.prefill_div').show();
                $('.cpb_swap_products_field').show();
            } else {
                $('.prefill_table').hide();
                $('.prefill_div').hide();
                $('.cpb_swap_products_field').hide();
            }
        } else {
            $('.cpb_swap_products_field').hide();
        }
    }

    // On load if already checked
    enableDisablePrefilledOptions();
    //Search bar for the products gets the list of products selected.
    $('.wdm_bundle_products_selector .select2Cpb-container .select2Cpb-choices .selected-option').each( function() {
        var pro_id = $(this).attr('data-id');
        var prod_name = $(this).text();
        product_list[pro_id] = prod_name;
    });

    //gets the list of products selected.
    $('#product_field_type > option:selected').each( function() {
        var pro_id = parseInt($(this).val());
        var prod_name = $(this).text();
        product_list[pro_id] = prod_name;
    });

    var mergeProductData = function () {
        var selectdata = get_addons_data();
        var newSelect = {};
        // console.log('Selected DAta ');
        // console.log(selectdata);
        for ( var i in selectdata ) {
            var product_id = i;
            if (selectdata[i].hasOwnProperty('variations') && !$.isEmptyObject(selectdata[i].variations)) {
                var variationsData = selectdata[i].variations;
                for (var variation in variationsData) {
                    console.log(variation);
                    if (newSelect.hasOwnProperty(variation)) {
                        continue;
                    }
                    console.log(variationsData);
                    newSelect[variation] = variationsData[variation];
                    newSelect[variation]['id'] = variation;
                    newSelect[variation]['parent_hash'] = product_id;
                    newSelect[variation]['product_type'] = 'variation';
                    newSelect[variation]['variation_id'] = variationsData[variation]['variation_id'];
                    if (variationsData[variation].hasOwnProperty('text_name')) {
                        newSelect[variation]['text'] = variationsData[variation]['text_name'];
                        delete newSelect[variation]['text_name'];
                    }
                }
            } else if( selectdata[i].hasOwnProperty('product_type') && 'variable' == selectdata[i].product_type ) {
                continue;
            } else {
                newSelect[i] = selectdata[i];
                if (selectdata[i].hasOwnProperty('text_name')) {
                    newSelect[i]['text'] = selectdata[i]['text_name'];
                    newSelect[i]['id'] = product_id;
                    newSelect[i]['product_type'] = 'simple';
                    newSelect[i]['product_id'] = product_id;
                    delete newSelect[i]['text_name'];
                }
            }
        }

        return newSelect;
    }


    /**
    * When the allow pre-filled boxes checkbox is clicked, then show the associated table.
    * Show the list of the selected add-on products to get selected as pre-filled products.
    * show the required fields.
    */
    function addTR()
    {
        var preBody = $(".prefill_table tbody");

        var checkbox_holder = "<tr><td><input type = 'checkbox' class = 'prefill_checkbox' name = 'prod_mandatory[]' value = '0' /></td>";

        var product_holder = "<td><select name='wdm_cpb_products[]' class='prefill_products_holder'>";

        // jQuery(productsData).each(function(){
        productsData = mergeProductData();
        for( var item in productsData) {
            // console.log(productsData[item]);
            var $product = productsData[item];
            var dataId = $product.product_type == "variation" ? $product.variation_id : $product.product_id;
            var parentHash = $product.product_type == "variation" ? $product.parent_hash : $product.product_id;
            product_holder += "<option value = '"+$product.id+"' data-id = '"+dataId+"' data-parent-id ='"+parentHash+"'>"+$product.text+"</option>";
        }
        product_holder += "</select></td>";

        var qty_holder = "<td class = 'prefill_qty'><input type = 'number' name = 'wdm_prefill_qty[]' min = '1' max = '"+maxVal+"' class = 'prefill_qty_id' /></td>";
        var remove_button_holder = "<td><a class='wdm_cpb_rem' href='#' id=''><img class='add_new_row_image' src='" + cpb_prefilled_object.remove_image + "' /></a>";
        var add_button_holder = "<a class='wdm_cpb_add' href='#' id=''><img class='add_new_row_image' src='" + cpb_prefilled_object.add_image + "' /></a>";
        var end_tableRow = "</td></tr>";

        $(checkbox_holder + product_holder + qty_holder + remove_button_holder + add_button_holder + end_tableRow).appendTo(preBody);
        $('.prefill_table').show();
    }

    /**
    * When a row in pre-filled table is removed
    * Find the last row for the table.
    * Attach the add new row icon on the last row.
    */
    function afterRemoveTr()
    {
        if (!$(".prefill_table tbody tr:last td:last .wdm_cpb_add").length) {
            //Attach tha add row icon to the last row.
            $(".prefill_table tbody tr:last td:last").append("<a class='wdm_cpb_add' href='#' id=''><img class='add_new_row_image' src='" + cpb_prefilled_object.add_image + "' /></a>");
        }

        var last_row = $('.prefill_table').find('tr:last');

        if (last_row.find("th:last").length) {
            $('.prefill_table').hide();
            $('.prefill_div').hide();
            $('.cpb_swap_products_field').hide();
            $('#cpb_enable_prefilled').prop('checked', false);
            $('.prefill_table tbody').empty();
        }
    }

    function removeProduct(newId)
    {
        // if ( $.isEmptyObject( productsData ) ) {
            productsData = mergeProductData();
        // }

        if (newId in productsData) {
            //remove simple products from prefill product
            removeSingleProduct(newId);
        } else {

            //remove variations products from prefill product
            removeVariableProduct(newId);
        }
    }

    function removeVariableProduct(newId)
    {
        jQuery("select[name='wdm_cpb_products[]']").each(function(index, object){
            var $this = jQuery(this);
            if($this.find('option:selected').attr('data-parent-id') == newId)
            {
                jQuery(this).closest('tr').detach();
                afterRemoveTr();
            }
        });

        jQuery( "select[name='wdm_cpb_products[]']").each(function(){
            jQuery("select[name='wdm_cpb_products[]'] option[data-parent-id='"+newId+"']").remove();
        });
    }

    function idInProductsData(newId, productsData)
    {
        var selectedProducts = jQuery( ':input.wc_products_selections' ).select2Cpb('data');

        if (newId in productsData) {
            return true;
        }

        for (var i in selectedProducts) {
            if (selectedProducts[i] != null && selectedProducts[i].hasOwnProperty('childrens')) {
                if (newId == selectedProducts[i].id) {
                    return true;
                }
            }
        }
        return false;
    }

    function removeSingleProduct(newId)
    {   
        delete productsData[newId];
        jQuery("select[name='wdm_cpb_products[]']").each(function(index, object){
            if(jQuery(this).val() == newId){
                jQuery(this).parents('tr').detach();
                afterRemoveTr();
            }
        });

        jQuery( "select[name='wdm_cpb_products[]']").each(function(){
            jQuery("select[name='wdm_cpb_products[]'] option[value='"+newId+"']").remove();
        });
    }

    //Gets the prefilled products checkbox is checked or not.
    $('#prefill_table_id').delegate('.prefill_products_holder', 'change', function(){
        $(this).parents('tr').find('.prefill_checkbox').val(this.value);
    });

    //When the prefill-checkbox is clicked get the selected products for the prefill list
    $('#prefill_table_id').delegate('.prefill_checkbox', 'change', function(){

        var associated_product = $(this).parents('tr').find('.prefill_products_holder').find(':selected').val();

        $(this).val(associated_product);
    });

    //For add-on products search function.
    $('.wc_products_selections').change(function(){
        add_product_to_prefill_list();
    });

    // jQuery('#cpb_product_data').delegate( '.cpb_variation_checkboxes', 'cpb_variation_added', function( event, variation_id ) {
    //     if (variation_id == jQuery(this).attr('data-variation_id')) {
    //         alert('public');
    //         add_product_to_prefill_list();
    //     }
    // });

    // jQuery('#cpb_product_data').delegate( '.cpb_variation_checkboxes', 'cpb_variation_removed', function( event, variation_id, variations_data ) {
    //     removeProduct( variation_id );
    // });

    jQuery('#cpb_product_data').delegate( '.cpb_variation_checkboxes', 'change', function() {
        var product_id = jQuery( this ).attr( 'data-variable_id' );
        var variation_id = jQuery( this ).attr( 'data-variation_id' );
        var variations_data = JSON.parse( jQuery( 'input[name=product_variation_data_'+product_id+']' ).val() );
        var selected_variation = jQuery( this ).val();

        if ( jQuery( this ).is( ':checked' ) ) {
            add_selected_variations( product_id, variations_data[ selected_variation ], selected_variation );
            // jQuery( '#cpb_variation_checkboxes' ).trigger( 'cpb_variation_added', [variation_id] );
            add_product_to_prefill_list();
        } else {
            removeProduct( selected_variation );
            remove_selected_variations( product_id, selected_variation );
            // jQuery( '.cpb_variation_checkboxes' ).trigger( 'cpb_variation_removed', [variation_id, variations_data] );
        }

    } );

    function add_product_to_prefill_list() {
        var newProduct = null;
        var newId = 0;
        var newName = '';

        // woocommerce 3.0.0 uses $('#product_field_type > option:last')
        if (jQuery( ':input.wc_products_selections' ).select2Cpb('data').length){
            var newProductList = mergeProductData();

            var diff = object_diff(newProductList, productsData);

            if (Object.keys(diff).length !== 0 && diff.constructor === Object) {
                //Adding Product
                newId = Object.keys(diff)[0];
                newName = newProductList[newId].text;
            }

        } else {
             newProduct = $('.wdm_bundle_products_selector .select2Cpb-container .select2Cpb-choices .selected-option:last');
             newId = $(newProduct).val();
             newName = $(newProduct).text();

        }
        
        if(newId === 0){
            return;
        }

        if (!idInProductsData(newId, productsData) && !$.isEmptyObject(diff)) {
            if (Object.keys(diff).length > 1) {
                var newData = mergeProductData(productsData, diff);
                for (var addon_id in diff) {
                    productsData[addon_id] = diff[addon_id];

                    jQuery( "select[name='wdm_cpb_products[]']").each(function() {
                        var parent_id = productsData[addon_id].product_type == "variation" ? productsData[addon_id].parent_hash : productsData[addon_id].id;
                        var product_id = productsData[addon_id].product_type == "variation" ? productsData[addon_id].variation_id : productsData[addon_id].product_id;
                        jQuery("<option></option>", {value: addon_id, html: productsData[addon_id].text, 'data-id':product_id, 'data-parent-id':parent_id}).appendTo(this);
                        // jQuery(this).find('option[value="'+newId+'"]').data('parent-id', newId);
                    });
                }
            } else {
                productsData[newId] = newProductList[newId];
                console.log(productsData[newId]);
                // productsData[newId]['text'] = newName;
                jQuery( "select[name='wdm_cpb_products[]']").each(function() {
                    var parent_id = productsData[newId].product_type == "variation" ? productsData[newId].parent_hash : productsData[newId].id;
                    var product_id = productsData[newId].product_type == "variation" ? productsData[newId].variation_id : productsData[newId].product_id;
                    jQuery("<option></option>", {value: newId, html: productsData[newId].text,'data-id':product_id, 'data-parent-id':parent_id}).appendTo(this);
                    // jQuery(this).find('option[value="'+newId+'"]').data('parent-id', newId);
                });
            }
        }
    }


    $('body').on('focus', '.wc_products_selections' , function() {
        $('a.select2Cpb-search-choice-close').click(function(){
            var dataId = jQuery(this.parentElement).find('.selected-option').data('id');
            removeProduct(dataId);
        });      
    });

    $('a.select2Cpb-search-choice-close').click(function(){  
        var dataId = jQuery(this.parentElement).find('.selected-option').data('id');
        removeProduct(dataId);
    });

    //When the allow pre-filled checkbox is changed.
    jQuery('#cpb_product_data').delegate('#cpb_enable_prefilled', 'change', function() {
        //Max value of the Product box.
        maxVal = parseInt($('#cpb_box_capacity').val(), 10);
        var productsAddon = jQuery( '#product_field_type' ).val();
        if(this.checked /*&& productsAddon != null && productsAddon != ''*/) {
            //When the pre-filled box is checked.
            //if there is no previous information about the pre-filled products.
            if($(".prefill_table tbody").is(":empty")) {
                addTR();
                $('.prefill_div').show();
                $('.cpb_swap_products_field').show();
            } else {
            //if there is some previous information about the pre-filled products.
                var last_row = $('.prefill_table').find('tr:last');
                if (last_row.find("th:last").length) {
                    addTR();
                    $('.prefill_div').show();
                    $('.cpb_swap_products_field').show();
                } else {
                    $('.prefill_div').show();
                    $('.prefill_table').show();
                    $('.cpb_swap_products_field').show();
                }
            }
        } else if (!this.checked) {
            $('.prefill_table').hide();
            $('.prefill_div').hide();
            $('.cpb_swap_products_field').hide();
        }
    });


    //Adding new pair of pre-filled product.
    $('#cpb_product_data').delegate('.wdm_cpb_add', 'click', function(){
        $(this).remove();
        addTR();
        return false;
    });

     //Removing pair of pre-filled product.
    $('#cpb_product_data').delegate('.wdm_cpb_rem', 'click', function(){
        //Remove that row.
        $(this).parents('tr').remove();
        afterRemoveTr();
        return false;
    });

    //When the box quantity is changed.
    //Change the max quantity for the pre-filled products.
    $('#cpb_box_capacity').on('change', function(){
        maxVal = parseInt($(this).val(), 10);
        $('.prefill_qty .prefill_qty_id').each(function(){
            $(this).attr('max', maxVal);
        });
    });

    //When the quantity field of the pre-filled products is changed.
    //Check if the quantity of the pre-filled quantity is less than or equal the box quantity.
    $('.prefill_table').delegate('.prefill_qty_id', 'change', function(){

        maxVal = parseInt($('#cpb_box_capacity').val(), 10);
        var qty = parseInt($(this).val(), 10);

        if(qty < 0 || qty > maxVal) {
            $(this).addClass('invalid_qty');
        } else {
            $(this).removeClass('invalid_qty');
        }
    });


// Total quantity of products selected for pre filled boxes should be lesser than or equal to CPB box quantity
    $('#post').submit(function( event ){
        //Check if the product is CPB, and pre-filled box is checked.
        if ( $('#product-type').val() == 'wdm_bundle_product' && $('#cpb_enable_prefilled').is(':checked') ) {
            var total_qty = 0;
            maxVal = parseInt($('#cpb_box_capacity').val(), 10);
            var sldText = '';
            $productIds = {};
            $productDatas = {};
            $parentIds = {};
            var $validQuantities = true;
            var qtyFlag = false;
            $('.prefill_qty .prefill_qty_id').each(function(){
                //Check if the pre-filled products have correct quantities.
                //Alert the error message if they don't.
                if ($(this).val() == '' || $(this).val() == 0) {
                    $(this).addClass('invalid_qty');
                    qtyFlag = true;
                }

                var productId = $(this).closest('tr').find('.prefill_products_holder').val();
                var product_data = jQuery(this).closest('tr').find('.prefill_products_holder option:selected').data('id');
                var parent_id = jQuery(this).closest('tr').find('.prefill_products_holder option:selected').data('parent-id');
                $productIds[productId] = $(this).val();
                $productDatas[productId] = product_data;
                $parentIds[productId] = parent_id;
            });


            if (qtyFlag) {
                alert(cpb_prefilled_object.qty_greater_zero);
                event.preventDefault();
            }

            //Ajax call to check if the products selected for pre-fill
            // They can be sold individually.
            $.ajax({
                type : "POST",
                url : cpb_prefilled_object.ajax_url,  
                data : {
                    'action' : "cpb_is_sold_individual", 
                    'product_ids': $productIds,
                    'product_data': $productDatas,
                    'parent_ids': $parentIds
                },
                dataType: "json",
                async: false,
                success : function(response){
                    if(!$.isEmptyObject(response)) {
                        for (var item in response) {
                            $('.prefill_products_holder').each(function(){
                                if($(this).val() == response[item]) {
                                    sldText += "\n"+product_list[response[item]];
                                    //Reset Quantity of Sold individual product to 1
                                    $(this).closest('tr').find('.prefill_qty .prefill_qty_id').addClass('invalid_qty');
                                }
                            });
                        }

                        alert(cpb_prefilled_object.sld_ind_text + sldText);
                        event.preventDefault();
                        $validQuantities = false;
                    }
                },
                // error : function(){alert ("Sorry :( ");}
            });

            if($validQuantities) {

                $('.prefill_table .prefill_qty_id').each(function(){
                // As 0 qty can not be added to cart if set mandatory
                    if (parseInt($(this).val(), 10) <= 0) {
                        $(this).parents('tr').find('.prefill_checkbox').attr('checked', false);
                    }
                    total_qty += parseInt($(this).val(), 10);
                });

                //Do not submit form if Total Quantity is greater than Box Quantity
                if (total_qty > maxVal && $('#cpb_enable_prefilled').is(':checked')) {
                    event.preventDefault();
                    alert(cpb_prefilled_object.total_prefill_qty_text);
                }
            }

        }
    });
});