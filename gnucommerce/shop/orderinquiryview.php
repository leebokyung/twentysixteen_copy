<?php
if( !defined('GC_NAME') ) exit;

// 불법접속을 할 수 없도록 세션에 아무값이나 저장하여 hidden 으로 넘겨서 다음 페이지에서 비교함
$token = md5(uniqid(rand(), true));
gc_set_session("ss_token", $token);

if( isset($_GET['od_id']) ){
    $_REQUEST['order_id'] = $_GET['od_id'];
}

$order_id = isset($_REQUEST['order_id']) ? preg_replace('/[^0-9]/', '', $_REQUEST['order_id']) : '';

if ( ! get_current_user_id() ) {
    if (gc_get_session('ss_orderview_uid') != $_GET['uid'])
        gc_alert("직접 링크로는 주문서 조회가 불가합니다.\\n\\n주문조회 화면을 통하여 조회하시기 바랍니다.", home_url() );
}

$od = apply_filters('gc_orderinquiryview_data', gc_get_order_data( $order_id ));

$is_admin = GC_VAR()->is_user_admin;

if(get_current_user_id() && !$is_admin){

    if( (int) $od['mb_id'] !== get_current_user_id() ){
        gc_alert("주문서를 볼수있는 권한이 없습니다");
    }
}

/*
$sql = $wpdb->prepare("SELECT * FROM {$wpdb->posts} left join {$wpdb->postmeta} on ( $wpdb->posts.ID = $wpdb->postmeta.post_id ) where $wpdb->postmeta.meta_key = 'od_id' and meta_value = %.0f", $od_id);

//$sql = $wpdb->prepare("select * from {$gc['shop_order_table']} where od_id = %.0f ", $od_id);

$is_admin = GC_VAR()->is_user_admin;

if(get_current_user_id() && !$is_admin)
    $sql .= $wpdb->prepare(" and $wpdb->postmeta.mb_id = '%s' ", get_current_user_id());

$od = $wpdb->get_row($sql, ARRAY_A);
*/

if (!$od['od_id'] || (! get_current_user_id() && md5($od['od_id'].$od['od_time'].$od['od_ip']) != gc_get_session('ss_orderview_uid'))) {
    echo "조회하실 주문서가 없습니다.";
    //gc_alert("조회하실 주문서가 없습니다.", home_url() );
}

// 결제방법
$settle_case = $od['od_settle_case'];
$tot_point = 0;

// LG 현금영수증 JS
if($od['od_pg'] == 'lg') {
    if($config['de_card_test']) {
        $lg_receipt_js = 'http://pgweb.uplus.co.kr:7085/WEB_SERVER/js/receipt_link.js';
    } else {
        $lg_receipt_js = 'http://pgweb.uplus.co.kr/WEB_SERVER/js/receipt_link.js';
    }
    wp_enqueue_script('gc_lg_receipt_js', apply_filters('gc_lg_receipt_js', $lg_receipt_js) );
}
?>

<!-- 주문상세내역 시작 { -->
<div id="sod_fin">

    <div id="sod_fin_no">주문번호 <strong><?php echo $order_id; ?></strong></div>

    <section id="sod_fin_list">
        <h2>주문하신 상품</h2>

        <?php
        $st_count1 = $st_count2 = 0;
        $custom_cancel = false;

        $sql = $wpdb->prepare(" select it_id, it_name, ct_send_cost, it_sc_type
                    from {$gc['shop_cart_table']}
                    where od_id = %.0f
                    group by it_id
                    order by ct_id ", $order_id);
        $rows = $wpdb->get_results($sql, ARRAY_A);
        ?>
        <ul id="sod_list_inq" class="sod_list">
            <?php
            foreach($rows as $row){
                $image_width = 50;
                $image_height = 50;
                $image = gc_get_it_image($row['it_id'], $image_width, $image_height);

                $sql = $wpdb->prepare(" select ct_id, it_name, ct_option, ct_qty, ct_price, ct_point, ct_status, io_type, io_price
                            from {$gc['shop_cart_table']}
                            where od_id = %.0f
                              and it_id = %.0f
                            order by io_type asc, ct_id asc ", $order_id, $row['it_id']);

                $res = $wpdb->get_results($sql, ARRAY_A);
                $rowspan = count($res) + 1;

                // 합계금액 계산
                $sql = $wpdb->prepare(" select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
                                SUM(ct_qty) as qty
                            from {$gc['shop_cart_table']}
                            where it_id = %.0f
                              and od_id = %.0f ", $row['it_id'], $order_id);

                $sum = $wpdb->get_var($sql);

                // 배송비
                switch($row['ct_send_cost'])
                {
                    case 1:
                        $ct_send_cost = '착불';
                        break;
                    case 2:
                        $ct_send_cost = '무료';
                        break;
                    default:
                        $ct_send_cost = '선불';
                        break;
                }

                // 조건부무료
                if($row['it_sc_type'] == 2) {
                    $sendcost = gc_get_item_sendcost($row['it_id'], $sum['price'], $sum['qty'], $order_id);

                    if($sendcost == 0)
                        $ct_send_cost = '무료';
                }
            ?>
            <li class="sod_li">
                <div class="li_name_od">
                    <a href="<?php echo gc_get_item_url($row['it_id']); ?>"><strong><?php echo $row['it_name']; ?></strong></a>
                </div>
            <?php
                $k = 0;
                foreach($res as $opt){
                    if( empty($opt) ) continue;
                    if($opt['io_type'])
                        $opt_price = $opt['io_price'];
                    else
                        $opt_price = $opt['ct_price'] + $opt['io_price'];

                    $sell_price = $opt_price * $opt['ct_qty'];
                    $point = $opt['ct_point'] * $opt['ct_qty'];
            ?>
                <div class="li_opt"><?php echo $opt['ct_option']; ?></div>
                <div class="li_prqty">
                    <span class="prqty_price li_prqty_sp"><span>판매가 </span><?php echo gc_number_format($opt_price); ?></span>
                    <span class="prqty_qty li_prqty_sp"><span>수량 </span><?php echo number_format($opt['ct_qty']); ?></span>
                    <span class="prqty_sc li_prqty_sp"><span>배송비 </span><?php echo $ct_send_cost; ?></span>
                    <span class="prqty_stat li_prqty_sp"><span>상태 </span><?php echo gc_print_stype_names($opt['ct_status']); ?></span>
                </div>
                <div class="li_total" style="padding-left:<?php echo $image_width + 10; ?>px;height:auto !important;height:<?php echo $image_height; ?>px;min-height:<?php echo $image_height; ?>px">
                    <a href="<?php echo gc_get_item_url($row['it_id']); ?>" class="total_img"><?php echo $image; ?></a>
                    <span class="total_price total_span"><span>주문금액 </span><?php echo gc_number_format($sell_price); ?></span>
                    <span class="total_point total_span"><span>적립금 </span><?php echo gc_number_format($point); ?></span>
                </div>
            <?php
                    $tot_point       += $point;
                    $k++;
                    $st_count1++;
                    if($opt['ct_status'] == gc_get_stype_names('order'))
                        $st_count2++;
                }
            ?>

            </li>
            <?php
            }

            // 주문 상품의 상태가 모두 주문이면 고객 취소 가능
            if($st_count1 > 0 && $st_count1 == $st_count2)
                $custom_cancel = true;
            ?>
        </ul>

        <div id="sod_sts_wrap">
            <span class="sound_only">상품 상태 설명</span>
            <button type="button" id="sod_sts_explan_open" class="btn_frmline">상태설명보기</button>
            <div id="sod_sts_explan">
                <dl id="sod_fin_legend">
                    <dt>주문</dt>
                    <dd>주문이 접수되었습니다.</dd>
                    <dt>입금</dt>
                    <dd>입금(결제)이 완료 되었습니다.</dd>
                    <dt>준비</dt>
                    <dd>상품 준비 중입니다.</dd>
                    <dt>배송</dt>
                    <dd>상품 배송 중입니다.</dd>
                    <dt>완료</dt>
                    <dd>상품 배송이 완료 되었습니다.</dd>
                </dl>
                <button type="button" id="sod_sts_explan_close" class="btn_frmline">상태설명닫기</button>
            </div>
        </div>

        <?php
        // 총계 = 주문상품금액합계 + 배송비 - 상품할인 - 결제할인 - 배송비할인
        $tot_price = $od['od_cart_price'] + $od['od_send_cost'] + $od['od_send_cost2'];

        if( isset($od['od_cart_coupon']) ){
            $tot_price -= (int) $od['od_cart_coupon'];
        }
        if( isset($od['od_coupon']) ){
            $tot_price -= (int) $od['od_coupon'];
        }
        if( isset($od['od_send_coupon']) ){
            $tot_price -= (int) $od['od_send_coupon'];
        }
        if( isset($od['od_cancel_price']) ){
            $tot_price -= (int) $od['od_cancel_price'];
        }
        ?>

        <dl id="sod_bsk_tot">
            <dt class="sod_bsk_dvr">주문총액</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_cart_price']); ?> 원</strong></dd>

            <?php if( isset($od['od_cart_coupon']) && $od['od_cart_coupon'] > 0) { ?>
            <dt class="sod_bsk_dvr">상품할인</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_cart_coupon']); ?> 원</strong></dd>
            <?php } ?>

            <?php if(isset($od['od_coupon']) && $od['od_coupon'] > 0) { ?>
            <dt class="sod_bsk_dvr">결제할인</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_coupon']); ?> 원</strong></dd>
            <?php } ?>

            <?php if (isset($od['od_send_cost']) && $od['od_send_cost'] > 0) { ?>
            <dt class="sod_bsk_dvr">배송비</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_send_cost']); ?> 원</strong></dd>
            <?php } ?>

            <?php if(isset($od['od_send_coupon']) && $od['od_send_coupon'] > 0) { ?>
            <dt class="sod_bsk_dvr">배송비할인</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_send_coupon']); ?> 원</strong></dd>
            <?php } ?>

            <?php if (isset($od['od_send_cost2']) && $od['od_send_cost2'] > 0) { ?>
            <dt class="sod_bsk_dvr">추가배송비</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_send_cost2']); ?> 원</strong></dd>
            <?php } ?>

            <?php if (isset($od['od_cancel_price']) && $od['od_cancel_price'] > 0) { ?>
            <dt class="sod_bsk_dvr">취소금액</dt>
            <dd class="sod_bsk_dvr"><strong><?php echo gc_number_format($od['od_cancel_price']); ?> 원</strong></dd>
            <?php } ?>

            <dt class="sod_bsk_cnt">총계</dt>
            <dd class="sod_bsk_cnt"><strong><?php echo gc_number_format($tot_price); ?> 원</strong></dd>

            <dt class="sod_bsk_point">적립금</dt>
            <dd class="sod_bsk_point"><strong><?php echo gc_number_format($tot_point); ?> 원</strong></dd>
        </dl>
    </section>

    <div id="sod_fin_view">
        <h2>결제/배송 정보</h2>
        <?php
        $receipt_price  = $od['od_receipt_price'];
        if( isset($od['od_receipt_point']) ){
            $receipt_price += (int) $od['od_receipt_point'];
        }
        $cancel_price = 0;
        if( isset($od['od_cancel_price']) ){
            $cancel_price = $od['od_cancel_price'];
        }
        $misu = true;
        $misu_price = $tot_price - $receipt_price - $cancel_price;

        if ($misu_price == 0 && ($od['od_cart_price'] > $od['od_cancel_price'])) {
            $wanbul = " (완불)";
            $misu = false; // 미수금 없음
        }
        else
        {
            $wanbul = gc_display_price($receipt_price);
        }

        // 결제정보처리
        if($od['od_receipt_price'] > 0)
            $od_receipt_price = gc_display_price($od['od_receipt_price']);
        else
            $od_receipt_price = '아직 입금되지 않았거나 입금정보를 입력하지 못하였습니다.';

        $app_no_subj = '';
        $disp_bank = true;
        $disp_receipt = false;
        $easy_pay_name = '';    //변수 초기화

        if($od['od_settle_case'] == gc_get_stype_names('creditcard') || $od['od_settle_case'] == 'KAKAOPAY') { //신용카드
            $app_no_subj = '승인번호';
            $app_no = $od['od_app_no'];
            $disp_bank = false;
            $disp_receipt = true;
        } else if($od['od_settle_case'] == gc_get_stype_names('easypayment')) {    //간편결제
            $app_no_subj = '승인번호';
            $app_no = $od['od_app_no'];
            $disp_bank = false;
            switch($od['od_pg']) {
                case 'lg':
                    $easy_pay_name = 'PAYNOW';
                    break;
                case 'inicis':
                    $easy_pay_name = 'KPAY';
                    break;
                case 'kcp':
                    $easy_pay_name = 'PAYCO';
                    break;
                default:
                    break;
            }
        } else if($od['od_settle_case'] == gc_get_stype_names('phonepayment')) {  //휴대폰
            $app_no_subj = '휴대폰번호';
            $app_no = $od['od_bank_account'];
            $disp_bank = false;
            $disp_receipt = true;
        } else if($od['od_settle_case'] == gc_get_stype_names('virtualaccount') || $od['od_settle_case'] == gc_get_stype_names('accounttransfer')) {     //가상계좌, 계좌이체
            $app_no_subj = '거래번호';
            $app_no = $od['od_tno'];
        }
        ?>

        <section id="sod_fin_pay">
            <h3><?php _e('결제정보', GC_NAME); ?></h3>

            <div class="odf_tbl">
                <table>
                <colgroup>
                    <col class="grid_2">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><?php _e('주문번호', GC_NAME); ?></th>
                    <td><?php echo $order_id; ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('주문일시', GC_NAME); ?></th>
                    <td><span class="entry-date"><?php echo $od['od_time']; ?></span></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('결제방식', GC_NAME); ?></th>
                    <td><?php echo ($easy_pay_name ? $easy_pay_name.'('.gc_print_stype_names($od['od_settle_case']).')' : gc_print_stype_names($od['od_settle_case'])); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('결제금액', GC_NAME); ?></th>
                    <td><?php echo $od_receipt_price; ?></td>
                </tr>
                <?php
                if($od['od_receipt_price'] > 0)
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('결제일시', GC_NAME); ?></th>
                    <td><?php echo $od['od_receipt_time']; ?></td>
                </tr>
                <?php
                }

                // 승인번호, 휴대폰번호, 거래번호
                if($app_no_subj)
                {
                ?>
                <tr>
                    <th scope="row"><?php echo $app_no_subj; ?></th>
                    <td><?php echo $app_no; ?></td>
                </tr>
                <?php
                }

                // 계좌정보
                if($disp_bank)
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('입금자명', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_deposit_name']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('입금계좌', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_bank_account']); ?></td>
                </tr>
                <?php
                }

                if($disp_receipt) {
                ?>
                <tr>
                    <th scope="row"><?php _e('영수증', GC_NAME); ?></th>
                    <td>
                        <?php
                        if($od['od_settle_case'] == gc_get_stype_names('phonepayment'))    //휴대폰
                        {
                            if($od['od_pg'] == 'lg') {  //LGU
                                require_once GC_SHOP_DIR_PATH.'/settle_lg.inc.php';
                                $LGD_TID      = $od['od_tno'];
                                $LGD_MERTKEY  = $config['cf_lg_mert_key'];
                                $LGD_HASHDATA = md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);

                                $hp_receipt_script = 'showReceiptByTID(\''.$LGD_MID.'\', \''.$LGD_TID.'\', \''.$LGD_HASHDATA.'\');';
                            } else if($od['od_pg'] == 'inicis') {
                            //이니시스
                            $hp_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid='.$od['od_tno'].'&noMethod=1\',\'receipt\',\'width=430,height=700\');';
                            } else {    //kcp
                                $hp_receipt_script = 'window.open(\''.GC_BILL_RECEIPT_URL.'mcash_bill&tno='.$od['od_tno'].'&order_no='.$od['od_id'].'&trade_mony='.$od['od_receipt_price'].'\', \'winreceipt\', \'width=500,height=690,scrollbars=yes,resizable=yes\');';
                            }
                        ?>
                        <a href="javascript:;" onclick="<?php echo $hp_receipt_script; ?>"><?php _e('영수증 출력', GC_NAME); ?></a>
                        <?php
                        }

                        if($od['od_settle_case'] == gc_get_stype_names('creditcard'))  //신용카드
                        {
                            if($od['od_pg'] == 'lg') {  //LGU
                                require_once GC_SHOP_DIR_PATH.'/settle_lg.inc.php';
                                $LGD_TID      = $od['od_tno'];
                                $LGD_MERTKEY  = $config['cf_lg_mert_key'];
                                $LGD_HASHDATA = md5($LGD_MID.$LGD_TID.$LGD_MERTKEY);

                                $card_receipt_script = 'showReceiptByTID(\''.$LGD_MID.'\', \''.$LGD_TID.'\', \''.$LGD_HASHDATA.'\');';
                            } else if($od['od_pg'] == 'inicis') {
                                //이니시스
                                $card_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/mCmReceipt_head.jsp?noTid='.$od['od_tno'].'&noMethod=1\',\'receipt\',\'width=430,height=700\');';
                            } else {    //kcp
                                $card_receipt_script = 'window.open(\''.GC_BILL_RECEIPT_URL.'card_bill&tno='.$od['od_tno'].'&order_no='.$od['od_id'].'&trade_mony='.$od['od_receipt_price'].'\', \'winreceipt\', \'width=470,height=815,scrollbars=yes,resizable=yes\');';
                            }
                        ?>
                        <a href="javascript:;" onclick="<?php echo $card_receipt_script; ?>"><?php _e('영수증 출력', GC_NAME); ?></a>
                        <?php
                        }

                        if($od['od_settle_case'] == 'KAKAOPAY')
                        {
                            $card_receipt_script = 'window.open(\'https://mms.cnspay.co.kr/trans/retrieveIssueLoader.do?TID='.$od['od_tno'].'&type=0\', \'popupIssue\', \'toolbar=no,location=no,directories=no,status=yes,menubar=no,scrollbars=yes,resizable=yes,width=420,height=540\');';
                        ?>
                        <a href="javascript:;" onclick="<?php echo $card_receipt_script; ?>"><?php _e('영수증 출력', GC_NAME); ?></a>
                        <?php
                        }
                        ?>
                    <td>
                    </td>
                </tr>
                <?php
                }

                if ($od['od_receipt_point'] > 0)
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('적립금사용', GC_NAME); ?></th>
                    <td><?php echo gc_display_mileage($od['od_receipt_point']); ?></td>
                </tr>

                <?php
                }

                if ($od['od_refund_price'] > 0)
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('환불 금액', GC_NAME); ?></th>
                    <td><?php echo gc_display_price($od['od_refund_price']); ?></td>
                </tr>
                <?php
                }

                // 현금영수증 발급을 사용하는 경우에만
                if ($config['de_taxsave_use']) {
                    // 미수금이 없고 현금일 경우에만 현금영수증을 발급 할 수 있습니다.
                    if ($misu_price == 0 && $od['od_receipt_price'] && ($od['od_settle_case'] == gc_get_stype_names('banktransfer') || $od['od_settle_case'] == gc_get_stype_names('accounttransfer') || $od['od_settle_case'] == gc_get_stype_names('virtualaccount'))) {     //무통장, 계좌이체, 가상계좌
                ?>
                <tr>
                    <th scope="row"><?php _e('현금영수증', GC_NAME); ?></th>
                    <td>
                    <?php
                    if ($od['od_cash'])
                    {
                        if($od['od_pg'] == 'lg') {
                            require_once GC_SHOP_DIR_PATH.'/settle_lg.inc.php';
                            switch($od['od_settle_case']) {
                                case gc_get_stype_names('accounttransfer'):    //계좌이체
                                    $trade_type = 'BANK';
                                    break;
                                case gc_get_stype_names('virtualaccount'):    //가상계좌
                                    $trade_type = 'CAS';
                                    break;
                                default:
                                    $trade_type = 'CR';
                                    break;
                            }
                            $cash_receipt_script = 'javascript:showCashReceipts(\''.$LGD_MID.'\',\''.$od['od_id'].'\',\''.$od['od_casseqno'].'\',\''.$trade_type.'\',\''.$CST_PLATFORM.'\');';
                        } else if($od['od_pg'] == 'inicis') {
                            //이니시스
                            $cash = unserialize($od['od_cash_info']);
                            $cash_receipt_script = 'window.open(\'https://iniweb.inicis.com/DefaultWebApp/mall/cr/cm/Cash_mCmReceipt.jsp?noTid='.$cash['TID'].'&clpaymethod=22\',\'showreceipt\',\'width=380,height=540,scrollbars=no,resizable=no\');';
                        } else {    //kcp
                            require_once GC_SHOP_DIR_PATH.'/settle_kcp.inc.php';

                            $cash = maybe_unserialize($od['od_cash_info']);
                            $cash_receipt_script = 'window.open(\''.GC_CASH_RECEIPT_URL.$config['de_kcp_mid'].'&orderid='.$od['od_id'].'&bill_yn=Y&authno='.$cash['receipt_no'].'\', \'taxsave_receipt\', \'width=360,height=647,scrollbars=0,menus=0\');';
                        }
                    ?>
                        <a href="javascript:;" onclick="<?php echo $cash_receipt_script; ?>" class="btn_frmline"><?php _e('현금영수증 확인하기', GC_NAME); ?></a>
                    <?php
                    }
                    else
                    {
                    ?>
                        <a href="javascript:;" onclick="window.open('<?php echo add_query_arg(array('od_id'=>$order_id), gc_get_page_url('taxsave')); ?>', 'taxsave', 'width=550,height=400,scrollbars=1,menus=0');" class="btn_frmline"><?php _e('현금영수증을 발급하시려면 클릭하십시오.', GC_NAME); ?></a>
                    <?php } ?>
                    </td>
                </tr>
                <?php
                    }
                }
                ?>
                </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_orderer">
            <h3>주문하신 분</h3>

            <div class="odf_tbl">
                <table>
                <colgroup>
                    <col class="grid_2">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><?php _e('이름', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_name']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('전화번호', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_tel']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('핸드폰', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_hp']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('주소', GC_NAME); ?></th>
                    <td><?php echo gc_get_text(sprintf("(%s)", $od['od_zip']).' '.gc_print_address($od['od_addr1'], $od['od_addr2'], $od['od_addr3'], $od['od_addr_jibeon'])); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('이메일', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_email']); ?></td>
                </tr>
                </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_receiver">
            <h3>받으시는 분</h3>

            <div class="odf_tbl">
                <table>
                <colgroup>
                    <col class="grid_2">
                    <col>
                </colgroup>
                <tbody>
                <tr>
                    <th scope="row"><?php _e('이름', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_b_name']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('전화번호', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_b_tel']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('핸드폰', GC_NAME); ?></th>
                    <td><?php echo gc_get_text($od['od_b_hp']); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('주소', GC_NAME); ?></th>
                    <td><?php echo gc_get_text(sprintf("(%s)", $od['od_b_zip']).' '.gc_print_address($od['od_b_addr1'], $od['od_b_addr2'], $od['od_b_addr3'], $od['od_b_addr_jibeon'])); ?></td>
                </tr>
                <?php
                // 희망배송일을 사용한다면
                if ($config['de_hope_date_use'])
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('희망배송일', GC_NAME); ?></th>
                    <td><?php echo substr($od['od_hope_date'],0,10).' ('.gc_get_yoil($od['od_hope_date']).')' ;?></td>
                </tr>
                <?php }
                if ($od['od_memo'])
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('전하실 말씀', GC_NAME); ?></th>
                    <td><?php echo gc_conv_content($od['od_memo'], 0); ?></td>
                </tr>
                <?php } ?>
                </tbody>
                </table>
            </div>
        </section>

        <section id="sod_fin_dvr">
            <h3><?php _e('배송정보', GC_NAME); ?></h3>

            <div class="odf_tbl">
                <table>
                <colgroup>
                    <col class="grid_2">
                    <col>
                </colgroup>
                <tbody>
                <?php
                if ($od['od_invoice'] && $od['od_delivery_company'])
                {
                ?>
                <tr>
                    <th scope="row"><?php _e('배송회사', GC_NAME); ?></th>
                    <td><?php echo $od['od_delivery_company']; ?> <?php echo gc_get_delivery_inquiry($od['od_delivery_company'], $od['od_invoice'], 'dvr_link'); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('운송장번호', GC_NAME); ?></th>
                    <td><?php echo $od['od_invoice']; ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php _e('배송일시', GC_NAME); ?></th>
                    <td><?php echo $od['od_invoice_time']; ?></td>
                </tr>
                <?php
                }
                else
                {
                ?>
                <tr>
                    <td class="empty_table" colspan="2"><?php _e('아직 배송하지 않았거나 배송정보를 입력하지 못하였습니다.', GC_NAME); ?></td>
                </tr>
                <?php
                }
                ?>
                </tbody>
                </table>
            </div>
        </section>
    </div>

    <section id="sod_fin_tot">
        <h2><?php _e('결제합계', GC_NAME); ?></h2>

        <ul>
            <li>
                <?php _e('총구매액', GC_NAME); ?>
                <strong><?php echo gc_display_price($tot_price); ?></strong>
            </li>
            <?php
            if ($misu_price > 0) {
            echo '<li>';
            echo __('미결제액', GC_NAME).PHP_EOL;
            echo '<strong>'.gc_display_price($misu_price).'</strong>';
            echo '</li>';
            }
            ?>
            <li id="alrdy">
                <?php _e('결제액', GC_NAME); ?>
                <strong><?php echo $wanbul; ?></strong>
            </li>
        </ul>
    </section>

    <section id="sod_fin_cancel">
        <h2><?php _e('주문취소', GC_NAME); ?></h2>
        <?php
        // 취소한 내역이 없다면
        if ($cancel_price == 0) {
            if ($custom_cancel) {
        ?>
        <button type="button" onclick="document.getElementById('sod_fin_cancelfrm').style.display='block';"><?php _e('주문 취소하기', GC_NAME); ?></button>

        <div id="sod_fin_cancelfrm">
            <form method="post" action="<?php echo gc_get_page_url('orderinquirycancel');?>" onsubmit="return fcancel_check(this);">
            <?php wp_nonce_field( 'gc_cancel_nonce', 'gc_nonce_field' ); ?>
            <input type="hidden" name="od_id"  value="<?php echo esc_attr($od['od_id']); ?>">
            <input type="hidden" name="token"  value="<?php echo esc_attr($token); ?>">

            <label for="cancel_memo">취소사유</label>
            <input type="text" name="cancel_memo" id="cancel_memo" required class="frm_input required" size="40" maxlength="100">
            <input type="submit" value="확인" class="btn_frmline">

            </form>
        </div>
        <?php
            }
        } else {
        ?>
        <p>주문 취소, 반품, 품절된 내역이 있습니다.</p>
        <?php } ?>
    </section>

    <?php if ($od['od_settle_case'] == gc_get_stype_names('virtualaccount') && $od['od_misu'] > 0 && $config['de_card_test'] && $is_admin && $od['od_pg'] == 'kcp') {
    preg_match("/\s{1}([^\s]+)\s?/", $od['od_bank_account'], $matchs);
    $deposit_no = trim($matchs[1]);
    ?>
    <p>관리자가 가상계좌 테스트를 한 경우에만 보입니다.</p>
    <div id="kcp_acc_test" class="odf_tbl">
        <form method="post" action="http://devadmin.kcp.co.kr/Modules/Noti/TEST_Vcnt_Noti_Proc.jsp" target="_blank">
        <table>
        <caption>모의입금처리</caption>
        <colgroup>
            <col class="grid_2">
            <col>
        </colgroup>
        <tbody>
        <tr>
            <th scope="col"><label for="e_trade_no">KCP 거래번호</label></th>
            <td><input type="text" name="e_trade_no" value="<?php echo $od['od_tno']; ?>"></td>
        </tr>
        <tr>
            <th scope="col"><label for="deposit_no">입금계좌</label></th>
            <td><input type="text" name="deposit_no" value="<?php echo esc_attr($deposit_no); ?>"></td>
        </tr>
        <tr>
            <th scope="col"><label for="req_name">입금자명</label></th>
            <td><input type="text" name="req_name" value="<?php echo $od['od_deposit_name']; ?>"></td>
        </tr>
        <tr>
            <th scope="col"><label for="noti_url">입금통보 URL</label></th>
            <td><input type="text" name="noti_url" value="<?php echo add_query_arg(array('virtualacc'=>'kcp'), gc_get_page_url('cart')); ?>"></td>
        </tr>
        </tbody>
        </table>
        <div id="sod_fin_test" class="btn_confirm">
            <input type="submit" value="입금통보 테스트" class="btn_submit">
        </div>
        </form>
    </div>
    <?php } ?>

</div>
<!-- } 주문상세내역 끝 -->

<script>
(function($) {
    $("#sod_sts_explan_open").on("click", function() {
        var $explan = $("#sod_sts_explan");
        if($explan.is(":animated"))
            return false;

        if($explan.is(":visible")) {
            $explan.slideUp(200);
            $("#sod_sts_explan_open").text("상태설명보기");
        } else {
            $explan.slideDown(200);
            $("#sod_sts_explan_open").text("상태설명닫기");
        }
    });

    $("#sod_sts_explan_close").on("click", function() {
        var $explan = $("#sod_sts_explan");
        if($explan.is(":animated"))
            return false;

        $explan.slideUp(200);
        $("#sod_sts_explan_open").text("상태설명보기");
    });
})(jQuery);

function fcancel_check(f)
{
    if(!confirm("주문을 정말 취소하시겠습니까?"))
        return false;

    var memo = f.cancel_memo.value;
    if(memo == "") {
        alert("취소사유를 입력해 주십시오.");
        return false;
    }

    return true;
}
</script>