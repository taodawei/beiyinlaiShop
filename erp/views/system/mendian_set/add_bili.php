<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
if($id>0)$level = $db->get_row("select * from zc_release where id=$id ");

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
        	<a href="javascript:history.go(-1);"><img src="images/users_39.png" alt=""/></a> 分润比例
        </div>
    	<div class="addhuiyuandengji">
    		<form action="?s=mendian_set&a=add_bili&submit=1&id=<?=$id?>" method="post" id="submitForm" class="layui-form">
        	<div class="addhuiyuandengji_up">
            	<ul>
            		<li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 业绩最小：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<input type="number" name="min" value="<?=$level->min?>" min="0" step="1" lay-verify="required" class="addhuiyuandengji_right_input" style="width:566px;"/>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 业绩最大：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<input type="number" name="max" value="<?=$level->max?>" min="0" step="1" lay-verify="required" class="addhuiyuandengji_right_input" style="width:566px;"/>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    
                    <li>
                    	<div class="addhuiyuandengji_left">
                        	<span>*</span> 业绩比例：
                        </div>
                    	<div class="addhuiyuandengji_right">
                        	<input type="number" name="bili" value="<?=$level->bili?>" min="0" step="0.01" max="1" lay-verify="required" class="addhuiyuandengji_right_input" style="width:566px;"/>
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