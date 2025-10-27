layui.use(['laydate','form','upload'], function(){
    var laydate = layui.laydate
    ,form = layui.form
    ,upload = layui.upload
    var uploadInit = upload.render({
        elem: '#uploadImg'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#uploadImg').html('<img src="'+res.url+'" width="720">');
                $("#originalPic").val(res.url);
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    form.on('submit(tijiao)', function(data){
        var uPattern = /^[a-zA-Z0-9_-]{5,16}$/;
        if($("#username").val()==''){
            layer.msg('请填写管理员账号',function(){});
            return false;
        }
        if(!uPattern.test($("#username").val())){
            layer.msg('管理员账号由5-16位字母或数字组成',function(){});
            return false;
        }
        if($("#ids").val()==0){
            if($("#pwd").val()==''){
                layer.msg('请填写管理员密码',function(){});
                return false;
            }
            if(!uPattern.test($("#pwd").val())){
                layer.msg('管理员密码由5-16位字母或数字组成',function(){});
                return false;
            }
        }
        if($("#name").val()==''){
            layer.msg('请填写管理员姓名',function(){});
            return false;
        }
        layer.load();
    });
});