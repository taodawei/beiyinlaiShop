function qiehuan_type(index,type){
	$("#mendian_type").val(type);
	$(".shangjiaruzhu_1_right .shangjiaruzhu_1_right_on").removeClass('shangjiaruzhu_1_right_on');
	$(".shangjiaruzhu_1_right img").attr('src','/skins/demo/images/querendingdan_19.png');
	$(".shangjiaruzhu_1_right ul li").eq(index).find('a').addClass('shangjiaruzhu_1_right_on').find('img').attr('src','/skins/demo/images/querendingdan_18.png');
	if(index==0){
		$(".show_li_1").show();
		$(".show_li_2").hide();
		$(".show_li_3").hide();
	}else if(index==1){
		$(".show_li_1").hide();
		$(".show_li_2").show();
		$(".show_li_3").show();
	}else{
		$(".show_li_1").hide();
		$(".show_li_2").hide();
		$(".show_li_3").show();
	}
}
function qiehuan_type1(index){
	$("#shangjiaruzhu_2_right .shangjiaruzhu_2_right_on").removeClass('shangjiaruzhu_2_right_on');
	$("#shangjiaruzhu_2_right img").attr('src','/skins/demo/images/querendingdan_19.png');
	$("#shangjiaruzhu_2_right span").eq(index).addClass('shangjiaruzhu_2_right_on').find('img').attr('src','/skins/demo/images/querendingdan_18.png');
	if(index==0){
		$("#url_li").show();
	}else{
		$("#url_li").hide();
	}
}
//上传压缩图片
/*function showPic(data,dom) {
	if (data.files && data.files[0]) {
        var file = data.files[0];
        EXIF.getData(file, function() {
            EXIF.getAllTags(this); 
            Orientation = EXIF.getTag(this, 'Orientation');
        });
		
        var reader = new FileReader();
        reader.readAsDataURL(file);
		alert(1);
        reader.onload = function(e){
			alert(2);
            var image = new Image();
            image.src = e.target.result;
            image.onload = function() {
				alert(3);
                var expectWidth = this.naturalWidth;
                var expectHeight = this.naturalHeight;
                if (this.naturalWidth > this.naturalHeight && this.naturalWidth > 800) {
                    expectWidth = 800;
                    expectHeight = expectWidth * this.naturalHeight / this.naturalWidth;
                } else if (this.naturalHeight > this.naturalWidth && this.naturalHeight > 1200) {
                    expectHeight = 1200;
                    expectWidth = expectHeight * this.naturalWidth / this.naturalHeight;
                }
                var canvas = document.createElement("canvas");
                var ctx = canvas.getContext("2d");
                canvas.width = expectWidth;
                canvas.height = expectHeight;
                ctx.drawImage(this, 0, 0, expectWidth, expectHeight);
                var base64 = null;
				alert(4);
                var mpImg = new MegaPixImage(image);
                mpImg.render(canvas, {
                    maxWidth: 800,
                    maxHeight: 1200,
                    quality: 0.8,
                    orientation: Orientation
                });
				alert(5);
                base64 = canvas.toDataURL("image/jpeg", 0.8);
                $("#"+dom).val(base64);
                $(data).prev().prev().text('已上传');
            };
        }
    }
}*/
function shenqing(){
	var submit = true;
	$(".mustrow").each(function(){
		if($(this).val().length==0){
			var tishi = $(this).attr('placeholder');
			layer.open({content:tishi,skin: 'msg',time: 2});
			submit = false;
			return false;
		}
	});
	if(submit==false){
		return false;
	}
	if($("#img_zhizhao").val()==''){
		layer.open({content:'请上传证书',skin: 'msg',time: 2});
		return false;
	}
	$("#ruzhu_form").submit();
}
function showPic(data,dom){
    wx.chooseImage({
    	count:1,//上传数量
    	sizeType: ['compressed'],//压缩
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
					                    localData = localData.replace('data:image/jgp', 'data:image/jpeg');
                                        $("#"+dom).val(localData);
                						$(data).find('font').text('已上传');
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