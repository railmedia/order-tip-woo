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
            'export_tips_to_csv_ajax',
            'delete_exported_csv_file_ajax'
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
            'title'   => __( 'Order Tips','order-tip-woo' ),
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

            $date_format    = get_option('date_format');

            $after_date     = new DateTime();
            $after_date     = $after_date->modify('-30 day');

            $today          = new DateTime();

            
            $av_statuses    = wc_get_order_statuses();
            $order_statuses = $this->get_order_statuses();
            
            $order_ids      = array();

            $order_ids = $this->get_orders_with_tips( array(
                'order_statuses' => $order_statuses,
                'after_date'     => $after_date->format('Y-m-d'),
                'before_date'    => $today->format('Y-m-d')
            ) );

            $data = array(
                'av_statuses' => $av_statuses,
                'date_format' => $this->date_format,
                'order_ids'   => $order_ids,
                'fee_names'   => $this->fee_names,
                'date'        => $today,
                'date_30_days_ago' => $after_date
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

        check_ajax_referer( 'reps-' . date('Y-m-d H'), 'security' );

        $after_date  = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from'] ) ) : '';
        $before_date = isset( $_POST['to'] ) ? sanitize_text_field( wp_unslash( $_POST['to'] ) ) : '';
        $paged       = isset( $_POST['paged'] ) && ! empty( $_POST['paged'] ) && is_numeric( $_POST['paged'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['paged'] ) ) ) : 1;
        $status      = isset( $_POST['status'] ) && ! empty( $_POST['status'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['status'] ) ) : 'all';
        $fee_names   = isset( $_POST['feeNames'] ) && ! empty( $_POST['feeNames'] ) ? array_flip( array_map( 'sanitize_text_field', wp_unslash( $_POST['feeNames'] ) ) ) : $this->fee_names;
        $av_statuses = wc_get_order_statuses();
        $order_statuses = $status == 'all' ? $this->get_order_statuses() : $status;

        if( $fee_names && $after_date && $before_date ) {

            $order_ids = $this->get_filtered_order_tips( $fee_names, $after_date, $before_date, $order_statuses, $paged );

            if( $order_ids['order_ids'] && ! $order_ids['errors'] ) {

                // ob_start();
                $result = array();

                $total = 0;
                $i = 1;
                
                $date_format = str_split( $this->date_format );
                if( ! in_array( array( 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v' ), $date_format ) ) {
                    $date_format = apply_filters( 'wc_order_tip_reports_date_time_format', implode( '', $date_format ) . ' H:i:s' );
                }

                foreach( $order_ids['order_ids'] as $order_id => $data ) {
                    
                    $order_status = $data['status'];
                    $total += $data['value'];
                    $date = $data['date'];                    

                    // $row_data = array(
                    //     'order_id'     => $order_id,
                    //     'av_statuses'  => $av_statuses,
                    //     'order_status' => $order_status,
                    //     'customer'     => $data['customer'],
                    //     'type'         => $data['type'],
                    //     'value'        => $data['value'],
                    //     'date'         => $data['date'],
                    //     'date_format'  => $date_format
                    // );

                    // include( WOOOTIPPATH . 'admin/views/reports-orders-list-row.php' );

                    $date = new DateTime( $data['date'] );

                    $result[] = array(
                        // 'idx'         => $i,
                        'orderId'     => esc_html( $order_id ),
                        'orderLink'   => esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ),
                        'orderStatus' => $av_statuses[ 'wc-' . $order_status ],
                        'customer'    => esc_html( $data['customer'] ),
                        'feeType'     => esc_html( $data['type'] ),
                        'feePrice'    => wc_price( number_format( esc_html( $data['value'] ), 2 ) ),
                        'feeValue'    => number_format( esc_html( $data['value'] ), 2 ),
                        'orderDate'   => esc_html( $date->format( $date_format ) )
                    );

                    $i++;

                }

                // $result = ob_get_clean();

            }

        } else {

            $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );

        }

        $after_date  = new DateTime( $after_date );
        $before_date = new DateTime( $before_date );

        wp_send_json( array(
            'order_ids'       => $order_ids['order_ids'],
            'order_ids_count' => count( $order_ids['order_ids'] ),
            'after_date_raw'  => $after_date,
            'before_date_raw' => $before_date,
            'after_date'      => $after_date->format( $this->date_format ),
            'before_date'     => $before_date->format( $this->date_format ),
            // 'fee_names'       => $fee_names,
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
    function get_filtered_order_tips( $fee_names, $after_date, $before_date, $order_statuses = array( 'wc-completed' ), $paged = 1 ) {

        if( ! $fee_names || ! $after_date || ! $before_date ) return;

        $errors = $order_ids = array();

        $order_ids = $this->get_orders_with_tips( array(
            'order_statuses' => $order_statuses,
            'after_date'     => $after_date,
            'before_date'    => $before_date,
            'fee_names'      => $fee_names
        ), array(), $paged );

        if( ! $order_ids ) {
            $errors[] = __( 'There are no orders with tips based on your date range.', 'order-tip-woo' );
        }

        return array(
            'order_ids' => $order_ids,
            'errors'    => $errors
        );

    }

    /**
    * Recursively get orders with tips
    * @since 1.4.3
    **/
    function get_orders_with_tips( array $args, array $order_ids = array(), int $paged = 1, int $limit = 100 ) {

        $order_statuses = isset( $args['order_statuses'] ) && $args['order_statuses'] ? $args['order_statuses'] : array( 'wc-completed' );
        $fee_names      = isset( $args['fee_names'] ) && $args['fee_names'] ? $args['fee_names'] : $this->fee_names;
        $after_date     = isset( $args['after_date'] ) && $args['after_date'] ? $args['after_date'] : '';
        $before_date    = isset( $args['before_date'] ) && $args['before_date'] ? $args['before_date'] : '';

        if( ! $after_date ) {
            $after_date = new DateTime();
            $after_date = $after_date->modify('-30 day');
            $after_date = $after_date->format('Y-m-d');
        }

        if( ! $before_date ) {
            $before_date = new DateTime();
            $before_date = $before_date->format('Y-m-d');
        }

        $orders = wc_get_orders( array(
            'orderby'      => 'date',
            'order'        => 'DESC',
            'type'         => 'shop_order',
            'status'       => $order_statuses,
            'date_created' => $after_date . '...' . $before_date,
            'limit'        => $limit,
            'paged'        => $paged
        ) );

        if( $orders ) {

            foreach( $orders as $order ) {

                $fees  = $order->get_fees();

                foreach( $fees as $fee ) {
                    $fee_name = $fee->get_name();
                    if( ! isset( $order_ids[ $order->get_id() ] ) && isset( $fee_names[ $fee_name ] ) ) {
                        $order_ids[ $order->get_id() ] = array(
                            'date'     => $order->get_date_created(),
                            'customer' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                            'status'   => $order->get_status(),
                            'value'    => floatval( $fee->get_total() ),
                            'type'     => $fee_name
                        );
                        if( count( $order_ids ) >= $limit ) {
                            return $order_ids;
                        }
                    }
                }

            }

        } else {
            return $order_ids;
        }

        if( count( $order_ids ) < $limit ) {
            $paged++;
            return $this->get_orders_with_tips( $args, $order_ids, $paged, $limit );
        }

        return $order_ids;

    }

    /**
    * Perform export action via AJAX
    * @since 1.5.0
    **/
    function export_tips_to_csv_ajax() {

        check_ajax_referer( 'export-report-to-csv-' . date('Y-m-d H'), 'security' );
        
        global $wp_filesystem;

        $errors = array();
        
        if ( ! function_exists( 'request_filesystem_credentials' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
        }
    
        if ( ! WP_Filesystem() ) {
            $errors[] = esc_html__( 'Failed to initialize the export requisites.', 'order-tip-woo' );
        }

        $data         = isset( $_POST['data'] ) ? map_deep( wp_unslash( $_POST['data'] ), 'sanitize_text_field' ) : array();
        
        $uploads_dir  = wp_upload_dir();
        $upload_path  = $uploads_dir['basedir'];
        $upload_url   = $uploads_dir['baseurl'];
        $upload_dir   = $upload_path . '/order-tip-woo';
        
        // Check if the directory exists, if not, create it
        if ( ! $wp_filesystem->is_dir( $upload_dir ) ) {
            if ( ! $wp_filesystem->mkdir( $upload_dir, FS_CHMOD_DIR ) ) {
                $errors[] = esc_html__( 'Failed to create the uploads directory. Please check the permissions.', 'order-tip-woo' );
                // if ( ! mkdir( $custom_directory, 0755, true ) ) {
                //     $errors[] = esc_html__( 'Failed to create the uploads directory. Please check the permissions.', 'order-tip-woo' );
                // }
            }
        }
    
        $filename  = 'order-tip-woo-export-' . time() . '.csv';
        $file_path = $upload_dir . '/' . $filename;
        $file_url  = $upload_url . '/order-tip-woo/' . $filename;
    
        // Create CSV content
        $csv_data = __( 'Order ID', 'order-tip-woo' ) . ',' . __( 'Tip name', 'order-tip-woo' ) . ',' . __( 'Tip value', 'order-tip-woo' ) . ',' . __( 'Order date', 'order-tip-woo' ) . "\n";
        if( $data ) {
            foreach( $data as $order ) {
                $order_id   = str_replace( ',', '', sanitize_text_field( esc_html( $order['orderId'] ) ) );
                $fee_name   = str_replace( ',', '', sanitize_text_field( esc_html( $order['feeName'] ) ) );
                $fee_value  = str_replace( ',', '', sanitize_text_field( esc_html( $order['feeValue'] ) ) );
                $order_date = str_replace( ',', '', sanitize_text_field( esc_html( $order['orderDate'] ) ) );

                $csv_data .= $order_id . ',' . $fee_name . ',' . $fee_value . ',' . $order_date . "\n";

            }
        }
    
        if ( ! $wp_filesystem->put_contents( $file_path, $csv_data, FS_CHMOD_FILE ) ) {
            return false; 
        }

        wp_send_json( array(
            'fileUrl'  => $file_url,
            'filePath' => $file_path,
            'errors'   => $errors
        ) );

        wp_die();

    }

    /**
    * Perform export action via AJAX
    * @since 1.5.0
    **/
    function delete_exported_csv_file_ajax() {

        check_ajax_referer( 'delete-exported-file-' . date('Y-m-d H'), 'security' );

        global $wp_filesystem;

        if( ! $wp_filesystem ) {
            require_once ( ABSPATH . '/wp-admin/includes/file.php' );
        }

        $filesystem = $wp_filesystem ? $wp_filesystem : ( function_exists( 'WP_Filesystem' ) ? WP_Filesystem() : null );

        if( ! $filesystem ) {
            
            wp_send_json( array(
                'status' => 'error'
            ) );

            wp_die();

        }

        $file_path = isset( $_POST['filePath'] ) && ! empty( $_POST['filePath'] ) ? sanitize_text_field( wp_unslash( $_POST['filePath'] ) ) : '';

        if( $wp_filesystem->is_file( $file_path ) ) {
            $wp_filesystem->delete( $file_path );
        }

        wp_send_json( array(
            'status' => 'success'
        ) );

        wp_die();

    }

    /**
    * Perform export action
    * @since 1.1.0
    **/
    function export_tips_to_csv() {

        $wootip_export_nonce = isset( $_POST['wootip_export_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['wootip_export_nonce'] ) ) : '';
        $page      = isset( $_POST['page'] ) ? sanitize_text_field( wp_unslash( $_POST['page'] ) ) : '';
        $tab       = isset( $_POST['tab'] ) ? sanitize_text_field( wp_unslash( $_POST['tab'] ) ) : '';
        $a         = isset( $_POST['a'] ) ? sanitize_text_field( wp_unslash( $_POST['a'] ) ) : '';
        $date_from = isset( $_POST['from'] ) ? sanitize_text_field( wp_unslash( $_POST['from'] ) ) : '';
        $date_to   = isset( $_POST['to'] ) ? sanitize_text_field( wp_unslash( $_POST['to'] ) ) : '';

        if(
            $wootip_export_nonce
            && wp_verify_nonce( $wootip_export_nonce, 'export-report-to-csv-' . date('Y-m-d H') )
            && is_user_logged_in() && current_user_can( 'manage_woocommerce' ) 
            && $page && ( 'wc-reports' === $page || 'wc-settings' === $page )
            && $tab && 'order_tip' === $tab
            && $a && 'export' === $a
            && $date_from
            && $date_to
        ) {

            $filename = 'order-tips-' . esc_html( $date_from ) . '-' . esc_html( $date_to ) . '.csv';

            header('Content-Description: File Transfer');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment; filename="'.$filename.'"');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

            $fp = fopen('php://output', 'w');

            // @codingStandardsIgnoreStart
    		$this->get_tips_csv_header( $fp, $date_from, $date_to );
    		$this->create_tips_csv_lines( $fp, $date_from, $date_to, $_POST['fees'] );
    		fclose($fp); // No need to use WP_Filesystem for files generated on the fly and not stored on the server
            // @codingStandardsIgnoreEnd

            die();

        }

    }

    /**
    * Get CSV file header
    * @since 1.1.1
    **/
    function get_tips_csv_header( $fp, $date_from, $date_to ) {

        
		$columns = array(
            __( 'Order ID', 'order-tip-woo' ),
            __( 'Tip name', 'order-tip-woo' ),
            __( 'Tip value', 'order-tip-woo' ),
            __( 'Order date', 'order-tip-woo' )
        );

		$csvheader = $columns;
		$csvheader = array_map('utf8_decode', $csvheader);

		fputcsv($fp, $csvheader, ',');

	}

    /**
    * Add CSV lines to the CSV file
    * @since 1.1.1
    **/
    function create_tips_csv_lines( $fp, $date_from, $date_to, $fee_names = array() ) {

        $fee_names = ! empty( $fee_names ) ? explode( ',', $fee_names ) : $this->fee_names;

        $orders = wc_get_orders(array(
            'orderby'      => 'date',
            'order'        => 'DESC',
            'type'         => 'shop_order',
            'date_created' => $date_from . '...' . $date_to,
            'limit'        => -1
        ));

        if( $orders ) {

            $total = 0;

            foreach( $orders as $order ) {

                $fees  = $order->get_fees();
                foreach( $fees as $fee ) {
                    $fee_name = $fee->get_name();
                    if( in_array( $fee_name, $fee_names ) ) {
                        
                        $fee_total = floatval( $fee->get_total() );

                        $total += $fee_total;
                        $created_date = new DateTime( $order->get_date_created() );
                        
                        fputcsv($fp, array(
                            $order->get_id(),
                            $fee_name,
                            floatval( $fee->get_total() ),
                            $created_date->format( $this->date_format )
                        ), ',');

                    }
                }

            }
            
            fputcsv( $fp, array(), ',' );
            fputcsv( $fp, array( esc_html__( 'Total', 'order-tip-woo' ), $total ), ',' );
            fputcsv( $fp, array( esc_html__( 'Currency', 'order-tip-woo' ), get_woocommerce_currency() ), ',' );

        }

    }

}
?>
