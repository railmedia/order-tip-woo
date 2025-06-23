<?php
/**
*
* Admin Notices
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WOO_Order_Tip_Admin_Notices {

    /**
    * Constructor
    **/
    function __construct() {
        // add_action('admin_notices', array( $this, 'notifications' ) );
    }

    /**
    * Adds a notification if settings are not saved after update to version 1.1
    * @since 1.0.0
    **/
    function notifications() {

    }

}
