<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
// if(is_file("../cache/channels_$comId.php")){
// 	$cache = 1;
// 	$content = file_get_contents("../cache/channels_$comId.php");
// 	$channels = json_decode($content);
// }
$channels = null;
if(empty($channels))$channels = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
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
	<script type="text/javascript" src="js/recharge_channel.js?v=1"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> 兑换分类管理
			</div>
			<div class="jiliangdanwei_up_right">
			    <? chekurl($arr,'<a href="javascript:" _href="?m=system&s=recharge_channel&a=addProductChannel" onclick="edit_channel(0,0,\'\',\'\',0,\'\');">+ 新 增</a>') ?>
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
							$channels1 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c->id." order by ordering desc,id asc");
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
								<div class="shangpinguanli_01_left" <?if($c->is_hot) echo 'style="color:red;"'?> >
									<span class="shangpinguanli_01_left_0<? if(empty($channels1)){echo '2';}else{if(in_array($c->id,$showArry)){echo '3';}else{echo '1';}}?>" ></span> <?=$c->title?>
								</div>
								<div class="shangpinguanli_01_right">
									链接地址：/index.php?p=4&channelId=<?=$c->id?>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=recharge_channel&a=addProductChannel" onclick="edit_channel(0,'.$c->id.',\'\',\'\',\'\');"><img src="images/biao_57.png"/> 新增子类</a>') ?>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=recharge_channel&a=addProductChannel" onclick="edit_channel('.$c->id.',0,\''.$c->title.'\',\''.$c->originalPic.'\',\''.$c->backimg.'\',\''.$c->is_hot.'\',\''.$c->type.'\',\''.$c->remark.'\');"><img src="images/biao_49.png"/> 修改</a>') ?>
									<? chekurl($arr,'<a href="?m=system&s=product_channel&a=move&type=1&id='.$c->id.'"><img src="images/biao_33.png"/> 向上</a>') ?>
									<? chekurl($arr,'<a href="?m=system&s=product_channel&a=move&type=0&id='.$c->id.'"><img src="images/biao_34.png"/>向下</a>') ?>
									<? chekurl($arr,'<a href="javascript:" _href="?m=system&s=change_channel&a=delChannel" '.($ifdele==1?'onclick="z_confirm(\'确定要删除“'.$c->title.'”分类吗？\',delChannel,'.$c->id.');"':'style="color:#999;opacity:.7"').'><img src="images/biao_48.png"/> 删除</a>') ?>
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
												$channels2 = $db->get_results("select * from demo_recharge_channel where comId=$comId and parentId=".$c1->id." order by ordering desc,id asc");
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
													<div class="shangpinguanli_01_left" <?if($c1->is_hot) echo 'style="color:red;"'?>>
														<span class="shangpinguanli_01_left_0<? if(empty($channels2)){echo '2';}else{if(in_array($c1->id,$showArry)){echo '3';}else{echo '1';}}?>"></span> <?=$c1->title?>
													</div>
													<div class="shangpinguanli_01_right">
														链接地址：/index.php?p=4&channelId=<?=$c1->id?>
														<a href="javascript:" onclick="edit_channel(0,<?=$c1->id?>,'','','');"><img src="images/biao_57.png"/> 新增子类</a>
														<a href="javascript:" onclick="edit_channel(<?=$c1->id?>,<?=$c->id?>,'<?=$c1->title?>','<?=$c1->originalPic?>','<?=$c1->backimg?>','<?=$c1->is_hot?>','<?=$c1->type?>','<?=$c1->remark?>');"><img src="images/biao_49.png"/> 修改</a>
														<!--<a href="?m=system&s=product_channel&a=move&type=1&id=<?=$c1->id?>"><img src="images/biao_33.png"/> 向上</a>-->
														<!--<a href="?m=system&s=product_channel&a=move&type=0&id=<?=$c1->id?>"><img src="images/biao_34.png"/>向下</a>-->
														<a href="?m=system&s=product_channel&a=tohot&id=<?=$c->id?>"><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c1->title?>”分类吗？',delChannel,<?=$c1->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
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
																	$channels3 = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=".$c2->id." order by ordering desc,id asc");
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
																			<!--<a href="javascript:" onclick="edit_channel(0,<?=$c2->id?>,'','');"><img src="images/biao_57.png"/> 新增子类</a>-->
																			<a href="javascript:" onclick="edit_channel(<?=$c2->id?>,<?=$c1->id?>,'<?=$c2->title?>','<?=$c2->originalPic?>','<?=$c2->backimg?>','<?=$c2->is_hot?>','<?=$c2->type?>','<?=$c2->remark?>');"><img src="images/biao_49.png"/> 修改</a>
																			<!--<a href="?m=system&s=product_channel&a=move&type=1&id=<?=$c2->id?>"><img src="images/biao_33.png"/> 向上</a>-->
																			<!--<a href="?m=system&s=product_channel&a=move&type=0&id=<?=$c2->id?>"><img src="images/biao_34.png"/>向下</a>-->
																			<a href="?m=system&s=product_channel&a=tohot&id=<?=$c->id?>"><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c2->title?>”分类吗？',delChannel,<?=$c2->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
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
																								<a href="javascript:" onclick="edit_channel(<?=$c3->id?>,<?=$c2->id?>,'<?=$c3->title?>','<?=$c3->originalPic?>','<?=$c3->backimg?>','<?=$c3->is_hot?>','<?=$c3->type?>','<?=$c3->remark?>');"><img src="images/biao_49.png"/> 修改</a><a href="?m=system&s=product_channel&a=move&type=1&id=<?=$c3->id?>"><img src="images/biao_33.png"/> 向上</a><a href="?m=system&s=product_channel&a=move&type=0&id=<?=$c3->id?>"><img src="images/biao_34.png"/>向下</a><a href="?m=system&s=product_channel&a=tohot&id=<?=$c->id?>"><a href="javascript:" <? if($ifdele==1){?>onclick="z_confirm('确定要删除“<?=$c3->title?>”分类吗？',delChannel,<?=$c3->id?>);"<? }else{?>style="color:#999;opacity:.7"<? }?>><img src="images/biao_48.png"/> 删除</a>
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
	<? require('views/help.html');?>
</body>
</html>