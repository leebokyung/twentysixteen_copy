<?php
if( !defined('GC_NAME') ) exit;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<div id="mb_login_od">
    <h2><?php _e('비회원 주문조회', GC_NAME); ?></h2>

    <form name="forderinquiry" method="post" action="<?php echo urldecode($url); ?>" autocomplete="off">

    <p>
        <label for="od_id"><?php _e('주문서번호', GC_NAME); ?><strong class="sound_only"> 필수</strong></label>
        <input type="text" name="od_id" value="<?php echo $od_id; ?>" id="od_id" required class="frm_input required" size="8" placeholder="<?php _e('주문서번호', GC_NAME); ?>">
    </p>
    <p>
        <label for="id_pwd"><?php _e('비밀번호', GC_NAME); ?><strong class="sound_only"> 필수</strong></label>
        <input type="password" name="od_pwd" size="8" id="od_pwd" placeholder="<?php _e('비밀번호', GC_NAME); ?>" required class="frm_input required">
    </p>
    <p>
        <input type="submit" value="확인" class="btn_submit">
    </p>
    </form>
</div>

<section id="mb_login_odinfo">
    <h2>비회원 주문조회 안내</h2>
    <p>메일로 발송해드린 주문서의 <strong>주문번호</strong> 및 주문 시 입력하신 <strong>비밀번호</strong>를 정확히 입력해주십시오.</p>
</section>