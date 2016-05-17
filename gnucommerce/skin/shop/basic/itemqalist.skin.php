<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>

<!-- 전체 상품 문의 목록 시작 { -->

<form method="get">
<div id="sqa_sch">
    <a href="<?php echo add_query_arg(array('view'=>'itemqalist'), get_permalink()); ?>">전체보기</a>
    <label for="sfl" class="sound_only">검색항목<strong class="sound_only"> 필수</strong></label>
    <select name="sfl" id="sfl" required class="required">
        <option value="">선택</option>
        <option value="b.it_name"    <?php echo selected($sfl, "b.it_name"); ?>>상품명</option>
        <option value="a.it_id"      <?php echo selected($sfl, "a.it_id"); ?>>상품코드</option>
        <option value="a.iq_subject" <?php echo selected($sfl, "a.is_subject"); ?>>문의제목</option>
        <option value="a.iq_question"<?php echo selected($sfl, "a.iq_question"); ?>>문의내용</option>
        <option value="a.iq_name"    <?php echo selected($sfl, "a.it_id"); ?>>작성자명</option>
        <option value="a.mb_id"      <?php echo selected($sfl, "a.mb_id"); ?>>작성자아이디</option>
    </select>

    <label for="stx" class="sound_only">검색어<strong class="sound_only"> 필수</strong></label>
    <input type="text" name="stx" value="<?php echo $stx; ?>" id="stx" required class="required frm_input">
    <input type="submit" value="검색" class="btn_submit">
</div>
</form>

<div id="sqa">

    <!-- <p><?php echo get_bloginfo(); ?> 전체 상품문의 목록입니다.</p> -->

    <?php
    $thumbnail_width = 500;
    $num = $total_count - ($npage - 1) * $rows;
    
    $i = 0;
    foreach($results as $row)
    {
        if( empty($row) ) continue;

        $iq_subject = gc_conv_subject($row['iq_subject'],50,"…");

        $is_secret = false;
        if($row['iq_secret']) {
            $iq_subject .= ' <img src="'.GC_SHOP_SKIN_URL.'/img/icon_secret.gif" alt="비밀글">';

            if(gc_is_admin() || (get_current_user_id() && get_current_user_id() == $row['mb_id'])) {
                $iq_question = gc_conv_content($row['iq_question'], 1);
            } else {
                $iq_question = '비밀글로 보호된 문의입니다.';
                $is_secret = true;
            }
        } else {
            $iq_question = gc_conv_content($row['iq_question'], 1);
        }

        $it_href = get_permalink($row['it_id']); 

        if ($row['iq_answer'])
        {
            $iq_answer = gc_conv_content($row['iq_answer'], 1);
            $iq_stats = '답변완료';
            $iq_style = 'sit_qaa_done';
            $is_answer = true;
        } else {
            $iq_stats = '답변전';
            $iq_style = 'sit_qaa_yet';
            $iq_answer = '답변이 등록되지 않았습니다.';
            $is_answer = false;
        }

        if ($i == 0) echo '<ol>';
    ?>
    <li>

        <div class="sqa_img">
            <a href="<?php echo $it_href; ?>">
                <?php echo gc_get_it_image($row['it_id'], 70, 70); ?>
                <span><?php echo $row['it_name']; ?></span>
            </a>
        </div>

        <section class="sqa_section">
            <h2><?php echo $iq_subject; ?></h2>

            <dl class="sqa_dl">
                <dt>작성자</dt>
                <dd><?php echo $row['iq_name']; ?></dd>
                <dt>작성일</dt>
                <dd><?php echo substr($row['iq_time'],0,10); ?></dd>
                <dt>상태</dt>
                <dd class="<?php echo $iq_style; ?>"><?php echo $iq_stats; ?></dd>
            </dl>

            <div id="sqa_con_<?php echo $i; ?>" class="sqa_con" style="display:none;">
                <div class="sit_qa_qaq">
                    <strong>문의내용</strong><br>
                    <?php echo $iq_question; // 상품 문의 내용 ?>
                </div>
                <?php if(!$is_secret) { ?>
                <div class="sit_qa_qaa">
                    <strong>답변</strong><br>
                    <?php echo $iq_answer; ?>
                </div>
                <?php } ?>
            </div>

            <div class="sqa_con_btn"><button class="sqa_con_<?php echo $i; ?>">보기</button></div>
        </section>

    </li>
    <?php
        $i++;
        $num--;
    }   //end foreach

    if ($i > 0) echo '</ol>';
    if ($i == 0) echo '<p id="sqa_empty">자료가 없습니다.</p>';
    ?>
</div>

<?php echo gc_get_paging(GC_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $npage, $total_page, add_query_arg(array('npage'=>false)), '', 'npage'); ?>

<script>
jQuery(document).ready(function($) {

    var $button = $('.sqa_section .sqa_con_btn button'),
        accordion_body = $('.sqa_section .sqa_con');

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