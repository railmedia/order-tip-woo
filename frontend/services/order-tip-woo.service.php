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

class WOO_Order_Tip_Service {

    public static function get_settings() {

        $settings = array(
            'wc_order_tip_enabled_cart',
            'wc_order_tip_cart_position',
            'wc_order_tip_enabled_checkout',
            'wc_order_tip_checkout_position',
            'wc_order_tip_is_taxable',
            'wc_order_tip_fee_name',
            'wc_order_tip_title',
            'wc_order_tip_type',
            'wc_order_tip_rates',
            'wc_order_tip_percentage_total',
            'wc_order_tip_custom',
            'wc_order_tip_custom_label',
            'wc_order_tip_display_custom_tip_label_in_tip_name',
            'wc_order_tip_custom_apply_label',
            'wc_order_tip_enter_placeholder',
            'wc_order_tip_custom_remove_label',
            'wc_order_tip_cash',
            'wc_order_tip_cash_label',
            'wc_order_tip_enable_alert_remove_tip',
            'wc_order_tip_remove_new_order',
            'wc_order_tip_woo_subscriptions'
        );
        foreach( $settings as $setting ) {
            $settings[ $setting ] = get_option( $setting );
        }

        return $settings;

    }

    /**
    * Tip form
    **/
    public static function tip_form( $settings = array() ) {

        $display_form = apply_filters( 'wc_order_tip_display_form', 1 );

        if( $display_form ) {

            wp_enqueue_style( 'woo-order-tip-css' );
            wp_enqueue_script( 'woo-order-tip-js' );

            $settings = $settings ? $settings : self::get_settings();

            include( WOOOTIPPATH . 'frontend/views/tip-form.php' );

        }

    }

    /**
    * Get tip data from session
    */
    public static function get_tip_data( $selector ) {

        $settings = self::get_settings();

        $tip_data = array();

        $wc_session = WC()->session;
        $tip = $wc_session ? $wc_session->get('tip') : array();
        
        if( ! $tip && isset( $_SESSION ) && isset( $_SESSION['tip'] ) && ! empty( $_SESSION['tip'] ) ) {
            $tip = isset( $_SESSION ) && isset( $_SESSION['tip'] ) && ! empty( $_SESSION['tip'] ) ? unserialize( sanitize_text_field( wp_unslash( $_SESSION['tip'] ) ) ) : array();
        }

        if( $tip && $selector ) {

            if( $tip == 'custom' ) {

                $tip_amount = $tip['tip'];

            } else {

                switch( $tip['tip_type'] ) {
                    case '1':
                        //Get subtotal
                        $subtotal = $selector->get_subtotal();
                        $tip_amount = ( $tip['tip'] / 100 ) * $subtotal;
                    break;
                    case '2':
                        $tip_amount = $tip['tip'];
                    break;
                }

            }

            $is_taxable = isset( $settings['wc_order_tip_is_taxable'] ) && $settings['wc_order_tip_is_taxable'] == 'yes' ? true : false;

            if( $settings['wc_order_tip_display_custom_tip_label_in_tip_name'] ) {
                $tip_label = sprintf( '%s (%s)', esc_html( $settings['wc_order_tip_fee_name'] ), esc_html( $tip['tip_label'] ) );
            } else {
                $tip_label = sprintf( '%s', esc_html( $settings['wc_order_tip_fee_name'] ) );
            }

            $recurring = false;

            if( WOOOTIPSUB && isset( $settings['wc_order_tip_woo_subscriptions'] ) ) {

                switch( $settings['wc_order_tip_woo_subscriptions'] ) {
                    case '3':
                        $recurring = true;
                    break;
                    case '4':
                        if( $tip['tip_recurring'] == true ) {
                            $recurring = true;
                        }
                    break;
                }

            }

            $tip_data = array(
                'tip_amount' => $tip_amount,
                'is_taxable' => $is_taxable,
                'tip_label'  => $tip_label,
                'recurring'  => $recurring,
                'fee_id'     => isset( $tip['fee_id'] ) ? $tip['fee_id'] : false
            );

        }

        return $tip_data;

    }

    /**
    * Remove tip when an order is placed, if this feature is enabled in the backend
    **/
    public static function remove_tip_on_order_placed( $order_id = 0 ) {

        $remove_tip = get_option( 'wc_order_tip_remove_new_order' );

        if( $remove_tip && ! is_admin() ) {

            self::remove_tip();

        }

    }

    public static function remove_tip() {

        $wc_session = WC()->session;
        if( $wc_session && $wc_session->get( 'tip' ) ) {
            $wc_session->__unset( 'tip' );
        }

        $session_tip = isset( $_SESSION ) && isset( $_SESSION['tip'] ) && ! empty( $_SESSION['tip'] ) ? unserialize( sanitize_text_field( wp_unslash( $_SESSION['tip'] ) ) ) : array();

        if( $session_tip ) {
            unset( $_SESSION['tip'] );
        }

    }

    /**
     * Get the first order's date
     */
    public static function get_first_order_date() {

        $orders = wc_get_orders( array(
            'orderby'      => 'date',
            'order'        => 'ASC',
            'type'         => 'shop_order',
            'limit'        => 1
        ) );

        return $orders ? $orders[0]->get_date_created() : '';

    }

}
?>