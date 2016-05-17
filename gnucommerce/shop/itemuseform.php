<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;
$gc = GC_VAR()->gc;
$config = GC_VAR()->config;

if (! is_user_logged_in() ) {
    gc_alert_close(__("사용후기는 회원만 작성 가능합니다.", GC_NAME));
}

$member = gc_get_member(get_current_user_id());

$check_param = array('gw', 'it_id', 'is_id');

foreach( $check_param as $v ){
    $$v = isset($_REQUEST[$v]) ? sanitize_text_field($_REQUEST[$v]) : '';
}

if($is_id){
    $use = get_comment( $is_id, ARRAY_A );
    
    $it_id = $use['comment_post_ID'];
}

if( !comments_open($it_id) ){
    gc_alert_close(__("사용후기를 작성할수 없습니다.", GC_NAME));
}

// 상품정보체크
$product_info = gc_get_it_array($it_id);

if(!$product_info)
    gc_alert_close(__("상품정보가 존재하지 않습니다.", GC_NAME));

if($gw == '') {
    $is_score = 5;

    // 사용후기 작성 설정에 따른 체크
    gc_check_itemuse_write($it_id, get_current_user_id());

    $commenter = wp_get_current_commenter();

    $use = array(
            'comment_author' => $commenter['comment_author'],
            'comment_author_email' =>  $commenter['comment_author_email'],
            'comment_subject'   => '',
            'comment_content'   =>  '',
            'is_score' => '',
        );
} else if ($gw == "u") {

    $use = get_comment( $is_id, ARRAY_A );
    if (!$use) {
        gc_alert_close("사용후기 정보가 없습니다.");
    }

    $use['comment_subject'] = get_comment_meta( $is_id, 'comment_subject', true );
    $use['is_score'] = get_comment_meta( $is_id, 'gc_is_score', true );
    
    if (!gc_is_admin() && $use['user_id'] != get_current_user_id()) {
        gc_alert_close("자신의 사용후기만 수정이 가능합니다.");
    }
}

$is_dhtml_editor = false;

/*
$editor_html = gc_editor_html('iq_question', gc_get_text($qa['iq_question'], 0), $is_dhtml_editor, 'cols="45" rows="8"');
$editor_js = '';
$editor_js .= gc_get_editor_js('iq_question', $is_dhtml_editor);
$editor_js .= gc_chk_editor_js('iq_question', $is_dhtml_editor);
*/

if( !$product_info['it_skin'] ){
    $product_info['it_skin'] = $config['de_shop_skin'];
}

$itemuseform_skin = GC_SKIN_PATH.'/shop/'.$product_info['it_skin'].'/itemuseform.skin.php';

add_filter('show_admin_bar', '__return_false');
//show_admin_bar(false);

add_action('wp_enqueue_scripts', 'gc_new_style_script', 99);
add_filter('wp_head','gc_remove_admin_bar_style', 99);

gc_new_html_header();

if(!file_exists($itemuseform_skin)) {
    echo str_replace( substr(ABSPATH, 0, -1), '', $itemuseform_skin).' 스킨 파일이 존재하지 않습니다.';
} else {
    include_once($itemuseform_skin);
}

gc_new_html_footer();
exit;
?>