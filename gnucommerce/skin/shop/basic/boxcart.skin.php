<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

if( $hresult = GC_Cart::get_cart_data() ){    //장바구니에 담긴 상품이 있으면
?>

<!-- 장바구니 간략 보기 시작 { -->
<div id="sbsk">
    <h2>장바구니</h2>

    <ul>
    <?php
    foreach($hresult as $row)
    {
        if( empty($row) ) continue;

        echo '<li>';
        $it_name = gc_get_text($row['it_name']);

        // 이미지로 할 경우
        //$it_name = gc_get_it_image($row['it_id'], 50, 50, true);
        echo '<a href="'.gc_get_page_url('cart').'">'.$it_name.'</a>';
        echo '</li>';
    }
?>
    </ul>

</div>
<!-- } 장바구니 간략 보기 끝 -->
<?php
} else {
    echo '<div id="sbsk_empty">'.__('장바구니 상품 없음.', GC_NAME).'</div>'.PHP_EOL;
}
?>