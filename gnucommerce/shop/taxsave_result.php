<?php
if( !defined('GC_NAME') ) exit;

$check_param = array('tx', 'od_id', 'tr_code', 'id_info', 'buyeremail', 'buyertel', 'pg_service');
$params = array();

foreach($check_param as $v){
    $params[$v] = isset($_POST[$v]) ? sanitize_text_field($_POST[$v]) : '';
}

extract($params);

if($tx == 'personalpay') {
    $od = gc_get_order_data($od_id, 'personal');
    $dir = $od['pp_pg'];
} else {
    $od = gc_get_order_data($od_id);
    $dir = $od['od_pg'];
}

// 신청폼
if(!$dir)
    $dir = $config['de_pg_service'];

GC_VAR()->title = __("현금영수증 -", GC_NAME);

add_filter('show_admin_bar', '__return_false');
//show_admin_bar(false);

add_action('wp_enqueue_scripts', 'gc_new_style_script', 99);
add_filter('wp_head','gc_remove_admin_bar_style', 99);

gc_new_html_header();

echo "<div class=\"new_win\" >";
include_once(GC_SHOP_DIR_PATH.'/'.$dir.'/taxsave_result.php');
echo "</div>";

gc_new_html_footer();
exit;
?>