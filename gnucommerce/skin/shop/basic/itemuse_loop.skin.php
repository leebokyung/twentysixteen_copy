<?php
if( !defined('GC_NAME') ) exit;

global $comment;

if( !isset($args['comment']) || empty($args['comment']) ){
    return;
}

if ( isset($args['style']) && 'div' == $args['style'] ) {
    $tag = 'div';
    $add_below = 'comment';
} else {
    $tag = 'li';
    $add_below = 'div-comment';
}

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

$comment_subject = get_comment_meta( get_comment_ID(), 'comment_subject', true );
if( !$comment_subject ){    //코멘트 제목이 없으면
    $comment_subject = __('제목이 없습니다.', GC_NAME);
}
$gc_is_score = get_comment_meta( get_comment_ID(), 'gc_is_score', true );
$is_num = gc_get_review_count() - ($args['page'] - 1) * $args['per_page'] - ($i - 1);
?>
	<<?php echo $tag ?> <?php comment_class( array('sit_use_li', empty( $args['has_children'] ) ? '' : 'parent' )) ?> id="comment-<?php comment_ID() ?>">
    <a type="button" class="sit_use_li_title"><?php echo $comment_subject; ?></a>
    <span class="abs_right">
    <?php if ( $args['avatar_size'] != 0 ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
    </span>
    <dl class="sit_use_dl">
        <dt>작성자</dt>
        <dd><?php echo get_comment_author_link(); ?></dd>
        <dt>작성일</dt>
        <dd><?php printf( __('%1$s'), get_comment_date(),  get_comment_time() ); ?></a></dd>
        <?php if($gc_is_score){    //평가한 점수가 있다면 ?>
        <dt>평점<dt>
        <dd class="sit_use_star"><img src="<?php echo GC_SHOP_URL; ?>/img/s_star<?php echo $gc_is_score; ?>.png" alt="별<?php echo $gc_is_score; ?>개"></dd>
        <?php } ?>
    </dl>
    <?php if ( $comment->comment_approved == '0' ) : ?>
    <div>
    <em class="comment-awaiting-moderation"><?php _e( '관리자의 승인을 기다리고 있는 글입니다.' ); ?></em>
    </div>
    <?php endif; ?>
    <div id="div-comment-<?php comment_ID() ?>" class="comment-body sit_use_con">
        <div class="sit_use_p">
            <?php comment_text(); ?>
            <?php if ( current_user_can( 'edit_comment', $comment->comment_ID ) || $comment->user_id == get_current_user_id() ) { ?>
            <div class="sit_use_cmd">
                <a href="<?php echo add_query_arg(array('is_id'=>$comment->comment_ID, 'gw'=>'u'), gc_get_page_url('itemuseform'));?>" class="itemuse_form btn01" onclick="return false;">수정</a>
                <a href="<?php echo add_query_arg(array('is_id'=>$comment->comment_ID, 'gw'=>'d', '_wpnonce'=>$nonce), gc_get_page_url('itemuseformupdate'));?>" class="itemuse_delete btn01">삭제</a>
            </div>
            <?php } ?>
        </div>
    </div>