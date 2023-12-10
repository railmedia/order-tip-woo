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
    * @var string
    **/
    private $date_format;

    /**
    * @var array
    **/
    private $fee_names;

    /**
    * Constructor
    **/
    function __construct() {

        $this->date_format = get_option( 'date_format' );

        $this->fee_names   = get_option( 'wc_order_tip_fee_names', array() );
        $this->fee_names[] = 'Tip'; //Backward compatibility with previously added tips

        add_filter('woocommerce_admin_reports', array( $this, 'tip_reports' ) );

        $ajax = array(
            'display_orders_list_customers_ajax',
        );
        foreach( $ajax as $ajax ) {
            add_action( 'wp_ajax_' . $ajax, array( $this, $ajax ) );
            add_action( 'wp_ajax_nopriv_' . $ajax, array( $this, $ajax ) );
        }

        add_action( 'admin_init', array($this, 'export_tips_to_csv') );

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

        if( $this->fee_names ) {

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
                        if( ! isset( $order_ids[ $order->get_id() ] ) && in_array( $fee_name, $this->fee_names ) ) {
                            $order_ids[ $order->get_id() ] = array(
                                'date'     => $order->get_date_created(),
                                'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                                'value'    => floatval( $fee->get_total() ),
                                'type'     => $fee_name
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
                <div class="wot-reports-col">
                    <a id="wot-export-csv" href="<?php echo esc_url( admin_url() ) . 'admin.php?page=wc-reports&tab=order_tip&a=export&from=' . date( 'Y-m-d', strtotime('-30 days') ) . '&to=' . date('Y-m-d'); ?>" class="button"><?php _e( 'Export to CSV', 'order-tip-woo' ); ?></a>
                </div>
            </div>
            <div id="woo-order-tip-reports-errors"></div>
            <p id="displaying-from-to">
                <?php
                    printf(
                        __( 'Displaying orders between %s and %s', 'order-tip-woo' ),
                        '<span id="displaying-from">' . date( $this->date_format, strtotime('-30 days') ) . '</span>',
                        '<span id="displaying-to">' . date( $this->date_format ) . '</span>'
                    );
                ?>
            </p>
            <table id="woo-order-tip-reports-table" class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                <tr>
                    <th><?php _e( 'Order ID', 'order-tip-woo' ); ?></th>
                    <th><?php _e( 'Customer', 'order-tip-woo' ); ?></th>
                    <th><?php _e( 'Type', 'order-tip-woo' ); ?></th>
                    <th><?php _e( 'Value', 'order-tip-woo' ); ?></th>
                    <th><?php _e( 'Date/Time', 'order-tip-woo' ); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                    $total = 0;
                    foreach( $order_ids as $order_id => $data ) {
                        $total += $data['value'];
                        $date = $data['date'];
                        $date_format = str_split( $this->date_format );
                        if( ! in_array( array( 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v' ), $date_format ) ) {
                            $date_format = apply_filters( 'wc_order_tip_reports_date_time_format', implode( '', $date_format ) . ' H:i:s' );
                        }
                ?>
                <tr>
                    <td>
                        <a href="<?php echo esc_url( admin_url() ); ?>post.php?post=<?php echo esc_html( $order_id ); ?>&action=edit" target="_blank"><?php echo esc_html( $order_id ); ?></a>
                    </td>
                    <td>
                        <?php echo esc_html( $data['customer'] ); ?>
                    </td>
                    <td>
                        <?php echo esc_html( $data['type'] ); ?>
                    </td>
                    <td>
                        <?php echo get_woocommerce_currency_symbol() . esc_html( number_format( $data['value'], 2 ) ); ?>
                    </td>
                    <td>
                        <?php echo esc_html( date( $date_format, strtotime( $data['date'] ) ) ); ?>
                    </td>
                </tr>
                <?php } ?>
                </tbody>
                <?php if( $order_ids && $total ) { ?>
                <tfoot>
                    <td colspan="3"><strong><?php _e( 'Total', 'order-tip-woo' ); ?></strong></td>
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

        $after_date  = $_POST['from'];
        $before_date = $_POST['to'];

        if( $this->fee_names && $after_date && $before_date ) {

            $order_ids = $this->get_filtered_order_tips( $this->fee_names, $after_date, $before_date );

            if( $order_ids['order_ids'] && ! $order_ids['errors'] ) {
                ob_start();
                $total = 0;
                foreach( $order_ids['order_ids'] as $order_id => $data ) {
                    $total += $data['value'];
                    $date = $data['date'];
                    $date_format = str_split( $this->date_format );
                    if( ! in_array( array( 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v' ), $date_format ) ) {
                        $date_format = apply_filters( 'wc_order_tip_reports_date_time_format', implode( '', $date_format ) . ' H:i:s' );
                    }
            ?>
            <tr>
                <td>
                    <a href="<?php echo esc_url( admin_url() ); ?>post.php?post=<?php echo esc_html( $order_id ); ?>&action=edit" target="_blank"><?php echo esc_html( $order_id ); ?></a>
                </td>
                <td>
                    <?php echo esc_html( $data['customer'] ); ?>
                </td>
                <td>
                    <?php echo esc_html( $data['type'] ); ?>
                </td>
                <td>
                    <?php echo get_woocommerce_currency_symbol() . esc_html( number_format( $data['value'], 2 ) ); ?>
                </td>
                <td>
                    <?php echo esc_html( date( $date_format, strtotime( $data['date'] ) ) ); ?>
                </td>
            </tr>
            <?php
                }
                $result = ob_get_clean();
            }

        } else {

            $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );
?>
<?php
        }

        echo wp_send_json( array(
            'after_date_raw'  => $after_date,
            'before_date_raw' => $before_date,
            'after_date'      => date( $this->date_format, strtotime( $after_date ) ),
            'before_date'     => date( $this->date_format, strtotime( $before_date ) ),
            'status'          => $errors ? 'error' : 'success',
            'total'           => isset( $total ) ? number_format( $total, 2 ) : 0,
            'result'          => $result,
            'errors'          => $errors
        ) );

        wp_die();

    }

    /**
    * Get filtered orders
    **/
    function get_filtered_order_tips( $fee_names, $after_date, $before_date ) {

        if( ! $fee_names || ! $after_date || ! $before_date ) return;

        $errors = $order_ids = array();

        $a_date = explode( '-', $after_date );
        $b_date = explode( '-', $before_date );

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
                            'date'     => $order->get_date_created(),
                            'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                            'value'    => floatval( $fee->get_total() ),
                            'type'     => $fee_name
                        );
                    }
                }
            }
        } else {
            $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );
        }

        return array(
            'order_ids' => $order_ids,
            'errors'    => $errors
        );

    }

    /**
    * Perform export action
    **/
    function export_tips_to_csv() {

        if(
            isset( $_GET['a'] ) && $_GET['a'] == 'export' &&
            isset( $_GET['from'] ) && $_GET['from'] &&
            isset( $_GET['to'] ) && $_GET['to']
        ) {

            $date_from = $_GET['from'];
            $date_to   = $_GET['to'];

    		$fp = $this->get_tips_csv_header( $date_from, $date_to );
    		$this->create_tips_csv_lines($fp, $date_from, $date_to );
    		fclose($fp);
            exit;

        }

    }

    /**
    * Get CSV file header
    **/
    function get_tips_csv_header( $date_from, $date_to ) {

        $filename = 'order-tips-' . esc_html( $date_from ) . '-' . esc_html( $date_to ) . '.csv';

		header('Content-Type: application/excel');
		header('Content-Disposition: attachment; filename="'.$filename.'"');

		$fp = fopen('php://output', 'w');
		$columns = array(
            __( 'Order ID', 'order-tip-woo' ),
            __( 'Tip name', 'order-tip-woo' ),
            __( 'Tip value', 'order-tip-woo' ),
            __( 'Order date', 'order-tip-woo' )
        );

		$csvheader = $columns;
		$csvheader = array_map('utf8_decode', $csvheader);

		fputcsv($fp, $csvheader, ',');

		return $fp;

	}

    /**
    * Add CSV lines to the CSV file
    **/
    function create_tips_csv_lines($fp, $date_from, $date_to) {

        $a_date = explode( '-', $date_from );
        $b_date = explode( '-', $date_to );

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

            $total = 0;

            foreach( $orders->posts as $order ) {

                $order = new WC_Order( $order->ID );
                $fees  = $order->get_fees();
                foreach( $fees as $fee ) {
                    $fee_name = $fee->get_name();
                    $fee_name = explode(' ', $fee_name);
                    $fee_name = $fee_name[0];
                    if( in_array( $fee_name, $this->fee_names ) ) {
                        $total += $fee->get_total();
                        fputcsv($fp, array(
                            $order->get_id(),
                            date( $this->date_format, strtotime( $order->get_date_created() ) ),
                            floatval( $fee->get_total() ),
                            $fee_name
                        ), ',');
                    }
                }

            }

            fputcsv( $fp, array(), ',' );
            fputcsv( $fp, array( __( 'Total', 'order-tip-woo' ), $total ), ',' );
            fputcsv( $fp, array( __( 'Currency', 'order-tip-woo' ), get_woocommerce_currency() ), ',' );

        }

    }

}
?>
