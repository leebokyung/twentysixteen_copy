<?php
if( !defined('GC_NAME') ) exit;

global $wp_query;

wp_enqueue_script( GC_NAME.'-shop-list-js', GC_DIR_URL.'js/shop.list.js', '', GC_VERSION, 100 );

$skin_dir = gc_shop_skin_path('', $post->it_skin);

$item_list = NEW GC_item_list();

$it_args = array(
    'skin_dir'  =>  $skin_dir,
    'item_list' =>  apply_filters('gc_main_item_obj', $item_list),
    'orderby'   => isset($_GET['orderby']) ? sanitize_text_field($_GET['orderby']) : 'post_date',
);
do_action('gc_ca_include_head', $it_args);

//상품 정렬 관련 html
if( gc_is_product_page($wp_query) || $wp_query->found_posts > 1 ){
    gc_skin_load('item_list_head.skin.php', $it_args);
}

$loop_skin_file = apply_filters('get_shop_list_skin_file', 'itemloop.skin.php');

echo apply_filters('gc_item_loop_start', '<ul class="sct boots_row sct_10">', array('sct', 'boots_row', 'sct_10'));

$k = 0;

while ( gc_have_items() ) : gc_the_item();

    $goods = gc_get_product_info(gc_get_item_id(), OBJECT);

    if( $goods->post_type == GC_NAME ){
        $it_args['goods'] = $goods;

        gc_skin_load($loop_skin_file, $it_args);
        $k++;
    }
endwhile;

echo apply_filters('gc_item_loop_end', '</ul>');

if(!$k){
    echo "등록된 상품이 없습니다. 상품을 등록해주세요.";
    return;
}
//페이지를 표시한다.    lib/template.hooks.php 참고
do_action( 'gc_pagination_print', $it_args);
?>