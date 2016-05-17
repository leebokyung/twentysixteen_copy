<?php
if ( !defined('ABSPATH') ) exit;

do_action('gc_before_item_view');

include_once(GC_DIR_PATH.'lib/iteminfo.lib.php');

global $post, $wpdb;

$ca = array();
$gc = GC_VAR()->gc;
$config = GC_VAR()->config;
$tmp_array = array('it_head_html'=>'','title'=>'','ca_name'=>'', 'it_tail_html'=>'', 'it_use'=>'');
$it = isset( $it ) ? $it : array_merge( json_decode(json_encode($post), true), $tmp_array);
$post_id = isset($post->ID) ? $post->ID : get_the_ID();
$it_id = 0;

$it = json_decode(wp_json_encode($post), true); 

if(empty($post->it_skin)){  //스킨 지정이 되지 않았을 경우 basic
    $post->it_skin = 'basic';
}

$it_images = isset($it['it_images']) ? maybe_unserialize($it['it_images']) : '';
$skin_dir = gc_shop_skin_path();

$related_items = array();
if($config['de_rel_list_use']){ //관련상품이 값이 있다면
    $related_items = get_metadata('post', $it['it_id'], GC_METAKEY_RELATED, true);   //관련상품 값을 구합니다.
}


add_filter('gc_view_content', 'gc_hook_conv_wp' , 1 , 2 );

/*
// 분류사용, 상품사용하는 상품의 정보를 얻음
$sql = " select a.*, b.ca_name, b.ca_use from {$g5['g5_shop_item_table']} a, {$g5['g5_shop_category_table']} b where a.it_id = '$it_id' and a.ca_id = b.ca_id ";
$it = sql_fetch($sql);
if (!$it['it_id'])
    alert('자료가 없습니다.');
if (!($it['ca_use'] && $it['it_use'])) {
    if (!$is_admin)
        alert('현재 판매가능한 상품이 아닙니다.');
}

// 분류 테이블에서 분류 상단, 하단 코드를 얻음
$sql = " select ca_skin_dir, ca_include_head, ca_include_tail, ca_cert_use, ca_adult_use from {$g5['g5_shop_category_table']} where ca_id = '{$it['ca_id']}' ";
$ca = sql_fetch($sql);

// 본인인증, 성인인증체크
if(!$is_admin) {
    $msg = shop_member_cert_check($it_id, 'item');
    if($msg)
        alert($msg, GC_SHOP_URL);
}

// 오늘 본 상품 저장 시작
// tv 는 today view 약자
$saved = false;
$tv_idx = (int)get_session("ss_tv_idx");
if ($tv_idx > 0) {
    for ($i=1; $i<=$tv_idx; $i++) {
        if (get_session("ss_tv[$i]") == $it_id) {
            $saved = true;
            break;
        }
    }
}

if (!$saved) {
    $tv_idx++;
    set_session("ss_tv_idx", $tv_idx);
    set_session("ss_tv[$tv_idx]", $it_id);
}
// 오늘 본 상품 저장 끝

*/


// 조회수 증가
if (gc_get_cookie('ck_shop_id') != $post_id) {
    $result = $wpdb->query($wpdb->prepare(" update {$gc['shop_item_table']} set it_hit = it_hit + 1 where it_id = %.0f ", $post_id));
    //헤더로 보낼것
    //gc_set_cookie("ck_shop_id", $post_id, GC_SERVER_TIME + 3600); // 1시간동안 저장
}

/*
// 분류 상단 코드가 있으면 출력하고 없으면 기본 상단 코드 출력
if ($ca['ca_include_head'])
    @include_once($ca['ca_include_head']);
else
    include_once('./_head.php');

// 분류 위치
// HOME > 1단계 > 2단계 ... > 6단계 분류
$ca_id = $it['ca_id'];
$nav_skin = $skin_dir.'/navigation.skin.php';
if(!is_file($nav_skin))
    $nav_skin = G5_SHOP_SKIN_PATH.'/navigation.skin.php';
include $nav_skin;

// 이 분류에 속한 하위분류 출력
$cate_skin = $skin_dir.'/listcategory.skin.php';
if(!is_file($cate_skin))
    $cate_skin = G5_SHOP_SKIN_PATH.'/listcategory.skin.php';
include $cate_skin;

*/

if (gc_is_admin()) {
    echo '<div class="sit_admin"><a href="'.admin_url('post.php?action=edit&post='.$post_id).'" class="btn_admin" target="_blank">상품 관리</a></div>';
}
?>

<!-- 상품 상세보기 시작 { -->
<?php
// 상단 HTML
echo '<div id="sit_hhtml">'.gc_conv_content($it['it_head_html'], 1).'</div>';

$prev_post = get_previous_post();

if ( !empty($prev_post) ) {
    $prev_title = '이전상품<span class="sound_only"> '.$prev_post->post_title.'</span>';
    $prev_href = '<a href="'.get_permalink( $prev_post->ID ).'" id="siblings_prev">';
    $prev_href2 = '</a>'.PHP_EOL;
} else {
    $prev_title = '';
    $prev_href = '';
    $prev_href2 = '';
}

$next_post = get_next_post();
if ( is_a( $next_post , 'WP_Post' ) ) {
    $next_title = '다음 상품<span class="sound_only"> '.get_the_title( $next_post->ID ).'</span>';
    $next_href = '<a href="'.get_permalink( $next_post->ID ).'" id="siblings_next">';
    $next_href2 = '</a>'.PHP_EOL;
} else {
    $next_title = '';
    $next_href = '';
    $next_href2 = '';
}

// 고객선호도 별점수
$star_score = gc_get_star_image($it['ID']);

global $item_use_count, $item_qa_count, $item_relation_count;

$item_use_count = 0;

// 관리자가 확인한 사용후기의 개수를 얻음
$item_use_count = gc_get_review_count($post_id);

$item_qa_count = 0;

// 상품문의의 개수를 얻음
$item_qa_count = $wpdb->get_var(
        $wpdb->prepare(
        " select count(*) as cnt from `{$gc['shop_item_qa_table']}` where it_id = %.0f ", $post_id)
        );

$item_relation_count = 0;

// 관련상품의 개수를 얻음
if($config['de_rel_list_use']) {
    if( $related_items = get_metadata('post', $it['it_id'], GC_METAKEY_RELATED, true) ){   //관련상품 데이터를 가져옴
        $item_relation_count = count(array_filter($related_items));
    }
}

$sns_share_links = isset($sns_share_links) ? $sns_share_links : '';

// 소셜 관련
$sns_title = gc_get_text($it['post_title']).' | '.get_bloginfo();
$sns_url  = get_permalink( $post_id );
$sns_share_links .= gc_get_sns_share_link('facebook', $sns_url, $sns_title, GC_SHOP_SKIN_URL.'/img/sns_fb_s.png').' ';
$sns_share_links .= gc_get_sns_share_link('twitter', $sns_url, $sns_title, GC_SHOP_SKIN_URL.'/img/sns_twt_s.png').' ';
$sns_share_links .= gc_get_sns_share_link('googleplus', $sns_url, $sns_title, GC_SHOP_SKIN_URL.'/img/sns_goo_s.png');

$is_soldout = false;
// 상품품절체크
if(GC_SOLDOUT_CHECK)
    $is_soldout = gc_is_soldout($it['it_id']);

// 주문가능체크
$is_orderable = true;
//it_tel_inq 는 전화문의
if(!$it['it_use'] || $it['it_tel_inq'] || $is_soldout)
    $is_orderable = false;

$option_item = $supply_item = '';  //초기화
$option_count = $supply_count = 0;  //초기화
if($is_orderable) {
    // 선택 옵션
    $option_item = gc_get_item_options($post->ID, $it['it_option_subject']);

    // 추가 옵션
    $supply_item = gc_get_item_supply($post->ID, $it['it_supply_subject']);

    // 상품 선택옵션 수
    $option_count = 0;
    if($it['it_option_subject']) {
        $temp = explode(',', $it['it_option_subject']);
        $option_count = count($temp);
    }

    // 상품 추가옵션 수
    $supply_count = 0;
    if($it['it_supply_subject']) {
        $temp = explode(',', $it['it_supply_subject']);
        $supply_count = count($temp);
    }
}

if(! function_exists('gc_pg_anchor')){
	function gc_pg_anchor($anc_id) {
		
		$config = GC_VAR()->config;

		global $item_use_count, $item_qa_count, $item_relation_count;
	?>
		<ul class="sanchor">
			<li><a href="#sit_inf" <?php if ($anc_id == 'inf') echo 'class="sanchor_on"'; ?>>상품정보</a></li>
			<li><a href="#sit_use" <?php if ($anc_id == 'use') echo 'class="sanchor_on"'; ?>>사용후기 <span class="item_use_count"><?php echo $item_use_count; ?></span></a></li>
			<li><a href="#sit_qa" <?php if ($anc_id == 'qa') echo 'class="sanchor_on"'; ?>>상품문의 <span class="item_qa_count"><?php echo $item_qa_count; ?></span></a></li>
			<?php if ( get_option('gc_de_baesong_content') ) { ?><li><a href="#sit_dvr" <?php if ($anc_id == 'dvr') echo 'class="sanchor_on"'; ?>>배송정보</a></li><?php } ?>
			<?php if ( get_option('gc_de_change_content') ) { ?><li><a href="#sit_ex" <?php if ($anc_id == 'ex') echo 'class="sanchor_on"'; ?>>교환정보</a></li><?php } ?>
			<?php if($config['de_rel_list_use']) { ?>
			<li><a href="#sit_rel" <?php if ($anc_id == 'rel') echo 'class="sanchor_on"'; ?>>관련상품 <span class="item_relation_count"><?php echo $item_relation_count; ?></span></a></li>
			<?php } ?>
		</ul>
	<?php
	}	//end funtion
}	//end if

$it_args = array(
    'it'    => $it,
    'it_images' => $it_images,
    'is_orderable'  => $is_orderable,   //주문가능한지 여부
    'star_score'    =>  $star_score,    //고객선호도
    'sns_share_links'   =>  $sns_share_links,
    'option_item'   => $option_item,
    'supply_item'   => $supply_item,
    'is_soldout'    =>  $is_soldout,
    'prev_href' =>  $prev_href,
    'next_href' =>  $next_href,
    'skin_dir'  => $skin_dir,
    'prev_title'    => $prev_title,
    'prev_href2'    => $prev_href2,
    'next_title'    => $next_title,
    'next_href2'    => $next_href2,
    'related_items' =>  $related_items, //관련상품들
    'option_count'  => $option_count,
    'supply_count'  => $supply_count,
    'item_info' => $item_info,
);

// 상품 구입폼
gc_skin_load('item.form.skin.php', $it_args);

// 상품 상세정보
gc_skin_load('item.info.skin.php', $it_args);

// 하단 HTML
echo gc_conv_content($it['it_tail_html'], 1);
?>