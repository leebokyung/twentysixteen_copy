<?php
if( !defined('GC_NAME') ) exit;

$sct_sort_href = get_post_type_archive_link(GC_NAME);

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>

<!-- 상품 정렬 선택 시작 { -->
<section id="sct_sort">
    <h2>상품 정렬</h2>
    <ul id="ssch_sort">
        <li><a href="<?php echo add_query_arg( array('orderby'=>'it_sum_qty'), $sct_sort_href); ?>" >판매많은순</a></li>
        <li><a href="<?php echo add_query_arg( array('orderby'=>'price-asc'), $sct_sort_href); ?>">낮은가격순</a></li>
        <li><a href="<?php echo add_query_arg( array('orderby'=>'price'), $sct_sort_href); ?>">높은가격순</a></li>
        <li><a href="<?php echo add_query_arg( array('orderby'=>'rating'), $sct_sort_href); ?>">평점높은순</a></li>
        <li><a href="<?php echo add_query_arg( array('orderby'=>'comment_count'), $sct_sort_href); ?>">후기많은순</a></li>
        <li><a href="<?php echo add_query_arg( array('orderby'=>'post_date'), $sct_sort_href); ?>">최근등록순</a></li>
    </ul>
</section>
<!-- } 상품 정렬 선택 끝 -->