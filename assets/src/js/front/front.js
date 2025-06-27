import $ from 'jquery';

(function($) {

    const orderTipPlacedEvent = new CustomEvent('wootipplaced');
    const orderTipRemove      = new CustomEvent('wootipremove');

    const WooOrderTip = {

        selectTip: trigger => {

            const applyTip = trigger.parent().find('.woo_order_tip_custom_text_field');
     
            jQuery('.woo_order_tip').removeClass('active');
     
            trigger.addClass('active');
     
            const tip = trigger.data('tip');
     
            if( tip == 'custom' ) {
                applyTip.toggle();
                jQuery('.woo_order_tip_apply').show();
            } else {
                WooOrderTip.applyTip( trigger );
            }
         
        },

        applyTip: trigger => {

            const container     = trigger.parents('#wooot_order_tip_form'),
                tip_type        = container.find('.woo_order_tip.active').data('tip-type'),
                tip_type_symbol = tip_type == '1' ? '%' : wootip.cs,
                tip_custom      = container.find('.woo_order_tip.active').data('tip-custom'),
                tip_cash        = container.find('.woo_order_tip.active').data('tip-cash'),
                tip_recurring   = container.find('#woo_recurring_tip').is(':checked');
            
            let errors = 0,
                tip    = container.find('.woo_order_tip.active').data('tip');

            tip = tip && 'custom' !== tip && 'number' === typeof tip ? Math.abs( tip ) : tip;

            const tip_label = tip + tip_type_symbol
            
            if( tip == 'custom' ) {

                tip = container.find('.woo_order_tip_custom_text').val();

                if( ! tip || parseInt( tip ) == 0 ) {
                    container.find('.woo_order_tip_custom_text').css('border', '1px solid red').focus();
                    errors = 1;
                    return false;
                } else {
                    container.find('.woo_order_tip_custom_text').css('border', 'initial');
                    errors = 0;
                }

            }

            if( ! errors ) {

                jQuery('.woocommerce').block({message: ''});

                jQuery.ajax({
                    type: "POST",
                    url: wootip.au,
                    dataType: 'json',
                    data: ({
                        action: 'apply_tip', 
                        tip: tip, 
                        tip_type: tip_type, 
                        tip_label: tip_label, 
                        tip_custom: tip_custom, 
                        tip_cash: tip_cash, 
                        tip_recurring: tip_recurring, 
                        security: wootip.n
                    }),
                    success: function (tipApplied) {

                        if( tipApplied.status && 'success' === tipApplied.status ) {
                            if( tip_custom ) {
                                jQuery('.woo_order_tip[data-tip="custom"]').text( wootip.s.cut + ' (' + wootip.cs + tip.replace( ',', wootip.ds ).replace( '.', wootip.ds ) + ')' );
                            }
                            jQuery('body').trigger( 'update_checkout' );
                            if( jQuery( 'button[name="update_cart"]' ).length ) {
                                jQuery( 'button[name="update_cart"]' ).attr('aria-disabled', false).removeAttr('disabled').trigger('click');
                            }
                            
                            jQuery('.woo_order_tip_remove').show();
                            jQuery('.woo_order_tip_apply').hide();
                            jQuery('.woo_order_tip_custom_text_field').hide();

                            document.dispatchEvent(orderTipPlacedEvent);

                            jQuery('.woocommerce').unblock();
                            
                        }

                    }
                });

            }

        },

        removeTip: () => {

            if( wootip.eart == '1' ) {

                if( confirm( wootip.s.rtc ) === true ) {
                    WooOrderTip.doRemoveTip();
                }
        
            } else {
                WooOrderTip.doRemoveTip();
            }

        },

        doRemoveTip: () => {

            jQuery('.woocommerce').block({message: ''});

            jQuery.ajax({
                type: "POST",
                url: wootip.au,
                dataType: 'html',
                data: ({action: 'remove_tip', security: wootip.n2}),
                success: function (tipRemoved) {

                    if( tipRemoved == 'success' ) {
                        document.dispatchEvent(orderTipRemove);
                        jQuery('.woo_order_tip[data-tip="custom"]').text( wootip.s.cut );
                        jQuery('body').trigger( 'update_checkout' );
                        jQuery('[name="update_cart"]').attr('aria-disabled', false).removeAttr('disabled').trigger('click');
                        jQuery('.woocommerce').unblock();
                        jQuery('.woo_order_tip_remove').hide();
                        jQuery('.woo_order_tip').removeClass('active');
                    }

                }

            });

        }

    }

    jQuery(function() {

        jQuery('body').on('click', '.woo_order_tip', function(evt){
            evt.preventDefault();
            WooOrderTip.selectTip( jQuery(this) );
        });

        jQuery('.woo_order_tip_custom_text').on('keypress', function(evt){
            if( evt.which == 13 ) {
                evt.preventDefault();
                return false;
            }
        });
 
        jQuery('body').on('change', '.woo_order_tip_custom_text', function(evt){
            jQuery(this).val( jQuery(this).val().replace(/[^0-9.,]/g, '') );
        });
     
        jQuery('body').on('click', '.woo_order_tip_apply', function(evt){
            evt.preventDefault();
            WooOrderTip.applyTip( jQuery(this) );
        });
     
        jQuery('body').on('change', '#woo_recurring_tip', function(evt){
            evt.preventDefault();
            WooOrderTip.applyTip( jQuery(this) );
        });
     
        jQuery('body').on('click', '.woo_order_tip_remove', function(evt){
            evt.preventDefault();
            WooOrderTip.removeTip();
        });
        
    });

})(jQuery);