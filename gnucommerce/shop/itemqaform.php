<?php
if( !defined('GC_NAME') ) exit;

if( !class_exists('GC_Cache_Option') ){
    include_once( GC_LIB_PATH.'/gc_cache.class.php' );
}

GC_Cache_Option::nocache();

global $wpdb;
$gc = GC_VAR()->gc;
$config = GC_VAR()->config;

if (! is_user_logged_in() ) {
    gc_alert_close(__("상품문의는 회원만 작성 가능합니다.", GC_NAME));
}

$member = gc_get_member(get_current_user_id());

$check_param = array('gw', 'it_id', 'iq_id');

foreach( $check_param as $v ){
    $$v = isset($_REQUEST[$v]) ? sanitize_text_field($_REQUEST[$v]) : '';
}

// 상품정보체크
$product_info = gc_get_it_array($it_id);

if(!$product_info)
    gc_alert_close(__("상품정보가 존재하지 않습니다.", GC_NAME));

$chk_secret = '';

if($gw == '') {
    $qa = array(
            'iq_subject' => '',
            'iq_question'   =>  '',
            'iq_email' => $member['user_email'],
            'iq_hp' =>  $member['mb_hp'],
        );
}

if ($gw == "u")
{
    $sql = $wpdb->prepare(" select * from {$gc['shop_item_qa_table']} where iq_id = %d ", $iq_id);
    $qa = $wpdb->get_row($sql, ARRAY_A);

    if (!$qa) {
        gc_alert_close(__("상품문의 정보가 없습니다.", GC_NAME));
    }

    $it_id    = $qa['it_id'];

    if ($qa['mb_id'] != get_current_user_id()) {
        gc_alert_close(__("자신의 상품문의만 수정이 가능합니다.", GC_NAME));
    }

    if($qa['iq_secret'])
        $chk_secret = 'checked="checked"';
}

$is_dhtml_editor = false;

$editor_html = gc_editor_html('iq_question', gc_get_text($qa['iq_question'], 0), $is_dhtml_editor, 'cols="45" rows="8"');
$editor_js = '';
$editor_js .= gc_get_editor_js('iq_question', $is_dhtml_editor);
$editor_js .= gc_chk_editor_js('iq_question', $is_dhtml_editor);

if( !$product_info['it_skin'] ){
    $product_info['it_skin'] = $config['de_shop_skin'];
}

$itemqaform_skin = GC_SKIN_PATH.'/shop/'.$product_info['it_skin'].'/itemqaform.skin.php';

add_filter('show_admin_bar', '__return_false');
//show_admin_bar(false);

add_action('wp_enqueue_scripts', 'gc_new_style_script', 99);
add_filter('wp_head','gc_remove_admin_bar_style', 99);

gc_new_html_header();

if(!file_exists($itemqaform_skin)) {
    echo str_replace( substr(ABSPATH, 0, -1), '', $itemqaform_skin).' '.__('스킨 파일이 존재하지 않습니다.', GC_NAME);
} else {
    include_once($itemqaform_skin);
}

gc_new_html_footer();
exit;
?>