<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

if(GC_IS_MOBILE){  //모바일이면
    include(__DIR__ ."/m_orderform.3.php");
    return;
}

// 무통장 또는 전자결제를 사용할 때만 실행
if($config['de_bank_use'] || $config['de_iche_use'] || $config['de_vbank_use'] || $config['de_hp_use'] || $config['de_card_use'] || $config['de_easy_pay_use']) {
?>

<div id="display_pay_button" class="btn_confirm" style="display:none">
    <input type="submit" value="주문하기" class="btn_submit">
    <a href="javascript:history.go(-1);" class="btn01">취소</a>
</div>
<div id="display_pay_process" style="display:none">
    <img src="<?php echo GC_DIR_URL; ?>shop/img/loading.gif" alt="">
    <span>주문완료 중입니다. 잠시만 기다려 주십시오.</span>
</div>

<script>
document.getElementById("display_pay_button").style.display = "";
</script>
<?php } ?>