<?php
if (!defined("GC_NAME")) exit; // 개별 페이지 접근 불가

if (!defined("_ORDERINQUIRY_")) exit; // 개별 페이지 접근 불가
?>

<!-- 주문 내역 목록 시작 { -->
<?php if (!$limit) { ?>총 <?php echo $cnt; ?> 건<?php } ?>

<div id="sod_inquiry">
    <ul>
    <?php
    $sql = $wpdb->prepare(" select *, (od_cart_coupon + od_coupon + od_send_coupon) as couponprice
               from {$gc['shop_order_table']}
              where mb_id = '%s'
              order by od_id desc
              $limit ", get_current_user_id());

    $results = $wpdb->get_results($sql, ARRAY_A);

    $i = 0;
    foreach($results as $row)
    {
        if( empty($row) ) continue;

        // 주문상품
        $sql = $wpdb->prepare(" select it_name, ct_option
                    from {$gc['shop_cart_table']}
                    where od_id = %.0f
                    order by io_type, ct_id
                    limit 1 ", $row['od_id']);
        $ct = $wpdb->get_row($sql, ARRAY_A);
        $ct_name = gc_get_text($ct['it_name']).' '.gc_get_text($ct['ct_option']);

        $sql = $wpdb->prepare(" select count(*) as cnt
                    from {$gc['shop_cart_table']}
                    where od_id = %.0f ", $row['od_id']);
        $ct2 = $wpdb->get_row($sql, ARRAY_A);
        if($ct2['cnt'] > 1)
            $ct_name .= ' 외 '.($ct2['cnt'] - 1).'건';

        switch($row['od_status']) {
            case gc_get_stype_names('order') :    //주문
                $od_status = __('입금확인중', GC_NAME);
                break;
            case gc_get_stype_names('deposit') :    //입금
                $od_status = __('입금완료', GC_NAME);
                break;
            case gc_get_stype_names('prepare') :    //준비
                $od_status = __('상품준비중', GC_NAME);
                break;
            case gc_get_stype_names('deliver') :    //배송
                $od_status = __('상품배송', GC_NAME);
                break;
            case gc_get_stype_names('complete') :    //완료
                $od_status = __('배송완료', GC_NAME);
                break;
            default:
                $od_status = __('주문취소', GC_NAME);
                break;
        }

        $od_invoice = '';
        if($row['od_delivery_company'] && $row['od_invoice'])
            $od_invoice = gc_get_text($row['od_delivery_company']).' '.gc_get_text($row['od_invoice']);

        $uid = md5($row['od_id'].$row['od_time'].$row['od_ip']);
    ?>

    <li>
        <div class="inquiry_idtime">
            <a href="<?php echo add_query_arg( array('view'=>'orderinquiryview', 'od_id'=>$row['od_id'], 'uid'=>$uid) ); ?>" class="idtime_link"><?php echo $row['od_id']; ?></a>
            <span class="idtime_time"><?php echo substr($row['od_time'],2,8); ?></span>
        </div>
        <div class="inquiry_name">
            <?php echo $ct_name; ?>
        </div>
        <div class="inquiry_price">
            <?php echo gc_display_price($row['od_receipt_price']); ?>
        </div>
        <div class="inquiry_inv">
            <span class="inv_status"><?php echo $od_status; ?></span>
            <span class="inv_inv"><?php echo $od_invoice; ?></span>
        </div>
    </li>

    <?php
    $i++;
    }   //end foreach

    if ($i == 0)
        echo '<li class="empty_list">'.__('주문 내역이 없습니다.', GC_NAME).'</li>';
    ?>
    </ul>
</div>
<!-- } 주문 내역 목록 끝 -->