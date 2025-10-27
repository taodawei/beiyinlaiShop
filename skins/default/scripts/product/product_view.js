var lay_flow;
layui.use('flow', function(){
	lay_flow = layui.flow;
});
$(function(){
	/*$('.flexslider').flexslider({
		animation: "slide",
		directionNav:false
	});*/
	//切换标签
	$(".cp_lijigoumai_3 a").click(function(){
		if($(this).attr("class")=='cp_lijigoumai_3_down_on'){
			return false;
		}else{
			var img = $(this).attr("data-img");
			var keyId = $(this).attr("data-key");
			if(img!=''){
				$("#fx_lijigoumai_1_img").attr('src',img);
			}
			$("#key-"+keyId).find("a").removeClass("cp_lijigoumai_3_down_on");
			$(this).addClass("cp_lijigoumai_3_down_on");
		}
		getSnInfo();
	});
	$("#search_addr").bind('input propertychange', function() {
		var keyword = $(this).val();
		$("#shouhuodizhi_queren_tc .shouhuodizhi_queren_1").each(function(){
			if($(this).html().indexOf(keyword)>-1){
				$(this).show();
			}else{
				$(this).hide();
			}
		});
	});
	init_pdt_info();
});
function init_pdt_info(){
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
}
function show_imgs(){
	$(".shangpinjieshao img").each(function(){
		if(typeof($(this).attr("src")) == 'undefined' || $(this).attr("src")=="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsQAAA7EAZUrDhsAAAANSURBVBhXYzh8+PB/AAffA0nNPuCLAAAAAElFTkSuQmCC"){
			$(this).attr("src",$(this).attr("data-original"));
		}
	});
}
function init_huadong(now){
	show_detail = 0;
	nwo_page = now;
	$("html,body").animate({scrollTop:0},1);
}
//拼团购、分享购切换
function qiehuan_price(){
	if(show_price==0){
		show_price = 1;
		$(".pintuanshangpinye_3_left_01").hide().next().show();
		$(".pintuanshangpinye_7_right").hide().next().show();
		$(".pintuanshangpinye_4_left").text('拼团购买');
	}else{
		show_price = 0;
		$(".pintuanshangpinye_3_left_01").show().next().hide();
		$(".pintuanshangpinye_7_right").show().next().hide();
		$(".pintuanshangpinye_4_left").text('分享购买');
	}
}
//点击收藏按钮
function shoucang(inventoryId){
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=shoucang",
		data: "inventoryId="+inventoryId+"&ifshoucang="+ifshoucang,
		dataType:"json",timeout : 20000,
		success: function(res){
			layer.closeAll();
			layer.open({content:res.message,skin: 'msg',time: 2});
			if(res.message=='请先登录'){
				var backurl = window.location.href;
  				backurl = encodeURIComponent(backurl);
				setTimeout(function(){
					location.href='/index.php?p=8&a=login&url='+backurl;
				},1800);
			}
			if(res.code==1){
				if(ifshoucang==0){
					ifshoucang = 1;
					$("#shoucang_btn").addClass('pintuanshangpinye_7_left_on').html('<img src="/skins/default/images/pintuanshangpinye_18.png"/><br>已收藏');
				}else{
					ifshoucang = 0;
					$("#shoucang_btn").removeClass('pintuanshangpinye_7_left_on').html('<img src="/skins/default/images/pintuanshangpinye_19.png"/><br>收藏');
				}
			}
		},
		error: function() {
			layer.closeAll();
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}
function init_pingjia(){
	if(ifpingjia==0){
		ifpingjia=1;
		layer.open({type:2,content:'加载中'});
		lay_flow.load({
			elem: '#flow_ul'
			,done: function(page, next){
				layer.closeAll();
				var lis = [];
				$.ajax({
					type: "POST",
					url: "/index.php?p=4&a=get_pdt_comments&pageNum=10&page="+page,
					data: "productId="+productId,
					dataType:"json",timeout : 20000,
					success: function(res){
						$.each(res.data, function(index, item){
							str = '<li>'+
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
							lis.push(str);
						});
						next(lis.join(''), page < res.pages);
						baguetteBox.run('.pingjia');
					},
					error: function() {
						layer.closeAll();
						layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
					}
				});
			}
		});
	}
}
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
//显示商品标签，type:1.单独购  2.分享购  3.0元购  4.拼团购
function show_keys(type,price,fanli){
	fanli_money = fanli;
	if(type==4){
		$("#fx_lijigoumai_kaituan").show();
	}else{
		$("#price_sale").text('￥'+price);
		$("#fx_lijigoumai_tc").show();
		if(type==3){
			$("#fx_lijigoumai_tc .fx_lijigoumai_3").eq(1).show();
			$("#fx_lijigoumai_tc .fx_lijigoumai_3").eq(0).hide();
		}else{
			$("#fx_lijigoumai_tc .fx_lijigoumai_3").eq(0).show();
			$("#fx_lijigoumai_tc .fx_lijigoumai_3").eq(1).hide();
		}
	}
	//var hei = $(document).scrollTop();
	//$(".fx_lijigoumai_tc .fx_lijigoumai").css("bottom",'-'+hei+'px');
	$("body").css("overflow","hidden");
	buy_type = type;
}
function select_address(dom,id,name,phone,addr){
	$(".cp_peisong_2 li").removeClass('addressOn');
	$(dom).addClass('addressOn');
	addressId = id;
	$("#address_div").html('<img src="/skins/default/images/shangpinxx_16.png"/> '+addr);
	$("#cp_peisong_tc").hide();
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=select_address&id="+id,
		data: "",
		dataType:"json",timeout : 8000,
		success: function(resdata){}
	});
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
		url: "/index.php?p=4&a=add_gouwuche&addType="+addType,
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
			if(addType==1){
				location.href='/index.php?p=4&a=queren&xinren='+xinren;
			}else if(addType==2){
				location.href='/index.php?p=4&a=queren&if_yushou=1';
			}else if(addType==3){
				location.href='/index.php?p=4&a=queren&lingyuangou=1';
			}else{
				layer.open({content:'产品添加成功~~',skin: 'msg',time: 2});
				$('#cp_lijigoumai_tc').hide();
				var gwc_num = parseInt($("#gwc_num").text());
				$("#gwc_num").text(gwc_num+num).show();
			}
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
	$(".cp_lijigoumai_3_down a.cp_lijigoumai_3_down_on").each(function(){
		if(sn==''){
			sn = $(this).attr("data-id");
			sn_title = $(this).text();
		}else{
			sn = sn+'-'+$(this).attr("data-id");
			sn_title +=','+$(this).text();
		}
	});
	$("#sn_title").text(sn_title);
	layer.open({type:2});
	console.log(sn);
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=get_pdtsn_info",
		data: "productId="+productId+"&key_ids="+sn+"&buy_type="+buy_type,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			inventoryId = resdata.inventoryId;
			if(inventoryId==0){
				layer.open({content:'该规格已下架',skin: 'msg',time: 2});
				buy_limit = 1;
			}else if(if_yushou==1&&inventoryId>0){
				location.href='?p=4&a=view&id='+inventoryId;
			}else{
				layer.closeAll();
				fanli_money = resdata.fanli;
				$("#price_sale").html('￥'+resdata.price);
				$(".chanpin_2_04").html('¥'+resdata.price+' <span>￥'+resdata.price_market+'</span>');
				$("#price_market1").html('￥'+resdata.price_market);
				$("#price_user").text(resdata.price_user);
				$("#price_user1").text(resdata.price_user);
				$("#price_tuan").text(resdata.price_tuan);
				$("#price_user_tuan1").text(resdata.price_user_tuan1);
				$("#price_user_shequ1").text(resdata.price_user_shequ1);
				$("#kucun").html('库存'+resdata.kucun);
				$("#pdt_sn").text(resdata.sn);
				$("#pdtContCont1").html(resdata.cont1);
				$("#pdtContCont2").html(resdata.cont2);
				$("#pdtContCont3").html(resdata.cont3);
				if(resdata.kucun<=0){
					buy_limit = 2;
				}else if(resdata.price<=0){
					buy_limit = 1;
				}else{
					buy_limit = 0;
				}
				if(resdata.images.length>0){
					swiper.removeAllSlides();
					$.each(resdata.images,function(key,val){
						swiper.appendSlide('<div class="swiper-slide"><a href="'+val+'"><img src="'+val+'" width="100%" /></a></div>');
					});
					baguetteBox.run('.swiper-wrapper');
				}
				show_imgs();
			}
		},
		error: function() {
			layer.closeAll('loading');
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}
function countDown(time,id){
  var end_time1 = time*1000;
  var sys_second1 = (end_time1-new Date().getTime())/1000;
  setInterval(function(){
    if(sys_second1>1) {
      sys_second1 -= 1;
      var day = Math.floor((sys_second1 / 3600) / 24);
      var hour = Math.floor((sys_second1 / 3600) % 24);
      var minute = Math.floor((sys_second1 / 60) % 60);
      var second = Math.floor(sys_second1 % 60);
      if(day>0){
        hour = day*24+hour;
      }
      if(minute<10){
      	minute = '0'+minute;
      }
      if(second<10){
      	second = '0'+second;
      }
      $("#jishiqi"+id).html(hour+":"+minute+":"+second);
    }else{
      $("#jishiqi"+id).html("00:00:00");
    }
  }, 1000);
}
function lingqu(id){
  layer.open({type:2});
  $.ajax({
    type: "POST",
    url: "/index.php?p=8&a=yhq_lingqu",
    data: "id="+id,
    dataType:"json",timeout :10000,
    success: function(res){
      layer.closeAll();
      layer.open({content:res.message,skin: 'msg',time: 2});
    }
  });
}
//团购相关
function huan_tuan_type(type){
	tuan_type = type;
	$("#tuan_type_div .pintuanxx_kaituan_2_down_on").removeClass("pintuanxx_kaituan_2_down_on");
	$("#tuan_type_div ul li").eq(type-1).find("a").addClass("pintuanxx_kaituan_2_down_on");
	$("#price_sale").hide();
	$("#price_user1").hide();
	if(type==1){
		$("#price_tuan").show();
		$("#price_shequ_tuan").hide();
		$("#price_user_tuan1").show();
		$("#price_user_shequ1").hide();
	}else{
		$("#price_tuan").hide();
		$("#price_shequ_tuan").show();
		$("#price_user_tuan1").hide();
		$("#price_user_shequ1").show();
	}
	
}
//点击购买、加购物车、开团;buyType:1购买 2开团；if_jiagou：1立即购买 2加购物车
function show_buy_div(buyType,if_jiagou){
	$('#cp_lijigoumai_tc').show();
	if(buyType==2){
		$('#gouwu_div').hide();
		$('#liji_div').hide();
		$('#kaituan_div').show();
		$("#tuan_type_div").show();
		huan_tuan_type(tuan_type);
	}else{
		$('#kaituan_div').hide();
		$("#tuan_type_div").hide();
		if(buyType==1){
			$('#gouwu_div').hide();
			$('#liji_div').show();
		}else{
			$('#gouwu_div').show();
			$('#liji_div').hide();
		}
		$("#price_sale").show();
		$("#price_tuan").hide();
		$("#price_shequ_tuan").hide();
		$("#price_user1").show();
		$("#price_user_tuan1").hide();
		$("#price_user_shequ1").hide();
	}
}
function kaituan(){
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
	if(num>tuan_limit){
		var tishi = '最多可买'+tuan_limit+'份';
		if(tuan_limit==0){
			tishi = '该商品已售罄~~';
		}
		layer.open({content:tishi,skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=add_gouwuche&addType=2",
		data: "productId="+productId+"&inventoryId="+inventoryId+"&num="+num,
		dataType:"json",timeout : 20000,
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
			location.href='/index.php?p=4&a=queren&if_yushou=0&tuan_type='+tuan_type+'&tuan_id='+tuan_id;
		},
		error: function() {
			layer.closeAll('loading');
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}