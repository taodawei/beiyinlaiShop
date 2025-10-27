layui.use(['laydate','upload','form'], function(){
	var laydate = layui.laydate
	,form = layui.form
	,upload = layui.upload
	upload.render({
		elem: '#upload1'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    //预读本地文件，如果是多文件，则会遍历。(不支持ie8/9)
		    obj.preview(function(index, file, result){
		    	$("#upload1").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload1").attr('src','/inc/img/nopic.svg');
			}else{
				$("#IDCARD_FRONT").val(res.url);
			}
		}
		,error: function(){
			layer.closeAll('loading');
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload2'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    //预读本地文件，如果是多文件，则会遍历。(不支持ie8/9)
		    obj.preview(function(index, file, result){
		    	$("#upload2").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload2").attr('src','/inc/img/nopic.svg');
			}else{
				$("#IDCARD_BACK").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload3'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    //预读本地文件，如果是多文件，则会遍历。(不支持ie8/9)
		    obj.preview(function(index, file, result){
		    	$("#upload3").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload3").attr('src','/inc/img/nopic.svg');
			}else{
				$("#UNI_CREDIT_CODE").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload4'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload4").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload4").attr('src','/inc/img/nopic.svg');
			}else{
				$("#CORP_CODE").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload5'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload5").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload5").attr('src','/inc/img/nopic.svg');
			}else{
				$("#TAX_CODE").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload6'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload6").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload6").attr('src','/inc/img/nopic.svg');
			}else{
				$("#ORG_CODE").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload7'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload7").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload7").attr('src','/inc/img/nopic.svg');
			}else{
				$("#OP_BANK_CODE").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload8'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload8").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload8").attr('src','/inc/img/nopic.svg');
			}else{
				$("#HAND_IDCARD").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload9'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload9").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload9").attr('src','/inc/img/nopic.svg');
			}else{
				$("#SETTLE_BANKCARD").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	upload.render({
		elem: '#upload10'
		,url: '/yop-api/sendUpload.php'
		,accept: 'images'
		,size: 1024
		,before:function(obj){
			layer.load();
			var files = obj.pushFile();
		    obj.preview(function(index, file, result){
		    	$("#upload10").attr('src',result);
		    });
		}
		,done: function(res){
			layer.closeAll('loading');
			if(res.code > 0){
				return layer.msg(res.msg);
				$("#upload10").attr('src','/inc/img/nopic.svg');
			}else{
				$("#HAND_BANKCARD").val(res.url);
			}
		}
		,error: function(){
			layer.msg('上传失败，请重试', {icon: 5});
		}
	});
	form.on('submit(tijiao)', function(data){
        layer.load();
    });
});