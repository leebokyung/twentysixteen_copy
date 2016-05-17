<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

$test = "";

if ($config['de_card_test']) {
    if ($config['de_escrow_use'] == 1) {
        // 에스크로결제 테스트
        $config['de_kcp_mid'] = "T0007";
        $config['de_kcp_site_key'] = '4Ho4YsuOZlLXUZUdOxM1Q7X__';
    }
    else {
        // 일반결제 테스트
        $config['de_kcp_mid'] = "T0000";
        $config['de_kcp_site_key'] = '3grptw1.zW0GSo4PQdaGvsF__';
    }

    $test = "_test";
    $g_wsdl = GC_SHOP_URL."/kcp/"."KCPPaymentService.wsdl";     //모바일에서 쓰임
    $g_conf_gw_url = "testpaygw.kcp.co.kr"; //모바일에서 쓰임
}
else {
    $config['de_kcp_mid'] = "SR".$config['de_kcp_mid'];
    $g_wsdl = GC_SHOP_URL."/kcp/"."real_KCPPaymentService.wsdl";    //모바일에서 쓰임
    $g_conf_gw_url = "paygw.kcp.co.kr";         //모바일에서 쓰임
}

$g_conf_home_dir  = GC_SHOP_DIR_PATH.'/kcp';
$g_conf_site_name = '';
if( GC_IS_MOBILE ){
    $g_conf_site_name = $config['de_admin_company_name'];
}
$g_conf_key_dir   = '';

/*=======================================================================
 KCP 결제처리 로그파일 생성을 위한 로그 디렉토리 절대 경로를 지정합니다.
 로그 파일의 경로는 웹에서 접근할 수 없는 경로를 지정해 주십시오.
 영카트5의 config.php 파일이 존재하는 경로가 /home/gnucommerce/www 라면
 로그 디렉토리는 /home/gnucommerce/log 등으로 지정하셔야 합니다.
 로그 디렉토리에 쓰기 권한이 있어야 로그 파일이 생성됩니다.
=======================================================================*/

$g_conf_log_dir = apply_filters('set_kcp_log_path', '/home100/kcp'); // 존재하지 않는 경로를 입력하여 로그 파일 생성되지 않도록 함.

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
{
    $g_conf_key_dir   = GC_SHOP_DIR_PATH.'/kcp/bin/pub.key';
}

$g_conf_site_cd  = $config['de_kcp_mid'];
$g_conf_site_key = $config['de_kcp_site_key'];

// 테스트 결제 때 PAYCO site_cd, site_key 재설정
if($config['de_card_test'] && isset($_POST['od_settle_case']) && $_POST['od_settle_case'] == gc_get_stype_names('easypayment')) {  //간편결제
    $g_conf_site_cd = 'S6729';
    $g_conf_site_key = '';
}

if (preg_match("/^T000/", $g_conf_site_cd) || $config['de_card_test']) {
    $g_conf_gw_url  = "testpaygw.kcp.co.kr";                    // real url : paygw.kcp.co.kr , test url : testpaygw.kcp.co.kr
}
else {
    $g_conf_gw_url  = "paygw.kcp.co.kr";
    if (!preg_match("/^SR/", $g_conf_site_cd)) {
        gc_alert("SR 로 시작하지 않는 KCP SITE CODE 는 지원하지 않습니다.");
    }
}

// KCP SITE KEY 입력 체크
if($config['de_iche_use'] || $config['de_vbank_use'] || $config['de_hp_use'] || $config['de_card_use']) {
    if(trim($config['de_kcp_site_key']) == '')
        gc_alert('KCP SITE KEY를 입력해 주십시오.');
}

$g_conf_js_url = "https://pay.kcp.co.kr/plugin/payplus{$test}_un.js";

$g_conf_log_level = "3";           // 변경불가
$g_conf_gw_port   = "8090";        // 포트번호(변경불가)
?>