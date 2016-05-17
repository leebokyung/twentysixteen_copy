<?php
if(!defined('GC_NAME')) exit;

// 보관기간이 지난 상품 삭제
gc_cart_item_clean();

$sw_direct = isset($_REQUEST['sw_direct']) ? sanitize_text_field($_REQUEST['sw_direct']) : '';
$act = isset($_REQUEST['act']) ? sanitize_text_field($_REQUEST['act']) : '';
$config = GC_VAR()->config;

// cart id 설정
gc_set_cart_id($sw_direct);

if($sw_direct)
    $tmp_cart_id = gc_get_session('ss_cart_direct');
else
    $tmp_cart_id = gc_get_session('ss_cart_id');

// 브라우저에서 쿠키를 허용하지 않은 경우라고 볼 수 있음.
if (!$tmp_cart_id)
{
    gc_alert('더 이상 작업을 진행할 수 없습니다.\\n\\n브라우저의 쿠키 허용을 사용하지 않음으로 설정한것 같습니다.\\n\\n브라우저의 인터넷 옵션에서 쿠키 허용을 사용으로 설정해 주십시오.\\n\\n그래도 진행이 되지 않는다면 쇼핑몰 운영자에게 문의 바랍니다.');
}

if($act == "buy")
{
    if(!count($_POST['ct_chk']))
        gc_alert("주문하실 상품을 하나이상 선택해 주십시오.");

    gc_orderform_pre_check($_POST['ct_chk'], $_POST['it_id'], $act, $tmp_cart_id);

    //무조건 checkout 페이지로 이동
    wp_safe_redirect( add_query_arg( array('noc'=>1), gc_get_page_url('checkout')) );
    exit;

}
else if ($act == "alldelete") // 모두 삭제이면
{
    $sql = $wpdb->prepare(" delete from {$gc['shop_cart_table']}
              where od_id = %.0f ", $tmp_cart_id);
    $result = $wpdb->query($sql);
}
else if ($act == "seldelete") // 선택삭제
{
    if(!count($_POST['ct_chk']))
        gc_alert("삭제하실 상품을 하나이상 선택해 주십시오.");

    $fldcnt = count($_POST['it_id']);
    for($i=0; $i<$fldcnt; $i++) {
        if( ! isset($_POST['ct_chk'][$i]) ) continue;

        $ct_chk = intval($_POST['ct_chk'][$i]);
        if($ct_chk) {
            $it_id = sprintf("%.0f", $_POST['it_id'][$i]);
            $sql = $wpdb->prepare(" delete from {$gc['shop_cart_table']} where it_id = %.0f and od_id = %.0f ", $it_id, $tmp_cart_id);
            $result = $wpdb->query($sql);
        }
    }
}
else // 장바구니에 담기
{
    $count = count($_POST['it_id']);
    if ($count < 1)
        gc_alert('장바구니에 담을 상품을 선택하여 주십시오.');

    $ct_count = 0;

    for($i=0; $i<$count; $i++) {
        // 보관함의 상품을 담을 때 체크되지 않은 상품 건너뜀
        if($act == 'multi' && !$_POST['chk_it_id'][$i])
            continue;

        $it_id = intval($_POST['it_id'][$i]);
        $opt_count = isset($_POST['io_id'][$it_id]) ? count($_POST['io_id'][$it_id]) : 0;

        if($opt_count && $_POST['io_type'][$it_id][0] != 0)
            gc_alert('상품의 선택옵션을 선택해 주십시오.');

        for($k=0; $k<$opt_count; $k++) {
            if ( intval($_POST['ct_qty'][$it_id][$k]) < 1 )
                gc_alert('수량은 1 이상 입력해 주십시오.');
        }

        $it = gc_get_product_info($it_id);

        if(!$it['ID'])
            gc_alert('상품정보가 존재하지 않습니다.');

        // 바로구매에 있던 장바구니 자료를 지운다.
        if($i == 0 && $sw_direct){
            $result = $wpdb->query(
                        $wpdb->prepare(" delete from {$gc['shop_cart_table']} where od_id = '%s' and ct_direct = 1 ", $tmp_cart_id)
                        );
        }
        // 최소, 최대 수량 체크
        if($it['it_buy_min_qty'] || $it['it_buy_max_qty']) {
            $sum_qty = 0;
            for($k=0; $k<$opt_count; $k++) {
                if( intval($_POST['io_type'][$it_id][$k]) == 0 )
                    $sum_qty += intval($_POST['ct_qty'][$it_id][$k]);
            }

            if($it['it_buy_min_qty'] > 0 && $sum_qty < $it['it_buy_min_qty'])
                gc_alert($it['it_name'].'의 선택옵션 개수 총합 '.number_format($it['it_buy_min_qty']).'개 이상 주문해 주십시오.');

            if($it['it_buy_max_qty'] > 0 && $sum_qty > $it['it_buy_max_qty'])
                gc_alert($it['it_name'].'의 선택옵션 개수 총합 '.number_format($it['it_buy_max_qty']).'개 이하로 주문해 주십시오.');

            // 기존에 장바구니에 담긴 상품이 있는 경우에 최대 구매수량 체크
            if($it['it_buy_max_qty'] > 0) {
                $row4 = $wpdb->get_row(
                        $wpdb->prepare(" select sum(ct_qty) as ct_sum
                            from {$gc['shop_cart_table']}
                            where od_id = '%s'
                              and it_id = %d
                              and io_type = '0'
                              and ct_status = '%s' ", $tmp_cart_id, $it_id, gc_get_stype_names('shopping')), ARRAY_A
                                  );

                if(($sum_qty + $row4['ct_sum']) > $it['it_buy_max_qty'])
                    gc_alert($it['it_name'].'의 선택옵션 개수 총합 '.number_format($it['it_buy_max_qty']).'개 이하로 주문해 주십시오.', gc_get_page_url('cart'));
            }
        }

        // 옵션정보를 얻어서 배열에 저장
        $opt_list = array();
        $result = $wpdb->get_results(
                    $wpdb->prepare(" select * from {$gc['shop_item_option_table']} where it_id = %d order by io_no asc ", $it_id), ARRAY_A
                    );
        $lst_count = 0;
        foreach($result as $row){
            $opt_list[$row['io_type']][$row['io_id']]['id'] = $row['io_id'];
            $opt_list[$row['io_type']][$row['io_id']]['use'] = $row['io_use'];
            $opt_list[$row['io_type']][$row['io_id']]['price'] = $row['io_price'];
            $opt_list[$row['io_type']][$row['io_id']]['stock'] = $row['io_stock_qty'];

            // 선택옵션 개수
            if(!$row['io_type'])
                $lst_count++;
        }

        //--------------------------------------------------------
        //  재고 검사, 바로구매일 때만 체크
        //--------------------------------------------------------
        // 이미 주문폼에 있는 같은 상품의 수량합계를 구한다.
        if($sw_direct) {
            for($k=0; $k<$opt_count; $k++) {
                $io_id = sanitize_text_field($_POST['io_id'][$it_id][$k]);
                $io_type = sanitize_text_field($_POST['io_type'][$it_id][$k]);
                $io_value = sanitize_text_field($_POST['io_value'][$it_id][$k]);

                $sql = $wpdb->prepare(" select SUM(ct_qty) as cnt from {$gc['shop_cart_table']}
                          where od_id <> '%s'
                            and it_id = %d
                            and io_id = '%s'
                            and io_type = '%s'
                            and ct_stock_use = 0
                            and ct_status = '%s'
                            and ct_select = '1' ", $tmp_cart_id, $it_id, $io_id, $io_type, gc_get_stype_names('shopping'));

                $sum_qty = $wpdb->get_var($sql);

                // 재고 구함
                $ct_qty = $_POST['ct_qty'][$it_id][$k];
                if(!$io_id)
                    $it_stock_qty = gc_get_it_stock_qty($it_id);
                else
                    $it_stock_qty = gc_get_option_stock_qty($it_id, $io_id, $io_type);

                if ( intval($ct_qty) + intval($sum_qty) > intval($it_stock_qty) )
                {
                    gc_alert($io_value." 의 재고수량이 부족합니다.\\n\\n현재 재고수량 : " . number_format(intval($it_stock_qty) - intval($sum_qty)) . " 개", '', true);
                }
            }
        }
        //--------------------------------------------------------

        // 옵션수정일 때 기존 장바구니 자료를 먼저 삭제
        if($act == 'optionmod'){
            $result = $wpdb->query(
                        $wpdb->prepare(" delete from {$gc['shop_cart_table']} where od_id = '%s' and it_id = %d ", $tmp_cart_id, $it_id)
                    );
        }
        // 장바구니에 Insert
        // 바로구매일 경우 장바구니가 체크된것으로 강제 설정
        if($sw_direct) {
            $ct_select = 1;
            $ct_select_time = GC_TIME_YMDHIS;
        } else {
            $ct_select = 0;
            $ct_select_time = '0000-00-00 00:00:00';
        }

        // 장바구니에 Insert
        $comma = '';
        $fileds_array = array('od_id', 'mb_id', 'it_id', 'it_name', 'it_sc_type', 'it_sc_method', 'it_sc_price', 'it_sc_minimum', 'it_sc_qty', 'ct_status', 'ct_price', 'ct_point', 'ct_point_use', 'ct_stock_use', 'ct_option', 'ct_qty', 'ct_notax', 'io_id', 'io_type', 'io_price', 'ct_time', 'ct_ip', 'ct_send_cost', 'ct_direct', 'ct_select', 'ct_select_time');

        for($k=0; $k<$opt_count; $k++) {
            $io_id = sanitize_text_field($_POST['io_id'][$it_id][$k]);
            $io_type = sanitize_text_field($_POST['io_type'][$it_id][$k]);
            $io_value = sanitize_text_field($_POST['io_value'][$it_id][$k]);

            // 선택옵션정보가 존재하는데 선택된 옵션이 없으면 건너뜀
            if($lst_count && $io_id == '')
                continue;

            // 구매할 수 없는 옵션은 건너뜀
            if($io_id && !$opt_list[$io_type][$io_id]['use'])
                continue;

            $io_price = isset($opt_list[$io_type][$io_id]['price']) ? $opt_list[$io_type][$io_id]['price'] : 0;
            $ct_qty = $_POST['ct_qty'][$it_id][$k];

            // 구매가격이 음수인지 체크
            if($io_type) {
                if((int)$io_price < 0)
                    gc_alert('구매금액이 음수인 상품은 구매할 수 없습니다.');
            } else {
                if((int)$it['it_price'] + (int)$io_price < 0)
                    gc_alert('구매금액이 음수인 상품은 구매할 수 없습니다.');
            }

            // 동일옵션의 상품이 있으면 수량 더함
            $sql = $wpdb->prepare(" select ct_id, io_type, ct_qty
                        from {$gc['shop_cart_table']}
                        where od_id = '%s'
                          and it_id = %d
                          and io_id = '%s'
                          and ct_status = '%s' ", $tmp_cart_id, $it_id, $io_id, gc_get_stype_names('shopping'));

            $row2 = $wpdb->get_row($sql, ARRAY_A);

            if($row2['ct_id']) {
                // 재고체크
                $tmp_ct_qty = $row2['ct_qty'];
                if(!$io_id)
                    $tmp_it_stock_qty = gc_get_it_stock_qty($it_id);
                else
                    $tmp_it_stock_qty = gc_get_option_stock_qty($it_id, $io_id, $row2['io_type']);

                if ($tmp_ct_qty + $ct_qty > $tmp_it_stock_qty)
                {
                    gc_alert($io_value." 의 재고수량이 부족합니다.\\n\\n현재 재고수량 : " . number_format($tmp_it_stock_qty) . " 개");
                }

                $result = $wpdb->query(
                            $wpdb->prepare(" update {$gc['shop_cart_table']}
                            set ct_qty = ct_qty + %d
                            where ct_id = %d ", $ct_qty, $row2['ct_id'])
                                );
                continue;
            }

            // 적립금 체크
            $mileage = 0;
            
            if($config['cf_use_mileage']) {
                if($io_type == 0) {
                    $mileage = gc_get_item_point($it, $io_id);
                } else {
                    $mileage = $it['it_supply_point'];
                }

                if($mileage < 0)
                    $mileage = 0;
            }

            $ct_send_cost = 0;

            // 배송비결제
            if($it['it_sc_type'] == 1)
                $ct_send_cost = 2; // 무료
            else if($it['it_sc_type'] > 1 && $it['it_sc_method'] == 1)
                $ct_send_cost = 1; // 착불
            
            $datas = array(
                'od_id' => $tmp_cart_id, 
                'user_id' => get_current_user_id(), 
                'it_id' => $it['ID'], 
                'it_name' => addslashes($it['post_title']), 
                'it_sc_type' => $it['it_sc_type'], 
                'it_sc_method' => $it['it_sc_method'], 
                'it_sc_price' => $it['it_sc_price'], 
                'it_sc_minimum' => $it['it_sc_minimum'], 
                'it_sc_qty' => $it['it_sc_qty'], 
                'ct_status' => gc_get_stype_names('shopping'),
                'ct_price'  =>  $it['it_price'],
                'ct_point'  =>  (int) $mileage,
                'ct_point_use'  => 0, 
                'ct_stock_use' => 0, 
                'ct_option' => $io_value, 
                'ct_qty' => $ct_qty, 
                'ct_notax'  =>  $it['it_notax'], 
                'io_id' =>  $io_id, 
                'io_type'   =>  $io_type, 
                'io_price'  =>  $io_price, 
                'ct_time'   =>  GC_TIME_YMDHIS, 
                'ct_ip' =>  $_SERVER['REMOTE_ADDR'],
                'ct_send_cost'  =>  $ct_send_cost, 
                'ct_direct' =>  $sw_direct, 
                'ct_select' =>  $ct_select, 
                'ct_select_time'    =>  $ct_select_time);

            $formats = array(
                '%.0f',     //od_id
                '%.0f',     //user_id
                '%.0f',     //it_id
                '%s',   //it_name
                '%d',   //it_sc_type
                '%d',   //it_sc_method
                '%d',   //it_sc_price
                '%d',   //it_sc_minimum
                '%d',   //it_sc_qty
                '%s',   //ct_status
                '%d',   //ct_price
                '%d', //ct_point
                '%d',   //ct_point_use
                '%d',   //ct_stock_use
                '%s', //ct_option
                '%d', //ct_qty
                '%d',   //ct_notax
                '%s',   //io_id
                '%d',   //io_type
                '%d',   //io_price
                '%s',  //ct_time
                '%s',   //ct_ip
                '%d',   //ct_send_cost
                '%d',   //ct_direct
                '%d',   //ct_select
                '%s',   //ct_select_time
                );
            $result = $wpdb->insert($gc['shop_cart_table'], $datas, $formats);

            $ct_count++;
        }

    }
}

// 바로 구매일 경우
if ($sw_direct)
{
    wp_safe_redirect( add_query_arg(array('sw_direct'=>$sw_direct), gc_get_page_url('checkout')) );
}
else
{
    wp_safe_redirect( gc_get_page_url('cart') );
}

//장바구니 페이지로 이동

exit;
?>