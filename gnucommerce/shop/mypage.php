<?php
if( !defined('GC_NAME') ) exit;

// 쿠폰
$cp_count = 0;
$sql = $wpdb->prepare(" select cp_id
            from {$gc['shop_coupon_table']}
            where mb_id IN ( '".get_current_user_id()."', '%s' )
              and cp_start <= '%s'
              and cp_end >= '%s' ", gc_get_stype_names('allmembers'), GC_TIME_YMD, GC_TIME_YMD);
$res = $wpdb->get_results($sql, ARRAY_A);

$member = apply_filters('gc_get_display_member', gc_get_member( get_current_user_id() ));

foreach( $res as $cp ){

    if( empty($cp) ) continue;

    if(!gc_is_used_coupon(get_current_user_id(), $cp['cp_id']))
        $cp_count++;
}
?>

<!-- 마이페이지 시작 { -->
<div class="mypage_info_container">

    <!-- 회원정보 개요 시작 { -->
    <section id="smb_my_ov">
        <h2><?php _e('회원정보 개요', GC_NAME); ?></h2>
        <ul>
            <li class="my_cou"><?php _e('보유쿠폰', GC_NAME); ?><a href="<?php echo add_query_arg(array('view'=>'coupon')); ?>" class="win_coupon"><?php echo number_format($cp_count); ?></a></li>
            <li class="my_point"><?php _e('보유적립금', GC_NAME); ?>
            <a href="<?php echo add_query_arg(array('view'=>'mileage')); ?>" class="win_point"><?php echo number_format($member['mb_mileage']); ?>원</a></li>

        </ul>
        <dl>
            <dt><?php _e('연락처', GC_NAME); ?></dt>
            <dd><?php echo ($member['mb_tel'] ? $member['mb_tel'] : __('미등록', GC_NAME)); ?> <a href="<?php echo add_query_arg(array('view'=>'myform'), get_permalink());?>" class="gc_mypage_button gc_modify"><?php _e('수정하기', GC_NAME); ?></a></dd>
            <dt>E-Mail</dt>
            <dd><?php echo ($member['user_email'] ? $member['user_email'] : __('미등록', GC_NAME)); ?></dd>
            <dt class="ov_addr"><?php _e('주소', GC_NAME); ?></dt>
            <dd class="ov_addr"><?php echo sprintf("(%s)", $member['mb_zip']).' '.gc_print_address($member['mb_addr1'], $member['mb_addr2'], $member['mb_addr3'], $member['mb_addr_jibeon']); ?></dd>
        </dl>
    </section>
    <!-- } 회원정보 개요 끝 -->

    <!-- 최근 주문내역 시작 { -->
    <section id="smb_my_od">
        <h2><a href="<?php echo add_query_arg(array('view'=>'orderinquiry')); ?>"><?php _e('최근 주문내역', GC_NAME); ?></a></h2>
        <?php
        // 최근 주문내역
        define("_ORDERINQUIRY_", true);

        $limit = " limit 0, 5 ";
        include GC_SHOP_DIR_PATH.'/orderinquiry.sub.php';
        ?>
    </section>
    <!-- } 최근 주문내역 끝 -->

    <!-- 최근 위시리스트 시작 { -->
    <section id="smb_my_wish">
        <h2><a href="<?php echo add_query_arg(array('view'=>'wishlist')); ?>"><?php _e('최근 위시리스트', GC_NAME); ?></a></h2>

        <ul>
            <?php
            $sql = $wpdb->prepare(" select *
                       from {$gc['shop_wish_table']} a,
                            {$gc['shop_item_table']} b
                      where a.mb_id = '%s'
                        and a.it_id  = b.it_id
                      order by a.wi_id desc
                      limit 0, 3 ", get_current_user_id());

            $results = $wpdb->get_results($sql, ARRAY_A);

            foreach($results as $row)
            {
                if( empty($row) ) continue;

                $image = gc_get_it_image($row['it_id'], 50, 50, true);
                $list_left_pad = 50 + 10;
            ?>
            <li style="padding-left:<?php echo $list_left_pad + 10; ?>px">
                <div class="wish_img"><?php echo $image; ?></div>
                <div class="wish_info">
                    <a href="<?php echo get_permalink($row['it_id']); ?>" class="info_link"><?php echo stripslashes($row['it_name']); ?></a>
                    <span class="info_date"><?php echo substr($row['wi_time'], 2, 8); ?></span>
                </div>
            </li>

            <?php
            }

            if ( !count($results) )
                echo '<li class="empty_list">'.__('보관 내역이 없습니다.', GC_NAME).'</li>';
            ?>
        </ul>
    </section>
    <!-- } 최근 위시리스트 끝 -->

<?php
do_action('gc_action_mypage');
?>
</div>
<!-- } 마이페이지 끝 -->