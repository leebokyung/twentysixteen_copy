<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가

if( !function_exists('sir_community_get_date') ){
    function sir_community_get_date($datetime)
    {

        $current_time = current_time( 'timestamp' );
        $current_ymd = date('Y-m-d', $current_time);

        $timestamp = strtotime($datetime);

        $date = array();
        $date['strtotime'] = $timestamp;
        $date['datetime'] = date('Y.m.d H:i:s', $timestamp);
        $date['sdatetime'] = date('y.m.d H:i:s', $timestamp);
        $date['date'] = date('Y.m.d', $timestamp);
        $date['sdate'] = date('y.m.d', $timestamp);
        $date['time'] = date('H:i:s', $timestamp);
        $date['stime'] = date('H:i', $timestamp);

        // 오늘은 시간으로 내일부터는 날짜로 반환
        $date['time2date'] = $date['stime'];
        if (substr($datetime,0,10) < $current_ymd) {
            $date['time2date'] = date('m.d', $timestamp);
        }
        // 오늘은 시간으로 내일부터는 날짜로 반환
        $date['ltime2date'] = $date['time'];
        if (substr($datetime,0,10) < $current_ymd) {
            $date['ltime2date'] = $date['sdate'];
        }

        return $date;
    }
}
?>
<div class="sir_comm_latest_text">
<h2 style="display:none"><a href="<?php echo esc_url( $gc_page_url ); ?>"><?php echo $bo_subject; ?></a></h2>
    <ul>
    <?php
    foreach($list as $row) {
        if( !isset($row['wr_id']) ) continue;
    ?>
        <li>
            <?php

            $date = sir_community_get_date( $row['wr_datetime'] );

            echo "<a href=\"".esc_url($row['href'])."\" >";
            echo "<span class=\"new-cont-title\">".esc_attr($row['subject']);

            if (isset($row['icon_new'])) echo " " . $row['icon_new'];
            if (isset($row['icon_hot'])) echo " " . $row['icon_hot'];
            if (isset($row['icon_file'])) echo " " . $row['icon_file'];
            if (isset($row['icon_link'])) echo " " . $row['icon_link'];
            if (isset($row['icon_secret'])) echo " " . $row['icon_secret'];

            echo "</span>";

            if ($row['comment_cnt'])
                echo "<span class=\"new-comment\"><b>".$row['comment_cnt']."</b></span>";

            echo "<span class=\"new-date\">".$date['time2date']."</span>";
            echo "</a>";
            ?>
        </li>
    <?php }  ?>
    <?php if (count($list) == 0) { //게시물이 없을 때  ?>
    <li><?php _e('게시물이 없습니다.', GC_NAME);?></li>
    <?php }  ?>
    </ul>
    <a href="<?php echo esc_url( $gc_page_url ); ?>" class="new-content-more"><?php _e('더보기', GC_NAME);?></a>
</div>
<!-- } <?php echo $bo_subject; ?> 최신글 끝 -->