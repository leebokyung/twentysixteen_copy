<?php
if( !defined('GC_NAME') ) exit;

global $comment;

if ( 'div' == $args['style'] ) {
    $tag = 'div';
    $add_below = 'comment';
} else {
    $tag = 'li';
    $add_below = 'div-comment';
}

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

$comment_subject = get_comment_meta( get_comment_ID(), 'comment_subject', true );
$gc_is_score = get_comment_meta( get_comment_ID(), 'gc_is_score', true );
$is_num = gc_get_review_count() - ($args['page'] - 1) * $args['per_page'] - ($i - 1);
?>
<<?php echo $tag;?> <?php comment_class( array('sit_use_li', empty( $args['has_children'] ) ? '' : 'parent' )) ?> id="comment-<?php comment_ID() ?>">
        <div class="sps_img">
            <a href="<?php echo get_permalink($comment->comment_post_ID); ?>">
                <?php echo gc_get_it_image($comment->comment_post_ID, 70, 70); ?>
                <span></span>
            </a>
        </div>

        <section class="sps_section">
            <h2><?php echo gc_get_text($comment_subject); ?></h2>

            <dl class="sps_dl">
                <dt>작성자</dt>
                <dd><?php echo get_comment_author_link(); ?></dd>
                <dt>작성일</dt>
                <dd><?php printf( __('%1$s %2$s'), get_comment_date(),  get_comment_time() ); ?></dd>
                <dt>평가점수</dt>
                <dd><img src="<?php echo GC_SHOP_URL; ?>/img/s_star<?php echo $gc_is_score; ?>.png" alt="별<?php echo $gc_is_score; ?>개"></dd>
            </dl>

            <div id="sps_con_<?php echo $i; ?>" class="sps_con" style="display:none;">
                <?php comment_text(); // 사용후기 내용 ?>
            </div>

            <div class="sps_con_btn"><button class="sps_con_<?php echo $i; ?>">보기</button></div>
        </section>
</<?php echo $tag;?>>