<?php
if( !defined('GC_NAME') ) exit;

//유저 프로필 관리 페이지에서 사용됩니다. 아래 문서 참고
//https://codex.wordpress.org/Plugin_API/Action_Reference/show_user_profile

//마이페이지에서도 회원정보를 수정할때 사용됩니다.
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__), $args );

wp_enqueue_script( GC_NAME.'-shop-js', GC_DIR_URL.'js/shop.js', array(), GC_VERSION );

$config = GC_VAR()->config;

$member = apply_filters('gc_get_display_member', array(
'mb_tel' => get_the_author_meta( 'mb_tel', $user->ID ),  //전화번호
'mb_hp' => get_the_author_meta( 'mb_hp', $user->ID ),   //핸드폰
'mb_zip' => get_the_author_meta( 'mb_zip', $user->ID ), //우편번호
'mb_addr1' => get_the_author_meta( 'mb_addr1', $user->ID ), //  주소
'mb_addr2' => get_the_author_meta( 'mb_addr2', $user->ID ), //
'mb_addr3' => get_the_author_meta( 'mb_addr3', $user->ID ), //
'mb_certify' => get_the_author_meta( 'mb_certify', $user->ID ), //  아이핀 인증 또는 핸드폰 인증
'mb_adult' => get_the_author_meta( 'mb_adult', $user->ID ), //
'mb_birth' => get_the_author_meta( 'mb_birth', $user->ID ), //
'mb_sex' => get_the_author_meta( 'mb_sex', $user->ID ), //
'mb_dupinfo' => get_the_author_meta( 'mb_dupinfo', $user->ID ), //
'gc_first_name' => get_the_author_meta( 'gc_first_name', $user->ID ), //
));

// 본인확인
$mb_certify_yes  =  $member['mb_certify'] ? 'checked="checked"' : '';
$mb_certify_no   = !$member['mb_certify'] ? 'checked="checked"' : '';

// 성인인증
$mb_adult_yes       =  $member['mb_adult']      ? 'checked="checked"' : '';
$mb_adult_no        = !$member['mb_adult']      ? 'checked="checked"' : '';

wp_enqueue_script( GC_NAME.'_certify_js', GC_DIR_URL . 'js/certify.js', array(), GC_VERSION );

add_action('wp_footer', 'gc_profile_check_js', 35);
add_action('admin_footer', 'gc_profile_check_js', 35);

function gc_profile_check_js(){
    $config = GC_VAR()->config;

    if($config['cf_cert_use'] && $config['cf_cert_hp']) {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // 휴대폰인증
        $("#win_hp_cert").off('click').click(function(e) {

            e.preventDefault();

            if(!cert_confirm( this.form ))
                return false;

            <?php
            $cert_type = $cert_url = '';

            switch($config['cf_cert_hp']) {
                case 'kcb':
                    //$cert_url = GC_OKNAME_URL.'/hpcert1.php';
                    $cert_url = gc_get_page_url('hpcert1');
                    $cert_type = 'kcb-hp';
                    break;
                case 'kcp':
                    //$cert_url = GC_KCPCERT_URL.'/kcpcert_form.php';
                    $cert_url = gc_get_page_url('kcpcert_form');
                    $cert_type = 'kcp-hp';
                    break;
                case 'lg':
                    //$cert_url = GC_LGXPAY_URL.'/AuthOnlyReq.php';
                    $cert_url = gc_get_page_url('lgauthonlyreq');
                    $cert_type = 'lg-hp';
                    break;
                default:
                    echo 'alert("'.__('기본환경설정에서 휴대폰 본인확인 설정을 해주십시오', GC_NAME).'");';
                    echo 'return false;';
                    break;
            }
            ?>

            certify_win_open("<?php echo $cert_type; ?>", "<?php echo $cert_url; ?>");
            return;
        });
    });
    </script>
    <?php 
    }   //end if 휴대폰인증

    if($config['cf_cert_use'] && $config['cf_cert_ipin']) { ?>
    <script>
    jQuery(document).ready(function($) {
        // 아이핀인증
        $("#win_ipin_cert").click(function(e) {

            e.preventDefault();

            if(!cert_confirm( this.form ))
                return false;

            var url = "<?php echo gc_get_page_url('ipin1'); ?>";
            certify_win_open('kcb-ipin', url);
            return;
        });
    });
    </script>
    <?php 
    }   //end if 아이핀인증
}
?>
    <table class="form-table">
    <?php
    if( gc_is_admin() == 'super' ){
    ?>
    <tr>
        <th>
            <?php _e('본인확인방법', GC_NAME); ?>
        </th>
        <td>
            <input type="radio" name="mb_certify_case" value="ipin" id="mb_certify_ipin" <?php if($member['mb_certify'] == 'ipin') echo 'checked="checked"'; ?>>
            <label for="mb_certify_ipin"><?php _e('아이핀', GC_NAME); ?></label>
            <input type="radio" name="mb_certify_case" value="hp" id="mb_certify_hp" <?php if($member['mb_certify'] == 'hp') echo 'checked="checked"'; ?>>
            <label for="mb_certify_hp"><?php _e('휴대폰', GC_NAME); ?></label>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('본인확인', GC_NAME); ?></th>
        <td>
            <input type="radio" name="mb_certify" value="1" id="mb_certify_yes" <?php echo $mb_certify_yes; ?>>
            <label for="mb_certify_yes"><?php _e('예', GC_NAME); ?></label>
            <input type="radio" name="mb_certify" value="" id="mb_certify_no" <?php echo $mb_certify_no; ?>>
            <label for="mb_certify_no"><?php _e('아니오', GC_NAME); ?></label>
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('성인인증', GC_NAME); ?></th>
        <td>
            <input type="radio" name="mb_adult" value="1" id="mb_adult_yes" <?php echo $mb_adult_yes; ?>>
            <label for="mb_adult_yes"><?php _e('예', GC_NAME); ?></label>
            <input type="radio" name="mb_adult" value="0" id="mb_adult_no" <?php echo $mb_adult_no; ?>>
            <label for="mb_adult_no"><?php _e('아니오', GC_NAME); ?></label>
        </td>
    </tr>
    <?php } //end if gc_is_admin ?>
    <?php
    if ($config['cf_cert_use']) {
        if($member['mb_certify'] == 'ipin')
            $mb_cert = __('아이핀', GC_NAME);
        else
            $mb_cert = __('휴대폰', GC_NAME);
    ?>
    <tr>
        <th><?php _e('본인확인', GC_NAME); ?></th>
        <td>
        <input type="hidden" name="cert_type" value="<?php echo $member['mb_certify']; ?>">
        <input type="hidden" name="gc_first_name" value="<?php echo $member['gc_first_name']; ?>">
        <?php
        if($config['cf_cert_use']) {
            if($config['cf_cert_ipin'])
                echo '<button type="button" id="win_ipin_cert" class="button button-secondary">'.__('아이핀 본인확인', GC_NAME).'</button>'.PHP_EOL;
            if($config['cf_cert_hp'])
                echo '<button type="button" id="win_hp_cert" class="button button-secondary">'.__('휴대폰 본인확인', GC_NAME).'</button>'.PHP_EOL;

            echo '<noscript>본인확인을 위해서는 자바스크립트 사용이 가능해야합니다.</noscript>'.PHP_EOL;
        }
        ?>
        <?php if( $member['mb_certify'] ){ ?>
        <div id="msg_certify">
            <?php
            $add_str = '';

            if ($member['mb_adult']) {
                $add_str = __('및 <strong>성인인증</strong>', GC_NAME);
            }
            
            echo sprintf(__('<strong>%s 본인확인</strong> %s 완료', GC_NAME ), $mb_cert, $add_str);

            ?>
        </div>
        <?php } ?>
        </td>
    </tr>
    <?php } //end if ?>
    <tr>
        <th>
            <label for="mb_tel"><?php _e('전화번호', GC_NAME); ?></label>
        </th>
        <td>
            <input type="text" name="mb_tel" id="mb_tel" value="<?php echo esc_attr( $member['mb_tel'] ); ?>" class="regular-text" />
            <br><span class="description"><?php _e('전화번호', GC_NAME); ?> ( <?php echo GC_NAME;?> )</span>
        </td>
    </tr>
    <tr>
        <th>
            <label for="mb_hp"><?php _e('핸드폰', GC_NAME); ?></label>
        </th>
        <td>
            <input type="text" name="mb_hp" id="mb_hp" value="<?php echo esc_attr( $member['mb_hp'] ); ?>" class="regular-text" />
            <br><span class="description"><?php _e('핸드폰', GC_NAME); ?> ( <?php echo GC_NAME;?> )</span>
        </td>
    </tr>
    <tr>
        <th>
            <label for="mb_zip"><?php _e('주소', GC_NAME); ?></label>
        </th>
        <td class="mb_address">
            <label for="mb_zip"><?php _e('우편번호', GC_NAME); ?></label>
            <div class="text_input"><input type="text" name="mb_zip" value="<?php echo esc_attr( $member['mb_zip'] ); ?>" id="mb_zip"  size="7" maxlength="6">
            <button type="button" class="btn_frmline btn_frmline1" onclick="gnucommerce.win_zip(this.form, 'mb_zip', 'mb_addr1', 'mb_addr2', 'mb_addr3', 'mb_addr_jibeon');"><?php _e('주소 검색', GC_NAME); ?></button>
            </div>
            <label for="mb_addr1"><?php _e('기본주소', GC_NAME); ?></label>
            <div class="text_input">
            <input type="text" name="mb_addr1" value="<?php echo esc_attr( $member['mb_addr1'] ); ?>" id="mb_addr1" class="regular-text"></div>
            <label for="mb_addr2"><?php _e('상세주소', GC_NAME); ?></label>
            <div class="text_input">
            <input type="text" name="mb_addr2" value="<?php echo esc_attr( $member['mb_addr2'] ); ?>" id="mb_addr2" class="regular-text">
            </div>
            <label class="text_title" for="mb_addr3"><?php _e('참고항목', GC_NAME); ?></label>
            <div class="text_input">
            <input type="text" name="mb_addr3" value="<?php echo esc_attr( $member['mb_addr3'] ); ?>" id="mb_addr3" class="regular-text">
            </div>
            <input type="hidden" name="mb_addr_jibeon" value="">
        </td>
    </tr>
    </table>