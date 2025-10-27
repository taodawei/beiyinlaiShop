<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION['demo_comId'];
if($_SESSION['if_tongbu']==1){
  $comId = 10;
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
}
$id = (int)$request['id'];
?>
<div class="wode">
  <div class="wode_1">
    银行卡
    <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=qianbao'">
      <img src="/skins/default/images/sousuo_1.png" alt=""/>
    </div>
  </div>
  <div class="yinhangka">
    <div class="yinhangka_up">
      <ul>
        <?
        $yhkList = $db->get_results("select * from user_bank where userId=$userId and comId=$comId");
          if($yhkList){///index.php?p=8&a=tjyhk&id=<?=$value->id
            foreach ($yhkList as $key => $value) {
              ?>
              <li><a href="/index.php?p=8&a=tjyhk&id=<?=$value->id?>">
                <div class="yinhangka_up_left">
                  <!-- <img src="/skins/default/images/yinhangka_13.png" alt=""/> -->
                </div>
                <div class="yinhangka_up_right">
                  <h2><?=$value->bank_name?></h2>
                  <br>
                  <span>**** **** **** <?=substr($value->bank_card, -4)?></span>
                </div>
                <div class="clearBoth"></div></a>
              </li>
              <? 
            }
          }?>
        </ul>
      </div>
      <? if(empty($yhkList)){?>
        <div class="yinhangka_down"><a href="/index.php?p=8&a=tjyhk">
          <div class="yinhangka_down_left">
            <b>+</b> 添加银行卡
          </div>
          <div class="yinhangka_down_right">
            <img src="/skins/default/images/yinhangka_15.png" alt=""/>
          </div>
          <div class="clearBoth"></div></a>
        </div>
      <? }?>
    </div>
  </div>

  <script type="text/javascript">
    $(function() {
      $('body').css('background', '#2e3233');
      $('#qr').click(function(){
        if($('#name').val() == '' || $('#msn').val() == '' || $('#bank_name').val() == '' || $('#bank_card').val() == ''){
          alert('请补全信息');
          return false;
        }
        $('#fomrtj').submit();
      });
    });
  </script>
