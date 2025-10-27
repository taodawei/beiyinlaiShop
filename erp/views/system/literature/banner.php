<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$channelId = (int)$request['channelId'];
$banners = $db->get_results("select * from banner where channelId=$channelId and comId=$comId order by ordering desc,id asc");

$positionArr = ['', 'PC', '手机', '小程序'];
foreach($banners as $k => $banner){
    $banners[$k]->position = $positionArr[$banner->position];
}
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
			location.href='?m=system&s=banner&a=del_banner&channelId=<?=$channelId?>&id='+id;
		}
	</script>
</head>
<body>
	<div class="right_up">
    	<img src="images/biao_131.png"/> 广告图片列表
    </div>
	<div class="right_down">
    	<div class="yx_guanggaofabu">
        	<div class="yx_guanggaofabu_01">
            	<div class="splist_up_01_right">                      
                    <div class="splist_up_01_right_3">
                        <a href="?m=system&s=banner&a=addBanner&channelId=<?=$channelId?>" class="splist_add">新 增</a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            	<div class="clearBoth"></div>
            </div>
        	<div class="yx_guanggaofabu_02">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                	<tr height="43">
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            广告名称
                        </td>
                    	<td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	广告图片
                        </td>
                    	<td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	广告位置
                        </td>
                        <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                        	发布时间
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
                    				<a href="<?=$banner->originalPic?>" target="_blank"><img src="<?=$banner->originalPic?>" height="50"></a>
                    			</td>
                    		    <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<?=$banner->position?>
                    			</td>	
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<?=$banner->dtTime?>
                    			</td>
                    			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                    				<a href="?m=system&s=banner&a=topBanner&id=<?=$banner->id?>&channelId=<?=$channelId?>"><img src="images/biao_135.png"/> 置顶</a>
                    				<a href="?m=system&s=banner&a=addBanner&id=<?=$banner->id?>&channelId=<?=$channelId?>"><img src="images/biao_136.png"/> 修改</a>
                    				<a href="javascript:" onclick="z_confirm('确定要删除该广告吗？',del_banner,<?=$banner->id?>);"><img src="images/biao_137.png"/> 删除</a>
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