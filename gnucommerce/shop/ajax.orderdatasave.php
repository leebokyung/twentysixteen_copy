<?php
if( !defined('GC_NAME') ) exit;

if(empty($_POST))
    wp_die('정보가 넘어오지 않았습니다.');

if(isset($_POST['pp_id']) && $_POST['pp_id'])
    $od_id = gc_get_session('ss_personalpay_id');
else
    $od_id = gc_get_session('ss_order_id');

// 일정 기간이 경과된 임시 데이터 삭제
$limit_time = date("Y-m-d H:i:s", (GC_SERVER_TIME - 86400 * 1));
$sql = $wpdb->prepare(" delete from {$gc['shop_order_data_table']} where dt_time < '%s' ", $limit_time);

$wpdb->query($sql);

$_POST['sw_direct'] = gc_get_session('ss_direct');

$dt_data = base64_encode(maybe_serialize($_POST));

// 동일한 주문번호가 있는지 체크
$sql = $wpdb->prepare(" select count(*) as cnt from {$gc['shop_order_data_table']} where od_id = %.0f ", $od_id);

$row_cnt = $wpdb->get_var($sql);

if($row_cnt)
    $wpdb->query($wpdb->prepare(" delete from {$gc['shop_order_data_table']} where od_id = %.0f ", $od_id));

$data = array(
'od_id' => $od_id,
'dt_pg' =>  $config['de_pg_service'],
'dt_data'   =>  $dt_data,
'dt_time'   =>  GC_TIME_YMDHIS,
);

$formats = array(
    '%.0f',
    '%s',
    '%s',
    '%s',
);
$result = $wpdb->insert($gc['shop_order_data_table'], $data, $formats);
?>