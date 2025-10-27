<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
?>
<div class="wode">
  <div class="wode_1">
      修改密码
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=zhgl'">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
  </div>
  <div class="xiugaimima">
    <form id="edptjForm" action="/index.php?p=8&a=editpwd&tijiao=1" method="post">
      <div class="xiugaimima_up">
          <ul>
            <li>
                  <div class="xiugaimima_up_left">
                      原密码
                    </div>
                  <div class="xiugaimima_up_right">
                      <input name="pwd" id="pwd" type="password" placeholder="请输入原密码"/>
                    </div>
                  <div class="clearBoth"></div>
                </li>
                <li>
                  <div class="xiugaimima_up_left">
                      新密码
                    </div>
                  <div class="xiugaimima_up_right">
                      <input name="newpass" id="newpass" type="password" placeholder="请输入新密码"/>
                    </div>
                  <div class="clearBoth"></div>
                </li>
                <li>
                  <div class="xiugaimima_up_left">
                      确认新密码
                    </div>
                  <div class="xiugaimima_up_right">
                      <input name="repwd"  id="repwd" type="password" placeholder="请确认新密码"/>
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
    if($("#pwd").val() == "" || $("#newpass").val() == "" || $("#repwd").val() == ""){
      alert('密码不能为空');
      return false;
    }
    var pwd = $("#pwd").val();
    var newpass = $("#newpass").val();
    var repwd = $("#repwd").val();
    layer.open({type:2});
      $.ajax({
          type: "POST",
          url: "/index.php?p=8&a=editpwd",
          data: "tijiao=1&pwd="+pwd+"&newpass="+newpass+"&repwd="+repwd,
          dataType:"json",
          timeout : 5000,
          success: function(resdata){
            layer.closeAll();
            layer.open({content:resdata.message,skin: 'msg',time: 2});
            if(resdata.code==1){
              setTimeout(function(){
                location.href='/index.php?p=8&a=zhgl';
              },1500);
            }
          }
      });
  });
});
</script>