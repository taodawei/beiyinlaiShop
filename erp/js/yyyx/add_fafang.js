var nowIndexTime;
$(document).ready(function(){
    $(document).bind('click',function(){ 
        hideTanchu("splist_up_01_left_01");
        hideTanchu("splist_up_01_left_02");
    });
    //上下架状态
    $('.splist_up_01_left_02_up').click(function(eve){
        $(this).toggleClass('openIcon');
        $(this).next().slideToggle(200);
        stopPropagation(eve); 
    });
    //高级搜索
    $('.splist_up_01_right_2_up').click(function(){
        $('.splist_up_01_right_2_down').css({'top':'0','opacity':'1','visibility':'visible'});
    });
    //点击。。。弹窗滑过清除自动隐藏倒计时
    $("#operate_row").hover(function(){
        clearTimeout(nowIndexTime);
    },function(){
        $("#operate_row").hide();
    });
    $(".splist_up_02_1").click(function(){
        $(".splist_up_02").hide();
        $(".splist_up_01").show();
    });
    
});
//隐藏高级搜搜
function hideSearch(){
    $('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function selectLevel(status,title){
    $("#level").val(status);
    $(".splist_up_01_left_02_up").eq(0).find('span').html(title);
    reloadTable(0);
}
function selectMendian(status,title){
    $("#mendianId").val(status);
    $(".splist_up_01_left_02_up").eq(1).find('span').html(title);
    reloadTable(0);
}
//隐藏搜索框
function hideTanchu(className){
    $("."+className+"_up").removeClass("openIcon");
    $("."+className+"_down").slideUp(200);
}
function hideSearch1(){
    $('.zsq_xuanzehuiyuan').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
function reloadTable(curpage){
    var level = $("#level").val();
    var mendianId = $("#mendianId").val();
    var keyword = $("#keyword").val();
    var money_start = $("#money_start").val();
    var money_end = $("#money_end").val();
    var jifen_start = $("#jifen_start").val();
    var jifen_end = $("#jifen_end").val();
    var dtTime_start = $("#dtTime_start").val();
    var dtTime_end = $("#dtTime_end").val();
    var login_start = $("#login_start").val();
    var login_end = $("#login_end").val();
    var selectedIds = $("#selectedIds").val();
    var page = 1;
    if(curpage==1){
        page = $("#page").val();
    }
    var order1 = $("#order1").val();
    var order2 = $("#order2").val();
    productListTalbe.reload({
        where: {
            order1: order1
            ,order2: order2
            ,level:level,
            mendianId:mendianId,
            keyword:keyword,
            money_start:money_start,
            money_end:money_end,
            jifen_start:jifen_start,
            jifen_end:jifen_end,
            dtTime_start:dtTime_start,
            dtTime_end:dtTime_end,
            login_start:login_start,
            login_end:login_end,
            selectedIds:selectedIds
        },page: {
            curr: page
        },initSort: {
            field: order1
            ,type: order2
          }
    });
    $("th[data-field='id']").hide();
}
function changeColor(color){
	$(".addshengriquan_left_yanse_down").hide();
	$(".addshengriquan_left_yanse_up_left span").css("backgroundColor",color);
	$("#color").val(color);
	$(".addshengriquan_right_02_2").css("backgroundColor",color);
}
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
    $("#users").val('');
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
function delRow(rowId){
    var nowValue = $("#selectedIds").val();
    ids = nowValue.split(",");
    var index = ids.indexOf(""+rowId);
    if (index > -1) {
        ids.splice(index, 1);
        nowValue = ids.join();
        $("#selectedIds").val(nowValue);
        $("#row_"+rowId).remove();
        var num = parseInt($("#selectNum").html());
        num--;
        $("#selectNum").html(num);
    }
}