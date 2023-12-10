<?php
/**
*
* Admin Config
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WOO_Order_Tip_Admin_Config {

    /**
    * Constructor
    **/
    function __construct() {
        add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 100 );
    }

    /**
    * Register and load assets
    **/
    function scripts() {

        wp_register_style( 'woo-order-tip-jqueryui', WOOOTIPURL . 'admin/assets/css/jqueryui-1.12.1/jquery-ui.min.css' );
        wp_register_style( 'woo-order-tip-admin-reports', WOOOTIPURL . 'admin/assets/css/woo-order-tip-admin-reports.css' );
        wp_register_script( 'woo-order-tip-admin-blockui', WOOOTIPURL . 'admin/assets/js/jquery.blockUI.js', array('jquery'), null, true );
        wp_register_script( 'woo-order-tip-admin-reports', WOOOTIPURL . 'admin/assets/js/woo-order-tip-admin-reports.js', array('jquery'), null, true );
        wp_localize_script( 'woo-order-tip-admin-reports', 'wootipar', array(
            'au'  => admin_url(),
            'aju' => admin_url( 'admin-ajax.php' ),
            'ajn' => wp_create_nonce('reps')
        ) );

        if(
            isset( $_GET['page'] ) && $_GET['page'] == 'wc-settings' &&
            isset( $_GET['tab'] ) && $_GET['tab'] == 'order_tip'
        ) {
            wp_enqueue_script( 'woo-order-tip-admin', WOOOTIPURL . 'admin/assets/js/woo-order-tip-admin.js', array('jquery'), null, true );
        }

    }

}
?>
