<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$id = (int)$request['id'];
if($id){
  $yhkInfo = $db->get_row("select * from user_bank where id=$id limit 1");
}
?>
<div class="wode">
  <div class="wode_1">
      添加银行卡
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=yhk'">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
    </div>
<form id="fomrtj" action="/index.php?p=8&a=tjyhk&id=<?=$id?>&tijiao=1" method="post">
  <input type="hidden" name="id" value="<?=$id?>">
    <div class="addyinhangka">
        <div class="addyinhangka_1">
          <ul>
            <li>
                  <input type="text" id="name" name="name" value="<?=$yhkInfo->name?>" placeholder="姓名" class="addyinhangka_1_input"/>
                </li>
                <li>
                  <input type="text" id="msn" name="msn" value="<?=$yhkInfo->msn?>" placeholder="身份证" class="addyinhangka_1_input"/>
                </li>
                <li>
                  <input type="text" id="bank_card" name="bank_card" value="<?=$yhkInfo->bank_card?>" placeholder="请输入银行储蓄卡卡号" class="addyinhangka_1_input"/>
                </li>
                <li>
                  <input type="text" id="bank_name" readonly="true" name="bank_name" value="<?=$yhkInfo->bank_name?>" placeholder="开户行" class="addyinhangka_1_input"/>
                </li>
                              
          </ul>
        </div>
        <div class="addyinhangka_2">
          <img src="/skins/default/images/yinhangka_1.png" alt=""> <span>请确保姓名、身份证、银行开户为同一人</span>
          <div class="clearBoth"></div>
        </div>
        <div class="duanxinyanzheng_4">
          <a href="#" id="qr">确定</a>
        </div>
    </div>
</form>
</div>
<script type="text/javascript">
$(function() {
    $('#qr').click(function(){
        if($('#name').val() == '' || $('#msn').val() == '' || $('#bank_name').val() == '' || $('#bank_card').val() == ''){
            alert('请补全信息');
            return false;
        }
        $('#fomrtj').submit();
    });
    $("#bank_card").change(function(){
      var cardId = $(this).val();
      if(cardId.length>6){
        layer.open({type:2,content:'检测中'});
        $.ajax({
          type: "POST",
          url: "/index.php?p=1&a=check_yhk",
          data: "cardId="+cardId,
          dataType:"json",timeout : 20000,
          success: function(res){
            console.log(res);
            layer.closeAll();
            if(res.code==0){
              layer.open({content:res.message,skin: 'msg',time: 2});
              $("#bank_card").focus();
            }else{
              $("#bank_name").val(res.bank_name);
            }
          },
          error: function() {
            layer.closeAll();
            layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
          }
        });
      }else{
        layer.open({content:'请填写正确的银行卡号',skin: 'msg',time: 2});
      }
    });
});
</script>
