<?php
if( !defined('GC_NAME') ) exit;

include_once(GC_SHOP_DIR_PATH.'/settle_inicis.inc.php');

// 세션 초기화
gc_set_session('P_TID',  '');
gc_set_session('P_AMT',  '');
gc_set_session('P_HASH', '');

$oid  = isset($_REQUEST['P_NOTI']) ? sanitize_text_field($_REQUEST['P_NOTI']) : '';

//세션값이 넘어오지 않았으면
if( !$oid ){
    wp_die( __('P_NOTI 값이 넘어 오지 않았습니다.', GC_NAME) );
}

$sql = $wpdb->prepare(" select * from {$gc['shop_order_data_table']} where od_id = %.0f ", $oid);
$row = $wpdb->get_row($sql, ARRAY_A);

$data = maybe_unserialize(base64_decode($row['dt_data']));

if(isset($data['pp_id']) && $data['pp_id']) {
    /*
    $order_action_url = G5_HTTPS_MSHOP_URL.'/personalpayformupdate.php';
    $page_return_url  = G5_SHOP_URL.'/personalpayform.php?pp_id='.$data['pp_id'];
    */
    $order_action_url = gc_get_page_url('personalpay_update');
    $page_return_url  = add_query_arg( array('pp_id'=>$data['pp_id']), gc_get_page_url('personalpay'));
} else {
    /*
    $order_action_url = G5_HTTPS_MSHOP_URL.'/orderformupdate.php';
    $page_return_url  = G5_SHOP_URL.'/orderform.php';
    */

    $order_action_url = gc_get_page_url('checkout_update');
    $page_return_url  = gc_get_page_url('checkout');

    if(gc_get_session('ss_direct'))
        $page_return_url = add_query_arg( array('sw_direct'=>1), $page_return_url);
}

if($_REQUEST['P_STATUS'] != '00') {
    gc_alert('오류 : '.gc_iconv_utf8($_REQUEST['P_RMESG1']).' 코드 : '.$_REQUEST['P_STATUS'], $page_return_url);
} else {
    $post_data = array(
        'P_MID' => $config['de_inicis_mid'],
        'P_TID' => $_REQUEST['P_TID']
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $_REQUEST['P_REQ_URL']);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $return = curl_exec($ch);

    if(!$return)
        gc_alert('KG이니시스와 통신 오류로 결제등록 요청을 완료하지 못했습니다.\\n결제등록 요청을 다시 시도해 주십시오.', $page_return_url);

    // 결과를 배열로 변환
    parse_str($return, $ret);
    $PAY = array_map('trim', $ret);

    if($PAY['P_STATUS'] != '00')
        gc_alert('오류 : '.gc_iconv_utf8($PAY['P_RMESG1']).' 코드 : '.$PAY['P_STATUS'], $page_return_url);

    // TID, AMT 를 세션으로 주문완료 페이지 전달
    $hash = md5($PAY['P_TID'].$PAY['P_MID'].$PAY['P_AMT']);
    gc_set_session('P_TID',  $PAY['P_TID']);
    gc_set_session('P_AMT',  $PAY['P_AMT']);
    gc_set_session('P_HASH', $hash);
}

$defalut_array = array(
    'P_HPP_CORP' =>  '',
    'P_APPL_NUM'    =>  '',
    'P_VACT_NUM'    =>  '',
    'P_VACT_NAME'   =>  '',
    'P_VACT_BANK_CODE'  =>  '',
    'P_CARD_ISSUER_CODE'    =>  '',
    'P_UNAME'   =>  '',
    );

$PAY = wp_parse_args($PAY, $defalut_array);

$exclude = array('res_cd', 'P_HASH', 'P_TYPE', 'P_AUTH_DT', 'P_AUTH_NO', 'P_HPP_CORP', 'P_APPL_NUM', 'P_VACT_NUM', 'P_VACT_NAME', 'P_VACT_BANK', 'P_CARD_ISSUER', 'P_UNAME');

//echo '<form name="forderform" method="post" action="'.$order_action_url.'" autocomplete="off">'.PHP_EOL;
echo '<form name="forderform" method="post" action="'.get_permalink(get_the_ID()).'" autocomplete="off">'.PHP_EOL;

echo gc_make_order_field($data, $exclude);

$get_bank_code = '';
$get_card_code = '';
if( $tmp1 = $PAY['P_VACT_BANK_CODE'] ){
    $get_bank_code = isset($BANK_CODE[$tmp1]) ? $BANK_CODE[$tmp1] : '';
}
if( $tmp1 = $PAY['P_CARD_ISSUER_CODE'] ){
    $get_card_code = isset($CARD_CODE[$tmp1]) ? $CARD_CODE[$tmp1] : '';
}

wp_nonce_field( 'gc_order_approval', 'gc_nonce_approval' );
echo '<input type="hidden" name="res_cd"        value="'.esc_attr($PAY['P_STATUS']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_HASH"        value="'.esc_attr($hash).'">'.PHP_EOL;
echo '<input type="hidden" name="P_TYPE"        value="'.esc_attr($PAY['P_TYPE']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_AUTH_DT"     value="'.esc_attr($PAY['P_AUTH_DT']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_AUTH_NO"     value="'.esc_attr($PAY['P_AUTH_NO']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_HPP_CORP"    value="'.esc_attr($PAY['P_HPP_CORP']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_APPL_NUM"    value="'.esc_attr($PAY['P_APPL_NUM']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_VACT_NUM"    value="'.esc_attr($PAY['P_VACT_NUM']).'">'.PHP_EOL;
echo '<input type="hidden" name="P_VACT_NAME"   value="'.esc_attr(gc_iconv_utf8($PAY['P_VACT_NAME'])).'">'.PHP_EOL;
echo '<input type="hidden" name="P_VACT_BANK"   value="'.esc_attr($get_bank_code).'">'.PHP_EOL;
echo '<input type="hidden" name="P_CARD_ISSUER" value="'.esc_attr($get_card_code).'">'.PHP_EOL;
echo '<input type="hidden" name="P_UNAME"       value="'.gc_iconv_utf8($PAY['P_UNAME']).'">'.PHP_EOL;

echo '</form>'.PHP_EOL;
?>

<div id="show_progress">
    <span style="display:block; text-align:center;margin-top:120px"><img src="<?php echo GC_SHOP_URL; ?>/img/loading.gif" alt=""></span>
    <span style="display:block; text-align:center;margin-top:10px; font-size:14px">주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<?php
GC_VAR()->add_inline_scripts(
    "
    setTimeout( function() {
        document.forderform.submit();
    }, 300);
    "
);
?>