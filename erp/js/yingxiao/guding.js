layui.use(['upload'], function(){
    var upload = layui.upload;
    upload.render({
        elem: '#tu1'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu1 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=0&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu2'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu2 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=1&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu3'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu3 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=2&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu4'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu3 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=3&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu5'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu5 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=4&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu6'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu6 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=5&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu7'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu7 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=6&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu8'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu8 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=7&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu9'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu9 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=8&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
    upload.render({
        elem: '#tu10'
        ,url: '?m=system&s=upload&a=upload&limit_width=no'
        ,before:function(){
            layer.load();
        }
        ,done: function(res){
            layer.closeAll('loading');
            if(res.code > 0){
                return layer.msg(res.msg);
            }else{
                $('#tu10 img').attr("src",res.url);
                $.ajax({
                    type: "POST",
                    url: "?s=yingxiao&a=update_banner",
                    data: "parentId=1&index=9&key=originalPic&val="+res.url,
                    dataType:'json',timeout : 8000
                });
            }
        }
        ,error: function(){
          layer.msg('上传失败，请重试', {icon: 5});
        }
    });
});
function change_url(index,val){
    $.ajax({
        type: "POST",
        url: "?s=yingxiao&a=update_banner",
        data: "parentId=1&index="+index+"&key=linkUrl&val="+val,
        dataType:'json',timeout : 8000
    });
}