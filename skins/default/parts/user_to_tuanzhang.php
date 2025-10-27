<link href="/skins/default/styles/yongjin.css" rel="stylesheet" type="text/css">
<div class="shengjizhinan">
	<div class="shengjizhinan_1" style="background-color:#fff">
    	微信号
        <div class="shengjizhinan_1_left" onclick="go_prev_page();">	
        	<img src="/skins/default/images/fenlei_1.png" />
        </div>
    </div>
	<div class="bangdingweixinshehzi_1" style="position:relative;">
        <input name="uploadfile" id="uploadfile" type="file" onchange="showPic(this);">
    	<img src="/skins/default/images/erweima_1.png" id="show_img" />
        <br>点击上传微信二维码
    </div>
    <div class="bangdingweixinshehzi_2">
    	微 信 号 <input type="text" id="wxh" placeholder="请输入微信号"/>
    </div>
    <div class="bangdingweixinshehzi_2">
        真实姓名 <input type="text" id="name" placeholder="请输入真实姓名"/>
    </div>
    <div class="bangdingweixinshehzi_2">
        手 机 号 <input type="text" id="phone" placeholder="请输入手机号"/>
    </div>
    <div class="bangdingweixinshehzi_3">	
    	<a href="javascript:" onclick="shengji();">提交</a>
    </div>
</div>
<script type="text/javascript" src="/skins/resource/scripts/MegaPixImage.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/exif.js"></script>
<script type="text/javascript">
var img_url = '';
function showPic(data) {
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
                if (this.naturalWidth > this.naturalHeight && this.naturalWidth > 300) {
                    expectWidth = 300;
                    expectHeight = expectWidth * this.naturalHeight / this.naturalWidth;
                } else if (this.naturalHeight > this.naturalWidth && this.naturalHeight > 300) {
                    expectHeight = 300;
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
                    maxWidth: 300,
                    maxHeight: 300,
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
                    img_url = resdata.url;
                    if(resdata.code==1){
                      $("#show_img").attr("src",resdata.url);
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
function shengji(){
    var wxh = $("#wxh").val();
    var name = $("#name").val();
    var phone = $("#phone").val();
    if(wxh==''){
        layer.open({content:'请填写真实微信号',skin: 'msg',time: 2});
        return false;
    }
    if(name=='' || phone==''){
        layer.open({content:'请填写姓名和电话',skin: 'msg',time: 2});
        return false;
    }
    $.ajax({
        type:"POST",
        url:"/index.php?p=1&a=shenqing_tuan",
        data:"wxh="+wxh+"&name="+name+"&phone="+phone+"&wx_img="+img_url+"&tijiao=1",
        timeout:"10000",
        dataType:"json",
        success: function(res){
            layer.closeAll();
            layer.open({content:res.message,skin: 'msg',time: 2});
            setTimeout(function(){
                location.href='/index.php?p=8';
            },2000);
        },
        error:function(){
            alert("超时,请重试");
        }
    });
}
</script>