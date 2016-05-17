<?php
if( !defined('GC_NAME') ) exit;

$gw = isset($_REQUEST['gw']) ? sanitize_text_field($_REQUEST['gw']) : '';

if ( !is_user_logged_in() ){
    gc_alert('권한이 없습니다.');
}

if ($gw == "d")
{
    $result = false;

    $wi_id = isset($_REQUEST['wi_id']) ? absint($_REQUEST['wi_id']) : 0;

    if( !$wi_id ){
        gc_alert('wi_id 값이 필요합니다.');
    }

    $sql = $wpdb->prepare(" select mb_id from {$gc['shop_wish_table']} where wi_id = %d ", $wi_id);

    $row = $wpdb->get_row($sql, ARRAY_A);

    if($row['mb_id'] != get_current_user_id()){
        gc_alert('위시리시트 상품을 삭제할 권한이 없습니다.');
    }

    $sql = $wpdb->prepare(" delete from {$gc['shop_wish_table']}
              where wi_id = %d
                and mb_id = '%s' ", $wi_id, get_current_user_id());
    $result = $wpdb->query($sql);

    if( $result !== false ){
        gc_alert('true');
    }
}
else
{
    $result = false;

    if( isset($_POST['it_id']) && is_array($_POST['it_id']) ){
        $it_id = (int) $_POST['it_id'][0];
    }

    if(!$it_id)
        gc_alert('상품코드가 올바르지 않습니다.', get_home_url() );

    // 상품정보 체크
    $sql = $wpdb->prepare(" select it_id from {$gc['shop_item_table']} where it_id = %.0f ", $it_id);
    $row_it_id = $wpdb->get_var($sql);

    if(!$row_it_id)
        gc_alert('상품정보가 존재하지 않습니다.', get_home_url() );

    $sql = $wpdb->prepare(" select wi_id from {$gc['shop_wish_table']}
              where mb_id = '%s' and it_id = %.0f ", get_current_user_id(), $it_id);
    $row_wi_id = $wpdb->get_var($sql);

    if ($row_wi_id) {
        gc_alert('이미 등록하셨습니다.');
    } else {    // 없다면 등록
        $sql = $wpdb->prepare(" insert {$gc['shop_wish_table']}
                    set mb_id = '%s',
                        it_id = %.0f,
                        wi_time = '%s',
                        wi_ip = '%s' ", get_current_user_id(), $it_id, GC_TIME_YMDHIS, $_SERVER['REMOTE_ADDR']);
        $result = $wpdb->query($sql);
    }

    if( $result !== false ){
        gc_alert('true', gc_get_page_url('wishlist'));
    }
}
?>