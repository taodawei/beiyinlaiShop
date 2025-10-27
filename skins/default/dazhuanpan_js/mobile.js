var loading = false;

$(document).bind("mobileinit", function(){
	$.mobile.defaultPageTransition = 'none';
	$.mobile.loadingMessage = '';
	$.mobile.fixedToolbars.setTouchToggleEnabled(false);
	$.event.special.swipe.horizontalDistanceThreshold = 130;
}).bind('pagechange',function(e,d){
	if(d.options.reverse){
		$('ul.zzp-lists a.touch').removeClass('touch');
	}

	$('ul.zzp-lists').undelegate().delegate('a','click',function(){
	  		var $this=$(this);
	  		$this.closest('ul.zzp-lists').find('a.touch').removeClass('touch');
	  		$this.addClass('touch');
	})
});

function getContext(){
	return $("div:jqmData(role='page'):last");
}

function debug(content){
	//console.log(content);
}

function initCategory(){
	var context = getContext();
	jQuery("#menu_box_two",context).hide();
	jQuery("#InfoCate",context).click(function(){		
		if(jQuery(".ui-page-active #menu_box_two").css('display') == 'none'){
			jQuery(".ui-page-active #menu_box_two").show();
			jQuery(".ui-page-active #InfoCate").removeClass("fold");
			jQuery(".ui-page-active #InfoCate").addClass("expand");
		}else{
			jQuery(".ui-page-active #menu_box_two").hide();
			jQuery(".ui-page-active #InfoCate").removeClass("expand");
			jQuery(".ui-page-active #InfoCate").addClass("fold");
		}
		
		return false;
	});
}

function initProduct(){
	var context = getContext();
	var count=3;	
	if(jQuery("#pmcCountTime",context).val()=='time'){
    var timer=window.setInterval(function(){
		count--;
		if(count<=0){
			clearInterval(timer);
			//ת
			window.history.back(-1); //0ʱһ
		}
	},1000);
}
	jQuery("#category_s",context).click(function(){
	      if(jQuery(".ui-page-active #category_l").css('display') == 'none'){
			jQuery(".ui-page-active #category_l").show();
			jQuery(".ui-page-active #category_s").removeClass("fold");
			jQuery(".ui-page-active #category_s").addClass("expand");
		}else{
			jQuery(".ui-page-active #category_l").hide();
			jQuery(".ui-page-active #category_s").removeClass("expand");
			jQuery(".ui-page-active #category_s").addClass("fold");
		}
	});	
}
$(".page_index").live('pageinit',function(event){
	loading = false;	
	initCategory();

	$(".page_index").unbind("swipeleft swiperight").bind("swipeleft", function( event, data ){
		debug(".page_index swipeleft");
		if(!loading){
			debug(".page_index swipeleft processing...");
			
			//loading = true;
			var page,loc=window.location.href;
			if(loc.indexOf("form=c")!=-1)
				$("#columns-Columns_navigation01-topnav-0 a").click();
			else
				$("#columns-Columns_navigation01-topnav-1 a").click();
		}

		return false;
	}).bind("swiperight", function( event, data ){
		debug(".page_index swiperight");
		if(!loading){
			debug(".page_index swiperight processing...");
			
			//loading = true;
			var page,loc=window.location.href;
			if(loc.indexOf("form=c")!=-1)
				$("#columns-Columns_navigation01-topnav-2 a").click();
			else
				$("#columns-Columns_navigation01-topnav-3 a").click();
		}

		return false;
	});
});	

$(".page_products_list").live('pageinit',function(event){
	loading = false;
	initCategory();
	//initProduct();

	$(".page_products_list").unbind("swipeleft swiperight").bind("swipeleft", function( event, data ){
		$(".ui-page-active #columns-Columns_navigation01-topnav-2 a").click();
		return false;
	}).bind("swiperight", function( event, data ){				
		$(".ui-page-active #columns-Columns_navigation01-topnav-0 a").click();
		return false;
	});
});

$(".page_news_list").live( 'pageinit',function(event){
	loading = false;	
	initCategory();

	$(".page_news_list").unbind("swipeleft swiperight").bind("swipeleft", function( event, data ){
		$(".ui-page-active #columns-Columns_navigation01-topnav-3 a").click();
		return false;				
	}).bind("swiperight", function( event, data ){
		$(".ui-page-active #columns-Columns_navigation01-topnav-1 a").click();
		return false;
	});
});


$(".page_products_detail").live( 'pageinit',function(event){	
	debug(".page_products_detail pageinit");	
	
	loading = false;	
	initCategory();
	//initProduct();	

	$(".page_products_detail").unbind("swipeleft swiperight").bind("swipeleft", function( event, data ){
		debug(".page_products_detail swipeleft");
		if(!loading){
			debug(".page_products_detail swipeleft processing...");
			//loading = true;
			var href = $(".ui-page-active #elem-Products_detail01-001 .next a").attr("href");
			if(href)
				$.mobile.changePage(href,{transition:"none"});
		}
		return false;
	}).bind("swiperight", function( event, data ){
		debug(".page_products_detail swiperight");
		if(!loading){
			debug(".page_products_detail swipeleft processing...");
			//loading = true;
			var href = $(".ui-page-active #elem-Products_detail01-001 .last a").attr("href");
			if(href)
				$.mobile.changePage(href,{transition:"none"});
		}
		return false;
	});
});

$(".page_news_detail").live( 'pageinit',function(event){
	debug(".page_news_detail pageinit");
	
	loading = false;	
	initCategory();
	
	$(".page_news_detail").unbind("swipeleft swiperight").bind("swipeleft", function( event, data ){
		debug(".page_news_detail swipeleft");		
		var href = $(".ui-page-active #News_detail01-001 .next a").attr("href");
		if(href)
			$.mobile.changePage(href,{transition:"none"});
		return false;		
	}).bind("swiperight", function( event, data ){
		debug(".page_news_detail swiperight");
			
		var href = $(".ui-page-active #News_detail01-001 .last a").attr("href");
		if(href)
			$.mobile.changePage(href,{transition:"none"});
		
		return false;
	});
});

function setCurrentColumn(clickColumn){
	sessionStorage["currentColumn"]=clickColumn;
}

function submitTotalSearch(){
	sessionStorage["currentColumn"]='1';
	$('.ui-page-active #totalSearch').submit()
}
  function newsreplace(id){
    document.getElementById('gsxwhead').className='on';
	document.getElementById('hydthead').className='on';
    document.getElementById(id+'head').className='';
	document.getElementById('gsxwcont').style.display='none';
	document.getElementById('hydtcont').style.display='none';
    document.getElementById(id+'cont').style.display='';
  }
  function lyreplace(id){
    document.getElementById('xglyhead').className='on';
	document.getElementById('wylyhead').className='on';
    document.getElementById(id+'head').className='';
	document.getElementById('xglycont').style.display='none';
	document.getElementById('wylycont').style.display='none';
    document.getElementById(id+'cont').style.display='';
  }
 $(function(){
   $("#ly").click(function(){
     $("#toolMoreWrap").toggle();
   });
   $("#citychoise .areaList").click(function(){
	  id = $(this).attr("id");
	  title = $(this).find("p").html();
	  $("#areaname").val($("#areaname").val()+title);
	  $("#areaname").css("display","blcok");
	  //$("#areaname").show();
	  $.ajax({
			type:"POST",
			url:"/pay.php?a=getWapAreas",
			data:"id="+id,
			timeout:"4000",
			dataType:"text",
			success: function(html){
			 if(html!=""){
			    $("#citychoise ul").html(html);
			  }else{
				$("#areaid").val(id);
			    $("#selectArea").hide();
				$("#areaname").show();
			  }
			},
			error:function(){
				alert("超时,请重试");
			}
		});
	});
   $("#areaname").click(function(){
	  $(this).val("");
	  $(this).hide();
	  getAreas(0);
	  $("#selectArea").show();
	});
   $("#z3g-goon").click(function(){
	 $("#successBox").hide();
	 $("#toolMoreWraps").hide();
	});
   $("#addGouwuche").click(function(){
	 id =$(this).attr("date-id");
	 if($(this).attr("userid")=="0"){
		alert("尚未登录，请先登录。");
		location.href='shouhuo.php';
	}else{
	userid = $(this).attr("userid");
	 $.ajax({
			type:"POST",
			url:"/pay.php?a=addGouwuche&userid="+userid,
			data:"id="+id,
			timeout:"4000",
			dataType:"text",
			success: function(html){
			  if(html==""){
			    alert("加入购物车失败，请刷新重试。");
			  }else if(html=="shouhuo"){
				location.href='shouhuoAdd.php?basket=basket';
			  }else if(html=="gouwuche"){
				 $("#toolMoreWraps").show();
				 $("#successBox").show();
			  }else{
				alert(html);  
			  }
			},
			error:function(){
				alert("超时,请重试");
			}
		});
	 }
   });
   $("#lijidinggou").click(function(){
	 id =$(this).attr("date-id");
	 if($(this).attr("userid")=="0"){
		alert("尚未登录，请先登录。");
		location.href='shouhuo.php';
	}else{
	userid = $(this).attr("userid");
	 $.ajax({
			type:"POST",
			url:"/pay.php?a=addGouwuche&userid="+userid,
			data:"id="+id,
			timeout:"4000",
			dataType:"text",
			success: function(html){
			  if(html==""){
			    alert("加入购物车失败，请刷新重试。");
			  }else if(html=="shouhuo"){
				location.href='shouhuoAdd.php?basket=basket';
			  }else if(html=="gouwuche"){
				 location.href='gouwuche.php';
			  }else{
				alert(html);  
			  }
			},
			error:function(){
				alert("超时,请重试");
			}
		});
	 }
   });
   $("#productlist .z3g-input").change(function(){
	  if(!isNaN($(this).val())){
	    var id = $(this).attr("data-id");
		var value=parseInt($(this).val());
		$.ajax({
				type:"POST",
				url:"/index.php?p=4&a=updatebasketfornum",
				data:"r="+id+"&num="+value,
				timeout:"4000",
				dataType:"text",                                 
				success: function(html){
					location.href="gouwuche.php";
				},
				error:function(html){
				   location.href="gouwuche.php";
				}
			});
	  }
	});
   $("#productlist li .reduce").click(function(){
		var id = $(this).attr("data-id");
		num = parseInt($(this).next().val())-1;
		var haoma = $(this).next();
		var value=num;
		if(value>0){
		  $.ajax({
				type:"POST",
				url:"/index.php?p=4&a=updatebasketfornum",
				data:"r="+id+"&num="+value,
				timeout:"4000",
				dataType:"text",                                 
				success: function(html){
					location.href="gouwuche.php";
				},
				error:function(html){
				   location.href="gouwuche.php";
				}
			});
		}
	});
   $("#productlist li .add").click(function(){
		var id = $(this).attr("data-id");
		num = parseInt($(this).prev().val())+1;
		var haoma = $(this).prev();
		var value=num;
		if(value>0){
		  $.ajax({
				type:"POST",
				url:"/index.php?p=4&a=updatebasketfornum",
				data:"r="+id+"&num="+value,
				timeout:"4000",
				dataType:"text",                                 
				success: function(content){
					location.href="gouwuche.php";
				},
				error:function(html){
				   location.href="gouwuche.php";
				}
			});
		}
	});
   $("#tjdingdan").click(function(){
	 if($("#psType").val()==""){
		 alert('请先选择配送方式');
		 $("#psType").focus();
		 return false;
     }else if($("#payType").val()==""){
		 alert('请先选择支付方式');
		 $("#payType").focus();
		 return false;
	 }else{
		 var remark=$("#remark").val();
		 var url = "order.php?psArea="+$("#areaid").val()+"&psType="+$("#psType").children('option:selected').val()+"&psPrice="+$("#psPrice").html()+"&payType="+$("#payType").children('option:selected').val()+"&payPrice="+$("#orderPrice").html()+"&remark="+remark;
		  location.href = url;
	 }
	});
   $("#psType").change(function(){
	  var psid =$(this).children('option:selected').val();
	  var psarea = $("#areaid").val();
	  var zongNum = $("#zongNum").val();
	  var zongHeight = $("#zongHeight").val();
	  if(psarea!=""){
	  $.ajax({
			type:"POST",
			url:"/pay.php?a=getPsPrice&psid="+psid+"&psarea="+psarea+"&zongNum="+zongNum,
			data:"zongHeight="+zongHeight,
			timeout:"4000",
			dataType:"text",
			success: function(html){
			  $("#psPrice").html(html);
			  $("#orderPrice").html(parseFloat(html)+parseFloat($("#pdtPrice").html()));
			},
			error:function(){
				alert("超时,请重试");
			}
		});
		}
	});
   $("#order_tabWrap li").click(function(){
	 id = $(this).attr("data-id");
	 $("#order_tabWrap li").each(function(){
	   $(this).removeClass("current");
	 });
	 $(this).addClass("current");
	 $("#order_cont1").hide();
	 $("#order_cont2").hide();
	 $("#order_cont3").hide();
	 $("#order_cont"+id).show();
   });
   $(".ordersList li .infoWrap").click(function(){
     id = $(this).attr("data-id");
	 location.href="vieworder.php?id="+id;
   });
 })
function getAreas(id){
	  if(id>0){
	    title = $("#"+id).find("p").html();
	    $("#areaname").val($("#areaname").val()+title);
	  }
	  $.ajax({
			type:"POST",
			url:"/pay.php?a=getWapAreas",
			data:"id="+id,
			timeout:"4000",
			dataType:"text",
			success: function(html){
			 if(html!=""){
			    $("#citychoise ul").html(html);
			  }else{
				$("#areaid").val(id);
			    $("#selectArea").hide();
				$("#areaname").show();
			  }
			},
			error:function(){
				alert("超时,请重试");
			}
		});
}
function changeStart(id){
  starttime = $("#starttime"+id).val();
  $.ajax({
		type:"POST",
		url:"/index.php?p=4&a=updatestarttime",
		data:"r="+id+"&starttime="+starttime,
		timeout:"4000",
		dataType:"text",                                 
		success: function(html){
			var content = eval('(' + html + ')');;
			$("#endtime"+id).val(content.endtime);
			$("#days"+id).html(content.days);
			$("#totolprice").html(content.price);
			alert("操作成功");
			},
			error:function(html){
				alert("操作失败，请刷新重试");
			}
		});
}
function changeend(id){
  starttime = $("#starttime"+id).val();
  endtime = $("#endtime"+id).find("option:selected").val();
  $.ajax({
		type:"POST",
		url:"/index.php?p=4&a=updateendtime",
		data:"r="+id+"&starttime="+starttime+"&endtime="+endtime,
		timeout:"4000",
		dataType:"text",                                 
		success: function(html){
		  if(html=="less"){
		    alert("订奶时间不能小于15天，请重新选择结束日期。");
		  }else{
			var content = eval('(' + html + ')');
			$("#days"+id).html(content.days);
			$("#totolprice").html(content.price);
			//alert("操作成功");
		  }
			},
			error:function(html){
				alert("操作失败，请刷新重试");
			}
		});
}
function checkLp(){
  if($("#username").val()==""){
   alert("卡号不能为空。");
   return false;
  }else if($("#password").val()==""){
    alert("密码不能为空。");
    return false;
  }else if($("#checkcode").val()==""){
    alert("验证码不能为空。");
    return false;
  }else{
    cardid = $("#username").val();
	cardpwd = $("#password").val();
	checkcode = $("#checkcode").val();
	$.ajax({
			type:"POST",
			url:"/index.php?m=pay&a=lipinka",
			data:"cardid="+cardid+"&cardpwd="+cardpwd+"&checkcode="+checkcode,
			timeout:"4000",
			dataType:"text",                                 
			success: function(html){
			  if(html=="checkcode"){
			    alert('验证码错误，请重新输入。');
			  }else if(html=="session"){
			    alert('由于长时间没有完成支付，导致支付失效，请去会员中心完成支付');
				location.href='userCenter.php';
			  }else if(html=="password"){
			    alert('密码不正确，请重新输入。');
			  }else if(html=="pipei"){
			    alert('该卡为个人专属卡，您不能使用。');
			  }else if(html=="money"){
			    alert('您的余额不足，请使用其他支付方式或联系客服为您充值。');
			  }else if(html=="nocard"){
			    alert('订奶卡不存在，请重新输入。');
			  }else if(html=="ok"){
			    alert('支付成功，我们会及时处理您的订单。');
				location.href='userCenter.php';
			  }else{
			    alert('系统错误，请联系管理员。');
				location.href='userCenter.php';
			  }
			  $("#lpform")[0].reset();
			},
			error:function(content){
				alert("请求错误，请从会员中心处支付。");
				location.href="/userCenter/";
			}
		});
  }
}
function getYzm(){
  var phone = $("#username").val();
  var yzm = $("#yzmdddd").val();
  $.ajax({
		type:"get",
		url:"/pay.php?a=checkPhone&phone="+phone,
		timeout:"4000",
		dataType:"text",
		success: function(html){
			 if(html=='ok'||html==''){
			   $.ajax({
				type:"get",
				url:"/send.php?phone="+phone+"&yzm="+yzm+"&port=80",
				timeout:"4000",
				dataType:"text",                                 
				success: function(html){
				  alert("发送成功，如果二分钟之内没有接收到请刷新重试。");
				  $("#yzmBtn").attr("onclick","");
				  $("#yzmBtn").attr("value","已发送");
				  $("#yzmBtn").prev().find(".ui-btn-text").html("已发送");
					},
					error:function(html){
						
					}
				});
			 }else{
			   alert("该手机号已经注册过了，请选择登录");
			   location.href='login.php';
			 }
			},
			error:function(html){
			}
		});
}
function checkPhone(phone){
    var ifhas = false;
	$.ajax({
		type:"get",
		url:"/pay.php?a=checkPhone&phone="+phone,
		timeout:"4000",
		dataType:"text",
		success: function(html){
			 if(html=='ok'||html==''){
			   ifhas=true;
			 }
			},
			error:function(html){
			}
		});
}
function ceshi(){
	$.ajax({
		type:"get",
		url:"/pay.php?a=checkPhone&phone=001",
		timeout:"4000",
		dataType:"text",
		success: function(html){
			 if(html=='ok'||html==''){
			   $.ajax({
				type:"get",
				url:"/send.php?phone=15284221022&yzm=123456&port=80",
				timeout:"4000",
				dataType:"text",                                 
				success: function(html){
				  alert("发送成功，如果二分钟之内没有接收到请刷新重试。");
				  $("#yzmBtn").attr("onclick","");
				  $("#yzmBtn").attr("value","已发送");
				  $("#yzmBtn").prev().find(".ui-btn-text").html("已发送");
					},
					error:function(html){
						
					}
				});
			 }else{
			   alert("该手机号已经注册过了，请选择登录");
			   //location.href='login.php';
			 }
			},
			error:function(html){
			}
		});
}
