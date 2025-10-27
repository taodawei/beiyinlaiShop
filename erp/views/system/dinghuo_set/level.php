<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$levels = $db->get_results("select * from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/dinghuo_level.js"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> <?=$kehu_title?>级别
			</div>
			<div class="jiliangdanwei_up_right">
				<a href="javascript:" onclick="edit_channel(0,'','');">+ 新 增</a>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="shangpinguanli" style="padding-top:20px">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
            	<tr height="49">
                	<td class="kehujibieshezhi_down_title" bgcolor="#d7ebf5" valign="middle" align="left">	
                    	级别名称
                    </td>
                    <td class="kehujibieshezhi_down_title" bgcolor="#d7ebf5" valign="middle" align="left">	
                    	订货折扣
                    </td>
                    <td class="kehujibieshezhi_down_title" bgcolor="#d7ebf5" valign="middle" align="left">	
                    	操作
                    </td>
                </tr>
                <? foreach($levels as $level){
                	$ifhas = $db->get_var("select id from demo_kehu where comId=$comId and level=$level->id limit 1");
                	?>
                	<tr height="49" data-id="<?=$level->id?>">
	                	<td class="kehujibieshezhi_down_tt" bgcolor="#ffffff" valign="middle" align="left">	
	                    	<?=$level->title?><? if($level->del==0){?>（系统默认，不可删除）<? }?>
	                    </td>
	                    <td class="kehujibieshezhi_down_tt" bgcolor="#ffffff" valign="middle" align="left">	
	                    	<?=$level->zhekou?>%
	                    </td>
	                    <td class="kehujibieshezhi_down_tt" bgcolor="#ffffff" valign="middle" align="left">	
	                    	<a href="javascript:" onclick="edit_channel(<?=$level->id?>,'<?=$level->title?>','<?=$level->zhekou?>');" class="kehujibieshezhi_xiugai">修改</a><a href="?m=system&s=dinghuo_set&a=totop&id=<?=$level->id?>">置顶</a><? if($level->del==1){?><a href="javascript:" <? if(empty($ifhas)){?>onclick="z_confirm('确定要删除“<?=$level->title?>”级别吗？',delChannel,<?=$level->id?>);"<? }else{?>style="color:#999;opacity:.7" onmouseover="tips(this,'已有该级别的客户,不能删除',1);" onmouseout="hideTips();";<? }?>>删除</a><? }?>
	                    </td>
	                </tr>
                <? }?>
            </table>
		</div>
	</div>
	<? require('views/help.html');?>
</body>
</html>