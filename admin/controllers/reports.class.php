<?php
/**
*
* Admin Reports - /wp-admin/admin.php?page=wc-reports&tab=order_tip
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WOO_Order_Tip_Admin_Reports {

    /**
    * Constructor
    **/
    function __construct() {

        add_filter('woocommerce_admin_reports', array( $this, 'tip_reports' ) );

        add_action( 'wp_ajax_display_orders_list_customers', array( $this, 'display_orders_list_customers_ajax' ) );
        add_action( 'wp_ajax_nopriv_display_orders_list_customers', array( $this, 'display_orders_list_customers_ajax' ) );

    }

    /**
    * Register reports tab
    **/
    function tip_reports($reports) {

        $reports['order_tip'] = array(
            'title'   =>__('Order Tips','woocommerce'),
            'reports' => array(
                'tip' => array(
                    'title'       => __( 'Order Tips', 'order-tip-woo' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( $this, 'display_orders_list_customers' )
                )
            )
        );

        return $reports;

    }

    /**
    * Default reports view
    **/
    function display_orders_list_customers() {

        $fee_names = get_option( 'wc_order_tip_fee_names', array() );

        if( $fee_names ) {

            wp_enqueue_style( 'woo-order-tip-jqueryui' );
            wp_enqueue_style( 'woo-order-tip-admin-reports' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'woo-order-tip-admin-blockui' );
            wp_enqueue_script( 'woo-order-tip-admin-reports' );

            $after_date = date( 'Y-m-d', strtotime('-30 days') );
            $after_date = explode( '-', $after_date );

            $order_ids = array();

            $orders = new WP_Query( array(
                'post_type'      => 'shop_order',
                'posts_per_page' => 9999,
                'post_status'    => 'any',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'date_query'     => array(
                    array(
                        'after'  => array(
                            'year' => $after_date[0],
                            'month'=> $after_date[1],
                            'day'  => $after_date[2]
                        ),
                        'inclusive' => true
                    ),
                )
            ) );

            if( $orders->post_count ) {
                foreach( $orders->posts as $order ) {
                    $order = new WC_Order( $order->ID );
                    $fees  = $order->get_fees();
                    foreach( $fees as $fee ) {
                        $fee_name = $fee->get_name();
                        $fee_name = explode(' ', $fee_name);
                        $fee_name = $fee_name[0];
                        if( ! isset( $order_ids[ $order->get_id() ] ) && in_array( $fee_name, $fee_names ) ) {
                            $order_ids[ $order->get_id() ] = array(
                                'value' => floatval( $fee->get_total() ),
                                'name'  => $fee_name
                            );
                        }
                    }
                }
            }
?>
        <div id="woo-order-tip-reports">
            <div id="woo-order-tip-reports-date-range">
                <div class="wot-reports-col">
                    <label for="wot-reports-date-from">
                        <?php _e( 'From', 'order-tip-woo' ); ?>
                    </label>
                    <input type="text" id="wot-reports-date-from" placeholder="Click to choose date" value="<?php echo date( 'Y-m-d', strtotime('-30 days') ); ?>" />
                </div>
                <div class="wot-reports-col">
                    <label for="wot-reports-date-to">
                        <?php _e( 'To', 'order-tip-woo' ); ?>
                    </label>
                    <input type="text" id="wot-reports-date-to" placeholder="Click to choose date" value="<?php echo date('Y-m-d'); ?>" />
                </div>
                <div class="wot-reports-col">
                    <button id="wot-set-date-range" class="button">Search</button>
                </div>
            </div>
            <div id="woo-order-tip-reports-errors"></div>
            <p id="displaying-from-to">
                <?php
                    $date_format = get_option( 'date_format' ) . '<br/>';
                    printf(
                        'Displaying orders between %s and %s',
                        '<span id="displaying-from">' . date( $date_format, strtotime('-30 days') ) . '</span>',
                        '<span id="displaying-to">' . date( $date_format ) . '</span>'
                    );
                ?>
            </p>
            <table id="woo-order-tip-reports-table" class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Name</th>
                    <th>Value</th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $total = 0;
                    foreach( $order_ids as $order_id => $data ) {
                        $total += $data['value'];
                ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url( admin_url() ); ?>post.php?post=<?php echo esc_html( $order_id ); ?>&action=edit" target="_blank"><?php echo esc_html( $order_id ); ?></a>
                    </td>
                    <td>
                        <?php echo esc_html( $data['name'] ); ?>
                    </td>
                    <td>
                        <?php echo get_woocommerce_currency_symbol() . esc_html( number_format( $data['value'], 2 ) ); ?>
                    </td>
                </tr>
                <?php } ?>
                </thody>
                <?php if( $order_ids && $total ) { ?>
                <tfoot>
                    <td colspan="2"><strong>Total</strong></td>
                    <td><strong><?php echo get_woocommerce_currency_symbol(); ?> <span id="woo-order-tip-reports-total"><?php echo number_format( $total, 2 ); ?></span></strong></td>
                </tfoot>
                <?php } ?>
            </table>
        </div>
<?php

        } else {
?>
        <h3><?php _e( 'There are no orders with tips in the database just yet', 'order-tip-woo' ); ?></h3>
<?php
        }

    }

    /**
    * Get reports for date range through AJAX
    **/
    function display_orders_list_customers_ajax() {

        check_ajax_referer( 'reps', 'security' );

        $errors = array();

        $fee_names   = get_option( 'wc_order_tip_fee_names', array() );
        $after_date  = $_POST['from'];
        $before_date = $_POST['to'];

        if( $fee_names && $after_date && $before_date ) {

            $a_date  = explode( '-', $after_date );
            $b_date = explode( '-', $before_date );

            $order_ids = array();

            $orders = new WP_Query( array(
                'post_type'      => 'shop_order',
                'posts_per_page' => 9999,
                'post_status'    => 'any',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'date_query'     => array(
                    array(
                        'after'  => array(
                            'year' => $a_date[0],
                            'month'=> $a_date[1],
                            'day'  => $a_date[2]
                        ),
                        'before'  => array(
                            'year' => $b_date[0],
                            'month'=> $b_date[1],
                            'day'  => $b_date[2]
                        ),
                        'inclusive' => true
                    ),
                )
            ) );

            if( $orders->post_count ) {
                foreach( $orders->posts as $order ) {
                    $order = new WC_Order( $order->ID );
                    $fees  = $order->get_fees();
                    foreach( $fees as $fee ) {
                        $fee_name = $fee->get_name();
                        $fee_name = explode(' ', $fee_name);
                        $fee_name = $fee_name[0];
                        if( ! isset( $order_ids[ $order->get_id() ] ) && in_array( $fee_name, $fee_names ) ) {
                            $order_ids[ $order->get_id() ] = array(
                                'value' => floatval( $fee->get_total() ),
                                'name'  => $fee_name
                            );
                        }
                    }
                }
            } else {
                $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );
            }
?>
            <?php
                ob_start();
                $total = 0;
                foreach( $order_ids as $order_id => $data ) {
                    $total += $data['value'];
            ?>
            <tr>
                <td>
                    <a href="<?php echo esc_url( admin_url() ); ?>post.php?post=<?php echo esc_html( $order_id ); ?>&action=edit" target="_blank"><?php echo esc_html( $order_id ); ?></a>
                </td>
                <td>
                    <?php echo esc_html( $data['name'] ); ?>
                </td>
                <td>
                    <?php echo get_woocommerce_currency_symbol() . esc_html( number_format( $data['value'], 2 ) ); ?>
                </td>
            </tr>
            <?php
                }
                $result = ob_get_clean();
            ?>
<?php

        } else {

            $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );
?>
<?php
        }

        echo wp_send_json( array(
            'after_date'  => $after_date,
            'before_date' => $before_date,
            'status' => $errors ? 'error' : 'success',
            'total'  => isset( $total ) ? number_format( $total, 2 ) : 0,
            'result' => $result,
            'errors' => $errors
        ) );

        wp_die();

    }

}
?>
