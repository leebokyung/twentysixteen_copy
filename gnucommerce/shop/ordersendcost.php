<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;

$gc = GC_VAR()->gc;

$code = isset($_POST['zipcode']) ? sanitize_text_field($_POST['zipcode']) : '';

if(!$code)
    die('0');

$sql = $wpdb->prepare(" select sc_id, sc_price
            from {$gc['shop_sendcost_table']}
            where sc_zip1 <= '%s'
              and sc_zip2 >= '%s' ", $code, $code);
$row = $wpdb->get_row($sql, ARRAY_A);

if(!$row['sc_id'])
    die('0');

die($row['sc_price']);
?>