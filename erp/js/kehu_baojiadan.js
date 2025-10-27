var nowIndexTime;
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	$("#super_channel").val($(dom).attr("lay-value"));
	$("#selectChannel").find('input').val($(dom).text());
}
$(document).ready(function(){
	$(document).bind('click',function(){ 
		hideTanchu("splist_up_01_left_01");
		hideTanchu("splist_up_01_left_02");
	});
	//分类
	$('.splist_up_01_left_01_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_01_down').slideToggle(200);
		stopPropagation(eve); 
	});
	//上下架状态
	$('.splist_up_01_left_02_up').click(function(eve){
		$(this).toggleClass('openIcon');
		$('.splist_up_01_left_02_down').slideToggle(200);
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
//批量操作
function shangjia(){
	var ids = $("#selectedIds").val();
	var num = $("#selectedNum").html();
	layer.confirm('确定要上架选中的'+num+'个产品吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=product&a=shangjia",
			data: "&ids="+ids,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll('loading');
				$("#selectedNum").html('0');
				$("#selectedIds").val('');
				$(".splist_up_01").show();
				$(".splist_up_02").hide();
				layer.msg('操作成功');
				reloadTable(1);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function xiajia(){
	var ids = $("#selectedIds").val();
	var num = $("#selectedNum").html();
	layer.confirm('确定要下架选中的'+num+'个产品吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=product&a=xiajia",
			data: "&ids="+ids,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll('loading');
				layer.msg('操作成功');
				reloadTable(1);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function delAll(){
	var ids = $("#selectedIds").val();
	var num = $("#selectedNum").html();
	layer.confirm('确定要删除选中的'+num+'个产品吗？', {
	  btn: ['确定','取消'],
	},function(){
		layer.closeAll();
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=product&a=delete",
			data: "&ids="+ids,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				layer.closeAll('loading');
				layer.msg('操作成功');
				reloadTable(1);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
		return true;
	});
}
function setAllTags(){
	var ids = $("#selectedIds").val();
	var tagstr = '';
	$("input:checkbox[name='piliang_tags']:checked").each(function(){
		tagstr = tagstr+','+$(this).val();
	});
	if(tagstr.length>0){
		tagstr = tagstr.substring(1);
	}
	layer.closeAll();
	layer.load();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=product&a=setTags",
		data: "&ids="+ids+"&tags="+tagstr,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll();
			layer.msg('操作成功');
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败', {icon: 5});
		}
	});
	return true;
}
//自定义字段点击上下箭头调整
function rowToUp(row){
	var nowli = $(row).parent();
	nowli.prev().before(nowli);
	if(nowli.index()==3){
		nowli.find(".rowtoup").remove();
		nowli.next().append('<span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>');
	}
}
function rowToDown(row){
	var nowli = $(row).parent();
	nowli.next().after(nowli);
	if(nowli.index()==4){
		nowli.append('<span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>');
		nowli.prev().find(".rowtoup").remove();
	}
}
//隐藏高级搜搜
function hideSearch(){
	$('.splist_up_01_right_2_down').css({'top':'-10px','opacity':'0','visibility':'hidden'});
}
//加载子分类
function loadZiChannels(menuId,ceng,hasnext){
	var channelDiv = $(".splist_up_01_left_01_down");
	if($("#ziChannels"+ceng).length==0&&hasnext==1){
		var ulstr = '<ul id="ziChannels'+ceng+'"><div style="text-align:center;"><img src="images/loading.gif"></div></ul>';
		var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
		channelDiv.css("width",(nowWidth+200)+"px");
		channelDiv.append(ulstr);
	}else{
		if(ceng<4&&$("#ziChannels4").length>0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels4").remove();
		}
		if(ceng<3&&$("#ziChannels3").length>0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels3").remove();
		}
		if($("#ziChannels"+ceng).length>0&&hasnext==0){
			var nowWidth = parseInt(channelDiv.css("width").toString().replace('px',''));
			channelDiv.css("width",(nowWidth-200)+"px");
			$("#ziChannels"+ceng).remove();
		}else{
			$("#ziChannels"+ceng).html('<div style="text-align:center;"><img src="images/loading.gif"></div>');
		}
	}
	if(hasnext==1){
		ajaxpost=$.ajax({
			type: "POST",
			url: "/erp_service.php?action=get_zi_channels",
			data: "&id="+menuId,
			dataType:"json",timeout : 8000,
			success: function(resdata){
				var listr = '';
				for(var i=0;i<resdata.items.length;i++){
					if(ceng<4){
						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectChannel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" onmouseenter="loadZiChannels('+resdata.items[i].id+','+(ceng+1)+','+resdata.items[i].hasNext+');" class="allsort_01_tlte">'+resdata.items[i].title+(resdata.items[i].hasNext==1?' <span><img src="images/biao_24.png"></span>':'')+' </a></li>';
					}else{
						listr=listr+'<li class="allsort_01"><a href="javascript:" onclick="selectChannel('+resdata.items[i].id+',\''+resdata.items[i].title+'\');" class="allsort_01_tlte">'+resdata.items[i].title+'</a></li>';
					}
				}
				$("#ziChannels"+ceng).html(listr);
			},
			error: function() {
				layer.closeAll();
				layer.msg('数据请求失败', {icon: 5});
			}
		});
	}
}
//选择分类
function selectChannel(channelId,title){
	$("#channelId").val(channelId);
	$(".splist_up_01_left_01_up span").html(title);
	reloadTable(0);
}
//隐藏搜索框
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	var channelId = $("#channelId").val();
	var keyword = $("#keyword").val();
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
			,channelId:channelId
			,keyword:keyword
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
//导出导入操作
function daochu(){
	var channelId = $("#channelId").val();
	var keyword = $("#keyword").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&channelId="+channelId+"&keyword="+keyword);
}