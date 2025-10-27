function fanwei(modelId){
    $("#myModal").css("top","30px").html('<div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>');
    $('#myModal').reveal();
    $("#editId").val(modelId);
    var departs = $("#departs_"+modelId).val();
    var departNames = $("#departNames_"+modelId).val();
    var users = $("#users_"+modelId).val();
    var userNames = $("#userNames_"+modelId).val();
    $("#departs").val(departs);
    $("#departNames").val(departNames);
    $("#users").val(users);
    $("#userNames").val(userNames);
    ajaxpost=$.ajax({
        type: "POST",
        url: "/erp_service.php",
        data: "action=getDinghuoFanwei&departs="+departs+"&users="+users+"&departNames="+departNames+"&userNames="+userNames,
        dataType : "text",timeout : 8000,
        success: function(data) {
            $('#myModal').html(data);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(textStatus);
        }
    });
}
function baocun(){
    var modelId = $("#editId").val();
    var departs = $("#departs").val();
    var departNames = $("#departNames").val();
    var users = $("#users").val();
    var userNames = $("#userNames").val();
    $("#departs_"+modelId).val(departs);
    var fanwei = departNames;
    $("#departNames_"+modelId).val(fanwei);
    $("#users_"+modelId).val(users);
    $("#userNames_"+modelId).val(userNames);
    if($("#userNames").val()!=''){
        if(fanwei==''){
            fanwei = $("#userNames").val();
        }else{
            fanwei = fanwei+','+$("#userNames").val();
        }
    }
    $("#fanwei_"+modelId).html(fanwei).attr("title",fanwei);
    var re = $("#return").val();
    if(modelId=='zongkuguan'){
        layer.load();
        var zongkuguan_id = $("#zongkuguan_id").val();
        $.ajax({
            type: "POST",
            url: "?m=system&s=quanxian&a=updateZongkuguan",
            data: "id="+zongkuguan_id+"&departs="+departs+"&users="+users+"&departNames="+departNames+"&userNames="+userNames,
            dataType : "json",timeout : 10000,
            success: function(resdata) {
                $("#zongkuguan_id").val(resdata.id);
                layer.closeAll();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                layer.msg("网络异常，请刷新页面重试");
            }
        });
    }else if(re=='kucun'){
        layer.load();
        var qxId = $("#qxid_"+modelId).val();
        $.ajax({
            type: "POST",
            url: "?m=system&s=quanxian&a=updateFanwei",
            data: "departs="+departs+"&departNames="+departNames+"&users="+users+"&userNames="+userNames+"&id="+qxId+"&storeId="+modelId,
            dataType:"json",timeout : 30000,
            success: function(resdata){
                $("#qxid_"+modelId).val(resdata.id);
                $("#functions_"+modelId+" input").attr("data-id",resdata.id);
                layer.closeAll();
            },
            error: function() {
              layer.msg('网络异常，请刷新页面重试');
            }
        });
    }
    $("#editId").val('0');
    $("#departNames").val('');
    $("#users").val('');
    $("#userNames").val('');
    $("#departs").val('');
    hide_myModal();
}
function get_users(id){
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
            url:"/erp_service.php?action=get_fanwei_users",
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
function search_users(keyword){
    if(keyword==''){
        $("#depart_users").show();
        $("#search_users").hide();
    }else{
        $("#depart_users").hide();
        $("#search_users").html("<li><img src='images/loading.gif'></li>").show();
        ajaxpost=$.ajax({
            type:"POST",
            url:"/erp_service.php?action=get_fanwei_users",
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
function add_depart(id,name){
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
        $(".splc_cont_left_con ul").append('<li id="left_depart'+id+'"><div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_depart('+id+',\''+name+'\')"><img src="images/close1.png" border="0"></a></div><div class="clearBoth"></div><div class="shenpi_set_add_03"><div class="gg_people_show_3_1"><img src="images/sp_bm.png"></div>'+name+'</div></li>');
    }
}
function add_user(id,name){
    var ids = $("#users").val();
    var idarray = ids.split(",");
    if(idarray.indexOf(""+id)>-1){
        alert(name+"已经在范围内了");
    }else{
        if(ids.length>0){
            $("#users").val($("#users").val()+","+id);
            $("#userNames").val($("#userNames").val()+","+name);
        }else{
            $("#users").val(id);
            $("#userNames").val(name);
        }
        $(".splc_cont_left_con ul").append('<li id="left_user'+id+'"><div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_user('+id+',\''+name+'\')"><img src="images/close1.png" border="0"></a></div><div class="clearBoth"></div><div class="shenpi_set_add_03"><div class="gg_people_show_3_1">'+name.substring(name.length-2)+'</div>'+name+'</div></li>');
    }
}
function del_depart(id,name){
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
function del_user(id,name){
    nowValue = $("#users").val();nowValue1 = $("#userNames").val();
    departArray = nowValue.split(",");departArray1 = nowValue1.split(",");
    var index = departArray.indexOf(""+id);
    if (index > -1) {
        departArray.splice(index, 1);
        departArray1.splice(index, 1);
        nowValue = departArray.join();nowValue1 = departArray1.join();
        $("#users").val(nowValue);$("#userNames").val(nowValue1);
        $("#left_user"+id).remove();
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