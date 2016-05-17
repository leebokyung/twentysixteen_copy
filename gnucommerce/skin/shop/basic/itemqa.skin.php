<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>

<!-- 상품문의 목록 시작 { -->
<section id="sit_qa_list">
    <h3>등록된 상품문의</h3>

    <?php
    $thumbnail_width = 500;
    $iq_num = $total_count - ($qpage - 1) * $rows;

    $i = 0;
    foreach($result as $row)
    {
        if( empty($row) ) continue;

        $iq_name    = gc_get_text($row['iq_name']);
        $iq_subject = gc_conv_subject($row['iq_subject'],50,"…");

        $is_secret = false;
        if($row['iq_secret']) {
            $iq_subject .= ' <img src="'.GC_SHOP_SKIN_URL.'/img/icon_secret.gif" alt="비밀글">';

            if(gc_is_admin() || sprintf("%.0f", $row['mb_id']) === get_current_user_id() ) {
                $iq_question = gc_conv_content($row['iq_question'], $row['iq_editor']);
            } else {
                $iq_question = '비밀글로 보호된 문의입니다.';
                $is_secret = true;
            }
        } else {
            $iq_question = gc_conv_content($row['iq_question'], $row['iq_editor']);
        }
        $iq_time    = substr($row['iq_time'], 2, 8);

        $hash = md5($row['iq_id'].$row['iq_time'].$row['iq_ip']);

        $iq_stats = '';
        $iq_style = '';
        $iq_answer = '';

        if ($row['iq_answer'])
        {
            $iq_answer = gc_conv_content($row['iq_answer'], $row['iq_editor']);
            $iq_stats = '답변완료';
            $iq_style = 'sit_qaa_done';
            $is_answer = true;
        } else {
            $iq_stats = '답변전';
            $iq_style = 'sit_qaa_yet';
            $iq_answer = '답변이 등록되지 않았습니다.';
            $is_answer = false;
        }

        if ($i == 0) echo '<ol id="sit_qa_ol">';
    ?>

        <li class="sit_qa_li">
            <a href="#" class="sit_qa_li_title"><b><?php echo $iq_num; ?>.</b> <?php echo $iq_subject; ?></a>
            <dl class="sit_qa_dl">
                <dt>작성자</dt>
                <dd><?php echo $iq_name; ?></dd>
                <dt>작성일</dt>
                <dd><?php echo $iq_time; ?></dd>
                <dt>상태</dt>
                <dd class="<?php echo $iq_style; ?>"><?php echo $iq_stats; ?></dd>
            </dl>

            <div id="sit_qa_con_<?php echo $i; ?>" class="sit_qa_con">
                <div class="sit_qa_p">
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

                <?php if (gc_is_admin() || (sprintf("%.0f", $row['mb_id']) === get_current_user_id() && !$is_answer)) { ?>
                <div class="sit_qa_cmd">
                    <a href="<?php echo add_query_arg(array('iq_id'=>$row['iq_id'], 'gw'=>'u'), $itemqa_form); ?>" class="itemqa_form_btn btn01" onclick="return false;">수정</a>
                    <a href="<?php echo add_query_arg(array('iq_id'=>$row['iq_id'], 'gw'=>'d', 'hash'=>$hash), $itemqa_formupdate); ?>" class="itemqa_delete btn01">삭제</a>
                </div>
                <?php } ?>
            </div>
        </li>

    <?php
        $i ++;
        $iq_num--;
    }   //end foreach

    if ($i > 0) echo '</ol>';

    if (!$i) echo '<p class="sit_empty">상품문의가 없습니다.</p>';
    ?>
</section>

<?php
//echo gc_itemqa_page($config['cf_write_pages'], $qpage, $total_page, "./itemqa.php?it_id=$it_id&amp;page=", "");
?>

<div id="sit_qa_wbtn">
    <a href="<?php echo $itemqa_form; ?>" class="btn02 itemqa_form_btn">상품문의 쓰기</a>
    <a href="<?php echo $itemqa_list; ?>" id="itemqa_list" class="btn01">더보기</a>
</div>

<script>
jQuery(document).ready(function($) {

    $(".itemqa_form_btn").click(function(e){
        e.preventDefault();
        window.open(this.href, "itemqa_form", "width=810,height=680,scrollbars=1");
        return false;
    });

    $(".itemqa_delete").click(function(){
        return confirm("정말 삭제 하시겠습니까?\n\n삭제후에는 되돌릴수 없습니다.");
    });

    $(".sit_qa_li_title").click(function(e){
        e.preventDefault();

        var $con = $(this).siblings(".sit_qa_con");

        gnucommerce.toggle_el_fn( $con );
    });

    $(".qa_page").click(function(){
        $("#itemqa").load($(this).attr("href"));
        return false;
    });
});
</script>
<!-- } 상품문의 목록 끝 -->