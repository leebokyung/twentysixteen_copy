<?php
if (!defined('ABSPATH')) exit; // 개별 페이지 접근 불가
?>
<div class="gp_skin_list">

<!-- 게시판 목록 시작 { -->
<div id="bo_gall" style="width:<?php echo $width; ?>">
    <div class="bo_gall_top">
        <!-- 게시판 카테고리 시작 { -->
        <?php if ($is_category) { ?>
        <nav id="bo_cate">
            <h2><?php echo $board['bo_subject'] ?> <?php _e('분류', GC_NAME); ?></h2>
            <ul id="bo_cate_ul">
                <?php echo $category_option ?>
            </ul>
        </nav>
        <?php } ?>
        <!-- } 게시판 카테고리 끝 -->
    
    	<!-- 게시판 태그 시작 -->
        <?php if( $is_use_tag ){ //태그 설정이 활성화 되어 있으면 ?>
        <ul class="list_head_tags">
        <?php
            foreach( $board_tag_lists as $s ){
                if( empty( $s ) ) continue;
                //카운트 숫자0인 경우 제외
                if( ! $s['count'] ) continue;
    
                $span_text = '<span class="tags-cnt"> '.$s['count'].'</span>'; //카운트 숫자
            ?>
            <li><?php echo gc_tag_class_link($s, $search_tag, 'tags-on', 'tags-txt', $span_text);?></li>
        <?php } // foreach end?>
        </ul>
        <?php } //end if ?>
    	<!-- 게시판 태그 끝 -->
    
        <!-- 게시판 페이지 정보 및 버튼 시작 { -->
        <div class="bo_fx">
            <?php if ($rss_href || $write_href) { ?>
            <ul class="btn_bo_user">
                <?php if ($rss_href) { ?><li><a href="<?php echo esc_url( $rss_href ); ?>" class="btn_b01 rss_open" target="_blank"><i class="fa fa-rss" aria-hidden="true"></i> RSS</a></li><?php } ?>
                <?php if ($admin_href) { ?><li><a href="<?php echo esc_url( $admin_href ); ?>" class="btn_admin" target="_blank"><i class="fa fa-cog" aria-hidden="true"></i> <?php _e('관리', GC_NAME);?></a></li><?php } ?>
                <?php if ($write_href) { ?><li><a href="<?php echo esc_url( $write_href ); ?>" class="btn_b02"><i class="fa fa-pencil" aria-hidden="true"></i> <?php _e('글쓰기', GC_NAME);?></a></li><?php } ?>
            </ul>
            <?php } ?>
        </div>
        <!-- } 게시판 페이지 정보 및 버튼 끝 -->
    </div>
    <form name="fboardlist" id="fboardlist" action="<?php echo $fboardlist_action_url; ?>" onsubmit="return fboardlist_submit(this);" method="post">
    <?php wp_nonce_field( 'gc_list', 'gc_nonce_field' ); ?>
    <input type="hidden" name="action" value="">
    <input type="hidden" name="board_page_id" value="<?php echo $post->ID?>" >
    <input type="hidden" name="bo_table" value="<?php echo esc_attr( $bo_table ); ?>">
    <input type="hidden" name="sfl" value="<?php echo esc_attr( $sfl ); ?>">
    <input type="hidden" name="stx" value="<?php echo esc_attr( $stx ); ?>">
    <input type="hidden" name="spt" value="<?php echo esc_attr( $spt ); ?>">
    <input type="hidden" name="sca" value="<?php echo esc_attr( $sca ); ?>">
    <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>">
    <input type="hidden" name="sw" value="">
    <?php if( $board['bo_use_tag'] ){ //게시판에서 태그기능을 사용한다면... ?>
    <input type="hidden" name="tag" value="<?php echo esc_attr( $tag ); ?>" >
    <?php } ?>

    <?php if ($is_checkbox) { ?>
    <div id="gall_allchk">
        <label for="chkall" class="sound_only"><?php _e('현재 페이지 게시물 전체', GC_NAME);  //현재 페이지 게시물 전체?></label>
        <input type="checkbox" id="chkall" onclick="if (this.checked) all_checked(true); else all_checked(false);">
    </div>
    <?php } ?>

    <div id="gall_ul" class="gall_row">
        <?php foreach( $list as $i=>$v ) {
            if( empty($v) ) continue;

            $classes = array();
            
            $classes[] = 'gall_li';
            $classes[] = 'col-gn-'.$bo_gallery_cols;

            if( $i && ($i % $bo_gallery_cols == 0) ){
                $classes[] = 'box_clear';
            }

            if( $wr_id == $v['wr_id'] ){
                $classes[] = 'gall_now';
            }
        ?>
        <div class="<?php echo implode(' ', $classes); ?>">
            <?php if ($is_checkbox) { ?>
            <label for="chk_wr_id_<?php echo $i ?>" class="sound_only"><?php echo $v['subject'] ?></label>
            <input type="checkbox" name="chk_wr_id[]" value="<?php echo $v['wr_id'] ?>" id="chk_wr_id_<?php echo $i ?>">
            <?php } ?>
            <span class="sound_only">
                <?php
                if ($wr_id == $v['wr_id'])
                    echo "<span class=\"bo_current\">".__('읽는중', GC_NAME)."</span>";
                else
                    echo $v['num'];
                 ?>
            </span>
            <ul class="gall_con">
                <li class="gall_href">
                    <a href="<?php echo $v['href'] ?>" class="thumbnail" >
                    <?php
                    if ($v['is_notice']) { // 공지사항  ?>
                        <strong><?php _e('공지', GC_NAME);?></strong>
                    <?php } else {
                        $thumb = gc_get_list_thumbnail($board['bo_table'], $v['wr_id'], $board['bo_gallery_width'], $board['bo_gallery_height']);

                        if($thumb['src']) {
                            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" class="gall_img" >';
                        } else {
                            $img_content = '<img src="'.$board_skin_url.'/img/no_img.jpg" class="gall_img" >';
                        }

                        echo $img_content;
                    }
                     ?>
                    </a>
                </li>
                <li class="gall_text_href">
                    <?php
                    // echo $v['icon_reply'];
                    if ($is_category && $v['ca_name']) {
                     ?>
                    <a href="<?php echo $v['ca_name_href'] ?>" class="bo_cate_link"><?php echo $v['ca_name'] ?></a>
                    <?php } ?>
                    <a href="<?php echo $v['href'] ?>">
                        <?php echo $v['subject'] ?>
                        <?php if ($v['comment_cnt']) { ?><span class="sound_only"><?php _e('댓글', GC_NAME);?></span><?php echo $v['comment_cnt']; ?><?php } ?>
                    </a>
                    <?php
                    // if ($v['link']['count']) { echo '['.$v['link']['count']}.']'; }
                    // if ($v['file']['count']) { echo '<'.$v['file']['count'].'>'; }

                    if (isset($v['icon_new'])) echo $v['icon_new'];
                    if (isset($v['icon_hot'])) echo $v['icon_hot'];
                    //if (isset($v['icon_file'])) echo $v['icon_file'];
                    //if (isset($v['icon_link'])) echo $v['icon_link'];
                    //if (isset($v['icon_secret'])) echo $v['icon_secret'];
                     ?>
                </li>
                
                <li class="text-info text-id">
                    <?php if($is_show_field['writer']){ // 게시판 설정 중 작성자 체크가 되어 있으면  ?>
                    <span><i class="fa fa-user" aria-hidden="true"></i> <?php echo $v['name'] ?></span>
                    <?php } ?>
                </li>
                <li class="text-info">
                    <?php if($is_show_field['wdate']){ // 게시판 설정 중 작성일 체크가 되어 있으면 ?>
                    <span class="text-date"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo $v['datetime2'] ?></span>
                    <?php } ?>
                    <?php if($is_show_field['visit']){ // 게시판 설정 중 조회 체크가 되어 있으면 ?>
                    <span class="text-hit"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo $v['wr_hit'] ?></span>
                    <?php } ?>
                </li>
                <li class="text-info">
                    <?php if ($is_good) { ?>
                    <span class="gall_subject text-good"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i><?php //추천?></span>
                    <strong><?php echo $v['wr_good'] ?></strong>
                    <?php } ?>
                    <?php if ($is_nogood) { ?>
                    <span class="gall_subject text-nogood"><i class="fa fa-thumbs-o-down" aria-hidden="true"></i><?php //비추천?></span>
                    <strong><?php echo $v['wr_nogood'] ?></strong>
                    <?php } ?>
                </li>
            </ul>
        </div>
        <?php } ?>
        <?php if (count($list) == 0) { echo "<li class=\"empty_list\">".__('내용이 없습니다.', GC_NAME)."</li>"; } ?>
    </div>

    <?php if ($list_href || $is_checkbox || $write_href) { ?>
    <div class="bo_fx">
        <?php if ($is_checkbox) { ?>
        <ul class="btn_bo_adm">
            <li><button type="submit" name="btn_submit" value="<?php _e('선택삭제', GC_NAME); //선택삭제 ?>" onclick="document.pressed=this.value"><i class="fa fa-eraser" aria-hidden="true"></i> <?php _e('선택삭제', GC_NAME); //선택삭제 ?></button></li>
            <li><button type="submit" name="btn_submit" value="<?php _e('선택복사', GC_NAME); //선택복사 ?>" onclick="document.pressed=this.value"><i class="fa fa-clipboard" aria-hidden="true"></i> <?php _e('선택복사', GC_NAME); //선택복사 ?></button></li>
            <li><button type="submit" name="btn_submit" value="<?php _e('선택이동', GC_NAME); //선택이동 ?>" onclick="document.pressed=this.value"><i class="fa fa-repeat" aria-hidden="true"></i> <?php _e('선택이동', GC_NAME); //선택이동 ?></button></li>
        </ul>
        <?php } ?>

        <?php if ($list_href || $write_href) { ?>
        <ul class="btn_bo_user">
            <?php if ($list_href) { ?><li><a href="<?php echo $list_href ?>" class="btn_b01"><i class="fa fa-list-ul" aria-hidden="true"></i> <?php _e('목록', GC_NAME); //목록 ?></a></li><?php } ?>
            <?php if ($write_href) { ?><li><a href="<?php echo $write_href ?>" class="btn_b02"><i class="fa fa-pencil" aria-hidden="true"></i> <?php _e('글쓰기', GC_NAME); //글쓰기 ?></a></li><?php } ?>
        </ul>
        <?php } ?>
    </div>
    <?php } ?>
    </form>
</div>

<?php if($is_checkbox) { ?>
<noscript>
<p><?php _e('자바스크립트를 사용하지 않는 경우', GC_NAME); //자바스크립트를 사용하지 않는 경우?><br><?php _e('별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.', GC_NAME); //별도의 확인 절차 없이 바로 선택삭제 처리하므로 주의하시기 바랍니다.?></p></p>
</noscript>
<?php } ?>

<!-- 페이지 -->
<?php echo $write_pages;  ?>

<!-- 게시판 검색 시작 { -->
<fieldset id="bo_sch">
    <legend><?php _e('게시물 검색', GC_NAME);   //게시물 검색 ?></legend>

    <form name="fsearch" method="get" class="gc_list_search">
    <?php foreach( $search_form_var as $key => $v ){ ?>
        <input type="hidden" name="<?php echo $key ?>" value="<?php echo esc_attr( $v ); ?>">
    <?php } ?>
    <input type="hidden" name="sca" value="<?php echo esc_attr( $sca ); ?>">
    <input type="hidden" name="sop" value="and">
    <label for="sfl" class="sound_only"><?php _e('검색대상', GC_NAME); //검색대상?></label>
    <select name="sfl" id="sfl">
        <option value="wr_subject"<?php echo selected($sfl, 'wr_subject'); ?>><?php _e('제목', GC_NAME);?></option>
        <option value="wr_content"<?php echo selected($sfl, 'wr_content'); ?>><?php _e('내용', GC_NAME);?></option>
        <option value="wr_subject||wr_content"<?php echo selected($sfl, 'wr_subject||wr_content'); ?>><?php _e('제목+내용', GC_NAME);?></option>
        <option value="user_id,1"<?php echo selected($sfl, 'user_id,1'); ?>><?php _e('회원아이디', GC_NAME);?></option>
        <option value="user_display_name,1"<?php echo selected($sfl, 'user_display_name,1'); ?>><?php _e('작성자', GC_NAME);?></option>
    </select>
    <label for="stx" class="sound_only"><?php _e('검색단어', GC_NAME);?><strong class="sound_only"> <?php _e('필수', GC_NAME);?></strong></label>
    <input type="text" name="stx" value="<?php echo stripslashes($stx) ?>" required id="stx" class="frm_input required" size="15" maxlength="15">
    <input type="submit" value="<?php _e('검색', GC_NAME);?>" class="btn_submit">
    </form>
</fieldset>
<!-- } 게시판 검색 끝 -->

<?php if ($is_checkbox) { ?>
<script>
function all_checked(sw) {
    var f = document.fboardlist;

    for (var i=0; i < f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]")
            f.elements[i].checked = sw;
    }
}

function fboardlist_submit(f) {
    var chk_count = 0;

    for (var i=0; i < f.length; i++) {
        if (f.elements[i].name == "chk_wr_id[]" && f.elements[i].checked)
            chk_count++;
    }

    if (!chk_count) {
        alert(document.pressed + "<?php _e(' - 할 게시물을 하나 이상 선택하세요.', GC_NAME);?>");
        return false;
    }

    if(document.pressed == "<?php _e('선택복사', GC_NAME);?>") {
        select_copy("copy");
        return;
    }

    if(document.pressed == "<?php _e('선택이동', GC_NAME);?>") {
        select_copy("move");
        return;
    }

    if(document.pressed == "<?php _e('선택삭제', GC_NAME);?>") {
        if (!confirm("<?php _e('선택한 자료를 정말 삭제하시겠습니까?', GC_NAME);?>\n\n<?php _e('한번 삭제한 자료는 복구할 수 없습니다', GC_NAME);?>\n\n<?php _e('답변글이 있는 게시글을 선택하신 경우,', GC_NAME)?>\n<?php _e('답변글도 선택하셔야 게시글이 삭제됩니다.', GC_NAME)?>"))
            return false;

        f.removeAttribute("target");
        f.action.value = "delete_all";
    }

    return true;
}

// 선택한 게시물 복사 및 이동
function select_copy(sw) {
    var f = document.fboardlist;

    if (sw == "copy")
        str = "<?php _e('복사', GC_NAME);?>";
    else
        str = "<?php _e('이동', GC_NAME);?>";

    var sub_win = window.open("", "move", "left=50, top=50, width=500, height=550, scrollbars=1");

    f.sw.value = sw;
    f.target = f.action.value = "move";
    f.action = "<?php echo $new_move_url; ?>";
    f.submit();
}
</script>
<?php } ?>
<!-- } 게시판 목록 끝 -->

</div>
