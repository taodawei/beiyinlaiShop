<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
?>
<div class="wode">
  <div class="wode_1">
      昵称
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=zhgl'">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
    </div>
  <div class="nicheng">
      <form id="nctjForm" action="/index.php?p=8&a=nc&tijiao=1" method="post">
        <div class="nicheng_up">
          <div class="nicheng_up_left">
              <input type="text" name="nickname" id="nickname" value="<?=$user->nickname;?>"/>
            </div>
          <div class="nicheng_up_right" onclick="$('#nickname').val('');">
              <img src="/skins/default/images/denglu_15.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="nicheng_down">
          6-18个字符，可有中英文、数字组成
        </div>
        <div class="xiugaimima_down">
          <a href="javascript:" id="qrxg">确认修改</a>
        </div>
      </form>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        $("#qrxg").click(function(){
          if($('#nickname').val() == ''){
            alert('请输入昵称');
            return false;
          }
          $('#nctjForm').submit();
        });
    });
</script>
