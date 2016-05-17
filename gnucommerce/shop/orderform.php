<?php
if( ! defined( 'GC_NAME' ) ) exit;

global $wpdb, $current_user;
get_currentuserinfo();
$gc = GC_VAR()->gc;
$config = GC_VAR()->config;

$sw_direct = isset($_REQUEST['sw_direct']) ? sanitize_text_field($_REQUEST['sw_direct']) : '';
$noc = isset($_REQUEST['noc']) ? sanitize_text_field($_REQUEST['noc']) : '';
$gc_agree = isset($_POST['gc_agree']) ? sanitize_text_field($_POST['gc_agree']) : '';

if( !$sw_direct && !$noc ){     //링크로 바로 들어왔으면, 재고를 체크한다.
    gc_orderform_pre_check();
}

gc_set_session("ss_direct", $sw_direct);

// 장바구니가 비어있는가?
if ($sw_direct) {
    $tmp_cart_id = gc_get_session('ss_cart_direct');
} else {
    $tmp_cart_id = gc_get_session('ss_cart_id');
}

if (gc_get_cart_count($tmp_cart_id) == 0){ //장바구니가 비어 있다면
    gc_skin_load('empty_cart.skin.php');
    return;
}

//우편번호 js 불러오기
gc_postcode_load();

wp_enqueue_script(GC_NAME.'shop_order_js', GC_DIR_URL.'js/shop.order.js' );

$send_cost = 0; //배송비 초기화
$is_kakaopay_use = false;   //변수 초기화

if ($config['de_hope_date_use']) {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-us-css', apply_filters('gc_jquery_ui_style', GC_DIR_URL.'/css/jquery-ui.min.css'));
    add_action( 'wp_footer', 'gc_ko_jqueryui_datepicker', 29 );
}
wp_enqueue_script( GC_NAME.'-popModal-js', GC_DIR_URL.'js/popModal.js', GC_NAME.'-popModal-js', GC_VERSION );
wp_enqueue_style( GC_NAME.'-popModal-css', GC_DIR_URL.'js/popModal.css', GC_NAME.'-popModal-css', GC_VERSION );

// 새로운 주문번호 생성
global $od_id;

$od_id = gc_get_uniqid();
gc_set_session('ss_order_id', $od_id);
$s_cart_id = $tmp_cart_id;

require_once(GC_SHOP_DIR_PATH.'/settle_'.$config['de_pg_service'].'.inc.php');
require_once(GC_SHOP_DIR_PATH.'/settle_kakaopay.inc.php');

// 결제폼 onsubmit attr
$form_onsubmit_attr = 'onsubmit="return gc_forderform_check(this);"';

if( GC_IS_MOBILE || $config['de_pg_service'] == 'kcp' ){    //모바일에서는 // 결제폼 onsubmit attr를 삭제
    $form_onsubmit_attr = '';
}

if( !$s_cart_id ){  //장바구니가 비어 있다면
    gc_skin_load('empty_cart.skin.php');
    return;
}

if( !is_user_logged_in() && !$gc_agree ){  //비회원이면 개인정보수집에 동의해야 한다.
    include_once(GC_SHOP_DIR_PATH.'/check_user_orderform.php');
    return;
}

// 전자결제를 사용할 때만 실행
//필요하지 않으므로 실행하지 않는다.
if($config['de_iche_use'] || $config['de_vbank_use'] || $config['de_hp_use'] || $config['de_card_use'] || $config['de_easy_pay_use']) {
    switch($config['de_pg_service']) {
        case 'lg':
            break;
        case 'inicis':
            GC_VAR()->add_inline_scripts("enable_click();");
            break;
        case 'kcp':
        default :
            if( !GC_IS_MOBILE ){
                GC_VAR()->add_inline_scripts("CheckPayplusInstall();");
            }
            break;
    }
}

$user_order_info = gc_get_member(get_current_user_id());   //현재 사용자 정보를 구함

$tot_point = 0;
$tot_sell_price = 0;

$goods = $goods_it_id = "";
$goods_count = -1;
$good_info = '';
$it_send_cost = 0;
$it_cp_count = 0;

$comm_tax_mny = 0; // 과세금액
$comm_vat_mny = 0; // 부가세
$comm_free_mny = 0; // 면세금액
$tot_tax_mny = 0;

// $s_cart_id 로 현재 장바구니 자료 쿼리
$sql = $wpdb->prepare(" select a.ct_id,
                a.it_id,
                a.it_name,
                a.ct_price,
                a.ct_point,
                a.ct_qty,
                a.ct_status,
                a.ct_send_cost,
                a.it_sc_type,
                b.it_notax
           from {$gc['shop_cart_table']} a left join {$gc['shop_item_table']} b on ( a.it_id = b.it_id )
          where a.od_id = %.0f
            and a.ct_select = '1' ", $s_cart_id);
$sql .= " group by a.it_id ";
$sql .= " order by a.ct_id ";

$results = $wpdb->get_results($sql, ARRAY_A);

$form_datas = array();
$i = 0;

foreach($results as $row){
    if( empty($row) ) continue;

    // 합계금액 계산
    $sql = $wpdb->prepare(" select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
                    SUM(ct_point * ct_qty) as point,
                    SUM(ct_qty) as qty
                from {$gc['shop_cart_table']}
                where it_id = '{$row['it_id']}'
                  and od_id = %.0f ", $s_cart_id);

    $sum = $wpdb->get_row($sql, ARRAY_A);

    if (!$goods)
    {
        //$goods = addslashes($row['it_name']);
        //$goods = gc_get_text($row['it_name']);
        $goods = preg_replace("/\'|\"|\||\,|\&|\;/", "", $row['it_name']);
        $goods_it_id = $row['it_id'];
    }
    $goods_count++;

    // 에스크로 상품정보
    if($config['de_escrow_use']) {
        if ($i>0)
            $good_info .= chr(30);
        $good_info .= "seq=".($i+1).chr(31);
        $good_info .= "ordr_numb={$od_id}_".sprintf("%04d", $i).chr(31);
        $good_info .= "good_name=".addslashes($row['it_name']).chr(31);
        $good_info .= "good_cntx=".$row['ct_qty'].chr(31);
        $good_info .= "good_amtx=".$row['ct_price'].chr(31);
    }

    $row['it_name_options'] = '<strong>' . stripslashes($row['it_name']) . '</strong>';
    $it_options = gc_print_item_options($row['it_id'], $s_cart_id);
    if($it_options) {
        $row['it_name_options'] .= '<div class="sod_opt">'.$it_options.'</div>';
    }

    // 복합과세금액
    if($config['de_tax_flag_use']) {
        if($row['it_notax']) {
            $comm_free_mny += $sum['price'];
        } else {
            $tot_tax_mny += $sum['price'];
        }
    }

    $point      = $sum['point'];
    $sell_price = $sum['price'];

    // 쿠폰
    
    $cp_button = '';    //버튼값 초기화
    if( is_user_logged_in() ) {
        $cp_count = 0;

        $sql = $wpdb->prepare(" select cp_id
                    from {$gc['shop_coupon_table']}
                    where mb_id IN ( '".get_current_user_id()."', '%s' )
                      and cp_start <= '%s'
                      and cp_end >= '%s'
                      and cp_minimum <= '%d'
                      and ( cp_method = '0' and cp_target = '{$row['it_id']}' )
                    ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD, $sell_price);

        $res = $wpdb->get_results($sql, ARRAY_A);
       
        foreach($res as $cp){
            if( empty($cp) ) continue;
            if(gc_is_used_coupon( get_current_user_id(), $cp['cp_id']))
                continue;

            $cp_count++;
        }

        if($cp_count) {
            $cp_button = '<div class="li_cp"><button type="button" class="cp_btn btn_frmline">'.__('쿠폰적용', GC_NAME).'</button></div>';
            $it_cp_count++;
        }
    }

    // 배송비
    switch($row['ct_send_cost'])
    {
        case 1:
            $ct_send_cost = __('착불', GC_NAME);
            break;
        case 2:
            $ct_send_cost = __('무료', GC_NAME);
            break;
        default:
            $ct_send_cost = __('선불', GC_NAME);
            break;
    }

    // 조건부무료
    if($row['it_sc_type'] == 2) {
        $sendcost = gc_get_item_sendcost($row['it_id'], $sum['price'], $sum['qty'], $s_cart_id);

        if($sendcost == 0)
            $ct_send_cost = '무료';
    }

    $row['sell_price'] = $sell_price;
    $row['sum_qty'] =  $sum['qty'];	//총수량
    $row['cp_button'] = $cp_button;
    $row['point'] = $point; //적립금
    $row['ct_send_cost'] = $ct_send_cost;	//배송비

    $form_datas[] = $row;   //배열에 저장

    $tot_point      += $point;
    $tot_sell_price += $sell_price;
    $i++;
} //end foreach $result

if( !$form_datas ){  //장바구니가 비어 있다면
    gc_skin_load('empty_cart.skin.php');
    return;
}

$send_cost = gc_get_sendcost($s_cart_id);

$tot_price = (int) $tot_sell_price + (int) $send_cost; // 총계 = 주문상품금액합계 + 배송비
?>

<div id="sod_frm" class="gc_order_form">
    <!-- 주문상품 확인 시작 { -->
    <p>주문하실 상품을 확인하세요.</p>

    <?php
    // 결제대행사별 코드 include (스크립트 등)
    require_once(GC_SHOP_DIR_PATH.'/'.$config['de_pg_service'].'/orderform.1.php');

    if($is_kakaopay_use) {
        require_once(GC_SHOP_DIR_PATH.'/kakaopay/orderform.1.php');
    }
    ?>
    <form name="forderform" id="forderform" method="post" <?php echo $form_onsubmit_attr;?> autocomplete="off">
    <?php wp_nonce_field( 'gc_order_nonce', 'gc_nonce_field' ); ?>

    <ul class="sod_list">
        <?php
        $i = 0;
        foreach( $form_datas as $row ){
            
            if( empty($row) ) continue;
            
            $image_width = 80;
            $image_height = 80;
            $image = gc_get_it_image($row['it_id'], $image_width, $image_height);
        ?>
        <li class="sod_li">
            <input type="hidden" name="it_id[<?php echo $i; ?>]"    value="<?php echo $row['it_id']; ?>">
            <input type="hidden" name="it_name[<?php echo $i; ?>]"  value="<?php echo gc_get_text($row['it_name']); ?>">
            <input type="hidden" name="it_price[<?php echo $i; ?>]" value="<?php echo $row['sell_price']; ?>">
            <input type="hidden" name="cp_id[<?php echo $i; ?>]" value="">
            <input type="hidden" name="cp_price[<?php echo $i; ?>]" value="0">
            <?php if($config['de_tax_flag_use']) {  //복합과세 설정 ?>
            <input type="hidden" name="it_notax[<?php echo $i; ?>]" value="<?php echo $row['it_notax']; ?>">
            <?php } ?>
            <div class="li_name">
                <?php echo $row['it_name_options']; ?>
                <div class="li_mod"  style="padding-left:<?php echo $image_width + 20; ?>px;"><?php echo $cp_button; ?></div>
                <span class="total_img"><?php echo $image; ?></span>

            </div>
            <div class="li_prqty">
                <span class="prqty_price li_prqty_sp"><span><?php _e('판매가', GC_NAME);?> </span><?php echo gc_number_format($row['ct_price']); ?></span>
                <span class="prqty_qty li_prqty_sp"><span><?php _e('수량', GC_NAME);?> </span><?php echo number_format($row['sum_qty']); ?></span>
                <span class="prqty_sc li_prqty_sp"><span><?php _e('배송비', GC_NAME);?> </span><?php echo $row['ct_send_cost']; ?></span>
            </div>
            <div class="li_total">
                <span class="total_price total_span"><span><?php _e('주문금액', GC_NAME);?> </span><strong><?php echo number_format($row['sell_price']); ?></strong></span>
                <span class="total_point total_span"><span><?php _e('적립금', GC_NAME);?> </span><strong><?php echo number_format($row['point']); ?></strong></span>
            </div>
        </li>
        <?php
        $i++;
        } // end foreach $form_datas

        // 배송비 계산
        $send_cost = gc_get_sendcost($s_cart_id);

        // 복합과세처리
        if($config['de_tax_flag_use']) {
            $comm_tax_mny = round(($tot_tax_mny + $send_cost) / 1.1);
            $comm_vat_mny = ($tot_tax_mny + $send_cost) - $comm_tax_mny;
        }
        ?>
    </ul>

    <?php if ($goods_count) $goods = sprintf(__('%s 외 %d 건', GC_NAME), $goods, $goods_count) ?>
    <!-- } 주문상품 확인 끝 -->

    <!-- 주문상품 합계 시작 { -->
    <dl id="sod_bsk_tot">
        <dt class="sod_bsk_sell">주문</dt>
        <dd class="sod_bsk_sell"><strong><?php echo number_format($tot_sell_price); ?> 원</strong></dd>
        <?php if($it_cp_count > 0) { ?>
        <dt class="sod_bsk_coupon ">쿠폰할인</dt>
        <dd class="sod_bsk_coupon"><strong id="ct_tot_coupon">0 원</strong></dd>
        <?php } ?>
        <dt class="sod_bsk_dvr">배송비</dt>
        <dd class="sod_bsk_dvr"><strong><?php echo number_format($send_cost); ?> 원</strong></dd>
        <dt class="sod_bsk_cnt">총계</dt>
        <dd class="sod_bsk_cnt">
            <strong id="ct_tot_price"><?php echo number_format($tot_price); ?> 원</strong>
        </dd>
        <dt class="sod_bsk_point">적립금</dt>
        <dd class="sod_bsk_point"><strong><?php echo number_format($tot_point); ?> 원</strong></dd>
    </dl>
    <!-- } 주문상품 합계 끝 -->

    <input type="hidden" name="od_price"    value="<?php echo $tot_sell_price; ?>">
    <input type="hidden" name="org_od_price"    value="<?php echo $tot_sell_price; ?>">
    <input type="hidden" name="od_send_cost" value="<?php echo $send_cost; ?>">
    <input type="hidden" name="od_send_cost2" value="0">
    <input type="hidden" name="item_coupon" value="0">
    <input type="hidden" name="od_coupon" value="0">
    <input type="hidden" name="od_send_coupon" value="0">

    <?php
    // 결제대행사별 코드 include (결제대행사 정보 필드)
    require_once(GC_SHOP_DIR_PATH.'/'.$config['de_pg_service'].'/orderform.2.php');

    if($is_kakaopay_use) {
        require_once(GC_SHOP_DIR_PATH.'/kakaopay/orderform.2.php');
    }
    ?>

    <!-- 주문하시는 분 입력 시작 { -->
    <section id="sod_frm_orderer">
        <h2 class="sod_title">주문하시는 분</h2>

        <ul class="orderer_box">
            <li>
                <label class="text_title" for="od_name">이름<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_name" value="<?php echo $user_order_info['mb_name']; ?>" id="od_name" required class="frm_input required" maxlength="20"></div>
            </tr>

            <?php if (! is_user_logged_in() ) { // 비회원이면 ?>
            <li>
                <label class="text_title" for="od_pwd">비밀번호</label>
                <div class="text_input">
                    <input type="password" name="od_pwd" id="od_pwd" required class="frm_input required" maxlength="20">
                    <span class="frm_info">영,숫자 3~20자 (주문서 조회시 필요)</span>
                </div>
            </li>
            <?php } ?>

            <li>
                <label class="text_title" for="od_tel">전화번호<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_tel" value="<?php echo $user_order_info['mb_tel'];?>" id="od_tel" required class="frm_input required" maxlength="20"></div>
            </li>
            <li>
                <label class="text_title" for="od_hp">핸드폰</label>
                <div class="text_input"><input type="text" name="od_hp" value="<?php echo $user_order_info['mb_hp'];?>" id="od_hp" class="frm_input" maxlength="20"></div>
            </li>
            <li>
                <label for="od_zip" class="text_title">우편번호<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_zip" value="<?php echo $user_order_info['mb_zip'];?>" id="od_zip" required class="frm_input required" size="7" maxlength="6">
                <button type="button" class="btn_frmline btn_frmline1" onclick="gnucommerce.win_zip('forderform', 'od_zip', 'od_addr1', 'od_addr2', 'od_addr3', 'od_addr_jibeon');">주소 검색</button>
                </div>
                <label class="text_title" for="od_addr1">기본주소<strong class="sound_only"> 필수</strong></label>
                <div class="text_input">
                <input type="text" name="od_addr1" value="<?php echo $user_order_info['mb_addr1'];?>" id="od_addr1" required class="frm_input frm_block frm_address required"></div>
                <label class="text_title" for="od_addr2">상세주소</label>
                <div class="text_input">
                <input type="text" name="od_addr2" value="<?php echo $user_order_info['mb_addr2'];?>" id="od_addr2" class="frm_input frm_block frm_address">
                </div>
                <label class="text_title" for="od_addr3">참고항목</label>
                <div class="text_input">
                <input type="text" name="od_addr3" value="<?php echo $user_order_info['mb_addr3'];?>" id="od_addr3" class="frm_input frm_block frm_address" readonly="readonly">
                </div>
                <input type="hidden" name="od_addr_jibeon" value="">
            </li>
            <li>
                <label class="text_title" for="od_email">E-mail<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_email" value="<?php echo $user_order_info['user_email'];?>" id="od_email" required class="frm_input required" maxlength="100"></div>
            </li>

            <?php if ($config['de_hope_date_use']) { // 배송희망일 사용 ?>
            <li>
                <label class="text_title" for="od_hope_date">희망배송일</label>
                <div class="text_input">
                    <!-- <select name="od_hope_date" id="od_hope_date">
                    <option value="">선택하십시오.</option>
                    <?php
                    for ($i=0; $i<7; $i++) {
                        $sdate = date("Y-m-d", GC_SERVER_TIME+86400*($config['de_hope_date_after']+$i));
                        echo '<option value="'.$sdate.'">'.$sdate.' ('.gc_get_yoil($sdate).')</option>'.PHP_EOL;
                    }
                    ?>
                    </select> -->
                    <input type="text" name="od_hope_date" value="" id="od_hope_date" required class="frm_input required" size="11" maxlength="10" readonly="readonly"> 이후로 배송 바랍니다.
                </div>
            </li>
            <?php } ?>
            <?php do_action('gc_orderform_print'); ?>
        </ul>
    </section>
    <!-- } 주문하시는 분 입력 끝 -->

    <!-- 받으시는 분 입력 시작 { -->
    <section id="sod_frm_taker">
        <h2>받으시는 분</h2>

        <ul class="orderer_box">
            <?php
            $addr_list = '';    //버튼값 초기화
            if(is_user_logged_in()) {
                // 배송지 이력
                $sep = chr(30);

                // 주문자와 동일
                $addr_list .= '<input type="radio" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
                $addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;

                $addr_list .= '<input type="radio" name="ad_sel_addr" value="new" id="od_sel_addr_new">'.PHP_EOL;
                $addr_list .= '<label for="od_sel_addr_new">신규배송지</label>'.PHP_EOL;

                $addr_list .='<p><a href="#" id="order_address" class="btn_frmline">최근 배송지목록</a></p>';
            } else {
                // 주문자와 동일
                $addr_list .= '<input type="checkbox" name="ad_sel_addr" value="same" id="ad_sel_addr_same">'.PHP_EOL;
                $addr_list .= '<label for="ad_sel_addr_same">주문자와 동일</label>'.PHP_EOL;
            }
            ?>
            <li>
                <div>배송지선택</div>
                <div class="text_input">
                    <?php echo $addr_list; ?>
                </div>
            </li>
            <?php if( is_user_logged_in() ) { ?>
            <li>
                <label class="text_title" for="ad_subject">배송지명</label>
                <div class="text_input">
                    <input type="text" name="ad_subject" id="ad_subject" class="frm_input" maxlength="20">
                </div>
            </li>
            <?php } ?>
            <li>
                <label class="text_title" for="od_b_name">이름<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_b_name" id="od_b_name" required class="frm_input required" maxlength="20"></div>
            </li>
            <li>
                <label class="text_title" for="od_b_tel">전화번호<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_b_tel" id="od_b_tel" required class="frm_input required" maxlength="20"></div>
            </li>
            <li>
                <label class="text_title" for="od_b_hp">핸드폰</label>
                <div class="text_input"><input type="text" name="od_b_hp" id="od_b_hp" class="frm_input" maxlength="20"></div>
            </li>
            <li>
                <label class="text_title" for="od_b_zip" class="sound_only">우편번호<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_b_zip" id="od_b_zip" required class="frm_input required" size="5" maxlength="6">
                <button type="button" class="btn_frmline btn_frmline1" onclick="gnucommerce.win_zip('forderform', 'od_b_zip', 'od_b_addr1', 'od_b_addr2', 'od_b_addr3', 'od_b_addr_jibeon');">주소 검색</button></div>
                <label class="text_title" for="od_b_addr1">기본주소<strong class="sound_only"> 필수</strong></label>
                <div class="text_input"><input type="text" name="od_b_addr1" id="od_b_addr1" required class="frm_input frm_block frm_address required">
                </div>
                <label class="text_title" for="od_b_addr2">상세주소</label>
                <div class="text_input"><input type="text" name="od_b_addr2" id="od_b_addr2" class="frm_input frm_block frm_address"></div>
                <label class="text_title" for="od_b_addr3">참고항목</label>
                <div class="text_input">
                <input type="text" name="od_b_addr3" id="od_b_addr3" readonly="readonly" class="frm_input frm_block frm_address">
                </div>
                <input type="hidden" name="od_b_addr_jibeon" value="">
            </li>
            <li>
                <label class="text_title" for="od_memo">전하실말씀</label>
                <div class="text_input"><textarea name="od_memo" id="od_memo"></textarea></div>
            </li>
        </ul>
    </section>
    <!-- } 받으시는 분 입력 끝 -->

    <!-- 결제정보 입력 시작 { -->
    <?php
    global $oc_cnt;
    $oc_cnt = $sc_cnt = 0;
    if(is_user_logged_in()) {
        // 주문쿠폰
        $sql = $wpdb->prepare(" select cp_id
                    from {$gc['shop_coupon_table']}
                    where mb_id IN ( '".get_current_user_id()."', '%s' )
                      and cp_method = '2'
                      and cp_start <= '%s'
                      and cp_end >= '%s'
                      and cp_minimum <= '%s' ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD, $tot_sell_price);
        
        $res = $wpdb->get_results($sql, ARRAY_A);

        foreach($res as $cp){
            if(gc_is_used_coupon(get_current_user_id(), $cp['cp_id']))
                continue;

            $oc_cnt++;            
        }

        if($send_cost > 0) {
            // 배송비쿠폰
            $sql = $wpdb->prepare(" select cp_id
                        from {$gc['shop_coupon_table']}
                        where mb_id IN ( '".get_current_user_id()."', '%s' )
                          and cp_method = '3'
                          and cp_start <= '%s'
                          and cp_end >= '%s'
                          and cp_minimum <= '$tot_sell_price' ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD, $tot_sell_price);

            $res = $wpdb->get_results($sql, ARRAY_A);

            foreach($res as $cp){
                if(gc_is_used_coupon(get_current_user_id(), $cp['cp_id']))
                    continue;

                $sc_cnt++;
            }
        }
    }
    ?>

    <section id="sod_frm_pay">
        <h2>결제정보</h2>

        <div class="tbl_frm01 tbl_wrap">
            <table>
            <col width="100px">
            <col>
            <tbody>
            <?php if($oc_cnt > 0) { ?>
            <tr>
                <th scope="row">주문할인쿠폰</th>
                <td>
                    <input type="hidden" name="od_cp_id" value="">
                    <button type="button" id="od_coupon_btn" class="btn_frmline">쿠폰적용</button>
                </td>
            </tr>
            <tr>
                <th scope="row">주문할인금액</th>
                <td><span id="od_cp_price">0</span>원</td>
            </tr>
            <?php } ?>
            <?php if($sc_cnt > 0) { ?>
            <tr>
                <th scope="row">배송비할인쿠폰</th>
                <td>
                    <input type="hidden" name="sc_cp_id" value="">
                    <button type="button" id="sc_coupon_btn" class="btn_frmline">쿠폰적용</button>
                </td>
            </tr>
            <tr>
                <th scope="row">배송비할인금액</th>
                <td><span id="sc_cp_price">0</span>원</td>
            </tr>
            <?php } ?>
            <tr>
                <th>총 주문금액</th>
                <td><span id="od_tot_price"><?php echo number_format($tot_price); ?></span>원</td>
            </tr>
            <tr>
                <th>추가배송비</th>
                <td><span id="od_send_cost2">0</span>원 (지역에 따라 추가되는 도선료 등의 배송비입니다.)</td>
            </tr>
            </tbody>
            </table>
        </div>

        <?php

        //적립금 관련
        if (!$config['de_card_mileage'])
            echo '<p id="sod_frm_pt_alert"><strong>무통장입금</strong> 이외의 결제 수단으로 결제하시는 경우 적립금을 적립해드리지 않습니다.</p>';

        $multi_settle = 0;
        $checked = '';

        $escrow_title = "";
        if ($config['de_escrow_use']) {
            $escrow_title = "에스크로 ";
        }

        if ($is_kakaopay_use || $config['de_bank_use'] || $config['de_vbank_use'] || $config['de_iche_use'] || $config['de_card_use'] || $config['de_hp_use'] || $config['de_easy_pay_use']) {
            echo '<fieldset id="sod_frm_paysel">';
            echo '<legend>결제방법 선택</legend>';
        }

        // 카카오페이
        if($is_kakaopay_use) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_kakaopay" name="od_settle_case" value="KAKAOPAY" '.$checked.'> <label for="od_settle_kakaopay" class="kakaopay_icon">KAKAOPAY</label>'.PHP_EOL;
            $checked = '';
        }

        // 무통장입금 사용
        if ($config['de_bank_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_bank" name="od_settle_case" value="'.gc_get_stype_names('banktransfer').'" '.$checked.'> <label for="od_settle_bank">'.gc_print_stype_names('banktransfer').'</label>'.PHP_EOL;
            $checked = '';
        }

        // 가상계좌 사용
        if ($config['de_vbank_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_vbank" name="od_settle_case" value="'.gc_get_stype_names('virtualaccount').'" '.$checked.'> <label for="od_settle_vbank">'.$escrow_title.gc_print_stype_names('virtualaccount').'</label>'.PHP_EOL;
            $checked = '';
        }

        // 계좌이체 사용
        if ($config['de_iche_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_iche" name="od_settle_case" value="'.gc_get_stype_names('accounttransfer').'" '.$checked.'> <label for="od_settle_iche">'.$escrow_title.gc_print_stype_names('accounttransfer').'</label>'.PHP_EOL;
            $checked = '';
        }

        // 휴대폰 사용
        if ($config['de_hp_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_hp" name="od_settle_case" value="'.gc_get_stype_names('phonepayment').'" '.$checked.'> <label for="od_settle_hp">'.gc_print_stype_names('phonepayment').'</label>'.PHP_EOL;
            $checked = '';
        }

        // 신용카드 사용
        if ($config['de_card_use']) {
            $multi_settle++;
            echo '<input type="radio" id="od_settle_card" name="od_settle_case" value="'.gc_get_stype_names('creditcard').'" '.$checked.'> <label for="od_settle_card">'.gc_print_stype_names('creditcard').'</label>'.PHP_EOL;
            $checked = '';
        }

        // PG 간편결제
        if($config['de_easy_pay_use']) {
            switch($config['de_pg_service']) {
                case 'lg':
                    $pg_easy_pay_name = 'PAYNOW';
                    break;
                case 'inicis':
                    $pg_easy_pay_name = 'KPAY';
                    break;
                default:
                    $pg_easy_pay_name = 'PAYCO';
                    break;
            }

            $multi_settle++;
            echo '<input type="radio" id="od_settle_easy_pay" name="od_settle_case" value="'.gc_get_stype_names('easypayment').'" '.$checked.'> <label for="od_settle_easy_pay" class="'.$pg_easy_pay_name.'">'.$pg_easy_pay_name.'</label>'.PHP_EOL;
            $checked = '';
        }

        global $temp_mileage;
        $temp_mileage = 0;

        // 회원이면서 적립금사용이면
        if (is_user_logged_in() && $config['cf_use_mileage'])
        {
            // 적립금 결제 사용 적립금보다 회원의 적립금이 크다면
            if ($user_order_info['mb_mileage'] >= $config['de_settle_min_mileage'])
            {
                $temp_mileage = (int)$config['de_settle_max_mileage'];

                if($temp_mileage > (int)$tot_sell_price)
                    $temp_mileage = (int)$tot_sell_price;

                if($temp_mileage > (int)$user_order_info['mb_mileage'])
                    $temp_mileage = (int)$user_order_info['mb_mileage'];

                $mileage_unit = (int)$config['de_settle_mileage_unit'];
                $temp_mileage = (int)((int)($temp_mileage / $mileage_unit) * $mileage_unit);
        ?>
            <p id="sod_frm_pt">보유적립금(<?php echo gc_display_mileage($user_order_info['mb_mileage']); ?>)중 <strong id="use_max_mileage">최대 <?php echo gc_display_mileage($temp_mileage); ?></strong>까지 사용 가능</p>
            <input type="hidden" name="max_temp_mileage" value="<?php echo $temp_mileage; ?>">
            <label for="od_temp_mileage">사용 적립금</label>
            <input type="text" name="od_temp_mileage" value="0" id="od_temp_mileage" class="frm_input" size="10">점 (<?php echo $mileage_unit; ?>점 단위로 입력하세요.)
        <?php
            $multi_settle++;
            }
        }

        if ($config['de_bank_use']) {
            // 은행계좌를 배열로 만든후
            $str = explode("\n", trim($config['de_bank_account']));
            if (count($str) <= 1)
            {
                $bank_account = '<input type="hidden" name="od_bank_account" value="'.$str[0].'">'.$str[0].PHP_EOL;
            }
            else
            {
                $bank_account = '<select name="od_bank_account" id="od_bank_account">'.PHP_EOL;
                $bank_account .= '<option value="">선택하십시오.</option>';
                for ($i=0; $i<count($str); $i++)
                {
                    //$str[$i] = str_replace("\r", "", $str[$i]);
                    $str[$i] = trim($str[$i]);
                    $bank_account .= '<option value="'.$str[$i].'">'.$str[$i].'</option>'.PHP_EOL;
                }
                $bank_account .= '</select>'.PHP_EOL;
            }
            echo '<div id="settle_bank" style="display:none">';
            echo '<label for="od_bank_account" class="sound_only">입금할 계좌</label>';
            echo $bank_account;
            echo '<br><label for="od_deposit_name">입금자명</label>';
            echo '<input type="text" name="od_deposit_name" id="od_deposit_name" class="frm_input" size="10" maxlength="20">';
            echo '</div>';
        }

        if ($config['de_bank_use'] || $config['de_vbank_use'] || $config['de_iche_use'] || $config['de_card_use'] || $config['de_hp_use']) {
            echo '</fieldset>';
        }

        if ($multi_settle == 0)
            echo '<p>결제할 방법이 없습니다.<br>운영자에게 알려주시면 감사하겠습니다.</p>';
        ?>
    </section>
    <!-- } 결제 정보 입력 끝 -->

    <?php
    do_action('order_button_before_print', $config['de_pg_service']);

    // 결제대행사별 코드 include (주문버튼)
    require_once(GC_SHOP_DIR_PATH.'/'.$config['de_pg_service'].'/orderform.3.php');

    if($is_kakaopay_use) {
        require_once(GC_SHOP_DIR_PATH.'/kakaopay/orderform.3.php');
    }
    ?>
    </form>

    <?php
    if ($config['de_escrow_use']) {
        // 결제대행사별 코드 include (에스크로 안내)
        require_once(GC_SHOP_DIR_PATH.'/'.$config['de_pg_service'].'/orderform.4.php');
    }

    do_action('order_form_after_print', $config['de_pg_service']);
    ?>

</div>
<?php
do_action('gc_orderform_footer', array(
    'od_id' => $od_id,
    'goods' =>  $goods,
    'tot_price' => $tot_price,
    'next_url'   =>  isset($next_url) ? $next_url : '',
    'noti_url'   => isset($noti_url) ? $noti_url : '',
    'useescrow' => isset($useescrow) ? $useescrow : '',
));

add_action( 'wp_footer', 'gc_orderform_php_js', 26 );

if(GC_IS_MOBILE){  //모바일이면
    add_action( 'wp_footer', 'gc_mobile_orderform_js', 27 );
}

function gc_mobile_orderform_js(){
    $sw_direct = isset($sw_direct) ? $sw_direct : isset($_REQUEST['sw_direct']) ? sanitize_text_field($_REQUEST['sw_direct']) : '';

    if (!wp_script_is( 'gc_mobile_orderform_js_load', 'done' ) ) {
        global $temp_mileage, $oc_cnt, $sc_cnt, $od_id;

        $config = GC_VAR()->config;
        $return_url = GC_VAR()->return_url;
        ?>

<script type="text/javascript">
/* <![CDATA[ */
function gc_pay_approval(){     //모바일 결제시 체크한다.

    // 재고체크
    var stock_msg = gnucommerce.order_stock_check();
    if(stock_msg != "") {
        alert(stock_msg);
        return false;
    }

    var f = document.sm_form;
    var pf = document.forderform;

    // 필드체크
    if(!gc_orderfield_check(pf))
        return false;

    // 금액체크
    if(!gc_payment_check(pf))
        return false;

    // pg 결제 금액에서 적립금 금액 차감
    if(settle_method != "<?php echo gc_get_stype_names('banktransfer');?>") {  //무통장
        var od_price = parseInt(pf.od_price.value);
        var send_cost = parseInt(pf.od_send_cost.value);
        var send_cost2 = parseInt(pf.od_send_cost2.value);
        var send_coupon = parseInt(pf.od_send_coupon.value);
        f.good_mny.value = od_price + send_cost + send_cost2 - send_coupon - temp_point;
    }

    // 카카오페이 지불
    if(settle_method == "KAKAOPAY") {
        <?php if($config['de_tax_flag_use']) { ?>
        pf.SupplyAmt.value = parseInt(pf.comm_tax_mny.value) + parseInt(pf.comm_free_mny.value);
        pf.GoodsVat.value  = parseInt(pf.comm_vat_mny.value);
        <?php } ?>
        pf.good_mny.value = f.good_mny.value;
        getTxnId(pf);
        return false;
    }

    <?php if($config['de_pg_service'] == 'kcp') { ?>
    f.buyr_name.value = pf.od_name.value;
    f.buyr_mail.value = pf.od_email.value;
    f.buyr_tel1.value = pf.od_tel.value;
    f.buyr_tel2.value = pf.od_hp.value;
    f.rcvr_name.value = pf.od_b_name.value;
    f.rcvr_tel1.value = pf.od_b_tel.value;
    f.rcvr_tel2.value = pf.od_b_hp.value;
    f.rcvr_mail.value = pf.od_email.value;
    f.rcvr_zipx.value = pf.od_b_zip.value;
    f.rcvr_add1.value = pf.od_b_addr1.value;
    f.rcvr_add2.value = pf.od_b_addr2.value;
    f.settle_method.value = settle_method;
    if(settle_method == "<?php echo gc_get_stype_names('easypayment');?>")  //간편결제
        f.payco_direct.value = "Y";
    else
        f.payco_direct.value = "";
    <?php } else if($config['de_pg_service'] == 'lg') { ?>
    var pay_method = "";
    var easy_pay = "";
    switch(settle_method) {
        case "<?php echo gc_get_stype_names('accounttransfer'); ?>":    //계좌이체
            pay_method = "SC0030";
            break;
        case "<?php echo gc_get_stype_names('virtualaccount'); ?>": //가상계좌
            pay_method = "SC0040";
            break;
        case "<?php echo gc_get_stype_names('phonepayment'); ?>":   //휴대폰
            pay_method = "SC0060";
            break;
        case "<?php echo gc_get_stype_names('creditcard'); ?>":     //신용카드
            pay_method = "SC0010";
            break;
        case "<?php echo gc_get_stype_names('easypayment');?>":     //간편결제
            easy_pay = "PAYNOW";
            break;
    }
    f.LGD_CUSTOM_FIRSTPAY.value = pay_method;
    f.LGD_BUYER.value = pf.od_name.value;
    f.LGD_BUYEREMAIL.value = pf.od_email.value;
    f.LGD_BUYERPHONE.value = pf.od_hp.value;
    f.LGD_AMOUNT.value = f.good_mny.value;
    f.LGD_EASYPAY_ONLY.value = easy_pay;
    <?php if($config['de_tax_flag_use']) { ?>
    f.LGD_TAXFREEAMOUNT.value = pf.comm_free_mny.value;
    <?php } ?>
    <?php } else if($config['de_pg_service'] == 'inicis') { ?>
    var paymethod = "";
    var width = 330;
    var height = 480;
    var xpos = (screen.width - width) / 2;
    var ypos = (screen.width - height) / 2;
    var position = "top=" + ypos + ",left=" + xpos;
    var features = position + ", width=320, height=440";
    switch(settle_method) {
        case "<?php echo gc_get_stype_names('accounttransfer'); ?>":    //계좌이체
            paymethod = "bank";
            break;
        case "<?php echo gc_get_stype_names('virtualaccount'); ?>": //가상계좌
            paymethod = "vbank";
            break;
        case "<?php echo gc_get_stype_names('phonepayment'); ?>":   //휴대폰
            paymethod = "mobile";
            break;
        case "<?php echo gc_get_stype_names('creditcard'); ?>":     //신용카드
            paymethod = "wcard";
            break;
        case "<?php echo gc_get_stype_names('easypayment');?>":     //간편결제
            paymethod = "wcard";
            f.P_RESERVED.value = p_reserved+"&d_kpay=Y&d_kpay_app=Y";
            break;
    }
    f.P_AMT.value = f.good_mny.value;
    f.P_UNAME.value = pf.od_name.value;
    f.P_MOBILE.value = pf.od_hp.value;
    f.P_EMAIL.value = pf.od_email.value;
    <?php if($config['de_tax_flag_use']) { ?>
    f.P_TAX.value = pf.comm_vat_mny.value;
    f.P_TAXFREE = pf.comm_free_mny.value;
    <?php } ?>
    f.P_RETURN_URL.value = "<?php echo $return_url.'&oid='.$od_id; ?>";
    f.action = "https://mobile.inicis.com/smart/" + paymethod + "/";
    <?php } ?>

    //var new_win = window.open("about:blank", "tar_opener", "scrollbars=yes,resizable=yes");
    //f.target = "tar_opener";

    // 주문 정보 임시저장
    var order_data = jQuery(pf).serialize();
    
    order_data += "&action="+encodeURIComponent('gc_orderdatasave');

    var save_result = "";
    jQuery.ajax({
        type: "POST",
        data: order_data,
        url: gnucommerce.ajaxurl,
        cache: false,
        async: false,
        success: function(data) {
            save_result = data;
        }
    });

    if(save_result) {
        alert(save_result);
        return false;
    }

    f.submit();
}
/* ]]> */
</script>

        <?php
    }   //end if
    global $wp_scripts;
    $wp_scripts->done[] = 'gc_mobile_orderform_js_load';
}   //end function gc_mobile_orderform_js

function gc_orderform_php_js(){

    $sw_direct = isset($sw_direct) ? $sw_direct : isset($_REQUEST['sw_direct']) ? sanitize_text_field($_REQUEST['sw_direct']) : '';

    if (!wp_script_is( 'gc_orderform_php_js_load', 'done' ) ) {
        global $temp_mileage, $oc_cnt, $sc_cnt;

        $config = GC_VAR()->config;
        $member = gc_get_member(get_current_user_id());
?>
<script>
var settle_method = "";
var temp_point = 0;

(function($) {
    var $cp_btn_el;
    var $cp_row_el;
    var zipcode = "";

    $(".cp_btn").click(function(e) {
        e.preventDefault();

        $cp_btn_el = $(this);
        $cp_row_el = $(this).closest("li");
        $("#cp_frm").remove();
        var it_id = $cp_btn_el.closest("li").find("input[name^=it_id]").val();

        if( typeof(it_id) != "undefined" ){
            $.post(
                gnucommerce.ajaxurl,
                { action : 'gc_orderitemcoupon', it_id: it_id,  sw_direct: "<?php echo $sw_direct; ?>" },
                function(data) {
                    $cp_btn_el.after(data);
                }
            );
        } else {
            alert('it_id 값이 없습니다.');
        }
    });

    $(document).on("click", ".cp_apply", function(e) {
        e.preventDefault();

        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='f_cp_id[]']").val();
        var price = $el.find("input[name='f_cp_prc[]']").val();
        var subj = $el.find("input[name='f_cp_subj[]']").val();
        var sell_price;

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        // 이미 사용한 쿠폰이 있는지
        var cp_dup = false;
        var cp_dup_idx;
        var $cp_dup_el;
        $("input[name^=cp_id]").each(function(index) {
            var id = $(this).val();

            if(id == cp_id) {
                cp_dup_idx = index;
                cp_dup = true;
                $cp_dup_el = $(this).closest("li");

                return false;
            }
        });

        if(cp_dup) {
            var it_name = $("input[name='it_name["+cp_dup_idx+"]']").val();
            if(!confirm(subj+ "쿠폰은 "+it_name+"에 사용되었습니다.\n"+it_name+"의 쿠폰을 취소한 후 적용하시겠습니까?")) {
                return false;
            } else {
                coupon_cancel($cp_dup_el);
                $("#cp_frm").remove();
                $cp_dup_el.find(".cp_btn").text("적용").focus();
                $cp_dup_el.find(".cp_cancel").remove();
            }
        }

        var $s_el = $cp_row_el.find(".total_price");;
        sell_price = parseInt($cp_row_el.find("input[name^=it_price]").val());
        sell_price = sell_price - parseInt(price);
        if(sell_price < 0) {
            alert("쿠폰할인금액이 상품 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }
        $s_el.text(gnucommerce.number_format(String(sell_price)));
        $cp_row_el.find("input[name^=cp_id]").val(cp_id);
        $cp_row_el.find("input[name^=cp_price]").val(price);

        calculate_total_price();
        $("#cp_frm").remove();
        $cp_btn_el.text("변경").focus();
        if(!$cp_row_el.find(".cp_cancel").size())
            $cp_btn_el.after("<button type=\"button\" class=\"cp_cancel btn_frmline\">취소</button>");
    });

    $(document).on("click", "#cp_close", function(e) {
        $("#cp_frm").remove();
        $cp_btn_el.focus();
    });

    $(document).on("click", ".cp_cancel", function(e) {
        coupon_cancel($(this).closest("li"));
        calculate_total_price();
        $("#cp_frm").remove();
        $(this).closest("li").find(".cp_btn").text("적용").focus();
        $(this).remove();
    });

    $("#od_coupon_btn").click(function() {
        $("#od_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=org_od_price]").val()) - parseInt($("input[name=item_coupon]").val());
        if(price <= 0) {
            alert('상품금액이 0원이므로 쿠폰을 사용할 수 없습니다.');
            return false;
        }
        $.post(
            gnucommerce.ajaxurl,
            { action : 'gc_ordercoupon', price: price },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(document).on("click", ".od_cp_apply", function(e) {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='o_cp_id[]']").val();
        var price = parseInt($el.find("input[name='o_cp_prc[]']").val());
        var subj = $el.find("input[name='o_cp_subj[]']").val();
        var send_cost = $("input[name=od_send_cost]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        var od_price = parseInt($("input[name=org_od_price]").val()) - item_coupon;

        if(price == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        if(od_price - price <= 0) {
            alert("쿠폰할인금액이 주문금액보다 크므로 쿠폰을 적용할 수 없습니다.");
            return false;
        }

        $("input[name=sc_cp_id]").val("");
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();

        $("input[name=od_price]").val(od_price - price);
        $("input[name=od_cp_id]").val(cp_id);
        $("input[name=od_coupon]").val(price);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(gnucommerce.number_format(String(price)));
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰변경").focus();
        if(!$("#od_coupon_cancel").size())
            $("#od_coupon_btn").after("<button type=\"button\" id=\"od_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $(document).on("click", "#od_coupon_close", function(e) {
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").focus();
    });

    $(document).on("click", "#od_coupon_cancel", function(e) {
        var org_price = $("input[name=org_od_price]").val();
        var item_coupon = parseInt($("input[name=item_coupon]").val());
        $("input[name=od_price]").val(org_price - item_coupon);
        $("input[name=sc_cp_id]").val("");
        $("input[name=od_coupon]").val(0);
        $("input[name=od_send_coupon]").val(0);
        $("#od_cp_price").text(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#od_coupon_frm").remove();
        $("#od_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
        $("#sc_coupon_btn").text("쿠폰적용");
        $("#sc_coupon_cancel").remove();
    });

    $("#sc_coupon_btn").click(function(e) {
        e.preventDefault();

        $("#sc_coupon_frm").remove();
        var $this = $(this);
        var price = parseInt($("input[name=od_price]").val());
        var send_cost = parseInt($("input[name=od_send_cost]").val());

        $.post(
            gnucommerce.ajaxurl,
            { action : 'gc_ordersendcostcoupon', price: price, send_cost: send_cost },
            function(data) {
                $this.after(data);
            }
        );
    });

    $(document).on("click", ".sc_cp_apply", function(e) {
        var $el = $(this).closest("tr");
        var cp_id = $el.find("input[name='s_cp_id[]']").val();
        var price = parseInt($el.find("input[name='s_cp_prc[]']").val());
        var subj = $el.find("input[name='s_cp_subj[]']").val();
        var send_cost = parseInt($("input[name=od_send_cost]").val());

        if(parseInt(price) == 0) {
            if(!confirm(subj+"쿠폰의 할인 금액은 "+price+"원입니다.\n쿠폰을 적용하시겠습니까?")) {
                return false;
            }
        }

        $("input[name=sc_cp_id]").val(cp_id);
        $("input[name=od_send_coupon]").val(price);
        $("#sc_cp_price").text(gnucommerce.number_format(String(price)));
        calculate_order_price();
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").text("쿠폰변경").focus();
        if(!$("#sc_coupon_cancel").size())
            $("#sc_coupon_btn").after("<button type=\"button\" id=\"sc_coupon_cancel\" class=\"btn_frmline\">쿠폰취소</button>");
    });

    $(document).on("click", "#sc_coupon_close", function(e) {
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").focus();
    });

    $(document).on("click", "#sc_coupon_cancel", function(e) {
        $("input[name=od_send_coupon]").val(0);
        $("#sc_cp_price").text(0);
        calculate_order_price();
        $("#sc_coupon_frm").remove();
        $("#sc_coupon_btn").text("쿠폰적용").focus();
        $(this).remove();
    });

    $("#od_b_addr2").focus(function() {
        var zip = $("#od_b_zip").val().replace(/[^0-9]/g, "");
        if(zip == "")
            return false;

        var code = String(zip);

        if(zipcode == code)
            return false;

        zipcode = code;
        calculate_sendcost(code);
    });

    $("#od_settle_bank").on("click", function() {
        $("[name=od_deposit_name]").val( $("[name=od_name]").val() );
        $("#settle_bank").show();
    });

    $("#od_settle_iche,#od_settle_card,#od_settle_vbank,#od_settle_hp,#od_settle_easy_pay,#od_settle_kakaopay").bind("click", function() {
        $("#settle_bank").hide();
    });

    // 배송지선택
    $("input[name=ad_sel_addr]").on("click", function() {
        var addr = $(this).val().split(String.fromCharCode(30));

        if (addr[0] == "same") {
            if($(this).is(":checked"))
                gumae2baesong(true);
            else
                gumae2baesong(false);
        } else {
            if(addr[0] == "new") {
                for(i=0; i<10; i++) {
                    addr[i] = "";
                }
            }

            var f = document.forderform;
            f.od_b_name.value        = addr[0];
            f.od_b_tel.value         = addr[1];
            f.od_b_hp.value          = addr[2];
            f.od_b_zip.value         = addr[3] + addr[4];
            f.od_b_addr1.value       = addr[5];
            f.od_b_addr2.value       = addr[6];
            f.od_b_addr3.value       = addr[7];
            f.od_b_addr_jibeon.value = addr[8];
            f.ad_subject.value       = addr[9];

            var zip1 = addr[3].replace(/[^0-9]/g, "");
            var zip2 = addr[4].replace(/[^0-9]/g, "");

            if(zip1 != "" && zip2 != "") {
                var code = String(zip1) + String(zip2);

                if(zipcode != code) {
                    zipcode = code;
                    calculate_sendcost(code);
                }
            }
        }
    });

    // 배송지목록
    $("#order_address").on("click", function(e) {
        e.preventDefault();
        
        var $othis = $(this);
        $othis.popModal({
            html : function(callback) {
                var ajax_var = jQuery.ajax({
                    url: gnucommerce.ajaxurl,
                    data: {
                        'action' : 'gc_get_user_orderaddress'
                    },
                    dataType   : 'html', // xml, html, script, json
                    cache: false,
                    success:function(data, status, xhr){
                        $othis.next(".popModal").css({"max-width":"100%"});
                        callback(data);
                    },
                    error : function(request, status, error){
                        //alert('false ajax :'+request.responseText);
                    }
                }); // end of ajax
            }
        });
    });
})(jQuery);

function coupon_cancel($el)
{
    (function($){
        var $dup_sell_el = $el.find(".total_price");
        var $dup_price_el = $el.find("input[name^=cp_price]");
        var org_sell_price = $el.find("input[name^=it_price]").val();

        $dup_sell_el.text(gnucommerce.number_format(String(org_sell_price)));
        $dup_price_el.val(0);
        $el.find("input[name^=cp_id]").val("");
    })(jQuery);
}

function calculate_total_price()
{
    (function($){
        var $it_prc = $("input[name^=it_price]");
        var $cp_prc = $("input[name^=cp_price]");
        var tot_sell_price = sell_price = tot_cp_price = 0;
        var it_price, cp_price, it_notax;
        var tot_mny = comm_tax_mny = comm_vat_mny = comm_free_mny = tax_mny = vat_mny = 0;
        var send_cost = parseInt($("input[name=od_send_cost]").val());

        $it_prc.each(function(index) {
            it_price = parseInt($(this).val());
            cp_price = parseInt($cp_prc.eq(index).val());
            sell_price += it_price;
            tot_cp_price += cp_price;
        });

        tot_sell_price = sell_price - tot_cp_price + send_cost;

        $("#ct_tot_coupon").text(gnucommerce.number_format(String(tot_cp_price))+" 원");
        $("#ct_tot_price").text(gnucommerce.number_format(String(tot_sell_price))+" 원");

        $("input[name=good_mny]").val(tot_sell_price);
        $("input[name=od_price]").val(sell_price - tot_cp_price);
        $("input[name=item_coupon]").val(tot_cp_price);
        $("input[name=od_coupon]").val(0);
        $("input[name=od_send_coupon]").val(0);
        <?php if($oc_cnt > 0) { ?>
        $("input[name=od_cp_id]").val("");
        $("#od_cp_price").text(0);
        if($("#od_coupon_cancel").size()) {
            $("#od_coupon_btn").text("쿠폰적용");
            $("#od_coupon_cancel").remove();
        }
        <?php } ?>
        <?php if($sc_cnt > 0) { ?>
        $("input[name=sc_cp_id]").val("");
        $("#sc_cp_price").text(0);
        if($("#sc_coupon_cancel").size()) {
            $("#sc_coupon_btn").text("쿠폰적용");
            $("#sc_coupon_cancel").remove();
        }
        <?php } ?>
        $("input[name=od_temp_mileage]").val(0);
        <?php if($temp_mileage > 0 && is_user_logged_in()) { ?>
        calculate_temp_point();
        <?php } ?>
        calculate_order_price();
    })(jQuery);
}

function calculate_order_price()
{
    (function($){
        var sell_price = parseInt($("input[name=od_price]").val());
        var send_cost = parseInt($("input[name=od_send_cost]").val());
        var send_cost2 = parseInt($("input[name=od_send_cost2]").val());
        var send_coupon = parseInt($("input[name=od_send_coupon]").val());
        var tot_price = sell_price + send_cost + send_cost2 - send_coupon;

        $("input[name=good_mny]").val(tot_price);
        $("#od_tot_price").text(gnucommerce.number_format(String(tot_price)));
    })(jQuery);
    <?php if($temp_mileage > 0 && is_user_logged_in()) { ?>
        calculate_temp_point();
    <?php } ?>
}

function calculate_temp_point()
{
    (function($){
        var sell_price = parseInt($("input[name=od_price]").val());
        var mb_point = parseInt(<?php echo $member['mb_mileage']; ?>);  //회원적립금
        var max_point = parseInt(<?php echo $config['de_settle_max_mileage']; ?>);
        var point_unit = parseInt(<?php echo $config['de_settle_mileage_unit']; ?>);
        temp_point = max_point;

        if(temp_point > sell_price)
            temp_point = sell_price;

        if(temp_point > mb_point)
            temp_point = mb_point;

        temp_point = parseInt(temp_point / point_unit) * point_unit;

        $("#use_max_mileage").text("최대 "+gnucommerce.number_format(String(temp_point))+"원");
        $("input[name=max_temp_mileage]").val(temp_point);
    })(jQuery);
}

function calculate_sendcost(code)
{
    jQuery.post(
        gnucommerce.ajaxurl,
        {action: 'gc_ordersendcost', zipcode: code },
        function(data) {
            jQuery("input[name=od_send_cost2]").val(data);
            jQuery("#od_send_cost2").text(gnucommerce.number_format(String(data)));

            calculate_order_price();
        }
    );
}

function gc_calculate_tax()
{
    var $it_prc = $("input[name^=it_price]");
    var $cp_prc = $("input[name^=cp_price]");
    var sell_price = tot_cp_price = 0;
    var it_price, cp_price, it_notax;
    var tot_mny = comm_free_mny = tax_mny = vat_mny = 0;
    var send_cost = parseInt($("input[name=od_send_cost]").val());
    var send_cost2 = parseInt($("input[name=od_send_cost2]").val());
    var od_coupon = parseInt($("input[name=od_coupon]").val());
    var send_coupon = parseInt($("input[name=od_send_coupon]").val());
    var temp_point = 0;

    $it_prc.each(function(index) {
        it_price = parseInt($(this).val());
        cp_price = parseInt($cp_prc.eq(index).val());
        sell_price += it_price;
        tot_cp_price += cp_price;
        it_notax = $("input[name^=it_notax]").eq(index).val();
        if(it_notax == "1") {
            comm_free_mny += (it_price - cp_price);
        } else {
            tot_mny += (it_price - cp_price);
        }
    });

    if($("input[name=od_temp_mileage]").size())
        temp_point = parseInt($("input[name=od_temp_mileage]").val());

    tot_mny += (send_cost + send_cost2 - od_coupon - send_coupon - temp_point);
    if(tot_mny < 0) {
        comm_free_mny = comm_free_mny + tot_mny;
        tot_mny = 0;
    }

    tax_mny = Math.round(tot_mny / 1.1);
    vat_mny = tot_mny - tax_mny;
    $("input[name=comm_tax_mny]").val(tax_mny);
    $("input[name=comm_vat_mny]").val(vat_mny);
    $("input[name=comm_free_mny]").val(comm_free_mny);
}

function gc_orderfield_check(f){

    gnucommerce.errmsg = "";
    gnucommerce.errfld = "";
    var deffld = "";

    gnucommerce.check_field(f.od_name, "주문하시는 분 이름을 입력하십시오.");
    if (typeof(f.od_pwd) != 'undefined')
    {
        gnucommerce.clear_field(f.od_pwd);
        if( (f.od_pwd.value.length<3) || (f.od_pwd.value.search(/([^A-Za-z0-9]+)/)!=-1) )
            gnucommerce.error_field(f.od_pwd, "회원이 아니신 경우 주문서 조회시 필요한 비밀번호를 3자리 이상 입력해 주십시오.");
    }
    gnucommerce.check_field(f.od_tel, "주문하시는 분 전화번호를 입력하십시오.");
    gnucommerce.check_field(f.od_addr1, "주소검색을 이용하여 주문하시는 분 주소를 입력하십시오.");
    //check_field(f.od_addr2, " 주문하시는 분의 상세주소를 입력하십시오.");
    gnucommerce.check_field(f.od_zip, "");

    gnucommerce.clear_field(f.od_email);
    if(f.od_email.value=='' || f.od_email.value.search(/(\S+)@(\S+)\.(\S+)/) == -1)
        error_field(f.od_email, "E-mail을 바르게 입력해 주십시오.");

    if (typeof(f.od_hope_date) != "undefined")
    {
        gnucommerce.clear_field(f.od_hope_date);
        if (!f.od_hope_date.value)
            gnucommerce.error_field(f.od_hope_date, "희망배송일을 선택하여 주십시오.");
    }

    gnucommerce.check_field(f.od_b_name, "받으시는 분 이름을 입력하십시오.");
    gnucommerce.check_field(f.od_b_tel, "받으시는 분 전화번호를 입력하십시오.");
    gnucommerce.check_field(f.od_b_addr1, "주소검색을 이용하여 받으시는 분 주소를 입력하십시오.");
    gnucommerce.check_field(f.od_b_zip, "");

    var od_settle_bank = document.getElementById("od_settle_bank");
    if (od_settle_bank) {
        if (od_settle_bank.checked) {
            gnucommerce.check_field(f.od_bank_account, "계좌번호를 선택하세요.");
            gnucommerce.check_field(f.od_deposit_name, "입금자명을 입력하세요.");
        }
    }

    // 배송비를 받지 않거나 더 받는 경우 아래식에 + 또는 - 로 대입
    f.od_send_cost.value = parseInt(f.od_send_cost.value);

    if (gnucommerce.errmsg)
    {
        alert(gnucommerce.errmsg);
        gnucommerce.errfld.focus();
        return false;
    }

    var settle_case = document.getElementsByName("od_settle_case");
    var settle_check = false;

    for (i=0; i<settle_case.length; i++)
    {
        if (settle_case[i].checked)
        {
            settle_check = true;
            settle_method = settle_case[i].value;
            break;
        }
    }

    if (!settle_check)
    {
        alert("결제방식을 선택하십시오.");
        return false;
    }

    return true;
}

// 결제체크
function gc_payment_check(f)
{
    var od_price = parseInt(f.od_price.value);
    var send_cost = parseInt(f.od_send_cost.value);
    var send_cost2 = parseInt(f.od_send_cost2.value);
    var send_coupon = parseInt(f.od_send_coupon.value);
    temp_point = 0;

    var max_point = 0;
    if (typeof(f.max_temp_mileage) != "undefined")
        max_point  = parseInt(f.max_temp_mileage.value);

    if (typeof(f.od_temp_mileage) != "undefined") {
        if (f.od_temp_mileage.value)
        {
            var point_unit = parseInt(<?php echo $config['de_settle_mileage_unit']; ?>);
            temp_point = parseInt(f.od_temp_mileage.value);

            if (temp_point < 0) {
                alert("적립금을 0 이상 입력하세요.");
                f.od_temp_mileage.select();
                return false;
            }

            if (temp_point > od_price) {
                alert("상품 주문금액(배송비 제외) 보다 많이 적립금결제할 수 없습니다.");
                f.od_temp_mileage.select();
                return false;
            }

            if (temp_point > max_point) {
                alert(max_point + "점 이상 결제할 수 없습니다.");
                f.od_temp_mileage.select();
                return false;
            }

            if (parseInt(parseInt(temp_point / point_unit) * point_unit) != temp_point) {
                alert("적립금를 "+String(point_unit)+"점 단위로 입력하세요.");
                f.od_temp_mileage.select();
                return false;
            }

            // pg 결제 금액에서 적립금 금액 차감
            if(settle_method != "<?php echo gc_get_stype_names('banktransfer'); ?>") {
                f.good_mny.value = od_price + send_cost + send_cost2 - send_coupon - temp_point;
            }
        }
    }

    var tot_price = od_price + send_cost + send_cost2 - send_coupon - temp_point;

    if (document.getElementById("od_settle_iche")) {
        if (document.getElementById("od_settle_iche").checked) {
            if (tot_price < 150) {
                alert("계좌이체는 150원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_card")) {
        if (document.getElementById("od_settle_card").checked) {
            if (tot_price < 1000) {
                alert("신용카드는 1000원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    if (document.getElementById("od_settle_hp")) {
        if (document.getElementById("od_settle_hp").checked) {
            if (tot_price < 350) {
                alert("휴대폰은 350원 이상 결제가 가능합니다.");
                return false;
            }
        }
    }

    <?php if($config['de_tax_flag_use']) { ?>
    gc_calculate_tax();
    <?php } ?>

    return true;
}

function gc_forderform_check(f)    //pc결제에서만 체크한다.( 모바일에서는 이 부분을 체크하지 않도록 한다. )
{
    // 재고체크
    var stock_msg = gnucommerce.order_stock_check();
    if(stock_msg != "") {
        alert(stock_msg);
        return false;
    }

    // 필드체크
    if(!gc_orderfield_check(f))
        return false;

    // 금액체크
    if(!gc_payment_check(f))
        return false;

    // 카카오페이 지불
    if(settle_method == "KAKAOPAY") {
        <?php if($config['de_tax_flag_use']) { ?>
        f.SupplyAmt.value = parseInt(f.comm_tax_mny.value) + parseInt(f.comm_free_mny.value);
        f.GoodsVat.value  = parseInt(f.comm_vat_mny.value);
        <?php } ?>
        getTxnId(f);
        return false;
    }

    // pay_method 설정
    <?php if($config['de_pg_service'] == 'kcp') { ?>
    f.site_cd.value = f.def_site_cd.value;
    f.payco_direct.value = "";
    switch(settle_method)
    {
        case "<?php echo gc_get_stype_names('accounttransfer'); ?>":    // 계좌이체
            f.pay_method.value   = "010000000000";
            break;
        case "<?php echo gc_get_stype_names('virtualaccount'); ?>":    //가상계좌
            f.pay_method.value   = "001000000000";
            break;
        case "<?php echo gc_get_stype_names('phonepayment'); ?>":  //휴대폰
            f.pay_method.value   = "000010000000";
            break;
        case "<?php echo gc_get_stype_names('creditcard'); ?>":    //신용카드
            f.pay_method.value   = "100000000000";
            break;
        case "<?php echo gc_get_stype_names('easypayment'); ?>":   //간편결제
            <?php if($config['de_card_test']) { ?>
            f.site_cd.value      = "S6729";
            <?php } ?>
            f.pay_method.value   = "100000000000";
            f.payco_direct.value = "Y";
            break;
        default:
            f.pay_method.value   = "<?php echo gc_get_stype_names('banktransfer'); ?>" //무통장;
            break;
    }
    <?php } else if($config['de_pg_service'] == 'lg') { ?>
    f.LGD_EASYPAY_ONLY.value = "";
    if(typeof f.LGD_CUSTOM_USABLEPAY === "undefined") {
        var input = document.createElement("input");
        input.setAttribute("type", "hidden");
        input.setAttribute("name", "LGD_CUSTOM_USABLEPAY");
        input.setAttribute("value", "");
        f.LGD_EASYPAY_ONLY.parentNode.insertBefore(input, f.LGD_EASYPAY_ONLY);
    }

    switch(settle_method)
    {
        case "<?php echo gc_get_stype_names('accounttransfer'); ?>":   //계좌이체
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0030";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0030";
            break;
        case "<?php echo gc_get_stype_names('virtualaccount'); ?>":    //가상계좌
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0040";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0040";
            break;
        case "<?php echo gc_get_stype_names('phonepayment'); ?>":  //휴대폰
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0060";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0060";
            break;
        case "<?php echo gc_get_stype_names('creditcard'); ?>":    //신용카드
            f.LGD_CUSTOM_FIRSTPAY.value = "SC0010";
            f.LGD_CUSTOM_USABLEPAY.value = "SC0010";
            break;
        case "<?php echo gc_get_stype_names('easypayment'); ?>":   //간편결제
            var elm = f.LGD_CUSTOM_USABLEPAY;
            if(elm.parentNode)
                elm.parentNode.removeChild(elm);
            f.LGD_EASYPAY_ONLY.value = "PAYNOW";
            break;
        default:
            f.LGD_CUSTOM_FIRSTPAY.value = "<?php echo gc_get_stype_names('banktransfer'); ?>"; //무통장
            break;
    }
    <?php } else if($config['de_pg_service'] == 'inicis') { ?>
    switch(settle_method)
    {
        case "<?php echo gc_get_stype_names('accounttransfer'); ?>":   //계좌이체
            f.gopaymethod.value = "onlydbank";
            break;
        case "<?php echo gc_get_stype_names('virtualaccount'); ?>":    //가상계좌
            f.gopaymethod.value = "onlyvbank";
            break;
        case "<?php echo gc_get_stype_names('phonepayment'); ?>":  //휴대폰
            f.gopaymethod.value = "onlyhpp";
            break;
        case "<?php echo gc_get_stype_names('creditcard'); ?>":    //신용카드
            f.gopaymethod.value = "onlycard";
            break;
        case "<?php echo gc_get_stype_names('easypayment'); ?>":    //간편결제
            f.gopaymethod.value = "onlykpay";
            break;
        default:
            f.gopaymethod.value = "<?php echo gc_get_stype_names('banktransfer'); ?>"; //무통장
            break;
    }
    <?php } ?>

    // 결제정보설정
    <?php if($config['de_pg_service'] == 'kcp') { ?>
    f.buyr_name.value = f.od_name.value;
    f.buyr_mail.value = f.od_email.value;
    f.buyr_tel1.value = f.od_tel.value;
    f.buyr_tel2.value = f.od_hp.value;
    f.rcvr_name.value = f.od_b_name.value;
    f.rcvr_tel1.value = f.od_b_tel.value;
    f.rcvr_tel2.value = f.od_b_hp.value;
    f.rcvr_mail.value = f.od_email.value;
    f.rcvr_zipx.value = f.od_b_zip.value;
    f.rcvr_add1.value = f.od_b_addr1.value;
    f.rcvr_add2.value = f.od_b_addr2.value;

    if(f.pay_method.value != "<?php echo gc_get_stype_names('banktransfer'); ?>") {    //무통장
        return jsf__pay( f );
    } else {
        return true;
    }
    <?php } ?>
    <?php if($config['de_pg_service'] == 'lg') { ?>
    f.LGD_BUYER.value = f.od_name.value;
    f.LGD_BUYEREMAIL.value = f.od_email.value;
    f.LGD_BUYERPHONE.value = f.od_hp.value;
    f.LGD_AMOUNT.value = f.good_mny.value;
    f.LGD_RECEIVER.value = f.od_b_name.value;
    f.LGD_RECEIVERPHONE.value = f.od_b_hp.value;
    <?php if($config['de_escrow_use']) { ?>
    f.LGD_ESCROW_ZIPCODE.value = f.od_b_zip.value;
    f.LGD_ESCROW_ADDRESS1.value = f.od_b_addr1.value;
    f.LGD_ESCROW_ADDRESS2.value = f.od_b_addr2.value;
    f.LGD_ESCROW_BUYERPHONE.value = f.od_hp.value;
    <?php } ?>
    <?php if($config['de_tax_flag_use']) { ?>
    f.LGD_TAXFREEAMOUNT.value = f.comm_free_mny.value;
    <?php } ?>

    if(f.LGD_CUSTOM_FIRSTPAY.value != "<?php echo gc_get_stype_names('banktransfer'); ?>") {   //무통장
          launchCrossPlatform(f);
    } else {
        f.submit();
    }
    <?php } ?>
    <?php if($config['de_pg_service'] == 'inicis') { ?>
    f.buyername.value   = f.od_name.value;
    f.buyeremail.value  = f.od_email.value;
    f.buyertel.value    = f.od_hp.value ? f.od_hp.value : f.od_tel.value;
    f.recvname.value    = f.od_b_name.value;
    f.recvtel.value     = f.od_b_hp.value ? f.od_b_hp.value : f.od_b_tel.value;
    f.recvpostnum.value = f.od_b_zip.value;
    f.recvaddr.value    = f.od_b_addr1.value + " " +f.od_b_addr2.value;

    if(f.gopaymethod.value != "<?php echo gc_get_stype_names('banktransfer'); ?>") {   //무통장
        if(!set_encrypt_data(f))
            return false;

        return pay(f);
    } else {
        return true;
    }
    <?php } ?>
}

// 구매자 정보와 동일합니다.
function gumae2baesong(checked) {
    var f = document.forderform;

    if(checked == true) {
        f.od_b_name.value = f.od_name.value;
        f.od_b_tel.value  = f.od_tel.value;
        f.od_b_hp.value   = f.od_hp.value;
        f.od_b_zip.value  = f.od_zip.value;
        f.od_b_addr1.value = f.od_addr1.value;
        f.od_b_addr2.value = f.od_addr2.value;
        f.od_b_addr3.value = f.od_addr3.value;
        f.od_b_addr_jibeon.value = f.od_addr_jibeon.value;

        calculate_sendcost(String(f.od_b_zip.value));
    } else {
        f.od_b_name.value = "";
        f.od_b_tel.value  = "";
        f.od_b_hp.value   = "";
        f.od_b_zip.value  = "";
        f.od_b_addr1.value = "";
        f.od_b_addr2.value = "";
        f.od_b_addr3.value = "";
        f.od_b_addr_jibeon.value = "";
    }
}

<?php if ($config['de_hope_date_use']) { ?>
(function($){
    $("#od_hope_date").datepicker({ changeMonth: true, changeYear: true, dateFormat: "yy-mm-dd", showButtonPanel: true, yearRange: "c-99:c+99", minDate: "+<?php echo (int)$config['de_hope_date_after']; ?>d;", maxDate: "+<?php echo (int)$config['de_hope_date_after'] + 6; ?>d;" });
})(jQuery);
<?php } ?>
</script>
<?php
    global $wp_scripts;
    $wp_scripts->done[] = 'gc_orderform_php_js_load';
    }   //end if
}   //end function gc_orderform_php_js


// 결제대행사별 코드 include (스크립트 실행)
if( GC_IS_MOBILE ){    // 모바일이면
    require_once(GC_SHOP_DIR_PATH.'/'.$config['de_pg_service'].'/orderform.5.php');
}
?>