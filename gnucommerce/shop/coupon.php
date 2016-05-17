<?php
if( !defined('GC_NAME') ) exit;

if (!is_user_logged_in())
    gc_alert('회원만 조회하실 수 있습니다.');

$sql = $wpdb->prepare(" select cp_id, cp_subject, cp_method, cp_target, cp_start, cp_end, cp_type, cp_price
            from {$gc['shop_coupon_table']}
            where mb_id IN ( '".get_current_user_id()."', '%s' )
              and cp_start <= '%s'
              and cp_end >= '%s'
            order by cp_no ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD);

$results = $wpdb->get_results($sql, ARRAY_A);
?>

<!-- 쿠폰 내역 시작 { -->
<div id="coupon">
    <h1 id="win_title">쿠폰내역</h1>

    <div class="tbl_wrap tbl_head01">
        <table>
        <thead>
        <tr>
            <th scope="col">쿠폰명</th>
            <th scope="col">적용대상</th>
            <th scope="col">할인금액</th>
            <th scope="col">사용기한</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $cp_count = 0;
        foreach($results as $row){
            if(gc_is_used_coupon(get_current_user_id(), $row['cp_id']))
                continue;

            if($row['cp_method'] == 1) {
                /*
                //카테고리 상품할인 아직 미구현
                */
                continue;

            } else if($row['cp_method'] == 2) {
                $cp_target = '결제금액 할인';
            } else if($row['cp_method'] == 3) {
                $cp_target = '배송비 할인';
            } else {
                $product_info = gc_get_it_info($row['cp_target']);
                if( !$product_info ){   //상품이 없으면
                    continue;
                }
                $cp_target = $product_info->post_title.' 상품할인';
            }

            if($row['cp_type'])
                $cp_price = $row['cp_price'].'%';
            else
                $cp_price = number_format($row['cp_price']).'원';

            $cp_count++;
        ?>
        <tr>
            <td><?php echo $row['cp_subject']; ?></td>
            <td><?php echo $cp_target; ?></td>
            <td class="td_numbig"><?php echo $cp_price; ?></td>
            <td class="td_datetime"><?php echo substr($row['cp_start'], 2, 8); ?> ~ <?php echo substr($row['cp_end'], 2, 8); ?></td>
        </tr>
        <?php
        }

        if(!$cp_count)
            echo '<tr><td colspan="4" class="empty_table">사용할 수 있는 쿠폰이 없습니다.</td></tr>';
        ?>
        </tbody>
        </table>
    </div>
</div>