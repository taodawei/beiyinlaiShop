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
//选择上下架状态
function selectStatus(status,title){
	$("#storeIds").val(status);
	$(".splist_up_01_left_02_up span").html(title);
	reloadTable(0);
}
//隐藏搜索框
function hideTanchu(className){
	$("."+className+"_up").removeClass("openIcon");
	$("."+className+"_down").slideUp(200);
}
//显示右侧点击。。。的弹窗
function showNext(dom){
	var top = $(dom).offset().top;
	if(top+129>document.body.clientHeight){
		top=top-50;
	}
	var width = parseInt($(dom).css("width"));
	var right = (width/2)+35;
	var nowIndex = $("#nowIndex").val();
	var index = $(dom).parent().parent().parent().attr("data-index");
	$("#operate_row").css({"top":(top-90)+"px","right":right+"px"});
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
//编辑产品
function edit_kucun(){
	var zindex = $("#nowIndex").val();
	var nowTr = $(".layui-table-main tr[data-index='"+zindex+"']");
	var shangxian = nowTr.find("td[data-field='shangxian'] div").html();
	var reg=/,/g;
	shangxian = shangxian.replace('<span style="color:red">','');
	shangxian = shangxian.replace('</span>','');
	shangxian = shangxian.replace(reg,'');
	var xiaxian = nowTr.find("td[data-field='xiaxian'] div").html();
	xiaxian = xiaxian.replace('<span style="color:red">','');
	xiaxian = xiaxian.replace('</span>','');
	xiaxian = xiaxian.replace(reg,'');
	layer.open({
		type: 1
		,title: false
		,closeBtn: false
		,area: '530px;'
		,shade: 0.3
		,id: 'LAY_layuipro'
		,btn: ['提交', '取消']
		,yes: function(index, layero){
			return false;
		}
		,btnAlign: 'r'
		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
		'<div class="spxx_shanchu_tanchu_01">'+
		'<div class="spxx_shanchu_tanchu_01_left">'+'商品库存修改'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_01_right">'+
		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
		'</div>'+
		'<div class="clearBoth"></div>'+
		'</div>'+
		'<div class="spxx_shanchu_tanchu_02" style="height:330px;padding:0px;">'+
		'<div class="yuandian_xx_kucunguanli_xiugai_tt_02">'+
        	'<ul>'+
        		'<li>'+
                	'<div class="yuandian_xx_kucunguanli_xiugai_tt_02_left">'+
                    	'库存上限'+
                    '</div>'+
                	'<div class="yuandian_xx_kucunguanli_xiugai_tt_02_right">'+
                    	'<input id="e_shangxian" value="'+shangxian+'" min="0" type="number" step="'+step+'">'+
                    '</div>'+
                	'<div class="clearBoth"></div>'+
                '</li>'+
                '<li>'+
                	'<div class="yuandian_xx_kucunguanli_xiugai_tt_02_left">'+
                    	'库存下限'+
                    '</div>'+
                	'<div class="yuandian_xx_kucunguanli_xiugai_tt_02_right">'+
                    	'<input id="e_xiaxian" value="'+xiaxian+'" min="0" type="number" step="'+step+'">'+
                    '</div>'+
                	'<div class="clearBoth"></div>'+
                '</li>'+
        	'</ul>'+
        '</div>'+
        '<div class="yuandian_xx_kucunguanli_xiugai_tt_03" style="display:none;">'+
        	'<ul>'+
        		'<li>'+
                	'<input type="radio" name="e_yingyong" checked="checked" value="0" title="只应用于此仓库下的此商品">'+
               	'</li>'+
        		'<li>'+
                	'<input type="radio" name="e_yingyong" value="1" title="应用于所有仓库下的此商品">'+
               	'</li>'+
               	'<li>'+
                	'<input type="radio" name="e_yingyong" value="2" title="应用于此仓库下此商品的其他规格">'+
               	'</li>'+
               	'<li>'+
                	'<input type="radio" name="e_yingyong" value="3" title="应用于所有仓库下此商品的其他规格">'+
               	'</li>'+
        	'</ul>'+
        '</div>'+
		'</div>'+
		'</div>'
		,success: function(layero){
			productListForm.render();
			var btn = layero.find('.layui-layer-btn');
			btn.find('.layui-layer-btn0').attr({
				href: 'javascript:checkEditForm();'
			});
			return false;
		}
	});
}
function checkEditForm(){
	var zindex = $("#nowIndex").val();
	var nowTr = $(".layui-table-main tr[data-index='"+zindex+"']");
	var inventoryId = nowTr.find("td[data-field='id'] div").html();
	var storeId = nowTr.find("td[data-field='storeId'] div").html();
	var shangxian = parseFloat($("#e_shangxian").val());
	var xiaxian = parseFloat($("#e_xiaxian").val());
	if(shangxian<xiaxian){
		layer.msg("上限不能小于下限！"+shangxian+" "+xiaxian,function(){});
		return false;
	}
	layer.load();
	var yingyong = $("input[name='e_yingyong']:checked").val();
	ajaxpost=$.ajax({
		type: "POST",
		url: "?m=system&s=kucun&a=edit",
		data: "&inventoryId="+inventoryId+"&storeId="+storeId+"&shangxian="+shangxian+"&xiaxian="+xiaxian+"&yingyong="+yingyong,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			layer.msg('操作成功');
			reloadTable(1);
		},
		error: function() {
			layer.closeAll();
			layer.msg('数据请求失败，请重试', {icon: 5});
		}
	});

}
function detail_kucun(){
	var zindex = $("#nowIndex").val();
	var nowTr = $(".layui-table-main tr[data-index='"+zindex+"']");
	var inventoryId = nowTr.find("td[data-field='id'] div").html();
	var storeId = nowTr.find("td[data-field='storeId'] div").html();
	var a = 'index';
	if(typeof(storeId)=='undefined'){
		storeId = 0;
		a = 'index1';
	}
	var channelId = $("#channelId").val();
	var storeIds = $("#storeIds").val();
	var brandId = $("#brandId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var kczt = $("#kczt").val();
	var page = $("#page").val();
	var order1 = $("#order1").val();
	var order2 = $("#order2").val();
	var url = '?m=system&s=kucun&a='+a+'&channelId='+channelId+"&storeIds="+storeIds+"&brandId="+brandId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao+"&kczt="+kczt+"&page="+page+"&order1="+order1+"&order2="+order2;
	url = encodeURIComponent(url);
	location.href="?m=system&s=kucun&a=jilus&inventoryId="+inventoryId+"&storeIds="+storeId+"&url="+url;
}
//获取当前选中的产品Id
function getPdtId(){
	var zindex = $("#nowIndex").val();
	return $(".layui-table-main tr[data-index='"+zindex+"'] td[data-field='id'] div").html();
}
function reloadTable(curpage){
	layer.load();
	var channelId = $("#channelId").val();
	var storeIds = $("#storeIds").val();
	var brandId = $("#brandId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var kczt = $("#kczt").val();
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
			,brandId:brandId
			,storeIds:storeIds
			,status:status
			,keyword:keyword
			,tags:tags
			,kczt:kczt
			,source:source
			,cuxiao:cuxiao
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
	$("th[data-field='storeId']").hide();
}
//自定义字段相关
function showRowset(){
	$("#bg").show();
	$("#xianshiziduan").show().animate({"right":"0px"},300);
}
function hideRowset(){
	$("#xianshiziduan").animate({"right":"-259px","display":"none"},300);
	$("#bg").hide();
}
//导出导入操作
function daochu(){
	var channelId = $("#channelId").val();
	var status = $("#s_status").val();
	var keyword = $("#keyword").val();
	var tags = $("#tags").val();
	var source = $("#source").val();
	var cuxiao = $("#cuxiao").val();
	var href = $("#daochuA").attr("href");
	$("#daochuA").attr("href",href+"&channelId="+channelId+"&status="+status+"&keyword="+keyword+"&tags="+tags+"&source="+source+"&cuxiao="+cuxiao);
}