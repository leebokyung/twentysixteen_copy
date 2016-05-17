<?php
if( !defined('GC_NAME') ) exit;
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
$empty_cart_msg = apply_filters('gc_empty_cart_msg', '장바구니가 비어 있습니다.');
?>
<div class="empty_cart_msg"><?php echo $empty_cart_msg; ?></div>