var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
});
$(function(){
	$(".cp_lijigoumai_3 a").click(function(){
		if($(this).attr("class")=='bendixiangqing_3_02_on'){
			return false;
		}else{
			var img = $(this).attr("data-img");
			var keyId = $(this).attr("data-key");
			if(img!=''){
				$("#fx_lijigoumai_1_img").attr('src',img);
			}
			$("#key-"+keyId).find("a").removeClass("bendixiangqing_3_02_on");
			$("#key-"+keyId).find("img").hide();
			$(this).addClass("bendixiangqing_3_02_on").next().show();
		}
		getSnInfo();
	});
});
/*function init_pdt_info(){
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=init_pdt_info",
		data: "inventoryId="+inventoryId+"&productId="+productId,
		dataType:"json",timeout : 8000,
		success: function(res){
			$("#comment_num").text(res.comment_num);
			if(res.gwc_num>0){
				$("#gwc_num").text(res.gwc_num).show();
			}
			if(res.comment_list.length>0){
				str = '';
				$.each(res.comment_list, function(index, item){
					str += '<li>'+
		                	'<div class="chanpin_6_02_1">'+
		                    	'<div class="chanpin_6_02_1_left">'+
		                        	'<img src="'+item.touxiang+'" /> '+item.username+
		                        '</div>'+
		                    	'<div class="chanpin_6_02_1_right">'+
		                        	item.xing+
		                        '</div>'+
		                    	'<div class="clearBoth"></div>'+
		                    '</div>'+
		                	'<div class="chanpin_6_02_2">	'+
		                    	item.content+
		                    '</div>'+
		                	'<div class="chanpin_6_02_3">'+
		                    	item.imgs+
		                    '</div>'+(item.key_vals=='无'?'':'<div class="chanpin_6_02_4">'+item.key_vals+'</div>')+
		                '</li>';
				});
				str+='<div class="clearBoth"></div>';
				$("#comment_list").html(str);
				baguetteBox.run('.pingjia');
			}else{
				$("#comment_list").html('<li><div style="text-align:center;padding:.5rem 0rem">暂无评价！</div></li>').parent().next().hide();
			}
			if(res.tuijian_list.length>0){
				str = '';
				$.each(res.tuijian_list, function(index, item){
					if(item.image!=''){
						imgs = item.image.split('|');
						item.image = imgs[0];
					}
					str += '<li class="chanpin_7_02_rightline chanpin_7_02_bottomline">'+
	                	'<a href="/index.php?p=4&a=view&id='+item.inventoryId+'">'+
	                    	'<div class="chanpin_7_02_img">'+
	                        	'<img src="'+(item.image==''?'/inc/img/nopic.svg':item.image)+'"/>'+
	                        '</div>'+
	                    	'<div class="chanpin_7_02_tt">'+
	                        	item.title+
	                        '</div>'+
	                    	'<div class="chanpin_7_02_price">'+
	                        	'￥<span>'+(Math.floor(item.price_sale*100)/100)+'</span>'+
	                        '</div>'+
	                    '</a>'+
	                '</li>';
	                if((index+1)%3==0){
	                	str+='<div class="clearBoth"></div>';
	                }
				});
				str+='<div class="clearBoth"></div>';
				$("#tuijian_list").html(str);
			}
			if(res.yhq_list.length>0){
				str = '';str1 = '';
				$.each(res.yhq_list, function(index, item){
					if(index<3){
						str += '<i>满'+item.man+'减'+item.money+'</i>';
					}
					str1 += '<li>'+
                      '<div class="zsyhq_3_left" style="background-color:'+item.color+';">'+
                          '<h2>￥<b>'+item.money+'</b></h2>满'+item.man+'元可用'+
                        '</div>'+
                        '<div class="zslingquanzhongxin_right">'+
                          '<h2>'+item.tiaojian+'</h2>'+
                            item.startTime+'-'+item.endTime+
                        '</div>';
                        if(item.if_lingqu==1){
                          str1 = str1+'<div class="zslingquanzhongxin_biao_1">'+
                            '<img src="/skins/default/images/a928_17.png"/>'+
                          '</div>';
                          if(item.if_ke_lingqu==1){
                            str1 = str1+'<div class="zslingquanzhongxin_biao_2" onclick="lingqu('+item.id+');">'+
                              '<img src="/skins/default/images/a928_18.png"/>'+
                            '</div>';
                          }
                        }else{
                          str1 = str1+'<div class="zslingquanzhongxin_biao_2" onclick="lingqu('+item.id+');">'+
                            '<img src="/skins/default/images/a928_18.png"/>'+
                          '</div>';
                        }
                        str1 = str1+'<div class="clearBoth"></div>'+
                    '</li>';
				});
				$("#pdt_yhq_list").html('领券 '+str);
				$(".zslingquanzhongxin ul").html(str1);
			}else{
				$("#pdt_yhq").hide();
			}
		},
		error: function() {
			layer.closeAll();
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}*/
function num_edit(n){
	var num = parseInt($('#num').val());
	num = num+n;
	if(num<1)num=1;
	if(num>max_num){
		num = max_num;
		var tishi = '最多可买'+max_num+'份';
		if(max_num==0){
			tishi = '该商品已售罄或超出限购数量';
		}
		layer.open({content:tishi,skin: 'msg',time: 2});
	}
	$('#num').val(num);
}
//0元购、参团、下单
function buy(addType){
	if(buy_limit==1){
		layer.open({content:'该规格已下架，不能购买',skin: 'msg',time: 2});
		return false;
	}else if(buy_limit==2){
		layer.open({content:'产品库存不足，不能购买',skin: 'msg',time: 2});
		return false;
	}
	var num =parseInt($("#num").val());
	if(num<=0){
		layer.open({content:'数量不能小于1',skin: 'msg',time: 2});
		return false;
	}
	if(num>max_num){
		var tishi = '最多可买'+max_num+'份';
		if(max_num==0){
			tishi = '该商品已售罄~~';
		}
		layer.open({content:tishi,skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=22&a=add_gouwuche&addType="+addType,
		data: "productId="+productId+"&inventoryId="+inventoryId+"&num="+num,
		dataType:"json",timeout : 8000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.open({content:resdata.message,skin:'msg',time:2});
				if(resdata.message=='请先登录'){
					var backurl = window.location.href;
  					backurl = encodeURIComponent(backurl);
					setTimeout(function(){
						location.href='/index.php?p=8&a=login&url='+backurl;
					},1800);
				}
				return false;
			}
			location.href='/index.php?p=22&a=queren';
		},
		error: function() {
			layer.closeAll('loading');
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}
//获取选中的规格的信息
function getSnInfo(){
	var sn = '';
	var sn_title = '';
	$(".bendixiangqing_3 a.bendixiangqing_3_02_on").each(function(){
		if(sn==''){
			sn = $(this).attr("data-id");
			sn_title = $(this).text();
		}else{
			sn = sn+'-'+$(this).attr("data-id");
			sn_title +=','+$(this).text();
		}
	});
	//$("#sn_title").text(sn_title);
	layer.open({type:2});
	console.log(sn);
	$.ajax({
		type: "POST",
		url: "/index.php?p=22&a=get_pdtsn_info",
		data: "productId="+productId+"&key_ids="+sn+"&buy_type="+buy_type,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			inventoryId = resdata.inventoryId;
			if(inventoryId==0){
				layer.open({content:'该规格已下架',skin: 'msg',time: 2});
				buy_limit = 1;
			}else{
				layer.closeAll();
				fanli_money = resdata.fanli;
				$("#price_sale").html('￥'+resdata.price);
				$(".bendixiangqing_2_02").html('¥'+resdata.price+' <span>门市价￥'+resdata.price_market+'</span>');
				$("#price_market1").html('￥'+resdata.price_market);
				$("#pdt_sn").text(resdata.sn);
				$("#pdt_kucun").text(resdata.kucun);
				if(resdata.kucun<=0){
					buy_limit = 2;
				}else if(resdata.price<=0){
					buy_limit = 1;
				}else{
					buy_limit = 0;
				}
			}
		},
		error: function() {
			layer.closeAll('loading');
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}