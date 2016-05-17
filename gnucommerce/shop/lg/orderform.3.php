<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

if( GC_IS_MOBILE ){  //모바일이면
    include( __DIR__ ."/m_orderform.3.php");
    return;
}
?>

<div id="display_pay_button" class="btn_confirm" style="display:none">
    <input type="button" value="주문하기" class="btn_submit" onclick="gc_forderform_check(this.form);"/>
    <a href="javascript:history.go(-1);" class="btn01">취소</a>
</div>
<div id="display_pay_process" style="display:none">
    <img src="<?php echo GC_SHOP_URL; ?>/img/loading.gif" alt="">
    <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<script>
document.getElementById("display_pay_button").style.display = "" ;
</script>