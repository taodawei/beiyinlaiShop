function addRow(){
  var rows = parseInt($("#row_table").attr("data-row"));
  rows = rows+1;
  var content = '<tr height="38" id="row_'+rows+'">'+
      '<td bgcolor="#ffffff" width="450" align="left" valign="middle">'+
          '<input type="text" id="fanwei_'+rows+'" readonly="true" placeholder="请选择分类/商品" onclick="fanwei(\''+rows+'\');" class="jiaoyijifen_up_zhidingshangpinfenlei_2">'+
      '</td>'+
      '<td bgcolor="#ffffff" width="100" align="left" valign="middle">'+
          '<input type="text" name="jifen_'+rows+'" lay-verify="shuzi" class="jiaoyijifen_up_zhidingshangpinfenlei_22">'+
      '</td>'+
      '<td bgcolor="#ffffff" width="100" align="left" valign="middle">'+
          '<a href="javascript:" onclick="del_row('+rows+');">删除</a>'+
      '</td>'+
      '<input type="hidden" name="departs_'+rows+'" id="departs_'+rows+'" value="">'+
      '<input type="hidden" name="users_'+rows+'" id="users_'+rows+'" value="">'+
      '<input type="hidden" name="departNames_'+rows+'" id="departNames_'+rows+'" value="">'+
      '<input type="hidden" name="userNames_'+rows+'" id="userNames_'+rows+'" value="">'+
      '<input type="hidden" name="rows[]" value="'+rows+'">'+
  '</tr>';
  $("#row_table").attr("data-row",rows).append(content);
}
function del_row(delrow){
  $("#row_"+delrow).remove();
}
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
        url: "?s=mendian_set&a=getPdtFanwei",
        data: "departs="+departs+"&users="+users+"&departNames="+departNames+"&userNames="+userNames,
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
    $("#fanwei_"+modelId).val(fanwei).attr("title",fanwei);
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
            url:"?s=mendian_set&a=getPdtsByChannel",
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
            url:"?s=mendian_set&a=getPdtsByChannel",
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
        $(".splc_cont_left_con ul").append('<li id="left_user'+id+'"><div class="shenpi_add_2_dele"><a href="javascript:void(0)" onclick="del_user('+id+',\''+name+'\')"><img src="images/close1.png" border="0"></a></div><div class="clearBoth"></div><div class="shenpi_set_add_03"><div class="gg_people_show_3_1">商品</div>'+name+'</div></li>');
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