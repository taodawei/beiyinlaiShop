<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user_shezhi = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
if(!empty($user_shezhi->jifen_pay_rule)){
    $content = json_decode($user_shezhi->jifen_pay_rule,true);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/mendianshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .layui-form-switch{height:32px;line-height:32px;width:65px;}
        .layui-form-switch em{font-size:16px;width:35px;right:8px;}
        .layui-form-switch i{top:4px;width:25px;height:25px;}
        .layui-form-onswitch i{left:44px;}
    </style>
</head>
<body>
	<div class="yueshezhi">
        <div class="yueshezhi_up">
            <img src="images/mdsz_12.png" alt=""/>  积分规则
        </div>
        <div class="jifenguize">
            <div class="jifenguize_up">
                <ul>
                    <li>
                        <a href="?s=mendian_set&a=jifen">交易积分</a>
                    </li>
                    <li>
                        <a href="javascript:" class="jifenguize_up_on">价值管理</a>
                    </li>
                    <!--<li>-->
                    <!--    <a href="?s=mendian_set&a=jifen_qiandao">每日签到</a>-->
                    <!--</li>-->
                    <!--<li>-->
                    <!--    <a href="?s=mendian_set&a=jifen_share">分享积分/抵扣金</a>-->
                    <!--</li>-->
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="jifenguize_down">
                <div class="jifenguize_down_01">
                    <div class="jiazhiguanli">
                        <form action="?s=mendian_set&a=jifen_jiazhi&submit=1" method="post" class="layui-form">
                            <div class="jiazhiguanli_up">
                                <div class="jiaoyijifen_up_1">
                                    会员积分价值
                                </div>
                                <div class="jiazhiguanli_up_1">
                                    <input type="checkbox" lay-skin="switch" <? if($user_shezhi->if_jifen_pay==1){?>checked="true"<? }?> lay-filter="if_jifen_pay" name="if_jifen_pay" value="1" lay-text="开启|关闭">
                                </div>
                                <div class="jiazhiguanli_up_3" <? if($user_shezhi->if_jifen_pay==0){echo 'style="display:none"';}?>> 
                                    <div class="jiazhiguanli_up_2">
                                        积分价值 每 <input name="content[jifen]" value="<?=$content['jifen']?>" lay-verify="shuzi" type="text"/>  积分 可以折算抵现1元 <span>*积分折算现金，积分数量为正整数。 </span>
                                    </div>
                                    <ul>
                                        <li>
                                            <input type="checkbox" name="content[if_bili]" <? if($content['if_bili']==1){?>checked="true"<?}?> value="1" lay-filter="if_bili" lay-skin="primary"> 交易每单可抵用订单金额比例 <input type="text" name="content[bili]" id="content_bili" value="<?=$content['bili']?>" <? if($content['if_bili']==0){?>readonly="true" lay-verify="bili" class="disabled"<?}?> placeholder=" 0.00"/> %  <span>*不设置则表示不限制。</span>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="content[if_shangxian]" <? if($content['if_shangxian']==1){?>checked="true"<?}?> value="1" lay-filter="if_shangxian" lay-skin="primary"> 默认每单交易抵现上限 <input type="text" name="content[shangxian]" id="content_shangxian" value="<?=$content['shangxian']?>" <? if($content['if_shangxian']==0){?>readonly="true" lay-verify="shuzi" class="disabled"<?}?> placeholder=" 0.00"/> 元  <span>*不设置则表示不限制。</span>
                                        </li>
                                        <li>
                                            <input type="checkbox" name="content[if_man]" <? if($content['if_man']==1){?>checked="true"<?}?> value="1" lay-filter="if_man" lay-skin="primary"> 每单消费金额满 <input type="text" name="content[man]" id="content_man" value="<?=$content['man']?>" <? if($content['if_man']==0){?>readonly="true" lay-verify="shuzi" class="disabled"<?}?> placeholder=" 0.00"/> 元，可使用积分抵现。  <span>*不设置则表示不限制。</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <!-- <div class="jiazhiguanli_down">
                                <div class="jiaoyijifen_up_1">
                                    分销商积分价值
                                </div>
                                <div class="jiazhiguanli_down_2">
                                    提升自身的分销等级<br>    
                                    分销等级和自身的分润相关<b> 等级越高，分销的分润值越大</b>
                                </div>
                            </div> -->
                            <div class="yueshezhi_down_03">
                                <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
    	layui.use(['form'], function(){
    		var form = layui.form;
	    	form.verify({
	    		shuzi:function(value,item){
	    			value = parseInt(value);
	    			if(isNaN(value)||value<1){
	    				return '字段必须是大于的正整数';
	    			}
	    		},
                bili:function(value,item){
                    value = parseFloat(value);
                    if(isNaN(value)||value<0||value>100){
                        return '比例必须是0-100之间的数字';
                    }
                }
	    	});
            form.on('switch(if_jifen_pay)',function(data){
                if(this.checked){
                    $(".jiazhiguanli_up_3").show(100);
                }else{
                    $(".jiazhiguanli_up_3").hide(100);
                }
            });
            form.on('checkbox(if_bili)',function(data){
                if(this.checked){
                    $("#content_bili").removeClass('disabled').prop('readonly',false);
                }else{
                    $("#content_bili").addClass('disabled').prop('readonly',true);
                }
            });
            form.on('checkbox(if_shangxian)',function(data){
                if(this.checked){
                    $("#content_shangxian").removeClass('disabled').prop('readonly',false);
                }else{
                    $("#content_shangxian").addClass('disabled').prop('readonly',true);
                }
            });
            form.on('checkbox(if_man)',function(data){
                if(this.checked){
                    $("#content_man").removeClass('disabled').prop('readonly',false);
                }else{
                    $("#content_man").addClass('disabled').prop('readonly',true);
                }
            });
	    	form.on('submit(tijiao)', function(data){
	    		layer.load();
	    	});
	    });
    </script>
    <script type="text/javascript" src="js/users/jifen.js"></script>
    <? require('views/help.html');?>
</body>
</html>