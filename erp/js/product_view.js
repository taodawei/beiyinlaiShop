$(document).ready(function(){
	if(ifshowImg==1){
		var scrollPic_02 = new ScrollPic();
		scrollPic_02.scrollContId   = "ISL_Cont_1";
		scrollPic_02.arrLeftId      = "LeftArr";
		scrollPic_02.arrRightId     = "RightArr";

		scrollPic_02.frameWidth     = 354;
		scrollPic_02.pageWidth      = 69;

		scrollPic_02.speed          = 10;
		scrollPic_02.space          = 10;
		scrollPic_02.autoPlay       = false;
		scrollPic_02.autoPlayTime   = 3; 
		scrollPic_02.initialize();
	}
	renderSns();
	$(".spxiangqing_02_img_big").jqueryzoom({
		xzoom: 300,
		yzoom: 300,
		offset: 10,
		position: "right",
		preload:1,
		lens:1
	});
	$(".chima a").click(function(){
		if($(this).attr("class")=="disabled"){
			return false;
		}else if($(this).attr("class")=='on'){
			$(this).removeClass("on");
			$("#inventoryId").val(0);
		}else{
			var img = $(this).attr("data-img");
			var keyId = $(this).attr("data-key");
			if(img!=''){
				showImg(img);
			}
			$("#key-"+keyId).find("a").removeClass("on");
			$(this).addClass("on");
		}
		renderSns();
		getSnInfo();
		$("#d_p_table1").html('<tr class="ant-table-row  ant-table-row-level-0"><td width="20%" align="center"><div class="load"><img src="images/loading.gif"></div></td></tr>');
		$("#d_p_table2").html('<tr class="ant-table-row  ant-table-row-level-0"><td width="20%" align="center"><div class="load"><img src="images/loading.gif"></div></td></tr>');
	});
	$(".spxiangqing_02_tt_02_right a").click(function(eve){
		$(this).toggleClass('openIcon');
		$("#show_dinghuo_price").slideToggle(200);
		stopPropagation(eve);
		loadPrice(1);
	});
	$("#show_dinghuo_price").click(function(eve){
		stopPropagation(eve);
	});
	$(document).click(function(){
		$(".spxiangqing_02_tt_02_right a").removeClass('openIcon');
		$("#show_dinghuo_price").slideUp(200);
	});
});
function loadPrice(type){
	var inventoryId = $("#inventoryId").val();
	if($("#d_p_table"+type).find(".load").length>0){
		$.ajax({
			type: "POST",
			url: "?m=system&s=product&a=get_dinghuo_price",
			data: "type="+type+"&inventoryId="+inventoryId,
			dataType:"json",timeout : 10000,
			success: function(resdata){
				var trstr = '';
				$.each(resdata.data,function(key,val){
					trstr = trstr+'<tr class="ant-table-row ant-table-row-level-0"><td width="20%"><span class="ant-table-row-indent indent-level-0" style="padding-left: 0px;"></span>'+val.name+'</td><td width="20%">'+val.if_dinghuo+'</td><td width="20%">'+val.price+'</td><td width="20%" class="dinghuo_if_min">'+val.min+'</td><td width="20%" class="dinghuo_if_max">'+val.max+'</td></tr>';
				});
				$("#d_p_table"+type).html(trstr);
			},
			error: function() {
				layer.closeAll('loading');
				layer.msg('数据请求失败，请刷新页面重试', {icon: 5});
			}
		});
	}
}
function showImg(url){
	$(".spxiangqing_02_img_big img").eq(0).attr({"src":url,"jqimg":url});
}
//重新渲染所有规格，不能选的设置为disabled
function renderSns(){
	var allSns = inventory_keys.split(',');
	if(rows==1){
		$(".chima a").each(function(){
			key = $(this).attr('data-id');
			if($.inArray(key,allSns)==-1){
				$(this).attr("class","disabled");
			}
		});
	}else if(rows==2){
		row1select = $(".spxiangqing_02_tt_04 li[row='1'] a.on").eq(0);
		row2select = $(".spxiangqing_02_tt_04 li[row='2'] a.on").eq(0);
		$(".chima a.disabled").removeClass("disabled");
		if(row1select.length>0){
			key1 = row1select.attr('data-id');
			$(".spxiangqing_02_tt_04 li[row='2'] a").each(function(){
				key2 = $(this).attr('data-id');
				if($.inArray(key1+'-'+key2,allSns)==-1){
					$(this).attr("class","disabled");
				}
			});
		}
		if(row2select.length>0){
			key2 = row2select.attr('data-id');
			$(".spxiangqing_02_tt_04 li[row='1'] a").each(function(){
				key1 = $(this).attr('data-id');
				if($.inArray(key1+'-'+key2,allSns)==-1){
					$(this).attr("class","disabled");
				}
			});
		}
	}else if(rows==3){
		row1select = $(".spxiangqing_02_tt_04 li[row='1'] a.on").eq(0);
		row2select = $(".spxiangqing_02_tt_04 li[row='2'] a.on").eq(0);
		row3select = $(".spxiangqing_02_tt_04 li[row='3'] a.on").eq(0);
		$(".chima a.disabled").removeClass("disabled");
		if(row1select.length>0&&row2select.length>0){
			key1 = row1select.attr('data-id');
			key2 = row2select.attr('data-id');
			$(".spxiangqing_02_tt_04 li[row='3'] a").each(function(){
				key3 = $(this).attr('data-id');
				if($.inArray(key1+'-'+key2+'-'+key3,allSns)==-1){
					$(this).attr("class","disabled");
				}
			});
		}
		if(row1select.length>0&&row3select.length>0){
			key1 = row1select.attr('data-id');
			key3 = row3select.attr('data-id');
			$(".spxiangqing_02_tt_04 li[row='2'] a").each(function(){
				key2 = $(this).attr('data-id');
				if($.inArray(key1+'-'+key2+'-'+key3,allSns)==-1){
					$(this).attr("class","disabled");
				}
			});
		}
		if(row2select.length>0&&row3select.length>0){
			key2 = row2select.attr('data-id');
			key3 = row3select.attr('data-id');
			$(".spxiangqing_02_tt_04 li[row='1'] a").each(function(){
				key1 = $(this).attr('data-id');
				if($.inArray(key1+'-'+key2+'-'+key3,allSns)==-1){
					$(this).attr("class","disabled");
				}
			});
		}
		//只选择了其中一个规格
		if(row1select.length+row2select.length+row3select.length==1){
			if(row1select.length==1){
				key1 = row1select.attr('data-id');
				$(".spxiangqing_02_tt_04 li[row='2'] a").each(function(){
					var row2li = $(this);
					key2 = row2li.attr('data-id');
					row2li.attr("class","disabled");
					$(".spxiangqing_02_tt_04 li[row='3'] a").each(function(){
						key3 = $(this).attr('data-id');
						$(this).attr("class","disabled");
						if($.inArray(key1+'-'+key2+'-'+key3,allSns)>-1){
							$(this).removeClass("disabled");
							row2li.removeClass("disabled");
						}
					});
				});
			}else if(row2select.length==1){
				key2 = row2select.attr('data-id');
				$(".spxiangqing_02_tt_04 li[row='1'] a").each(function(){
					var row2li = $(this);
					key1 = row2li.attr('data-id');
					row2li.attr("class","disabled");
					$(".spxiangqing_02_tt_04 li[row='3'] a").each(function(){
						key3 = $(this).attr('data-id');
						$(this).attr("class","disabled");
						if($.inArray(key1+'-'+key2+'-'+key3,allSns)>-1){
							$(this).removeClass("disabled");
							row2li.removeClass("disabled");
						}
					});
				});
			}else if(row3select.length==1){
				key3 = row3select.attr('data-id');
				$(".spxiangqing_02_tt_04 li[row='1'] a").each(function(){
					var row2li = $(this);
					key1 = row2li.attr('data-id');
					row2li.attr("class","disabled");
					$(".spxiangqing_02_tt_04 li[row='2'] a").each(function(){
						key2 = $(this).attr('data-id');
						$(this).attr("class","disabled");
						if($.inArray(key1+'-'+key2+'-'+key3,allSns)>-1){
							$(this).removeClass("disabled");
							row2li.removeClass("disabled");
						}
					});
				});
			}
		}
	}
}
//获取选中的规格的信息
function getSnInfo(){
	layer.load();
	var sn = '';
	$(".chima a.on").each(function(){
		if(sn==''){
			sn = $(this).attr("data-id");
		}else{
			sn = sn+'-'+$(this).attr("data-id");
		}
	});
	$.ajax({
		type: "POST",
		url: "/erp_service.php?action=get_pdtsn_info",
		data: "productId="+productId+"&key_ids="+sn,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			//console.log(resdata);
			layer.closeAll('loading');
			$("#inventoryId").val(resdata.inventoryId);
			if(resdata.inventoryId>0){
				$("#sn").html(resdata.pdt_info.sn);
				$("#code").html(resdata.pdt_info.code);
				$("#price_market").html(resdata.pdt_info.price_market);
				$("#price_sale").html(resdata.pdt_info.price_sale);
				$("#weight").html(resdata.pdt_info.weight);
				$("#kucun").html(resdata.pdt_info.kucun);
				if(resdata.pdt_info.cont1!=null&&resdata.pdt_info.cont1!=''){
					$("#pdtcontCont1").html(resdata.pdt_info.cont1);
				}
				if(resdata.pdt_info.cont2!=null&&resdata.pdt_info.cont2!=''){
					$("#pdtcontCont2").html(resdata.pdt_info.cont2);
				}
				if(resdata.pdt_info.cont3!=null&&resdata.pdt_info.cont3!=''){
					$("#pdtcontCont3").html(resdata.pdt_info.cont3);
				}
			}
		},
		error: function() {
			layer.closeAll('loading');
			layer.msg('数据请求失败，请刷新页面重试', {icon: 5});
		}
	});
}
function editPdt(){
	var id = $("#inventoryId").val();
	if(id==0){
		layer.msg('请先选择要修改的规格',function(){});
	}else{
		var url =encodeURIComponent('?m=system&s=product&a=view&id='+id);
		location.href='?m=system&s=product&a=edit&id='+id+"&url="+url;
	}
}
function del_pdt(){
	var id = $("#inventoryId").val();
	if(id==0){
		layer.msg('请先选择要删除的规格',function(){});
	}else{
		layer.open({
			type: 1
			,title: '确定要删除该产品吗？'
			,shade: 0.3
			,area: '390px;'
			,id: 'LAY_layuipro1'
			,btn: ['删除该规格','删除所有规格','取消']
			,closeBtn: false
			,yes: function(){
				location.href='?m=system&s=product&a=delPdt&isall=0&id='+id+'&url='+url;
			},btn2: function(){
				location.href='?m=system&s=product&a=delPdt&isall=1&id='+id+'&url='+url;
			}
			,btnAlign: 'r'
			,content:'<div style="margin:12px 15px;text-align:center;width:360px;">确定要删除该产品吗？删除后产品数据无法恢复！</div>'
		});		
	}
}