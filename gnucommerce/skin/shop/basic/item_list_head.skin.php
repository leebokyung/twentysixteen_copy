<?php
if( !defined('GC_NAME') ) exit;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

echo '<div id="sct_sortlst">';
    gc_skin_load('list.sort.skin.php', $args);
    // 상품 보기 타입 변경 버튼
    gc_skin_load('list.sub.skin.php', $args);
echo '</div>';
?>