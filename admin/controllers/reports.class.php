<?php
/**
*
* Admin Reports - /wp-admin/admin.php?page=wc-reports&tab=order_tip
* Soon these reports will be removed. For the time being they can still be accessed at the above URL
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
* @since 1.1.0
*/
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
    * @var object
    **/
    private $views;

    /**
    * Constructor
    **/
    function __construct() {

        $this->date_format = get_option( 'date_format' );

        $this->fee_names = $this->get_fee_names();

        add_filter( 'woocommerce_admin_reports', array( $this, 'tip_reports' ) );
        add_action( 'order_tip_settings_reports', array( $this, 'display_orders_list_reports' ) );
        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'purge_fee_names' ) );

        $ajax = array(
            'display_orders_list_reports_ajax',
        );
        foreach( $ajax as $ajax ) {
            add_action( 'wp_ajax_' . $ajax, array( $this, $ajax ) );
            add_action( 'wp_ajax_nopriv_' . $ajax, array( $this, $ajax ) );
        }

        add_action( 'admin_init', array($this, 'export_tips_to_csv') );

    }

    /**
    * Get all fee names
    * @since 1.3.0
    **/
    function get_fee_names() {

        $fees = wp_cache_get( 'woot_fee_names' );

        if( false === $fees ) {

            global $wpdb;

            $fees = array();
            // Even if using a $wpdb call, this is probably the most efficient way to retrieve the fee names. Looking forward to receiving other suggestions of how to achieve the same
            $order_fees = $wpdb->get_results("SELECT DISTINCT order_item_name FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_type='fee'"); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery

            if( $order_fees ) {
                foreach( $order_fees as $order_fee_name ) {
                    if( ! isset( $fees[ $order_fee_name->order_item_name ] ) ) {
                        $fees[ $order_fee_name->order_item_name ] = true;
                    }
                }
            }

            wp_cache_set( 'woot_fee_names', $fees );

            return $fees;

        }

        return $fees;

    }

    function purge_fee_names() {

        $fees = wp_cache_get( 'woot_fee_names' );

        if( $fees ) {
            wp_cache_delete( 'woot_fee_names' );
        }

    }

    /**
    * Gets all the available WooCommerce orders statuses
    * @since 1.1.0
    **/
    function get_order_statuses() {

        $av_statuses = wc_get_order_statuses();
        $order_statuses = array();

        if( $av_statuses ) {
            foreach( $av_statuses as $status => $label ) {
                if( ! in_array( $status, $order_statuses ) ) {
                    $order_statuses[] = $status;
                }
            }
        }

        ksort( $order_statuses );

        return $order_statuses;

    }

    /**
    * Register reports tab
    * @since 1.1.0
    **/
    function tip_reports($reports) {

        $reports['order_tip'] = array(
            'title'   => __( 'Order Tips','woocommerce' ),
            'reports' => array(
                'tip' => array(
                    'title'       => __( 'Order Tips', 'order-tip-woo' ),
                    'description' => '',
                    'hide_title'  => true,
                    'callback'    => array( $this, 'display_orders_list_reports' )
                )
            )
        );

        return $reports;

    }

    /**
    * Default reports view
    * @since 1.1.0
    **/
    function display_orders_list_reports() {

        if( $this->fee_names ) {

            wp_enqueue_style( 'woo-order-tip-jqueryui' );
            wp_enqueue_style( 'woo-order-tip-admin-reports' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'woo-order-tip-admin-blockui' );
            wp_enqueue_script( 'woo-order-tip-admin-reports' );

            $after_date = gmdate( 'Y-m-d', strtotime('-30 days') );
            $after_date = explode( '-', $after_date );

            $order_ids = array();

            $av_statuses = wc_get_order_statuses();
            $order_statuses = $this->get_order_statuses();

            $orders = new WP_Query( array(
                'post_type'      => 'shop_order',
                'posts_per_page' => 9999,
                'post_status'    => 'any',
                'orderby'        => 'date',
                'order'          => 'DESC',
                'post_status'    => $order_statuses ? $order_statuses : array( 'wc-completed' ),
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

                    $order = wc_get_order( $order->ID );

                    $order_status = $order->get_status();

                    $fees  = $order->get_fees();

                    foreach( $fees as $fee ) {
                        $fee_name = $fee->get_name();
                        if( ! isset( $order_ids[ $order->get_id() ] ) && isset( $this->fee_names[ $fee_name ] ) ) {
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

            $data = array(
                'av_statuses' => $av_statuses,
                'date_format' => $this->date_format,
                'order_ids'   => $order_ids,
                'fee_names'   => $this->fee_names
            );

            include( WOOOTIPPATH . 'admin/views/reports-orders-list.php' );

        } else {
?>
        <h3><?php esc_html_e( 'There are no orders with tips in the database just yet', 'order-tip-woo' ); ?></h3>
<?php
        }

    }

    /**
    * Get reports for date range through AJAX
    * @since 1.1.0
    **/
    function display_orders_list_reports_ajax() {

        check_ajax_referer( 'reps', 'security' );

        $after_date  = sanitize_text_field( esc_html( $_POST['from'] ) );
        $before_date = sanitize_text_field( esc_html( $_POST['to'] ) );
        $status      = sanitize_text_field( esc_html( $_POST['status'] ) );
        $fee_names   = isset( $_POST['feeNames'] ) && $_POST['feeNames'] ? array_map( 'sanitize_text_field', $_POST['feeNames'] ) : $this->fee_names;
        $av_statuses = wc_get_order_statuses();
        $order_statuses = $status == 'all' ? $this->get_order_statuses() : array( $status );

        if( $fee_names && $after_date && $before_date ) {

            $order_ids = $this->get_filtered_order_tips( $fee_names, $after_date, $before_date, $order_statuses );

            if( $order_ids['order_ids'] && ! $order_ids['errors'] ) {

                ob_start();

                $total = 0;
                foreach( $order_ids['order_ids'] as $order_id => $data ) {
                    
                    $order = wc_get_order( $order_id );
                    $order_status = $order->get_status();
                    $total += $data['value'];
                    $date = $data['date'];
                    $date_format = str_split( $this->date_format );
                    if( ! in_array( array( 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v' ), $date_format ) ) {
                        $date_format = apply_filters( 'wc_order_tip_reports_date_time_format', implode( '', $date_format ) . ' H:i:s' );
                    }

                    $data = array(
                        'order_id'     => $order_id,
                        'av_statuses'  => $av_statuses,
                        'order_status' => $order_status,
                        'customer'     => $data['customer'],
                        'type'         => $data['type'],
                        'value'        => $data['value'],
                        'date'         => $data['date'],
                        'date_format'  => $date_format
                    );

                    include( WOOOTIPPATH . 'admin/views/reports-orders-list-row.php' );

                }

                $result = ob_get_clean();

            }

        } else {

            $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );

        }

        wp_send_json( array(
            'after_date_raw'  => $after_date,
            'before_date_raw' => $before_date,
            'after_date'      => gmdate( $this->date_format, strtotime( $after_date ) ),
            'before_date'     => gmdate( $this->date_format, strtotime( $before_date ) ),
            'status'          => $errors ? 'error' : 'success',
            'total'           => isset( $total ) ? number_format( $total, 2 ) : 0,
            'result'          => $result,
            'errors'          => $errors
        ) );

        wp_die();

    }

    /**
    * Get filtered orders
    * @since 1.1.0
    **/
    function get_filtered_order_tips( $fee_names, $after_date, $before_date, $order_statuses = array() ) {

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
            'post_status'    => $order_statuses ? $order_statuses : array( 'wc-completed' ),
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
    * @since 1.1.0
    **/
    function export_tips_to_csv() {

        if(
            isset( $_GET['wootip_export_nonce'] ) && 
            wp_verify_nonce( $_GET['wootip_export_nonce'], 'export-report-to-csv' ) && 
            is_user_logged_in() && current_user_can( 'manage_woocommerce' ) && 
            isset( $_GET['page'] ) && ( 'wc-reports' === $_GET['page'] || 'wc-settings' === $_GET['page'] ) && 
            isset( $_GET['tab'] ) && 'order_tip' === $_GET['tab'] && 
            isset( $_GET['a'] ) && 'export' === $_GET['a'] &&
            isset( $_GET['from'] ) && $_GET['from'] &&
            isset( $_GET['to'] ) && $_GET['to']
        ) {

            $date_from = $_GET['from'];
            $date_to   = $_GET['to'];

            // @codingStandardsIgnoreStart
    		$fp = $this->get_tips_csv_header( $date_from, $date_to );
    		$this->create_tips_csv_lines( $fp, $date_from, $date_to, $_GET['fees'] );
    		fclose($fp); // No need to use WP_Filesystem for files generated on the fly and not stored on the server
            // @codingStandardsIgnoreEnd

            exit;

        }

    }

    /**
    * Get CSV file header
    * @since 1.1.1
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
    * @since 1.1.1
    **/
    function create_tips_csv_lines( $fp, $date_from, $date_to, $fee_names = array() ) {

        $a_date = explode( '-', $date_from );
        $b_date = explode( '-', $date_to );
        $fee_names = ! empty( $fee_names ) ? explode( ',', $fee_names ) : $this->fee_names;

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
                    if( in_array( $fee_name, $fee_names ) ) {
                        $total += $fee->get_total();
                        fputcsv($fp, array(
                            $order->get_id(),
                            $fee_name,
                            floatval( $fee->get_total() ),
                            gmdate( $this->date_format, strtotime( $order->get_date_created() ) )
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
