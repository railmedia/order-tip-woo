<?php
/*
* Plugin Name: Order Tip for WooCommerce
* Plugin URI: https://order-tip-for-woocommerce.tudorache.me/
* Description: Adds a form to the cart and checkout pages where customer can add tips to the WooCommerce orders.
* Version: 1.5.1
* Author: Adrian Emil Tudorache
* Author URI: https://www.tudorache.me
* Text Domain: order-tip-woo
* Domain Path: /languages
* WC requires at least: 3.0.0
* WC tested up to: 9.6.0
* License: GPLv2 or later
*/

/**
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/


if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);

define( 'WOOTIPVER', $plugin_data['Version'] );
define( 'WOOOTIPPATH', plugin_dir_path( __FILE__ ) );
define( 'WOOOTIPBASE', plugin_basename( __FILE__ ) );
define( 'WOOOTIPURL', plugin_dir_url( __FILE__ ) );
define( 'WOOOTIPSUB', in_array( 'woocommerce-subscriptions/woocommerce-subscriptions.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? true : false );

load_plugin_textdomain( 'order-tip-woo', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    require_once( __DIR__ . '/frontend/init.php' );
    if( is_admin() ) {
        require_once( __DIR__ . '/admin/init.php' );
    }
}

require_once( __DIR__ . '/global/uninstall.php' );

function woootip_deactivate_uninstall() {
    woootip_uninstall();
    flush_rewrite_rules();
}
register_uninstall_hook( __FILE__, 'woootip_deactivate_uninstall' );

add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );
?>
