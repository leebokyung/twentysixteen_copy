<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;

$check_params = array('it_id', 'ss_hp', 'agree');
$params = array();

foreach( $check_params as $v ){
    $params[$v] = isset($_POST[$v]) ? sanitize_text_field($_POST[$v]) : '';
}

extract($params);

$it = gc_get_it_array($it_id);

if(!isset($it['it_id']) || empty($it['it_id']) ){
    die(wp_json_encode( array('msg'=>__("상품정보가 존재하지 않습니다.", GC_NAME)) ));
}

if(!$it['it_soldout'] || !$it['it_stock_sms'])
    die(wp_json_encode( array('msg'=>__("재입고SMS 알림을 신청할 수 없는 상품입니다.", GC_NAME)) ));

$ss_hp = gc_hyphen_hp_number($ss_hp);
if(!$ss_hp)
    die(wp_json_encode( array('msg'=>__("휴대폰번호를 입력해 주십시오.", GC_NAME)) ));

if(!$agree)
     die(wp_json_encode( array('msg'=>__("개인정보처리방침안내에 동의해 주십시오.", GC_NAME)) ));

// 중복등록 체크
$sql = $wpdb->prepare("select count(*) as cnt from {$wpdb->gc_shop_item_stocksms_table} where it_id = '%s' and ss_hp = '%s' and ss_send = '0' ", $it_id, $ss_hp);
if( $cnt = $wpdb->get_var($sql) ){
    die(wp_json_encode( array('msg'=>__("해당 상품에 대하여 이전에 알림 요청을 등록한 내역이 있습니다.", GC_NAME)) ));
}

// 정보입력
$datas = array(
    'it_id' => $it_id,
    'ss_hp' => $ss_hp,
    'ss_ip' => isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
    'ss_datetime'   =>  GC_TIME_YMDHIS,
);

$formats = array(
    'it_id' => '%s',
    'ss_hp' => '%s',
    'ss_ip' => '%s',
    'ss_datetime'   =>  '%s',
);

$result = $wpdb->insert( $wpdb->gc_shop_item_stocksms_table, $datas, $formats );

if( $result === false ){
    die(wp_json_encode( array('msg'=>__("재입고SMS 알림 요청이 실패했습니다.", GC_NAME)) ));
} else {
    $msg = array(
        'msg'=>'true',
        's' => __('재입고SMS 알림 요청 등록이 완료됐습니다.', GC_NAME),
        );
    die(wp_json_encode( $msg ));
}
?>