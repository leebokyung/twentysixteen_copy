<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

//https://codex.wordpress.org/Function_Reference/wp_list_comments 를 참고할것
$per_page = $config['cf_page_rows'] ? $config['cf_page_rows'] : 5;
$nonce = wp_create_nonce( 'gc-itemuse' );
?>
<div id="gc_reviews">
	<div class="gc_comments">
		<h2><?php
			if ( $count = gc_get_review_count() )
				printf( __('%s건 의 사용후기', GC_NAME), $count);
			else
				_e( '사용후기', GC_NAME );
		?></h2>

		<?php if ( $comments ) : ?>

			<ol id="sit_use_ol">
				<?php wp_list_comments( apply_filters( 'gc_comment_args', 'type=comment&callback=gnucommerce_comments&reverse_top_level=0&per_page='.$per_page.'&nonce='.$nonce.'&comments_per_page&skin='.$post->it_skin ), $comments ); ?>
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

			<p class="gc_no_reviews"><?php _e( '사용후기가 없습니다.', GC_NAME ); ?></p>

		<?php endif; ?>
	</div>
    
    <?php
        gnucommerce_comments('', array('skin'=>$post->it_skin), '', 'write' );
    ?>
	<div class="clear"></div>
</div>
<script>
jQuery(document).ready(function($) {

    $(".itemuse_delete").click(function(e){
        
        if (confirm("정말 삭제 하시겠습니까?\n\n삭제후에는 되돌릴수 없습니다.")) {
            return true;
        } else {
            e.preventDefault();
            return false;
        }
    });

    $(".sit_use_li_title").click(function(e){
        e.preventDefault();

        var $con = $(this).siblings(".sit_use_con");
        gnucommerce.toggle_el_fn( $con[0] );
    });

    $(".itemuse_form").click(function(e){
        window.open(this.href, "itemuse_form", "width=810,height=680,scrollbars=1");
        return false;
    });
});
</script>