<?php
if( !defined('GC_NAME') ) exit;

if ( !is_user_logged_in() ) {
    gc_alert_close(__("상품문의는 회원만 작성이 가능합니다.", GC_NAME));
}

global $wpdb;
$gc = GC_VAR()->gc;

$check_param = array('iq_id', 'it_id', 'iq_secret', 'iq_email', 'iq_hp', 'iq_subject', 'hash', 'gw');

foreach($check_param as $v){
    $$v = isset($_REQUEST[$v]) ? sanitize_text_field($_REQUEST[$v]) : '';
}

$product_info = gc_get_it_array($it_id);
if(!$product_info || $product_info['post_type'] != GC_NAME ){
    gc_alert_close('상품고유번호 값이 없습니다.');
}

$member = gc_get_member(get_current_user_id());

$iq_question = isset($_REQUEST['iq_question']) ? implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_REQUEST['iq_question']))) : '';
$iq_answer = isset($_REQUEST['iq_answer']) ? implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_REQUEST['iq_answer']))) : '';

if ($gw == "" || $gw == "u") {
    $iq_name     = addslashes($member['user_display_name']);
    $iq_password = '';

    if (!$iq_subject) gc_alert("제목을 입력하여 주십시오.");
    if (!$iq_question) gc_alert("질문을 입력하여 주십시오.");
}

$url = add_query_arg(array('_'=>gc_get_token()), get_permalink($it_id));
$url .= '#sit_qa';

if ($gw == "")
{
    $insert_datas = apply_filters('gc_shop_qa_insert_datas', array(
            'it_id' => $it_id,
            'mb_id' => get_current_user_id(),
            'iq_secret' =>  $iq_secret,
            'iq_name'   =>  $iq_name,
            'iq_email' => $iq_email,
            'iq_hp' =>  $iq_hp,
            'iq_password'   =>  $iq_password,
            'iq_subject'    =>  $iq_subject,
            'iq_question'   =>  $iq_question,
            'iq_time'   =>  GC_TIME_YMDHIS,
            'iq_ip' =>  isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'],
        ));

    $formats = apply_filters('gc_shop_qa_insert_datas',array(
        '%s',   //it_id
        '%s',   //mb_id
        '%d',   //iq_secret
        '%s',   //iq_name
        '%s',   //iq_email
        '%s',   //iq_hp
        '%s',   //iq_password
        '%s',   //iq_subject
        '%s',   //iq_question
        '%s',   //iq_answer
        '%s',   //iq_time
        '%s',   //iq_ip
        ));

    $result = $wpdb->insert($gc['shop_item_qa_table'], $insert_datas, $formats);

    $alert_msg = '상품문의가 등록 되었습니다.';
}
else if ($gw == "u")
{
    if ( !gc_is_admin() )  //admin 권한이 아니면
    {
        $sql = $wpdb->prepare(" select count(*) as cnt from {$gc['shop_item_qa_table']} where mb_id = '%s' and iq_id = %d ", get_current_user_id(), (int) $iq_id);
        $row_cnt = $wpdb->get_var($sql);
        if (!$row_cnt)
            gc_alert("자신의 상품문의만 수정하실 수 있습니다.");
    }

    $sql = $wpdb->prepare(" update {$gc['shop_item_qa_table']}
                set iq_secret = %d,
                    iq_email = '%s',
                    iq_hp = '%s',
                    iq_subject = '%s',
                    iq_question = '%s'
              where iq_id = %d ", (int) $iq_secret, $iq_email, $iq_hp, $iq_subject, $iq_question, (int) $iq_id);
    $result = $wpdb->query($sql);

    $alert_msg = '상품문의가 수정 되었습니다.';
}
else if ($gw == "d")
{
    if ( !gc_is_admin() )  //admin 권한이 아니면
    {
        $sql = $wpdb->prepare(" select iq_answer from {$gc['shop_item_qa_table']} where mb_id = '%s' and iq_id = %d ", get_current_user_id(), (int) $iq_id);

        $row = $wpdb->get_row($sql, ARRAY_A);
        if (!$row)
            gc_alert("자신의 상품문의만 삭제하실 수 있습니다.");

        if ($row['iq_answer'])
            gc_alert("답변이 있는 상품문의는 삭제하실 수 없습니다.");
    }

    $sql = $wpdb->prepare(" delete from {$gc['shop_item_qa_table']} where iq_id = %d and md5(concat(iq_id,iq_time,iq_ip)) = '%s' ", $iq_id, $hash);
    $result = $wpdb->query($sql);

    $alert_msg = '상품문의가 삭제 되었습니다.';
}

do_action('gc_item_qa_update', $url);

if($gw == 'd')
    gc_alert($alert_msg, $url);
else
    gc_alert_opener($alert_msg, $url);

exit;
?>