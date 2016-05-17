<?php
if( !defined('GC_NAME') ) exit;

$check_param = array('sfl', 'stx', 'npage', 'sst', 'sod');
$params = array();

foreach($check_param as $v){
    $params[$v] = isset($_REQUSET[$v]) ? sanitize_text_field($_REQUSET[$v]) : '';
}

extract($params);

$check_sfls = array('b.it_name', 'a.it_id', 'a.iq_subject', 'a.iq_question', 'a.iq_name', 'a.mb_id');

$sql_common = " from `{$gc['shop_item_qa_table']}` a JOIN `{$gc['shop_item_table']}` b ON (a.it_id=b.it_id) JOIN `{$wpdb->posts}` c ON (c.ID=b.it_id) ";
$sql_search = " where c.post_status = 'publish' AND c.post_type = '".GC_NAME."' ";

if(!$sfl)
    $sfl = 'b.it_name';

if ($stx && in_array($sfl, $check_sfls) ) { //허용된 필드만 검색
    $sql_search .= " and ( ";
    switch ($sfl) {
        case "a.it_id" :
            $sql_search .= $wpdb->prepare(" (".esc_sql($sfl)." like '%s') ", $stx.'%');
            break;
        case "a.iq_name" :
        case "a.mb_id" :
            $sql_search .= $wpdb->prepare(" (".esc_sql($sfl)." = '%s') ", $stx);
            break;
        default :
            $sql_search .= $wpdb->prepare(" (".esc_sql($sfl)." like '%s') ", '%'.$stx.'%');;
            break;
    }
    $sql_search .= " ) ";
}

if (!$sst) {
    $sst  = "a.iq_id";
    $sod = "desc";
}
$sql_order = " order by ".esc_sql($sst)." ".esc_sql($sod)." ";

$rows = $config['cf_page_rows'] ? $config['cf_page_rows'] : 10;
if ($npage < 1) { $npage = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($npage - 1) * $rows; // 시작 열을 구함

$sql = " select SQL_CALC_FOUND_ROWS a.*, b.it_name
          $sql_common
          $sql_search
          $sql_order
          limit $from_record, $rows ";

$results = $wpdb->get_results($sql, ARRAY_A);
$total_count = (int) $wpdb->get_var('SELECT FOUND_ROWS()');

$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산

$qa_args = array(
'rows'  => $rows,
'npage' => $npage,
'results'   =>  $results,
'total_count'   => $total_count,
'total_page'    => $total_page,
);

gc_skin_load('itemqalist.skin.php', wp_parse_args($qa_args, $params));
?>