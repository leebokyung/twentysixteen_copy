<?php
if (!defined('ABSPATH')) exit; // 개별 페이지 접근 불가
$delete_str = '';
if ($w == 'x') $delete_str = __('댓글', GC_NAME);
if ($w == 'u') $gc['title'] = $delete_str." ".__('수정', GC_NAME);
else if ($w == 'd' || $w == 'x') $gc['title'] = $delete_str." ".__('삭제', GC_NAME);
else $gc['title'] = $gc['title'];
?>

<!-- 비밀번호 확인 시작 { -->
<div id="pw_confirm" class="mbskin">
    <h1><?php echo $gc['title'] ?></h1>
    <p>
        <?php if ($w == 'u') { ?>
        <strong><?php _e('작성자만 글을 수정할 수 있습니다.', GC_NAME); ?></strong>
		<?php _e('작성자 본인이라면, 글 작성시 입력한 비밀번호를 입력하여 글을 수정할 수 있습니다.', GC_NAME); ?>
        <?php } else if ($w == 'd' || $w == 'x') {  ?>
        <strong><?php _e('작성자만 글을 삭제할 수 있습니다.', GC_NAME); ?></strong>
		<?php _e('작성자 본인이라면, 글 작성시 입력한 비밀번호를 입력하여 글을 삭제할 수 있습니다.', GC_NAME); ?>
        <?php } else {  ?>
        <strong><?php _e('비밀글 기능으로 보호된 글입니다.', GC_NAME); ?></strong>
		<?php _e('작성자와 관리자만 열람하실 수 있습니다. 본인이라면 비밀번호를 입력하세요.', GC_NAME); ?>
        <?php }  ?>
    </p>

    <form name="fboardpassword" action="<?php echo $password_action_url;?>" method="post">
    <?php wp_nonce_field($nonce_name, $nonce_key); ?>
    <input type="hidden" name="action" value="<?php echo esc_attr( $action );?>">
    <input type="hidden" name="w" value="<?php echo esc_attr( $w ); ?>">
    <input type="hidden" name="bo_table" value="<?php echo esc_attr( $bo_table ); ?>">
    <input type="hidden" name="wr_id" value="<?php echo esc_attr( intval($wr_id) ); ?>">
    <input type="hidden" name="cm_id" value="<?php echo esc_attr( intval($cm_id) ); ?>">
    <input type="hidden" name="sfl" value="<?php echo esc_attr( $sfl ); ?>">
    <input type="hidden" name="stx" value="<?php echo esc_attr( $stx ); ?>">
    <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>">
    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">

    <fieldset>
        <label for="pw_wr_password"><?php _e('비밀번호', GC_NAME); ?><strong class="sound_only"><?php _e('필수', GC_NAME); ?></strong></label>
        <input type="password" name="user_pass" id="password_user_pass" required class="frm_input required" size="15" maxLength="20">
        <input type="submit" value="확인" class="btn_submit">
    </fieldset>
    </form>

    <div class="btn_confirm">
        <a href="<?php echo esc_url( $return_url ); ?>"><?php _e('돌아가기', GC_NAME); ?></a>
    </div>

</div>
<!-- } 비밀번호 확인 끝 -->