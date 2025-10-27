<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$product_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
$ruku_types = array();
$chuku_types = array();
if(!empty($product_set->ruku_types)){
	$ruku_types = explode('@_@',$product_set->ruku_types);
}
if(!empty($product_set->chuku_types)){
	$chuku_types = explode('@_@',$product_set->chuku_types);
}
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id");
$cangkuOptions = '';
foreach ($cangkus as $ck){
	$cangkuOptions .= '<option value="'.$ck->id.'">'.$ck->title.'</option>';
}
$shenpis = $db->get_results("select * from demo_kucun_shenpi where comId=$comId");
$ruku_shenpis = array();
$chuku_shenpis = array();
$diaobo_shenpis = array();
$caigou_shenpis = array();
$caigou_tuihuo_shenpis = array();
if(!empty($shenpis)){
	foreach ($shenpis as $shenpi) {
		switch ($shenpi->type){
			case 1:
				$ruku_shenpis[] = $shenpi;
			break;
			case 2:
				$chuku_shenpis[] = $shenpi;
			break;
			case 3:
				$diaobo_shenpis[] = $shenpi;
			break;
			case 4:
				$caigou_shenpis[] = $shenpi;
			break;
			case 5:
				$caigou_tuihuo_shenpis[] = $shenpi;
			break;
		}
	}
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
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript">
		var optionstr = '<?=$cangkuOptions?>';
	</script>
	<script type="text/javascript" src="js/kuncun_set.js"></script>
</head>
<body>
	<div class="cangkuguanli_1">
		<div class="cangkuguanli_1_left">
			<img src="images/biao_87.png"> 出入库设置
		</div>
		<div class="clearBoth"></div>
	</div>
	<div class="right_down">
		<div class="churukushezhi">
			<form action="?m=system&s=kucun_set&a=churuku&tijiao=1" id="setForm" class="layui-form" method="post">
				<div class="churukushezhi_01">
					<div class="churukushezhi_01_up">
						<span>单号设置</span>
					</div>
					<div class="churukushezhi_01_down">
						<ul>
							<li>
								<div class="churukushezhi_01_down_1">
									入库单号：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="ruku_pre" value="<?=$product_set->ruku_pre?>" lay-verify="required" placeholder="单号前缀" class="layui-input"/>
								</div>
								<div class="churukushezhi_01_down_2">
									+
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" value="000000" readonly="ture" class="layui-input disabled"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									出库单号：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="chuku_pre" value="<?=$product_set->chuku_pre?>" lay-verify="required" placeholder="单号前缀" class="layui-input"/>
								</div>
								<div class="churukushezhi_01_down_2">
									+
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" value="000000" readonly="ture" class="layui-input disabled"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									调拨单号：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="diaobo_pre" value="<?=$product_set->diaobo_pre?>" lay-verify="required" placeholder="单号前缀" class="layui-input"/>
								</div>
								<div class="churukushezhi_01_down_2">
									+
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" value="000000" readonly="ture" class="layui-input disabled"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									采购单号：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="caigou_pre" value="<?=$product_set->caigou_pre?>" lay-verify="required" placeholder="单号前缀" class="layui-input"/>
								</div>
								<div class="churukushezhi_01_down_2">
									+
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" value="000000" readonly="ture" class="layui-input disabled"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_1">
									采购退货单号：
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" name="caigou_tuihuo_pre" value="<?=$product_set->caigou_tuihuo_pre?>" lay-verify="required" placeholder="单号前缀" class="layui-input"/>
								</div>
								<div class="churukushezhi_01_down_2">
									+
								</div>
								<div class="churukushezhi_01_down_2">
									<input type="text" value="000000" readonly="ture" class="layui-input disabled"/>
								</div>
								<div class="clearBoth"></div>
							</li>
							<li>
								<div class="churukushezhi_01_down_shuoming">
									说明：前缀由管理员自主设置， 000000为自动生成序号位数。
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="churukushezhi_02">
					<div class="churukushezhi_01_up">
						<span>出入库设置</span>
					</div>
					<div class="churukushezhi_02_down">
						<ul>
							<li>
								<div class="churukushezhi_02_down_1">
									<h2>手动入库类型管理</h2>管理您的入库类型，默认为其它入库。
								</div>
								<div class="churukushezhi_02_down_2" id="ruku_rows" rows="<?=count($ruku_types)?>">
									<?
									if(!empty($ruku_types)){
										$i=0;
										foreach ($ruku_types as $ruku){
											$i++;
											?>
											<div id="ruku_rows<?=$i?>">
												<input type="text" name="ruku_types[<?=$i?>]" value="<?=$ruku?>" lay-verify="required" class="layui-input" style="width:" placeholder="填写入库类型"/> <a href="javascript:" onclick="delRow('ruku',<?=$i?>);"><img src="images/chukushezhi_12.gif"/></a><a href="javascript:" onclick="addRow('ruku');"><img src="images/chukushezhi_13.gif"/></a>
											</div>
											<?
										}
									}
									?>
								</div>
							</li>
							<li>
								<div class="churukushezhi_02_down_1">
									<h2>出库类型管理</h2>管理您的出库类型，默认为其它出库。
								</div>
								<div class="churukushezhi_02_down_2" id="chuku_rows" rows="<?=count($chuku_types)?>">
									<?
									if(!empty($chuku_types)){
										$i=0;
										foreach ($chuku_types as $ruku){
											$i++;
											?>
											<div id="chuku_rows<?=$i?>">
												<input type="text" name="chuku_types[<?=$i?>]" value="<?=$ruku?>" lay-verify="required" class="layui-input" style="width:" placeholder="填写出库类型"/> <a href="javascript:" onclick="delRow('chuku',<?=$i?>);"><img src="images/chukushezhi_12.gif"/></a><a href="javascript:" onclick="addRow('chuku');"><img src="images/chukushezhi_13.gif"/></a>
											</div>
											<?
										}
									}
									?>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="churukushezhi_03">
					<div class="churukushezhi_01_up">
						<span>设置审批</span>
					</div>
					<div class="churukushezhi_03_down">
						<ul>
							<li>
								<div class="churukushezhi_03_down_1">
									<input type="checkbox" name="ruku_shenpi" <? if($product_set->ruku_shenpi==1){?>checked<? }?> title="开启入库审批" lay-skin="primary" lay-filter="ruku_shenpi">
								</div>
								<div class="churukushezhi_03_down_2">
									不开启则入库不需要审批
								</div>
								<div class="churukushezhi_03_down_3" id="ruku_shenpi_cont" <? if($product_set->ruku_shenpi==0){?>style="display:none"<? }?> rows="<?=count($ruku_shenpis)?>">
									<? if(!empty($ruku_shenpis)){
										$i= 0;
										foreach ($ruku_shenpis as $shenpi) {
											$i++;
											$options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
											?>
											<div id="ruku_shenpi<?=$i?>">
												<div class="churukushezhi_03_down_3_01">
													设置审批人
												</div>
												<div class="churukushezhi_03_down_3_02">
													<select name="ruku_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
												</div>
												<div class="churukushezhi_03_down_3_03">
													<div class="churukushezhi_03_down_3_03_up" id="ruku_shenpi_user<?=$i?>" onclick="selectSpUser('ruku',<?=$i?>);">
														<?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
													</div>
												</div>
												<div class="churukushezhi_03_down_3_04">
													<a href="javascript:" onclick="addShenpiRow('ruku');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('ruku',<?=$i?>);"><img src="images/biao_66.png"/></a>
												</div>
												<div class="clearBoth"></div>
												<input type="hidden" name="ruku_shenpi_user[<?=$i?>]" id="ruku_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
												<input type="hidden" name="ruku_shenpi_id[<?=$i?>]" id="ruku_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
											</div>
											<?
										}
									}?>
									
								</div>
								<div class="churukushezhi_03_down_2" <? if($product_set->ruku_shenpi==0){?>style="display:none"<? }?>>
									说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则入库时不需要审批
								</div>
							</li>
							<li>
								<div class="churukushezhi_03_down_1">
									<input type="checkbox" name="chuku_shenpi" <? if($product_set->chuku_shenpi==1){?>checked<? }?> title="开启出库审批" lay-skin="primary" lay-filter="chuku_shenpi">
								</div>
								<div class="churukushezhi_03_down_2">
									不开启则出库不需要审批
								</div>
								<div class="churukushezhi_03_down_3" id="chuku_shenpi_cont" <? if($product_set->chuku_shenpi==0){?>style="display:none"<? }?> rows="<?=count($chuku_shenpis)?>">
									<? if(!empty($chuku_shenpis)){
										$i= 0;
										foreach ($chuku_shenpis as $shenpi) {
											$i++;
											$options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
											?>
											<div id="chuku_shenpi<?=$i?>">
												<div class="churukushezhi_03_down_3_01">
													设置审批人
												</div>
												<div class="churukushezhi_03_down_3_02">
													<select name="chuku_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
												</div>
												<div class="churukushezhi_03_down_3_03">
													<div class="churukushezhi_03_down_3_03_up" id="chuku_shenpi_user<?=$i?>" onclick="selectSpUser('chuku',<?=$i?>);">
														<?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
													</div>
												</div>
												<div class="churukushezhi_03_down_3_04">
													<a href="javascript:" onclick="addShenpiRow('chuku');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('chuku',<?=$i?>);"><img src="images/biao_66.png"/></a>
												</div>
												<div class="clearBoth"></div>
												<input type="hidden" name="chuku_shenpi_user[<?=$i?>]" id="chuku_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
												<input type="hidden" name="chuku_shenpi_id[<?=$i?>]" id="chuku_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
											</div>
											<?
										}
									}?>
									
								</div>
								<div class="churukushezhi_03_down_2" <? if($product_set->chuku_shenpi==0){?>style="display:none"<? }?>>
									说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则出库时不需要审批
								</div>
							</li>
							<li>
								<div class="churukushezhi_03_down_1">
									<input type="checkbox" name="diaobo_shenpi" <? if($product_set->diaobo_shenpi==1){?>checked<? }?> title="开启调拨审批" lay-skin="primary" lay-filter="diaobo_shenpi">
								</div>
								<div class="churukushezhi_03_down_2">
									不开启则调拨不需要审批
								</div>
								<div class="churukushezhi_03_down_3" id="diaobo_shenpi_cont" <? if($product_set->diaobo_shenpi==0){?>style="display:none"<? }?> rows="<?=count($diaobo_shenpis)?>">
									<? if(!empty($diaobo_shenpis)){
										$i= 0;
										foreach ($diaobo_shenpis as $shenpi) {
											$i++;
											$options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
											?>
											<div id="diaobo_shenpi<?=$i?>">
												<div class="churukushezhi_03_down_3_01">
													设置审批人
												</div>
												<div class="churukushezhi_03_down_3_02">
													<select name="diaobo_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
												</div>
												<div class="churukushezhi_03_down_3_03">
													<div class="churukushezhi_03_down_3_03_up" id="diaobo_shenpi_user<?=$i?>" onclick="selectSpUser('diaobo',<?=$i?>);">
														<?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
													</div>
												</div>
												<div class="churukushezhi_03_down_3_04">
													<a href="javascript:" onclick="addShenpiRow('diaobo');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('diaobo',<?=$i?>);"><img src="images/biao_66.png"/></a>
												</div>
												<div class="clearBoth"></div>
												<input type="hidden" name="diaobo_shenpi_user[<?=$i?>]" id="diaobo_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
												<input type="hidden" name="diaobo_shenpi_id[<?=$i?>]" id="diaobo_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
											</div>
											<?
										}
									}?>
									
								</div>
								<div class="churukushezhi_03_down_2" <? if($product_set->diaobo_shenpi==0){?>style="display:none"<? }?>>
									说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则调拨时不需要审批
								</div>
							</li>
							<li>
								<div class="churukushezhi_03_down_1">
									<input type="checkbox" name="caigou_shenpi" <? if($product_set->caigou_shenpi==1){?>checked<? }?> title="开启采购审批" lay-skin="primary" lay-filter="caigou_shenpi">
								</div>
								<div class="churukushezhi_03_down_2">
									不开启则采购不需要审批
								</div>
								<div class="churukushezhi_03_down_3" id="caigou_shenpi_cont" <? if($product_set->caigou_shenpi==0){?>style="display:none"<? }?> rows="<?=count($caigou_shenpis)?>">
									<? if(!empty($caigou_shenpis)){
										$i= 0;
										foreach ($caigou_shenpis as $shenpi) {
											$i++;
											$options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
											?>
											<div id="caigou_shenpi<?=$i?>">
												<div class="churukushezhi_03_down_3_01">
													设置审批人
												</div>
												<div class="churukushezhi_03_down_3_02">
													<select name="caigou_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
												</div>
												<div class="churukushezhi_03_down_3_03">
													<div class="churukushezhi_03_down_3_03_up" id="caigou_shenpi_user<?=$i?>" onclick="selectSpUser('caigou',<?=$i?>);">
														<?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
													</div>
												</div>
												<div class="churukushezhi_03_down_3_04">
													<a href="javascript:" onclick="addShenpiRow('caigou');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('caigou',<?=$i?>);"><img src="images/biao_66.png"/></a>
												</div>
												<div class="clearBoth"></div>
												<input type="hidden" name="caigou_shenpi_user[<?=$i?>]" id="caigou_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
												<input type="hidden" name="caigou_shenpi_id[<?=$i?>]" id="caigou_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
											</div>
											<?
										}
									}?>
									
								</div>
								<div class="churukushezhi_03_down_2" <? if($product_set->caigou_shenpi==0){?>style="display:none"<? }?>>
									说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则采购时不需要审批
								</div>
							</li>
							<li>
								<div class="churukushezhi_03_down_1">
									<input type="checkbox" name="caigou_tuihuo_shenpi" <? if($product_set->caigou_tuihuo_shenpi==1){?>checked<? }?> title="开启采购退货审批" lay-skin="primary" lay-filter="caigou_tuihuo_shenpi">
								</div>
								<div class="churukushezhi_03_down_2">
									不开启则采购退货不需要审批
								</div>
								<div class="churukushezhi_03_down_3" id="caigou_tuihuo_shenpi_cont" <? if($product_set->caigou_tuihuo_shenpi==0){?>style="display:none"<? }?> rows="<?=count($dcaigou_tuihuo_shenpis)?>">
									<? if(!empty($caigou_tuihuo_shenpis)){
										$i= 0;
										foreach ($caigou_tuihuo_shenpis as $shenpi) {
											$i++;
											$options = str_replace('value="'.$shenpi->storeId.'"','value="'.$shenpi->storeId.'" selected="selected"',$cangkuOptions);
											?>
											<div id="caigou_tuihuo_shenpi<?=$i?>">
												<div class="churukushezhi_03_down_3_01">
													设置审批人
												</div>
												<div class="churukushezhi_03_down_3_02">
													<select name="caigou_tuihuo_shenpi_store[<?=$i?>]"><option value="0">所有仓库</option><?=$options?></select>
												</div>
												<div class="churukushezhi_03_down_3_03">
													<div class="churukushezhi_03_down_3_03_up" id="caigou_tuihuo_shenpi_user<?=$i?>" onclick="selectSpUser('caigou_tuihuo',<?=$i?>);">
														<?=empty($shenpi->username)?'未设置审批人':$shenpi->username?>
													</div>
												</div>
												<div class="churukushezhi_03_down_3_04">
													<a href="javascript:" onclick="addShenpiRow('caigou_tuihuo');"><img src="images/biao_65.png"/></a> <a href="javascript:" onclick="delShenpiRow('caigou_tuihuo',<?=$i?>);"><img src="images/biao_66.png"/></a>
												</div>
												<div class="clearBoth"></div>
												<input type="hidden" name="caigou_tuihuo_shenpi_user[<?=$i?>]" id="caigou_tuihuo_shenpi_user_<?=$i?>" value="<?=empty($shenpi->userId)?0:$shenpi->userId.'|'.$shenpi->username?>">
												<input type="hidden" name="caigou_tuihuo_shenpi_id[<?=$i?>]" id="caigou_tuihuo_shenpi_id_<?=$i?>" value="<?=$shenpi->id?>">
											</div>
											<?
										}
									}?>
									
								</div>
								<div class="churukushezhi_03_down_2" <? if($product_set->caigou_tuihuo_shenpi==0){?>style="display:none"<? }?>>
									说明：可按仓库设置指定审批人，不选择仓库默认为所有仓库，如果某仓库没有设置审批人而且没有设置所有仓库的审批人，则采购退货时不需要审批
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div class="churukushezhi_04">
					<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="tijiao" > 保 存 </button>
				</div>
			</form>
		</div>
	</div>
	<div id="myModal" class="reveal-modal">
      <div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>
    </div>
    <? require('views/help.html');?>
</body>
</html>