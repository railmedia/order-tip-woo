jQuery(document).ready(function() {

   if( jQuery('.woo_order_tip').length ) {
       woo_order_select_tip();
   }

   jQuery('#woo_order_tip_apply').on('click', function(e){
       e.preventDefault();
       woo_order_apply_tip( jQuery(this) );
   });

   jQuery('#woo_order_tip_remove').on('click', function(e){
       e.preventDefault();
       woo_order_remove_tip();
   });

});

function woo_order_select_tip() {

   jQuery('.woo_order_tip').on('click', function(e){

       e.preventDefault();

       var applyTip = jQuery('#woo_order_tip_apply');

       jQuery('.woo_order_tip').removeClass('active');

       jQuery(this).addClass('active');

       var tip = jQuery(this).attr('data-tip');

       if( tip == 'custom' ) {
           applyTip.show();
           jQuery('#woo_order_tip_custom_text').toggle().focus();
       } else {
           woo_order_apply_tip();
       }

       jQuery('#woo_order_tip_custom_text').on('keypress', function(e){
           if(e.keyCode == 13) {
               e.preventDefault();
               return false;
           }
       });

       jQuery('#woo_order_tip_custom_text').on('change', function(e){

           jQuery(this).val( jQuery(this).val().replace(/[^\d\.]/g, '') );

       });

   });

}

function woo_order_apply_tip( trigger ) {

   var tip       = jQuery('.woo_order_tip.active').attr('data-tip'),
       tip_type  = jQuery('.woo_order_tip.active').attr('data-tip-type'),
       tip_custom= jQuery('.woo_order_tip.active').attr('data-tip-custom'),
       tip_cash  = jQuery('.woo_order_tip.active').attr('data-tip-cash'),
       tip_label = jQuery('.woo_order_tip.active').text(),
       errors    = 0;

   if( tip == 'custom' ) {

       tip = jQuery('#woo_order_tip_custom_text').val();

       if( ! tip ) {
           jQuery('#woo_order_tip_custom_text').css('border', '1px solid red').focus();
           errors = 1;
           return false;
       } else {
           jQuery('#woo_order_tip_custom_text').css('border', 'initial');
           errors = 0;
       }

   }

   if( ! errors ) {

       jQuery('.woocommerce').block({message: ''});

       jQuery.ajax({
           type: "POST",
           url: wootip.au,
           dataType: 'html',
           data: ({action: 'apply_tip', tip: tip, tip_type: tip_type, tip_label: tip_label, tip_custom: tip_custom, tip_cash: tip_cash, security: wootip.n}),
           success: function (tipApplied) {

               if( tipApplied == 'success' ) {
                   location.reload(true);
               }

           }
       });

   }

}

function woo_order_remove_tip() {

   if( confirm( wootip.s.rtc ) === true ) {

       jQuery('.woocommerce').block({message: ''});

       jQuery.ajax({
           type: "POST",
           url: wootip.au,
           dataType: 'html',
           data: ({action: 'remove_tip', security: wootip.n2}),
           success: function (tipRemoved) {
               if( tipRemoved == 'success' ) {
                   location.reload(true);
               }
           }
       });

   }

}
