(function($){

    const WOOTAdminReports = {

        init: function() {

            jQuery('#wot-reports-date-from, #wot-reports-date-to').datepicker({
                dateFormat: 'yy-mm-dd'
            });

            jQuery('p.submit').remove();

        },

        onSetFilters: function() {

            var dateFrom = jQuery('#wot-reports-date-from'),
                dateTo   = jQuery('#wot-reports-date-to'),
                status   = jQuery('#wot-reports-order-status'),
                feeNames = jQuery('#wot-reports-order-fees input:checked'),
                errormsg = jQuery('#woo-order-tip-reports-errors'),
                containerRes = jQuery('#woo-order-tip-reports-table tbody'),
                totalRes = jQuery('#woo-order-tip-reports-table tfoot #woo-order-tip-reports-total'),
                fromRes  = jQuery('#displaying-from-to #displaying-from'),
                toRes    = jQuery('#displaying-from-to #displaying-to'),
                errors   = WOOTAdminReports.validateDates();
    
            if( ! errors ) {
    
                errormsg.empty();
    
                jQuery('#woo-order-tip-reports').block({
                    message: '',
                    overlayCSS: {
                        backgroundColor: 'rgb(255,255,255)'
                    }
                });
    
                var queryFeeNames = [];
                feeNames.map(function(index, fee){
                    if( ! queryFeeNames.includes( fee.value ) ) {
                        queryFeeNames.push( fee.value );
                    }
                });
    
                jQuery.ajax({
                    type: "POST",
                    url: wootipar.aju,
                    dataType: 'json',
                    data: ({
                        action: 'display_orders_list_reports_ajax', 
                        from: dateFrom.val(), 
                        to: dateTo.val(), 
                        feeNames: queryFeeNames,
                        status: status.val(), 
                        security: wootipar.ajn
                    }),
                    success: function(data) {
    
                        if( data.status == 'error' ) {
                            jQuery.each( data.errors, function(i, err) {
                                errormsg.append( '<p>' + err + '</p>' );
                            });
                        } else {
                            fromRes.text( data.after_date );
                            toRes.text( data.before_date );
                            containerRes.empty().html( data.result );
                            totalRes.empty().text( data.total );
                        }
    
                        jQuery('#woo-order-tip-reports').unblock();
    
                    }
                });
    
            }

        },

        onFiltersChange: function(){

            var dateFrom = jQuery('#wot-reports-date-from'),
                dateTo   = jQuery('#wot-reports-date-to'),
                feeNames = jQuery('#wot-reports-order-fees input:checked'),
                expButton= jQuery('#wot-export-csv'),
                errors   = WOOTAdminReports.validateDates();
    
            var queryFeeNames = [];

            feeNames.map(function(index, fee){
                if( ! queryFeeNames.includes( fee.value ) ) {
                    queryFeeNames.push( fee.value );
                }
            });
    
            if( ! errors ) {

                var url = wootipar.exn;

                url = url.replace( 'fromDate', dateFrom.val() );
                url = url.replace( 'toDate', dateTo.val() );
                url = url.replace( 'Fees', queryFeeNames.join(',') );

                expButton.removeAttr('disabled').attr('href', url);

            } else {
                expButton.attr('disabled', 'disabled').attr('href', '#!');
            }

        },

        validateDates: function() {
        
            var dateFrom = jQuery('#wot-reports-date-from'),
                dateTo   = jQuery('#wot-reports-date-to'),
                errors   = 0;

            if( ! dateFrom.val() ) {
                dateFrom.css('border', '1px solid red').focus();
                errors = 1;
                return errors;
            } else {
                dateFrom.css('border', '1px solid #7e8993');
                errors = 0;
            }

            if( ! dateTo.val() ) {
                dateTo.css('border', '1px solid red').focus();
                errors = 1;
                return errors;
            } else {
                dateTo.css('border', '1px solid #7e8993');
                errors = 0;
            }

            return errors;

        }

    };

    jQuery(function(){

        WOOTAdminReports.init();

        jQuery('body').on('click', '#wot-set-filters', function(evt){
            evt.preventDefault();
            WOOTAdminReports.onSetFilters();
        });

        jQuery('body').on('change', '#wot-reports-date-from, #wot-reports-date-to, #wot-reports-order-fees input', function(evt){
            WOOTAdminReports.onFiltersChange();
        });

        jQuery('label[for="wot-reports-order-fees"]').on('click', function(evt){
            jQuery('#wot-reports-order-fees').slideToggle()
        });

    });

})(jQuery);