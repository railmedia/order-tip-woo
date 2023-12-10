<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function woootip_uninstall() {

    $options = array(
        'wc_order_tip_enabled_cart',
        'wc_order_tip_enabled_checkout',
        'wc_order_tip_is_taxable',
        'wc_order_tip_title',
        'wc_order_tip_type',
        'wc_order_tip_rates',
        'wc_order_tip_custom',
        'wc_order_tip_cash',
        'wc_order_tip_remove_new_order'
    );

    foreach( $options as $option ) {
        delete_option( $option );
    }

}
?>
