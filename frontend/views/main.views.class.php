<?php
class WOO_Order_Tip_Main_Views {

    function tip_form( $data ) {

        $settings = $data['settings'];
        $tip_type = $settings['wc_order_tip_type'] == '1' ? '%' : get_woocommerce_currency_symbol();
        $tip_rates = apply_filters( 'wc_order_tip_rates', $settings['wc_order_tip_rates'] );
        $wc_session = WC()->session;
        $active_tip = $wc_session->get('tip');

?>
    <div id="wooot_order_tip_form">

        <h3><?php echo apply_filters( 'wc_order_tip_title', $settings['wc_order_tip_title'] ); ?></h3>

        <?php
            foreach( $tip_rates as $tip_rate ) {
                switch( $settings['wc_order_tip_type'] ) {
                    case '1':
                        $tip_label = $tip_rate . $tip_type;
                    break;
                    case '2':
                        $tip_label = $tip_type . ' ' . $tip_rate;
                    break;
                }

                if( $active_tip ) {
                    $active_class = $tip_rate == $active_tip['tip'] && $active_tip['tip_custom'] == '0' ? 'active' : '';
                }
        ?>

        <button id="woo_order_tip_<?php echo $tip_rate; ?>" class="woo_order_tip <?php echo $active_class; ?>" data-tip="<?php echo $tip_rate; ?>" data-tip-type="<?php echo $settings['wc_order_tip_type'] ?>" data-tip-custom="0" data-tip-cash="0"><?php echo $tip_label; ?></button>

        <?php } ?>

        <?php
            if( $settings['wc_order_tip_cash'] ) {

                if( $active_tip ) {
                    $active_class =  $active_tip['tip_custom'] == '0' && $active_tip['tip_cash'] == '1' ? 'active' : '';
                }
        ?>

        <button id="woo_order_tip_custom" class="woo_order_tip <?php echo $active_class; ?>" data-tip="0" data-tip-type="2" data-tip-custom="0"  data-tip-cash="1">Cash</button>

        <?php } ?>

        <?php
            if( $settings[ 'wc_order_tip_custom' ] ) {
                $custom_tip_suffix = isset( $active_tip['tip_custom'] ) && $active_tip['tip_custom'] == 1 ? ' (' . get_woocommerce_currency_symbol() . $active_tip['tip'] . ')' : '';
                $active_class      = isset( $active_tip['tip_custom'] ) && $active_tip['tip_custom'] == 1 ? 'active' : '';
        ?>

        <button id="woo_order_tip_custom" class="woo_order_tip <?php echo $active_class; ?>" data-tip="custom" data-tip-type="2" data-tip-custom="1"  data-tip-cash="0">Custom Tip<?php echo $custom_tip_suffix; ?></button>

        <p class="form-row woo_order_tip_custom_text">
            <input style="display:none;" type="text" class="input-text" data-tip-type="<?php echo $settings['wc_order_tip_type'] ?>" data-currency="<?php echo get_woocommerce_currency_symbol(); ?>" placeholder="<?php _e( 'Enter tip amount', 'order-tip-woo' ); ?>" id="woo_order_tip_custom_text" />
        </p>

        <?php } ?>

        <button id="woo_order_tip_apply" name="woo_order_tip_apply" style="display:none;"><?php printf( __( 'Add %s tip to order', 'order-tip-woo' ), '<span></span>' ); ?></button>

        <button id="woo_order_tip_remove" style="<?php echo ! $active_tip ? 'display:none;' : ''; ?>"><?php _e( 'Remove Tip', 'order-tip-woo' ); ?></button>

    </div>
<?php

    }

}
?>
