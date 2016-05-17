<?php
if( !defined('GC_NAME') ) exit;

// Prevent timeout
@set_time_limit(0);

$param_array = array('od_settle_case', 'od_b_zip', 'od_temp_mileage', 'escw_yn', 'od_email', 'od_name', 'od_tel', 'od_hp', 'od_zip', 'od_addr1', 'od_addr2', 'od_addr3', 'od_addr_jibeon', 'od_b_name', 'od_b_tel', 'od_b_hp', 'od_b_addr1', 'od_b_addr2', 'od_b_addr3', 'od_b_addr_jibeon', 'od_deposit_name', 'od_send_cost', 'od_send_cost2', 'od_bank_account', 'ad_subject', 'od_cp_id', 'sc_cp_id', 'bankname', 'od_hope_date');

$encfilelds = array();

if( $_POST['od_settle_case'] != gc_get_stype_names('banktransfer') ){  //무통장이 아니면
    //이니시스 필드 체크 추가
    $tmp = array('currency', 'gopaymethod', 'oid', 'quotainterest', 'paymethod', 'cardcode', 'cardquota', 'rbankcode', 'reqsign', 'uid', 'sid', 'version', 'clickcontrol', 'good_mny', 'goodname', 'buyername', 'buyeremail', 'parentemail', 'buyertel', 'recvname', 'recvtel', 'recvaddr', 'recvpostnum', 'quotainterest');
    
    $param_array = array_merge($tmp, $param_array);

    $encfilelds = array('ini_encfield', 'acceptmethod', 'encrypted', 'sessionkey', 'ini_certid');

    $param_array = array_merge($encfilelds, $param_array);
}

global $current_user;
get_currentuserinfo();

$tmp_store = array();

foreach( $param_array as $v ){
    if( $encfilelds && in_array($v, $encfilelds) ){
        $tmp_store[$v] = isset($_POST[$v]) ? gc_encfilelds_filter($_POST[$v]) : '';
    } else {
        $tmp_store[$v] = isset($_POST[$v]) ? sanitize_text_field($_POST[$v]) : '';
    }
}

extract( $tmp_store );

$od_memo = isset($_POST['od_memo']) ? implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['od_memo'] ) ) ) : '';

gc_debug_log_make( $_POST);
gc_debug_log_make($tmp_store, '_t');
$page_return_url = '';  //변수초기화

// 결제등록 완료 체크
if(GC_IS_MOBILE) {  //모바일이면

    $page_return_url = gc_get_page_url('checkout');
    if(gc_get_session('ss_direct')) {
        $page_return_url .= add_query_arg( array('sw_direct'=>1), $page_return_url);
    }
    if( $od_settle_case != gc_get_stype_names('banktransfer') ){
        if($config['de_pg_service'] == 'inicis' && !gc_request_key_check('P_HASH') ){
            gc_alert(__('결제등록 요청 후 주문해 주십시오.', GC_NAME), $page_return_url);
        }
    }

} else {    //PC이면
    if(($od_settle_case != gc_get_stype_names('banktransfer') && $od_settle_case != 'KAKAOPAY') && $config['de_pg_service'] == 'lg' && !isset($_POST['LGD_PAYKEY']) ){
        gc_alert(__('결제등록 요청 후 주문해 주십시오.', GC_NAME));
    }
}

// 장바구니가 비어있는가?
if (gc_get_session("ss_direct"))
    $tmp_cart_id = gc_get_session('ss_cart_direct');
else
    $tmp_cart_id = gc_get_session('ss_cart_id');

if (gc_get_cart_count($tmp_cart_id) == 0)// 장바구니에 담기
    gc_alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', gc_get_page_url('cart', true) );

$error = '';

// 장바구니 상품 재고 검사
$sql = $wpdb->prepare(" select it_id,
                ct_qty,
                it_name,
                io_id,
                io_type,
                ct_option
           from {$gc['shop_cart_table']}
          where od_id = %.0f
            and ct_select = '1' ", $tmp_cart_id);

$rows = $wpdb->get_results($sql, ARRAY_A);

foreach( $rows as $row ){

    if( empty($row) ) continue;

    // 상품에 대한 현재고수량
    if($row['io_id']) {
        $it_stock_qty = (int)gc_get_option_stock_qty($row['it_id'], $row['io_id'], $row['io_type']);
    } else {
        $it_stock_qty = (int)gc_get_it_stock_qty($row['it_id']);
    }
    // 장바구니 수량이 재고수량보다 많다면 오류
    if ($row['ct_qty'] > $it_stock_qty){
        $error .= "{$row['ct_option']} 의 재고수량이 부족합니다. 현재고수량 : $it_stock_qty 개\\n\\n";
    }
}

if(!count($rows))
    gc_alert('장바구니가 비어 있습니다.\\n\\n이미 주문하셨거나 장바구니에 담긴 상품이 없는 경우입니다.', gc_get_page_url('cart', true) );

if ($error != "")
{
    $error .= "다른 고객님께서 {$od_name}님 보다 먼저 주문하신 경우입니다. 불편을 끼쳐 죄송합니다.";
    gc_alert($error, $page_return_url);
}

$i_price     = isset($_POST['od_price']) ? (int)$_POST['od_price'] : 0;
$i_send_cost  = isset($_POST['od_send_cost']) ? (int)$_POST['od_send_cost'] : 0;
$i_send_cost2  = isset($_POST['od_send_cost2']) ? (int)$_POST['od_send_cost2'] : 0;
$i_send_coupon  = isset($_POST['od_send_coupon']) ? (int)$_POST['od_send_coupon'] : 0;
$i_temp_point = isset($_POST['od_temp_mileage']) ? (int)$_POST['od_temp_mileage'] : 0;

// 주문금액이 상이함
$sql = $wpdb->prepare(" select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as od_price,
              COUNT(distinct it_id) as cart_count
            from {$gc['shop_cart_table']} where od_id = %.0f and ct_select = '1' ", $tmp_cart_id);

$row = $wpdb->get_row($sql, ARRAY_A);
$tot_ct_price = $row['od_price'];
$cart_count = $row['cart_count'];
$tot_od_price = $tot_ct_price;

// 쿠폰금액계산
$tot_cp_price = 0;
$tot_it_cp_price = $tot_od_cp_price = 0;

if( is_user_logged_in() ) {
    // 상품쿠폰
    $it_cp_cnt = isset($_POST['cp_id']) ? count($_POST['cp_id']) : 0;
    $arr_it_cp_prc = array();
    for($i=0; $i<$it_cp_cnt; $i++) {
        $cid = sanitize_text_field($_POST['cp_id'][$i]);
        $it_id = absint($_POST['it_id'][$i]);
        $sql = $wpdb->prepare(" select cp_id, cp_method, cp_target, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$gc['shop_coupon_table']}
                    where cp_id = '%s'
                      and mb_id IN ( '".get_current_user_id()."', '%s' )
                      and cp_start <= '%s'
                      and cp_end >= '%s'
                      and cp_method IN ( 0, 1 ) ", $cid, gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD );

        $cp = $wpdb->get_row($sql, ARRAY_A);
        if(!$cp['cp_id'])
            continue;

        // 사용한 쿠폰인지
        if(gc_is_used_coupon(get_current_user_id(), $cp['cp_id']))
            continue;

        // 분류할인인지
        if($cp['cp_method']) {
            $sql = $wpdb->prepare(" select it_id, ca_id, ca_id2, ca_id3
                        from {$gc['shop_item_table']}
                        where it_id = %.0f ", $it_id);
            $row2 = $wpdb->get_row($sql, ARRAY_A);

            if(!$row2['it_id'])
                continue;

            if($row2['ca_id'] != $cp['cp_target'] && $row2['ca_id2'] != $cp['cp_target'] && $row2['ca_id3'] != $cp['cp_target'])
                continue;
        } else {
            if($cp['cp_target'] != $it_id)
                continue;
        }

        // 상품금액
        $sql = $wpdb->prepare(" select SUM( IF(io_type = '1', io_price * ct_qty, (ct_price + io_price) * ct_qty)) as sum_price
                    from {$gc['shop_cart_table']}
                    where od_id = %.0f
                      and it_id = %.0f
                      and ct_select = '1' ", $tmp_cart_id, $it_id);
        $ct = $wpdb->get_row($sql, ARRAY_A);
        $item_price = $ct['sum_price'];

        if($cp['cp_minimum'] > $item_price)
            continue;

        $dc = 0;
        if($cp['cp_type']) {
            $dc = floor(($item_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
        } else {
            $dc = $cp['cp_price'];
        }

        if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
            $dc = $cp['cp_maximum'];

        if($item_price < $dc)
            continue;

        $tot_it_cp_price += $dc;
        $arr_it_cp_prc[$it_id] = $dc;
    }

    $tot_od_price -= $tot_it_cp_price;

    // 주문쿠폰
    if( $od_cp_id ) {
        $sql = $wpdb->prepare(" select cp_id, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$gc['shop_coupon_table']}
                    where cp_id = '%s'
                      and mb_id IN ( '".get_current_user_id()."', '%s' )
                      and cp_start <= '%s'
                      and cp_end >= '%s'
                      and cp_method = '2' ", $od_cp_id, gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD);
        $cp = $wpdb->get_row($sql, ARRAY_A);

        // 사용한 쿠폰인지
        $cp_used = gc_is_used_coupon(get_current_user_id(), $cp['cp_id']);

        $dc = 0;
        if(!$cp_used && $cp['cp_id'] && ($cp['cp_minimum'] <= $tot_od_price)) {
            if($cp['cp_type']) {
                $dc = floor(($tot_od_price * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
            } else {
                $dc = $cp['cp_price'];
            }

            if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
                $dc = $cp['cp_maximum'];

            if($tot_od_price < $dc)
                die('Order coupon error.');

            $tot_od_cp_price = $dc;
            $tot_od_price -= $tot_od_cp_price;
        }
    }

    $tot_cp_price = $tot_it_cp_price + $tot_od_cp_price;
}

if ((int)($row['od_price'] - $tot_cp_price) !== $i_price) {
    die("Error.");
}

// 배송비가 상이함
$send_cost = gc_get_sendcost($tmp_cart_id);
$tot_sc_cp_price = 0;   //초기화

if(get_current_user_id() && $send_cost > 0) {
    // 배송쿠폰
    if( $sc_cp_id ) {
        $sql = $wpdb->prepare(" select cp_id, cp_type, cp_price, cp_trunc, cp_minimum, cp_maximum
                    from {$gc['shop_coupon_table']}
                    where cp_id = '%s'
                      and mb_id IN ( '".get_current_user_id()."', '%s' )
                      and cp_start <= '%s'
                      and cp_end >= '%s'
                      and cp_method = '3' ", $sc_cp_id, gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD);
        $cp = $wpdb->get_row($sql, ARRAY_A);

        // 사용한 쿠폰인지
        $cp_used = gc_is_used_coupon(get_current_user_id(), $cp['cp_id']);

        $dc = 0;
        if(!$cp_used && $cp['cp_id'] && ($cp['cp_minimum'] <= $tot_od_price)) {
            if($cp['cp_type']) {
                $dc = floor(($send_cost * ($cp['cp_price'] / 100)) / $cp['cp_trunc']) * $cp['cp_trunc'];
            } else {
                $dc = $cp['cp_price'];
            }

            if($cp['cp_maximum'] && $dc > $cp['cp_maximum'])
                $dc = $cp['cp_maximum'];

            if($dc > $send_cost)
                $dc = $send_cost;

            $tot_sc_cp_price = $dc;
        }
    }
}

if ((int)($send_cost - $tot_sc_cp_price) !== (int)($i_send_cost - $i_send_coupon)) {
    die("Error..");
}

// 추가배송비가 상이함
$od_b_zip   = preg_replace('/[^0-9]/', '', $od_b_zip);
$zipcode = $od_b_zip;

$sql = $wpdb->prepare(" select sc_id, sc_price from {$gc['shop_sendcost_table']} where sc_zip1 <= '%s' and sc_zip2 >= '%s' ", $zipcode, $zipcode);

$tmp = $wpdb->get_row($sql, ARRAY_A);
if(!$tmp['sc_id'])
    $send_cost2 = 0;
else
    $send_cost2 = (int)$tmp['sc_price'];
if($send_cost2 !== $i_send_cost2)
    die("Error...");

$member = gc_get_member(get_current_user_id());

// 결제적립금이 상이함
// 회원이면서 적립금사용이면
$temp_mileage = 0;

if ( is_user_logged_in() && $config['cf_use_mileage'])
{
    if($member['mb_mileage'] >= $config['de_settle_min_mileage']) {
        $temp_mileage = (int)$config['de_settle_max_mileage'];

        if($temp_mileage > (int)$tot_od_price)
            $temp_mileage = (int)$tot_od_price;

        if($temp_mileage > (int)$member['mb_mileage'])
            $temp_mileage = (int)$member['mb_mileage'];

        $mileage_unit = (int)$config['de_settle_mileage_unit'];
        $temp_mileage = (int)((int)($temp_mileage / $mileage_unit) * $mileage_unit);
    }
}

if (($i_temp_point > (int)$temp_mileage || $i_temp_point < 0) && $config['cf_use_mileage'])
    die("Error....");

if ($od_temp_mileage)
{
    if ($member['mb_mileage'] < $od_temp_mileage)
        gc_alert('회원님의 적립금이 부족하여 적립금으로 결제 할 수 없습니다.');
}

$i_price = $i_price + $i_send_cost + $i_send_cost2 - $i_temp_point - $i_send_coupon;
$order_price = $tot_od_price + $send_cost + $send_cost2 - $tot_sc_cp_price - $od_temp_mileage;

//변수 초기화
$od_receipt_time = '';
$od_tno = '';
$od_app_no  = '';   //가상계좌

$od_status = gc_get_stype_names('order');  //주문
if ($od_settle_case == gc_get_stype_names('banktransfer'))    //무통장
{
    $od_receipt_point   = $i_temp_point;
    $od_receipt_price   = 0;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0) {
        $od_status      = gc_get_stype_names('deposit');    //입금
        $od_receipt_time = GC_TIME_YMDHIS;
    }
} else if ($od_settle_case == gc_get_stype_names('accounttransfer')){  //계좌이체
    switch($config['de_pg_service']) {
        case 'lg':
            include GC_SHOP_DIR_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            if(GC_IS_MOBILE){
                include GC_SHOP_DIR_PATH.'/inicis/m_pay_result.php';    //모바일이면
            } else {
                include GC_SHOP_DIR_PATH.'/inicis/inipay_result.php';  //pc이면
            }
            break;
        case 'kcp' :
        default:
            include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub.php';
            $bank_name  = iconv("cp949", "utf-8", $bank_name);
            break;
    }

    $od_tno             = $tno;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_deposit_name    = $od_name;
    $od_bank_account    = $bank_name;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = gc_get_stype_names('deposit');    //입금
}
else if ($od_settle_case == gc_get_stype_names('virtualaccount'))     //가상계좌
{
    switch($config['de_pg_service']) {
        case 'lg':
            include GC_SHOP_DIR_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            if(GC_IS_MOBILE){
                include GC_SHOP_DIR_PATH.'/inicis/m_pay_result.php';    //모바일이면
            } else {
                include GC_SHOP_DIR_PATH.'/inicis/inipay_result.php';
            }
            $od_app_no = $app_no;
            break;
        case 'kcp' :
        default:
            include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub.php';
            $bankname   = iconv("cp949", "utf-8", $bankname);
            $depositor  = iconv("cp949", "utf-8", $depositor);
            break;
    }

    $od_receipt_point   = $i_temp_point;
    $od_tno             = $tno;
    $od_receipt_price   = 0;
    $od_bank_account    = $bankname.' '.$account;
    $od_deposit_name    = $depositor;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
}
else if ($od_settle_case == gc_get_stype_names('phonepayment'))    //휴대폰
{
    switch($config['de_pg_service']) {
        case 'lg':
            include GC_SHOP_DIR_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            if(GC_IS_MOBILE){
                include GC_SHOP_DIR_PATH.'/inicis/m_pay_result.php';    //모바일이면
            } else {
                include GC_SHOP_DIR_PATH.'/inicis/inipay_result.php';
            }
            break;
        case 'kcp' :
        default:
            include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub.php';
            break;
    }

    $od_tno             = $tno;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $commid . ($commid ? ' ' : '').$mobile_no;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = gc_get_stype_names('deposit');    //입금
}
else if ($od_settle_case == gc_get_stype_names('creditcard'))  //신용카드
{
    switch($config['de_pg_service']) {
        case 'lg':
            include GC_SHOP_DIR_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            if(GC_IS_MOBILE){
                include GC_SHOP_DIR_PATH.'/inicis/m_pay_result.php';    //모바일이면
            } else {
                include GC_SHOP_DIR_PATH.'/inicis/inipay_result.php';
            }
            break;
        case 'kcp' :
        default:
            include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub.php';
            $card_name  = iconv("cp949", "utf-8", $card_name);
            break;
    }

    $od_tno             = $tno;
    $od_app_no          = $app_no;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $card_name;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = gc_get_stype_names('deposit');    //입금
}
else if ($od_settle_case == gc_get_stype_names('easypayment')) //간편결제
{
    switch($config['de_pg_service']) {
        case 'lg':
            include GC_SHOP_DIR_PATH.'/lg/xpay_result.php';
            break;
        case 'inicis':
            include GC_SHOP_DIR_PATH.'/inicis/inipay_result.php';
            break;
        case 'kcp' :
        default:
            include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub.php';
            $card_name  = iconv("cp949", "utf-8", $card_name);
            break;
    }

    $od_tno             = $tno;
    $od_app_no          = $app_no;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $card_name;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = gc_get_stype_names('deposit');    //입금
}
else if ($od_settle_case == "KAKAOPAY")
{
    include GC_SHOP_DIR_PATH.'/kakaopay/kakaopay_result.php';

    $od_tno             = $tno;
    $od_app_no          = $app_no;
    $od_receipt_price   = $amount;
    $od_receipt_point   = $i_temp_point;
    $od_receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $app_time);
    $od_bank_account    = $card_name;
    $pg_price           = $amount;
    $od_misu            = $i_price - $od_receipt_price;
    if($od_misu == 0)
        $od_status      = gc_get_stype_names('deposit');    //입금
}
else
{
    wp_die("od_settle_case Error!!!");
}

$od_pg = $config['de_pg_service'];
if($od_settle_case == 'KAKAOPAY')
    $od_pg = 'KAKAOPAY';

// 주문금액과 결제금액이 일치하는지 체크
if(isset($tno) && !empty($tno)) {
    if((int)$order_price !== (int)$pg_price) {
        $cancel_msg = '결제금액 불일치';
        switch($od_pg) {

            case 'KAKAOPAY':    //카카오페이
                $_REQUEST['TID']               = $tno;
                $_REQUEST['Amt']               = $amount;
                $_REQUEST['CancelMsg']         = $cancel_msg;
                $_REQUEST['PartialCancelCode'] = 0;
                include GC_SHOP_DIR_PATH.'/kakaopay/kakaopay_cancel.php';
                break;
            case 'lg':
                include GC_SHOP_DIR_PATH.'/lg/xpay_cancel.php';
                break;
            case 'inicis':  //이니시스
                include GC_SHOP_DIR_PATH.'/inicis/inipay_cancel.php';
                break;
            case 'kcp' : //kcp
            default:
                include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub_cancel.php';
                break;
        }

        wp_die("Receipt Amount Error");
    }
}

if (is_user_logged_in()){
    $od_pwd = gc_get_encrypt_string(gc_rand_string(8));   //회원인 경우 랜덤 패스워드를 넣는다.
} else {
    $od_pwd = gc_get_encrypt_string(strip_tags($_POST['od_pwd']));
}
// 주문번호를 얻는다.
$od_id = gc_get_session('ss_order_id');

$od_escrow = 0;
if($escw_yn == 'Y')
    $od_escrow = 1;

// 복합과세 금액
$od_tax_mny = round($i_price / 1.1);
$od_vat_mny = $i_price - $od_tax_mny;
$od_free_mny = 0;
if($config['de_tax_flag_use']) {
    $od_tax_mny = (int)$_POST['comm_tax_mny'];
    $od_vat_mny = (int)$_POST['comm_vat_mny'];
    $od_free_mny = (int)$_POST['comm_free_mny'];
}

$od_pg            = $config['de_pg_service'];
$od_email         = sanitize_email($od_email);
$od_name          = gc_clean_xss_tags($od_name);
$od_tel           = gc_clean_xss_tags($od_tel);
$od_hp            = gc_clean_xss_tags($od_hp);
$od_zip           = preg_replace('/[^0-9]/', '', $od_zip);
$od_addr1         = gc_clean_xss_tags($od_addr1);
$od_addr2         = gc_clean_xss_tags($od_addr2);
$od_addr3         = gc_clean_xss_tags($od_addr3);
$od_addr_jibeon   = preg_match("/^(N|R)$/", $od_addr_jibeon) ? $od_addr_jibeon : '';
$od_b_name        = gc_clean_xss_tags($od_b_name);
$od_b_tel         = gc_clean_xss_tags($od_b_tel);
$od_b_hp          = gc_clean_xss_tags($od_b_hp);
$od_b_addr1       = gc_clean_xss_tags($od_b_addr1);
$od_b_addr2       = gc_clean_xss_tags($od_b_addr2);
$od_b_addr3       = gc_clean_xss_tags($od_b_addr3);
$od_b_addr_jibeon = preg_match("/^(N|R)$/", $od_b_addr_jibeon) ? $od_b_addr_jibeon : '';
$od_memo          = gc_clean_xss_tags($od_memo);
$od_deposit_name  = gc_clean_xss_tags($od_deposit_name);

//주문서에 입력

$order_metas = array(
    'od_id' => $od_id,
    'mb_id' =>  get_current_user_id(),
    'od_pwd'    =>  $od_pwd,
    'od_name'   =>  $od_name,
    'od_email'  =>  $od_email,
    'od_tel'    =>  $od_tel,
    'od_hp'     =>  $od_hp,
    'od_zip'    =>  $od_zip,
    'od_addr1'  =>  $od_addr1,
    'od_addr2'  =>  $od_addr2,
    'od_addr3'  =>  $od_addr3,
    'od_addr_jibeon'    =>  $od_addr_jibeon,
    'od_b_name' =>  $od_b_name,
    'od_b_tel'  =>  $od_b_tel,
    'od_b_hp'   =>  $od_b_hp,
    'od_b_zip' =>  $od_b_zip,
    'od_b_addr1'    =>  $od_b_addr1,
    'od_b_addr2'    =>  $od_b_addr2,
    'od_b_addr3'    =>  $od_b_addr3,
    'od_b_addr_jibeon'  =>  $od_b_addr_jibeon,
    'od_deposit_name'   =>  $od_deposit_name,
    'od_memo'   =>  $od_memo,
    'od_cart_count' =>  $cart_count,
    'od_cart_price' =>  $tot_ct_price,
    'od_cart_coupon'    =>  $tot_it_cp_price,
    'od_send_cost'  =>  $od_send_cost,
    'od_send_coupon'    =>  $tot_sc_cp_price,
    'od_send_cost2' =>  $od_send_cost2,
    'od_coupon' =>  $tot_od_cp_price,
    'od_receipt_price'  =>  $od_receipt_price,
    'od_receipt_point'  =>  $od_receipt_point,
    'od_bank_account'   =>  $od_bank_account,
    'od_receipt_time'   =>  !empty($od_receipt_time) ? $od_receipt_time : '0000-00-00 00:00:00',
    'od_misu'   =>  $od_misu,
    'od_pg' =>  $od_pg,
    'od_tno'    =>  $od_tno,
    'od_app_no' =>  $od_app_no,
    'od_escrow' =>  $od_escrow,
    'od_tax_flag'   =>  $config['de_tax_flag_use'],
    'od_tax_mny'    =>  $od_tax_mny,
    'od_vat_mny'    =>  $od_vat_mny,
    'od_free_mny'   =>  $od_free_mny,
    'od_status'     =>  $od_status,
    'od_shop_memo'  =>  '',
    'od_hope_date'  =>  !empty($od_hope_date) ? $od_hope_date : '0000-00-00 00:00:00',
    'od_time'   => GC_TIME_YMDHIS,
    'od_ip' =>  isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
    'od_settle_case'    =>  $od_settle_case,
    'od_mod_history'    =>  '',
    'od_cash_info'  =>  '',
);

$order_formats = array(
    '%.0f', //od_id
    '%s',   //mb_id
    '%s', //od_pwd
    '%s',   //od_name
    '%s',   //od_email
    '%s',   //od_tel
    '%s',   //od_hp
    '%s',   //od_zip
    '%s',   //od_addr1
    '%s',   //od_addr2
    '%s',   //od_addr3
    '%s',   //od_addr_jibeon
    '%s',   //od_b_name
    '%s',   //od_b_tel
    '%s',   //od_b_hp
    '%s',   //od_b_zip
    '%s',   //od_b_addr1
    '%s',   //od_b_addr2
    '%s',   //od_b_addr3
    '%s',   //od_b_addr_jibeon
    '%s',   //od_deposit_name
    '%s',   //od_memo
    '%d',   //od_cart_count
    '%d',   //od_cart_price
    '%d',   //od_cart_coupon
    '%d',   //od_send_cost
    '%d',   //od_send_coupon
    '%d',   //od_send_cost2
    '%d',   //od_coupon
    '%d',   //od_receipt_price
    '%d',   //od_receipt_point
    '%s',   //od_bank_account
    '%s',   //od_receipt_time
    '%d',   //od_misu
    '%s',   //od_pg
    '%s',   //od_tno
    '%s',   //od_app_no
    '%d',   //od_escrow
    '%d',   //od_tax_flag
    '%d',   //od_tax_mny
    '%d',   //od_vat_mny
    '%d',   //od_free_mny
    '%s',   //od_status
    '%s',   //od_shop_memo
    '%s',   //od_hope_date
    '%s',   //od_time
    '%s',   //od_ip
    '%s',   //od_settle_case
    '%s',   //od_mod_history
    '%s',   //od_cash_info
);

if( GC_IS_MOBILE ){    //모바일이면
    $order_metas['od_mobile'] = 1;
    $order_formats[] = '%d';
}

try {
    $wpdb->query( 'START TRANSACTION' );

    $order_id = gc_order_post_insert($order_metas, $order_formats);

    if ( !$order_id ) {
        throw new Exception( sprintf( __( '오류 %d: 주문번호가 생성되지 않았습니다. 다시 시도해 주세요.', GC_NAME ), 400 ) );
    }

    // 장바구니 상태변경
    // 신용카드로 주문하면서 신용카드 적립금 사용하지 않는다면 적립금 부여하지 않음
    $cart_status = $od_status;
    $sql_card_point = "";
    if ($od_receipt_price > 0 && !$config['de_card_mileage']) {
        $sql_card_point = " , ct_point = '0' ";
    }
    $sql = $wpdb->prepare("update {$gc['shop_cart_table']}
               set od_id = %.0f,
                   ct_status = '%s'
                   $sql_card_point
             where od_id = %.0f
               and ct_select = '1' ", $order_id, $cart_status, $tmp_cart_id);
    $result = $wpdb->query($sql);

    if( $result === false ){
        if($tno) {
            $cancel_msg = '주문상태 변경 오류';
            switch($config['de_pg_service']) {
                case 'lg':
                    include GC_SHOP_DIR_PATH.'/lg/xpay_cancel.php';
                    break;
                case 'KAKAOPAY':
                    $_REQUEST['TID']               = $tno;
                    $_REQUEST['Amt']               = $amount;
                    $_REQUEST['CancelMsg']         = $cancel_msg;
                    $_REQUEST['PartialCancelCode'] = 0;
                    include GC_SHOP_DIR_PATH.'/kakaopay/kakaopay_cancel.php';
                    break;
                case 'inicis':
                    include GC_SHOP_DIR_PATH.'/inicis/inipay_cancel.php';
                    break;
                case 'kcp':
                default:
                    include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub_cancel.php';
                    break;
            }
        }

        // 관리자에게 오류 알림 메일발송
        $error = 'status';
        include GC_SHOP_DIR_PATH.'/ordererrormail.php';

        // 주문삭제
        $wpdb->query(
            $wpdb->prepare(" delete from {$gc['shop_order_table']} where od_id = %.0f ", $order_id));

         throw new Exception('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>'.strtoupper($config['de_pg_service']).'를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
    }

    // 회원이면서 적립금을 사용했다면 테이블에 사용을 추가
    if (is_user_logged_in() && $od_receipt_point)
        gc_insert_mileage(get_current_user_id(), (-1) * $od_receipt_point, "주문번호 $od_id 결제");

    $od_memo = nl2br(gc_htmlspecialchars2(stripslashes($od_memo))) . "&nbsp;";

    // 쿠폰사용내역기록
    if( is_user_logged_in() ){
        $it_cp_cnt = isset($_POST['cp_id']) ? count($_POST['cp_id']) : 0;
        for($i=0; $i<$it_cp_cnt; $i++) {
            $cid = sanitize_text_field($_POST['cp_id'][$i]);
            $cp_it_id = sanitize_text_field($_POST['it_id'][$i]);
            $cp_prc = isset($arr_it_cp_prc[$cp_it_id]) ? (int)$arr_it_cp_prc[$cp_it_id] : '';

            if(trim($cid)) {
                $sql = $wpdb->prepare(" insert into {$gc['shop_coupon_log_table']}
                            set cp_id       = '%s',
                                mb_id       = '".get_current_user_id()."',
                                od_id       = %.0f,
                                cp_price    = %d,
                                cl_datetime = '%s' ", $cid, $order_id, $cp_prc, GC_TIME_YMDHIS);
                $result = $wpdb->query($sql);
            }

            // 쿠폰사용금액 cart에 기록
            $cp_prc = isset($arr_it_cp_prc[$cp_it_id]) ? (int)$arr_it_cp_prc[$cp_it_id] : '';
            $sql = $wpdb->prepare(" update {$gc['shop_cart_table']}
                        set cp_price = %d
                        where od_id = %.0f
                          and it_id = %.0f
                          and ct_select = '1'
                        order by ct_id asc
                        limit 1 ", $cp_prc, $order_id, $cp_it_id);
            $result = $wpdb->query($sql);
        }
        if( $od_cp_id ) {
            $sql = $wpdb->prepare(" insert into {$gc['shop_coupon_log_table']}
                        set cp_id       = '%s',
                            mb_id       = '".get_current_user_id()."',
                            od_id       = %.0f,
                            cp_price    = %d,
                            cl_datetime = '%s' ", $od_cp_id, $order_id, $tot_od_cp_price, GC_TIME_YMDHIS);
            $result = $wpdb->query($sql);
        }

        if( $sc_cp_id ) {
            $sql = $wpdb->prepare(" insert into {$gc['shop_coupon_log_table']}
                        set cp_id       = '%s',
                            mb_id       = '".get_current_user_id()."',
                            od_id       = %.0f,
                            cp_price    = %d,
                            cl_datetime = '%s' ", $sc_cp_id, $order_id, $tot_sc_cp_price, GC_TIME_YMDHIS);
            $result = $wpdb->query($sql);
        }
    }

    // orderview 에서 사용하기 위해 session에 넣고
    $uid = md5($order_id.GC_TIME_YMDHIS.$_SERVER['REMOTE_ADDR']);
    gc_set_session('ss_orderview_uid', $uid);

    if( GC_IS_MOBILE ){
        // 주문 정보 임시 데이터 삭제
        $sql = $wpdb->prepare(" delete from {$gc['shop_order_data_table']} where od_id = %.0f and dt_pg = '%s' ", $od_id, $od_pg);
        $wpdb->query($sql);
    }

    // 주문번호제거
    gc_set_session('ss_order_id', '');

    // 기존자료 세션에서 제거
    if (gc_get_session('ss_direct'))
        gc_set_session('ss_cart_direct', '');

    // 배송지처리
    if( is_user_logged_in() ) {
        $meta_address = array(
            'mb_name' => $od_name,  //이름
            'mb_tel' => $od_tel,    //전화번호
            'mb_hp' => $od_hp,      //핸드폰
            'mb_zip' => $od_zip,  //우편번호
            'mb_addr1' => $od_addr1,    //기본주소
            'mb_addr2' => $od_addr2,    //상세주소
            'mb_addr3' => $od_addr3,    //참고항목
        );
        
        if( !($member['mb_name'] && $member['mb_zip'] && $member['mb_hp'] && $member['mb_addr1']) ){    //회원 주문하시는분 저장 (데이터가 없으면)
            gc_user_meta_save($meta_address);
        }

        $input_address = array(
            'ad_subject' => $ad_subject,
            'ad_name' => $od_b_name,
            'ad_tel' => $od_b_tel,
            'ad_hp' => $od_b_hp,
            'ad_zip' => $od_b_zip,
            'ad_addr1' => $od_b_addr1,
            'ad_addr2' => $od_b_addr2,
            'ad_addr3' => $od_b_addr3,
        );

        if( $user_address_list = gc_get_user_address_list() ){
        
            $result_count = gc_multi_array_search($user_address_list, array_intersect_key($input_address, gc_get_user_address_keys()) );

            if( empty($result_count) || !count($result_count) ){    //중복된 주소가 없으면
                $input_address['ad_id'] = time();
                array_unshift($user_address_list, $input_address);
            }
        } else {
            $input_address['ad_id'] = time();
            $user_address_list = array($input_address);
        }

        update_user_meta(get_current_user_id(), 'gc_user_address', $user_address_list);
    }

    $wpdb->query( 'COMMIT' );

} catch ( Exception $e ) {
    $wpdb->query( 'ROLLBACK' );
    $return = new WP_Error( 'checkout-error', $e->getMessage() );

    if( is_wp_error( $return ) ) {
        echo $return->get_error_message();
    }

    // 주문정보 입력 오류시 결제 취소
    if(isset($tno) && empty($tno)) {
        $cancel_msg = '주문정보 입력 오류';
        switch($od_pg) {
            case 'lg':
                include GC_SHOP_DIR_PATH.'/lg/xpay_cancel.php';
                break;
            case 'KAKAOPAY':
                $_REQUEST['TID']               = $tno;
                $_REQUEST['Amt']               = $amount;
                $_REQUEST['CancelMsg']         = $cancel_msg;
                $_REQUEST['PartialCancelCode'] = 0;
                include GC_SHOP_DIR_PATH.'/kakaopay/kakaopay_cancel.php';
                break;
            case 'inicis':
                include GC_SHOP_DIR_PATH.'/inicis/inipay_cancel.php';
                break;
            case 'kcp':
            default:
                include GC_SHOP_DIR_PATH.'/kcp/pp_ax_hub_cancel.php';
                break;
        }
    }

    // 관리자에게 오류 알림 메일발송
    $error = 'order';
    include GC_SHOP_DIR_PATH.'/ordererrormail.php';

    wp_die('<p>고객님의 주문 정보를 처리하는 중 오류가 발생해서 주문이 완료되지 않았습니다.</p><p>'.strtoupper($config['de_pg_service']).'를 이용한 전자결제(신용카드, 계좌이체, 가상계좌 등)은 자동 취소되었습니다.');
}

include_once(GC_SHOP_DIR_PATH.'/ordermail1.inc.php');
include_once(GC_SHOP_DIR_PATH.'/ordermail2.inc.php');

// SMS BEGIN --------------------------------------------------------
// 주문고객과 쇼핑몰관리자에게 SMS 전송
if($config['cf_sms_use'] && ($config['de_sms_use2'] || $config['de_sms_use3'])) {
    $is_sms_send = false;

    // 충전식일 경우 잔액이 있는지 체크
    if($config['cf_icode_id'] && $config['cf_icode_pw']) {
        $userinfo = gc_get_icode_userinfo($config['cf_icode_id'], $config['cf_icode_pw']);

        if($userinfo['code'] == 0) {
            if($userinfo['payment'] == 'C') { // 정액제
                $is_sms_send = true;
            } else {
                $minimum_coin = 100;
                if(defined('GC_ICODE_COIN'))
                    $minimum_coin = intval(GC_ICODE_COIN);

                if((int)$userinfo['coin'] >= $minimum_coin)
                    $is_sms_send = true;
            }
        }
    }

    if($is_sms_send) {
        $sms_contents = array($config['de_sms_cont2'], $config['de_sms_cont3']);
        $recv_numbers = array($od_hp, $config['de_sms_hp']);
        $send_numbers = array($config['de_admin_company_tel'], $od_hp);

        include_once(GC_DIR_PATH.'lib/icode.sms.lib.php');

        $SMS = new GC_SMS; // SMS 연결
        $SMS->SMS_con($config['cf_icode_server_ip'], $config['cf_icode_id'], $config['cf_icode_pw'], $config['cf_icode_server_port']);
        $sms_count = 0;

        for($s=0; $s<count($sms_contents); $s++) {
            $sms_content = $sms_contents[$s];
            $recv_number = preg_replace("/[^0-9]/", "", $recv_numbers[$s]);
            $send_number = preg_replace("/[^0-9]/", "", $send_numbers[$s]);

            $sms_content = str_replace("{이름}", $od_name, $sms_content);
            $sms_content = str_replace("{보낸분}", $od_name, $sms_content);
            $sms_content = str_replace("{받는분}", $od_b_name, $sms_content);
            $sms_content = str_replace("{주문번호}", $order_id, $sms_content);
            $sms_content = str_replace("{주문금액}", number_format($tot_ct_price + $od_send_cost + $od_send_cost2), $sms_content);
            $sms_content = str_replace("{회원아이디}", $member['user_login'], $sms_content);
            $sms_content = str_replace("{회사명}", $config['de_admin_company_name'], $sms_content);

            $idx = 'de_sms_use'.($s + 2);

            if($config[$idx] && $recv_number) {
                $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", stripslashes($sms_content)), "");
                $sms_count++;
            }
        }

        // 무통장 입금 때 고객에게 계좌정보 보냄
        if($od_settle_case == gc_get_stype_names('banktransfer') && $config['de_sms_use2'] && $od_misu > 0) {  //무통장
            $sms_content = $od_name."님의 입금계좌입니다.\n금액:".number_format($od_misu)."원\n계좌:".$od_bank_account."\n".$config['de_admin_company_name'];

            $recv_number = preg_replace("/[^0-9]/", "", $od_hp);
            $send_number = preg_replace("/[^0-9]/", "", $config['de_admin_company_tel']);
            $SMS->Add($recv_number, $send_number, $config['cf_icode_id'], iconv("utf-8", "euc-kr", $sms_content), "");
            $sms_count++;
        }

        if($sms_count > 0)
            $SMS->Send();
    }
}
// SMS END   --------------------------------------------------------

// 사용자 결제 성공일때 hook
do_action('gc_user_complete_pay', $order_id, $uid, $order_metas );

gc_goto_url( add_query_arg( array('order'=>1, 'order_id'=>$order_id, 'uid'=>$uid), get_permalink() ) );
?>
<h1>
결제중입니다. 브라우저를 끄지 말고 기다려주세요.
</h1>
<script>
    // 결제 중 새로고침 방지 샘플 스크립트 (중복결제 방지)
    function noRefresh()
    {
        //CTRL + N키 막음.
        if ((event.keyCode == 78) && (event.ctrlKey == true))
        {
            event.keyCode = 0;
            return false;
        }
        //F5 번키 막음.
        if(event.keyCode == 116)
        {
            event.keyCode = 0;
            return false;
        }
    }

    document.onkeydown = noRefresh ;
</script>