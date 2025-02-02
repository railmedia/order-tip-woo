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
        add_filter( 'plugin_action_links', array( $this, 'plugin_action_links' ), 10, 2 );
    }

    /**
    * Register and load assets
    * @since 1.0.0
    **/
    function scripts() {

        $first_order_date = WOO_Order_Tip_Service::get_first_order_date();
        $date             = new DateTime();

        wp_register_style( 'woo-order-tip-admin-reports', WOOOTIPURL . 'assets/css/adminReports.css', array(), WOOTIPVER );
        wp_register_script( 'woo-order-tip-admin-reports', WOOOTIPURL . 'assets/build/adminReports.bundle.js', array('jquery'), WOOTIPVER, true );
        wp_localize_script( 'woo-order-tip-admin-reports', 'wootipar', array(
            'aju' => admin_url( 'admin-ajax.php' ),
            'ajn' => wp_create_nonce('reps'),
            'erc' => wp_create_nonce('export-report-to-csv'),
            'def' => wp_create_nonce('delete-exported-file'),
            'fod' => $first_order_date ? $first_order_date->format('Y') : '',
            'cuy' => $date->format('Y'),
            'exn' => esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-reports&tab=order_tip&a=export&from=fromDate&to=toDate&fees=Fees' ), 'export-report-to-csv', 'wootip_export_nonce' ) )
        ) );

    }

    /**
    * Add row links to the plugins screen, along with the Deactivate link
    * @since 1.2.0
    **/
    function plugin_action_links( $plugin_actions, $plugin_file ) {

        $new_actions = array();

        if ( $plugin_file == 'order-tip-woo/order-tip-for-woocommerce.php' ) {
            $new_actions['order_tip_settings'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=wc-settings&tab=order_tip' ) ), __( 'Settings', 'order-tip-woo' ) );
            $new_actions['order_tip_reports'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'admin.php?page=wc-settings&tab=order_tip&section=reports' ) ), __( 'Tip Reports', 'order-tip-woo' ) );
        }

        return array_merge( $new_actions, $plugin_actions );

    }

}
?>
