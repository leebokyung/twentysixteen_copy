<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;

$config = GC_VAR()->config;
$gc = GC_VAR()->gc;

$result = false;

/*------------------------------------------------------------------------------
    ※ KCP 에서 가맹점의 결과처리 페이지로 데이터를 전송할 때에, 아래와 같은
       IP 에서 전송을 합니다. 따라서 가맹점측께서 전송받는 데이터에 대해 KCP
       에서 전송된 건이 맞는지 체크하는 부분을 구현할 때에, 아래의 IP 에 대해
       REMOTE ADDRESS 체크를 하여, 아래의 IP 이외의 다른 경로를 통해서 전송된
       데이터에 대해서는 결과처리를 하지 마시기 바랍니다.
------------------------------------------------------------------------------*/
if(!$config['de_card_test']) {

    switch ($_SERVER['REMOTE_ADDR']) {
        case '203.238.36.58' :
        case '203.238.36.160' :
        case '203.238.36.161' :
        case '203.238.36.173' :
        case '203.238.36.178' :
            break;
        default :   //허용 외 아이피이면
            $super_admin = gc_is_admin();
            $egpcs_str = "ENV[" . serialize($_ENV) . "] "
                       . "GET[" . serialize($_GET) . "]"
                       . "POST[" . serialize($_POST) . "]"
                       . "COOKIE[" . serialize($_COOKIE) . "]"
                       . "SESSION[" . serialize($_SESSION) . "]";

            $headers = 'From: '.__('경고', GC_NAME).' <waring>' . "\r\n";
            $title = __('올바르지 않은 접속 보고', GC_NAME);
            $content = "{$_SERVER['SCRIPT_NAME']} 에 {$_SERVER['REMOTE_ADDR']} 이 ".GC_TIME_YMDHIS." 에 접속을 시도하였습니다.\n\n";
            $content .= $egpcs_str;
            
            do_action('gc_kcp_virtual_account_error', $content, $title);

            /*
            add_filter( 'wp_mail_content_type', 'gc_set_html_content_type' );
            wp_mail( get_option( 'admin_email' ), $title, nl2br($content), $headers );
            remove_filter( 'wp_mail_content_type', 'gc_set_html_content_type' );
            */

            return;
    }
}   //end if

    /* ============================================================================== */
    /* =   PAGE : 공통 통보 PAGE                                                    = */
    /* = -------------------------------------------------------------------------- = */
    /* =   Copyright (c)  2006   KCP Inc.   All Rights Reserverd.                   = */
    /* ============================================================================== */
?>
<?php
    /* ============================================================================== */
    /* =   01. 공통 통보 페이지 설명(필독!!)                                        = */
    /* = -------------------------------------------------------------------------- = */
    /* =   에스크로 서비스의 경우, 가상계좌 입금 통보 데이터와 가상계좌 환불        = */
    /* =   통보 데이터, 구매확인/구매취소 통보 데이터, 배송시작 통보 데이터 등을    = */
    /* =   KCP 를 통해 별도로 통보 받을 수 있습니다. 이러한 통보 데이터를 받기      = */
    /* =   위해 가맹점측은 결과를 전송받는 페이지를 마련해 놓아야 합니다.           = */
    /* =   현재의 페이지를 업체에 맞게 수정하신 후, KCP 관리자 페이지에 등록해      = */
    /* =   주시기 바랍니다. 등록 방법은 연동 매뉴얼을 참고하시기 바랍니다.          = */
    /* ============================================================================== */

    if( $log_folder = gc_get_upload_path() ){
        gc_write_log($log_folder."/log/kcp/kcp_common.log", print_r($_POST));
    }
    /* ============================================================================== */
    /* =   02. 공통 통보 데이터 받기                                                = */
    /* = -------------------------------------------------------------------------- = */
    $site_cd      = isset($_POST["site_cd"]) ? sanitize_text_field( $_POST [ "site_cd"  ] ) : '';                 // 사이트 코드
    $tno          = isset($_POST["tno"]) ?  sanitize_text_field($_POST [ "tno"      ]) : '';                 // KCP 거래번호
    $order_no     = isset($_POST["order_no"]) ? sanitize_text_field($_POST [ "order_no" ]) : '';                 // 주문번호
    $tx_cd        = isset($_POST [ "tx_cd"    ]) ? sanitize_text_field($_POST [ "tx_cd" ]) : '';                  // 업무처리 구분 코드
    $tx_tm        = isset($_POST [ "tx_tm"    ]) ? sanitize_text_field($_POST [ "tx_tm" ]) : '';                  // 업무처리 완료 시간
    /* = -------------------------------------------------------------------------- = */
    $ipgm_name    = "";                                    // 주문자명
    $remitter     = "";                                    // 입금자명
    $ipgm_mnyx    = "";                                    // 입금 금액
    $bank_code    = "";                                    // 은행코드
    $account      = "";                                    // 가상계좌 입금계좌번호
    $op_cd        = "";                                    // 처리구분 코드
    $noti_id      = "";                                    // 통보 아이디
    /* = -------------------------------------------------------------------------- = */
    $refund_nm    = "";                                    // 환불계좌주명
    $refund_mny   = "";                                    // 환불금액
    $bank_code    = "";                                    // 은행코드
    /* = -------------------------------------------------------------------------- = */
    $st_cd        = "";                                    // 구매확인 코드
    $can_msg      = "";                                    // 구매취소 사유
    /* = -------------------------------------------------------------------------- = */
    $waybill_no   = "";                                    // 운송장 번호
    $waybill_corp = "";                                    // 택배 업체명

    /* = -------------------------------------------------------------------------- = */
    /* =   02-1. 가상계좌 입금 통보 데이터 받기                                     = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
        $ipgm_name = isset($_POST[ "ipgm_name" ]) ? sanitize_text_field($_POST[ "ipgm_name" ]) : '';                // 주문자명
        $remitter  = isset($_POST[ "remitter"  ]) ? sanitize_text_field($_POST[ "remitter" ]) : '';                // 입금자명
        $ipgm_mnyx = isset($_POST[ "ipgm_mnyx" ]) ? sanitize_text_field($_POST[ "ipgm_mnyx" ]) : '';                // 입금 금액
        $bank_code = isset($_POST[ "bank_code" ]) ? sanitize_text_field($_POST[ "bank_code" ]) : '';                // 은행코드
        $account   = isset($_POST[ "account"   ]) ? sanitize_text_field($_POST[ "account" ]) : '';                // 가상계좌 입금계좌번호
        $op_cd     = isset($_POST[ "op_cd"     ]) ? sanitize_text_field($_POST[ "op_cd" ]) : '';                // 처리구분 코드
        $noti_id   = isset($_POST[ "noti_id"   ]) ? sanitize_text_field($_POST[ "noti_id" ]) : '';                // 통보 아이디
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-2. 가상계좌 환불 통보 데이터 받기                                     = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX01" )
    {
        $refund_nm  = isset($_POST[ "refund_nm"  ]) ? sanitize_text_field($_POST[ "refund_nm" ]) : '';              // 환불계좌주명
        $refund_mny = isset($_POST[ "refund_mny" ]) ? sanitize_text_field($_POST[ "refund_mny" ]) : '';              // 환불금액
        $bank_code  = isset($_POST[ "bank_code"  ]) ? sanitize_text_field($_POST[ "bank_code" ]) : '';              // 은행코드
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-3. 구매확인/구매취소 통보 데이터 받기                                 = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX02" )
    {
        $st_cd = isset($_POST[ "st_cd" ]) ? sanitize_text_field($_POST[ "st_cd" ]) : '';                        // 구매확인 코드

        if ( $st_cd == "N" )                               // 구매확인 상태가 구매취소인 경우
        {
            $can_msg = isset($_POST[ "can_msg" ]) ? sanitize_text_field($_POST[ "can_msg" ]) : '';                // 구매취소 사유
        }
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   02-4. 배송시작 통보 데이터 받기                                          = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX03" )
    {
        $waybill_no   = isset($_POST[ "waybill_no"   ]) ? sanitize_text_field($_POST[ "waybill_no" ]) : '';          // 운송장 번호
        $waybill_corp = isset($_POST[ "waybill_corp" ]) ? sanitize_text_field($_POST[ "waybill_corp" ]) : '';          // 택배 업체명
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   03. 공통 통보 결과를 업체 자체적으로 DB 처리 작업하시는 부분입니다.      = */
    /* = -------------------------------------------------------------------------- = */
    /* =   통보 결과를 DB 작업 하는 과정에서 정상적으로 통보된 건에 대해 DB 작업을  = */
    /* =   실패하여 DB update 가 완료되지 않은 경우, 결과를 재통보 받을 수 있는     = */
    /* =   프로세스가 구성되어 있습니다. 소스에서 result 라는 Form 값을 생성 하신   = */
    /* =   후, DB 작업이 성공 한 경우, result 의 값을 "0000" 로 세팅해 주시고,      = */
    /* =   DB 작업이 실패 한 경우, result 의 값을 "0000" 이외의 값으로 세팅해 주시  = */
    /* =   기 바랍니다. result 값이 "0000" 이 아닌 경우에는 재통보를 받게 됩니다.   = */
    /* = -------------------------------------------------------------------------- = */

    /* = -------------------------------------------------------------------------- = */
    /* =   03-1. 가상계좌 입금 통보 데이터 DB 처리 작업 부분                        = */
    /* = -------------------------------------------------------------------------- = */
    if ( $tx_cd == "TX00" )
    {
        $sql = $wpdb->prepare(" select pp_id, od_id from {$gc['shop_personalpay_table']} where pp_id = %.0f and pp_tno = '%s' ", $order_no, $tno);
        $row = $wpdb->get_row($sql, ARRAY_A);

        $result = false;

        if($row['pp_id']) {
            // 개인결제 UPDATE
            $sql = $wpdb->prepare(" update {$gc['shop_personalpay_table']}
                        set pp_receipt_price    = %d,
                            pp_receipt_time     = '%s'
                        where pp_id = %.0f
                          and pp_tno = '%s' ", $ipgm_mnyx, $tx_tm, $order_no, $tno);
            $result = $wpdb->query($sql);

            if($row['od_id']) {
                // 주문서 UPDATE
                $receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $tx_tm);

                $od_shop_memo = sprintf(__("\n개인결제 %s 로 결제완료 - %s", GC_NAME), $row['pp_id'], $receipt_time);

                $sql = $wpdb->prepare(" update {$gc['shop_order_table']}
                            set od_receipt_price = od_receipt_price + %d,
                                od_receipt_time = '%s',
                                od_shop_memo = concat(od_shop_memo, %s)
                          where od_id = %.0f ", $ipgm_mnyx, $tx_tm, $od_shop_memo, $row['od_id']);
                $result = $wpdb->query($sql);
            }
        } else {
            // 주문서 UPDATE
            $sql = $wpdb->prepare(" update {$gc['shop_order_table']}
                        set od_receipt_price = %d,
                            od_receipt_time = '%s'
                      where od_id = %.0f
                        and od_tno = '%s' ", $ipgm_mnyx, $tx_tm, $order_no, $tno);
            $result = $wpdb->query($sql);
        }
    }

    if($result !== false) {
        if($row['od_id'])
            $od_id = $row['od_id'];
        else
            $od_id = $order_no;

        // 주문정보 체크
        $sql = $wpdb->prepare(" select count(od_id) as cnt
                    from {$gc['shop_order_table']}
                    where od_id = %.0f
                      and od_status = '%s' ", $od_id, gc_get_stype_names('order'));
        $row_cnt = $wpdb->get_var($sql);

        if($row_cnt == 1) {
            // 미수금 정보 업데이트
            $info = gc_get_order_info($od_id);

            $sql = $wpdb->prepare(" update {$gc['shop_order_table']}
                        set od_misu = %d ", $info['od_misu']);

            if($info['od_misu'] == 0)
                $sql .= $wpdb->prepare(" , od_status = '%s' ", gc_get_stype_names('deposit')); //입금
            $sql .= $wpdb->prepare(" where od_id = %.0f ", $od_id);
            $wpdb->query($sql);

            // 장바구니 상태변경
            if($info['od_misu'] == 0) {
                $sql = $wpdb->prepare(" update {$gc['shop_cart_table']}
                            set ct_status = '%s'
                            where od_id = %.0f ", gc_get_stype_names('deposit'), $od_id);    //입금
                $wpdb->query($sql);
            }
        }
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-2. 가상계좌 환불 통보 데이터 DB 처리 작업 부분                        = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX01" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-3. 구매확인/구매취소 통보 데이터 DB 처리 작업 부분                    = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX02" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-4. 배송시작 통보 데이터 DB 처리 작업 부분                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX03" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-5. 정산보류 통보 데이터 DB 처리 작업 부분                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX04" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-6. 즉시취소 통보 데이터 DB 처리 작업 부분                             = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX05" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-7. 취소 통보 데이터 DB 처리 작업 부분                                 = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX06" )
    {
    }

    /* = -------------------------------------------------------------------------- = */
    /* =   03-7. 발급계좌해지 통보 데이터 DB 처리 작업 부분                         = */
    /* = -------------------------------------------------------------------------- = */
    else if ( $tx_cd == "TX07" )
    {
    }
    /* ============================================================================== */


    /* ============================================================================== */
    /* =   04. result 값 세팅 하기                                                  = */
    /* ============================================================================== */
?>
<html><body><form><input type="hidden" name="result" value="0000"></form></body></html>
<?php
exit;
?>