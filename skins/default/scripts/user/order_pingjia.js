layui.use(['rate'], function(){
	var rate = layui.rate;
	rate.render({
		elem: '#pingfen',
		value : 5,
		setText: function(value){
			star = value;
		}
	})
});
//上传压缩图片
function showPic(data) {
	if (data.files.length>0) {
        for (var i = 0; i <data.files.length; i++) {
            var file = data.files[i];
            EXIF.getData(file, function() {
                EXIF.getAllTags(this); 
                Orientation = EXIF.getTag(this, 'Orientation');
            });
            var reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(e){
                var image = new Image();
                image.src = e.target.result;
                image.onload = function() {
                    var expectWidth = this.naturalWidth;
                    var expectHeight = this.naturalHeight;
                    if (this.naturalWidth > this.naturalHeight && this.naturalWidth > 800) {
                        expectWidth = 800;
                        expectHeight = expectWidth * this.naturalHeight / this.naturalWidth;
                    } else if (this.naturalHeight > this.naturalWidth && this.naturalHeight > 800) {
                        expectHeight = 800;
                        expectWidth = expectHeight * this.naturalWidth / this.naturalHeight;
                    }
                    var canvas = document.createElement("canvas");
                    var ctx = canvas.getContext("2d");
                    canvas.width = expectWidth;
                    canvas.height = expectHeight;
                    ctx.drawImage(this, 0, 0, expectWidth, expectHeight);
                    var base64 = null;
                    var mpImg = new MegaPixImage(image);
                    mpImg.render(canvas, {
                        maxWidth: 800,
                        maxHeight: 800,
                        quality: 0.8,
                        orientation: Orientation
                    });
                    base64 = canvas.toDataURL("image/jpeg", 0.8);
                    layer.open({type:2});
                    $.ajax({
                    	type: "POST",
                    	url: "/index.php?p=1&a=upload_content",
                    	data: "content="+base64,
                    	dataType:"json",timeout : 10000,
                    	success: function(resdata){
                    		layer.closeAll();
                    		layer.open({content:resdata.msg,skin: 'msg',time: 2});
                    		if(resdata.code==1){
                    			var str = '<div style="display:inline-block;margin-right:5px"><img src="'+resdata.url+'"></div>';
                    			$("#upload_img_div").before(str);
                    			imgs = imgs==''?resdata.url:imgs+'|'+resdata.url;
                    		}
                    	},
                    	error:function(){
                    		layer.closeAll();
                    		layer.open({content:'网络异常',skin: 'msg',time: 2});
                    	}
                    });
                    
                };
            }
        }
    }
}
/*function showPic(data){
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
            layer.open({type:2});
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
                                        $.ajax({
                                            type: "POST",
                                            url: "/index.php?p=1&a=upload_content",
                                            data: "content="+localData,
                                            dataType:"json",timeout : 10000,
                                            success: function(resdata){
                                                layer.closeAll();
                                                layer.open({content:resdata.msg,skin: 'msg',time: 2});
                                                if(resdata.code==1){
                                                    var str = '<div style="display:inline-block;margin-right:5px"><img src="'+resdata.url+'"></div>';
                                                    $("#upload_img_div").before(str);
                                                    imgs = imgs==''?resdata.url:imgs+'|'+resdata.url;
                                                }
                                            },
                                            error:function(){
                                                layer.closeAll();
                                                layer.open({content:'网络异常',skin: 'msg',time: 2});
                                            }
                                        });
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
}*/

function pingjia(){
	var content = $("#content").val();
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=19&a=pingjia&tijiao=1",
		data: "orderId="+orderId+"&inventoryId="+inventoryId+"&star="+star+"&imgs="+imgs+"&content="+content+"&comId="+comId,
		dataType:"json",timeout : 10000,
		success: function(resdata){
			layer.closeAll();
			layer.open({content:resdata.message,skin: 'msg',time: 2});
			if(resdata.code==1){
				setTimeout(function(){
					history.go(-1);
				},1800);
			}
		},
		error:function(){
			layer.closeAll();
			layer.open({content:'网络异常',skin: 'msg',time: 2});
		}
	});
}