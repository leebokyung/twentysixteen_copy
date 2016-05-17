<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;

$param_array = array('qpage');

foreach($param_array as $v){
    $$v = isset($_GET[$v]) ? sanitize_text_field($_GET[$v]) : '';
}

$qpage = $qpage ? $qpage : 0;

/*
// 현재페이지, 총페이지수, 한페이지에 보여줄 행, URL
function gc_itemqa_page($write_pages, $cur_page, $total_page, $url, $add="")
{
    //$url = preg_replace('#&amp;page=[0-9]*(&amp;page=)$#', '$1', $url);
    $url = preg_replace('#&amp;page=[0-9]*#', '', $url) . '&amp;page=';

    $str = '';
    if ($cur_page > 1) {
        $str .= '<a href="'.$url.'1'.$add.'" class="qa_page qa_start">처음</a>'.PHP_EOL;
    }

    $start_page = ( ( (int)( ($cur_page - 1 ) / $write_pages ) ) * $write_pages ) + 1;
    $end_page = $start_page + $write_pages - 1;

    if ($end_page >= $total_page) $end_page = $total_page;

    if ($start_page > 1) $str .= '<a href="'.$url.($start_page-1).$add.'" class="qa_page pg_prev">이전</a>'.PHP_EOL;

    if ($total_page > 1) {
        for ($k=$start_page;$k<=$end_page;$k++) {
            if ($cur_page != $k)
                $str .= '<a href="'.$url.$k.$add.'" class="qa_page">'.$k.'</a><span class="sound_only">페이지</span>'.PHP_EOL;
            else
                $str .= '<span class="sound_only">열린</span><strong class="pg_current">'.$k.'</strong><span class="sound_only">페이지</span>'.PHP_EOL;
        }
    }

    if ($total_page > $end_page) $str .= '<a href="'.$url.($end_page+1).$add.'" class="qa_page pg_next">다음</a>'.PHP_EOL;

    if ($cur_page < $total_page) {
        $str .= '<a href="'.$url.$total_page.$add.'" class="qa_page pg_end">맨끝</a>'.PHP_EOL;
    }

    if ($str)
        return "<nav class=\"pg_wrap\"><span class=\"pg\">{$str}</span></nav>";
    else
        return "";
}
*/

$itemqa_list = gc_get_page_url('itemqalist');
$itemqa_form = add_query_arg(array('it_id'=>$it['it_id']), gc_get_page_url('itemqaform'));
$itemqa_formupdate = add_query_arg(array('it_id'=>$it['it_id']), gc_get_page_url('itemqaformupdate'));

$rows = $config['cf_page_rows'] ? $config['cf_page_rows'] : 5;
if ($qpage < 1) $qpage = 1; // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($qpage - 1) * $rows; // 시작 레코드 구함

$sql = $wpdb->prepare("select SQL_CALC_FOUND_ROWS * from `{$gc['shop_item_qa_table']}` where it_id = %.0f order by iq_id desc limit %d, %d", get_the_ID(), $from_record, $rows);

$result = $wpdb->get_results($sql, ARRAY_A);

// 테이블의 전체 레코드수만 얻음
$total_count = $wpdb->get_var('SELECT FOUND_ROWS()');

$total_page  = ceil($total_count / $rows); // 전체 페이지 계산

$it_args['itemqa_list'] = $itemqa_list;
$it_args['itemqa_form'] = $itemqa_form;
$it_args['itemqa_formupdate'] = $itemqa_formupdate;
$it_args['qpage'] = $qpage;
$it_args['result'] = $result;
$it_args['total_count'] = $total_count;
$it_args['total_page'] = $total_page;
$it_args['rows'] = $rows;

gc_skin_load('itemqa.skin.php', $it_args);
?>