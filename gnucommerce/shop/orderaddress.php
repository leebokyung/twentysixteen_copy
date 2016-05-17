<?php
if( !defined('GC_NAME') ) exit;

if(!is_user_logged_in())
    die('회원이시라면 회원로그인 후 이용해 주십시오.');

$user_address_list = gc_get_user_address_list();

if( empty($user_address_list) || !count($user_address_list) ){
    die('배송지 목록 자료가 없습니다.');
}

?>
<div id="sod_addr" class="new_win sod_addr">

    <h1 id="win_title">최근 배송지 목록</h1>

    <div class="tbl_head01 tbl_wrap">
        <table class="gc_cart_table">
        <colgroup>
        <col>
        <col>
        <col>
        <col>
        <col>
        </colgroup>
        <thead class="hidden_title">
        <tr>
            <th scope="col">배송지명</th>
            <th scope="col">이름</th>
            <th scope="col">전화번호</th>
            <th scope="col">주소</th>
            <th scope="col">관리</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $sep = chr(30);
        $i = 0;
        foreach( $user_address_list as $row ){
            $row = wp_parse_args($row, gc_get_user_address_keys(1));
            $addr = $row['ad_name'].$sep.$row['ad_tel'].$sep.$row['ad_hp'].$sep.$row['ad_zip'].$sep.$row['ad_addr1'].$sep.$row['ad_addr2'].$sep.$row['ad_addr3'].$sep.$row['ad_jibeon'].$sep.$row['ad_subject'];
        ?>
        <tr>
            <td class="td_name">
                <span class="title_label">배송지명</span>
                <?php echo $row['ad_subject']; ?>
            </td>
            <td class="td_namesmall"><span class="title_label">이름</span><?php echo $row['ad_name']; ?></td>
            <td class="td_numbig"><span class="title_label">전화번호</span><div><?php echo $row['ad_tel']; ?><br><?php echo $row['ad_hp']; ?></div></td>
            <td><span class="title_label">주소</span><?php echo gc_print_address($row['ad_addr1'], $row['ad_addr2'], $row['ad_addr3'], $row['ad_jibeon']); ?></td>
            <td class="td_mng">
                <input type="hidden" value="<?php echo $addr; ?>">
                <button type="button" class="sel_address btn_frmline" >선택</button>
                <a href="#" class="del_address btn_frmline" data-ad-id="<?php echo $row['ad_id'];?>">삭제</a>
            </td>
        </tr>
        <?php
        $i++;
        }   //end foreach
        ?>
        </tbody>
        </table>
    </div>

    <div class="win_btn">
        <button type="button" class="gc_popup_close">닫기</button>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    $(".gc_popup_close").on("click", function(e){  //닫기
        e.preventDefault();
        $(".popModal").find('.close').trigger("click");
    });

    $(".sel_address").on("click", function(e) { //선택
        e.preventDefault();
        var addr = $(this).siblings("input").val().split(String.fromCharCode(30)),
            f = document.forderform,
            temp_zipcode = f.od_b_zip.value;

        f.od_b_name.value        = addr[0];
        f.od_b_tel.value         = addr[1];
        f.od_b_hp.value          = addr[2];
        f.od_b_zip.value         = addr[3];
        f.od_b_addr1.value       = addr[4];
        f.od_b_addr2.value       = addr[5];
        f.od_b_addr3.value       = addr[6];
        f.od_b_addr_jibeon.value = addr[7];
        f.ad_subject.value       = addr[8];

        var zipcode = addr[3].replace(/[^0-9]/g, "");

        if(zipcode != "") {
            var code = String(zipcode);

            if(temp_zipcode != code) {
                calculate_sendcost(code);
            }
        }

        $(".gc_popup_close").trigger("click");
    });

    $(".del_address").on("click", function(e) {
        e.preventDefault();

        var ad_id = $(this).attr("data-ad-id"),
            $othis = $(this);

        if (ad_id && confirm("배송지 목록을 삭제하시겠습니까?")) {
            $.ajax({
                type: "POST",
                data: {
                    action : 'gc_order_address_delete', ad_id : ad_id
                },
                url: gnucommerce.ajaxurl,
                cache: false,
                async: false,
                success: function(data) {
                    if( data === "true" ){
                        $othis.parents("tr").hide("slow", function(){
                            $(this).remove();
                        });
                    } else {
                        alert("error");
                    }
                }
            });
        }
        return;
    });

    // 전체선택 부분
    $("#chk_all").on("click", function() {
        if($(this).is(":checked")) {
            $("input[name^='chk[']").attr("checked", true);
        } else {
            $("input[name^='chk[']").attr("checked", false);
        }
    });

});
</script>