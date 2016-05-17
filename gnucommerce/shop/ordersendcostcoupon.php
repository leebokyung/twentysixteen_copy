<?php
if( !defined('GC_NAME') ) exit;

global $wpdb;

$gc = GC_VAR()->gc;

if( !is_user_logged_in() )  //비회원이면
    exit;

$price = isset($_POST['price']) ? (int) $_POST['price'] : 0;
$send_cost = isset($_POST['send_cost']) ? (int) $_POST['send_cost'] : 0;

// 쿠폰정보
$sql = $wpdb->prepare(" select SQL_CALC_FOUND_ROWS *
            from {$gc['shop_coupon_table']}
            where mb_id IN ( '".get_current_user_id()."', '%s' )
              and cp_method = '3'
              and cp_start <= '%s'
              and cp_end >= '%s'
              and cp_minimum <= %d ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD, $price);

$results = $wpdb->get_results($sql, ARRAY_A);
$count = (int) $wpdb->get_var('SELECT FOUND_ROWS()');
?>

<!-- 쿠폰선택 시작 { -->
<div id="sc_coupon_frm">
    <?php if($count > 0) { ?>
    <div class="tbl_head02 tbl_wrap">
        <table>
        <caption>쿠폰 선택</caption>
        <thead>
        <tr>
            <th scope="col">쿠폰명</th>
            <th scope="col">할인금액</th>
            <th scope="col">적용</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        foreach($results as $row){
            if( empty($row) ) continue;

            // 사용한 쿠폰인지 체크
            if(gc_is_used_coupon(get_current_user_id(), $row['cp_id']))
                continue;

            $dc = 0;
            if($row['cp_type']) {
                $dc = floor(($send_cost * ($row['cp_price'] / 100)) / $row['cp_trunc']) * $row['cp_trunc'];
            } else {
                $dc = $row['cp_price'];
            }

            if($row['cp_maximum'] && $dc > $row['cp_maximum'])
                $dc = $row['cp_maximum'];

            if($dc > $send_cost)
                $dc = $send_cost;
        ?>
        <tr>
            <td>
                <input type="hidden" name="s_cp_id[]" value="<?php echo $row['cp_id']; ?>">
                <input type="hidden" name="s_cp_prc[]" value="<?php echo $dc; ?>">
                <input type="hidden" name="s_cp_subj[]" value="<?php echo $row['cp_subject']; ?>">
                <?php echo gc_get_text($row['cp_subject']); ?>
            </td>
            <td class="td_numbig"><?php echo number_format($dc); ?></td>
            <td class="td_mngsmall"><button type="button" class="sc_cp_apply btn_frmline">적용</button></td>
        </tr>
        <?php
        $i++;
        }   //end foreach
        ?>
        </tbody>
        </table>
    </div>
    <?php
    } else {
        echo '<p>사용할 수 있는 쿠폰이 없습니다.</p>';
    }
    ?>
    <div class="btn_confirm">
        <button type="button" id="sc_coupon_close" class="btn_submit">닫기</button>
    </div>
</div>
<!-- } 쿠폰선택 끝 -->