<?php
if( !defined('GC_NAME') ) exit;

if(!get_current_user_id())  //비회원이면
    exit;

global $wpdb;

$gc = GC_VAR()->gc;

// 상품정보
$it_id = isset($_POST['it_id']) ? sprintf("%.0f", $_POST['it_id']) : 0;
$sw_direct = isset($_POST['sw_direct']) ? sanitize_text_field($_POST['sw_direct']) : '';

$it = gc_get_product_info($it_id);

// 상품 총 금액
if($sw_direct)
    $cart_id = gc_get_session('ss_cart_direct');
else
    $cart_id = gc_get_session('ss_cart_id');

$sql = $wpdb->prepare(" select SUM( IF(io_type = '1', io_price * ct_qty, (ct_price + io_price) * ct_qty)) as sum_price
            from {$gc['shop_cart_table']}
            where od_id = %.0f
              and it_id = %.0f ", $cart_id, $it_id);

$item_price = $wpdb->get_var($sql);

$add_where = '';
//상품 카테고리가 지정되어 있으면
/*
$term_ids = array();
if( $product_terms = wp_get_object_terms( $it_id, GC_CATEGORY_TAXONOMY ) ){
    foreach($product_terms as $terms){
        $term_ids[] = $terms->term_id;
    }

    //나중에 카테고리 할인을 구현
    //$add_where .= " OR ( cp_method = '1' and ( cp_target IN ( ".implode(',', $term_ids)." ) )";
}
*/

// 쿠폰정보
$sql = $wpdb->prepare(" select SQL_CALC_FOUND_ROWS *
            from {$gc['shop_coupon_table']}
            where mb_id IN ( '".get_current_user_id()."', '%s' )
              and cp_start <= '%s'
              and cp_end >= '%s'
              and cp_minimum <= %d
              and (
                    ( cp_method = '0' and cp_target = '%s' )
                    $add_where
                  ) ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD, $item_price, $it['it_id']);

$results = $wpdb->get_results($sql, ARRAY_A);
$count = (int) $wpdb->get_var('SELECT FOUND_ROWS()');
?>

<!-- 쿠폰 선택 시작 { -->
<div id="cp_frm">
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
                $dc = floor(($item_price * ($row['cp_price'] / 100)) / $row['cp_trunc']) * $row['cp_trunc'];
            } else {
                $dc = $row['cp_price'];
            }

            if($row['cp_maximum'] && $dc > $row['cp_maximum'])
                $dc = $row['cp_maximum'];
        ?>
        <tr>
            <td>
                <input type="hidden" name="f_cp_id[]" value="<?php echo $row['cp_id']; ?>">
                <input type="hidden" name="f_cp_prc[]" value="<?php echo $dc; ?>">
                <input type="hidden" name="f_cp_subj[]" value="<?php echo $row['cp_subject']; ?>">
                <?php echo gc_get_text($row['cp_subject']); ?>
            </td>
            <td class="td_numbig"><?php echo number_format($dc); ?></td>
            <td class="td_mngsmall"><button type="button" class="cp_apply btn_frmline">적용</button></td>
        </tr>
        <?php
        }
        ?>
        </tbody>
        </table>
    </div>
    <?php
    } else {
        echo '<div class="empty_list">사용할 수 있는 쿠폰이 없습니다.</div>';
    }
    ?>
    <div class="btn_confirm">
        <button type="button" id="cp_close" class="btn_submit">닫기</button>
    </div>
</div>
<!-- } 쿠폰 선택 끝 -->