<?php
if( !defined('GC_NAME') ) exit;

if ( ! is_user_logged_in() ) {
    return;
}

global $wpdb;
$gc = GC_VAR()->gc;

$check_param = array('gw', 'it_id', 'is_id', '_wpnonce');

foreach($check_param as $v){
    $$v = isset($_REQUEST[$v]) ? sanitize_text_field($_REQUEST[$v]) : '';
}

if ($gw == "d" && wp_verify_nonce( $_wpnonce, 'gc-itemuse' ) )
{
    $comment = get_comment( $is_id, ARRAY_A );

    if ( !gc_is_admin() )
    {
        if( $comment['user_id'] != get_current_user_id() ){
            gc_alert(__("자신의 사용후기만 삭제하실 수 있습니다.", GC_NAME));
        }
    }

    wp_delete_comment( $is_id );
    gc_update_use_avg($comment['comment_post_ID']);
    
    $url = get_permalink($comment['comment_post_ID']);

    $alert_msg = __("사용후기를 삭제 하였습니다.", GC_NAME);

    gc_alert($alert_msg, $url);

    exit;
}
?>