<?
global $db;
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
/*if($_SESSION['if_tongbu']==1){
  $comId = 10;
  $userId = (int)$_SESSION[TB_PREFIX.'zhishangId'];
  $db_service = getCrmDb();
  $user = $db_service->get_row("select id,username,name as nickname,image,level,money from demo_user where id=$userId");
}else{
  $user = $db->get_row("select id,username,nickname,image,level,money from users where id=$userId");
}*/

$id = (int)$request['id'];
$bzj = 0;//200;
$ktx = ($user->money-$bzj>0)?($user->money-$bzj):0;
$sjyhk = $db->get_row("select * from user_bank where userId=$userId and comId=$comId limit 1");
?>
<style>
#qd{
  float: right;
  color: #cf2950;
}
#txmoney{
  font-size: 1.5rem;
}
</style>
<form id="tjtx" action="/index.php?p=8&a=tixian&tijiao=1" method="post">
<input type="hidden" id="ktx" value="<?=$ktx?>">
<div class="wode">
  <div class="wode_1">
      提现
        <div class="wode_1_duanxin">
            <a href="/index.php?p=8&a=qianbao"><img src="/skins/default/images/xuanzediqu_1.png" alt=""/></a>
        </div>
    </div>

    <input type="hidden" name="zffs" id="zffs" value="<?=$sjyhk->id?>">
    <div class="chongzhi">
      <div class="chongzhi_1">
          <div class="chongzhi_1_left" >
              到账银行卡 <!-- <img src="/skins/default/images/yinhangka_14.png" alt=""/> --> &nbsp; 
              <span><?=$sjyhk->bank_name?>（<?=substr($sjyhk->bank_card, -4)?>）</span>
            </div>
          <div class="chongzhi_1_left" style="display: none;">
              到账微信零钱 <!-- <img src="/skins/default/images/yinhangka_14.png" alt=""/> --> &nbsp; 
              <span style="display: none;"><?=$sjyhk->bank_name?>（<?=substr($sjyhk->bank_card, -4)?>）</span>
            </div>
          <div class="chongzhi_1_right" style="display: none;">
              <img src="/skins/default/images/chongzhi_1.png" alt=""/>
            </div>
          <div class="clearBoth"></div>
        </div>
      <div class="chongzhi_2">
          提现金额
        </div>
      <div class="chongzhi_3">
          ¥ <input id="txmoney"  name="txmoney" type="number" min="1" max="5000" /> <span id="qbtx">全部提现</span>
        </div>
      <div class="chongzhi_4">
          当前钱包余额<?=$ktx?>元<最小提现金额1元>
        </div>
      <div class="chongzhi_5">
          <? if($ktx>0){?>
            <a href="javascript:" id="qrtx" style="background-color:#cf2950;">下一步</a>
          <? }else{?>
            <a href="javascript:" style="background-color:#ccc;">下一步</a>
          <? }?>
        </div>
    </div>
</div>

<!--支付方式-弹出-->
<div class="tixian_yinhang_tc" style="display:none;">
  <div class="bj" style="background-color:rgba(0,0,0,0.7);">
    </div>
  <div class="chongzhi_zhifufangshi">
      <div class="chongzhi_zhifufangshi_up">
          选择支付方式 <span id="qd">确定</span>
        </div>
      <div class="chongzhi_zhifufangshi_down">
          <ul>
          <?
          $yhkList = $db->get_results("select * from user_bank where userId=$userId");
          if($yhkList){
            foreach ($yhkList as $key => $value) {
              ?>
              <li data-id=<?=$value->id?> <? if($value->id == $sjyhk->id){?> current<? }?>>
                  <div class="chongzhi_zhifufangshi_down_left">
                      <!-- <img src="/skins/default/images/yinhangka_13.png" alt=""/> --> 
                      <?=$value->bank_name?>（<?=substr($value->bank_card, -4)?>）
                    </div>
                  <div class="chongzhi_zhifufangshi_down_right">
                      <img src="<? if($value->id == $sjyhk->id){?>/skins/default/images/shenqingshouhou_12.png<? }else{?>/skins/default/images/shenqingshouhou_11.png<? }?>" alt=""/>
                    </div>
                  <div class="clearBoth"></div>
                </li>
              <? 
            }
          }?>
          </ul>
        </div>
    </div>
</div>
<!--提现-支付密码-弹出-->
<div class="tixian_zhifumima_tc" style="display:none;">
  <div class="bj" style="background-color:rgba(0,0,0,0.7);">
    </div>
  <div class="tixian_zhifumima" style=" height:12rem;">
      <div class="tixian_zhifumima_1">
          <img src="/skins/default/images/xuanzediqu_1.png" alt=""/>
        </div>
      <div class="tixian_zhifumima_2">
          <h2>请输入支付密码</h2>¥<span id="txmoneySpan"></span>
        </div>
      <div class="duanxinyanzheng_2">
          <!-- <span><input type="text"/><input type="text"/><input type="text"/><input type="text"/><input type="text"/><input type="text"/></span> -->
          <span>
            <input type="password" id="zfmm" name="zfmm" maxlength="8">
            <i></i><i></i><i></i><i></i><i></i><i></i>
          </span>
        </div>
        <div class="chongzhi_5">
            <a href="javascript:;" id="zdzf" style="background-color:#cf2950; width:8rem; margin-top:0.5rem;">确定</a>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
  $(function(){
    $(".chongzhi_1_right img").click(function(){
      $(".tixian_yinhang_tc").show();
    });
    $(".chongzhi_zhifufangshi_down ul li").click(function(){
      $(this).find('img').attr('src');
      $(".chongzhi_zhifufangshi_down ul li").find(".chongzhi_zhifufangshi_down_right img").attr('src', '/skins/default/images/shenqingshouhou_11.png');
      $(this).find(".chongzhi_zhifufangshi_down_right img").attr('src', '/skins/default/images/shenqingshouhou_12.png');
      $(".chongzhi_zhifufangshi_down ul li").removeClass('current');
      $(this).addClass('current');
    });
    $(".bj").click(function(){
      $(".tixian_yinhang_tc").hide();
    });    
    $("#qrtx").click(function(){
      if($("#txmoney").val() == '' || parseFloat($("#txmoney").val()) > parseFloat($("#ktx").val())){
        layer.open({content:'余额不足',skin: 'msg',time: 2});
        return false;
      }
      if($("#txmoney").val()<1 || $("#txmoney").val()>5000){
        layer.open({content:'提现金额在1-5000之间',skin: 'msg',time: 2});
        return false;
      }
      $("#txmoneySpan").text($("#txmoney").val());
      $(".tixian_zhifumima_tc").show();
      $("input#zfmm").focus();
    });
    $(".tixian_zhifumima_1 img").click(function(){
      $(".tixian_zhifumima_tc").hide();
    });

    $("#zdzf").click(function(){
      var zf = $("#zfmm").val();
      $.ajax({
        type:"POST",
        url:"/index.php?p=8&a=qrtxmm",
        data:"zf="+zf,
        timeout:"4000",
        dataType:"json",
        success: function(res){
          if(res.code==0){
            layer.open({content:res.message,skin: 'msg',time: 2});
            $("input#zfmm").focus();  
          }else if(res.code==1){
            $("#tjtx").submit();
          }
        },
        error:function(){
          alert("超时,请重试");
        }
      });
    });

    $("#qd").click(function(){
      var id = $(".chongzhi_zhifufangshi_down ul li.current").attr('data-id');
      $("#zffs").val(id);
      var html = $(".chongzhi_zhifufangshi_down ul li.current .chongzhi_zhifufangshi_down_left").html();
      $(".chongzhi_1_left span").html(html);
      $(".tixian_yinhang_tc").hide();
    });

    $('#qbtx').click(function(){
      $('#txmoney').val($('#ktx').val());
    });
  });
</script>
