<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

global $post;

wp_enqueue_script(GC_NAME.'_skin_bxsilder_js', gc_shop_skin_path('url', $post->it_skin).'/js/jquery.bxslider.js' );
wp_enqueue_script(GC_NAME.'_simple-lightbox_js', gc_shop_skin_path('url', $post->it_skin).'/js/simple-lightbox.min.js' );

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
$gc_ajax_nonce = wp_create_nonce( "item_form" );
?>

<form name="fitem" method="post" onsubmit="return fitem_submit(this);">
<input type="hidden" name="it_id[]" value="<?php echo $post->ID; ?>">
<input type="hidden" name="sw_direct">
<input type="hidden" name="url">

<div id="sit_ov_wrap">
    <!-- 상품이미지 미리보기 시작 { -->
    <ul class="clearfix">
        <li class="w50" id="sit_pvi">
            <div class="shop_view_slider">
                <ul class="gc_bxslider">
                <?php
                if ( $it_images ) {
                    foreach ( $it_images as $attachment_id ) {
                        if( empty($attachment_id) ) continue;
                        $image_attributes = wp_get_attachment_image_src( $attachment_id, 'full' ); // returns an array

                        if( $image_html = wp_get_attachment_image( $attachment_id, 'gc-mimg' ) ){
                            echo '<li><a href="'.$image_attributes[0].'" class="gc_images" data-attachment_id="' . esc_attr( $attachment_id ) . '">
                                ' . wp_get_attachment_image( $attachment_id, 'gc-mimg' ) . '
                            </a></li>';
                        }
                    }
                } else {
                    echo '<li><img src="'.gc_default_image_src().'" title="'.__('이미지가 없습니다', GC_NAME).'" ></li>';
                }
                ?>
                </ul>
                <?php
                // 썸네일
                $thumb_count = 0;

                if ( $it_images ) {
                    echo '<div id="gc-bx-pager">';
                    foreach($it_images as $attachment_id) {
                        if( empty($attachment_id) ) continue;
                        if( $thumb_src = wp_get_attachment_image($attachment_id, array(60,60)) ){
                            echo '<a href="#" data-attachment_id="' . esc_attr( $attachment_id ) . '" data-slide-index="'.$thumb_count.'" class="popup_item_image img_thumb">'.$thumb_src.'<span class="sound_only"> '.$thumb_count.'번째 이미지 새창</span></a>';
                            $thumb_count++;
                        }
                    }   //end foreach
                    echo '</div>';
                }
                ?>
            </div>
        </li>
        <!-- } 상품이미지 미리보기 끝 -->

        <!-- 상품 요약정보 및 구매 시작 { -->
        <li id="sit_ov" class="w50">
            <h2 id="sit_title">
                <?php echo stripslashes($post->post_title); ?> <span class="sound_only"><?php _e('요약정보 및 구매', GC_NAME); ?></span>  
            </h2>
            <div id="sit_info">
                <p id="sit_desc"><?php echo $it['it_basic']; ?></p>
                            
                <?php if($is_orderable) { ?>
                <p id="sit_opt_info">
                    <?php echo sprintf(__('상품 선택옵션 %s 개, 추가옵션 %s 개', GC_NAME), $option_count, $supply_count); ?>
                </p>
                <?php } ?>
            
                <ul class="sit_ov_box">
                    <?php if ($it['it_maker']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('제조사', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo $it['it_maker']; ?></span>
                    </li>
                    <?php } ?>
        
                    <?php if ($it['it_origin']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('원산지', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo $it['it_origin']; ?></span>
                    </li>
                    <?php } ?>
        
                    <?php if ($it['it_brand']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('브랜드', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo $it['it_brand']; ?></span>
                    </li>
                    <?php } ?>
        
                    <?php if ($it['it_model']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('모델', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo $it['it_model']; ?></span>
                    </li>
                    <?php } ?>
        
                    <?php if (!$it['it_use']) { // 판매가능이 아닐 경우 ?>
                    <li>
                        <span class="ov_row"><?php _e('판매가격', GC_NAME); ?></span>
                        <span class="ov_description"><?php _e('판매중지', GC_NAME); ?></span>
                    </li>
                    <?php } else if ($it['it_tel_inq']) { // 전화문의일 경우 ?>
                    <li>
                        <span class="ov_row"><?php _e('판매가격', GC_NAME); ?></span>
                        <span class="ov_description"><?php _e('전화문의', GC_NAME); ?></span>
                    </li>
                    <?php } else { // 전화문의가 아닐 경우?>
                    <?php if ($it['it_cust_price']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('시중가격', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo gc_display_price($it['it_cust_price']); ?></span>
                    </li>
                    <?php } // 시중가격 끝 ?>
        
                    <li>
                        <span class="ov_row"><?php _e('판매가격', GC_NAME); ?></span>
                        <span class="ov_description">
                            <?php echo gc_display_price(gc_get_price($it)); ?>
                            <input type="hidden" id="it_price" value="<?php echo gc_get_price($it); ?>">
                        </span>
                    </li>
                    <?php } ?>
        
                    <li>
                        <span class="ov_row"><?php _e('재고수량', GC_NAME); ?></span>
                        <span class="ov_description">
                        <?php echo sprintf(__('%s 개', GC_NAME), gc_number_format(gc_get_it_stock_qty($it['it_id'])) ); ?>
                        </span>
                    </li>
        
                    <?php if ($config['cf_use_mileage']) { // 적립금을 사용한다면 ?>
                    <li>
                        <span class="ov_row"><?php _e('적립금', GC_NAME); ?></span>
                        <span class="ov_description">
                            <?php
                            if($it['it_point_type'] == 2) {
                                echo '구매금액(추가옵션 제외)의 '.$it['it_point'].'%';
                            } else {
                                $it_point = gc_get_item_point($it);
                                echo gc_number_format($it_point).'원';
                            }
                            ?>
                        </span>
                    </li>
                    <?php } ?>
                    <?php
                    $ct_send_cost_label = __('배송비결제', GC_NAME);
        
                    if($it['it_sc_type'] == 1)
                        $sc_method = __('무료배송', GC_NAME);
                    else {
                        if($it['it_sc_method'] == 1)
                            $sc_method = __('수령후 지불', GC_NAME);
                        else if($it['it_sc_method'] == 2) {
                            $ct_send_cost_label = '<label for="ct_send_cost">'.__('배송비결제', GC_NAME).'</label>';
                            $sc_method = '<select name="ct_send_cost" id="ct_send_cost">
                                              <option value="0">'.__('주문시 결제', GC_NAME).'</option>
                                              <option value="1">'.__('수령후 지불', GC_NAME).'</option>
                                          </select>';
                        }
                        else
                            $sc_method = __('주문시 결제', GC_NAME);
                    }
                    ?>
                    <li>
                        <span class="ov_row"><?php echo $ct_send_cost_label; ?></span>
                        <span class="ov_description"><?php echo $sc_method; ?></span>
                    </li>
                    <?php if($it['it_buy_min_qty']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('최소구매수량', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo gc_number_format($it['it_buy_min_qty']); ?> 개</span>
                    </li>
                    <?php } ?>
                    <?php if($it['it_buy_max_qty']) { ?>
                    <li>
                        <span class="ov_row"><?php _e('최대구매수량', GC_NAME); ?></span>
                        <span class="ov_description"><?php echo gc_number_format($it['it_buy_max_qty']); ?> 개</span>
                    </li>
                    <?php } ?>
                </ul>
                <p class="sns_star">
                    <?php if ($star_score) { ?>
                    <img src="<?php echo GC_DIR_URL; ?>img/s_star<?php echo $star_score?>.png" alt="" class="sit_star">
                    <?php } ?>
                </p>
            </div>
            
            
            <?php
            if($option_item) {
            ?>
            <!-- 선택옵션 시작 { -->
            <section class="sit_option">
                <h3><?php _e('선택옵션', GC_NAME); ?></h3>
                <ul class="sit_ov_box sit_op_box">
                <?php // 선택옵션
                echo $option_item;
                ?>

                </ul>
            </section>
            <!-- } 선택옵션 끝 -->
            <?php
            }
            ?>

            <?php
            if($supply_item) {
            ?>
            <!-- 추가옵션 시작 { -->
            <section class="sit_option">
                <h3><?php _e('추가옵션', GC_NAME); ?></h3>
                <ul class="sit_ov_box sit_op_box">
                <?php // 추가옵션
                echo $supply_item;
                ?>
                </ul>
            </section>
            <!-- } 추가옵션 끝 -->
            <?php
            }
            ?>

            <?php if ($is_orderable) { ?>
            <!-- 선택된 옵션 시작 { -->
            <section id="sit_sel_option">
                <h3><?php _e('선택된 옵션', GC_NAME); ?></h3>
                <?php
                if(!$option_item) {
                    if(!$it['it_buy_min_qty'])
                        $it['it_buy_min_qty'] = 1;
                ?>
                <ul id="sit_opt_added">
                    <li class="sit_opt_list">
                        <input type="hidden" name="io_type[<?php echo $post->ID; ?>][]" value="0">
                        <input type="hidden" name="io_id[<?php echo $post->ID; ?>][]" value="">
                        <input type="hidden" name="io_value[<?php echo $post->ID; ?>][]" value="<?php echo $it['it_name']; ?>">
                        <input type="hidden" class="io_price" value="0">
                        <input type="hidden" class="io_stock" value="<?php echo $it['it_stock_qty']; ?>">
                        <span class="sit_opt_subj"><?php echo $it['it_name']; ?></span>
                        <span class="sit_opt_prc">(+0원)</span>
                        <div>
                            <label for="ct_qty_11" class="sound_only"><?php _e('수량', GC_NAME); ?></label>
                            <input type="text" name="ct_qty[<?php echo $post->ID; ?>][]" value="<?php echo $it['it_buy_min_qty']; ?>" id="ct_qty_11" class="frm_input" size="5">
                            <button type="button" class="sit_qty_plus btn_frmline"><?php _e('증가', GC_NAME); ?></button>
                            <button type="button" class="sit_qty_minus btn_frmline"><?php _e('감소', GC_NAME); ?></button>
                        </div>
                    </li>
                </ul>
                <?php
                GC_VAR()->add_inline_scripts('gnucommerce.price_calculate();');
                }   //end if $is_orderable
                ?>
            </section>
            <!-- } 선택된 옵션 끝 -->

            <!-- 총 구매액 -->
            <div id="sit_tot_price"></div>
            <?php } ?>

            <?php if($is_soldout) { ?>
            <p id="sit_ov_soldout"><?php _e('상품의 재고가 부족하여 구매할 수 없습니다.', GC_NAME); ?></p>
            <?php } ?>

            <div id="sit_ov_btn">
                <?php if ($is_orderable) { ?>
                <input type="submit" onclick="document.pressed=this.value;" value="<?php _e('바로구매', GC_NAME); ?>" id="sit_btn_buy" class="icon_buy_btn">
                <input type="submit" onclick="document.pressed=this.value;" value="<?php _e('장바구니', GC_NAME); ?>" id="sit_btn_cart" class="icon_cart_btn">
                
                <?php } ?>
                <?php if(!$is_orderable && $it['it_soldout'] && $it['it_stock_sms']) { ?>
                <button type="button" data-itid="<?php echo $it['it_id']; ?>" title="<?php _e('상품 재입고알림 SMS', GC_NAME); ?>" id="sit_btn_buy" class="sms_alram"><i class="fa fa-bell sms_alram_icon" aria-hidden="true"></i> <?php _e('재입고알림', GC_NAME); ?></button>
                <?php } ?>
                <button type="button" onclick="gc_item_wish(document.fitem, '<?php echo $it['it_id']; ?>');" id="sit_btn_wish"><i class="fa fa-heart icon_wish_btn" aria-hidden="true"></i> <?php _e('위시리스트', GC_NAME); ?></button>
            </div>

            <script>
            gnucommerce.wish_ing = false;
            // 상품보관
            function gc_item_wish(f, it_id)
            {
                <?php if( !is_user_logged_in() ){ ?>
                alert("<?php _e('로그인 하셔야 합니다.', GC_NAME); ?>");
                return;
                <?php } ?>
                if( gnucommerce.wish_ing ){ //등록중이면 return;
                    return;
                }
            
                gnucommerce.wish_ing = true;
                var formData = jQuery(f).serialize()+"&action=gc_wishupdate&security=<?php echo $gc_ajax_nonce;?>";

                var ajax_var = jQuery.ajax({
                    type:"POST",
                    url: gnucommerce.ajaxurl,
                    data:formData,
                    dataType   : 'json', // xml, html, script, json
                    cache: false,
                    success:function(data, status, xhr){
                        if( data.msg == "true" ){
                            if (confirm("<?php _e('위시리스트에 추가되었습니다. 위시리스트로 이동하시겠습니까?', GC_NAME); ?>")){
                                if( data.url ){
                                    location.replace(data.url);
                                }
                            }
                        } else {
                            alert(data.msg);
                        }
                        gnucommerce.wish_ing = false;
                    },
                    error : function(request, status, error){
                        alert('false ajax :'+request.responseText);
                        gnucommerce.wish_ing = false;
                    }
                }); // end of ajax

                return false;
            }
            </script>
        </li>
    </ul>
    <!-- } 상품 요약정보 및 구매 끝 -->
    <div id="sit_star_sns" >
        <?php echo $sns_share_links; ?>
    </div>
    <!-- 다른 상품 보기 시작 { -->
    <div id="sit_siblings">
        <?php
        if ($prev_href || $next_href) {
            echo '<span class="prev">' .$prev_href.$prev_title.$prev_href2.'</span>';
            echo '<span class="next">'.$next_href.$next_title.$next_href2.'</span>';
        } else {
            echo '<span class="sound_only">'.__('이 분류에 등록된 다른 상품이 없습니다.', GC_NAME).'</span>';
        }
        ?>                    
    </div>
    <!-- } 다른 상품 보기 끝 -->
</div>
</form>

<?php
if(!$is_orderable && $it['it_soldout'] && $it['it_stock_sms']) {    //재고입고알림 관련
    gc_skin_load('itemstocksms.skin.php', array('it'=>$it) );   //재고입고알림 rayer
}
?>

<script>
jQuery(document).ready(function($) {
    // 상품이미지 첫번째 링크

    $("#sit_ov_btn").on("click", "button", function(e){
        e.preventDefault();
    });

    <?php if( $it_images ){ ?>
    if (typeof $.fn.bxSlider !== 'undefined') {
        $('.gc_bxslider').bxSlider({
            pagerCustom: '#gc-bx-pager',
            swipeThreshold: 100,
            speed : 200,
            onSliderLoad: function(){
                $(".bx-clone").children("a").removeClass();
                var gallery = $(".gc_bxslider .gc_images").simpleLightbox();
            },
            onSlideAfter: function(e,q,i){
            }
        });
    }
    <?php } ?>
});


// 바로구매, 장바구니 폼 전송
function fitem_submit(f)
{
    if (document.pressed == "장바구니") {
        f.sw_direct.value = 0;
    } else { // 바로구매
        f.sw_direct.value = 1;
    }

    // 판매가격이 0 보다 작다면
    if (document.getElementById("it_price").value < 0) {
        alert("전화로 문의해 주시면 감사하겠습니다.");
        return false;
    }

    if(jQuery(".sit_opt_list").size() < 1) {
        alert("상품의 선택옵션을 선택해 주십시오.");
        return false;
    }

    var val, io_type, result = true;
    var sum_qty = 0;
    var min_qty = parseInt(<?php echo $it['it_buy_min_qty']; ?>);
    var max_qty = parseInt(<?php echo $it['it_buy_max_qty']; ?>);
    var $el_type = jQuery("input[name^=io_type]");

    jQuery("input[name^=ct_qty]").each(function(index) {
        val = jQuery(this).val();

        if(val.length < 1) {
            alert("수량을 입력해 주십시오.");
            result = false;
            return false;
        }

        if(val.replace(/[0-9]/g, "").length > 0) {
            alert("수량은 숫자로 입력해 주십시오.");
            result = false;
            return false;
        }

        if(parseInt(val.replace(/[^0-9]/g, "")) < 1) {
            alert("수량은 1이상 입력해 주십시오.");
            result = false;
            return false;
        }

        io_type = $el_type.eq(index).val();
        if(io_type == "0")
            sum_qty += parseInt(val);
    });

    if(!result) {
        return false;
    }

    if(min_qty > 0 && sum_qty < min_qty) {
        alert("선택옵션 개수 총합 "+number_format(String(min_qty))+"개 이상 주문해 주십시오.");
        return false;
    }

    if(max_qty > 0 && sum_qty > max_qty) {
        alert("선택옵션 개수 총합 "+number_format(String(max_qty))+"개 이하로 주문해 주십시오.");
        return false;
    }

    return true;
}
</script>