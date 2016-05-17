<?php
if (!defined("GC_NAME")) exit; // 개별 페이지 접근 불가
?>

<input type="hidden" name="req_tx"         value="">      <!-- 요청 구분          -->
<input type="hidden" name="res_cd"         value="">      <!-- 결과 코드          -->
<input type="hidden" name="tran_cd"        value="">      <!-- 트랜잭션 코드      -->
<input type="hidden" name="ordr_idxx"      value="">      <!-- 주문번호           -->
<input type="hidden" name="good_mny"       value="">      <!-- 결제금액    -->
<input type="hidden" name="good_name"      value="">      <!-- 상품명             -->
<input type="hidden" name="buyr_name"      value="">      <!-- 주문자명           -->
<input type="hidden" name="buyr_tel1"      value="">      <!-- 주문자 전화번호    -->
<input type="hidden" name="buyr_tel2"      value="">      <!-- 주문자 휴대폰번호  -->
<input type="hidden" name="buyr_mail"      value="">      <!-- 주문자 E-mail      -->
<input type="hidden" name="enc_info"       value="">      <!-- 암호화 정보        -->
<input type="hidden" name="enc_data"       value="">      <!-- 암호화 데이터      -->
<input type="hidden" name="use_pay_method" value="">      <!-- 요청된 결제 수단   -->
<input type="hidden" name="rcvr_name"      value="">      <!-- 수취인 이름        -->
<input type="hidden" name="rcvr_tel1"      value="">      <!-- 수취인 전화번호    -->
<input type="hidden" name="rcvr_tel2"      value="">      <!-- 수취인 휴대폰번호  -->
<input type="hidden" name="rcvr_mail"      value="">      <!-- 수취인 E-Mail      -->
<input type="hidden" name="rcvr_zipx"      value="">      <!-- 수취인 우편번호    -->
<input type="hidden" name="rcvr_add1"      value="">      <!-- 수취인 주소        -->
<input type="hidden" name="rcvr_add2"      value="">      <!-- 수취인 상세 주소   -->
<input type="hidden" name="param_opt_1"    value="">
<input type="hidden" name="param_opt_2"    value="">
<input type="hidden" name="param_opt_3"    value="">
<input type="hidden" name="disp_tax_yn"    value="N">
<?php if($config['de_tax_flag_use']) { ?>
<input type="hidden" name="tax_flag"          value="TG03">     <!-- 변경불가    -->
<input type="hidden" name="comm_tax_mny"      value="<?php echo $comm_tax_mny; ?>">         <!-- 과세금액    -->
<input type="hidden" name="comm_vat_mny"      value="<?php echo $comm_vat_mny; ?>">         <!-- 부가세     -->
<input type="hidden" name="comm_free_mny"     value="<?php echo $comm_free_mny; ?>">        <!-- 비과세 금액 -->
<?php } ?>

<div id="display_pay_button" class="btn_confirm">
    <span id="show_req_btn"><input type="button" name="submitChecked" onClick="gc_pay_approval();" value="<?php _e('결제하기', GC_NAME); ?>" class="btn_submit"></span>
    <span id="show_pay_btn" style="display:none;"><input type="button" onClick="forderform_check();" value="<?php _e('주문하기', GC_NAME); ?>" class="btn_submit"></span>
    <a href="<?php echo gc_get_page_url('shop'); ?>" class="btn_cancel btn01"><?php _e('취소', GC_NAME); ?></a>
</div>

<?php
// 무통장 입금만 사용할 때는 주문하기 버튼 보이게
if(!($config['de_iche_use'] || $config['de_vbank_use'] || $config['de_hp_use'] || $config['de_card_use'])) {
?>
<script>
document.getElementById("show_req_btn").style.display = "none";
document.getElementById("show_pay_btn").style.display = "";
</script>
<?php } ?>