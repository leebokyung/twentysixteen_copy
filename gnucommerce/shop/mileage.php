<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

//wp-super-cache 사용시 이 페이지는 cache하지 않음
if ( ! defined( 'DONOTCACHEPAGE' ) ) {
    define( 'DONOTCACHEPAGE', true );
}

$npage = isset($_REQUEST['npage']) ? (int) $_REQUEST['npage'] : 0;

if (!is_user_logged_in())
    gc_alert('회원만 조회하실 수 있습니다.');

$sql_common = $wpdb->prepare(" from {$gc['mileage_table']} where user_id = '%s' ", get_current_user_id());
$sql_order = " order by mi_id desc ";

$p_rows = $config['cf_page_rows'] ? $config['cf_page_rows'] : 15;
if ($npage < 1) { $npage = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($npage - 1) * $p_rows; // 시작 열을 구함

$sql = " select SQL_CALC_FOUND_ROWS * {$sql_common} {$sql_order} limit {$from_record}, {$p_rows} ";
$results = $wpdb->get_results($sql, ARRAY_A);

$total_count = (int) $wpdb->get_var('SELECT FOUND_ROWS()');
$total_page  = ceil($total_count / $p_rows);  // 전체 페이지 계산

$member = gc_get_member(get_current_user_id());

gc_skin_load('mileage.skin.php', array(
    'results' => $results,
    'total_count' => $total_count,
    'total_page' => $total_page,
    'npage' => $npage,
    'member'    => $member,
));
?>