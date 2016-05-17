<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

if($is_kakaopay_use) {

$js_array = array(
    'gc_conflict_start_js' => GC_DIR_URL.'js/jquery.Conflict.start.js',   // $을 jQuery 글로벌 변수로 지정
    'easyxdm_js' => $CnsPayDealRequestUrl.'/dlp/scripts/lib/easyXDM.min.js',
    'json3_js' => $CnsPayDealRequestUrl.'/dlp/scripts/lib/json3.min.js',
    'kakaopaydlpconf_js' => $CNSPAY_WEB_SERVER_URL.'/js/dlp/client/kakaopayDlpConf.js',
    'kakaopaydlpmin_js' => $CNSPAY_WEB_SERVER_URL.'/js/dlp/client/kakaopayDlp.min.js',
);

foreach($js_array as $key=>$js){
    wp_enqueue_script( $key, $js );
}

$kakaopaydlp_css = 'https://pg.cnspay.co.kr:443/dlp/css/kakaopayDlp.css';
wp_enqueue_style( 'kakaopaydlp_css', $kakaopaydlp_css, array(), GC_VERSION );

add_action( 'wp_footer', 'gc_orderform_kakao_js', 30 );
    function gc_orderform_kakao_js(){
        if (!wp_script_is( 'gc_orderform_kakao_js_load', 'done' ) ) {
?>
        <script type="text/javascript">
            /**
            cnspay	를 통해 결제를 시작합니다.
            */
            function cnspay(frm) {
                if(document.getElementById("od_settle_kakaopay").checked){
                    // TO-DO : 가맹점에서 해줘야할 부분(TXN_ID)과 KaKaoPay DLP 호출 API
                    // 결과코드가 00(정상처리되었습니다.)
                    if(frm.resultCode.value == '00') {
                        // TO-DO : 가맹점에서 해줘야할 부분(TXN_ID)과 KaKaoPay DLP 호출 API
                        kakaopayDlp.setTxnId(frm.txnId.value);
                            kakaopayDlp.setChannelType('WPM', 'TMS');
                            kakaopayDlp.addRequestParams({ MOBILE_NUM : frm.od_hp.value});
                        kakaopayDlp.callDlp('kakaopay_layer', frm, submitFunc);
                    } else {
                        alert('[RESULT_CODE] : ' + frm.resultCode.value + '\n[RESULT_MSG] : ' + frm.resultMsg.value);
                    }
                }
            }

            function makeHashData(frm) {
                var result = true;
                
                jQuery.ajax({
                    url: gnucommerce.ajaxurl,
                    type: "POST",
                    data: {
                        action : 'gc_makehashdata',
                        Amt : frm.good_mny.value,
                        ediDate : frm.EdiDate.value
                    },
                    dataType: "json",
                    async: false,
                    cache: false,
                    success: function(data) {
                        if(data.error == "") {
                            frm.EncryptData.value = data.hash_String;
                        } else {
                            try { console.log(data.error) } catch (e) { alert(data.error) };
                            result = false;
                        }
                    }
                });

                return result;
            }

            function getTxnId(frm) {
                if(makeHashData(frm)) {
                    frm.Amt.value = frm.good_mny.value;
                    frm.BuyerEmail.value = frm.od_email.value;
                    frm.BuyerName.value = frm.od_name.value;

                    jQuery.ajax({
                        url: gnucommerce.ajaxurl,
                        type: "POST",
                        data: jQuery("#kakaopay_request input").serialize()+"&action="+encodeURIComponent('gc_getTxnId'),
                        dataType: "json",
                        async: false,
                        cache: false,
                        success: function(data) {
                            frm.resultCode.value = data.resultCode;
                            frm.resultMsg.value = data.resultMsg;
                            frm.txnId.value = data.txnId;
                            frm.prDt.value = data.prDt;

                            cnspay(frm);
                        },
                        error: function(data) {
                            try { console.log(data) } catch (e) { alert(data) };
                        }
                    });
                }
            }

            var submitFunc = function cnspaySubmit(data){

                if(data.RESULT_CODE === '00') {

                    // 부인방지토큰은 기본적으로 name="NON_REP_TOKEN"인 input박스에 들어가게 되며, 아래와 같은 방법으로 꺼내서 쓸 수도 있다.
                    // 해당값은 가군인증을 위해 돌려주는 값으로서, 가맹점과 카카오페이 양측에서 저장하고 있어야 한다.
                    // var temp = data.NON_REP_TOKEN;

                    document.forderform.submit();
                } else if(data.RESLUT_CODE === 'KKP_SER_002') {
                    // X버튼 눌렀을때의 이벤트 처리 코드 등록
                    alert('[RESULT_CODE] : ' + data.RESULT_CODE + '\n[RESULT_MSG] : ' + data.RESULT_MSG);
                } else {
                    alert('[RESULT_CODE] : ' + data.RESULT_CODE + '\n[RESULT_MSG] : ' + data.RESULT_MSG);
                }
            };
        </script>
<?php
        global $wp_scripts;
        $wp_scripts->done[] = 'gc_orderform_kakao_js_load';
        }   //end if
    } //end function gc_orderform_kakao_js
}
?>