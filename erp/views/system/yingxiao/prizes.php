<?
global $db,$request;
$id = (int)$request['dazhuanpan_id'];
$title = $db->get_var("select title from demo_dazhuanpan where id=$id");
$sql = "select * from demo_dazhuanpan_prize where dazhuanpan_id=$id";
$sb = new sqlbuilder('list',$sql,'ordering desc,id desc',$db,10);
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>奖品管理</title>
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
		function del_gift(id){
			layer.load();
			location.href='?m=system&s=yingxiao&a=del_gift&id='+id+'&dazhuanpan_id=<?=$id?>';
		}
        function jinyong(id){
            layer.load();
            location.href='?m=system&s=yingxiao&a=jinyong&id='+id+'&dazhuanpan_id=<?=$id?>';
        }
        function qiyong(id){
            layer.load();
            location.href='?m=system&s=yingxiao&a=qiyong&id='+id+'&dazhuanpan_id=<?=$id?>';
        }
	</script>
    <style type="text/css">
    .zzp-page .zzp-operations{
        float:left;
    }

    .zzp-page .zzp-right {
        width:370px;/*解决小分辨率下错位问题*/
        float:right;
    }
    .zzp-page .checkbox{
        overflow:hidden;
    }
    .zzp-page .checkbox em,.zzp-page .checkbox label{
        float:left;
    }
    .zzp-page .checkbox em{
        margin-top:5px;
        margin-right:3px;
    }
    .zzp-btn25{
        display:inline-block;
        padding-right:2px;  
        height:25px;
        margin-right:5px;
        overflow:hidden;
    }
    .pager a,.pager span{margin-right:10px;}
    </style>
</head>
<body>
	<div class="right_up">
    	<a href="?s=yingxiao&a=dazhuanpan"><img src="images/back.gif"/></a> <?=$title?> - 奖品管理
    </div>
	<div class="right_down">
    	<div class="yx_guanggaofabu">
        	<div class="yx_guanggaofabu_01">
                <div class="splist_up_01_left" style="line-height:60px;">
                每个活动奖品最多设置10个
                </div>
            	<div class="splist_up_01_right">
                    <div class="splist_up_01_right_3">
                        <a href="?m=system&s=yingxiao&a=addGift&dazhuanpan_id=<?=$id?>"  class="splist_add">新 增</a>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            	<div class="clearBoth"></div>
            </div>
            <form id="search_form" name="form1" method="post" action="?s=yingxiao&a=ordering_gift">
            	<div class="yx_guanggaofabu_02">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="43">
                        	<td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                            	标题
                            </td>
                            <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                                概率
                            </td>
                            <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                                数量
                            </td>
                            <td bgcolor="#d7ebf5" class="yx_guanggaofabu_02_title" align="left" valign="middle">
                                操作
                            </td>
                        </tr>
                        <? 
                        if(!empty($sb->results)){
                            foreach($sb->results as $k =>$v){
                        		?>
                        		<tr height="43">
                        			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle" <? if($v['is_stop']==1){?>style="color:#888;"<? }?>>
                        				<?=$v['name']?>
                        			</td>
                                    <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle" <? if($v['is_stop']==1){?>style="color:#888;"<? }?>>
                                        <?=$v['chance']/100;?>%
                                    </td>
                                    <td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle" <? if($v['is_stop']==1){?>style="color:#888;"<? }?>>
                                        <?=$v['num']?>
                                    </td>
                        			<td bgcolor="#ffffff" class="yx_guanggaofabu_02_tt" align="left" valign="middle">
                        				<? if($v['status']==1){?>
                                        <a href="javascript:" onclick="z_confirm('确定要禁用？',jinyong,<?=$v['id']?>);"><img src="images/biao_136.png"/> 禁用</a>
                                        <a href="?m=system&s=yingxiao&a=addGift&id=<?=$v['id']?>"><img src="images/biao_136.png"/> 修改</a>
                        				<a href="javascript:" onclick="z_confirm('确定要删除该信息吗？',del_gift,<?=$v['id']?>);"><img src="images/biao_137.png"/> 删除</a>
                                        <? }else{?>
                                        <a href="javascript:" onclick="z_confirm('确定要启用？',qiyong,<?=$v['id']?>);"><img src="images/biao_136.png"/> 启用</a>
                                        <? }?>
                        			</td>
                        		</tr>
                        		<? }
                            }
                        ?>
                    </table>
                </div>
            </form>
        </div>
        <div class="zzp-page" style="margin-top:15px;padding-left:2px;padding-bottom:5px;height:20px;">
            <ul class="zzp-operations" style="display: none;">
                <li class="edit">
                    <div class="batch-pop" style="position:relative;margin-left:10px;float:left;">
                        <button onclick="javascript:document.getElementById('search_form').submit();" class="layui-laypage-btn" style="width:70px;">排序</button>
                    </div>
                </li>
            </ul>
            <div class="zzp-right" style="width:420px;">
               <ul class="pager" >
                 <? echo $sb->get_pager_show();?>
             </ul></div>  <div class="clear-both"></div>
         </div>
     </div>
    <? require('views/help.html');?>
</body>
</html>