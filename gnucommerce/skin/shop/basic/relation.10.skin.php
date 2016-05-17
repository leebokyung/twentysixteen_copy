<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
global $post;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>

<!-- 상품진열 10 시작 { -->
<ul class="sct boots_row sct_10">
<?php
$item_list = NEW GC_item_list();
$loop_skin_file = apply_filters('get_shop_related_skin_file', 'itemloop.skin.php');
foreach($related_items as $related_id){
    //상품이 없다면 continue;

    if( empty($related_id) || false == ($post = gc_get_product_info($related_id, OBJECT)) ) continue;

    gc_skin_load($loop_skin_file, array('goods'=>$post, 'item_list'=> apply_filters('gc_main_item_obj', $item_list, 'is_relation') ));
}
?>
</ul>