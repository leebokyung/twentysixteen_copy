<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

include_once(GC_SHOP_DIR_PATH.'/settle_inicis.inc.php');

// 세션비교
$hash = md5(gc_get_session('P_TID').$config['de_inicis_mid'].gc_get_session('P_AMT'));
if($hash != $_POST['P_HASH'])
    gc_alert('결제 정보가 일치하지 않습니다. 올바른 방법으로 이용해 주십시오.');

//초기화
$bankname = '';
$account = '';
//최종결제요청 결과 성공 DB처리
$tno             = gc_get_session('P_TID');
$amount          = gc_get_session('P_AMT');
$app_time        = sanitize_text_field($_POST['P_AUTH_DT']);
$pay_method      = sanitize_text_field($_POST['P_TYPE']);
$pay_type        = $PAY_METHOD[$pay_method];
$depositor       = sanitize_text_field($_POST['P_UNAME']);
$commid          = sanitize_text_field($_POST['P_HPP_CORP']);
$mobile_no       = sanitize_text_field($_POST['P_APPL_NUM']);
$app_no          = sanitize_text_field($_POST['P_AUTH_NO']);
$card_name       = sanitize_text_field($_POST['P_CARD_ISSUER']);
if ($config['de_escrow_use'] == 1)
    $escw_yn         = 'Y';
switch($pay_type) {
    case '계좌이체':
        $bank_name = sanitize_text_field($_POST['P_VACT_BANK']);
        break;
    case '가상계좌':
        $bankname  = sanitize_text_field($_POST['P_VACT_BANK']);
        $account   = sanitize_text_field($_POST['P_VACT_NUM'].' '.$_POST['P_VACT_NAME']);
        $app_no    = sanitize_text_field($_POST['P_VACT_NUM']);
        break;
    default:
        break;
}

// 세션 초기화
gc_set_session('P_TID',  '');
gc_set_session('P_AMT',  '');
gc_set_session('P_HASH', '');
?>