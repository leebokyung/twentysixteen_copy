<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

global $wpdb;

$subject = get_bloginfo().' 주문 오류 알림 메일';

if($error == 'order') {
    $content = '<p>'.__('주문정보를 DB에 입력하는 중 오류가 발생했습니다.', GC_NAME).'</p>';
} else if($error == 'status') {
    $content = '<p>'.__('주문 상품의 상태를 변경하는 중 DB 오류가 발생했습니다.', GC_NAME).'</p>';
}

if($tno) {
    $content .= '<p>PG사의 '.$od_settle_case.'는 자동 취소되었습니다.</p>';
    $content .= '<p>취소 내역은 PG사 상점관리자에서 확인할 수 있습니다.</p>';
}

$content .= '<p>오류내용</p>';
$content .= '<p>'.$sql.'</p><p>'.$wpdb->print_error().'<p>error file : '.$_SERVER['SCRIPT_NAME'].'</p>';

$subject = apply_filters('gc_order_error_mail_subject', $subject, $od_settle_case);
$content = apply_filters('gc_order_error_mail_content', $content, $od_settle_case);
$headers = 'From: '.$config['cf_admin_email_name'].' <'.$config['cf_admin_email'].'>' . "\r\n";

add_filter( 'wp_mail_content_type', 'gc_set_html_content_type' );
wp_mail($od_email, $subject, $content, $headers);
remove_filter( 'wp_mail_content_type', 'gc_set_html_content_type' );

unset($error);
?>