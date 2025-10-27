$(function(){
	$("#select_rdiv").click(function(){
		$("#shenqingshouhou_yuanyin_tc").show();
	});
});
function qiehuan_type(index,val){
	$(".shouhoushenqing_1_down ul li .shouhoushenqing_1_down_on").removeClass('shouhoushenqing_1_down_on');
	$(".shouhoushenqing_1_down ul li").eq(index).find("a").addClass('shouhoushenqing_1_down_on');
	$("#type").val(val);
	if(val==1){
		$(".hide_1").hide();
		$(".hide_3").show();
		//$("#money").val('').removeAttr('readonly');
	}else if(val==2){
		$(".hide_1").show();
		$(".hide_3").show();
        $(".tuihuo_num").show();
        $(".huanhuo_num").hide();
		//$("#money").val(price_payed).attr('readonly','true');
        $("#nums").val(max_num);
	}else{
		$(".hide_1").show();
		$(".hide_3").hide();
        $(".tuihuo_num").hide();
        $(".huanhuo_num").show();
	}
}
function select_reason(index,reason){
	$(".shenqingshouhou_yuanyin_3 ul li .shenqingshouhou_yuanyin_3_on").removeClass("shenqingshouhou_yuanyin_3_on").find('.shenqingshouhou_yuanyin_3_right img').attr("src","/skins/default/images/shenqingshouhou_11.png");
	$(".shenqingshouhou_yuanyin_3 ul li").eq(index).find('a').addClass('shenqingshouhou_yuanyin_3_on').find('.shenqingshouhou_yuanyin_3_right img').attr("src","/skins/default/images/shenqingshouhou_12.png");
	$("#reason").val(reason);
	$("#select_rdiv").html(reason+' <img src="/skins/shequ/images/biao_17.png">');
}
function num_edit(n,proId){
	var num = parseInt($('#nums_'+proId).val());
	num = num+n;
	if(num<0)num=0;
	if(num>max_num){
		num = max_num;
		layer.open({content:'最多可申请'+max_num+'份',skin: 'msg',time: 2});
	}
    //var money = price_sale * num;
    //$("#money").val(money);
	$('#nums_'+proId).val(num);
}
/*function showPic(data){
	if (data.files && data.files[0]) {
        var file = data.files[0];
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
                var mpImg = new MegaPixImage(image);
                mpImg.render(canvas, {
                    maxWidth: 800,
                    maxHeight: 1200,
                    quality: 0.8,
                    orientation: Orientation
                });
                base64 = canvas.toDataURL("image/jpeg", 0.8);
                var str = '<li onclick="$(this).remove();"><img src="'+base64+'"><input type="hidden" name="images[]" value="'+base64+'"></li>';
                $("#add_img_li").before(str);
            };
        }
    }
}*/
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
function change_kuaidi_type(type){
    $("#kuaidi_type").val(type);
    if(type==1){
        $("#yunfei_div").hide();
    }else{
        $("#yunfei_div").show();
    }
}
function tijiao(){
	var type = $("#type").val();
	var money = $("#money").val();
	var remark = $("#remark").val();
    var kuaidi_type = $("#kuaidi_type").val();
    var kuaidi_money =$("#kuaidi_money").val();
	if($("#reason").val()==''){
		layer.open({content:'请选择申请原因',skin: 'msg',time: 2});
		return false;
	}
	if(type<3 && (money=='' || money<=0)){
		layer.open({content:'退款金额不正确',skin: 'msg',time: 2});
		return false;
	}
    if(type>1 && kuaidi_type==0){
        layer.open({content:'请选择运费负责',skin: 'msg',time: 2});
        return false;
    }
    if(type>1 && kuaidi_type==2 && kuaidi_money==''){
        layer.open({content:'请填写运费',skin: 'msg',time: 2});
        return false;
    }
	if(remark.length<6){
		layer.open({content:'问题描述不能少于6个字',skin: 'msg',time: 2});
		return false;
	}
	/*if((kuaidi_type==2 || type==1) && $("#add_img_li").prevAll().length==0){
		layer.open({content:'请先上传问题商品图片',skin: 'msg',time: 2});
		return false;
	}*/
	layer.open({type:2});
	$.ajax({
		type: "POST",
		url: "/index.php?p=21&a=add&tijiao=1&orderId="+orderId+"&comId="+comId,
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