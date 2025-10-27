<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
// if(is_file("../cache/channels_$comId.php")){
// 	$cache = 1;
// 	$content = file_get_contents("../cache/channels_$comId.php");
// 	$channels = json_decode($content);
// }
$channels = null;
if(empty($channels))$channels = $db->get_results("select * from web_links where comId=$comId and parentId=0 order by ordering desc,id asc");
$showArry = array();
$id = (int)$request['id'];
if(!empty($id)){
	$pids = getParentIds($id);
	if(!empty($pids))$showArry=explode(',',$pids);
}

// var_dump($arr);die;
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
	<script type="text/javascript" src="js/product_channel.js?v=1"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> 底部导航
			</div>
			<div class="jiliangdanwei_up_right">
			    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=mendian_set&a=addLink" onclick="edit_link(0,0,\'\',\'\',\'\');">+ 新 增</a>') ?>
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
							$channels1 = $db->get_results("select * from web_links where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
						}
						if(!empty($channels1)){
							$ifdele = 0;
						}else{
							$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and channelId=$c->id limit 1");
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
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=mendian_set&a=addLink" onclick="edit_link(0,'.$c->id.',\'\',\'\',\'\');"><img src="images/biao_57.png"/> 新增子导航</a>') ?>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=mendian_set&a=addLink" onclick="edit_link('.$c->id.',0,\''.$c->title.'\',\''.$c->originalPic.'\',\''.$c->links.'\',\''.$c->is_hot.'\',\''.$c->type.'\',\''.$c->remark.'\');"><img src="images/biao_49.png"/> 修改</a>') ?>
									<? chekurl($arr,'<a href="?m=system&s=mendian_set&a=moveLink&type=1&id='.$c->id.'"><img src="images/biao_33.png"/> 向上</a>') ?>
									<? chekurl($arr,'<a href="?m=system&s=mendian_set&a=moveLink&type=0&id='.$c->id.'"><img src="images/biao_34.png"/>向下</a>') ?>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=product_channel&a=delChannel" '.($ifdele==1?'onclick="z_confirm(\'确定要删除“'.$c->title.'”导航吗？\',delLinks,'.$c->id.');"':'style="color:#999;opacity:.7"').'><img src="images/biao_48.png"/> 删除</a>') ?>
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
												$channels2 = $db->get_results("select * from web_links where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
											}
											if(!empty($channels2)){
												$ifdele = 0;
											}else{
												$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and channelId=$c1->id limit 1");
												$ifdele = empty($ifhas)?1:0;
											}
											?>
											<li data-id="<?=$c1->id?>" data-pid="<?=$c->id?>">
												<div class="shangpinguanli_01">
													<div class="shangpinguanli_01_left">
														<span class="shangpinguanli_01_left_0<? if(empty($channels2)){echo '2';}else{if(in_array($c1->id,$showArry)){echo '3';}else{echo '1';}}?>"></span> <?=$c1->title?>
													</div>
													<div class="shangpinguanli_01_right">
														链接地址：/index.php?p=4&channelId=<?=$c1->id?>
														<a href="javascript:" onclick="edit_link(0,<?=$c1->id?>,'','','');"><img src="images/biao_57.png"/> 新增子导航</a>
														<a href="javascript:" onclick="edit_link(<?=$c1->id?>,<?=$c->id?>,'<?=$c1->title?>','<?=$c1->originalPic?>','<?=$c1->links?>','<?=$c1->is_hot?>','<?=$c1->type?>','<?=$c1->remark?>');"><img src="images/biao_49.png"/> 修改</a><a href="?m=system&s=mendian_set&a=moveLink&type=1&id=<?=$c1->id?>"><img src="images/biao_33.png"/> 向上</a><a href="?m=system&s=mendian_set&a=moveLink&type=0&id=<?=$c1->id?>"><img src="images/biao_34.png"/>向下</a><a href="?m=system&s=product_channel&a=tohot&id=<?=$c->id?>"><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c1->title?>”导航吗？',delLinks,<?=$c1->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
													</div>
													<div class="clearBoth"></div>
												</div>
												<?
												if(!empty($channels2)){
													?>
													<div class="shangpinguanli_03" pid="<?=$c1->id?>" <? if(in_array($c1->id,$showArry)){?>style="display:block"<? }?>>
														<ul>
															<?
															foreach ($channels2 as $c2) {
																$ifdele = 1;
																if($cache==1){
																	$channels3 = $c2->channels;
																}else{
																	$channels3 = $db->get_results("select * from web_links where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
																}
																if(!empty($channels3)){
																	$ifdele = 0;
																}else{
																	$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and channelId=$c2->id limit 1");
																	$ifdele = empty($ifhas)?1:0;
																}
																?>
																<li data-id="<?=$c2->id?>" data-pid="<?=$c1->id?>">
																	<div class="shangpinguanli_01">
																		<div class="shangpinguanli_01_left">
																			<span class="shangpinguanli_01_left_0<? if(empty($channels3)){echo '2';}else{if(in_array($c2->id,$showArry)){echo '3';}else{echo '1';}}?>"></span> <?=$c2->title?>
																		</div>
																		<div class="shangpinguanli_01_right">
																			链接地址：/index.php?p=4&channelId=<?=$c2->id?>
																			<!--<a href="javascript:" onclick="edit_link(0,<?=$c2->id?>,'','');"><img src="images/biao_57.png"/> 新增子导航</a>-->
																			<a href="javascript:" onclick="edit_link(<?=$c2->id?>,<?=$c1->id?>,'<?=$c2->title?>','<?=$c2->originalPic?>','<?=$c2->links?>','<?=$c2->is_hot?>','<?=$c2->type?>','<?=$c2->remark?>');"><img src="images/biao_49.png"/> 修改</a><a href="?m=system&s=mendian_set&a=moveLink&type=1&id=<?=$c2->id?>"><img src="images/biao_33.png"/> 向上</a><a href="?m=system&s=mendian_set&a=moveLink&type=0&id=<?=$c2->id?>"><img src="images/biao_34.png"/>向下</a><a href="?m=system&s=product_channel&a=tohot&id=<?=$c->id?>"><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c2->title?>”导航吗？',delLinks,<?=$c2->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
																		</div>
																		<div class="clearBoth"></div>
																	</div>
																	<?
																	if(!empty($channels3)){
																		?>
																		<div class="shangpinguanli_04" pid="<?=$c2->id?>" <? if(in_array($c2->id,$showArry)){?>style="display:block"<? }?>>
																			<ul>
																				<?
																				foreach ($channels3 as $c3) {
																					$ifhas = $db->get_var("select id from demo_product_inventory where comId=$comId and channelId=$c3->id limit 1");
																					$ifdele = empty($ifhas)?1:0;
																					?>
																					<li data-id="<?=$c3->id?>" data-pid="<?=$c2->id?>">
																						<div class="shangpinguanli_01">
																							<div class="shangpinguanli_01_left">
																								<span class="shangpinguanli_01_left_02"></span> <?=$c3->title?>
																							</div>
																							<div class="shangpinguanli_01_right">
																								链接地址：/index.php?p=4&channelId=<?=$c3->id?>
																								<a href="javascript:" onclick="edit_link(<?=$c3->id?>,<?=$c2->id?>,'<?=$c3->title?>','<?=$c3->originalPic?>','<?=$c3->links?>','<?=$c3->is_hot?>','<?=$c3->type?>','<?=$c3->remark?>');"><img src="images/biao_49.png"/> 修改</a><a href="?m=system&s=mendian_set&a=moveLink&type=1&id=<?=$c3->id?>"><img src="images/biao_33.png"/> 向上</a><a href="?m=system&s=mendian_set&a=moveLink&type=0&id=<?=$c3->id?>"><img src="images/biao_34.png"/>向下</a><a href="?m=system&s=product_channel&a=tohot&id=<?=$c->id?>"><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c3->title?>”导航吗？',delLinks,<?=$c3->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
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
																?>
															</ul>
														</div>
														<?
													}
												?>
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
	<script type="text/javascript" charset="utf-8">
    function edit_link(id,pid,title,imgurl,links,is_hot = 0, type=0, remark =''){
    	if (type == 0){
    		var selectType = '<option value="0" selected>默认</option><option value="1">首页列表</option>';
    	}else if(type == 1){
    		var selectType = '<option value="0">默认</option><option value="1" selected>首页列表</option>';
    	}else{
    		var selectType = '<option value="0">默认</option><option value="1">首页列表</option>';
    	}
    	if (is_hot == 0){
    		var selectHot = '<option value="0" selected>否</option><option value="1">是</option><';
    	}else{
    		var selectHot = '<option value="0">否</option><option value="1">是</option><';
    	}
    
    	layer.load();
    	ajaxpost=$.ajax({
    		type: "POST",
    		url: "/erp_service.php?action=get_web_links",
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
    		        ,content: '<div class="spxx_shanchu_tanchu" style="display: block;height: 400px;">'+
    					'<form action="?m=system&s=mendian_set&a=addLink&id='+id+'" method="post" id="channelForm"><div class="spxx_shanchu_tanchu_01">'+
    				    	'<div class="spxx_shanchu_tanchu_01_left">'+
    				        	(id==0?'新增':'修改')+'商品导航'+
    				        '</div>'+
    				    	'<div class="spxx_shanchu_tanchu_01_right">'+
    				        	'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
    				        '</div>'+
    				    	'<div class="clearBoth"></div>'+
    				    '</div>'+
    					'<div class="spxx_shanchu_tanchu_02" style="height:210px;">'+
    				    	'<div class="jiliang_tanchu">'+
    				        	'<span>*</span> 所属导航 '+
    				            '<select name="parentId"><option value="0">顶级导航</option>'+resdata+'</select>'+
    				            '<br>'+
    				            '<span>*</span> 导航名称 <input type="text" name="title" id="channel_title" value="'+title+'"><br>'+
    
    				            '<span>&nbsp;</span> 导航图标 <img src="'+(imgurl==''?'images/add.jpg':imgurl)+'" style="margin-left:10px;cursor:pointer;height:50px" id="channel_img"> &nbsp;<a href="javascript:" onclick="del_channel_img();">删除</a><br>'+
    				            '<span>*</span> 链接地址 <input type="text" name="links" id="channel_title" value="'+links+'"><br>'+
    		
    				        '</div>'+
    				    '</div>'+
    				    '<input type="hidden" name="originalPic" id="channel_imgurl" value="'+imgurl+'" />'+
    				    // '<input type="hidden" name="backimg" id="backimg" value="'+backimg+'" />'+
    				'</form></div>'
    		        ,success: function(layero){
    		        	layupload.render({
    					    elem: '#channel_img'
    					    ,url: '?m=system&s=upload&a=upload&limit_width=no'
    					    ,before:function(){
    					    	layer.load();
    					    }
    					    ,done: function(res){
    					      layer.closeAll('loading');
    					      if(res.code > 0){
    					      	return layer.msg(res.msg);
    					      }else{
    					      	$("#channel_img").attr("src",res.url);
    					      	$("#channel_imgurl").val(res.url);
    					      }
    					  	}
    					  	,error: function(){
    					  		layer.msg('上传失败，请重试', {icon: 5});
    					  	}
    					});
    					
    					layupload.render({
    					    elem: '#channel_backimg'
    					    ,url: '?m=system&s=upload&a=upload&limit_width=no'
    					    ,before:function(){
    					    	layer.load();
    					    }
    					    ,done: function(res){
    					      layer.closeAll('loading');
    					      if(res.code > 0){
    					      	return layer.msg(res.msg);
    					      }else{
    					      	$("#channel_backimg").attr("src",res.url);
    					      	$("#backimg").val(res.url);
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
    
    function delLinks(id){
    	layer.closeAll();
    	layer.load();
    	ajaxpost=$.ajax({
    		type: "POST",
    		url: "?m=system&s=mendian_set&a=delLink",
    		data: "&id="+id,
    		dataType:"json",
    		timeout : 20000,
    		success: function(resdata) {
    			layer.closeAll('loading');
    			if(resdata.code==0){
    				layer.msg(resdata.message, {icon: 5});
    			}else{
    				var ids = resdata.ids.split(',');
    				for(i=0;i<ids.length;i++){
    					$(".shangpinguanli li[data-id='"+ids[i]+"']").remove();
    				}
    			}
    		},
    		error: function() {
    			layer.closeAll('loading');
    			layer.msg('超时，数据请求失败', {icon: 5});
    		}
    	});	
    }
	</script>
	<? require('views/help.html');?>
</body>
</html>