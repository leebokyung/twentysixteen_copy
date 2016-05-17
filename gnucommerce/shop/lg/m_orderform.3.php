<?php
if( ! defined( 'ABSPATH' ) ) exit; // 개별 페이지 접근 불가
?>

<input type="hidden" name="LGD_PAYKEY"        id="LGD_PAYKEY">                              <!-- LG유플러스 PAYKEY(인증후 자동셋팅)-->

<input type="hidden" name="good_mny"          value="<?php echo $tot_price ?>" >
<input type="hidden" name="res_cd"            value="">                                     <!-- 결과 코드          -->

<?php if($config['de_tax_flag_use']) { ?>
<input type="hidden" name="comm_tax_mny"      value="<?php echo $comm_tax_mny; ?>">         <!-- 과세금액    -->
<input type="hidden" name="comm_vat_mny"      value="<?php echo $comm_vat_mny; ?>">         <!-- 부가세     -->
<input type="hidden" name="comm_free_mny"     value="<?php echo $comm_free_mny; ?>">        <!-- 비과세 금액 -->
<?php } ?>

<div id="display_pay_button" class="btn_confirm">
    <span id="show_req_btn"><input type="button" name="submitChecked" onClick="gc_pay_approval();" value="<?php _e('결제하기', GC_NAME); ?>" class="btn_submit"></span>
    <span id="show_pay_btn" style="display:none;"><input type="button" onClick="forderform_check();" value="<?php _e('주문하기', GC_NAME); ?>" class="btn_submit"></span>
    <a href="<?php echo gc_get_page_url('shop'); ?>" class="btn_cancel btn01"><?php _e('취소', GC_NAME); ?></a>
</div>