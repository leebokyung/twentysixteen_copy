<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;
$gc = GC_VAR()->gc;

$oid = isset($_REQUEST['oid']) ? sanitize_text_field($_REQUEST['oid']) : '';

include_once(GC_SHOP_DIR_PATH.'/settle_inicis.inc.php');

// 세션 초기화
gc_set_session('P_TID',  '');
gc_set_session('P_AMT',  '');
gc_set_session('P_HASH', '');

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

    if($_SESSION['ss_direct'])
        $page_return_url = add_query_arg( array('sw_direct'=>1), $page_return_url);
}

$sql = $wpdb->prepare(" select * from {$gc['inicis_log_table']} where oid = %.0f ", $oid);
$row = $wpdb->get_row($sql, ARRAY_A);

if(!$row['oid'])
    gc_alert('결제 정보가 존재하지 않습니다.\\n\\n올바른 방법으로 이용해 주십시오.', $page_return_url);

if($row['P_STATUS'] != '00')
    gc_alert('오류 : '.$row['P_RMESG1'].' 코드 : '.$row['P_STATUS'], $page_return_url);

$PAY = array_map('trim', $row);

// TID, AMT 를 세션으로 주문완료 페이지 전달
$hash = md5($PAY['P_TID'].$PAY['P_MID'].$PAY['P_AMT']);
gc_set_session('P_TID',  $PAY['P_TID']);
gc_set_session('P_AMT',  $PAY['P_AMT']);
gc_set_session('P_HASH', $hash);

$sql = $wpdb->prepare(" delete from {$gc['inicis_log_table']} where oid = %.0f", $oid);

$wpdb->query($sql);

$exclude = array('res_cd', 'P_HASH', 'P_TYPE', 'P_AUTH_DT', 'P_VACT_BANK', 'gc_nonce_field', '_wp_http_referer');

//echo '<form name="forderform" method="post" action="'.$order_action_url.'" autocomplete="off">'.PHP_EOL;
echo '<form name="forderform" method="post" action="'.get_permalink(get_the_ID()).'" autocomplete="off">'.PHP_EOL;

echo gc_make_order_field($data, $exclude);

wp_nonce_field( 'gc_order_approval', 'gc_nonce_approval' );
echo '<input type="hidden" name="res_cd"      value="'.$PAY['P_STATUS'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_HASH"      value="'.$hash.'">'.PHP_EOL;
echo '<input type="hidden" name="P_TYPE"      value="'.$PAY['P_TYPE'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_AUTH_DT"   value="'.$PAY['P_AUTH_DT'].'">'.PHP_EOL;
echo '<input type="hidden" name="P_VACT_BANK" value="'.$PAY['P_FN_NM'].'">'.PHP_EOL;

echo '</form>'.PHP_EOL;
?>

<div id="pay_working">
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