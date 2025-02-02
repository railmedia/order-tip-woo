<?php
/**
*
* Main Frontend
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WOO_Order_Tip_Main {

    /**
    * Frontend Views
    * @var object;
    **/
    private $views;

    /**
    * Plugin options
    * @var array;
    **/
    private $settings;

    /**
    * Constructor
    **/
    function __construct() {

        $this->settings = WOO_Order_Tip_Service::get_settings();
        
        if( $this->settings['wc_order_tip_enabled_cart'] == 'yes' && $this->settings['wc_order_tip_cart_position'] ) {

            switch( $this->settings['wc_order_tip_cart_position'] ) {
                case 'before_cart':
                    add_action( 'woocommerce_before_cart', array( $this, 'tip_form' ) );
                break;
                case 'after_coupon':
                    add_action( 'woocommerce_cart_coupon', array( $this, 'tip_form' ) );
                break;
                case 'after_cart_table':
                    add_action( 'woocommerce_after_cart_table', array( $this, 'tip_form' ) );
                break;
                case 'before_totals':
                    add_action( 'woocommerce_before_cart_totals', array( $this, 'tip_form' ) );
                break;
                case 'after_cart':
                    add_action( 'woocommerce_after_cart', array( $this, 'tip_form' ) );
                break;
            }

        }

        if( $this->settings['wc_order_tip_enabled_checkout'] == 'yes' && $this->settings['wc_order_tip_checkout_position'] ) {

            switch( $this->settings['wc_order_tip_checkout_position'] ) {
                case 'before_checkout_form':
                    add_action( 'woocommerce_before_checkout_form', array( $this, 'tip_form' ) );
                break;
                case 'before_order_notes':
                    add_action( 'woocommerce_before_order_notes', array( $this, 'tip_form' ) );
                break;
                case 'after_customer_details':
                    add_action( 'woocommerce_checkout_after_customer_details', array( $this, 'tip_form' ) );
                break;
                case 'before_order_review':
                    add_action( 'woocommerce_checkout_order_review', array( $this, 'tip_form' ) );
                break;
                case 'after_checkout_form':
                    add_action( 'woocommerce_after_checkout_form', array( $this, 'tip_form' ) );
                break;
            }

        }

        add_action( 'wp_ajax_apply_tip', array( $this, 'add_tip_to_session' ) );
        add_action( 'wp_ajax_nopriv_apply_tip', array( $this, 'add_tip_to_session' ) );
        add_action( 'wp_ajax_remove_tip', array( $this, 'remove_tip_from_session' ) );
        add_action( 'wp_ajax_nopriv_remove_tip', array( $this, 'remove_tip_from_session' ) );

        add_action( 'init', array( $this, 'init_session' ) );
        add_action( 'woocommerce_cart_calculate_fees', array( $this, 'add_tip_to_cart' ), 10, 1 );
        add_action( 'woocommerce_new_order', array( $this, 'remove_tip_on_order_placed' ) );
        add_action( 'woocommerce_thankyou', array( $this, 'remove_tip_on_order_placed' ) );

        add_shortcode( 'order_tip_form', array( $this, 'tip_form_shortcode' ) );

    }

    /**
    * Initialize the classic PHP session. The tip is stored in both PHP session and Woo session.
    **/
    function init_session() {

        if( function_exists( 'WC' ) && ! session_id() && WC()->session && WOO_Order_Tip_Service::should_use_php_session() ) {
            session_start();
        }

    }

    /**
    * Store the tip in the session
    **/
    function add_tip_to_session() {

        check_ajax_referer( 'apply_order_tip', 'security' );

        $session_tip = WOO_Order_Tip_Service::should_use_php_session() ? ( isset( $_SESSION['tip'] ) && ! empty( $_SESSION['tip'] ) ? unserialize( sanitize_text_field( wp_unslash( $_SESSION['tip'] ) ) ) : array() ) : array();

        $wc_session = WC()->session;
        if( ! $session_tip ) {
            $session_tip = $wc_session->get('tip');
        }

        $tip = array(
            'tip'           => isset( $_REQUEST['tip'] ) && ! empty( $_REQUEST['tip'] ) ? floatval( str_replace( ',', '.', sanitize_text_field( wp_unslash( $_REQUEST['tip'] ) ) ) ) : 0,
            'tip_type'      => isset( $_REQUEST['tip_type'] ) && ! empty( $_REQUEST['tip_type'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['tip_type'] ) ) ) : '',
            'tip_label'     => isset( $_REQUEST['tip_label'] ) && ! empty( $_REQUEST['tip_label'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['tip_label'] ) ) : '',
            'tip_cash'      => isset( $_REQUEST['tip_cashe'] ) && ! empty( $_REQUEST['tip_cash'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['tip_cash'] ) ) ) : 0,
            'tip_custom'    => isset( $_REQUEST['tip_custom'] ) && ! empty( $_REQUEST['tip_custom'] ) ? intval( sanitize_text_field( wp_unslash( $_REQUEST['tip_custom'] ) ) ) : 0,
            'tip_recurring' => isset( $_REQUEST['tip_recurring'] ) && ! empty( $_REQUEST['tip_recurring'] ) && 'true' === sanitize_text_field( wp_unslash( $_REQUEST['tip_recurring'] ) ) ? true : false
        );

        if( $session_tip && isset( $session_tip['active_tip_id'] ) ) {
            $tip['active_tip_id'] = $session_tip['active_tip_id'];
        }

        if( $session_tip && isset( $session_tip['active_tip_amount'] ) ) {
            $tip['active_tip_amount'] = $session_tip['active_tip_amount'];
        }

        if( $tip['tip_type'] == 2 && ! $tip['tip_cash'] && $tip['tip_custom'] ) {
            $tip['tip_label'] = get_option( 'wc_order_tip_custom_label' );
        }

        if( $tip['tip_cash'] ) {
            $tip['tip_label'] = get_option('wc_order_tip_cash_label');
        }

        if( WOO_Order_Tip_Service::should_use_php_session() ) {
            $_SESSION['tip'] = serialize( $tip );
        }

        $wc_session = WC()->session;
        $sess_customer = $wc_session->get('customer');
        if( $sess_customer ) {
            $sess_customer['tip'] = $tip;
            $wc_session->set( 'tip', $tip );
        }

        wp_send_json( array(
            'tip' => $session_tip,
            'status' => 'success'
        ) );

        wp_die();

    }

    /**
    * Remove the tip from the session
    **/
    function remove_tip_from_session() {

        check_ajax_referer( 'remove_order_tip', 'security' );

        $wc_session = WC()->session;
        $wc_session->__unset( 'tip' );

        if( WOO_Order_Tip_Service::should_use_php_session() && isset( $_SESSION['tip'] ) ) {
            unset( $_SESSION['tip'] );
        }

        echo 'success';

        wp_die();

    }

    /**
    * Tip form shortcode callback
    **/
    function tip_form_shortcode() {
        ob_start();
        $this->tip_form();
        return ob_get_clean();
    }

    /**
    * Tip form
    **/
    function tip_form() {
        echo WOO_Order_Tip_Service::tip_form( $this->settings );
    }

    /**
    * Add tip action
    **/
    function add_tip_to_cart( $cart ) {

        $tip_data = WOO_Order_Tip_Service::get_tip_data( $cart );

        if( $tip_data && ( $cart || WC()->cart ) ) {

            $object = $cart;

            if( true != $tip_data['recurring']  ) {
                $object = WC()->cart;
            }

            $object->add_fee( $tip_data['tip_label'], $tip_data['tip_amount'], $tip_data['is_taxable'], '' );

        }

    }

    /**
    * Remove tip when an order is placed, if this feature is enabled in the backend
    **/
    function remove_tip_on_order_placed( $orderid ) {

        if( $this->settings['wc_order_tip_remove_new_order'] && ! is_admin() ) {

            $wc_session = WC()->session;
            if( $wc_session && $wc_session->get( 'tip' ) ) {
                $wc_session->__unset( 'tip' );
            }

            if( WOO_Order_Tip_Service::should_use_php_session() ) {
            
                $session_tip = isset( $_SESSION ) && isset( $_SESSION['tip'] ) && ! empty( $_SESSION['tip'] ) ? unserialize( sanitize_text_field( wp_unslash( $_SESSION['tip'] ) ) ) : array();

                if( $session_tip ) {
                    unset( $_SESSION['tip'] );
                }
                
            }

        }

    }

}
?>
