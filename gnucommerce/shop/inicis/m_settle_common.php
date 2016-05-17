<?php
if( !defined('GC_NAME') ) exit;

//*******************************************************************************
// FILE NAME : mx_rnoti.php
// FILE DESCRIPTION :
// 이니시스 smart phone 결제 결과 수신 페이지 샘플
// 기술문의 : ts@inicis.com
// HISTORY
// 2010. 02. 25 최초작성
// 2010  06. 23 WEB 방식의 가상계좌 사용시 가상계좌 채번 결과 무시 처리 추가(APP 방식은 해당 없음!!)
// WEB 방식일 경우 이미 P_NEXT_URL 에서 채번 결과를 전달 하였으므로,
// 이니시스에서 전달하는 가상계좌 채번 결과 내용을 무시 하시기 바랍니다.
//*******************************************************************************

$PGIP = $_SERVER['REMOTE_ADDR'];

if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25")	//PG에서 보냈는지 IP로 체크
{
    global $wpdb;
    $gc = GC_VAR()->gc;

    $INIpayHome = GC_SHOP_DIR_PATH.'/inicis'; // 이니페이 홈디렉터리
    $INIpayLog  = GC_DEBUG_MODE;                  // 로그를 기록하려면 true 로 수정

    if($INIpayLog) {
        $logfile = fopen( GC_VAR()->get_log_path('inicis') . "/m_noti_result".date("Ymd").".log", "a+" );  //로그기록 임의 수정

        fwrite( $logfile,"************************************************");
        fwrite( $logfile, var_export($_POST, true) );   //로그기록
        fwrite( $logfile, "\r\n");

        fclose( $logfile );
    }

    // 이니시스 NOTI 서버에서 받은 Value
    $P_TID;				// 거래번호
    $P_MID;				// 상점아이디
    $P_AUTH_DT;			// 승인일자
    $P_STATUS;			// 거래상태 (00:성공, 01:실패)
    $P_TYPE;			// 지불수단
    $P_OID;				// 상점주문번호
    $P_FN_CD1;			// 금융사코드1
    $P_FN_CD2;			// 금융사코드2
    $P_FN_NM;			// 금융사명 (은행명, 카드사명, 이통사명)
    $P_AMT;				// 거래금액
    $P_UNAME;			// 결제고객성명
    $P_RMESG1;			// 결과코드
    $P_RMESG2;			// 결과메시지
    $P_NOTI;			// 노티메시지(상점에서 올린 메시지)
    $P_AUTH_NO;			// 승인번호

    $P_TID     = isset($_POST['P_TID']) ? sanitize_text_field($_POST['P_TID']) : '';
    $P_MID     = isset($_POST['P_MID']) ? sanitize_text_field($_POST['P_MID']) : '';
    $P_AUTH_DT = isset($_POST['P_AUTH_DT']) ? sanitize_text_field($_POST['P_AUTH_DT']) : '';
    $P_STATUS  = isset($_POST['P_STATUS']) ? sanitize_text_field($_POST['P_STATUS']) : '';
    $P_TYPE    = isset($_POST['P_TYPE']) ? sanitize_text_field($_POST['P_TYPE']) : '';
    $P_OID     = isset($_POST['P_OID']) ? sanitize_text_field($_POST['P_OID']) : '';
    $P_FN_CD1  = isset($_POST['P_FN_CD1']) ? sanitize_text_field($_POST['P_FN_CD1']) : '';
    $P_FN_CD2  = isset($_POST['P_FN_CD2']) ? sanitize_text_field($_POST['P_FN_CD2']) : '';
    $P_FN_NM   = isset($_POST['P_FN_NM']) ? $_POST['P_FN_NM'] : '';
    $P_AMT     = isset($_POST['P_AMT']) ? (int)$_POST['P_AMT'] : 0;
    $P_UNAME   = isset($_POST['P_UNAME']) ? sanitize_text_field($_POST['P_UNAME']) : '';
    $P_RMESG1  = isset($_POST['P_RMESG1']) ? $_POST['P_RMESG1'] : '';
    $P_RMESG2  = isset($_POST['P_RMESG2']) ? $_POST['P_RMESG2'] : '';
    $P_NOTI    = isset($_POST['P_NOTI']) ? sanitize_text_field($_POST['P_NOTI']) : '';
    $P_AUTH_NO = isset($_POST['P_AUTH_NO']) ? sanitize_text_field($_POST['P_AUTH_NO']) : '';

    //WEB 방식의 경우 가상계좌 채번 결과 무시 처리
    //(APP 방식의 경우 해당 내용을 삭제 또는 주석 처리 하시기 바랍니다.)
    if($P_TYPE == "VBANK")	//결제수단이 가상계좌이며
    {
       if($P_STATUS != "02") //입금통보 "02" 가 아니면(가상계좌 채번 : 00 또는 01 경우)
       {
          echo "OK";
          exit;
       }

       // 입금결과 처리
        $sql = $wpdb->prepare(" select pp_id, od_id from {$gc['shop_personalpay_table']} where pp_id = %.0f and pp_tno = '%s' ", $P_OID, $P_TID);
        $row = $wpdb->get_row($sql, ARRAY_A);

        $result = false;
        $receipt_time = $P_AUTH_DT;
        
        if(!$INIpayLog){
            $wpdb->hide_errors();
        }

        if($row['pp_id']) {
            // 개인결제 UPDATE

            $sql = $wpdb->prepare(" update {$gc['shop_personalpay_table']}
                        set pp_receipt_price    = %d,
                            pp_receipt_time     = '%s'
                        where pp_id = %.0f
                          and pp_tno = '%s' ", $P_AMT, $receipt_time, $P_OID, $P_TID);
            $wpdb->query($sql);

            if($row['od_id']) {
                // 주문서 UPDATE
                $receipt_time    = preg_replace("/([0-9]{4})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})([0-9]{2})/", "\\1-\\2-\\3 \\4:\\5:\\6", $receipt_time);
                
                if( $od = gc_get_order_data($row['od_id']) ){

                    $od_shop_memo = sprintf(__("\n개인결제 %s 로 결제완료 - %s", GC_NAME), $row['pp_id'], $receipt_time);
                    $sql = $wpdb->prepare(" update {$gc['shop_order_table']}
                                set od_receipt_price = od_receipt_price + %d,
                                    od_receipt_time = '%s',
                                    od_shop_memo = concat(od_shop_memo, %s)
                              where od_id = %.0f ", $P_AMT, $receipt_time, $od_shop_memo, $row['od_id']);
                    $result = $wpdb->query($sql);
                }

            }
        } else {
            $sql = $wpdb->prepare(" update {$gc['shop_order_table']}
                        set od_receipt_price = %d,
                            od_receipt_time = '%s'
                      where od_id = %.0f
                        and od_tno = '%s' ", $P_AMT, $receipt_time, $P_OID, $P_TID);

            $result = $wpdb->query($sql);
        }

        if($result !== false) {
            if($row['od_id'])
                $od_id = $row['od_id'];
            else
                $od_id = $P_OID;

            // 주문정보 체크
            $sql = $wpdb->prepare(" select count(od_id) as cnt from {$gc['shop_order_table']} where od_id = %.0f and od_status = '%s' ", $od_id, gc_get_stype_names('order'));    //주문

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

        if($result !== false) {
            echo "OK";
            exit;
        } else {
            echo "FAIL";
            exit;
        }
    } //end if VBANK

    $PageCall_time = date("H:i:s", GC_SERVER_TIME);

    $value = array(
                "PageCall time" => $PageCall_time,
                "P_TID"			=> $P_TID,
                "P_MID"         => $P_MID,
                "P_AUTH_DT"     => $P_AUTH_DT,
                "P_STATUS"      => $P_STATUS,
                "P_TYPE"        => $P_TYPE,
                "P_OID"         => $P_OID,
                "P_FN_CD1"      => $P_FN_CD1,
                "P_FN_CD2"      => $P_FN_CD2,
                "P_FN_NM"       => $P_FN_NM,
                "P_AMT"         => $P_AMT,
                "P_UNAME"       => $P_UNAME,
                "P_RMESG1"      => $P_RMESG1,
                "P_RMESG2"      => $P_RMESG2,
                "P_NOTI"        => $P_NOTI,
                "P_AUTH_NO"     => $P_AUTH_NO
            );

    // 결과 incis log 테이블 기록
    if($P_TYPE == 'BANK') {
        $log_data = array(
                'oid'   => $P_OID,
                'P_TID' =>  $P_TID,
                'P_MID' =>  $P_MID,
                'P_AUTH_DT' =>  $P_AUTH_DT,
                'P_STATUS'  =>  $P_STATUS,
                'P_TYPE'    =>  $P_TYPE,
                'P_OID'     =>  $P_OID,
                'P_FN_NM'   =>  gc_iconv_utf8($P_FN_NM),
                'P_AMT'     =>  $P_AMT,
                'P_RMESG1'  =>  gc_iconv_utf8($P_RMESG1),
            );
        
        $formats = array(
                '%.0f',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
            );
        global $wpdb;
        $gc = GC_VAR()->gc;
        $wpdb->hide_errors();
        $insert = $wpdb->insert($gc['inicis_log_table'], $log_data, $formats);
    }

    // 결제처리에 관한 로그 기록
    if( $INIpayLog ){
        gc_inicis_writeLog($value);
    }
    /***********************************************************************************
     ' 위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로 실패시는 "FAIL" 을
     ' 리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
     ' (주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
     ' 기타 다른 형태의 echo "" 는 하지 않으시기 바랍니다
    '***********************************************************************************/

    echo 'OK';
    exit;
}   //end if REMOTE_ADDR

function gc_inicis_writeLog($msg)
{
    if( $log_path = GC_VAR()->get_log_path('inicis') ){
        $file = $log_path."/noti_input_".date("Ymd").".log";

        if(!($fp = fopen($file, "a+"))) return 0;

        ob_start();
        print_r($msg);
        $ob_msg = ob_get_contents();
        ob_clean();

        if(fwrite($fp, " ".$ob_msg."\n") === FALSE)
        {
            fclose($fp);
            return 0;
        }
        fclose($fp);
        return 1;
    }
}
?>