<?php
class WOO_Order_Tip_Config {

    function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ), 100 );
    }

    function scripts() {
        wp_register_style( 'woo-order-tip-css', WOOOTIPURL . 'frontend/assets/css/woo-order-tip.css' );
        wp_register_script( 'woo-order-tip-js', WOOOTIPURL . 'frontend/assets/js/woo-order-tip.js', array('jquery'), null, true );
        wp_localize_script( 'woo-order-tip-js', 'wootip', array(
            'n'  => wp_create_nonce('apply_order_tip'),
            'n2' => wp_create_nonce('remove_order_tip'),
            'au' => admin_url( 'admin-ajax.php' ),
            'cs' => get_woocommerce_currency_symbol(),
            'ic' => is_cart(),
            's'  => array(
                'rtc' => __( 'Are you sure you wish to remove the tip?', 'order-tip-woo' )
            )
        ) );
    }

}
?>
