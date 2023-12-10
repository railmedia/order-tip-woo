jQuery(document).ready(function(){

    jQuery('#wot-reports-date-from, #wot-reports-date-to').datepicker({
        dateFormat: 'yy-mm-dd'
    });

    jQuery('body').on('click', '#wot-set-date-range', function(e){

        e.preventDefault();

        var dateFrom = jQuery('#wot-reports-date-from'),
            dateTo   = jQuery('#wot-reports-date-to'),
            errormsg = jQuery('#woo-order-tip-reports-errors'),
            containerRes = jQuery('#woo-order-tip-reports-table tbody'),
            totalRes = jQuery('#woo-order-tip-reports-table tfoot #woo-order-tip-reports-total'),
            fromRes  = jQuery('#displaying-from-to #displaying-from'),
            toRes    = jQuery('#displaying-from-to #displaying-to'),
            errors   = 0;

        if( ! dateFrom.val() ) {
            dateFrom.css('border', '1px solid red').focus();
            errors = 1;
            return false;
        } else {
            dateFrom.css('border', '1px solid #7e8993');
            errors = 0;
        }

        if( ! dateTo.val() ) {
            dateTo.css('border', '1px solid red').focus();
            errors = 1;
            return false;
        } else {
            dateTo.css('border', '1px solid #7e8993');
            errors = 0;
        }

        if( ! errors ) {

            errormsg.empty();

            jQuery('#woo-order-tip-reports').block({
                message: '',
                overlayCSS: {
                    backgroundColor: 'rgb(255,255,255)'
                }
            });

            jQuery.ajax({
                type: "POST",
                url: wootipar.aju,
                dataType: 'json',
                data: ({action: 'display_orders_list_customers', from: dateFrom.val(), to: dateTo.val(), security: wootipar.ajn}),
                success: function(data) {

                    if( data.status == 'error' ) {
                        jQuery.each( data.errors, function(i, err) {
                            errormsg.append( '<p>' + err + '</p>' );
                        });
                    } else {
                        fromRes.text( data.from );
                        toRes.text( data.to );
                        containerRes.empty().html( data.result );
                        totalRes.empty().text( data.total );
                    }

                    jQuery('#woo-order-tip-reports').unblock();
                    
                }
            });

        }

    });

});
