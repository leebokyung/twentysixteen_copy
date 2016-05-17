<?php
if( ! defined( 'GC_NAME' ) ) exit;

$it_id = isset($_POST['it_id']) ? sprintf('%.0f', $_POST['it_id']) : 0;
$opt_id = isset($_POST['opt_id']) ? sanitize_text_field($_POST['opt_id']) : 0;
$idx = isset($_POST['idx']) ? (int) $_POST['idx'] : 0;
$sel_count = isset($_POST['sel_count']) ? (int) $_POST['sel_count'] : 0;

$sql = $wpdb->prepare(" select * from {$gc['shop_item_option_table']}
                where io_type = '0'
                  and it_id = '%s'
                  and io_use = '1'
                  and io_id like '%s'
                order by io_no asc ", $it_id, $opt_id.'%');

$rows = $wpdb->get_results($sql, ARRAY_A);

$str = '<option value="">선택</option>';
$opt = array();

foreach( $rows as $row ){
    if( empty($row) ) continue;

    $val = explode(chr(30), $row['io_id']);
    $key = $idx + 1;

    if(!strlen($val[$key]))
        continue;

    $continue = false;
    foreach($opt as $v) {
        if(strval($v) === strval($val[$key])) {
            $continue = true;
            break;
        }
    }
    if($continue)
        continue;

    $opt[] = strval($val[$key]);

    if($key + 1 < $sel_count) {
        $str .= PHP_EOL.'<option value="'.$val[$key].'">'.$val[$key].'</option>';
    } else {
        if($row['io_price'] >= 0)
            $price = '&nbsp;&nbsp;+ '.number_format($row['io_price']).'원';
        else
            $price = '&nbsp;&nbsp; '.number_format($row['io_price']).'원';

        $io_stock_qty = gc_get_option_stock_qty($it_id, $row['io_id'], $row['io_type']);

        if($io_stock_qty < 1)
            $soldout = '&nbsp;&nbsp;[품절]';
        else
            $soldout = '';

        $str .= PHP_EOL.'<option value="'.$val[$key].','.$row['io_price'].','.$io_stock_qty.'">'.$val[$key].$price.$soldout.'</option>';
    }
}

echo $str;
?>