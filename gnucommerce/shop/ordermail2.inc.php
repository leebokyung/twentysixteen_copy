<?php
if( !defined('GC_NAME') ) exit; // 개별 페이지 접근 불가

add_filter( 'wp_mail_content_type', 'gc_set_html_content_type' );

$headers = 'From: '.$config['cf_admin_email_name'].' <'.$config['cf_admin_email'].'>' . "\r\n";

//------------------------------------------------------------------------------
// 운영자에게 메일보내기
//------------------------------------------------------------------------------
$subject = get_bloginfo().' - 주문 알림 메일 ('.$od_name.')';
ob_start();
include GC_SHOP_DIR_PATH.'/mail/orderupdate1.mail.php';
$content = ob_get_contents();
ob_end_clean();

wp_mail($od_email, $subject, $content, $headers );
//------------------------------------------------------------------------------

//------------------------------------------------------------------------------
// 주문자에게 메일보내기
//------------------------------------------------------------------------------
$subject = get_bloginfo().' - 주문 내역 안내 메일';
ob_start();
include GC_SHOP_DIR_PATH.'/mail/orderupdate2.mail.php';
$content = ob_get_contents();
ob_end_clean();

wp_mail($od_email, $subject, $content, $headers );
//------------------------------------------------------------------------------


//------------------------------------------------------------------------------
// 판매자에게 메일 보내기 (상품별로 보낸다.)
//------------------------------------------------------------------------------

unset($list);
$sql = $wpdb->prepare(" select b.it_sell_email,
                a.it_id,
                a.it_name
           from {$gc['shop_cart_table']} a left join {$gc['shop_item_table']} b on ( a.it_id = b.it_id )
          where a.od_id = %.0f
            and a.ct_select = '1'
            and b.it_sell_email <> ''
          group by a.it_id ", $od_id);

$results = $wpdb->get_results($sql, ARRAY_A);

$i = 0;
foreach($results as $row)
{
    if( empty($row) ) continue;

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

    $list[$i]['it_id']   = $row['it_id'];
    $list[$i]['it_simg'] = gc_get_it_image($row['it_id'], 70, 70);
    $list[$i]['it_name'] = $row['it_name'];
    $list[$i]['it_opt']  = $options;
    $list[$i]['ct_price'] = $sum['price'];

    $subject = get_bloginfo().' - 주문 알림 메일 (주문자 '.$od_name.'님)';
    ob_start();
    include GC_SHOP_DIR_PATH.'/mail/orderupdate3.mail.php';
    $content = ob_get_contents();
    ob_end_clean();

    wp_mail($config['cf_admin_email'], $subject, $content, $headers );

    $i++;
}   //end foreach   $results
//==============================================================================

remove_filter( 'wp_mail_content_type', 'gc_set_html_content_type' );
?>