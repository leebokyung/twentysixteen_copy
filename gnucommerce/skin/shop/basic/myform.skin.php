<?php
if( !defined('GC_NAME') ) exit;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__), $args );
?>
<div class="gc_myform_wrap">
<div class="myform_msg">
<?php _e('상품 주문시 사용되는 정보를 수정합니다.', GC_NAME ); ?>
</div>
<form name="gc_user_form" action="<?php echo add_query_arg( array('view'=>'myformupdate'), get_permalink() );?>" method="post">
<?php wp_nonce_field( 'gc_user_form', 'gc_nonce_field' ); ?>
<input type="hidden" name="view" value="myformupdate" >
<?php
do_action('gc_user_myform', $user);
?>
<div class="btn_group">
    <input type="submit" value="<?php _e('수정하기', GC_NAME);?>" id="btn_submit" class="btn_submit">
</div>

</form>
</div>