jQuery(document).ready(function($){
    // Trigger WooCommerce Tooltips. This is used to trigger tooltips added by function \wc_help_tip
    var tiptip_args = {
        'attribute': 'data-tip',
        'fadeIn': 50,
        'fadeOut': 50,
        'delay': 200
    };

    jQuery('.tips, .help_tip, .woocommerce-help-tip').tipTip(tiptip_args);
    
    if (jQuery('#_wdmcpb_anonymize_mgs').is(':disabled')) {
        jQuery('.wdmcpb_labels').hide();
    }

    if (jQuery('#_wdmcpb_anonymize_mgs').is(':checked') && !jQuery('table#wdm_privacy tr.wdmcpb_labels').hasClass('wdmcpb_labels_hide')) {
        jQuery('table#wdm_privacy tr.wdmcpb_labels').show();
    } else {
        jQuery('table#wdm_privacy tr.wdmcpb_labels').hide();
    }

	if (jQuery('#_wdm_enable_addbox_total').is(':checked')) {
		jQuery('table#cpb-settings-table tr.add_box_charges_hide').show();
	} else {
		jQuery('table#cpb-settings-table tr.add_box_charges_hide').hide();
	}

	if (jQuery('#_wdm_enable_giftbox_total').is(':checked')) {
		jQuery('table#cpb-settings-table tr.gift_box_total_hide').show();
	} else {
		jQuery('table#cpb-settings-table tr.gift_box_total_hide').hide();
	}

	//When the 
    $('#cpb-settings-table').delegate('#_wdm_enable_addbox_total', 'change', function(){
    	if (jQuery(this).is(':checked')) {
			jQuery('table#cpb-settings-table tr.add_box_charges_hide').show();
    	} else {
			jQuery('table#cpb-settings-table tr.add_box_charges_hide').hide();
    	}
    });

    $('#cpb-settings-table').delegate('#_wdm_enable_giftbox_total', 'change', function(){
    	if (jQuery(this).is(':checked')) {
			jQuery('table#cpb-settings-table tr.gift_box_total_hide').show();
    	} else {
			jQuery('table#cpb-settings-table tr.gift_box_total_hide').hide();
    	}
    });

    $('#wdm_privacy').delegate('#_wdmcpb_anonymize_mgs', 'change', function(){
        if (jQuery(this).is(':checked') && !jQuery('table#wdm_privacy tr.wdmcpb_labels').hasClass('wdmcpb_labels_hide')) {
            jQuery('table#wdm_privacy tr.wdmcpb_labels').show();
        } else {
            jQuery('table#wdm_privacy tr.wdmcpb_labels').hide();
        }
    });
});