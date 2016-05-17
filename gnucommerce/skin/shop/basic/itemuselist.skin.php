<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
global $wp_query;
?>

<!-- 전체 상품 사용후기 목록 시작 { -->
<form method="get">
<div id="sps_sch">
    <a href="<?php echo add_query_arg(array('view'=>'itemuselist'), get_permalink()); ?>">전체보기</a>
    <label for="sfl" class="sound_only">검색항목<strong class="sound_only"> 필수</strong></label>
    <select name="sfl" id="sfl" required>
        <option value="">선택</option>
        <option value="b.it_name"   <?php selected($sfl, "b.it_name"); ?>>상품명</option>
        <option value="a.it_id"     <?php selected($sfl, "a.it_id"); ?>>상품코드</option>
        <option value="a.is_subject"<?php selected($sfl, "a.is_subject"); ?>>후기제목</option>
        <option value="a.is_content"<?php selected($sfl, "a.is_content"); ?>>후기내용</option>
        <option value="a.is_name"   <?php selected($sfl, "a.is_name"); ?>>작성자명</option>
        <option value="a.mb_id"     <?php selected($sfl, "a.mb_id"); ?>>작성자아이디</option>
    </select>
    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" required class="required frm_input">
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<div id="sps">

    <!-- <p><?php echo get_bloginfo(); ?> 전체 사용후기 목록입니다.</p> -->

    <?php if ( $comments = get_comments(array('post_type'=>GC_NAME)) ) :
    $comments_per_page = get_query_var( 'comments_per_page' );
    $wp_query->query_vars['comments_per_page'] = $per_page;
    $ori_comments = isset($wp_query->comments) ? $wp_query->comments : array();
    $wp_query->comments = $comments;
    $current_cpage = get_query_var( 'cpage' ) ? get_query_var( 'cpage' ) : 1;
    ?>

        <ol id="sit_use_ol">
            <?php wp_list_comments( apply_filters( 'gc_comment_list_args', 'type=comment&callback=gnucommerce_comment_lists&reverse_top_level=0&page='.$current_cpage.'&per_page='.$per_page ), $comments ); ?>
        </ol>

        <?php if ( get_comment_pages_count($comments, $per_page) > 1 ) :
            echo '<nav class="gc_review_pagination">';
            paginate_comments_links( apply_filters( 'gc_review_pagination_args', array(
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
                'type'      => 'list',
                'add_fragment' => '#sit_use',
            ) ) );
            echo '</nav>';
        endif; ?>

    <?php else : ?>
        
        <p id="sps_empty"><?php _e( '사용후기가 없습니다.', GC_NAME ); ?></p>

    <?php
    $wp_query->comments = $ori_comments;    //원래 값으로 되돌린다.
    $wp_query->query_vars['comments_per_page'] = $comments_per_page;   //원래 값으로 되돌린다.
    endif;
    ?>

</div>

<script>
jQuery(document).ready(function($) {

    var $button = $('.sps_section .sps_con_btn button'),
        accordion_body = $('.sps_section .sps_con');

    $button.on('click', function(e) {
        e.preventDefault();

        if (!$(this).data('toggle_enable')) {
            accordion_body.slideUp('normal');
            $(this).parent().prev().stop(true,true).slideToggle('normal');
            $button.text("보기").data('toggle_enable', false);
            $(this).text("닫기").data('toggle_enable', true);
        } else {
            $(this).text("보기").data('toggle_enable', false);
            $(this).parent().prev().slideUp('normal');
        }

    });
});
</script>
<!-- } 전체 상품 사용후기 목록 끝 -->