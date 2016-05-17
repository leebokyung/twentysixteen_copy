<?php
if( ! defined( 'ABSPATH' ) ) exit;

global $wpdb;
$gc = GC_VAR()->gc;
$config = GC_VAR()->config;

/*
xpay_approval.php 에서 세션에 저장했던 파라미터 값이 유효한지 체크
세션 유지 시간(로그인 유지시간)을 적당히 유지 하거나 세션을 사용하지 않는 경우 DB처리 하시기 바랍니다.
*/

if(!isset($_SESSION['PAYREQ_MAP'])){
    gc_alert('세션이 만료 되었거나 유효하지 않은 요청 입니다.', gc_get_page_url('shop'));
}

$payReqMap = $_SESSION['PAYREQ_MAP']; //결제 요청시, Session에 저장했던 파라미터 MAP

GC_VAR()->title = __('LG유플러스 eCredit 서비스 결제', GC_NAME);

$LGD_RESPCODE = isset($_REQUEST['LGD_RESPCODE']) ? sanitize_text_field($_REQUEST['LGD_RESPCODE']) : '';
$LGD_RESPMSG  = isset($_REQUEST['LGD_RESPMSG']) ? sanitize_text_field(@iconv("EUC-KR","UTF-8//IGNORE", $_REQUEST['LGD_RESPMSG'])) : '';

$LGD_PAYKEY   = '';

$LGD_OID          = $payReqMap['LGD_OID'];

$sql = $wpdb->prepare(" select * from {$gc['shop_order_data_table']} where od_id = %.0f ", $LGD_OID);
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

if($LGD_RESPCODE == '0000') {
    $LGD_PAYKEY                = isset($_REQUEST['LGD_PAYKEY']) ? sanitize_text_field($_REQUEST['LGD_PAYKEY']) : '';
    $payReqMap['LGD_RESPCODE'] = $LGD_RESPCODE;
    $payReqMap['LGD_RESPMSG']  = $LGD_RESPMSG;
    $payReqMap['LGD_PAYKEY']   = $LGD_PAYKEY;
} else {
    gc_alert('LGD_RESPCODE:' . $LGD_RESPCODE . ' ,LGD_RESPMSG:' . iconv("EUC-KR","UTF-8", $LGD_RESPMSG), $page_return_url); //인증 실패에 대한 처리 로직 추가
}
?>

<?php
$exclude = array('res_cd', 'LGD_PAYKEY');

echo '<form name="forderform" method="post" action="'.get_permalink(get_the_ID()).'" autocomplete="off">'.PHP_EOL;

echo gc_make_order_field($data, $exclude);

echo '<input type="hidden" name="res_cd" value="'.$LGD_RESPCODE.'">'.PHP_EOL;
echo '<input type="hidden" name="LGD_PAYKEY" value="'.$LGD_PAYKEY.'">'.PHP_EOL;

echo '</form>'.PHP_EOL;
?>

<div>
    <div id="show_progress">
        <span style="display:block; text-align:center;margin-top:120px"><img src="<?php echo GC_SHOP_URL; ?>/img/loading.gif" alt=""></span>
        <span style="display:block; text-align:center;margin-top:10px; font-size:14px">주문완료 중입니다. 잠시만 기다려 주십시오.</span>
    </div>
</div>

<script type="text/javascript">
    setTimeout( function() {
        document.forderform.submit();
    }, 300);
</script>
<?php
exit;
?>