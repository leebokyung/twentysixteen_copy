<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>

<!-- 사용후기 쓰기 시작 { -->
<div id="sit_use_write" class="new_win">
    <h1 id="win_title">사용후기 쓰기</h1>
    <?php
        $comment_form = array(
            'title_reply'          => '',
            'title_reply_to'       => __( '%s님께 글 남기기', GC_NAME ),
            'comment_notes_before' => '',
            'comment_notes_after'  => '',
            'fields'               => array(
                'author' => '<p class="comment-form-author">' . '<label for="author">' . __( '이름', GC_NAME ) . ' <span class="required">*</span></label> ' .
                            '<input id="author" name="author" type="text" value="' . esc_attr( $use['comment_author'] ) . '" size="30" aria-required="true" /></p>',
                'email'  => '<p class="comment-form-email"><label for="email">' . __( '이메일', GC_NAME ) . ' <span class="required">*</span></label> ' .
                            '<input id="email" name="email" type="text" value="' . esc_attr(  $use['comment_author_email'] ) . '" size="30" aria-required="true" /></p>',
            ),
            'label_submit'  => __( '확인', GC_NAME ),
            'logged_in_as'  => '',
            'comment_field' => ''
        );
    
        if( $gw == 'u' ){
            $comment_form['comment_field'] .= '<input type="hidden" name="comment_ID" value="'.esc_attr($use['comment_ID']).'" >';
            $comment_form['comment_field'] .= '<input type="hidden" name="gc_type" value="'.esc_attr(GC_NAME).'" >';
        }

        $comment_form['comment_field'] .= '<p class="comment-form-subject"><label for="comment_subject">' . __( '제목', GC_NAME ) . '<span class="required">*</span></label><input id="comment_subject" name="comment_subject" type="text" value="'.esc_attr(  $use['comment_subject'] ).'" aria-required="true" /></p>';

        $comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . __( '후기내용', GC_NAME ) . '</label><textarea id="comment" name="comment" cols="45" rows="8" aria-required="true">'.$use['comment_content'].'</textarea></p>';

        $comment_form['comment_field'] .= '<div class="comment-form-rating"><label for="rating">' . __( '평점', GC_NAME ) .'</label>
            <p><input type="radio" name="gc_is_score" value="5" id="gc_is_score5" '.gc_checked( $use['is_score'], 5 ).' ><label for="gc_is_score5" class="gc_rating">'.__('매우만족', GC_NAME).'</label><img src="'.GC_SHOP_URL.'/img/s_star5.png"></p>
            <p><input type="radio" name="gc_is_score" value="4" id="gc_is_score4" '.gc_checked( $use['is_score'], 4 ).'><label for="gc_is_score4" class="gc_rating">'.__('만족', GC_NAME).'</label><img src="'.GC_SHOP_URL.'/img/s_star4.png"></p>
            <p><input type="radio" name="gc_is_score" value="3" id="gc_is_score3" '.gc_checked( $use['is_score'], 3 ).'><label for="gc_is_score3" class="gc_rating">'.__('보통', GC_NAME).'</label><img src="'.GC_SHOP_URL.'/img/s_star3.png"></p>
            <p><input type="radio" name="gc_is_score" value="2" id="gc_is_score2" '.gc_checked( $use['is_score'], 2 ).'><label for="gc_is_score2" class="gc_rating">'.__('불만', GC_NAME).'</label><img src="'.GC_SHOP_URL.'/img/s_star2.png"></p>
            <p><input type="radio" name="gc_is_score" value="1" id="gc_is_score1" '.gc_checked( $use['is_score'], 1 ).'><label for="gc_is_score1" class="gc_rating">'.__('매우불만', GC_NAME).'</label><img src="'.GC_SHOP_URL.'/img/s_star1.png"></p>
        </div>';

        comment_form( apply_filters( 'gc_review_comment_form_args', $comment_form ), $it_id);
    ?>
</div>
<!-- } 사용후기 쓰기 끝 -->