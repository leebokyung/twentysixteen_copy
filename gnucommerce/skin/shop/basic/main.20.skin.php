<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
global $post;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );

wp_enqueue_script(GC_NAME.'_swiper_js', plugin_dir_url (__FILE__).'js/swiper.jquery.min.js' );
$uniqid_string = 'slider_'.uniqid();
?>
<div class="swiper-container gc_main_20 <?php echo $uniqid_string;?>">
<div class="swiper-wrapper">
<!-- 상품진열 10 시작 { -->
<?php
$loop = 0;

foreach($items as $post){
    if( empty($post) ) continue;

    $classes = array();

    $loop++;
?>
    <div class="swiper-slide">
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
    </div>
<?php
}   //end foreach

if($loop == 0) echo "<li class=\"sct_noitem\">등록된 상품이 없습니다.</li>\n";
?>
<!-- } 상품진열 10 끝 -->
</div>
<!-- Add Arrows -->
<div class="swiper-button-next"></div>
<div class="swiper-button-prev"></div>
</div>
<!-- Initialize Swiper -->
<script>
jQuery(document).ready(function($) {
    (function(){
        var load_num = gc_swiper_get_width();

        var mySwiper = new Swiper('.gc_main_20.<?php echo $uniqid_string;?>', {
            slidesPerView: load_num,
            nextButton: '.<?php echo $uniqid_string;?> .swiper-button-next',
            prevButton: '.<?php echo $uniqid_string;?> .swiper-button-prev',
            spaceBetween: 30,
            loop:true,
        });
        function gc_fixSwiper() {
            if( load_num != gc_swiper_get_width() ){
                load_num = mySwiper.params.slidesPerView = gc_swiper_get_width();
                mySwiper.update();
            }
        }
        function gc_swiper_get_width(){
            var $window_width = $(window).width(),
                num = <?php echo $list_mod; ?>;
            if( $window_width < 675 ){
                num = 2;
            }
            if( $window_width <= 380 ){
                num = 1;
            }

            return num;
        }
        $(window).resize(function(){ gc_fixSwiper() });
    })();
});
</script>