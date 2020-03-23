jQuery( document ).ready( function() {
	// alert("HI");
	//Get the selected layout.
	if (cpb_layout_object.selectedLayout == 'horizontal') {
		hideVerticalFields();
	} else if (cpb_layout_object.selectedLayout == 'vertical' || cpb_layout_object.selectedLayout == 'vertical-right') {
		hideHorizontalFields();
	}

	jQuery( 'input#_wdm_gift_boxes_color' ).alphaColorPicker();
	jQuery( 'input#_wdm_gift_bgcolor' ).alphaColorPicker();
	//Hide or show the layout specific fields based on selected layouts.
	jQuery('#_wdm_desktop_layout').change(function(){
		var changedLayoutPath = jQuery(this).val();
		var temp = changedLayoutPath.split("/");
		var changedLayout = temp[temp.length - 1];

		if (changedLayout == 'vertical') {
			showVerticalFields();
			hideHorizontalFields();
		} else if (changedLayout == 'vertical-right') {
			showVerticalFields();
			hideHorizontalFields();
		} else if (changedLayout == 'horizontal') {
			showHorizontalFields();
			hideVerticalFields()		;
		}
	});
	/**
	* Hides the horizontal fields.
	*/
	function hideHorizontalFields()
	{
		jQuery('#_wdm_product_item_grid').hide();
		jQuery('#_wdm_item_field').hide();
		jQuery('._wdm_product_item_grid_field').hide();
		jQuery('._wdm_item_field_field').hide();
		jQuery('._wdm_gift_bgcolor_field').hide();
	}
	/**
	* Shows the horizontal fields.
	*/
	function showHorizontalFields()
	{
		jQuery('#_wdm_product_item_grid').show();
		jQuery('#_wdm_item_field').show();
		jQuery('._wdm_product_item_grid_field').show();
		jQuery('._wdm_item_field_field').show();	
		jQuery('._wdm_gift_bgcolor_field').show();
	}
	/**
	* Hides the vertical fields.
	*/
	function hideVerticalFields()
	{
		jQuery('#cpb_product_column_size').hide();
		jQuery('#cpb_box_column_size').hide();
		jQuery('._wdm_column_field_field').hide();
		jQuery('._wdm_product_grid_field').hide();
	}
	/**
	* Shows the vertical fields.
	*/
	function showVerticalFields()
	{
		jQuery('#cpb_product_column_size').show();
		jQuery('#cpb_box_column_size').show();
		jQuery('._wdm_column_field_field').show();
		jQuery('._wdm_product_grid_field').show();
	}

});