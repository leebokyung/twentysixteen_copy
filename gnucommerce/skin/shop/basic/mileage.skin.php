<?php
if( !defined('GC_NAME') ) exit;

do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>
<div class="mypage_info_container">
    <h1 id="win_title"><?php echo $member['user_display_name']; ?>님의 적립금 내역</h1>

    <ul id="mileage_ul">
        <?php
        $sum_point1 = $sum_point2 = $sum_point3 = 0;

        foreach( $results as $row ) {
            $point1 = $point2 = 0;
            if ($row['mi_mileage'] > 0) {
                $point1 = '+' .number_format($row['mi_mileage']);
                $sum_point1 += $row['mi_mileage'];
            } else {
                $point2 = number_format($row['mi_mileage']);
                $sum_point2 += $row['mi_mileage'];
            }

            $mi_content = $row['mi_content'];

            $expr = '';
            //if($row['mi_expired'] == 1)
                $expr = ' txt_expired';
        ?>
        <li>
            <div class="point_wrap01">
                <span class="point_date"><?php echo gc_conv_date_format('y-m-d H시', $row['mi_datetime']); ?></span>
                <span class="point_log"><?php echo $mi_content; ?></span>
            </div>
            <div class="point_wrap02">
                <span class="point_expdate<?php echo $expr; ?>">
                    <?php if ($row['mi_expired'] == 1) { ?>
                    만료<?php echo substr(str_replace('-', '', $row['mi_expire_date']), 2); ?>
                    <?php } else echo $row['mi_expire_date'] == '9999-12-31' ? '&nbsp;' : $row['mi_expire_date']; ?>
                </span>
                <span class="point_inout"><?php if ($point1) echo $point1; else echo $point2; ?></span>
            </div>
        </li>
        <?php
        }

        if ( !count($results) )
            echo '<li class="empty_list">'.__('적립 내역이 없습니다.', GC_NAME).'</li>';
        else {
            if ($sum_point1 > 0)
                $sum_point1 = "+" . number_format($sum_point1);
            $sum_point2 = number_format($sum_point2);
        }
        ?>
        </ul>

        <div id="mileage_sum">
            <div class="sum_row">
                <span class="sum_tit">지급</span>
                <b class="sum_val"><?php echo $sum_point1; ?></b>
            </div>
            <div class="sum_row">
                <span class="sum_tit">사용</span>
                <b class="sum_val"><?php echo $sum_point2; ?></b>
            </div>
            <div class="sum_row">
                <span class="sum_tit"><?php _e('보유 적립금', GC_NAME);?></span>
                <b class="sum_val"><?php echo number_format($member['mb_mileage']); ?></b>
            </div>
        </div>

    <?php
    echo gc_get_paging(GC_IS_MOBILE ? $config['cf_mobile_pages'] : $config['cf_write_pages'], $npage, $total_page, add_query_arg(array('npage'=>false)), '', 'npage');
    ?>
</div>