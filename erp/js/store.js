var nowIndexTime,map;
function searchMap(){
	var local = new BMap.LocalSearch(map, {
		renderOptions:{map: map, panel:"r-result"},
		pageCapacity:5
	});
	var areaName = '';
	var sheng = $("#ps1 option:selected").text();
	if(sheng!='选择省份'){
		areaName = sheng;
		var shi = $("#ps2 option:selected").text();
		if(shi!='请先选择省'&&shi!='请选择'){
			areaName = areaName+shi;
		}
	}
	local.search(areaName+$("#e_address").val());
}

$(document).ready(function(){
	//点击。。。弹窗滑过清除自动隐藏倒计时
	$("#operate_row").hover(function(){
		clearTimeout(nowIndexTime);
	},function(){
		$("#operate_row").hide();
	});
});
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+69>document.body.clientHeight){
		top=top-50;
	}

	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	var nowTr = $(".layui-table-main tr[data-index='"+index+"']").eq(0);
	status = nowTr.find("td[data-field='status'] div font").eq(0).html();
	if(status=='已启用'){
		$("#operate_row li").eq(1).html('<a href="javascript:jin_store();"><img src="images/biao_88.png"> 禁用</a>');
	}else{
		$("#operate_row li").eq(1).html('<a href="javascript:qiyong_store();"><img src="images/biao_888.png"> 启用</a>');
	}
	$("#operate_row").css({"top":(top-30)+"px","right":right+"px"});
	if(nowIndex==index){
		$("#operate_row").stop().slideToggle(250);
	}else{
		if($("#operate_row").css("display")=='none'){
			$("#operate_row").stop().slideDown(250);
		}
	}
	$("#nowIndex").val(index);
	return false;
}
//定时隐藏点击。。。出来的弹窗
function hideNext(){
	nowIndexTime = setTimeout(function(){$("#operate_row").hide();},300);
}
//删除单个仓库
function del_store(){
	var zindex = $("#nowIndex").val();
	if(zindex==0){
		layer.msg("默认仓库不能删除!",function(){});
		return false;
	}
	layer.confirm('确定要删除该仓库吗？', {
	  btn: ['确定','取消'],
	}, function(){
		var storeId = getPdtId();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=store&a=delete",
			data: "&id="+storeId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==1){
					reloadTable(1);	
				}else{
					layer.msg(resdata.message);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});	
}
//编辑产品
function edit_store(params){
	$("#cangkugl_xiugai").show();
	if (map == null){
		map = new BMap.Map("container");
		var top_left_control = new BMap.ScaleControl({anchor: BMAP_ANCHOR_TOP_LEFT});
		var top_left_navigation = new BMap.NavigationControl();      
		map.addControl(top_left_control);
		map.addControl(top_left_navigation);
		map.enableScrollWheelZoom();
		map.enableContinuousZoom();
		map.addEventListener("click",function(e){
			document.getElementById('TextBox1').value = e.point.lng;
			document.getElementById('TextBox2').value = e.point.lat;
			map.clearOverlays();
			var point = new BMap.Point(e.point.lng,e.point.lat);
			var marker = new BMap.Marker(point);
			map.addOverlay(marker);
		});
		map.clearOverlays();
	}
	var storeId = 0;
	var areaId = 0;
	var title = '';
	var sn = '';
	var name = '';
	var phone = '';
	var address = '';
	var hengzuobiao = '';
	var zongzuobiao = '';
	if(params==1){
		$("#cangkugl_xiugai .cangkugl_xiugai_01").html('修改仓库');
		var zindex = $("#nowIndex").val();
		storeId = getPdtId();
		var nowTr = $(".layui-table-main tr[data-index='"+zindex+"']").eq(0);
		title = nowTr.find("td[data-field='title'] div").eq(0).html();
		sn = nowTr.find("td[data-field='sn'] div").eq(0).html();
		address = nowTr.find("td[data-field='address'] div").eq(0).html();
		position = nowTr.find("td[data-field='position'] div").eq(0).html();
		areaId = parseInt(nowTr.find("td[data-field='areaId'] div").eq(0).html());
		name = nowTr.find("td[data-field='name'] div").eq(0).html();
		phone = nowTr.find("td[data-field='phone'] div").eq(0).html();
		if(position.length>8){
			var zuobiao = position.split('|');
			hengzuobiao = zuobiao[0];
			zongzuobiao = zuobiao[1];
			map.centerAndZoom(new BMap.Point(zuobiao[0],zuobiao[1]), 18);
			var point1 = new BMap.Point(zuobiao[0],zuobiao[1]);
			var marker1 = new BMap.Marker(point1);
			map.addOverlay(marker1);
		}else{
			map.centerAndZoom("北京市",12);
		}
	}else{
		$("#cangkugl_xiugai .cangkugl_xiugai_01").html('新增仓库');
		map.centerAndZoom("北京市",12);
	}
	if(areaId>0){
		layer.load();
		$.ajax({
			type: "POST",
			url: "?m=system&s=dinghuo&a=getAreaInfo",
			data: "&id="+areaId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll();
				$("#ps1").html(resdata.areas1);
				$("#ps2").html(resdata.areas2);
				$("#ps3").html(resdata.areas3);
				productListForm.render('select');
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}else{
		$("#ps1 option[value='']").remove();
		$("#ps1").prepend('<option value="" selected="true">请选择</option>');
		$("#ps2").html('<option value="">请先选择省</option>');
		$("#ps3").html('<option value="">请先选择市</option>');
		productListForm.render('select');
	}
	$("#storeId").val(storeId);
	$("#e_title").val(title);
	$("#e_sn").val(sn);
	$("#e_address").val(address);
	$("#TextBox1").val(hengzuobiao);
	$("#TextBox2").val(zongzuobiao);
	$("#psarea").val(areaId);
	$("#e_name").val(name);
	$("#e_phone").val(phone);
	$("#bg").show();

}
function jin_store(params){
	var zindex = $("#nowIndex").val();
	if(zindex==0){
		layer.msg("默认仓库不能禁用!",function(){});
		return false;
	}
	layer.confirm('确定要禁用该仓库吗？', {
	  btn: ['确定','取消'],
	}, function(){
		var storeId = getPdtId();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=store&a=jinyong",
			data: "&id="+storeId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==1){
					reloadTable(1);	
				}else{
					layer.msg(resdata.message);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function qiyong_store(params){
	var zindex = $("#nowIndex").val();
	layer.confirm('确定要启用该仓库吗？', {
	  btn: ['确定','取消'],
	}, function(){
		var storeId = getPdtId();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=store&a=qiyong",
			data: "&id="+storeId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll();
				if(resdata.code==1){
					reloadTable(1);	
				}else{
					layer.msg(resdata.message);
				}
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var page = 1;
	if(curpage==1){
		page = $("#page").val();
	}
	productListTalbe.reload({
		where: {
			
		},page: {
			curr: page
		}
	});
	$("th[data-field='id']").hide();
	$("th[data-field='position']").hide();
	$("th[data-field='areaId']").hide();
	$("th[data-field='name']").hide();
    $("th[data-field='phone']").hide();
}
function quxiao(){
	$("#bg").hide();
	$("#cangkugl_xiugai").hide();
}
function edit_kuaidi(){
	location.href='?m=system&s=store&a=kuaidi&storeId='+getPdtId();
}