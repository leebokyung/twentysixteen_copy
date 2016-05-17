<?php
if( !defined('GC_NAME') ) exit;

define("_ORDERINQUIRY_", true);

$od_id = isset($_POST['od_id']) ? sanitize_text_field($_POST['od_id']) : 0;
$od_pwd = isset($_POST['od_pwd']) ? sanitize_text_field($_POST['od_pwd']) : '';
$npage = isset($_GET['npage']) ? absint($_GET['npage']) : '';

$od_pwd = gc_get_encrypt_string($od_pwd);

// 회원인 경우
if ( is_user_logged_in() )
{
    $sql_common = $wpdb->prepare(" from {$gc['shop_order_table']} where mb_id = '%s' ", get_current_user_id());
}
else if ($od_id && $od_pwd) // 비회원인 경우 주문서번호와 비밀번호가 넘어왔다면
{
    $sql_common = $wpdb->prepare(" from {$gc['shop_order_table']} where od_id = %.0f and od_pwd = '%s' ", $od_id, $od_pwd);
}
else // 그렇지 않다면 로그인으로 가기
{
    gc_not_permission_page( add_query_arg(array('view'=>'orderinquiry'), get_permalink()) );
    return;
}

// 테이블의 전체 레코드수만 얻음
$total_count = (int) $wpdb->get_var(" select count(*) as cnt " . $sql_common);

// 비회원 주문확인시 비회원의 모든 주문이 다 출력되는 오류 수정
// 조건에 맞는 주문서가 없다면
if ($total_count == 0)
{
    if ( is_user_logged_in() ){ // 회원일 경우는 메인으로 이동
        gc_alert('주문이 존재하지 않습니다.', get_home_url() );
        return;
    } else { // 비회원일 경우는 이전 페이지로 이동
        gc_alert('주문이 존재하지 않습니다.');
    }
}

$rows = $config['cf_page_rows'] ? $config['cf_page_rows'] : 15;
$total_page  = ceil($total_count / $rows);  // 전체 페이지 계산
if ($npage < 1) { $npage = 1; } // 페이지가 없으면 첫 페이지 (1 페이지)
$from_record = ($npage - 1) * $rows; // 시작 열을 구함


// 비회원 주문확인의 경우 바로 주문서 상세조회로 이동
if (!is_user_logged_in())
{
    $sql = $wpdb->prepare(" select od_id, od_time, od_ip from {$gc['shop_order_table']} where od_id = %.0f and od_pwd = '%s' ", $od_id, $od_pwd);
    $row = $wpdb->get_row($sql, ARRAY_A);
    if ($row['od_id']) {
        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);
        gc_set_session('ss_orderview_uid', $uid);
        gc_goto_url( add_query_arg(array('view'=>'orderinquiryview', 'od_id'=>$row['od_id'], 'uid'=>$uid)) );
    }
    return;
}
?>

<!-- 주문 내역 시작 { -->
<div id="sod_v">
    <p id="sod_v_info">주문서번호 링크를 누르시면 주문상세내역을 조회하실 수 있습니다.</p>

    <?php
    $limit = " limit $from_record, $rows ";
    include GC_SHOP_DIR_PATH."/orderinquiry.sub.php";
    ?>

    <?php echo gc_get_paging($config['cf_write_pages'], $npage, $total_page, add_query_arg( array('npage'=>false) ), '', 'npage'); ?>
</div>
<!-- } 주문 내역 끝 -->