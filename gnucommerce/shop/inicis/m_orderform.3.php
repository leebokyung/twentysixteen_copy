<?php
if (!defined("GC_NAME")) exit; // 개별 페이지 접근 불가
?>

<div id="display_pay_button" class="btn_confirm">
    <span id="show_req_btn"><input type="button" name="submitChecked" onClick="gc_pay_approval();" value="<?php _e('결제하기',  GC_NAME); ?>" class="btn_submit"></span>
    <span id="show_pay_btn" style="display:none;"><input type="button" onClick="forderform_check();" value="<?php _e('주문하기', GC_NAME); ?>" class="btn_submit"></span>
    <a href="<?php echo home_url(); ?>" class="btn_cancel btn01"><?php _e('취소', GC_NAME); ?></a>
</div>