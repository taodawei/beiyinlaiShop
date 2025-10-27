<link rel="stylesheet" type="text/css" href="/skins/default/styles/sqzz.css">
<div>
    <div class="sqzz1">
        <div class="zqzz_top1">
            申请经销商
            <div class="zqzz_top1_1" onclick="location.href='/index.php?p=8';">
                <img src="/skins/default/images/s_biao_14.png"/>
            </div>
        </div>
        <div class="sqzz2" style="margin-top: 1.75rem;">
            <form id="form1" method="post" action="?p=1&a=shenqing1113&tijiao=1">
                <input type="hidden" placeholder="请上传身份证照片" id="originalPic" name="originalPic" value="">
                <div class="sqzz2_1" style="position:relative;">
                    <input name="uploadfile" id="uploadfile" type="file" onchange="showPic(this);" style="position:absolute;left:0px;right:0px;top:0px;bottom:0px;opacity:0;">
                    <img src="/skins/default/images/sqzz_icon8.png"/><br>拍照/选择身份证
                </div>
                <div class="sqzz2_2">
                    <ul>
                        <li>
                            <div class="sqzz2_2_1"><img src="/skins/default/images/sqzz_icon9.png"/>姓名：</div>
                            <div class="sqzz2_2_2"><input type="text" name="name" id="name" placeholder="请输入您的姓名"/></div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="sqzz2_2_1"><img src="/skins/default/images/sqzz_icon10.png"/>电话：</div>
                            <div class="sqzz2_2_2"><input type="text" name="phone" id="phone" placeholder="请输入您的电话"/></div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="sqzz2_2_1"><img src="/skins/default/images/sqzz_icon13.png"/>地址：</div>
                            <div class="sqzz2_2_2"><input type="text" name="address" id="address" placeholder="请输入地址"/></div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            </form>
        </div>
        <div class="sqzz3">
            <a href="javascript:" onclick="shenqing();"><img src="/skins/default/images/sqzz_icon16.png" /></a>
        </div>
    </div>
    <div class="clearBoth"></div>
</div>
<script type="text/javascript" src="/skins/resource/scripts/MegaPixImage.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/exif.js"></script>
<script type="text/javascript">
    function shenqing(){
        var tijiao = 1;
        $("#form1 input").each(function(){
            if($(this).val()==''){
                layer.open({content:$(this).attr('placeholder'),skin: 'msg',time: 2});
                tijiao = 0;
                return false;
            }
        });
        if(tijiao==1){
            $("#form1").submit();
        }
    }
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
                            layer.open({content:'上传成功',skin: 'msg',time: 2});
                            $("#originalPic").val(resdata.url);
                            $(".sqzz2_1 img").attr('src',resdata.url).css('width','100%');
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
</script>