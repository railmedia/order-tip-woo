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
<div id="woo-order-tip-reports">
    <div id="woo-order-tip-reports-date-range">
        <div class="row">
        <div class="wot-reports-col">
            <label for="wot-reports-date-from">
                <?php esc_html_e( 'From', 'order-tip-woo' ); ?>
            </label>
            <input type="text" id="wot-reports-date-from" placeholder="Click to choose date" value="<?php echo wp_kses_post( gmdate( 'Y-m-d', strtotime('-30 days') ) ); ?>" />
        </div>
        <div class="wot-reports-col">
            <label for="wot-reports-date-to">
                <?php esc_html_e( 'To', 'order-tip-woo' ); ?>
            </label>
            <input type="text" id="wot-reports-date-to" placeholder="Click to choose date" value="<?php echo wp_kses_post( gmdate('Y-m-d') ); ?>" />
        </div>
        <div class="wot-reports-col">
            <label for="wot-reports-order-status">
                <?php esc_html_e( 'Order Status', 'order-tip-woo' ); ?>
            </label>
            <select id="wot-reports-order-status">
                <option value="all"><?php esc_html_e( 'All', 'order-tip-woo' ); ?></option>
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
        <div class="wot-reports-col">
            <button id="wot-set-filters" class="button"><?php esc_html_e( 'Filter', 'order-tip-woo' ); ?></button>
        </div>
        <div class="wot-reports-col">
            <a 
                id="wot-export-csv" 
                href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=wc-reports&tab=order_tip&a=export&from=' . wp_kses_post( gmdate( 'Y-m-d', strtotime('-30 days') ) ) . '&to=' . wp_kses_post( gmdate('Y-m-d') ) ), 'export-report-to-csv', 'wootip_export_nonce' ) ); ?>" 
                class="button"
            >
                <?php esc_html_e( 'Export to CSV', 'order-tip-woo' ); ?>
            </a>
        </div>
        </div>
        <div class="row">
            <div class="wot-reports-col">
                <label for="wot-reports-order-fees">
                    <?php esc_html_e( 'Fee title', 'order-tip-woo' ); ?>
                    <?php if( $data['fee_names'] ) { ?>
                    - <a href="#!"><?php esc_html_e( 'Apply fee title filter', 'order-tip-woo' ); ?></a>
                    <?php } ?>
                </label>
                <?php if( $data['fee_names'] ) { ?>
                <div id="wot-reports-order-fees" style="display: none;">
                    <?php
                        if( $data['fee_names'] ) {
                            foreach( $data['fee_names'] as $name => $value ) {
                    ?>
                    <p>
                    <input id="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_html( $name ); ?>" type="checkbox" />
                    <label for="<?php echo esc_attr( $name ); ?>"><?php echo esc_html( $name ); ?></label>
                    </p>
                    <?php } } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <div id="woo-order-tip-reports-errors"></div>
    <p id="displaying-from-to">
        <?php
            printf(
                /* translators: 1: From date, 2: To date */
                wp_kses_post( __( 'Displaying orders between %1$s and %2$s', 'order-tip-woo' ) ),
                '<span id="displaying-from">' . esc_html( gmdate( $data['date_format'], strtotime('-30 days') ) ) . '</span>',
                '<span id="displaying-to">' . esc_html( gmdate( $data['date_format'] ) ) . '</span>'
            );
        ?>
    </p>
    <table id="woo-order-tip-reports-table" class="wp-list-table widefat fixed striped table-view-list pages">
        <thead>
        <tr>
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
            foreach( $data['order_ids'] as $order_id => $order_data ) {
                $order = wc_get_order( $order_id );
                $order_status = $order->get_status();
                $total += $order_data['value'];
                $date = $order_data['date'];
                $date_format = str_split( $data['date_format'] );
                if( ! in_array( array( 'a', 'A', 'B', 'g', 'G', 'h', 'H', 'i', 's', 'u', 'v' ), $date_format ) ) {
                    $date_format = apply_filters( 'wc_order_tip_reports_date_time_format', implode( '', $date_format ) . ' H:i:s' );
                }
        ?>
        <tr>
            <td>
                <a href="<?php echo esc_url( admin_url() ); ?>post.php?post=<?php echo esc_html( $order_id ); ?>&action=edit" target="_blank"><?php echo esc_html( $order_id ); ?></a>
            </td>
            <td>
                <?php echo esc_html( $data['av_statuses'] ? $data['av_statuses'][ 'wc-' . $order_status ] : ucfirst( $order_status ) ); ?>
            </td>
            <td>
                <?php echo esc_html( $order_data['customer'] ); ?>
            </td>
            <td>
                <?php echo esc_html( $order_data['type'] ); ?>
            </td>
            <td>
                <?php echo esc_html( get_woocommerce_currency_symbol() . number_format( $order_data['value'], 2 ) ); ?>
            </td>
            <td>
                <?php echo esc_html( gmdate( $date_format, strtotime( $order_data['date'] ) ) ); ?>
            </td>
        </tr>
        <?php } ?>
        </tbody>
        <?php if( $data['order_ids'] && $total ) { ?>
        <tfoot>
            <td colspan="6"><strong><?php esc_html_e( 'Total', 'order-tip-woo' ); ?>: <?php echo esc_html( get_woocommerce_currency_symbol() ); ?><span id="woo-order-tip-reports-total"><?php echo esc_html( number_format( $total, 2 ) ); ?></span></strong></td>
        </tfoot>
        <?php } ?>
    </table>
</div>