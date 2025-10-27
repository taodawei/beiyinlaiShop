<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$lists = $db->get_results("select * from roles order by id asc");
$count = count($lists);
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery-3.1.1.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">
		function del_admin(id){
			layer.load();
			location.href='?m=system&s=adminlist&a=del_roles&id='+id;
		}
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_131.png"/> 角色列表
    </div>
	<div class="right_down">
    	<div class="yx_guanggaofabu">
        	<div class="yx_guanggaofabu_01">
            	<div class="splist_up_01_right">                      
                    <div class="splist_up_01_right_3">
                        <? chekurl($arr,'<a href="?m=system&s=adminlist&a=addroles&id=0&type=0" class="splist_add">新 增</a>') ?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            	<div class="clearBoth"></div>
            </div>
        	<div class="yx_guanggaofabu_02">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                	<tr height="43">
                    	<td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	角色名称
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	创建时间
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	备注
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            操作
                        </td>
                    </tr>
                    <? 
                    if(!empty($lists)){
                    	foreach($lists as $list){
                    		?>
                    		<tr height="43">
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<?=$list->name?>
                    			</td>
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<?=date('Y-m-d H:i:s',$list->dtTime)?>
                    			</td>
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<?=$list->mark?>
                    			</td>
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    			    <? chekurl($arr,'<a href="?m=system&s=adminlist&a=addroles&id='.$list->id.'&type=0"><img src="images/biao_136.png"/> 修改</a>') ?>
                    			    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=adminlist&a=del_roles" onclick="z_confirm(\'确定要删除该角色吗？\',del_admin,'.$list->id.');"><img src="images/biao_137.png"/> 删除</a>') ?>
                    			</td>
                    		</tr>
                    		<? }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</body>
</html>