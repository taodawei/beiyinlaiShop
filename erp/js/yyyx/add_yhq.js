var layForm;
layui.use(['form','laydate'], function(){
	var laydate = layui.laydate;
	layForm = layui.form;
	laydate.render({
		elem: '#day_time'
		,type: 'time'
		,format: 'HH:mm:ss'
	});
	laydate.render({
		elem: '#startTime'
		,type: 'datetime'
        ,format: 'yyyy-MM-dd HH:mm:ss'
	});
	laydate.render({
		elem: '#endTime'
		,type: 'datetime'
        ,format: 'yyyy-MM-dd HH:mm:ss'
        ,done: function(value, date, endDate){
        	$("#yulan_time").html($("#startTime").val()+' -<Br>'+value);
        }
	});
	layForm.verify({
		shuzi:function(value,item){
            if(value==''){value=0;}
			value = parseFloat(value);
			if(isNaN(value)||value<0){
				return '字段不能小于0';
			}
		}
	});
	layForm.on('radio(man1)',function(){
		$("#man").addClass('disabled').prop('readonly',true);
		$("#yulan_man").html('不限制');
	});
	layForm.on('radio(man2)',function(){
		$("#man").removeClass('disabled').prop('readonly',false);
	});
	layForm.on('radio(area1)',function(){
		$("#area_div").hide();
	});
	layForm.on('radio(area2)',function(){
		$("#area_div").show();
	});
	layForm.on('radio(type1)',function(){
		$("#pdt_div").hide();
		$("#yulan_xianzhi").html('全部商品可用');
	});
	layForm.on('radio(type2)',function(){
		$("#pdt_div").show();
		$("#yulan_xianzhi").html('部分商品可用');
	});
	layForm.on('radio(level1)',function(){
		$("#level_div").hide();
	});
	layForm.on('radio(level2)',function(){
		$("#level_div").show();
	});
	layForm.on('radio(mendian1)',function(){
		$(".addshengriquan_left_03_mendian").hide();
	});
	layForm.on('radio(mendian2)',function(){
		$(".addshengriquan_left_03_mendian").show();
	});
	layForm.on('checkbox(if_day_limit)',function(data){
		if(data.elem.checked){
			$(".addshengriquan_left_03_right_fafangliang_3").show(50);
		}else{
			$(".addshengriquan_left_03_right_fafangliang_3").hide(50);
		}
	});
	layForm.on('submit(tijiao)',function(){
		var beginDate=$("#startTime").val();  
		var endDate=$("#endTime").val();
		var d1 = new Date(beginDate.replace(/\-/g, "\/"));
		var d2 = new Date(endDate.replace(/\-/g, "\/"));
		if(d1 >=d2){
			layer.msg("开始时间不能大于结束时间！",function(){});
			return false;
		}
		layer.load();
	});
	$(".addshengriquan_left_yanse_up_right").click(function(){
		$(".addshengriquan_left_yanse_down").toggle();
	});
	$("#title").change(function(){
		$("#yulan_title").html($(this).val());
	});
	$("#money").change(function(){
		$("#yulan_money").html($(this).val());
	});
	$("#man").change(function(){
		$("#yulan_man").html('满￥'+$(this).val()+'可用');
	});
	$("#content").change(function(){
		$("#yulan_content").html('使用说明：<pre>'+$(this).val()+'</pre>');
	});
});
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
function add_user(id,name,unit){
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