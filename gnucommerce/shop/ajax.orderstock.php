<?php
if( !defined('GC_NAME') ) exit;

if (gc_get_session('ss_direct'))
    $tmp_cart_id = gc_get_session('ss_cart_direct');
else
    $tmp_cart_id = gc_get_session('ss_cart_id');

if (gc_get_cart_count($tmp_cart_id) == 0)// 장바구니에 담기
    die("장바구니가 비어 있습니다.\n\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.");

$keep_term = $config['de_cart_keep_term'];
if(!$keep_term)
    $keep_term = 15; // 기본값 15일

if(defined('GC_CART_STOCK_LIMIT'))
    $cart_stock_limit = GC_CART_STOCK_LIMIT;
else
    $cart_stock_limit = 3;

// 기준 시간을 초과한 경우 체크
if($cart_stock_limit > 0) {
    if($cart_stock_limit > $keep_term * 24)
        $cart_stock_limit = $keep_term * 24;

    $stocktime = GC_SERVER_TIME - (3600 * $cart_stock_limit);

    $sql = $wpdb->prepare(" select count(*) as cnt
                from {$gc['shop_cart_table']}
                where od_id = %.0f
                and ct_status = '%s'
                and ct_select = '1'
                and ct_select_time > '%s' ", $tmp_cart_id, gc_get_stype_names('shopping'), date('Y-m-d H:i:s', $stocktime));

    $row_cnt = $wpdb->get_var($sql);

    if(!$row_cnt){
        die("주문 요청 때까지 ".$cart_stock_limit."시간 이상 경과되어 주문 상품이 초기화 됐습니다.\n\n 장바구니에서 주문하실 상품을 다시 확인해 주십시오.");
    }
}

// 재고체크
$sql = $wpdb->prepare(" select *
            from {$gc['shop_cart_table']}
            where od_id = %.0f
              and ct_select = '1'
              and ct_status = '%s' ", $tmp_cart_id, gc_get_stype_names('shopping'));
$results = $wpdb->get_results($sql, ARRAY_A);

foreach($results as $row){
    if( empty($row) ) continue;

    $ct_qty = $row['ct_qty'];

    if(!$row['io_id']){
        $it_stock_qty = gc_get_it_stock_qty($row['it_id']);
    } else {
        $it_stock_qty = gc_get_option_stock_qty($row['it_id'], $row['io_id'], $row['io_type']);
    }

    if ($ct_qty > $it_stock_qty)
    {
        $item_option = $row['it_name'];
        if($row['io_id'])
            $item_option .= '('.$row['ct_option'].')';

        die($item_option." 의 재고수량이 부족합니다.\n\n현재 재고수량 : " . number_format($it_stock_qty) . " 개");
    }
}

die("");
?>