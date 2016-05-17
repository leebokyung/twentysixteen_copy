<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

if( GC_IS_MOBILE ){ //모바일이면
    return;
}

// kcp 전자결제를 사용할 때만 실행
if($config['de_iche_use'] || $config['de_vbank_use'] || $config['de_hp_use'] || $config['de_card_use']) {
    GC_VAR()->add_inline_scripts("StartSmartUpdate();");
}
?>