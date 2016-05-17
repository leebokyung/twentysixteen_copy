<?php
if( !defined('GC_NAME') ) exit;

//sms 재입고
wp_enqueue_script('jquery-ui-dialog');
wp_enqueue_style("wp-jquery-ui-dialog");
wp_enqueue_script( 'gcboard-common-js', GC_DIR_URL.'js/common.js', '', GC_VERSION );
?>
<div id="gc_itemstock_dialog_box">
<form id="itemstock_form" method="post">
<input type="hidden" name="it_id" value="<?php echo esc_attr($it['it_id']); ?>">
<?php wp_nonce_field( 'itemstock-sms-dialog' ); ?>
    <div class="tbl_frm01 tbl_wrap">
        <table>
         <colgroup>
            <col class="grid_4">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="row"><?php _e('상품', GC_NAME); ?></th>
            <td><?php echo $it['it_name']; ?></td>
        </tr>
        <tr>
            <th scope="row"><label for="ss_hp"><?php _e('휴대폰번호', GC_NAME); ?><strong class="sound_only"> <?php _e('필수', GC_NAME); ?></strong></label></th>
            <td><input type="text" name="ss_hp" value="<?php echo get_the_author_meta( 'mb_hp', get_current_user_id() ); ?>" id="ss_hp" required class="required frm_input"></td>
        </tr>
        <tr>
            <th scope="row"><strong><?php _e('개인정보처리방침안내', GC_NAME); ?></strong></th>
            <td><textarea readonly><?php echo gc_get_text(get_option('gc_cf_privacy')) ?></textarea></td>
        </tr>
        </tbody>
        </table>
    </div>
    <div id="sms_agree" class="win_desc">
        <label for="agree"><?php _e('개인정보처리방침안내의 내용에 동의합니다.', GC_NAME); ?></label>
        <input type="checkbox" name="agree" value="1" id="agree">
    </div>
    <div class="win_btn">
        <input type="submit" value="<?php _e('확인', GC_NAME); ?>" class="btn_submit">
    </div>
</form>

</div>
<script>
jQuery(document).ready(function($) {
    var $c_box = $("#gc_itemstock_dialog_box").dialog({
        'dialogClass' : 'wp-dialog',
        'modal' : false,
        'autoOpen' : false,
        'closeOnEscape' : true,
        'width' : 'auto',
        'height' : 'auto',
        create: function( event, ui ) {
            // Set maxWidth
            $(this).css("maxWidth", "600px");
        },
        'buttons' : [
            {
            'text' : 'Close',
            'class' : 'button-primary',
            'click' : function() {
                            $(this).dialog('close');
                        }
            }
        ]
    });

    $("#sit_btn_buy").on("click", function(e){
        e.preventDefault();
        var title = $(this).attr("title");
        $("span.ui-dialog-title").text(title);
        ($c_box.dialog("isOpen") == false) ? $c_box.dialog("open") : $c_box.dialog("close");
    });

    $("#itemstock_form").submit(function(e) {
        e.preventDefault();

        var $form = $(this),
            agree_check = $form.find("input[name='agree']").prop("checked"),
            formData = jQuery(this).serialize()+"&action=itemstocksmsupdate";

        if( !agree_check ){
            alert("<?php _e('개인정보처리방침안내에 동의해 주십시오.', GC_NAME);?>");
            return false;
        }

        if(confirm("<?php _e('재입고SMS 알림 요청을 등록하시겠습니까?', GC_NAME);?>")) {
            var ajax_var = $.ajax({
                type:"POST",
                url: gnucommerce.ajaxurl,
                data:formData,
                dataType   : 'json', // xml, html, script, json
                cache: false,
                success:function(data, status, xhr){
                    if(data.msg == 'true' && data.s){
                        alert(data.s);
                        $c_box.dialog("close");
                    } else {
                        alert(data.msg);
                    }
                },
                error : function(request, status, error){
                    alert('false ajax :'+request.responseText);
                }
            }); // end of ajax
        }
        return false;
    });
});
</script>