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
<tr>
    <td>
        <a href="<?php echo esc_url( admin_url() ); ?>post.php?post=<?php echo esc_html( $data['order_id'] ); ?>&action=edit" target="_blank"><?php echo esc_html( $data['order_id'] ); ?></a>
    </td>
    <td>
        <?php echo esc_html( $data['av_statuses'] ? $data['av_statuses'][ 'wc-' . $data['order_status'] ] : ucfirst( $data['order_status'] ) ); ?>
    </td>
    <td>
        <?php echo esc_html( $data['customer'] ); ?>
    </td>
    <td>
        <?php echo esc_html( $data['type'] ); ?>
    </td>
    <td>
        <?php echo esc_html( get_woocommerce_currency_symbol() . esc_html( number_format( $data['value'], 2 ) ) ); ?>
    </td>
    <td>
        <?php echo esc_html( gmdate( $data['date_format'], strtotime( $data['date'] ) ) ); ?>
    </td>
</tr>