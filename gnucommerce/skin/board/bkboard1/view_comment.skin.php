<?php
if (!defined('ABSPATH')) exit; // 개별 페이지 접근 불가

$cm_content='';
?>

<script>
// 글자수 제한
var char_min = parseInt(<?php echo $comment_min ?>); // 최소
var char_max = parseInt(<?php echo $comment_max ?>); // 최대
</script>

<!-- 댓글 시작 { -->
<section id="bo_vc">
    <h2><?php _e('댓글목록', GC_NAME); //댓글목록?></h2>
    <?php
    $cmt_amt = count($list);
    for ($i=0; $i<$cmt_amt; $i++) {
        $comment_id = $list[$i]['cm_id'];
        $cmt_depth = (int) $list[$i]['cm_parent'] > 0 ? 1 : 0; // 댓글단계
        $cmt_depth = $cmt_depth * 20;
        $comment = $list[$i]['content'];
        $comment = preg_replace("/\[\<a\s.*href\=\"(http|https|ftp|mms)\:\/\/([^[:space:]]+)\.(mp3|wma|wmv|asf|asx|mpg|mpeg)\".*\<\/a\>\]/i", "<script>doc_write(obj_movie('$1://$2.$3'));</script>", $comment);
        $cmt_sv = $cmt_amt - $i + 1; // 댓글 헤더 z-index 재설정 ie8 이하 사이드뷰 겹침 문제 해결
     ?>

    <article id="c_<?php echo $comment_id ?>" <?php if ($cmt_depth) { ?>style="margin-left:<?php echo $cmt_depth ?>px;border-top-color:#e0e0e0"<?php } ?>>
        <header style="z-index:<?php echo $cmt_sv; ?>">
            <h1><?php echo gc_get_text($list[$i]['user_display_name']); ?><?php _e('님의 댓글', GC_NAME);   //님의 댓글?></h1>
            <i class="fa fa-user" aria-hidden="true"></i> <?php echo $list[$i]['name'] ?>
            <?php if ($cmt_depth) { ?><img src="<?php echo $board_skin_url ?>/img/icon_reply.gif" class="icon_reply" alt="댓글의 댓글"><?php } ?>
            <!-- <?php if ($is_ip_view) { ?>
            아이피
            <span class="bo_vc_hdinfo"><?php echo $list[$i]['ip']; ?></span>
            <span class="icon-comment-date icon-text">작성자</span><?php } ?><span class="bo_vc_hdinfo"><time datetime="<?php echo date('Y-m-d\TH:i:s+09:00', strtotime($list[$i]['datetime'])) ?>"><?php echo $list[$i]['datetime'] ?></time></span> -->
        </header>

        <!-- 댓글 출력 -->
        <p>
            <?php if (strstr($list[$i]['cm_option'], "secret")) { ?><img src="<?php echo $board_skin_url; ?>/img/icon_secret.gif" alt="<?php _e('비밀글', GC_NAME); ?>"><?php } ?>
            <?php echo $comment ?>
        </p>

        <span id="edit_<?php echo $comment_id ?>"></span><!-- 수정 -->
        <span id="reply_<?php echo $comment_id ?>"></span><!-- 답변 -->

        <input type="hidden" value="<?php echo strstr($list[$i]['cm_option'],"secret") ?>" id="secret_comment_<?php echo $comment_id ?>">
        <textarea id="save_comment_<?php echo $comment_id ?>" style="display:none"><?php echo gc_get_text($list[$i]['content1'], 0) ?></textarea>

        <?php if($list[$i]['is_reply'] || $list[$i]['is_edit'] || $list[$i]['is_del']) {
            $query_string = add_query_arg(array());

            if($w == 'cu') {
                $cmt = gc_get_write($gc['comment_table'], $cm_id, 'cm_id');
                $cm_content = ($is_admin || $cmt['user_id'] == $member['user_id']) ? $cmt['cm_content'] : __('자신의 글이 아니므로 수정할 수 없습니다.', GC_NAME);
            }

            $c_reply_href = add_query_arg( array('c_id'=>$comment_id, 'w'=>'c'),$query_string ).'#bo_vc_w';
            $c_edit_href = add_query_arg( array('c_id'=>$comment_id, 'w'=>'cu'),$query_string ).'#bo_vc_w';
         ?>
        <footer>
            <ul class="bo_vc_act">
                <?php if ($list[$i]['is_reply']) { ?><li><a href="<?php echo esc_url( $c_reply_href ); ?>" onclick="gc_view_cm.comment_box('<?php echo $comment_id ?>', 'c'); return false;" class="no-ajaxy"><?php _e('답변', GC_NAME); ?></a></li><?php } ?>
                <?php if ($list[$i]['is_edit']) { ?><li><a href="<?php echo esc_url( $c_edit_href ); ?>" onclick="gc_view_cm.comment_box('<?php echo $comment_id ?>', 'cu'); return false;" class="no-ajaxy"><?php _e('수정', GC_NAME); ?></a></li><?php } ?>
                <?php if ($list[$i]['is_del'])  { ?><li><a href="<?php echo esc_url( $list[$i]['del_link'] ); ?>" onclick="return gc_view_cm.comment_delete();" class="no-ajaxy"><?php _e('삭제', GC_NAME); ?></a></li><?php } ?>
            </ul>
        </footer>
        <?php } ?>
    </article>
    <?php } ?>
    <?php if ($i == 0) { //댓글이 없다면 ?><p id="bo_vc_empty"><?php _e('등록된 댓글이 없습니다.', GC_NAME);    //등록된 댓글이 없습니다.?></p><?php } ?>

</section>
<!-- } 댓글 끝 -->

<?php if ($is_comment_write) {
    if($w == '')
        $w = 'c';
?>
<!-- 댓글 쓰기 시작 { -->
<aside id="bo_vc_w">
    <h2><?php _e('댓글 쓰기', GC_NAME); ?></h2>
    <form name="fviewcomment" id="fviewcomment" action="<?php $comment_action_url; ?>" onsubmit="return gc_view_cm.fviewcomment_submit(this);" method="post" autocomplete="off">
    <?php wp_nonce_field( 'gc_comment_write', 'gc_nonce_field' ); ?>
    <input type="hidden" name="gc_rq" value="gc">
    <input type="hidden" name="action" value="write_comment_update">
    <input type="hidden" name="w" value="<?php echo esc_attr( $w ); ?>" id="w">
    <input type="hidden" name="bo_table" value="<?php echo esc_attr( $bo_table ); ?>">
    <input type="hidden" name="cm_id" value="<?php echo esc_attr( intval($cm_id) ); ?>" id="cm_id">
    <input type="hidden" name="sca" value="<?php echo esc_attr( $sca ); ?>">
    <input type="hidden" name="sfl" value="<?php echo esc_attr( $sfl ); ?>">
    <input type="hidden" name="stx" value="<?php echo esc_attr( $stx ); ?>">
    <input type="hidden" name="spt" value="<?php echo esc_attr( $spt ); ?>">
    <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>">
    <input type="hidden" name="page_id" value="<?php echo get_the_ID(); ?>">
    <input type="hidden" name="is_good" value="">

    <div class="tbl_frm01 tbl_wrap">
        <table>
        <tbody>
        <?php if ($is_guest) { ?>
        <tr>
            <th scope="row"><label for="user_name"><?php _e('이름', GC_NAME);?><strong class="sound_only"> <?php _e('필수', GC_NAME);?></strong></label></th>
            <td><input type="text" name="user_name" value="<?php echo esc_attr( gc_get_cookie("ck_sns_name") ); ?>" id="user_name" required class="frm_input required" size="10" maxLength="20"></td>
        </tr>
        <tr>
            <th scope="row"><label for="user_pass"><?php _e('비밀번호', GC_NAME);?><strong class="sound_only"> <?php _e('필수', GC_NAME);?></strong></label></th>
            <td><input type="password" name="user_pass" id="user_pass" required class="frm_input required" size="10" maxLength="20"></td>
        </tr>
        <?php } ?>
        <tr>
            <th scope="row"><label for="cm_secret"><?php _e('비밀글사용', GC_NAME);    //비밀글사용?></label></th>
            <td><input type="checkbox" name="cm_secret" value="secret" id="cm_secret"></td>
        </tr>
        <?php if ($is_guest) { ?>
        <tr>
            <th scope="row"><?php _e('자동등록방지', GC_NAME);    //자동등록방지?></th>
            <td><?php echo $captcha_html; ?></td>
        </tr>
        <?php } ?>
        <tr>
            <th scope="row"><?php _e('내용', GC_NAME);    //내용?></th>
            <td>
                <?php if ($comment_min || $comment_max) { ?><strong id="char_cnt"><span id="char_count"></span> <?php _e('길이', GC_NAME);    //글자?></strong><?php } ?>
                <textarea id="cm_content" name="cm_content" maxlength="10000" required class="required" title="내용"
                <?php if ($comment_min || $comment_max) { ?>onkeyup="gc_check_byte('cm_content', 'char_count');"<?php } ?>><?php echo $cm_content;  ?></textarea>
                <?php if ($comment_min || $comment_max) { ?><script> gc_check_byte('cm_content', 'char_count'); </script><?php } ?>
            </td>
        </tr>
        </tbody>
        </table>
    </div>

    <div class="btn_confirm">
        <input type="submit" id="btn_submit" class="btn_submit" value="<?php _e('댓글등록', GC_NAME);    //댓글등록?>">
    </div>

    </form>
</aside>

<?php
$gcboard->board_var['comment']=array(
'is_guest'=>$is_guest,
);
add_action('wp_footer', 'gc_view_comment_script', 38);

function gc_view_comment_script(){
    global $gcboard;
    
    extract($gcboard->board_var['comment']);
    ?>
    <script>
    var gc_view_cm = {
        save_before : '',
        save_html : document.getElementById('bo_vc_w').innerHTML
    };

    (function($){
        //comment textarea
        $("form[name=fviewcomment]").on("textarea#cm_content[maxlength]", "keyup change", function() {
            var str = $(this).val()
            var mx = parseInt($(this).attr("maxlength"))
            if (str.length > mx) {
                $(this).val(str.substr(0, mx));
                return false;
            }
        });

        gc_view_cm.good_and_write = function()
        {
            var f = document.fviewcomment;
            if (this.fviewcomment_submit(f)) {
                f.is_good.value = 1;
                f.submit();
            } else {
                f.is_good.value = 0;
            }
        }

        gc_view_cm.fviewcomment_submit = function(f)
        {
            var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자

            f.is_good.value = 0;

            var subject = "";
            var content = "";

            $.ajax({
                url: gcboard.ajax_url,
                type: "POST",
                data: {
                    "action": "gc_bss_filter",
                    "subject": "",
                    "content": f.cm_content.value
                },
                dataType: "json",
                async: false,
                cache: false,
                success: function(data, textStatus) {
                    subject = data.subject;
                    content = data.content;
                }
            });

            if (content) {
                alert("<?php _e('내용에 금지 단어가 포함되어 있습니다 : ', GC_NAME);?>'"+content);
                f.cm_content.focus();
                return false;
            }

            // 양쪽 공백 없애기
            var pattern = /(^\s*)|(\s*$)/g; // \s 공백 문자
            document.getElementById('cm_content').value = document.getElementById('cm_content').value.replace(pattern, "");
            if (char_min > 0 || char_max > 0)
            {
                gc_check_byte('cm_content', 'char_count');
                var cnt = parseInt(document.getElementById('char_count').innerHTML);
                if (char_min > 0 && char_min > cnt)
                {
                    alert( gcboard.sprintf("<?php _e('%d 글자 이상 쓰셔야 합니다.', GC_NAME);?>", char_min) );     //char_min+"글자 이상 쓰셔야 합니다."
                    return false;
                } else if (char_max > 0 && char_max < cnt)
                {
                    alert(gcboard.sprintf("<?php _e('%d 글자 이하로 쓰셔야 합니다.', GC_NAME);?>", char_max) );   //char_max+"글자 이하로 쓰셔야 합니다."
                    return false;
                }
            }
            else if (!document.getElementById('cm_content').value)
            {
                alert("<?php _e('댓글을 입력하여 주십시오.', GC_NAME);?>");     //댓글을 입력하여 주십시오.
                return false;
            }

            if (typeof(f.user_name) != 'undefined')
            {
                f.user_name.value = f.user_name.value.replace(pattern, "");
                if (f.user_name.value == '')
                {
                    alert("<?php _e('이름을 입력하세요.', GC_NAME);?>");   //이름을 입력하세요.
                    f.user_name.focus();
                    return false;
                }
            }

            if (typeof(f.user_pass) != 'undefined')
            {
                f.user_pass.value = f.user_pass.value.replace(pattern, "");
                if (f.user_pass.value == '')
                {
                    alert("<?php _e('비밀번호를 입력하세요.', GC_NAME);?>");   //비밀번호를 입력하세요.
                    f.user_pass.focus();
                    return false;
                }
            }

            <?php if($is_guest) echo gc_chk_captcha_js();  ?>

            document.getElementById("btn_submit").disabled = "disabled";

            return true;
        }

        gc_view_cm.comment_box = function(comment_id, work)
        {
            var el_id,
                othis = this,
                form_el = 'fviewcomment',
                respond = document.getElementById(form_el);

            // 댓글 아이디가 넘어오면 답변, 수정
            if (comment_id)
            {
                if (work == 'c')
                    el_id = 'reply_' + comment_id;
                else
                    el_id = 'edit_' + comment_id;
            }
            else
                el_id = 'bo_vc_w';

            var comm = document.getElementById(el_id);

            if (othis.save_before != el_id)
            {

                if (othis.save_before)
                {
                    document.getElementById(othis.save_before).style.display = 'none';
                }

                comm.style.display = '';

                comm.appendChild(respond);

                // 댓글 수정
                if (work == 'cu')
                {
                    document.getElementById('cm_content').value = document.getElementById('save_comment_' + comment_id).value;
                    if (typeof char_count != 'undefined')
                        gc_check_byte('cm_content', 'char_count');
                    if (document.getElementById('secret_comment_'+comment_id).value)
                        document.getElementById('cm_secret').checked = true;
                    else
                        document.getElementById('cm_secret').checked = false;
                }

                document.getElementById('cm_id').value = comment_id;
                document.getElementById('w').value = work;

                othis.save_before = el_id;
            }
        }

        gc_view_cm.comment_delete = function()
        {
            return confirm("<?php _e('댓글을 삭제하시겠습니까?', GC_NAME);?>");  //댓글을 삭제하시겠습니까?
        }

    })(jQuery);
    </script>
    <?php
}   //end function gc_view_comment_script
?>
<?php } ?>
<!-- } 댓글 쓰기 끝 -->