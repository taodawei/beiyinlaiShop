<?
global $db;
if($_SESSION['if_tongbu']==1){
  $db_service = getCrmDb();
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
  $user = $db_service->get_row("select username from demo_user where id=$userId");
}else{
  $userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
  $user = $db->get_row("select username from users where id=$userId");
}
?>
<link rel="stylesheet" type="text/css" href="/skins/erp_zong/styles/shouquan.css">
<div class="wode">
  <div class="wode_1">
      修改支付密码
        <div class="wode_1_left" onclick="go_prev_page();">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
  </div>
  <div class="bangding_3">
    <form id="edptjForm" action="/index.php?p=8&a=editzfpwd&tijiao=1" method="post">
      <input type="hidden" id="username" value="<?=$user->username?>">
      <input type="hidden" name="url" id="url" value="<?=urlencode($request['url'])?>">
      <div class="xiugaimima_up">
        <ul>
          <li>
            <div class="bangding_3_left">
                支付密码
              </div>
            <div class="bangding_3_right">
                <input name="zfpass" id="zfpass" type="password" placeholder="请输入6位支付密码"/>
              </div>
            <div class="clearBoth"></div>
          </li>
          <li>
                <div class="bangding_3_left">
                    验证码
                </div>
                <div class="bangding_3_right">
                    <input type="text" name="yzm" id="yzm" placeholder="短信验证码"/>
                    <span id="send_btn" onclick="sendSms();">获取验证码</span>
                </div>
                <div class="clearBoth"></div>
            </li>
        </ul>
      </div>
      <div class="xiugaimima_down">
          <a href="javascript:;" id="qr">确认修改</a>
        </div>
    </form>
  </div>
</div>
<script type="text/javascript">
$(function(){
  $("#qr").click(function(){
    if($("#zfpass").val().length!=6){
      alert('支付密码必须为6位数字');
      return false;
    }
    var username = $("#username").val();
    var url = $("#url").val();
    var zfpass = $("#zfpass").val();
    var yzm = $("#yzm").val();
    layer.open({type:2});
      $.ajax({
          type: "POST",
          url: "/index.php?p=8&a=editzfpwd",
          data: "tijiao=1&username="+username+"&zfpass="+zfpass+"&yzm="+yzm,
          dataType:"json",
          timeout : 5000,
          success: function(resdata){
            layer.closeAll();
            layer.open({content:resdata.message,skin: 'msg',time: 2});
            if(resdata.code==1){
              setTimeout(function(){
                location.href = url==''?'/index.php?p=8&a=zhgl':'<?=urldecode($url)?>';
              },1500);
            }
          }
      });
  });
});
function sendSms(){
  var phone = $("#username").val();
  if(phone.length!=11){
    layer.open({content:'您的账号有问题，请联系管理员',skin: 'msg',time: 2});
    return false;
  }
  if(wait!=60){
    return false;
  }
  layer.open({type:2});
  $.ajax({
    type:"POST",
    url:"/index.php?p=8&a=sendSms2&phone="+phone,
    data:"type=reg",
    timeout:"4000",
    dataType:"json",
    success: function(res){
      layer.closeAll();
      layer.open({content:res.message,skin: 'msg',time: 2});
      if(res.code==1){
        time();
      }
    },
    error:function(){
      alert("超时,请重试");
    }
  });
}
var wait=60;
function time() {
  if (wait == 0) {
    $("#send_btn").text('重新获取');
    wait = 60;
  } else {
    $("#send_btn").text(wait+'秒');
    wait--;
    setTimeout(function() {
      time();
    },1000)
  }
}
</script>