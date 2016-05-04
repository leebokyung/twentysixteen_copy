<?php
if (!defined("ABSPATH")) exit; // 개별 페이지 접근 불가

wp_enqueue_script( $bo_table.'-view-skin-js', $board_skin_url.'/js/view.skin.js' );
?>

<!-- 게시물 읽기 시작 { -->
<div id="bo_v_table"><?php echo $board['bo_subject']; ?></div>

<article id="bo_v" style="width:<?php echo $width; ?>">
    <header>
        <h1 id="bo_v_title">
            <?php
            if ($category_name) echo $view['ca_name'].' | '; // 분류 출력 끝
            echo gc_cut_str(gc_get_text($view['wr_subject']), 70); // 글제목 출력
            ?>
        </h1>
    </header>

    <section id="bo_v_atc">
        <h2 id="bo_v_atc_title"><?php _e('본문', GC_NAME); //본문?></h2>

        <?php

        // 파일 출력
        $v_img_count = count($view['file']);
        if($v_img_count) {
            echo "<div id=\"bo_v_img\">\n";
            
            foreach((array) $view['file'] as $view_file ){
                if( !isset($view_file['view']) ) continue;
                //echo $view_file['view'];
                echo gc_get_view_thumbnail($view_file['view'], $board['bo_image_width']);
            }

            echo "</div>\n";
        }

         ?>

        <!-- 본문 내용 시작 { -->
        <div id="bo_v_con"><?php echo gc_get_view_thumbnail($view['content'],  $board['bo_image_width']); ?></div>
        <?php //echo $view['rich_content']; // {이미지:0} 과 같은 코드를 사용할 경우 ?>
        <!-- } 본문 내용 끝 -->
        
        <!-- 게시물 상단 버튼 시작 { -->
        <div id="bo_v_top">
            <?php
            ob_start();
             ?>
            <?php if ($prev_href || $next_href) { ?>
            <ul class="bo_v_nb">
                <?php if ($prev_href) { ?><li><a href="<?php echo esc_url( $prev_href ); ?>" class="btn_b01"><span class="icon-btn-next icon-text">이전</span><?php _e('이전', GC_NAME); //이전?></a></li><?php } ?>
                <?php if ($next_href) { ?><li><a href="<?php echo esc_url( $next_href ); ?>" class="btn_b01"><?php _e('다음', GC_NAME); //다음?><span class="icon-btn-prev icon-text">다음</span></a></li><?php } ?>
            </ul>
            <?php } ?>
    
            <ul class="bo_v_com">
                <?php if ($update_href) { ?><li><a href="<?php echo esc_url( $update_href ); ?>" class="btn_b01"><span class="icon-btn-modify icon-text">수정</span><?php _e('수정', GC_NAME); //수정?></a></li><?php } ?>
                <?php if ($delete_href) { ?><li><a href="<?php echo esc_url( $delete_href ); ?>" class="btn_b01" onclick="gcboard.del(this.href); return false;"><span class="icon-btn-delete icon-text">삭제</span><?php _e('삭제', GC_NAME); //삭제?></a></li><?php } ?>
                <?php if ($copy_href) { ?><li><a href="<?php echo esc_url( $copy_href ); ?>" class="btn_admin no-ajaxy" onclick="board_move(this.href); return false;"><span class="icon-btn-copy icon-text">복사</span><?php _e('복사', GC_NAME); //복사?></a></li><?php } ?>
                <?php if ($move_href) { ?><li><a href="<?php echo esc_url( $move_href ); ?>" class="btn_admin no-ajaxy" onclick="board_move(this.href); return false;"><span class="icon-btn-move icon-text">이동</span><?php _e('이동', GC_NAME); //이동?></a></li><?php } ?>
                <?php if ($search_href) { ?><li><a href="<?php echo esc_url( $search_href ); ?>" class="btn_b01"><?php _e('검색', GC_NAME); //검색?></a></li><?php } ?>
                <li><a href="<?php echo $list_href ?>" class="btn_b01"><span class="icon-btn-list icon-text">목록</span><?php _e('목록', GC_NAME); //목록?></a></li>
                <?php if ($reply_href) { ?><li><a href="<?php echo esc_url( $reply_href ); ?>" class="btn_b01"><span class="icon-btn-comment icon-text">답변</span><?php _e('답변', GC_NAME); //답변?></a></li><?php } ?>
                <?php if ($write_href) { ?><li><a href="<?php echo esc_url( $write_href ); ?>" class="btn_b02"><span class="icon-btn-write icon-text">글쓰기</span><?php _e('글쓰기', GC_NAME); //글쓰기?></a></li><?php } ?>
            </ul>
            <?php
            $link_buttons = ob_get_contents();
            ob_end_flush();
             ?>
        </div>
        <!-- } 게시물 상단 버튼 끝 -->
    
        <?php if ($is_signature) { ?><p><?php echo $signature ?></p><?php } ?>

        <!-- 스크랩 추천 비추천 시작 { -->
        <?php if ($scrap_href || $good_href || $nogood_href) { ?>
        <div id="bo_v_act">
            <?php if ($scrap_href) { ?><a href="<?php echo esc_url( $scrap_href ); ?>" target="_blank" class="btn_b01" onclick="gcboard.win_scrap(this.href); return false;"><span class="icon-btn-scrap icon-text">스크랩</span><?php _e('스크랩', GC_NAME); //스크랩?></a><?php } ?>
            <?php if ($good_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo esc_url( $good_href ) ?>" id="good_button" class="btn_b03" target="_blank">
                    <span class="icon-btn-like icon-text"><?php _e('추천', GC_NAME); //추천?></span>
                    <strong><?php echo number_format($view['wr_good']) ?></strong></a>
                <b id="bo_v_act_good"></b>
            </span>
            <?php } ?>
            <?php if ($nogood_href) { ?>
            <span class="bo_v_act_gng">
                <a href="<?php echo esc_url( $nogood_href ) ?>" id="nogood_button" class="btn_b03" target="_blank">
                    <span class="icon-btn-hate icon-text"><?php _e('비추천', GC_NAME); //비추천?></span>
                    <strong><?php echo number_format($view['wr_nogood']) ?></strong></a>
                <b id="bo_v_act_nogood"></b>
            </span>
            <?php } ?>
        </div>
        <?php } else {
            if($board['bo_use_good'] || $board['bo_use_nogood']) {
        ?>
        <div id="bo_v_act">
            <p class="bo_v_act_view">
                <?php if($board['bo_use_good']) { ?>
                <span class="icon-btn-like icon-text"><?php _e('추천', GC_NAME); //추천?></span>
                <?php } ?>
                <strong><?php echo number_format($view['wr_good']) ?></strong>
            </p>
            <p class="bo_v_act_view">
                <?php if($board['bo_use_nogood']) { ?>
                <span class="icon-btn-hate icon-text"><?php _e('비추천', GC_NAME); //비추천?></span>
                <?php } ?>
                <strong><?php echo number_format($view['wr_nogood']) ?></strong>
            </p>
        </div>
        <?php
            }
        }
        ?>
        <!-- } 스크랩 추천 비추천 끝 -->

        <section id="bo_v_info">
            <h2><?php _e('페이지정보', GC_NAME);?></h2>
            <span class="icon-view-name icon-text">작성자</span><strong><?php echo $view['name'] ?></strong> <!-- <?php _e('작성자', GC_NAME);?><?php if ($is_ip_view) { echo "&nbsp;($ip)"; } ?> -->
            <span class="icon-view-date icon-text">날짜</span><strong><?php echo date("y-m-d H:i", strtotime($view['wr_datetime'])) ?></strong> <!-- <span class="sound_only"><?php _e('날짜', GC_NAME);?></span> -->
            <span class="icon-view-hits icon-text">조회수</span><strong>조회 <?php echo number_format($view['wr_hit']) ?></strong> <!-- <?php _e('조회수', GC_NAME);?> -->
            <span class="icon-view-comment icon-text">댓글</span><strong>댓글 <?php echo number_format($view['wr_comment']) ?></strong> <!-- <?php _e('댓글', GC_NAME);?> -->
            <button type="button" class="bo_v_add">첨부파일+링크</button>
            <!-- 첨부파일 시작 { -->
            <div id="bo_v_file" class="pvji_open  pv_ji_close">
                <h2><?php _e('첨부파일', GC_NAME);?></h2>
                <ul>
                    <?php
                    // 가변 파일
                    for ($i=0; $i<count($view['file']); $i++) {
                        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view']) {
                     ?>
                        <li>
                            <a href="<?php echo esc_url( $view['file'][$i]['href'] ); ?>" class="view_file_download no-ajaxy">
                                <span class="icon-view-file icon-text">첨부파일</span><!-- <img src="<?php echo $board_skin_url ?>/img/icon_file.gif" alt="첨부"> -->
                                <strong><?php echo $view['file'][$i]['source'] ?></strong>
                                <?php echo $view['file'][$i]['content'] ?> (<?php echo $view['file'][$i]['size'] ?>)
                            </a>
                            <span class="bo_v_file_cnt"><?php echo $view['file'][$i]['download'] ?><?php _e('회', GC_NAME);    //회 ?></span>
                            <span class="bo_v_file_date"><?php echo $view['file'][$i]['datetime'] ?></span>
                        </li>
                    <?php
                        }
                    }
                     ?>   
                </ul>
       
                <?php
                $cnt = 0;
                if ($view['file']['count']) {
                    $cnt = 0;
                    for ($i=0; $i<count($view['file']); $i++) {
                        if (isset($view['file'][$i]['source']) && $view['file'][$i]['source'] && !$view['file'][$i]['view'])
                            $cnt++;
                    }
                }
                ?>    
        
                <?php if($cnt) { ?>
                
                <!-- } 첨부파일 끝 -->
                <?php } ?>
            
                <?php
                if (implode('', $view['link'])) {
                 ?>
                 <!-- 관련링크 시작 { -->
                <div id="bo_v_link">
                    <h2><?php _e('관련링크', GC_NAME); //관련링크?></h2>
                    <ul>
                    <?php
                    // 링크
                    $cnt = 0;
                    for ($i=1; $i<=count($view['link']); $i++) {
                        if ($view['link'][$i]) {
                            $cnt++;
                            $link = gc_cut_str($view['link'][$i], 70);
                     ?>
                        <li>
                            <a href="<?php echo esc_url( $view['link_href'][$i] ); ?>" target="_blank">
                                <span class="icon-view-link icon-text">관련링크</span>
                                <!-- <img src="<?php echo $board_skin_url ?>/img/icon_link.gif" alt="<?php _e('관련링크', GC_NAME); //관련링크?>"> -->
                                <strong><?php echo $link ?></strong>
                            </a>
                            <span class="bo_v_link_cnt"><?php echo $view['link_hit'][$i] ?><?php _e('회', GC_NAME); //회?></span>
                        </li>
                    <?php
                        }
                    }
                     ?>
                    </ul>
                </div>
                <!-- } 관련링크 끝 -->
            </div>
        </section>
    
    <script>
    jQuery(document).ready(function($){
        $('button', '#bo_v_info').toggle(function(){
            $(this).addClass('pv_ji_cl');
            $(this).next('div.pvji_open').removeClass('pv_ji_close');
        },function(){
            $(this).removeClass('pv_ji_cl');
            $(this).next('div.pvji_open').addClass('pv_ji_close');
        });
    });
    </script>
    
    <?php } ?>
    </section>

    <?php
    if( count($tag_array) ){   //태그가 존재한다면
        echo "<div class=\"bo-tags bo-tags-view\">";
        foreach( $tag_array as $term ){
            if( empty($term) ) continue;
    ?>
        <a href="<?php echo $term['href']?>"><?php echo $term['name'];?></a>
    <?php
        }
        echo "</div>";
    }
    ?>

    <?php
    // 코멘트 입출력
    include_once(GC_BBS_PATH.'/view_comment.php');
    ?>

    <!-- 링크 버튼 시작 { -->
    <!-- <div id="bo_v_bot">
        <?php echo $link_buttons ?>
    </div> -->
    <!-- } 링크 버튼 끝 -->

</article>
<!-- } 게시판 읽기 끝 -->

<?php
add_action('wp_footer', 'gc_view_js_script', 38);
$gcboard->board_var['view']=array(
'wr_id'=>$wr_id,
'bo_table'=>$bo_table,
'board'=>$board,
);

function gc_view_js_script(){
    global $gcboard;

    extract($gcboard->board_var['view']);
    ?>
    <script>
    <?php if ($board['bo_download_point'] < 0) { ?>
    function view_file_download(){
        var othis = this;
        (function($){
            if(!gcboard.is_member) {
                alert("<?php _e('다운로드 권한이 없습니다.', GC_NAME);?>\n<?php _e('회원이시라면 로그인 후 이용해 보십시오.', GC_NAME);?>");
                return false;
            }

            var msg = "<?php echo sprintf(__('파일을 다운로드 하시면 포인트가 차감(%s점)됩니다.) points.', GC_NAME), number_format($board['bo_download_point']));?>\n\n<?php _e('포인트는 게시물당 한번만 차감되며 다음에 다시 다운로드 하셔도 중복하여 차감하지 않습니다.', GC_NAME);?>\n\n<?php _e('그래도 다운로드 하시겠습니까?', GC_NAME);?>";

            if(confirm(msg)) {
                var href = $(othis).attr("href")+"&js=on";
                $(this).attr("href", href);
                window.open( href );
            }

            return false;
        })(jQuery);
    }
    <?php } ?>

    function board_move(href)
    {
        window.open(href, "boardmove", "left=50, top=50, width=500, height=550, scrollbars=1");
    }

    function excute_good($el, $tx)
    {
        (function($){
            $.post(
                $el.attr("href"),
                { action: "good", use_ajax : 1, wr_id : <?php echo $wr_id ?>, 'bo_table' : "<?php echo $bo_table ?>" },
                function(data) {
                    if(data.error) {
                        alert(data.error);
                        return false;
                    }

                    if(data.count) {
                        $el.find("strong").text(gcboard.number_format(String(data.count)));
                        if($tx.attr("id").search("nogood") > -1) {
                            $tx.text("<?php _e('이 글을 비추천하셨습니다.', GC_NAME);?>");
                            $tx.fadeIn(200).delay(2500).fadeOut(200);
                        } else {
                            $tx.text("<?php _e('이 글을 추천하셨습니다.', GC_NAME);?>");
                            $tx.fadeIn(200).delay(2500).fadeOut(200);
                        }
                    }
                }, "json"
            );
        })(jQuery);
    }
    </script>
<?php } //end function gc_view_js_script ?>
<!-- } 게시글 읽기 끝 -->