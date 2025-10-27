<?
global $db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if($_SESSION['if_tongbu']==1){
  $db_service = getCrmDb();
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
  $user = $db->get_row("select id,username,nickname,image,level,money from users where comId=$comId and zhishangId=$userId limit 1");
  //$user = $db_service->get_row("select username,name as nickname,image,level,money,jifen from demo_user where id=$userId");
}else{
  $userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
  $user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
}

require_once "wxshare.php";
$jssdk = new JSSDK("wx884dbf4e2438fa18", "711ab512cdb945214a7d5c812b864c9b");
$signPackage = $jssdk->GetSignPackage();
?>
<style>
.a-upload {
  width: auto;
  position: relative;
  cursor: pointer;
  overflow: hidden;
  display: inline-block;
  *display: inline;
  *zoom: 1;
  background-color:none;
}
</style>
<div class="wode">
  <div class="wode_1">
    我的
    <div class="wode_1_left" onclick="location.href='/index.php?p=8'">
      <img src="/skins/default/images/sousuo_1.png" />
    </div>
  </div>
  <div class="zhanghuguanli">
    <div class="zhanghuguanli_01">
      <ul>
        <li>
          <div class="zhanghuguanli_01_left" style="line-height:2.25rem;">
            头像
          </div>
          <div class="zhanghuguanli_01_right">
              <a href="javascript:;" class="a-upload">
                <input name="uploadfile" id="uploadfile" type="file" onchange="showPic(this);">
                <img src="<?php echo $user->image==""?"/skins/default/images/wode_1.png":$user->image?>"  class="zhanghuguanli_01_right_img1"/>
                <img src="/skins/default/images/querendingdan_12.png" />
              </a>
          </div>
          <div class="clearBoth"></div>
        </li>
        <li>
          <div class="zhanghuguanli_01_left">
            用户名
          </div>
          <div class="zhanghuguanli_01_right">
            <span><?=$user->username;?></span>
          </div>
          <div class="clearBoth"></div>
        </li>
        <li>
          <div class="zhanghuguanli_01_left">
            昵称
          </div><a href="/index.php?p=8&a=nc">
            <div class="zhanghuguanli_01_right">
              <?=$user->nickname;?>  <img src="/skins/default/images/querendingdan_12.png" />
            </div></a>
            <div class="clearBoth"></div>
          </li>
          <li>
            <div class="zhanghuguanli_01_left">
              账户安全
            </div><a href="/index.php?p=8&a=editpwd">
              <div class="zhanghuguanli_01_right">
                修改密码  <img src="/skins/default/images/querendingdan_12.png" />
              </div></a>
              <div class="clearBoth"></div>
            </li>
            <li>
              <div class="zhanghuguanli_01_left">
                支付密码
              </div><a href="/index.php?p=8&a=editzfpwd">
                <div class="zhanghuguanli_01_right">
                  修改密码  <img src="/skins/default/images/querendingdan_12.png" />
                </div></a>
                <div class="clearBoth"></div>
              </li>
              <li>
                <div class="zhanghuguanli_01_left">
                  发票资质
                </div><a href="/index.php?p=8&a=editfapiao">
                <div class="zhanghuguanli_01_right">
                  设置发票资质  <img src="/skins/default/images/querendingdan_12.png" />
                </div></a>
                <div class="clearBoth"></div>
              </li>
              <li onclick="location.href='/index.php?p=8&a=logout'">
                <div class="zhanghuguanli_01_left">
                  退出登录
                </div>
                <div class="clearBoth"></div>
              </li>
            </ul>
          </div>
          <!-- <? if($user->level>1){?>
            <div class="zhanghuguanli_02">
              <ul>
                <li>
                  <div class="zhanghuguanli_01_left">
                    我的授权证书
                  </div><a href="/index.php?p=8&a=sq">
                    <div class="zhanghuguanli_01_right">
                      <img src="/skins/default/images/querendingdan_12.png" />
                    </div></a>
                    <div class="clearBoth"></div>
                </li>
                <li>
                  <div class="zhanghuguanli_01_left">
                    ID号
                  </div>
                  <div class="zhanghuguanli_01_right">
                    <b><?=$user->id;?></b>
                  </div>
                  <div class="clearBoth"></div>
                </li>
              </ul>
            </div>
          <? }?> -->
        </div>
      </div>
<script type="text/javascript" src="/skins/resource/scripts/MegaPixImage.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/exif.js"></script>
<script type="text/javascript">
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
                  url: "/index.php?p=1&a=upload_content&touxiang=1",
                  data: "content="+base64,
                  dataType:"json",timeout : 10000,
                  success: function(resdata){
                    layer.closeAll();
                    layer.open({content:resdata.msg,skin: 'msg',time: 2});
                    if(resdata.code==1){
                      $(".zhanghuguanli_01_right_img1").attr("src",resdata.url);
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