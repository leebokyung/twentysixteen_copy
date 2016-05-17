<?php
if( ! defined( 'GC_NAME' ) ) exit;
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<div class="gc_check_agree">
    <section id="mb_login_notmb" class="check_agree_container">
        <h2>비회원 구매</h2>

        <p>
            비회원으로 주문하시는 경우 적립금을 지급하지 않습니다.
        </p>
        
        <form name="gc_agree_form" method="post" onsubmit="return gc_guest_submit(this);">
            <div id="guest_privacy">
                <?php echo gc_hook_conv_wp('', get_option('gc_de_guest_privacy')); ?>
            </div>
            
            <input type="checkbox" id="gc_agree" name="gc_agree" value="1">
            <label for="gc_agree" class="gc_agree">개인정보수집에 대한 내용을 읽었으며 이에 동의합니다.</label>

            <div class="btn_confirm">
                <input type="submit" class="btn02" value="비회원으로 구매하기">
            </div>
        </form>
        <script>
        function gc_guest_submit(f)
        {
            if (document.getElementById('gc_agree')) {
                if (!document.getElementById('gc_agree').checked) {
                    alert("개인정보수집에 대한 내용을 읽고 이에 동의하셔야 합니다.");
                    return false;
                }
            }
            
            return true;
        }
        </script>
    </section>
</div>