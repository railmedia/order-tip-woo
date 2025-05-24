<?php
/**
*
* Tip form view
*
* @package Order Tip for WooCommerce
* @author  Adrian Emil Tudorache
* @license GPL-2.0+
* @link    https://www.tudorache.me/
**/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$tip_type        = $settings['wc_order_tip_type'] && '1' == $settings['wc_order_tip_type'] ? '%' : get_woocommerce_currency_symbol();
$tip_rates       = apply_filters( 'wc_order_tip_rates', $settings['wc_order_tip_rates'] );
$subtotal        = WC()->cart ? WC()->cart->get_subtotal() : 0;
$wc_session      = WC()->session;
$active_tip      = $wc_session->get('tip');
$custom_cash_val = $active_tip && isset( $active_tip['tip_custom'] ) && $active_tip['tip_custom'] ? $active_tip['tip'] : '';
$recurring_tip   = $active_tip ? $active_tip['tip_recurring'] : false;
$active_class    = '';
$has_tip_label_suffix = $settings['wc_order_tip_percentage_total'] && '1' == $settings['wc_order_tip_percentage_total'] ? true : false;
do_action( 'before_order_tip_form' );
?>
<div id="wooot_order_tip_form">
    <?php if( isset( $settings['wc_order_tip_title'] ) && $settings['wc_order_tip_title'] ) { ?>
    <div class="order_tip_title"><?php echo wp_kses_post( apply_filters( 'wc_order_tip_title', $settings['wc_order_tip_title'] ) ); ?></div>
    <?php } ?>
    <?php
        foreach( $tip_rates as $tip_rate ) {
            $tip_label_suffix = '';
            switch( $settings['wc_order_tip_type'] ) {
                case '1':
                    $tip_label = $tip_rate . $tip_type;
                    if( $subtotal && $settings['wc_order_tip_percentage_total'] ) {
                        $tip_label_suffix = wc_price( ( $tip_rate / 100 ) * $subtotal );
                    }
                break;
                case '2':
                    $tip_label = $tip_type . ' ' . $tip_rate;
                break;
            }
            if( $active_tip ) {
                $active_class = $tip_rate == $active_tip['tip'] && $active_tip['tip_custom'] == '0' ? 'active' : '';
            }
    ?>
    <button id="woo_order_tip_<?php echo esc_attr( $tip_rate ); ?>" type="button" class="woo_order_tip <?php echo isset( $active_class ) ? esc_attr( $active_class ) : ''; ?>" data-tip="<?php echo esc_attr( $tip_rate ); ?>" data-tip-type="<?php echo esc_attr( $settings['wc_order_tip_type'] ); ?>" data-tip-custom="0" data-tip-cash="0">
        <?php echo esc_html( $tip_label ); ?>
        <?php if( $has_tip_label_suffix && $tip_label_suffix ) { ?>
        <span class="tip-label-suffix">
            <?php echo wp_kses_post( $tip_label_suffix ); ?>
        </span>
        <?php } ?>
    </button>
    <?php } ?>
    <?php
        if( $settings['wc_order_tip_cash'] ) {
            if( $active_tip ) {
                $active_class =  $active_tip['tip_custom'] == '0' && $active_tip['tip_cash'] == '1' ? 'active' : '';
            }
    ?>
    <button id="woo_order_tip_cash" type="button" class="woo_order_tip <?php echo esc_attr( $active_class ); ?>" data-tip="0" data-tip-type="2" data-tip-custom="0"  data-tip-cash="1">
        <?php echo wp_kses_post( apply_filters( 'wc_order_tip_cash_label', $settings['wc_order_tip_cash_label'] ) ); ?>
    </button>
    <?php } ?>
    <?php
        if( $settings[ 'wc_order_tip_custom' ] ) {
            $ds = get_option( 'woocommerce_price_decimal_sep' );
            $ds ? $ds : '.';
            $active_tip_amount = isset( $active_tip['tip'] ) && $active_tip['tip'] ? str_replace( ',', $ds, str_replace( '.', $ds, $active_tip['tip'] ) ) : '';
            $custom_tip_suffix = isset( $active_tip['tip_custom'] ) && $active_tip['tip_custom'] == 1 ? ' (' . get_woocommerce_currency_symbol() . $active_tip_amount . ')' : '';
            $active_class      = isset( $active_tip['tip_custom'] ) && $active_tip['tip_custom'] == 1 ? 'active' : '';
    ?>
    <button id="woo_order_tip_custom" type="button" class="woo_order_tip <?php echo esc_attr( $active_class ); ?>" data-tip="custom" data-tip-type="2" data-tip-custom="1" data-tip-cash="0">
        <?php echo wp_kses_post( apply_filters( 'wc_order_tip_custom_label', $settings['wc_order_tip_custom_label'] ) ); ?><?php echo esc_html( $custom_tip_suffix ); ?>
    </button>
    
    <?php 
        if( WOOOTIPSUB && isset( $settings['wc_order_tip_woo_subscriptions'] ) && $settings['wc_order_tip_woo_subscriptions'] == '4' ) { 
            $checked = $recurring_tip === true ? 'checked="checked"' : ''
    ?>
    <p class="woo_order_tip_recurring_tip_field">
        <input id="woo_recurring_tip" type="checkbox" <?php echo esc_attr( $checked ); ?> /> <label for="woo_recurring_tip"><?php esc_html_e( 'Recurring tip', 'order-tip-woo' ); ?></label>
    </p>
    <?php } ?>
    
    <p class="form-row woo_order_tip_custom_text_field" style="display:none;">
        <input  
            type="text" 
            class="input-text woo_order_tip_custom_text" 
            data-tip-type="<?php echo esc_attr( $settings['wc_order_tip_type'] ); ?>" 
            data-currency="<?php echo esc_attr( get_woocommerce_currency_symbol() ); ?>" 
            placeholder="<?php echo wp_kses_post( apply_filters( 'wc_order_tip_custom_enter_tip_placeholder', $settings['wc_order_tip_enter_placeholder'] ) ); ?>" 
            value="<?php echo esc_attr( $custom_cash_val ); ?>"
        />
    </p>
    <?php } ?>
    <button class="woo_order_tip_apply" type="button" name="woo_order_tip_apply" style="display:none;"><?php echo esc_html( $settings['wc_order_tip_custom_apply_label'] ); ?><span></span></button>
    <button class="woo_order_tip_remove" type="button" style="<?php echo ! $active_tip ? 'display:none;' : ''; ?>"><?php echo esc_html( $settings['wc_order_tip_custom_remove_label'] ); ?></button>
</div>
<?php do_action( 'after_order_tip_form' ); ?>