<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
global $post;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<ul class="gc_st_container main_10">
<!-- 상품진열 10 시작 { -->
<?php
$loop = 0;

foreach($items as $post){
    if( empty($post) ) continue;

    $classes = array();

    $classes[] = 'col-yc-'.$list_mod;

    if( $loop && ($loop % $list_mod == 0) ){
        $classes[] = 'box_clear';
    }

    $loop++;
?>
    <li <?php post_class( $classes, $post->ID ); ?>>
        <?php do_action( 'gc_before_shortcode_loop' ); ?>
        <a class="item_box_href" href="<?php the_permalink(); ?>">
            <?php
                echo '<div class="sct_img">'.gc_get_product_thumbnail().'</div>';  //썸네일
            ?>
            <div class="sct_txt">
                <?php
                    if ($item_list->view_it_icon) {  //아이콘 표시 여부
                        echo "<div class=\"sct_icon\">".gc_item_icon($post)."</div>\n";
                    }
                    if( $item_list->view_it_name ){     //상품 이름을 표시
                        echo "<div class=\"sct_tit\">".the_title()."</div>\n";
                    }
                    if ($item_list->view_it_basic && $post->it_basic){     //기본설명 표시
                        echo "<div class=\"sct_basic\">".esc_html($post->it_basic)."</div>\n";
                    }
                    echo "<div class=\"sct_cost\">\n";
                    
                    if ($item_list->view_it_cust_price && $post->it_cust_price) {
                        echo "<strike>".gc_display_price($post->it_cust_price)."</strike>\n";
                    }
                    echo gc_display_price(gc_get_price($post), $post->it_tel_inq)."\n";

                    echo "</div>\n";
            ?>
            </div>
            <?php do_action( 'gc_after_shortcode_loop_title', $post, $items ); ?>
        </a>
        <?php do_action( 'gc_after_shortcode_loop', $post, $items ); ?>
    </li>
<?php
}   //end foreach

if($loop == 0) echo "<li class=\"sct_noitem\">등록된 상품이 없습니다.</li>\n";
?>
<!-- } 상품진열 10 끝 -->
</ul>