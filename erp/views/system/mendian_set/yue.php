<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user_shezhi = $db->get_row("select if_yue_tixian,jifen_yue,jifen_yue_num,jifen_yue_limit from user_shezhi where comId=$comId");
if(!empty($user_shezhi->jifen_content)){
    $content = json_decode($user_shezhi->jifen_content,true);
}
$rows = 1;
if(!empty($content['items']))$rows = count($content['items']);
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
    </style>
</head>
<body>
	<div class="yueshezhi">
        <div class="yueshezhi_up">
            <img src="images/mdsz_12.png" alt=""/>  余额设置
        </div>
        <div class="yueshezhi_down">
            <form action="?s=mendian_set&a=yue&submit=1" method="post" class="layui-form">
                <div class="yueshezhi_down_01"> 
                    <ul>
                        <li>
                            <div class="yueshezhi_down_01_left">
                                余额提现    
                            </div>
                            <div class="yueshezhi_down_01_right">
                                <input type="checkbox" lay-skin="switch" <? if($user_shezhi->if_yue_tixian==1){?>checked="true"<? }?> name="if_yue_tixian" value="1" lay-text="开启|关闭">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="yueshezhi_down_01_left">
                                积分转余额   
                            </div>
                            <div class="yueshezhi_down_01_right">
                                <input type="checkbox" lay-skin="switch" <? if($user_shezhi->jifen_yue==1){?>checked="true"<? }?> name="jifen_yue" value="1" lay-text="开启|关闭" lay-filter="jifen_yue">
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
                <div class="yueshezhi_down_02" <? if($user_shezhi->jifen_yue==0){?>style="display:none;"<? }?>>
                    <ul>
                        <li>
                            <input type="text" name="jifen_yue_num" lay-verify="shuzi" value="<?=$user_shezhi->jifen_yue_num?>" /> 积分可转换1元余额
                        </li>
                        <li>
                            每日转余额上限  <input type="text" name="jifen_yue_limit" lay-verify="shuzi" value="<?=$user_shezhi->jifen_yue_limit?>"/> 
                        </li>
                    </ul>
                </div>
                <div class="yueshezhi_down_03">
                    <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                </div>
            </form>
        </div>
    </div>
    <script type="text/javascript">
    	layui.use(['form'], function(){
    		var form = layui.form;
	    	form.verify({
	    		shuzi:function(value,item){
	    			value = parseInt(value);
	    			if(isNaN(value)||value<0){
	    				return '字段必须是大于0的正整数';
	    			}
	    		}
	    	});
            form.on('switch(jifen_yue)',function(data){
                if(this.checked){
                    $(".yueshezhi_down_02").show(10);
                }else{
                    $(".yueshezhi_down_02").hide(10);
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