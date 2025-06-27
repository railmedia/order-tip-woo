<?php
/**
*
* Admin Reports views - /wp-admin/admin.php?page=wc-reports&tab=order_tip
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
* @since 1.4.0
*/
?>
<div class="notice notice-success is-dismissible">
    <p>
        <span class="dashicons dashicons-info"></span>
        <?php esc_html_e( 'Tips are WooCommerce fees. In the Fee title field below as well as in the column Type of the list of orders you may find fee names that may not be related to the tips. Please feel free to filter the orders by selecting the fees of interest from the Fee title drop down below.', 'order-tip-woo' ); ?>
    </p>
</div>
<div id="woo-order-tip-reports">
    <div id="woo-order-tip-reports-filters">
        <div class="row">
            <div class="wot-reports-col">
                <div class="wot-reports-filter-title">
                    <label>
                        <?php esc_html_e( 'Date range', 'order-tip-woo' ); ?>
                    </label>
                </div>
                <div class="wot-reports-filter-value">
                    <div class="filter-item">
                        <label for="wot-reports-date-from">
                            <?php esc_html_e( 'From', 'order-tip-woo' ); ?>
                        </label>
                        <input type="text" id="wot-reports-date-from" placeholder="Click to choose date" value="<?php echo wp_kses_post( $data['date_30_days_ago']->format( 'Y-m-d' ) ); ?>" />
                    </div>
                    <div class="filter-item">
                        <label for="wot-reports-date-to">
                        <?php esc_html_e( 'To', 'order-tip-woo' ); ?>
                    </label>
                    <input type="text" id="wot-reports-date-to" placeholder="Click to choose date" value="<?php echo wp_kses_post( $data['date']->format( 'Y-m-d' ) ); ?>" />
                    </div>
                </div>
            </div>
            <div class="wot-reports-col">
                <div class="wot-reports-filter-title">
                    <label for="wot-reports-order-status">
                        <?php esc_html_e( 'Order Status', 'order-tip-woo' ); ?>
                    </label>
                </div>
                <div class="wot-reports-filter-value">
                    <div class="filter-item">
                        <select id="wot-reports-order-status" class="wc-enhanced-select" multiple data-placeholder="<?php esc_html_e( 'Click to select', 'order-tip-woo' ); ?>" style="width: 100%;">
                            <!-- <option value="all"><?php esc_html_e( 'All', 'order-tip-woo' ); ?></option> -->
                            <?php
                                if( $data['av_statuses'] ) {
                                    foreach( $data['av_statuses'] as $status => $label ) {
                            ?>
                            <option value="<?php echo esc_attr( $status ); ?>"><?php echo esc_html( $label ); ?></option>
                            <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
            <?php if( $data['fee_names'] ) { ?>
            <div class="wot-reports-col">
                <div class="wot-reports-filter-title">
                    <label for="wot-reports-order-fees">
                        <?php esc_html_e( 'Fee title', 'order-tip-woo' ); ?>
                        <span class="woocommerce-help-tip" tabindex="0" data-tip="<?php esc_html_e( 'You may find various fees names in the selector. Please select the tip names that apply to your search criteria.', 'order-tip-woo' ); ?>" aria-label="The street address for your business location."></span>
                        <?php if( $data['fee_names'] ) { ?>
                        <!-- - <a href="#!"><?php esc_html_e( 'Apply fee title filter', 'order-tip-woo' ); ?></a> -->
                        <?php } ?>
                    </label>
                </div>
                <div class="wot-reports-filter-value">
                    <div class="filter-item">
                        <select id="wot-reports-order-fees" class="wc-enhanced-select" multiple data-placeholder="<?php esc_html_e( 'Click to select', 'order-tip-woo' ); ?>" style="width: 100%">
                            <?php
                                if( $data['fee_names'] ) {
                                    foreach( $data['fee_names'] as $name => $value ) {
                            ?>
                            <option value="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></option>
                            <?php } } ?>
                        </select>
                        <!-- <div id="wot-reports-order-fees" style="display: none;">
                            <?php
                                if( $data['fee_names'] ) {
                                    foreach( $data['fee_names'] as $name => $value ) {
                            ?>
                            <p>
                            <input id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $name ); ?>" type="checkbox" />
                            <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></label>
                            </p>
                            <?php } } ?>
                        </div> -->
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="wot-reports-col">
                <button id="wot-set-filters" class="button button-primary"><?php esc_html_e( 'Filter', 'order-tip-woo' ); ?></button>
            </div>
        </div>
    </div>
    <div id="woo-order-tip-reports-errors"></div>
    <div id="woo-order-tip-reports-status">
        <div id="displaying-from-to">
            <?php
                printf(
                    /* translators: 1: From date, 2: To date */
                    wp_kses_post( __( 'Displaying orders between %1$s and %2$s', 'order-tip-woo' ) ),
                    '<span id="displaying-from">' . esc_html( $data['date_30_days_ago']->format( $data['date_format'] ) ) . '</span>',
                    '<span id="displaying-to">' . esc_html( $data['date']->format( $data['date_format'] ) ) . '</span>'
                );
            ?>
        </div>
        <div id="export">
            <button id="wot-export-csv" class="button button-primary"><?php esc_html_e( 'Export to CSV', 'order-tip-woo' ); ?></button>
        </div>
    </div>
    <div id="woo-order-tip-loading">
        <?php esc_html_e( 'Loading orders', 'order-tip-woo' ); ?> <span class="dashicons dashicons-update"></span>
    </div>
    <table id="woo-order-tip-reports-table" class="wp-list-table widefat fixed striped table-view-list pages">
        <thead>
        <tr>
            <th style="width: 30px; text-align: left;">
                <input style="margin-left:0;" title="<?php esc_attr_e( 'Select all', 'order-tip-woo' ); ?>" class="select-all" type="checkbox" />
            </th>
            <th style="width: 30px;"><strong>#</strong></th>
            <th><strong><?php esc_html_e( 'Order ID', 'order-tip-woo' ); ?></strong></th>
            <th><strong><?php esc_html_e( 'Order Status', 'order-tip-woo' ); ?></strong></th>
            <th><strong><?php esc_html_e( 'Customer', 'order-tip-woo' ); ?></strong></th>
            <th><strong><?php esc_html_e( 'Type', 'order-tip-woo' ); ?></strong></th>
            <th><strong><?php esc_html_e( 'Value', 'order-tip-woo' ); ?></strong></th>
            <th><strong><?php esc_html_e( 'Date/Time', 'order-tip-woo' ); ?></strong></th>
        </tr>
        </thead>
        <tbody>
        <?php
            $total = 0;
            $i = 1;
            $date_format = str_split( $data['date_format'] );
            if( ! in_array( array( 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v' ), $date_format ) ) {
                $date_format = apply_filters( 'wc_order_tip_reports_date_time_format', implode( '', $date_format ) . ' H:i:s' );
            }
            foreach( $data['order_ids'] as $order_id => $order_data ) {

                // $order = wc_get_order( $order_id );
                // $order_status = $order->get_status();
                $order_status = $order_data['status'];
                $total += $order_data['value'];
                $date = $order_data['date'];

                $row_data = array(
                    'order_id'     => $order_id,
                    'order_status' => $order_status,
                    'customer'     => $order_data['customer'],
                    'type'         => $order_data['type'],
                    'value'        => $order_data['value'],
                    'date'         => $date,
                    'date_format'  => $date_format
                );

                include( WOOOTIPPATH . 'admin/views/reports-orders-list-row.php' );
        ?>
        <?php $i++; } ?>
        </tbody>
        <?php //if( $data['order_ids'] && $total ) { ?>
        <tfoot>
            <td>
                <input class="select-all" title="<?php esc_attr_e( 'Select all', 'order-tip-woo' ); ?>" style="margin-left:0;" type="checkbox" />
            </td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td colspan="2"><strong><?php esc_html_e( 'Total', 'order-tip-woo' ); ?>: <?php echo esc_html( get_woocommerce_currency_symbol() ); ?><span id="woo-order-tip-reports-total"><?php echo esc_html( number_format( $total, 2 ) ); ?></span></strong></td>
        </tfoot>
        <?php //} ?>
    </table>
</div>