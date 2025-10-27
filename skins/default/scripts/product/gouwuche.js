$(function(){
	$("#select_all").click(function(){
		if($(this).prop("checked")){
			$(".gouwuche_3 li").attr("data-select",1);
			$(".gouwuche_3 input[type='checkbox']").prop("checked", true);
		}else{
			$(".gouwuche_3 li").attr("data-select",0);
			$(".gouwuche_3 input[type='checkbox']").removeAttr("checked");
		}
		renderZongji();
	});
});
function num_edit(num,domId){
	var nowvalue = parseInt($("#gouwuche_input_"+domId).val());
	nowvalue+=num;
	if(nowvalue<1)nowvalue=1;
	$("#gouwuche_input_"+domId).val(nowvalue);
	update_num(domId,nowvalue);
	renderZongji();
}
function update_num(domId,num){
	num = parseInt(num);
	if(num<1)num=1;
	$("#gouwuche_input_"+domId).val(num);
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=edit_gouwuche_num",
		data: "id="+domId+"&num="+num,
		dataType:"json",timeout : 5000,
		success: function(resdata){
			$("#gwc_num").text(resdata.count);
		}
	});
}
function del_product(id,pdtId){
	layer.open({
		content: '确定要删除该商品吗？'
		,btn: ['确定', '取消']
		,yes: function(index){
			layer.open({type:2});
			$.ajax({
				type: "POST",
				url: "/index.php?p=4&a=del_gouwuche",
				data: "id="+id,
				dataType:"json",timeout : 5000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.open({content:resdata.message,skin:'msg',time:2});
						return false;
					}
					$("#g_pdt_"+id).remove();
					renderZongji();
				},
				error: function() {
					layer.closeAll('loading');
					layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
				}
			});
		}
	});
}
function select_gouwuche_item(pdtId){
	var nowSelect = parseInt($("#g_pdt_"+pdtId).attr("data-select"));
	if(nowSelect==1){
		$("#g_pdt_"+pdtId).attr("data-select",'0');
	}else{
		$("#g_pdt_"+pdtId).attr("data-select",'1');
	}
	renderZongji();
}
function renderZongji(){
	var num = 0;
	var zongNum = 0;
	var zongPrice = 0;
	$(".gouwuche_3 li").each(function(){
		if($(this).attr("data-select")==1){
			$(this).find("input.gouwuche_input").each(function(){
				num++;
				var znum = parseFloat($(this).val());
				var zprice = parseFloat($(this).attr("data-price"));
				zongNum = zongNum+znum;
				zongPrice = zongPrice+(znum*zprice);
			});
		}
	});
	zongNum = Math.round(zongNum*100)/100;
	zongPrice = Math.round(zongPrice*100)/100;
	//weight = Math.round(weight*100)/100;
	//$("#zongWeight").html(weight);
	//$("#gouwuche_num").html(num);
	$("#gouwuche_zongnum").html(zongNum);
	$("#gouwuche_zongprice").html('￥'+zongPrice);
}
function qingkong_gouwuche(){
	layer.open({
		content: '确定要清空购物车吗？'
		,btn: ['确定', '取消']
		,yes: function(index){
			layer.open({type:2});
			$.ajax({
				type: "POST",
				url: "/index.php?p=4&a=qingkong_gouwuche",
				data: "",
				dataType:"json",timeout : 5000,
				success: function(resdata){
					layer.closeAll();
					if(resdata.code==0){
						layer.open({content:resdata.message,skin:'msg',time:2});
						return false;
					}
					$(".gouwuche_3").html('<a href="/index.php?p=4" style="padding:2rem 0rem;text-align:center;display: block;">购物车还是空的，来挑几件中意的商品吧~~~</a>');
					renderZongji();
				},
				error: function() {
					layer.closeAll('loading');
					layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
				}
			});
		}
	});
}
function tijiao_gouwuche(){
	var str = '';
	$(".gouwuche_3 li").each(function(){
		if($(this).attr("data-select")==1){
			$(this).find("input.gouwuche_input").each(function(){
				var dom = $(this);
				var value = parseFloat(dom.val());
				if(value>0){
					var id = dom.attr("data-id");
					var productId = dom.attr("data-pid");
					var comId = dom.attr('data-comId');
					if(str==''){
						str = id+'@@'+productId+'@@'+value+'@@'+comId;
					}else{
						str = str+'||'+id+'@@'+productId+'@@'+value+'@@'+comId;
					}
				}
			});
		}
	});
	if(str==''){
		layer.open({content:'请先选择要下单的商品~~',skin: 'msg',time: 162});
		return false;
	}
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=4&a=add_gouwuche",
		data: "addType=1&content="+str,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			if(resdata.code==0){
				layer.open({content:resdata.message,skin: 'msg',time: 2});
				return false;
			}
			location.href='/index.php?p=4&a=queren';
		},
		error: function() {
			layer.closeAll();
			layer.open({content:'网络错误，请刷新重试',skin: 'msg',time: 2});
		}
	});
}