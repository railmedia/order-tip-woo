<?php
class WOO_Order_Tip_Main {

    var $views, $settings;

    function __construct() {

        $this->views = new WOO_Order_Tip_Main_Views;

        $this->settings = array();
        $settings = array(
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
        foreach( $settings as $setting ) {
            $this->settings[ $setting ] = get_option( $setting );
        }
        if( $this->settings['wc_order_tip_enabled_cart'] == 'yes' ) {
            add_action( 'woocommerce_cart_coupon', array( $this, 'tip_form' ) );
        }
        if( $this->settings['wc_order_tip_enabled_checkout'] == 'yes' ) {
            add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'tip_form' ) );
        }

        add_action( 'wp_ajax_apply_tip', array( $this, 'add_tip_to_session' ) );
        add_action( 'wp_ajax_nopriv_apply_tip', array( $this, 'add_tip_to_session' ) );
        add_action( 'wp_ajax_remove_tip', array( $this, 'remove_tip_from_session' ) );
        add_action( 'wp_ajax_nopriv_remove_tip', array( $this, 'remove_tip_from_session' ) );

        add_action( 'woocommerce_cart_calculate_fees', array( $this, 'do_add_tip' ) );
        add_action( 'woocommerce_new_order', array( $this, 'remove_tip_on_order_placed' ) );

    }

    function add_tip_to_session() {

        check_ajax_referer( 'apply_order_tip', 'security' );

        $tip = array(
            'tip'       => intval( sanitize_text_field( $_POST['tip'] ) ),
            'tip_type'  => intval( sanitize_text_field( $_POST['tip_type'] ) ),
            'tip_label' => sanitize_text_field( $_POST['tip_label'] ),
            'tip_cash'  => intval( sanitize_text_field( $_POST['tip_cash'] ) ),
            'tip_custom'=> intval( sanitize_text_field( $_POST['tip_custom'] ) )
        );

        $wc_session = WC()->session;
        $wc_session->set( 'tip', $tip );

        echo 'success';

        wp_die();

    }

    function remove_tip_from_session() {

        check_ajax_referer( 'remove_order_tip', 'security' );

        $wc_session = WC()->session;
        $wc_session->__unset( 'tip' );

        echo 'success';

        wp_die();

    }

    function tip_form() {
        wp_enqueue_style( 'woo-order-tip-css' );
        wp_enqueue_script( 'woo-order-tip-js' );
        $data = array(
            'settings' => $this->settings
        );
        echo $this->views->tip_form( $data );
    }

    function do_add_tip() {

        $wc_session = WC()->session;
        $tip = $wc_session->get('tip');

        if( $tip ) {

            if( $tip == 'custom' ) {

                $tip_amount = $tip['tip'];

            } else {

                switch( $tip['tip_type'] ) {
                    case '1':
                        //Get subtotal
                        $subtotal = WC()->cart->get_subtotal();
                        $tip_amount = ( $tip['tip'] / 100 ) * $subtotal;
                    break;
                    case '2':
                        $tip_amount = $tip['tip'];
                    break;
                }

            }

            $is_taxable = isset( $this->settings['wc_order_tip_is_taxable'] ) && $this->settings['wc_order_tip_is_taxable'] == 'yes' ? true : false;
            $tip_label = esc_html__( sprintf( 'Tip (%s)', esc_html( $tip['tip_label'] ) ), 'order-tip-woo' );

            WC()->cart->add_fee( $tip_label, number_format( $tip_amount, 2 ), $is_taxable, '' );

        }

    }

    function remove_tip_on_order_placed( $orderid ) {

        if( $this->settings['wc_order_tip_remove_new_order'] ) {
            $wc_session = WC()->session;
            $wc_session->__unset( 'tip' );
        }

    }

}
?>
