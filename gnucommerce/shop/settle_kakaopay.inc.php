<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

$is_kakaopay_use = false;
if($config['de_kakaopay_mid'] && $config['de_kakaopay_key'] && $config['de_kakaopay_enckey'] && $config['de_kakaopay_hashkey'] && $config['de_kakaopay_cancelpwd']) {
    $is_kakaopay_use = true;
    require_once(GC_SHOP_DIR_PATH.'/kakaopay/incKakaopayCommon.php');
}
?>