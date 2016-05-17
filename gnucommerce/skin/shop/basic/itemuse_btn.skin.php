<?php
if( !defined('GC_NAME') ) exit;
global $post;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<div id="review_form_wrapper">
    <div class="review_btn">
        <?php if( comments_open($post->ID) ){ ?>
        <a href="<?php echo add_query_arg(array('it_id'=>$post->ID), gc_get_page_url('itemuseform'));?>" class="btn02 review_form_btn" data-target="commentform">사용후기 쓰기</a>
        <?php } //end if ?>
        <a href="<?php echo gc_get_page_url('itemuselist');?>" class="btn01 itemuse_list">더보기</a>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $(".btn02.review_form_btn").click(function(e){
        e.preventDefault();

        window.open(this.href, "itemuse_form", "width=810,height=680,scrollbars=1");
        return false;
    });
});
</script>