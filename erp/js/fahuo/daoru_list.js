$(document).ready(function(){
	$(document).bind('click',function(){ 
		$("#riqilan").slideUp(200);
	});
	$(".sprukulist_01").click(function(eve){
		$("#riqilan").slideToggle(200);
		stopPropagation(eve);
	});
});
function reloadTable(curpage){
	layer.load();
  	var startTime = $("#startTime").val();
  	var endTime = $("#endTime").val();
  	var username = $("#username").val();
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
			,startTime:startTime
			,endTime:endTime
			,username:username
		},page: {
			curr: page
		},initSort: {
		    field: order1
		    ,type: order2
		  }
	});
	$("th[data-field='id']").hide();
}
layui.use(['upload','form'], function(){
	var form = layui.form
	,upload = layui.upload;
	upload.render({
	    elem: '#uploadFile'
	    ,url: '?m=system&s=upload&a=uploadXls'
	    ,accept: 'file'
    	,exts: 'xls|xlsx'
	    ,before: function(obj){
	      layer.load();
	    }
	    ,done: function(res){
	      layer.closeAll('loading');
	      //导入成功之后
	      $.ajax({
			type:"post",
			url:"?s=fahuo&a=daoru_order",
			data:"filepath="+res.url,
			timeout:"4000",
			dataType:"json",
			async:false,
			success: function(data){
				$("#shuju").html(data.content);
				var res = JSON.stringify(data.data);
    			console.log(res);
				$("#pandianJsonData").val(res);
				$(".print_tc").show();
				reloadTable(1);
				//window.location.reload();
			},
			error:function(){
	            //alert("超时,请刷新");
	        }

	    });
	      //导入成功之后
	    }
	    ,error: function(){
	      layer.closeAll('loading');
	      layer.msg('上传失败，请重试', {icon: 5});
	    }
	});
});