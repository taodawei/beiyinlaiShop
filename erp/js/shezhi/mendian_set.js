layui.use(['form','upload'], function(){
	var form = layui.form,upload = layui.upload;
	form.on('submit(tijiao)', function(data){
		layer.load();
		$("#productSetForm").submit();
		return false;
	});
    form.on('select(fanli_type)', function(data){
        if(data.value=='1'){
            $("#tuanzhang_rule_div").hide();
        }else{
            $("#tuanzhang_rule_div").show();
        }
    });
	upload.render({
		elem: '#upload_gift_img'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,size:100
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $("#gift_img").val(res.url);
                $("#upload_gift_img").attr('src',res.url);
            }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
	});
	
	upload.render({
		elem: '#upload_share_img'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,size:100
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $("#share_img").val(res.url);
                $("#upload_share_img").attr('src',res.url);
            }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
	});
	
	upload.render({
		elem: '#upload_back_img'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,size:500
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $("#back_img").val(res.url);
                $("#upload_back_img").attr('src',res.url);
            }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
	});
    upload.render({
        elem: '#upload_zhishang_back'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,size:500
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $("#zhishang_back").val(res.url);
                $("#upload_zhishang_back").attr('src',res.url);
            }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#upload_yaoqing_back'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,size:500
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $("#yaoqing_back").val(res.url);
                $("#upload_yaoqing_back").attr('src',res.url);
            }
        }
        ,error: function(){
            layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    $("#add_area_btn").click(function(){
        $("#spxx_shanchu_tanchu").show();
    });
    $("#shangshangji_fanli").change(function(){
        var shangshangji_bili = parseFloat($("#shangshangji_fanli").val());
        var shangji_bili = parseFloat($("#shangji_fanli").val());
        if(!isNaN(shangji_bili) && !isNaN(shangshangji_bili)){
            if((shangshangji_bili+shangji_bili)>100){
                layer.msg('上级返佣比例+上上级/团长返佣比例不能超过100%', {icon: 5});
                $(this).focus();
                return false;
            }else{
                $("#user_fanli").val(100-shangshangji_bili-shangji_bili);
            }
        }
    });
    $("#shangji_fanli").change(function(){
        var shangshangji_bili = parseFloat($("#shangshangji_fanli").val());
        var shangji_bili = parseFloat($("#shangji_fanli").val());
        if(!isNaN(shangji_bili) && !isNaN(shangshangji_bili)){
            if((shangshangji_bili+shangji_bili)>100){
                layer.msg('上级返佣比例+上上级/团长返佣比例不能超过100%', {icon: 5});
                $(this).focus();
                return false;
            }else{
                $("#user_fanli").val(100-shangshangji_bili-shangji_bili);
            }
        }
    });
});
function add_reason(dom,name){
	$(dom).before('<input type="text" name="'+name+'[]" value="" placeholder="'+(name=='tousu_reason'?'填写投诉原因':'填写退换货原因')+'">');
}
function add_yuming(dom){
	$(dom).before('<input type="text" name="website[]" class="layui-input" style="width:280px;display:inline-block;margin-right:10px;"/>');
}
function addStore(){
    var areaIds = $("#areaIds").val();
    var areaNames = $("#areaIdsFanwei").val();
    var storeId = $("#add_store_id option:selected").val();
    var storeName=$("#add_store_id option:selected").html();
    if(areaIds==''){
        layer.msg("请先选择区域");
        return false;
    }
    layer.load();
    ajaxpost=$.ajax({
        type: "POST",
        url: "?s=mendian_set&a=addFahuoStore",
        data: "areaIds="+areaIds+"&storeId="+storeId,
        dataType : "json",timeout : 10000,
        success: function(resdata) {
            layer.closeAll();
            layer.msg(resdata.message);
            if(resdata.code==1){
                var str = '<div style="margin:8px;line-height:35px;margin-left:45px;">'+areaNames+'&nbsp;&nbsp;发货仓库：'+storeName+'&nbsp;&nbsp;<a href="javascript:" onclick="del_fahuo_store(this,'+resdata.id+');" style="color:red;">删除</a></div>';
                $("#fahuo_areas").append(str);
            }
            $("#spxx_shanchu_tanchu").hide();
            $("#areaIds").val('');
            $("#areaIdsFanwei").val('');
            $("#spxx_shanchu_tanchu").hide();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            layer.msg("请求超时，请检查网络");
            hide_myModal();
        }
    });
}
function del_fahuo_store(dom,id){
    layer.confirm('确定要删除吗？', {
        btn: ['确定','取消'],
    },function(){
        $(dom).parent().remove();
        layer.closeAll();
        ajaxpost=$.ajax({
            type: "POST",
            url: "?s=mendian_set&a=delFahuoStore",
            data: "id="+id,
            dataType : "json",timeout : 8000,
            success: function(resdata) {}
        });
    });
}

//选择区域
function area_fanwei(modelId){
    $("#myModal").css("top","30px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $('#myModal').reveal();
    $("#editId").val(modelId);
    var departs = $("#"+modelId).val();
    var departNames = $("#"+modelId+"Fanwei").val();
    $("#departs").val(departs);
    $("#departNames").val(departNames);
    ajaxpost=$.ajax({
        type: "POST",
        url: "?s=yyyx&a=getAreas",
        data: "departs="+departs+"&departNames="+departNames,
        dataType : "text",timeout : 10000,
        success: function(data) {
            $('#myModal').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            layer.msg("请求超时，请检查网络");
            hide_myModal();
        }
    });
}
function area_baocun(){
    var modelId = $("#editId").val();
    var departs = $("#departs").val();
    var departNames = $("#departNames").val();
    $("#"+modelId).val(departs);
    var fanwei = departNames;
    $("#"+modelId+"Fanwei").val(fanwei);
    $("#editId").val('0');
    $("#departNames").val('');
    $("#departs").val('');
    hide_myModal();
}
function get_areas(id){
    var img = $("img.depart_select_img[data-id="+id+"]").attr("src");
    if(img=='images/tree_bg2.jpg'){
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg1.jpg");
    }else{
        $("img.depart_select_img[data-id="+id+"]").attr("src","images/tree_bg2.jpg");
    }
    $("#departUsers"+id).slideToggle(100);
    if($("#departUsers"+id).html()==""){
        $("#departUsers"+id).html("<li><img src='images/loading.gif'></li>");
        ajaxpost=$.ajax({
            type:"POST",
            url:"?s=yyyx&a=getAreasByPid",
            data:"id="+id,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#departUsers"+id).html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function search_areas(keyword){
    if(keyword==''){
        $("#depart_users").show();
        $("#search_users").hide();
    }else{
        $("#depart_users").hide();
        $("#search_users").html("<li><img src='images/loading.gif'></li>").show();
        ajaxpost=$.ajax({
            type:"POST",
            url:"?s=yyyx&a=getAreasByPid",
            data:"keyword="+keyword,
            timeout:"10000",
            dataType:"text",
            success: function(html){
              if(html==""){
                
              }else{
                $("#search_users").html(html);
            }
        },
        error:function(){
            alert("系统错误，请刷新重试");
        }
    });
    }
}
function add_area_depart(id,name){
    var ids = $("#departs").val();
    var idarray = ids.split(",");
    if(idarray.indexOf(""+id)>-1){
        alert(name+"已经在范围内了");
    }else{
        if(ids.length>0){
            $("#departs").val($("#departs").val()+","+id);
            $("#departNames").val($("#departNames").val()+","+name);
        }else{
            $("#departs").val(id);
            $("#departNames").val(name);
        }
        $(".splc_cont_left_con ul").append('<li id="left_depart'+id+'"><div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_area_depart('+id+',\''+name+'\')"><img src="images/close1.png" border="0"></a></div><div class="clearBoth"></div><div class="shenpi_set_add_03"><div class="gg_people_show_3_1"><img src="images/sp_bm.png"></div>'+name+'</div></li>');
    }
}
function del_area_depart(id,name){
    nowValue = $("#departs").val();nowValue1 = $("#departNames").val();
    departArray = nowValue.split(",");departArray1 = nowValue1.split(",");
    var index = departArray.indexOf(""+id);
    if (index > -1) {
        departArray.splice(index, 1);
        departArray1.splice(index, 1);
        nowValue = departArray.join();nowValue1 = departArray1.join();
        $("#departs").val(nowValue);$("#departNames").val(nowValue1);
        $("#left_depart"+id).remove();
    }
}
function hide_myModal(){
    if(ajaxpost){
        ajaxpost.abort();
    }
    $("#myModal").css("opacity","0").css("display","none");
    $(".reveal-modal-bg").fadeOut(200);
    $("#myModal").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
}