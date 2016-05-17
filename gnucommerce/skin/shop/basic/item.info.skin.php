<?php
if (!defined('GC_NAME')) exit; // 개별 페이지 접근 불가
do_action( GC_NAME.'_skin_action', __FILE__, plugin_dir_path( __FILE__) );
?>

<!-- 상품 정보 시작 { -->
<section id="sit_inf">
    <h2><?php _e('상품 정보', GC_NAME); ?></h2>
    <?php echo gc_pg_anchor('inf'); ?>

    <?php if ($it['it_basic']) { // 상품 기본설명 ?>
    <h3><?php _e('상품 기본설명', GC_NAME); ?></h3>
    <div id="sit_inf_basic">
         <?php echo $it['it_basic']; ?>
    </div>
    <?php } ?>

    <?php if ($post->post_content) { // 상품 상세설명 ?>
    <h3><?php _e('상품 상세설명', GC_NAME); ?></h3>
    <div id="sit_inf_explan">
        <?php echo do_shortcode(gc_hook_conv_wp('', $post->post_content)); ?>
    </div>
    <?php } ?>

    <?php
    if ($it['it_info_value']) { // 상품 정보 고시
        $info_data = unserialize(stripslashes($it['it_info_value']));
        if(is_array($info_data)) {
            $gubun = $it['it_info_gubun'];
            $info_array = $item_info[$gubun]['article'];
    ?>
    <h3><?php _e('상품 정보 고시', GC_NAME); ?></h3>
    <table id="sit_inf_open">
    <colgroup>
        <col class="grid_4">
        <col>
    </colgroup>
    <tbody>
    <?php
    foreach($info_data as $key=>$val) {
        $ii_title = $info_array[$key][0];
        $ii_value = $val;
    ?>
    <tr>
        <th scope="row"><?php echo $ii_title; ?></th>
        <td><?php echo $ii_value; ?></td>
    </tr>
    <?php } //foreach?>
    </tbody>
    </table>
    <!-- 상품정보고시 end -->
    <?php
        } else {
            if($is_admin) {
                echo '<p>상품 정보 고시 정보가 올바르게 저장되지 않았습니다.<br>config.php 파일의 GC_ESCAPE_FUNCTION 설정을 addslashes 로<br>변경하신 후 관리자 &gt; 상품정보 수정에서 상품 정보를 다시 저장해주세요. </p>';
            }
        }
    } //if
    ?>

</section>
<!-- } 상품 정보 끝 -->

<!-- 사용후기 시작 { -->
<section id="sit_use">
    <h2><?php _e('사용후기', GC_NAME); ?></h2>
    <?php echo gc_pg_anchor('use'); ?>

    <div id="itemuse">
        <?php
            //인클루드를 쓰지 말고 comments_template 함수를 쓸것, https://wordpress.org/support/topic/have_comments-help
            include_once(GC_SHOP_DIR_PATH.'/itemuse.php');
        ?>
    </div>
</section>
<!-- } 사용후기 끝 -->

<!-- 상품문의 시작 { -->
<section id="sit_qa">
    <h2><?php _e('상품문의', GC_NAME); ?></h2>
    <?php echo gc_pg_anchor('qa'); ?>

    <div id="itemqa"><?php include_once(GC_SHOP_DIR_PATH.'/itemqa.php'); ?></div>
</section>
<!-- } 상품문의 끝 -->

<?php if ($de_baesong_content = get_option('gc_de_baesong_content')) { // 배송정보 내용이 있다면 ?>
<!-- 배송정보 시작 { -->
<section id="sit_dvr">
    <h2><?php _e('배송정보', GC_NAME); ?></h2>
    <?php echo gc_pg_anchor('dvr'); ?>

    <?php echo apply_filters('gc_view_content', gc_conv_content($de_baesong_content, 1), $de_baesong_content); ?>
</section>
<!-- } 배송정보 끝 -->
<?php } ?>


<?php if ($de_change_content = get_option('gc_de_change_content') ) { // 교환/반품 내용이 있다면 ?>
<!-- 교환/반품 시작 { -->
<section id="sit_ex">
    <h2><?php _e('교환/반품', GC_NAME); ?></h2>
    <?php echo gc_pg_anchor('ex'); ?>

    <?php echo apply_filters('gc_view_content', gc_conv_content($de_change_content, 1), $de_change_content); ?>
</section>
<!-- } 교환/반품 끝 -->
<?php } ?>

<?php if( !empty($related_items) ){ //관련상품이 있다면 ?>
<!-- 관련상품 시작 { -->
<section id="sit_rel">
    <h2><?php _e('관련상품', GC_NAME); ?></h2>
    <?php echo gc_pg_anchor('rel'); ?>

    <div class="sct_wrap">
        <?php
            gc_skin_load($config['de_rel_list_skin'], array('related_items'=>$related_items)); //관련 상품을 출력합니다.
        ?>
    </div>
</section>
<!-- } 관련상품 끝 -->
<?php } ?>