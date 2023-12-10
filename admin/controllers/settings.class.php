<?php
class WOO_Order_Tip_Admin_Settings {

    function __construct() {

        add_filter( 'woocommerce_settings_tabs_array', array( $this, 'settings_tabs' ), 50 );
        add_action( 'woocommerce_settings_tabs_order_tip', array( $this, 'settings_tab_options' ) );
        add_action( 'woocommerce_update_options_order_tip', array( $this, 'update_settings' ) );
        add_filter( 'plugin_action_links_'  . WOOOTIPBASE, array($this, 'settings_link_plugins_screen') );

    }

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

    function settings_tabs( $settings_tabs ) {
        $settings_tabs['order_tip'] = __( 'Order Tip', 'order-tip-woo' );
        return $settings_tabs;
    }

    function settings_tab_options() {
        woocommerce_admin_fields( $this->get_settings_tab_options() );
    }

    function update_settings() {
        woocommerce_update_options( $this->get_settings_tab_options() );
    }

    function get_settings_tab_options() {

        $settings = array(
            'section_title' => array(
                'name'     => __( 'Order Tip Settings', 'order-tip-woo' ),
                'type'     => 'title',
                'desc'     => '',
                'id'       => 'wc_order_tip_section_title'
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
            'enabled_checkout' => array(
                'name'     => __( 'Enabled on Checkout page', 'order-tip-woo' ),
                'type'     => 'checkbox',
                'desc'     => __( 'If checked, the tip form will appear under the Checkout form on the Checkout page', 'order-tip-woo' ),
                'desc_tip' => true,
                'default'  => 1,
                'label'    => __( 'Enable', 'order-tip-woo' ),
                'id'       => 'wc_order_tip_enabled_checkout'
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
            'section_end' => array(
                 'type' => 'sectionend',
                 'id' => 'wc_order_tip_section_end'
            )
        );

        return apply_filters( 'wc_order_tip_settings', $settings );

    }

}
?>
