<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user_shezhi = $db->get_row("select if_qiandao,qiandao_rule from user_shezhi where comId=$comId");
if(!empty($user_shezhi->qiandao_rule)){
    $content = json_decode($user_shezhi->qiandao_rule,true);
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
        .layui-form-radio span{font-size:16px}
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
                        <a href="?s=mendian_set&a=jifen_jiazhi">价值管理</a>
                    </li>
                    <li>
                        <a href="javascript:" class="jifenguize_up_on">每日签到</a>
                    </li>
                    <li>
                        <!--<a href="?s=mendian_set&a=jifen_share">分享积分/抵扣金</a>-->
                    </li>
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="jifenguize_down">
                <div class="jifenguize_down_01">
                    <div class="meiriqiandao">
                        <div class="jiaoyijifen_up_1">
                            用户积分 签到获取
                        </div>
                        <form action="?s=mendian_set&a=jifen_qiandao&submit=1" method="post" class="layui-form">
                            <div class="meiriqiandao_01">
                                <input type="checkbox" lay-skin="switch" <? if($user_shezhi->if_qiandao==1){?>checked="true"<? }?> lay-filter="if_qiandao" name="if_qiandao" value="1" lay-text="开启|关闭">
                            </div>
                            <div class="meiriqiandao_02" <? if($user_shezhi->if_qiandao==0){?>style="display:none;"<?}?>>
                                <ul>
                                    <li class="jiaoyijifen_up_2_li <? if($content['type']==1){echo 'on';}?>">
                                        <div class="meiriqiandao_02_up">
                                            <input lay-filter="type1" value="1" <? if($content['type']==1){?>checked="true"<? }?> type="radio" name="content[type]" title="每日固定积分" /> 
                                        </div>
                                        <div class="meiriqiandao_02_down" <? if($content['type']!=1){?>style="display:none"<? }?>>
                                            每日签到一次，签到积分  <input type="text" name="content[jifen]" lay-verify="shuzi" value="<?=$content['jifen']?>" />
                                        </div>
                                    </li>
                                    <li style="display:none;" class="jiaoyijifen_up_2_li <? if($content['type']==2){echo 'on';}?>">
                                        <div class="meiriqiandao_02_up">
                                            <input lay-filter="type2" value="2" <? if($content['type']==2){?>checked="true"<? }?> type="radio" name="content[type]" title="累计签到模式"/> 
                                        </div>
                                        <div class="meiriqiandao_02_down" <? if($content['type']!=2){?>style="display:none"<? }?>>
                                            首次签到积分  <input type="text" name="content[first]" lay-verify="shuzi" value="<?=$content['first']?>"/> 连续签到每日额外增加积分  <input type="text" name="content[leijia]" lay-verify="shuzi" value="<?=$content['leijia']?>"/> 连续签到上限天数  <input type="text" name="content[day]" lay-verify="shuzi" value="<?=$content['day']?>"/>  天
                                        </div>
                                        <div class="meiriqiandao_02_3">
                                            * 比如：第一天签到5分，第二天签到5+1分，持续7天、第7天为5+6*1=11分；后续保持11分，不会再增加 
                                            <br>* 一旦中断持续签到，则从第一天重新计算
                                            <br><span>* 只有在【累计签到模式】下，才会累计签到天数，切换为【每日固定积分】后，用户点击签到，则会将之前的累计天数清零</span> 
                                        </div>
                                    </li>
                                </ul>
                            </div>
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
	    		}
	    	});
            form.on('radio(type1)',function(){
                $(".meiriqiandao_02_down").hide();
                $(".meiriqiandao_02_down").eq(0).show(100);
                $(".jiaoyijifen_up_2_li").removeClass("on");
                $(".jiaoyijifen_up_2_li").eq(0).addClass("on");
            });
            form.on('radio(type2)',function(){
                $(".meiriqiandao_02_down").hide();
                $(".meiriqiandao_02_down").eq(1).show(100);
                $(".jiaoyijifen_up_2_li").removeClass("on");
                $(".jiaoyijifen_up_2_li").eq(1).addClass("on");
            });
            form.on('switch(if_qiandao)',function(data){
                if(this.checked){
                    $(".meiriqiandao_02").show();
                }else{
                    $(".meiriqiandao_02").hide();
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