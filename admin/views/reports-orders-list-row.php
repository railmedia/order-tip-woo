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

$idx          = esc_html( $i );
$order_id     = esc_html( $row_data['order_id'] );
$order_status = ! empty( $row_data['order_status'] ) && ! empty( $row_data['av_statuses'][ 'wc-' . $row_data['order_status'] ] ) ? $row_data['av_statuses'][ 'wc-' . $row_data['order_status'] ] : '';
$order_status = ! $order_status && ! empty( $row_data['order_status'] ) ? ucfirst( $row_data['order_status'] ) : '';
$order_status = $order_status ? esc_html( $order_status ) : '';
// $order_status = esc_html( $row_data['av_statuses'] ? $row_data['av_statuses'][ 'wc-' . $row_data['order_status'] ] : ucfirst( $row_data['order_status'] ) );
$customer     = esc_html( $row_data['customer'] );
$fee_type     = esc_html( $row_data['type'] );
$fee_value    = esc_html( $row_data['value'] );
$date         = new DateTime( $row_data['date'] );
$date         = esc_html( $date->format( $row_data['date_format'] ) )
?>
<tr data-orderid="<?php echo $order_id; ?>">
    <td style="width: 30px;">
        <input title="<?php printf( /* translators: 1: Order ID */ esc_attr__( 'Select %d', 'order-tip-woo' ), esc_attr( $row_data['order_id'] ) ); ?>" class="select-order" type="checkbox" />
    </td>
    <td class="row-count" data-value="<?php echo esc_attr( $idx ); ?>">
        <?php echo esc_html( $idx ); ?>
    </td>
    <td class="order-id" data-value="<?php echo esc_attr( $order_id ); ?>">
        <a href="<?php echo esc_url( admin_url('post.php?post=' . $order_id . '&action=edit') ); ?>" target="_blank"><?php echo esc_html( $order_id ); ?></a>
    </td>
    <td class="order-status-col" data-value="<?php echo esc_attr( $order_status ); ?>">
        <?php echo esc_html( $order_status ); ?>
    </td>
    <td class="customer-name" data-value="<?php echo esc_attr( $customer ); ?>">
        <?php echo esc_html( $customer ); ?>
    </td>
    <td class="fee-type" data-value="<?php echo esc_attr( $fee_type ); ?>">
    <?php echo esc_html( $fee_type ); ?>
    </td>
    <td class="order-value" data-value="<?php echo esc_attr( $fee_value ); ?>">
        <?php echo esc_html( get_woocommerce_currency_symbol() . number_format( $fee_value, 2 ) ); ?>
    </td>
    <td class="order-date" data-value="<?php echo esc_attr( $date ); ?>">
        <?php echo esc_html( $date ); ?>
    </td>
</tr>