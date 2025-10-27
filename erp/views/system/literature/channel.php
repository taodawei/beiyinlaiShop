<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$banners = $db->get_results("select * from banner_channel where comId=$comId order by ordering desc,id asc");
$count = count($banners);
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
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">
		function del_banner(id){
			layer.load();
			location.href='?m=system&s=banner&a=del_banner_channel&id='+id;
		}
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_131.png"/> 首页自定义模块
    </div>
	<div class="right_down">
    	<div class="yx_guanggaofabu">
        	<div class="yx_guanggaofabu_01">
            	<div class="splist_up_01_right">
                    <div class="splist_up_01_right_3">
                        <? chekurl($arr,'<a href="?m=system&s=banner&a=addChannel" class="splist_add">新 增</a>') ?>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            	<div class="clearBoth"></div>
            </div>
        	<div class="yx_guanggaofabu_02">
                <form id="search_form" name="form1" method="post" action="?s=banner&a=ordering_channel">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                	<tr height="43">
                    	<td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	模块标题
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            副标题
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            是否显示标题
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            每行几个图片
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            排序(倒序)
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            操作
                        </td>
                    </tr>
                    <? 
                    if(!empty($banners)){
                    	foreach($banners as $banner){
                    		?>
                    		<tr height="43">
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<?=$banner->title?>
                    			</td>
                                <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                                    <?=$banner->remark?>
                                </td>
                                <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                                    <?=$banner->show_title==1?'显示':'不显示'?>
                                </td>
                                <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                                    <?=$banner->shuliang?>
                                </td>
                                <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                                    <input name="ordering[<? echo $banner->id?>]" type="number" value="<? echo  $banner->ordering?>" class="layui-input" style="width:70px;" />
                                </td>
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    			    <? chekurl($arr,'<a href="?m=system&s=banner&a=banner&channelId='.$banner->id.'"><img src="images/biao_136.png"/> 管理图片</a>') ?>
                    			    <? chekurl($arr,'<a href="?m=system&s=banner&a=addChannel&id='.$banner->id.'"><img src="images/biao_136.png"/> 修改</a>') ?>
                    			    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=banner&a=del_banner_channel" onclick="z_confirm(\'确定要删除该模块吗？\',del_banner,'.$banner->id.');"><img src="images/biao_137.png"/> 删除</a>') ?>
                                    
                    			</td>
                    		</tr>
                    		<? }
                    }
                    ?>
                </table>
                </form>
                <div class="zzp-page" style="margin-top:15px;padding-left:2px;padding-bottom:5px;height:20px;">
                    <ul class="zzp-operations">
                        <li class="edit">
                            <div class="batch-pop" style="position:relative;margin-left:10px;float:left;">
                                <button onclick="javascript:document.getElementById('search_form').submit();" class="layui-btn" style="width:70px;">排序</button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html>