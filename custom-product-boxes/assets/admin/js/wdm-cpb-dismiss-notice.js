jQuery(document).ready(function($){
    //onclicking the notice on top of page.
    jQuery(document).on( 'click', '.wdmcpb-settings-notice .notice-dismiss', function() {

        jQuery.ajax({
            url: wdmcpb_notice_object.ajax_url,
            data: {
                action: 'wdmcpb_dismiss_settings_notice'
            }
        })
    });
});