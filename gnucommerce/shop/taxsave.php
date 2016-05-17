<?php
if( !defined('GC_NAME') ) exit;

$check_param = array('tx', 'od_id');

foreach($check_param as $v){
    $$v = isset($_REQUEST[$v]) ? sanitize_text_field($_REQUEST[$v]) : '';
}

add_filter('show_admin_bar', '__return_false');
//show_admin_bar(false);

add_action('wp_enqueue_scripts', 'gc_new_style_script', 99);
add_filter('wp_head','gc_remove_admin_bar_style', 99);

gc_new_html_header();

if($tx == 'personalpay') {
    $od = gc_get_order_data($od_id, 'personal');

    if (!$od)
        wp_die('<p class="scash_empty">개인결제 내역이 존재하지 않습니다.</p>');

    $goods_name = $od['pp_name'].'님 개인결제';
    $amt_tot = (int)$od['pp_receipt_price'];
    $dir = $od['pp_pg'];
    $od_name = $od['pp_name'];
    $od_email = gc_get_text($od['pp_email']);
    $od_tel = gc_get_text($od['pp_hp']);

    $amt_tot = (int)$od['pp_receipt_price'];
    $amt_sup = (int)round(($amt_tot * 10) / 11);
    $amt_svc = 0;
    $amt_tax = (int)($amt_tot - $amt_sup);
} else {
    $od = gc_get_order_data($od_id);
    if (!$od)
        wp_die('<p id="scash_empty">주문서가 존재하지 않습니다.</p>');

    $goods = gc_get_goods($od['od_id']);
    $goods_name = $goods['full_name'];
    $amt_tot = (int)($od['od_receipt_price'] - $od['od_refund_price']);
    $dir = $od['od_pg'];
    $od_name = $od['od_name'];
    $od_email = gc_get_text($od['od_email']);
    $od_tel = gc_get_text($od['od_tel']);

    $amt_tot = (int)$od['od_tax_mny'] + (int)$od['od_vat_mny'] + (int)$od['od_free_mny'];
    $amt_sup = (int)$od['od_tax_mny'] + (int)$od['od_free_mny'];
    $amt_tax = (int)$od['od_vat_mny'];
    $amt_svc = 0;
}

$trad_time = date("YmdHis");

// 신청폼
if(!$dir)
    $dir = $config['de_pg_service'];

GC_VAR()->title = sprintf(__("주문번호 %s 현금영수증 발행", GC_NAME), $od_id);

echo "<div class=\"new_win\" >";
include_once(GC_SHOP_DIR_PATH.'/'.$dir.'/taxsave_form.php');
echo "</div>";

gc_new_html_footer();
exit;
?>