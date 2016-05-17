<?php
if( !defined('GC_NAME') ) exit;

if ( !is_user_logged_in() ){
    gc_not_permission_page( add_query_arg(array('view'=>'wishlist')) );
}
$gc_ajax_nonce = wp_create_nonce( "item_form" );
?>

<!-- 위시리스트 시작 { -->
<div id="sod_ws">

    <form name="fwishlist" method="post" action="./cartupdate.php">
    <input type="hidden" name="act"       value="multi">
    <input type="hidden" name="sw_direct" value="">
    <input type="hidden" name="prog"      value="wish">

    <div class="tbl_head01 tbl_wrap">
        <table>
        <thead>
        <tr>
            <th scope="col" class="td_chk">선택</th>
            <th scope="col" class="td_img">이미지</th>
            <th scope="col">상품명</th>
            <th scope="col">보관일시</th>
            <th scope="col" class="td_mngsmall">삭제</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sql  = " select a.wi_id, a.wi_time, b.* from {$gc['shop_wish_table']} a left join {$gc['shop_item_table']} b on ( a.it_id = b.it_id ) ";
        $sql .= $wpdb->prepare(" where a.mb_id = '%s' order by a.wi_id desc ", get_current_user_id() );
        $result = $wpdb->get_results($sql, ARRAY_A);
        
        $i = 0;
        foreach($result as $row) {

            if( empty($row) ) continue;

            $out_cd = '';
            $sql = $wpdb->prepare(" select count(*) as cnt from {$gc['shop_item_option_table']} where it_id = %.0f and io_type = '0' ", $row['it_id']);

            $tmp_cnt = $wpdb->get_var($sql);

            if($tmp_cnt)
                $out_cd = 'no';

            $it_price = gc_get_price($row);

            if ($row['it_tel_inq']) $out_cd = 'tel_inq';

            $image = gc_get_it_image($row['it_id'], 70, 70);
        ?>

        <tr>
            <td class="td_chk">
                <?php
                // 품절검사
                if(gc_is_soldout($row['it_id']))
                {
                ?>
                품절
                <?php } else { //품절이 아니면 체크할수 있도록한다 ?>
                <label for="chk_it_id_<?php echo $i; ?>" class="sound_only"><?php echo $row['it_name']; ?></label>
                <input type="checkbox" name="chk_it_id[<?php echo $i; ?>]" value="1" id="chk_it_id_<?php echo $i; ?>" onclick="out_cd_check(this, '<?php echo $out_cd; ?>');">
                <?php } ?>
                <input type="hidden" name="it_id[<?php echo $i; ?>]" value="<?php echo $row['it_id']; ?>">
                <input type="hidden" name="io_type[<?php echo $row['it_id']; ?>][0]" value="0">
                <input type="hidden" name="io_id[<?php echo $row['it_id']; ?>][0]" value="">
                <input type="hidden" name="io_value[<?php echo $row['it_id']; ?>][0]" value="<?php echo $row['it_name']; ?>">
                <input type="hidden"   name="ct_qty[<?php echo $row['it_id']; ?>][0]" value="1">
            </td>
            <td class="sod_ws_img"><?php echo $image; ?></td>
            <td class="td_tit"><a href="<?php echo get_permalink($row['it_id']); ?>"><?php echo stripslashes($row['it_name']); ?></a></td>
            <td class="td_datetime"><?php echo $row['wi_time']; ?></td>
            <td class="td_mngsmall"><a href="#" data_wi_id="<?php echo $row['wi_id']; ?>" class="wi_id_delete">삭제</a></td>
        </tr>
        <?php
        $i++;
        }   //end foreach

        if ($i == 0)
            echo '<tr><td colspan="5" class="empty_table">보관함이 비었습니다.</td></tr>';
        ?>
        </tr>
        </tbody>
        </table>
    </div>

    <div id="sod_ws_act">
        <button type="submit" class="btn01" onclick="return fwishlist_check(document.fwishlist,'');">장바구니 담기</button>
        <button type="submit" class="btn02" onclick="return fwishlist_check(document.fwishlist,'direct_buy');">주문하기</button>
    </div>
    </form>
</div>

<script>
(function($){
    gnucommerce.wish_ing = false;
    $(".wi_id_delete").on("click", function(e){
        e.preventDefault();

        if( gnucommerce.wish_ing ){ //삭제중이면 return;
            return;
        }

        gnucommerce.wish_ing = true;
        var $othis = $(this),
            wi_id = $othis.attr("data_wi_id");
            formData = "action=gc_wish_delete&gw=d&security=<?php echo $gc_ajax_nonce;?>&wi_id="+wi_id;
        
        var ajax_var = jQuery.ajax({
            type:"POST",
            url: gnucommerce.ajaxurl,
            data:formData,
            dataType   : 'json', // xml, html, script, json
            cache: false,
            success:function(data, status, xhr){
                if( data.msg == 'true' ){
                    $othis.parents("tr").fadeOut("slow", function(){
                        $(this).remove();
                    });
                } else {
                    alert( data.msg );
                }
                gnucommerce.wish_ing = false;
            },
            error : function(request, status, error){
                alert('false ajax :'+request.responseText);
                gnucommerce.wish_ing = false;
            }
        }); // end of ajax

        return false;
    });
})(jQuery);
    function out_cd_check(fld, out_cd)
    {
        if (out_cd == 'no'){
            alert("옵션이 있는 상품입니다.\n\n상품을 클릭하여 상품페이지에서 옵션을 선택한 후 주문하십시오.");
            fld.checked = false;
            return;
        }

        if (out_cd == 'tel_inq'){
            alert("이 상품은 전화로 문의해 주십시오.\n\n장바구니에 담아 구입하실 수 없습니다.");
            fld.checked = false;
            return;
        }
    }

    function fwishlist_check(f, act)
    {
        var k = 0;
        var length = f.elements.length;

        for(i=0; i<length; i++) {
            if (f.elements[i].checked) {
                k++;
            }
        }

        if(k == 0)
        {
            alert("상품을 하나 이상 체크 하십시오");
            return false;
        }

        if (act == "direct_buy")
        {
            f.sw_direct.value = 1;
        }
        else
        {
            f.sw_direct.value = 0;
        }

        return true;
    }
</script>
<!-- } 위시리스트 끝 -->