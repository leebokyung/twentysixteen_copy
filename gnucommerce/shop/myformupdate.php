<?php
if( !defined('GC_NAME') ) exit;

if ( !is_user_logged_in() ) {
    gc_alert(__("로그인 하셔야 합니다.", GC_NAME));
}

do_action('gc_user_profile_update', get_current_user_id());

$url = add_query_arg(array('view'=>'myform'), get_permalink());

$alert_msg = __("수정되었습니다.", GC_NAME);

gc_alert($alert_msg, $url);
?>