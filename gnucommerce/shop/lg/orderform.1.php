<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

if( GC_IS_MOBILE ){  //모바일이면
    include( __DIR__ ."/m_orderform.1.php");
    return;
}

// 전자결제를 사용할 때만 실행
if($config['de_iche_use'] || $config['de_vbank_use'] || $config['de_hp_use'] || $config['de_card_use'] || $config['de_easy_pay_use']) {

$xpay_crossplatform_js = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') ? 'https' : 'http';
$xpay_crossplatform_js .= '://xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js';

wp_enqueue_script( 'xpay_crossplatform_js', $xpay_crossplatform_js );

add_action( 'wp_footer', 'gc_orderform_lg_js', 30 );
    function gc_orderform_lg_js(){
        global $LGD_WINDOW_TYPE, $CST_PLATFORM;
        
        $order_action_url = add_query_arg( array('noc'=>false, 'sw_direct'=>false), get_permalink());

        if (!wp_script_is( 'gc_orderform_lg_js_load', 'done' ) ) {
?>
        <script type="text/javascript">

        /*
        * 수정불가.
        */
        var LGD_window_type = "<?php echo $LGD_WINDOW_TYPE; ?>";

        /*
        * 수정불가
        */
        function launchCrossPlatform(frm) {
            jQuery.ajax({
                url: gnucommerce.ajaxurl,
                type: "POST",
                data: jQuery("#LGD_PAYREQUEST input").serialize()+"&action="+encodeURIComponent('xpay_request'),
                dataType: "json",
                async: false,
                cache: false,
                success: function(data) {
                    frm.LGD_HASHDATA.value = data.LGD_HASHDATA;

                    lgdwin = openXpay(frm, '<?php echo $CST_PLATFORM; ?>', LGD_window_type, null, "", "");
                },
                error: function(data) {
                    try { console.log(data) } catch (e) { alert(data.error) };
                }
            });
        }
        /*
        * FORM 명만  수정 가능
        */
        function getFormObject() {
            return document.getElementById("forderform");
        }

        /*
         * 인증결과 처리
         */
        function payment_return() {
            var fDoc;

            fDoc = lgdwin.contentWindow || lgdwin.contentDocument;

            if (fDoc.document.getElementById('LGD_RESPCODE').value == "0000") {
                document.getElementById("LGD_PAYKEY").value = fDoc.document.getElementById('LGD_PAYKEY').value;
                document.getElementById("forderform").target = "_self";
                document.getElementById("forderform").action = "<?php echo $order_action_url; ?>";
                document.getElementById("forderform").submit();
            } else {
                document.getElementById("forderform").target = "_self";
                document.getElementById("forderform").action = "<?php echo $order_action_url; ?>";
                alert("LGD_RESPCODE (결과코드) : " + fDoc.document.getElementById('LGD_RESPCODE').value + "\n" + "LGD_RESPMSG (결과메시지): " + fDoc.document.getElementById('LGD_RESPMSG').value);
                closeIframe();
            }
        }
        </script>
<?php
        global $wp_scripts;
        $wp_scripts->done[] = 'gc_orderform_lg_js_load';
        }   //end if
    } //end function gc_orderform_lg_js
}
?>