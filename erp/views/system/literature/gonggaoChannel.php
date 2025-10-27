<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$channels = $db->get_results("select * from demo_list_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
$showArry = array();
$id = (int)$request['id'];
if(!empty($id)){
	$pids = getParentIds($id);
	if(!empty($pids))$showArry=explode(',',$pids);
}
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
	<script type="text/javascript" src="js/shezhi/list_channel.js"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> 资讯分类管理
			</div>
			<div class="jiliangdanwei_up_right">
			    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=banner&a=addGonggaoChannel" onclick="edit_channel_new(0,0,\'\',\'\',\'\');">+ 新 增</a>') ?>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="shangpinguanli" style="padding-top:20px">
			<ul>
				<?
				if(!empty($channels)){
					foreach ($channels as $c){
						$ifdele = 1;
						if($cache==1){
							$channels1 = $c->channels;
						}else{
							$channels1 = $db->get_results("select * from demo_list_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
						}
						if(!empty($channels1)){
							$ifdele = 0;
						}else{
							$ifhas = $db->get_var("select id from demo_list where comId=$comId and channelId=$c->id limit 1");
							$ifdele = empty($ifhas)?1:0;
						}
						?>
						<li data-id="<?=$c->id?>" data-pid="0">
							<div class="shangpinguanli_01">
								<div class="shangpinguanli_01_left">
									<span class="shangpinguanli_01_left_0<? if(empty($channels1)){echo '2';}else{if(in_array($c->id,$showArry)){echo '3';}else{echo '1';}}?>"></span> <?=$c->title?>
								</div>
								<div class="shangpinguanli_01_right">
								    链接地址：/index.php?p=4&channelId=<?=$c->id?>
									 <a href="javascript:" onclick="edit_channel_new(0,<?=$c->id?>,'','','');"><img src="images/biao_57.png"/> 新增子类</a>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=banner&a=addGonggaoChannel" onclick="edit_channel_new('.$c->id.',0,\''.$c->title.'\',\''.$c->en_title.'\',\''.$c->originalPic.'\');"><img src="images/biao_49.png"/> 修改</a>') ?>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=banner&a=dellistChannel" '.($ifdele==1?'onclick="z_confirm(\'确定要删除“'.$c->title.'”分类吗？\',delChannel,'.$c->id.');"':'style="color:#999;opacity:.7">').'<img src="images/biao_48.png"/> 删除</a>') ?>
								</div>
								<div class="clearBoth"></div>
							</div>
							<?
							if(!empty($channels1)){
								?>
								<div class="shangpinguanli_02" pid="<?=$c->id?>" <? if(in_array($c->id,$showArry)){?>style="display:block"<? }?>>
									<ul>
										<?
										foreach ($channels1 as $c1) {
											$ifdele = 1;
											if($cache==1){
												$channels2 = $c1->channels;
											}else{
												$channels2 = $db->get_results("select * from demo_list_channel where comId=10 and parentId=".$c1->id." order by ordering desc,id asc");
											}
											if(!empty($channels2)){
												$ifdele = 0;
											}else{
												$ifhas = $db->get_var("select id from demo_list where comId=10 and channelId=$c1->id limit 1");
												$ifdele = empty($ifhas)?1:0;
											}
											?>
											<li data-id="<?=$c1->id?>" data-pid="<?=$c->id?>">
												<div class="shangpinguanli_01">
													<div class="shangpinguanli_01_left">
														<?=$c1->title?>
													</div>
													<div class="shangpinguanli_01_right">
													    链接地址：/index.php?p=4&channelId=<?=$c1->id?>
														<a href="javascript:" onclick="edit_channel_new(<?=$c1->id?>,<?=$c->id?>,'<?=$c1->title?>','<?=$c1->en_title?>','<?=$c1->originalPic?>');"><img src="images/biao_49.png"/> 修改</a><a href="?m=system&s=pdts_channel&a=totop&id=<?=$c1->id?>"><img src="images/biao_50.png"/> 置顶</a><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c1->title?>”分类吗？',delChannel,<?=$c1->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
													</div>
													<div class="clearBoth"></div>
												</div>
											</li>
											<?
										}
										?>
									</ul>
								</div>
								<?
							}
							?>
						</li>
						<?
					}
				}
				?>
			</ul>
		</div>
	</div>
	<script>
	    function edit_channel_new(id,pid,title,en_title,img){
    	layer.load();
    	ajaxpost=$.ajax({
    		type: "POST",
    		url: "/erp_service.php?action=get_pdts_channels",
    		data: "&id="+id+"&pid="+pid,
    		dataType:"text",timeout : 8000,
    		success: function(resdata) {
    			layer.closeAll('loading');
    			layer.open({
    		        type: 1
    		        ,title: false //不显示标题栏
    		        ,closeBtn: false
    		        ,area: '530px;'
    		        ,shade: 0.3
    		        ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
    		        ,btn: ['提交', '取消']
    		        ,yes: function(index, layero){
    		        	return false;
    				}
    		        ,btnAlign: 'r'
    		        ,zIndex: layer.zIndex
    		        ,success: function(layero){
    		        	layer.setTop(layero);
    		        }
    		        ,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
    					'<form action="?m=system&s=banner&a=addGonggaoChannel&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
    				    	'<div class="spxx_shanchu_tanchu_01_left">'+
    				        	(id==0?'新增':'修改')+'资讯分类'+
    				        '</div>'+
    				    	'<div class="spxx_shanchu_tanchu_01_right">'+
    				        	'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
    				        '</div>'+
    				    	'<div class="clearBoth"></div>'+
    				    '</div>'+
    					'<div class="spxx_shanchu_tanchu_02" style="height:216px">'+
    				    	'<div class="jiliang_tanchu">'+
    				        	'<span>*</span> 所属分类 '+
    				            '<select name="parentId"><option value="0">顶级分类</option>'+resdata+'</select>'+
    				            '<br>'+
    				            '<span>*</span> 中文名称 <input type="text" name="title" id="channel_title" value="'+title+'"><br>'+
    				            '<span>*</span> 英文名称 <input type="text" name="en_title" value="'+en_title+'"><br>'+
    				            '<span>&nbsp;</span> 分类图片 <img id="upload_channel_img" src="'+(img.length>5?img:'images/mrtp.gif')+'" width="70" height="70" style="margin-left:12px;margin-top:10px;cursor:pointer">&nbsp;&nbsp;<font color="red">建议上传尺寸120*120，大小不超过200K</font><br>'+
    				        '</div>'+
    				    '<input type="hidden" name="originalPic" value="'+img+'" id="upload_originalPic"></div>'+
    				'</form></div>'
    		        ,success: function(layero){
    		        	lay_upload.render({
    		        		elem: '#upload_channel_img'
    		        		,url: '?m=system&s=upload&a=upload&width=120&height=120'
    		        		,before:function(){
    		        			layer.load();
    		        		}
    		        		,done: function(res){
    		        			layer.closeAll('loading');
    		        			if(res.code > 0){
    		        				return layer.msg(res.msg);
    		        			}else{
    		        				$("#upload_originalPic").val(res.url);
    		        				$("#upload_channel_img").attr("src",res.url);
    		        			}
    		        		}
    		        		,error: function(){
    		        			layer.msg('上传失败，请重试', {icon: 5});
    		        		}
    		        	});
    		        	var btn = layero.find('.layui-layer-btn');
    		        	btn.find('.layui-layer-btn0').attr({
    		        		href: 'javascript:checkChannelForm();'
    		        	});
    		        	return false;
    		        }
    		      });
    		},
    		error: function() {
    			layer.closeAll('loading');
    			layer.msg('数据请求失败', {icon: 5});
    		}
    	});	
    }
	</script>
	
	<? require('views/help.html');?>
</body>
</html>