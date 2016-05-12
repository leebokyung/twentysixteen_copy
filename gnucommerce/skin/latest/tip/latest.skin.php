<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
include_once( GC_LIB_PATH.'/thumbnail.lib.php' );  //리스트에서 이미지를 사용할시 사용

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

$row_mod = isset($row_mod) ? (int) $row_mod : 2;    //한줄에 출력할 이미지수
$content_len = isset($content_len) ? (int) $content_len : 80; 
?>
<div class="sir_community_latest_tip">
<h2 style="display:none"><a href="<?php echo esc_url( $gc_page_url ); ?>"><?php echo $bo_subject; ?></a></h2>
    <ul class="latest_row">
    <?php
    $i = 0;

    foreach($list as $row) {
        if( !isset($row['wr_id']) ) continue;

        $classes = array();
        
        $classes[] = 'col-gn-'.$row_mod;

        if( $i && ($i % $row_mod == 0) ){
            $classes[] = 'box_clear';
        }

        $thumb = gc_get_list_thumbnail($bo_table, $row['wr_id'], 236, 236);

        if($thumb['src']) {     //이미지가 있을때
            $img_content = '<img src="'.$thumb['src'].'" alt="'.$thumb['alt'].'" class="thumb" >';
        } else {    //이미지가 없을때
            $img_content = '<img src="'.get_template_directory_uri().'/img/no_img.png" alt="no image" class="thumb" >';
        }

        $date = sir_community_get_date( $row['wr_datetime'] );
        $content = gc_cut_str(strip_tags($row['wr_content']), $content_len, '…');
    ?>
        <li class="<?php echo implode(' ', $classes); ?>">
            <div class="utl_mw">
            <?php
            echo "<a href=\"".esc_url($row['href'])."\" class=\"block\">";
            echo $img_content;
            echo "</a>";
            ?>
            </div>
            <div class="new-con-txt">
            <?php
            echo "<span class=\"new-title\"><a href=\"".esc_url($row['href'])."\" class=\"new-title\">";
            echo esc_attr($row['subject']);

            if ($row['comment_cnt'])
                echo PHP_EOL.'<span class="new-comment"><b>'.$row['comment_cnt'].'</b></span>';

            echo "</a></span>";

            echo '<span class="new-txt"><a href="'.esc_url($row['href']).'">'.esc_attr($content).'</a></span>';
            ?>
            <br>
            <span class="new-name"><?php echo esc_attr($row['user_display_name']); ?></span>
            <span class="new-date"><?php echo $date['time2date']; ?></span>
            </div>
        </li>
    <?php $i++; } //end foreach  ?>
    <?php if (count($list) == 0) { //게시물이 없을 때  ?>
    <li><?php _e('게시물이 없습니다.', GC_NAME);?></li>
    <?php }  ?>
    </ul>
    <a href="<?php echo esc_url( $gc_page_url ); ?>" class="new-content-more"><?php _e('더보기', GC_NAME);?></a>
</div>
<!-- } <?php echo $bo_subject; ?> 최신글 끝 -->