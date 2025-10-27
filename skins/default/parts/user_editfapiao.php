<?
global $db;
$comId = $_SESSION['if_tongbu']==1?10:$_SESSION['demo_comId'];
$userId = $_SESSION['if_tongbu']==1?$_SESSION[TB_PREFIX.'zhishangId']:(int)$_SESSION[TB_PREFIX.'user_ID'];
$fapiao = $db->get_row("select * from user_fapiao where userId=$userId and comId=$comId limit 1");
?>
<style type="text/css">
.kaipiaoxinxiadd{margin-top:0.35rem;background-color:#ffffff;padding:0.75rem 0}
.kaipiaoxinxiadd_1{padding-bottom:3rem}
.kaipiaoxinxiadd_1 ul li{height:2.75rem;border-bottom:#e9eced 0.025rem solid;padding:0 0.625rem}
.kaipiaoxinxiadd_1_left{width:4.45rem;float:left;height:2.75rem;line-height:2.75rem;font-size:0.65rem;color:#000000}
.kaipiaoxinxiadd_1_right{width:11.95rem;float:right}
.kaipiaoxinxiadd_1_right input{width:100%;height:2.75rem;vertical-align:top;border:none;background:none;outline:none;font-size:0.65rem;color:#000000}
.kaipiaoxinxiadd_2{padding-bottom:2.5rem;text-align:center}
.kaipiaoxinxiadd_2 a{display:inline-block;width:15.05rem;height:1.925rem;background-color:#1ea8ff;text-align:center;line-height:1.925rem;font-size:0.7rem;color:#ffffff;border-radius:0.1rem}
</style>
<div class="wode">
  <div class="wode_1">
      设置发票资质
        <div class="wode_1_left" onclick="location.href='/index.php?p=8&a=zhgl';">
          <img src="/skins/default/images/sousuo_1.png" alt=""/>
        </div>
  </div>
  <div class="kaipiaoxinxiadd">
  	<form action="/index.php?p=8&a=editfapiao&tijiao=1" method="post" id="edptjForm">
	<div class="kaipiaoxinxiadd_1">
    	<ul>
    		<li>
            	<div class="kaipiaoxinxiadd_1_left">
                	公司名称
                </div>
            	<div class="kaipiaoxinxiadd_1_right">
                	<input type="text" name="com_title" value="<?=$fapiao->com_title?>" id="com_title" placeholder="请输入公司名称"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li>
            	<div class="kaipiaoxinxiadd_1_left">
                	纳税人识别码
                </div>
            	<div class="kaipiaoxinxiadd_1_right">
                	<input type="text" name="shibiema" id="shibiema" value="<?=$fapiao->shibiema?>" placeholder="请输入纳税人识别码"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li>
            	<div class="kaipiaoxinxiadd_1_left">
                	电话号码
                </div>
            	<div class="kaipiaoxinxiadd_1_right">
                	<input type="text" name="phone" id="phone" value="<?=$fapiao->phone?>" placeholder="请输入电话号码"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li>
            	<div class="kaipiaoxinxiadd_1_left">
                	单位地址
                </div>
            	<div class="kaipiaoxinxiadd_1_right">
                	<input type="text" name="address" id="address" value="<?=$fapiao->address?>" placeholder="请输入单位地址"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li>
            	<div class="kaipiaoxinxiadd_1_left">
                	开户银行
                </div>
            	<div class="kaipiaoxinxiadd_1_right">
                	<input type="text" name="bank_name" id="bank_name" value="<?=$fapiao->bank_name?>" placeholder="请输入开户银行"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li>
            	<div class="kaipiaoxinxiadd_1_left">
                	银行账号
                </div>
            	<div class="kaipiaoxinxiadd_1_right">
                	<input type="text" name="bank_card" id="bank_card" value="<?=$fapiao->bank_card?>" placeholder="请输入银行账号"/>
                </div>
            	<div class="clearBoth"></div>
            </li>
            <li>
                <div class="kaipiaoxinxiadd_1_left">
                    收票人手机
                </div>
                <div class="kaipiaoxinxiadd_1_right">
                    <input type="text" name="shoupiao_phone" id="shoupiao_phone" value="<?=$fapiao->shoupiao_phone?>" placeholder="请输入收票人手机"/>
                </div>
                <div class="clearBoth"></div>
            </li>
            <li>
                <div class="kaipiaoxinxiadd_1_left">
                    收票人邮箱
                </div>
                <div class="kaipiaoxinxiadd_1_right">
                    <input type="text" name="shoupiao_email" id="shoupiao_email" value="<?=$fapiao->shoupiao_email?>" placeholder="请输入银行账号"/>
                </div>
                <div class="clearBoth"></div>
            </li>
    	</ul>
    </div>
	<div class="kaipiaoxinxiadd_2">
    	<a href="javascript:" id="qr">保存</a>
    </div>
</form>
</div>
</div>
<script type="text/javascript">
$(function(){
  $("#qr").click(function(){
    if($("#com_title").val().length<3){
      alert('请填写完整的公司名称');
      return false;
    }
    if($("#shibiema").val().length<6){
      alert('请填写正确的纳税人识别码');
      return false;
    }
    $('#edptjForm').submit();
  });
});
</script>