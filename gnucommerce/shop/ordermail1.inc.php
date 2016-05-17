<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

unset($list);

$ttotal_price = 0;
$ttotal_point = 0;

//==============================================================================
// 메일보내기
//------------------------------------------------------------------------------
// Loop 배열 자료를 만들고
$sql = $wpdb->prepare(" select a.it_id,
                a.it_name,
                a.ct_qty,
                a.ct_price,
                a.ct_point,
                b.it_sell_email,
                b.it_origin
           from {$gc['shop_cart_table']} a left join {$gc['shop_item_table']} b on ( a.it_id = b.it_id )
          where a.od_id = %.0f
            and a.ct_select = '1'
          group by a.it_id
          order by a.ct_id asc ", $od_id);

$results = $wpdb->get_results($sql, ARRAY_A);

$i = 0;
foreach($results as $row)
{
    // 합계금액 계산
    $sql = $wpdb->prepare(" select SUM(IF(io_type = 1, (io_price * ct_qty), ((ct_price + io_price) * ct_qty))) as price,
                    SUM(ct_point * ct_qty) as point,
                    SUM(ct_qty) as qty
                from {$gc['shop_cart_table']}
                where it_id = %.0f
                  and od_id = %.0f
                  and ct_select = '1' ", $row['it_id'], $od_id);
    $sum = $wpdb->get_row($sql, ARRAY_A);

    // 옵션정보
    $sql2 = $wpdb->prepare(" select ct_option, ct_qty, io_price
                from {$gc['shop_cart_table']}
                where it_id = %.0f and od_id = %.0f and ct_select = '1'
                order by io_type asc, ct_id asc ", $row['it_id'], $od_id);
    $result2 = $wpdb->get_results($sql2, ARRAY_A);

    $options = '';
    $options_ul = ' style="margin:0;padding:0"'; // ul style
    $options_li = ' style="padding:5px 0;list-style:none"'; // li style

    $k = 0;
    foreach($result2 as $row2){

        if( empty($row2) ) continue;

        if($k == 0)
            $options .= '<ul'.$options_ul.'>'.PHP_EOL;
        $price_plus = '';
        if($row2['io_price'] >= 0)
            $price_plus = '+';
        $options .= '<li'.$options_li.'>'.$row2['ct_option'].' ('.$price_plus.gc_display_price($row2['io_price']).') '.$row2['ct_qty'].'개</li>'.PHP_EOL;
        $k++;
    }   //end foreach

    if($k > 0)
        $options .= '</ul>';

    $list[$i]['g_dir']         = home_url();
    $list[$i]['it_id']         = $row['it_id'];
    $list[$i]['it_simg']       = gc_get_it_image($row['it_id'], 70, 70);
    $list[$i]['it_name']       = $row['it_name'];
    $list[$i]['it_origin']     = $row['it_origin'];
    $list[$i]['it_opt']        = $options;
    $list[$i]['ct_price']      = $row['ct_price'];
    $list[$i]['stotal_price']  = $sum['price'];
    $list[$i]['stotal_point']  = $sum['point'];

    $ttotal_price  += $list[$i]['stotal_price'];
    $ttotal_point  += $list[$i]['stotal_point'];
    $i++;
}   //end foreach
//------------------------------------------------------------------------------

// 배송비가 있다면 총계에 더한다
if ($od_send_cost)
    $ttotal_price += $od_send_cost;

// 추가배송비가 있다면 총계에 더한다
if ($od_send_cost2)
    $ttotal_price += $od_send_cost2;
?>