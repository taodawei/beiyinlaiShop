layui.use(['laydate','form','upload'], function(){
    var laydate = layui.laydate
    ,form = layui.form
    ,upload = layui.upload
    var uploadInit = upload.render({
        elem: '#uploadImg'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#uploadImg').html('<img src="'+res.url+'" width="720">');
                $("#originalPic").val(res.url);
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    form.on('submit(tijiao)', function(data){
        if($("#originalPic").val()==''){
            layer.msg('请上传图片',function(){});
            return false;
        }
        layer.load();
    });
});
$(function(){
     $(document).bind('click',function(){
        $("#kehuList").hide();
        if($("#inventoryId").val()==0){
            $("#searchKehuInput").val('');
        }
    });
    $('#searchKehuInput').bind('input propertychange', function() {
        $("#inventoryId").val(0);
        // clearTimeout(jishiqi);
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