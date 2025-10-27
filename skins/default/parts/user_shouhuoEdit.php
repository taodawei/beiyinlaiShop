<? 
global $db, $request;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$id = (int)$request['id'];
$shouhuo = $db->get_row("select * from user_address where id=$id");
$url = urlencode($request['url']);
?>
<style type="text/css">
._citys {width:100%; height:100%;display: inline-block; position: relative;}
._citys span {color: #cf2a51; height: 15px; width: 15px; line-height: 15px; text-align: center; border-radius: 3px; position: absolute; right: 1em; top: 10px; border: 1px solid #cf2a51; cursor: pointer;}
._citys0 {width: 100%; height: 34px; display: inline-block; padding: 0; margin: 0;}
._citys0 li {float:left; height:34px;line-height: 34px;overflow:hidden; font-size:.75rem; color: #888; width: 33%; text-align: center; cursor: pointer; }
.citySel {border-bottom: 2px solid #cf2a51; }
._citys1 {width: 100%;height:80%; display: inline-block; padding: 10px 0; overflow: auto;}
._citys1 a {height: 35px; display: block; color: #666; padding-left: 6px; margin-top: 3px; line-height: 35px; cursor: pointer; font-size:.7rem; overflow: hidden;}
._citys1 a:hover { color: #fff; background-color: #cf2a51;}
.ui-content{border: 1px solid #EDEDED;}
</style>
<div class="sousuo">
  <div class="wode_1">
      新建地址
        <div class="wode_1_left" onclick="history.go(-1)">
          <img src="/skins/default/images/sousuo_1.png" />
        </div>
    </div>
  <form id="sheditForm" name="sheditForm" method="post" action="/index.php?p=8&a=shouhuoEdit&tijiao=1">
    <input type="hidden" name="id" value="<?=$request['id']?>" />
    <input type="hidden" name="url" value="<?=$url?>" />
    <div class="xinjiandizhi">
      <div class="xinjiandizhi_1">
          <ul>
            <li>
                  <input type="text" placeholder="收件人姓名" id="name" name="name" value="<?=$shouhuo->name?>" class="xinjiandizhi_1_input"/>
                </li>
                <li>
                  <input type="text" placeholder="手机号" id="phone" name="phone" value="<?=$shouhuo->phone?>" class="xinjiandizhi_1_input"/>
                </li>
                <li>
                  <input type="text" placeholder="省市区" id="select_city" readonly="true" name="ssq" value="<?php echo $shouhuo->areaName;?>" class="xinjiandizhi_1_input"/>
                    <img src="/skins/default/images/querendingdan_11.png" class="img_select_city" />
                    <input type="hidden" name="harea" id="harea" data-id="<?php echo $shouhuo->areaId;?>">
                    <input type="hidden" name="areaId" id="areaId" value="<?php echo $shouhuo->areaId;?>">
                </li>
                <li>
                  <textarea name="address" id="address" class="xinjiandizhi_1_textarea" placeholder="详细地址（需要填写小区或街道、不需重填写省市区）"><?=$shouhuo->address?></textarea>
                </li>
          </ul>
        </div>
        <div class="xinjiandizhi_2">
            <div class="xinjiandizhi_2_left">
              设为默认地址
            </div>
            <div class="xinjiandizhi_2_right">
              <input type="hidden" name="moren" id="moren" value="">
            <?php if($shouhuo->moren == 1){?>
              <img src="/skins/default/images/xinjiandizhi_11.png"  data-i="2" class="xinjiandizhi_1_qh" />
            <?php }else{?>
              <img src="/skins/default/images/xinjiandizhi_1.png"  data-i="1" class="xinjiandizhi_1_qh" />
            <?php }?>
            </div>
            <div class="clearBoth"></div>
        </div>
      <div class="xinjiandizhi_3" style="position:relative;" id="shbc">
          <a href="javascript:;" >保存</a>
        </div>
    </div>
  </form>
</div>

<div class="bj" style="display:none;"></div>
<script type="text/javascript">
    var areaId = 0;
    var url = '<?=empty($url)?'/index.php':$url?>';
</script>
<script type="text/javascript" src="/skins/resource/scripts/cityJson.js?v=1.1"></script>
<script type="text/javascript" src="/skins/resource/scripts/citySet.js"></script>
<script>
$(document).ready(function(){
  $(".xinjiandizhi_1_qh").click(function(){
    if($(this).attr('data-i') == "1"){
      $(this).attr('src','/skins/default/images/xinjiandizhi_11.png');
      $(this).attr('data-i', "2");
    }else if($(this).attr('data-i') == "2"){
      $(this).attr('src','/skins/default/images/xinjiandizhi_1.png');
      $(this).attr('data-i', "1");
    }
  });
  $("#select_city").click(function (e) {
    SelCity(this,e);
    $(".bj").show();
  });
  $(".img_select_city").click(function(e){
    var dom = $("#select_city");
    SelCity(dom[0],e);
    $(".bj").show();
  });
  $(".bj").click(function(){
    $(this).hide();
  });
  $("#shbc").click(function(){
    $('#moren').val($('.xinjiandizhi_1_qh').attr('data-i'));
    $('#areaId').val($('#harea').attr('data-id'));

    if($('#name').val() == ''){
      alert('请输入收件人姓名');
      return false;
    }
    if($.trim($('#phone').val()).length !=11){
      alert('请输入正确的手机号');
      return false;
    }
    if($('#areaId').val() == ''){
      alert('请选择省市区');
      return false;
    }
    if($('#address').val() == ''){
      alert('请输入详细地址');
      return false;
    }
    $('#sheditForm').submit();
  });
});

</script>

