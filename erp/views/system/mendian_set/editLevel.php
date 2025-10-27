<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if($id>0)$level = $db->get_row("select * from user_level where id=$id and comId=$comId");
$user_shezhi = $db->get_row("select if_fixed_zhekou,fixed_zhekou from user_shezhi where comId=$comId");
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/mendianshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="yueshezhi">
    	<div class="yueshezhi_up">
        	<a href="javascript:history.go(-1);"><img src="images/users_39.png" alt=""/></a> 新增会员等级
        </div>
    	<div class="addhuiyuandengji">
    		<form action="?s=mendian_set&a=editLevel&submit=1&id=<?=$id?>" method="post" id="submitForm" class="layui-form">
        	<div class="addhuiyuandengji_up">
            	<ul>
            		<li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 会员级别名称：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<input type="text" name="title" value="<?=$level->title?>" lay-verify="required" class="addhuiyuandengji_right_input" style="width:566px;"/>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 等级升级条件：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<span>累计业绩达到</span><input type="number" name="jifen" value="<?=$level->jifen?>" lay-verify="required" class="addhuiyuandengji_right_input"/>
                        </div>
                        <div class="addhuiyuandengji_right">
                        	注：如果二次变动，不影响客户已有等级！
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    
                    <li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 邀请代理人数：
                        </div>
                    	<div class="addhuiyuandengji_right">
                            <input type="number" name="yq_num" value="<?=$level->yq_num?>" lay-verify="required" class="addhuiyuandengji_right_input"/>
                        </div>
                        <div class="addhuiyuandengji_right">
                        	
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 奖励金额：
                        </div>
                    	<div class="addhuiyuandengji_right">
                            <input type="number" name="price" value="<?=$level->price?>" lay-verify="required" class="addhuiyuandengji_right_input"/>
                        </div>
                        <div class="addhuiyuandengji_right">
                        	
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li style="display:none;">
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 会员等级折扣：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<input type="text" name="zhekou" <? if($user_shezhi->if_fixed_zhekou==1){?> value="<?=$user_shezhi->fixed_zhekou?>" readonly="true" class="addhuiyuandengji_right_input disabled"<?}else{?> value="<?=$level->zhekou?>"  class="addhuiyuandengji_right_input"<? }?>/>
                        </div>
                        <div class="addhuiyuandengji_right">
                        	注：0-10之间，0和10为不打折
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="addhuiyuandengji_left">
                        	使用须知：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<div class="addhuiyuandengji_right_xuzhi">
                            	<textarea name="content" placeholder="最多可输入200个字，简述相关等级信息，以便会员知晓。" maxlength="200"><?=$level->content?></textarea>
                            </div>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
            	</ul>
            </div>
        	<div class="yueshezhi_down_03">
            	<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
  				<button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
            </div>
        	</form>
        </div>
    </div>
    <script type="text/javascript">
    	layui.use(['form'], function(){
    		var form = layui.form;
	    	form.verify({
	    		zhekou:function(value,item){
	    			value = parseFloat(value);
	    			if(isNaN(value)||value<0||value>10){
	    				return '字段不能小于0或大于10';
	    			}
	    		}
	    	});
	    	form.on('submit(tijiao)', function(data){
	    		layer.load();
	    	});
	    });
    </script>
    <? require('views/help.html');?>
</body>
</html>