$(function(){
    $(document).bind('click',function(){
        $("#kehuList").hide();
        if($("#inventoryId").val()==0){
            $("#searchKehuInput").val('');
        }
    });
    $('#searchKehuInput').bind('input propertychange', function() {
        $("#inventoryId").val(0);
        clearTimeout(jishiqi);
        var val = $(this).val();
        jishiqi=setTimeout(function(){getpdtList(val);},500);
    });
    $('.dhd_adddinghuodan_1_right_02').click(function(eve){
        $("#kehuList").show();
        var keyword = $("#searchKehuInput").val();
        getpdtList(keyword);
        stopPropagation(eve);
    });
    $('#searchKehuInput').click(function(eve){
        if($("#kehuList").css("display")=="none"){
            $("#kehuList").show();
            getpdtList('');
        }
        stopPropagation(eve);
    });
    $('.splist_up_01_left_01_up').click(function(eve){
        $(this).toggleClass('openIcon');
        $('.splist_up_01_left_01_down').slideToggle(200);
        stopPropagation(eve);
    });
     $(".sprukulist_01").click(function(eve){
        $("#riqilan").slideToggle(200);
        stopPropagation(eve);
     });
});
function getpdtList(keyword){
    $("#kehuList ul").html('<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>');
    var hasIds = '0';
    $.ajax({
        type: "POST",
        url: "/erp_service.php?action=getPdtList&id=0&storeId=0",
        data: "keyword="+keyword,
        dataType:'text',timeout : 8000,
        success: function(resdata){
            $("#kehuList ul").html(resdata);
        }
    });
}
function selectRow(id,inventoryId,sn,title,key_vals,productId,unitstr,kucun){
    $("#kehuList").hide();
    $("#searchKehuInput").val(title);
    $("#inventoryId").val(inventoryId);
}