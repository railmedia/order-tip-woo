<?php
/**
*
* Admin Settings - /wp-admin/admin.php?page=wc-settings&tab=order_tip
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WOO_Order_Tip_Admin_Settings {

    /**
    * Constructor
    **/
    function __construct() {

        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'settings_tabs' ), 50 );
        add_action( 'woocommerce_settings_tabs_order_tip', array( $this, 'settings_tab_options' ) );
        add_action( 'woocommerce_update_options_order_tip', array( $this, 'update_settings' ) );
        add_filter( 'plugin_action_links_'  . WOOOTIPBASE, array($this, 'settings_link_plugins_screen') );
        add_action( 'admin_init', array( $this, 'save_tip_names' ) );

    }

    /**
    * Save tip names for the order reports. Useful in case the tip name is changed by the admin over time.
    **/
    function save_tip_names() {

        $fee_name = get_option( 'wc_order_tip_fee_name' );
        if( $fee_name ) {
            $fee_names = get_option( 'wc_order_tip_fee_names', array() );
            if( ! in_array( $fee_name, $fee_names ) ) {
                $fee_names[] = $fee_name;
            }
            update_option( 'wc_order_tip_fee_names', $fee_names );
        }

    }

    /**
    * Create and add the settings tab to the WooCommerce Admin
    **/
    function settings_link_plugins_screen( $links ) {

		$the_links = array();

		$settings = esc_url( add_query_arg(
			array(
                'page' => 'wc-settings',
                'tab'  => 'order_tip'
            ),
			get_admin_url() . 'admin.php'
		) );

		$the_links[] = '<a href=' . $settings . '>' . __( 'Settings', 'order-tip-woo' ) . '</a>';

		foreach( $the_links as $the_link ) {
			array_push( $links, $the_link );
		}

		return $links;
	}

    /**
    * Declare the settings tab in the WooCommerce admin
    **/
    function settings_tabs( $settings_tabs ) {
        $settings_tabs['order_tip'] = __( 'Order Tip', 'order-tip-woo' );
        return $settings_tabs;
    }

    /**
    * Initialize tab options
    **/
    function settings_tab_options() {
        woocommerce_admin_fields( $this->get_settings_tab_options() );
    }

    /**
    * Update admin settings when they are saved
    **/
    function update_settings() {
        woocommerce_update_options( $this->get_settings_tab_options() );
    }

    /**
    * Create admin settings fields
    **/
    function get_settings_tab_options() {

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Order Tip Settings', 'order-tip-woo' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_order_tip_section_title'
            ),
            'section_description' => array(
                'name'     => __( 'Shortcode', 'order-tip-woo' ),
                'type'     => 'title',
                'desc'     => sprintf( __( 'You can use the %1$s[order_tip_form]%2$s shortcode in any page, post or widget. It will automatically take the settings below. However, we %1$sdo not%2$s recommend to use the shortcode on the cart or checkout pages if you enable the tip form using the checkboxes below.', 'order-tip-woo' ), '<strong>', '</strong>' ),
                'id'       => 'wc_order_tip_shortcode'
            ),
            'enabled_cart' => array(
                'name'     => __( 'Enabled on Cart page', 'order-tip-woo' ),
                'type'     => 'checkbox',
                'desc'     => __( 'If checked, the tip form will appear under the Apply Coupon form on the Cart page', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => 1,
                'label'    => __( 'Enable', 'order-tip-woo' ),
                'id'       => 'wc_order_tip_enabled_cart'
            ),
            'cart_position' => array(
                'name'    => __( 'Select position on the cart page', 'order-tip-woo' ),
                'type'     => 'select',
                'desc'     => __( 'Select the position on the cart page. Please make sure to enable the tip form using the checkbox above.', 'order-tip-woo' ),
                'desc_tip' => true,
                'options'  => array(
                    'before_cart'  => __( 'Before the cart', 'order-tip-woo' ),
                    'after_coupon' => __( 'After the coupon and before the Update cart button', 'order-tip-woo' ),
                    'after_cart_table' => __( 'After the cart table', 'order-tip-woo' ),
                    'before_totals'=> __( 'Before cart totals', 'order-tip-woo' ),
                    'after_cart'   => __( 'After the cart', 'order-tip-woo' ),
                ),
                'id'       => 'wc_order_tip_cart_position'
            ),
            'enabled_checkout' => array(
                'name'     => __( 'Enabled on Checkout page', 'order-tip-woo' ),
                'type'     => 'checkbox',
                'desc'     => __( 'If checked, the tip form will appear under the Checkout form on the Checkout page', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => 1,
                'label'    => __( 'Enable', 'order-tip-woo' ),
                'id'       => 'wc_order_tip_enabled_checkout'
            ),
            'checkout_position' => array(
                'name'    => __( 'Select position on the checkout page', 'order-tip-woo' ),
                'type'     => 'select',
                'desc'     => __( 'Select the position on the checkout page. Please make sure to enable the tip form using the checkbox above.', 'order-tip-woo' ),
                'desc_tip' => true,
                'options'  => array(
                    'before_checkout_form'  => __( 'Before the checkout form', 'order-tip-woo' ),
                    'before_order_notes'    => __( 'Before the order notes field', 'order-tip-woo' ),
                    'after_customer_details'=> __( 'After customer details', 'order-tip-woo' ),
                    'before_order_review'   => __( 'Before the order review', 'order-tip-woo' ),
                    'after_checkout_form'   => __( 'After the checkout form', 'order-tip-woo' )
                ),
                'id'       => 'wc_order_tip_checkout_position'
            ),
            'is_taxable' => array(
                'name'     => __( 'Is taxable', 'order-tip-woo' ),
                'type'     => 'checkbox',
                'desc'     => __( 'If checked, the tip amount will be taxed as per your WooCommerce Tax settings.', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => 1,
                'label'    => __( 'Enable', 'order-tip-woo' ),
                'id'       => 'wc_order_tip_is_taxable'
            ),
            'fee_name'        => array(
                'name'     => __( 'Tip fee name', 'order-tip-woo' ),
                'type'     => 'text',
                'desc'     => __( 'The tip fee name will appear before the order total. It will always be followed by the tip amount. Default format is "Tip (AMOUNT)"', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => 'Tip',
                'label'    => __( 'Enable', 'order-tip-woo' ),
                'id'       => 'wc_order_tip_fee_name'
            ),
            'title'        => array(
                'name'     => __( 'Tip form title', 'order-tip-woo' ),
                'type'     => 'text',
                'desc'     => __( 'The tip form title will appear before the tip form', 'order-tip-woo' ),
                'desc_tip' => true,
                'label'    => __( 'Enable', 'order-tip-woo' ),
                'id'       => 'wc_order_tip_title'
            ),
            'type'         => array(
                'name'     => __( 'Tip Type', 'order-tip-woo' ),
                'type'     => 'select',
                'options'  => array(
                    '1'    => __( 'Percent of the order total', 'order-tip-woo' ),
                    '2'    => __( 'Fixed amount', 'order-tip-woo' )
                ),
                'id'       => 'wc_order_tip_type',
                'desc'     => __( 'Select the type of tip you would like to use.', 'order-tip-woo' ),
                'desc_tip' => true
            ),
            'rates' => array(
                'name'     => __( 'Tip Rates', 'order-tip-woo' ),
                'type'     => 'multiselect',
                'css'      => 'min-height:120px',
                'options'  => array(
                    '5'    => '5',
                    '10'   => '10',
                    '15'   => '15',
                    '20'   => '20',
                    '25'   => '25',
                    '30'   => '30'
                ),
                'id'       => 'wc_order_tip_rates',
                'desc'     => __( 'Enable various tip rates. Keep CTRL or CMD key pressed while selecting.', 'order-tip-woo' ),
                'desc_tip' => true
            ),
            'custom_tip'   => array(
                'name'     => __( 'Enable custom tip field', 'order-tip-woo' ),
                'type'     => 'select',
                'options'  => array(
                    '1'    => __( 'Yes', 'order-tip-woo' ),
                    '0'    => __( 'No', 'order-tip-woo' )
                ),
                'id'       => 'wc_order_tip_custom',
                'desc'     => __( 'If enabled, the customer will be able to add their own fixed amount tip.', 'order-tip-woo' ),
                'desc_tip' => true
            ),
            'custom_tip_label' => array(
                'name'     => __( 'Custom tip field label', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_custom_label',
                'desc'     => __( 'Set a label of your choice for the custom tip button. The default label is "Custom Tip"', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => __( 'Custom Tip', 'order-tip-woo' )
            ),
            'custom_tip_apply_label' => array(
                'name'     => __( 'Custom tip apply tip button label', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_custom_apply_label',
                'desc'     => __( 'Set a label of your choice for the custom tip apply button. The default label is "Add tip to order"', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => __( 'Add tip to order', 'order-tip-woo' )
            ),
            'custom_tip_enter_placeholder' => array(
                'name'     => __( 'Custom tip enter tip input placeholder label', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_enter_placeholder',
                'desc'     => __( 'Set a placeholder of your choice for the custom enter tip input field. The default label is "Enter tip amount"', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => __( 'Enter tip amount', 'order-tip-woo' )
            ),
            'custom_tip_remove_label' => array(
                'name'     => __( 'Custom tip remove tip button label', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_custom_remove_label',
                'desc'     => __( 'Set a label of your choice for the custom tip remove button. The default label is "Remove tip"', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => __( 'Remove tip', 'order-tip-woo' )
            ),
            'cash'         => array(
                'name'     => __( 'Enable cash tip', 'order-tip-woo' ),
                'type'     => 'select',
                'options'  => array(
                    '1'    => __( 'Yes', 'order-tip-woo' ),
                    '0'    => __( 'No', 'order-tip-woo' )
                ),
                'id'       => 'wc_order_tip_cash',
                'desc'     => __( 'If enabled, customers will be able to choose to tip by cash (on delivery or local pickup).', 'order-tip-woo' ),
                'desc_tip' => true
            ),
            'cash_label'   => array(
                'name'     => __( 'Cash tip field label', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_cash_label',
                'desc'     => __( 'Set a label of your choice for the cash tip button. The default label is "Cash"', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => __( 'Cash', 'order-tip-woo' )
            ),
            'remove_on_placed_order' => array(
                'name'     => __( 'Clear tip after the order has been placed', 'order-tip-woo' ),
                'type'     => 'select',
                'options'  => array(
                    '1'    => __( 'Yes', 'order-tip-woo' ),
                    '0'    => __( 'No', 'order-tip-woo' )
                ),
                'id'       => 'wc_order_tip_remove_new_order',
                'desc'     => __( 'If enabled, the tip that may be added to the cart, will be removed. Otherwise, it will be preserved on future orders in the current session.', 'order-tip-woo' ),
                'desc_tip' => true
            ),
            'remove_confirm_msg' => array(
                'name'     => __( 'Remove tip confirmation message', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_remove_confirm_msg',
                'desc'     => __( 'Set a message of your choice for the tip removal confirmation message.', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => __( 'Are you sure you wish to remove the tip?', 'order-tip-woo' )
            ),
            'updated_to_1_1' => array(
                'name'     => __( 'Updated to 1.1', 'order-tip-woo' ),
                'type'     => 'text',
                'id'       => 'wc_order_tip_updated_1_1',
                'default'  => '1'
            ),
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_order_tip_section_end'
            )
        );

        return apply_filters( 'wc_order_tip_settings', $settings );

    }

}
?>
