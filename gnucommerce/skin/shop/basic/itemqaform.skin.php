<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<!-- 상품문의 쓰기 시작 { -->
<div class="gc_itemqaform new_win">
<?php
    if( is_user_logged_in() ){ //로그인 하지 않았다면
?>
    <h1 id="win_title" class="itemqa_title">상품문의 쓰기</h1>
    <form name="fitemqa" method="post" onsubmit="return fitemqa_submit(this);" autocomplete="off" class="tbl_wrap">
    <?php wp_nonce_field( 'gc_itemqa_write', 'gc_nonce_itemqa' ); ?>
    <input type="hidden" name="gw" value="<?php echo $gw; ?>">
    <input type="hidden" name="it_id" value="<?php echo $it_id; ?>">
    <input type="hidden" name="iq_id" value="<?php echo $iq_id; ?>">
    <p class="itemqaform_secret">
        <input type="checkbox" name="iq_secret" id="iq_secret" value="1" <?php echo $chk_secret; ?>>
        <label for="iq_secret">비밀글</label>
    </p>
    <p class="itemqaform_email">
         <label for="iq_email">이메일</label>
         <input type="text" name="iq_email" value="<?php echo gc_get_text($qa['iq_email']); ?>" class="frm_input"> 이메일을 입력하시면 답변 등록 시 답변이 이메일로 전송됩니다.
    </p>
    <p class="itemqaform_hp">
         <label for="iq_hp">휴대폰</label>
         <input type="text" name="iq_hp" value="<?php echo gc_get_text($qa['iq_hp']); ?>" class="frm_input"> 휴대폰번호를 입력하시면 답변 등록 시 답변등록 알림이 SMS로 전송됩니다.
    </p>
    <p class="itemqaform_subject">
         <label for="iq_subject">제목<span class="required">*</span></label>
         <input type="text" name="iq_subject" value="<?php echo gc_get_text($qa['iq_subject']); ?>" id="iq_subject" required class="required frm_input" minlength="2" maxlength="250">
    </p>
    <p class="itemqaform_subject">
         <label for="iq_question">질문내용<span class="required">*</span></label>
         <?php echo $editor_html; ?>
    </p>
    <div class="win_btn">
        <input type="submit" value="작성완료" class="btn_submit">
        <button type="button" onclick="self.close();">닫기</button>
    </div>
    </form>
<?php } else {

gc_not_permission_page( get_permalink(get_the_ID()) );

} //end if is_user_logged_in ?>
</div>
<!-- } 상품문의 쓰기 끝 -->

<script type="text/javascript">
function fitemqa_submit(f)
{
    <?php echo $editor_js; ?>

    return true;
}
</script>