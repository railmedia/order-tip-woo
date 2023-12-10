<?php
$files = array(
    'controllers/settings.class.php' => 'WOO_Order_Tip_Admin_Settings'
);
foreach( $files as $file => $init ) {
    require_once( __DIR__ . '/' . $file );
    if( $init )
        new $init;
}
?>
