$(function(){
	$("#select_rdiv").click(function(){
		$("#shenqingshouhou_yuanyin_tc").show();
	});
});
function select_reason(index,reason){
	$(".shenqingshouhou_yuanyin_3 ul li .shenqingshouhou_yuanyin_3_on").removeClass("shenqingshouhou_yuanyin_3_on").find('.shenqingshouhou_yuanyin_3_right img').attr("src","/skins/demo/images/shenqingshouhou_11.png");
	$(".shenqingshouhou_yuanyin_3 ul li").eq(index).find('a').addClass('shenqingshouhou_yuanyin_3_on').find('.shenqingshouhou_yuanyin_3_right img').attr("src","/skins/demo/images/shenqingshouhou_12.png");
	$("#reason").val(reason);
	$("#select_rdiv").html(reason+' <img src="/skins/demo/images/querendingdan_11.png">');
}
function check_tuanzhang(){
    var tuanzhangId = $("#tuanzhangId").val();
    if(tuanzhangId>0){
        layer.open({type:2});
        $.ajax({
            type: "POST",
            url: "/index.php?p=8&a=check_tuanzhang&id="+tuanzhangId,
            data: "",
            dataType:"json",timeout : 8000,
            success: function(res){
                layer.closeAll();
                if(res.code==1){
                    layer.open({
                        content: '请核对要投诉的团长信息：<br><span style=\"color:red;\">团长ID：'+tuanzhangId+"&nbsp;&nbsp;团长姓名："+res.name+"</span><Br>如果不对请重新输入团长ID"
                        ,btn: '确定'
                      });
                }else{
                    layer.open({content:'团长ID不存在，请核对后重新输入',skin: 'msg',time: 2});
                    $("#tuanzhangId").val('');
                }
            },
            error: function() {
                layer.closeAll();
                layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
            }
        });
    }
}
function showPic(data){
    wx.chooseImage({
        count:3,//上传数量
        sizeType: ['compressed'],
        success: function(res) {
            images.localId = res.localIds;
            if (images.localId.length == 0) {
                alert('请先使用 chooseImage 接口选择图片');
                return;
            }
            var i = 0, length = images.localId.length;
            images.serverId = [];
            function upload() {
                wx.uploadImage({
                    localId: images.localId[i],
                    success: function(res) {
                        i++;
                        images.serverId.push(res.serverId);
                        wx.downloadImage({
                            serverId: res.serverId, // 需要下载的图片的服务器端ID，由uploadImage接口获得
                            isShowProgressTips: 1, // 默认为1，显示进度提示
                            success: function (res) {
                                var localId = res.localId; // 返回图片下载后的本地ID
                                //通过下载的本地的ID获取的图片的base64数据，通过对数据的转换进行图片的保存
                                wx.getLocalImgData({
                                    localId: localId, // 图片的localID
                                    success: function (res) {
                                        var localData = res.localData;
                                        if (localData.indexOf('data:image') != 0) {
                                            localData = 'data:image/jpeg;base64,' +  localData;
                                        }
                                        localData = localData.replace(/\r|\n/g, '');
                                        localData = localData.replace('data:image/jpg', 'data:image/jpeg');
                                        var str = '<li onclick="$(this).remove();"><img src="'+localData+'"><input type="hidden" name="images[]" value="'+localData+'"></li>';
                						$("#add_img_li").before(str);
                                    }
                                });
                            }
                        });
                        if (i < length) {
                            upload();
                        }
                    },
                    fail: function(res) {
                        alert(JSON.stringify(res));
                    }
                });
            }
            return upload();
        }
    });
}
function tijiao(){
	var tuanzhangId = $("#tuanzhangId").val();
    var reason = $("#reason").val();
	var remark = $("#remark").val();
    if(tuanzhangId==''){
        layer.open({content:'请输入要投诉的团长的ID',skin: 'msg',time: 2});
        return false;
    }
	if($("#reason").val()==''){
		layer.open({content:'请选择投诉类型',skin: 'msg',time: 2});
		return false;
	}
	if(remark.length<6){
		layer.open({content:'投诉内容不能少于6个字',skin: 'msg',time: 2});
		return false;
	}
	if($("#add_img_li").prevAll().length==0){
		layer.open({content:'请先上传证据图片',skin: 'msg',time: 2});
		return false;
	}
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=8&a=add_tousu&tijiao=1",
		data: $("#add_form").serialize(),
		dataType:"json",timeout : 20000,
		success: function(res){
			layer.closeAll();
			layer.open({content:res.message,skin: 'msg',time: 2});
			if(res.code==1){
				setTimeout(function(){
					location.href='/index.php?p=8';
				},1500);
			}
		},
		error: function() {
			layer.closeAll();
			layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
		}
	});
}