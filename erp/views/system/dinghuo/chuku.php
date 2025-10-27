<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
if(is_file("../cache/kucun_set_$comId.php")){
	$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
}else{
	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
}
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$k = $db->get_row("select title,level,storeId from demo_kehu where id=$jilu->kehuId");
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
if(!empty($jilu->shouhuoInfo)){
	$shouhuoInfo = json_decode($jilu->shouhuoInfo,true);
}
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
$jilus = $db->get_results("select * from demo_kucun_jilu$fenbiao where comId=$comId and dinghuoId=$id order by id desc");
$chukuJilus = array();
$zuofeiJilus = array();
if(!empty($jilus)){
	foreach ($jilus as $j) {
		if($j->status==-2){
			$zuofeiJilus[] = $j;
		}else{
			$chukuJilus[] = $j;
		}
	}
}
$liucheng = getLiucheng();
$fahuo_store = $db->get_row("select areaId,address,name,phone from demo_kucun_store where id=$j->storeId");
$fahuo_address = getAreaName($fahuo_store->areaId).$fahuo_store->address;
$fahuo_name = $fahuo_store->name;
$fahuo_phone = $fahuo_store->phone;
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>订单详细</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		#riqi1 .layui-laydate{border-right:0px;}
		#riqi2 .layui-laydate{border-left:0px;}
	</style>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>"><img src="images/biao_63.png"/> 订单详情</a>
	</div>
	<div class="purchase_xiang" style="background:#fff">
		<div class="purchase_class">
			<div class=""><a href="?m=system&s=dinghuo&a=detail&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">订单详情</a></div>
			<div class="purchase_order"><a href="javascript:">出库发货记录</a></div>
			<div><a href="?m=system&s=dinghuo&a=shoukuan&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">收款记录</a></div>
		</div>
	</div>
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="dhd_chukufahuojl_01">	
				<span><?
				switch ($jilu->status) {
					case 0:
					$status = '订单待审核';
					break;
					case 1:
					$status = '待财务审核';
					break;
					case 2:
					$status = '待出库';
					break;
					case 3:
					$status = '待出库审核';
					break;
					case 4:
					$status = '待发货';
					break;
					case 5:
					$status = '待收货';
					break;
					case 6:
					$status = '已完成';
					break;
					case -1:
					$status = '已作废';
					break;
				}
				echo $status;
				?></span> &nbsp;&nbsp; 订单号：<?=$jilu->orderId?><? if($jilu->orderType==2){?>&nbsp;<div class="" style="display: inline-block;position:static;"><div class="sub-tag" style="background-color:#03a9f3;color:#fff;padding:0px 2px">代下单</div></div><? }?>     &nbsp;&nbsp;    <?=$k->title?>（<?=$db->get_var("select title from demo_kehu_level where id=$k->level");?>） <br>
				收货信息：<?=$shouhuoInfo['company']?> ， <?=$shouhuoInfo['name']?> ， <?=$shouhuoInfo['phone']?> ， <?=$shouhuoInfo['address']?>
			</div>
			<? if($jilu->status>1&&$jilu->chukuStatus<2){?>
			<div class="dhd_chukufahuojl_02">
				<div class="dhd_chukufahuojl_02_up">
					<div class="dhd_chukufahuojl_02_up_left">
						<img src="images/biao_124.png"> 待出库商品清单
					</div>
					<div class="dhd_chukufahuojl_02_up_right">
						<span class="dhd_chukufahuojl_02_up_right_01"></span>
					</div>
					<div class="clearBoth"></div>
				</div>
				<form action="?m=system&s=dinghuo&a=addChuku&jiluId=<?=$jilu->id?>&kehuStore=<?=$jilu->storeId?>" id="chukuForm" class="layui-form">
					<div class="dhd_chukufahuojl_02_down">
						<div class="dhd_chukufahuojl_02_down_01">
							<div class="dhd_chukufahuojl_02_down_01_left">
								出库仓库：<span style="width:150px;display:inline-block;margin-right:20px;"><select id="storeId" name="storeId" lay-filter="storeId">
									<?
									foreach ($cangkus as $c){
										?><option value="<?=$c->id?>" <? if($c->id==$jilu->storeId){?>selected="selected"<? }?>><?=$c->title?></option><?
									}
									?>
								</select></span>
								<input type="checkbox" id="ifkucun" name="ifkucun" title="仅显示库存大于0的商品" lay-skin="primary" lay-filter="ifkucun">
							</div>
							<? 
							if($liucheng['if_chuku']==1){
								if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'chuku')){?>
								<div class="dhd_chukufahuojl_02_down_01_right">
									本次出库数设为 0 表示此商品暂不出库 <a href="javascript:" onclick="chuku();" class="dhd_chukufahuojl_chuku">出库</a>
								</div>
								<? }
							}else if($liucheng['if_fahuo']==1){
								if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'fahuo')){?>
								<div class="dhd_chukufahuojl_03_down_01 noprint" style="display:inline-block;float:right;">
									<a href="javascript:" class="dhd_chukufahuojl_03_down_01_fahuo">发货</a>
									<div class="dhd_chukufahuojl_03_down_01_fahuo_erji" style="display:none;">
										<a href="javascript:chuku_fahuo(1,0,'','','');" class="dhd_chukufahuojl_putongfahuo">普通发货</a>
										<a href="javascript:chuku_fahuo(2,0,'<?=$fahuo_address?>','<?=$fahuo_name?>','<?=$fahuo_phone?>');" class="dhd_chukufahuojl_dianzifahuo">电子面单发货</a>
									</div>
								</div>
								<? }
							}?>
							<div class="clearBoth"></div>
						</div>
						<div class="dhd_chukufahuojl_02_down_02">
							<table id="product_list" lay-filter="product_list"></table>
						</div>
					</div>
				</form>
			</div>
			<? }
			if(!empty($chukuJilus)){
			?>
			<div class="dhd_chukufahuojl_02">
				<div class="dhd_chukufahuojl_02_up">
					<div class="dhd_chukufahuojl_02_up_left">
						　　 出库/发货记录
					</div>
					<div class="dhd_chukufahuojl_02_up_right">
						<span class="dhd_chukufahuojl_02_up_right_01"></span>
					</div>
					<div class="clearBoth"></div>
				</div>
				<div>
					<? foreach($chukuJilus as $j){
						$fahuo = $db->get_row("select * from demo_dinghuo_fahuo where jiluId=$j->id and dinghuoId=$id limit 1");
						if(!empty($fahuo->beizhu)){
							$fahuo->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$fahuo->beizhu);
						}
						$details = $db->get_results("select pdtInfo,units,num,dinghuoId from demo_kucun_jiludetail$fenbiao where jiluId=".$j->id." order by id asc");
					?>
					<div class="dhd_chukufahuojl_03_down" id="print_<?=$j->id?>">
						<div class="dhd_chukufahuojl_03_down_01 noprint">
							<? if(!empty($fahuo)){
								if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'fahuo')||strstr($qx_arry['dinghuo']['functions'],'all')){?>
								<a href="javascript:editFahuo(<?=$j->id?>,<?=$fahuo->id?>,'<?=date("Y-m-d H:i",strtotime($fahuo->fahuoTime))?>','<?=$fahuo->kuaidi_company?>','<?=$fahuo->kuaidi_order?>','<?=$fahuo->beizhu?>');"><img src="images/biao_31.png">修改物流</a>
								<? }
								if($fahuo->type==2&&!empty($fahuo->kuaidi_order)){?>
								<a href="?m=system&s=dinghuo&a=printMiandan&id=<?=$fahuo->id?>" target="_blank"><img src="images/biao_64.png">打印电子面单</a>
								<? }?>
								<a href="?m=system&s=dinghuo&a=print_fahuo&id=<?=$j->id?>&dinghuoId=<?=$id?>" target="_blank"><img src="images/biao_64.png">打印发货单</a>
								<a href="?m=system&s=dinghuo&a=daochuFahuo&id=<?=$j->id?>" target="_blank"><img src="images/biao_126.png">导出</a>
								<? if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'fahuo')||strstr($qx_arry['dinghuo']['functions'],'all')){?>
								<a href="javascript:zuofei_fahuo(<?=$fahuo->id?>);"><img src="images/biao_75.png">作废</a>
								<? }?>
							<? }else{?>
								<a href="?m=system&s=dinghuo&a=print_chuku&id=<?=$j->id?>&dinghuoId=<?=$id?>" target="_blank"><img src="images/biao_64.png">打印出库单</a>
								<a href="?m=system&s=dinghuo&a=daochuChuku&id=<?=$j->id?>" target="_blank"><img src="images/biao_126.png">导出</a>
								<? if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'chuku')||strstr($qx_arry['dinghuo']['functions'],'all')){?>
								<a href="javascript:zuofei(<?=$j->id?>);"><img src="images/biao_75.png">作废</a>
								<? }
								if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'fahuo')||strstr($qx_arry['dinghuo']['functions'],'all')){?>
								<a href="javascript:" class="dhd_chukufahuojl_03_down_01_fahuo">发货</a>
								<div class="dhd_chukufahuojl_03_down_01_fahuo_erji" style="display:none;">
									<a href="javascript:fahuo(1,<?=$j->id?>,'','','');" class="dhd_chukufahuojl_putongfahuo">普通发货</a>
									<a href="javascript:fahuo(2,<?=$j->id?>,'<?=$fahuo_address?>','<?=$fahuo_name?>','<?=$fahuo_phone?>');" class="dhd_chukufahuojl_dianzifahuo">电子面单发货</a>
								</div>
							<? }
							}
							?>
						</div>
						<div class="dhd_chukufahuojl_02_down_02">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tbody><tr height="43">
									<td bgcolor="#cedee6" width="70" align="center" valign="middle">
									</td>
									<td bgcolor="#cedee6" width="270" align="center" valign="middle">
										商品名称
									</td>
									<td bgcolor="#cedee6" width="250" align="center" valign="middle">
										规格
									</td>
									<td bgcolor="#cedee6" align="center" valign="middle">
										商品编码
									</td>
									<td bgcolor="#cedee6" align="center" valign="middle">
										本次出库数
									</td>
									<td bgcolor="#cedee6" align="center" valign="middle">
										重量小计
									</td>
								</tr>
								<? 
								$zongWeight = 0;
								if(!empty($details)){
									foreach ($details as $i=>$d){
										$d->num = abs($d->num);
										$pdtInfo = json_decode($d->pdtInfo,true);
										$weight = $db->get_var("select weight from demo_dinghuo_detail$fenbiao where id=$d->dinghuoId");
										$zongWeight+=$weight*$d->num;
										?>
										<tr id="row_tr<?=$i+1?>">
											<td class="return_xiang_td1"><?=$i+1?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['sn']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['title']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['key_vals']?></td>
											<td class="return_xiang_td1"><?=getXiaoshu($d->num,$product_set->number_num)?><?=$d->units?></td>
											<td class="return_xiang_td1"><?=$weight*$d->num?><?=$product_set->weight?></td>
										</tr>
										<?
									}
								}?>
							</tbody></table>
						</div>
						<div class="dhd_chukufahuojl_03_down_03">
							<? if(!empty($fahuo)){?>状态　　<? switch ($fahuo->status){
								case 1:
									echo '已发货';
								break;
								case 2:
									echo '已完成';
								break;
							}?> 　　   <?=date("Y-m-d H:i",strtotime($fahuo->dtTime))?><br><? }?>
							出库信息　　出库编号：<?=$j->orderId?>　　出库时间：<?=date("Y-m-d H:i",strtotime($j->dtTime))?>　　	出库重量合计：<?=$zongWeight?><?=$product_set->weight?>　	出库仓库：<?=$j->storeName?><br>
							<? if(!empty($fahuo)){?>
							物流信息　　发货日期：<?=date("Y-m-d H:i",strtotime($fahuo->fahuoTime))?>　　　物流公司：<?=$fahuo->kuaidi_company?>　　　　物流单号：<?=$fahuo->kuaidi_order?>　　物流备注：<?=$fahuo->beizhu?>　　　<a <? if(empty($fahuo->kuaidi_order)){?>href="javascript:layer.msg('请先填写物流单号！',function(){});"<?}else if($fahuo->kuaidi_type==1||empty($fahuo->kuaidi_type)){?>href="https://www.kuaidi100.com/chaxun?com=<?=$fahuo->kuaidi_company?>&nu=<?=$fahuo->kuaidi_order?>" target="_blank"<? }else{?>href="javascript:" onclick="viewWuliu(<?=$fahuo->kuaidi_type?>,'<?=$fahuo->kuaidi_company?>','<?=$fahuo->kuaidi_order?>');"<? }?>>物流信息 <img src="images/biao_67.png"></a>
							<? }?>
						</div>
					</div>
					<? }?>
				</div>                	
			</div>
			<? }
			if(!empty($zuofeiJilus)){
			?>
			<div class="dhd_chukufahuojl_02">
				<div class="dhd_chukufahuojl_02_up">
					<div class="dhd_chukufahuojl_02_up_left">
						　<img src="images/biao_125.png"> 已作废记录
					</div>
					<div class="dhd_chukufahuojl_02_up_right">
						<span class="dhd_chukufahuojl_02_up_right_01"></span>
					</div>
					<div class="clearBoth"></div>
				</div>
				<? foreach($zuofeiJilus as $j){
						$fahuo = $db->get_row("select * from demo_dinghuo_fahuo where jiluId=$j->id and dinghuoId=$id limit 1");
						$details = $db->get_results("select pdtInfo,units,num,dinghuoId from demo_kucun_jiludetail$fenbiao where jiluId=".$j->id." order by id asc");
					?>
					<div class="dhd_chukufahuojl_03_down" >
						<div class="dhd_chukufahuojl_02_down_02">
							<table width="100%" border="0" cellpadding="0" cellspacing="0">
								<tbody><tr height="43">
									<td bgcolor="#cedee6" width="70" align="center" valign="middle">
									</td>
									<td bgcolor="#cedee6" width="270" align="center" valign="middle">
										商品名称
									</td>
									<td bgcolor="#cedee6" width="250" align="center" valign="middle">
										规格
									</td>
									<td bgcolor="#cedee6" align="center" valign="middle">
										商品编码
									</td>
									<td bgcolor="#cedee6" align="center" valign="middle">
										本次出库数
									</td>
									<td bgcolor="#cedee6" align="center" valign="middle">
										重量小计
									</td>
								</tr>
								<? 
								$zongWeight = 0;
								if(!empty($details)){
									foreach ($details as $i=>$d){
										$d->num = abs($d->num);
										$pdtInfo = json_decode($d->pdtInfo,true);
										$weight = $db->get_var("select weight from demo_dinghuo_detail$fenbiao where id=$d->dinghuoId");
										$zongWeight+=$weight*$d->num;
										?>
										<tr>
											<td class="return_xiang_td1"><?=$i+1?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['sn']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['title']?></td>
											<td class="return_xiang_td1"><?=$pdtInfo['key_vals']?></td>
											<td class="return_xiang_td1"><?=getXiaoshu($d->num,$product_set->number_num)?><?=$d->units?></td>
											<td class="return_xiang_td1"><?=$weight*$d->num?><?=$product_set->weight?></td>
										</tr>
										<?
									}
								}?>
							</tbody></table>
						</div>
						<div class="dhd_chukufahuojl_03_down_03">
							状态　　已作废<br>
							备注　　<?=$j->shenheCont?><br>
							出库信息　　出库编号：<?=$j->orderId?>　　出库时间：<?=date("Y-m-d H:i",strtotime($j->dtTime))?>　　	出库重量合计：<?=$zongWeight?><?=$product_set->weight?>　	出库仓库：<?=$j->storeName?>
						</div>
					</div>
					<? }?>
				<div class="clearBoth"></div>
			</div>
			<? }?>
		</div>
	</div>
<!--电子面单发货弹出-->
<div class="dhd_chukufahuojl_dianzifahuo_tc" id="dhd_chukufahuojl_dianzifahuo_tc" data-id="0" data-fahuo="0" style="display:none;">
	<form action="?m=system&s=dinghuo&a=fahuo_kuaidiniao" id="fahuo_kuaidiniao_form" method="post" class="layui-form">
	<div class="bjkh_bj"></div>
    <div class="skqr_zf">
    	<div class="bjkh_jebangsjxx_1">
        	电子面单信息确认
        </div>
    	<div class="bjkh_jebangsjxx_2">
        	<div class="dhd_chukufahuojl_dianzifahuo_01">
            	<ul>
            		<li>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_left">
                        	发货快递
                        </div>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_right">
                        	<select name="expressno" id="k_expressno">
                        		<option value="SF,顺丰">顺丰</option>
                        		<option value="EMS,EMS">EMS</option>
                        		<option value="ZJS,宅急送">宅急送</option>
                        		<option value="YTO,圆通">圆通</option>
                        		<option value="HTKY,百世快递">百世快递</option>
                        		<option value="ZTO,中通">中通</option>
                        		<option value="YD,韵达">韵达</option>
                        		<option value="STO,申通">申通</option>
                        		<option value="HHTT,天天快递">天天快递</option>
                        		<option value="YZPY,邮政快递包裹">邮政快递包裹</option>
                        		<option value="DBL,德邦">德邦</option>
                        		<option value="UC,优速">优速</option>
                        		<option value="XFEX,信丰">信丰</option>
                        		<option value="QFKD,全峰">全峰</option>
                        		<option value="KYSY,跨越速运">跨越速运</option>
                        		<option value="ANE,安能小包">安能小包</option>
                        		<option value="FAST,快捷快递">快捷快递</option>
                        		<option value="GTO,国通">国通</option>
                        		<option value="ZTKY,中铁快运">中铁快运</option>
                        		<option value="YZBK,邮政国内标快">邮政国内标快</option>
                        	</select>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_left" style="line-height:18px;">
                        	统一设置<br>商品名称
                        </div>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_right">
                        	<input type="text" name="expressDesc" id="k_expressDesc" /><br>
                            <img src="images/biao_85.png"/> 不统一设置时，各面单的商品名称默认第一个商品名称
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_left" style="line-height:18px;">
                        	发货地址
                        </div>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_right" style="position:relative;top:-8px;">
                        	<span id="k_address" style="color:#555;font-size:14px;"></span><br>
                            <img src="images/biao_85.png"/> 仓库地址不对？<a href="index.php?a=shezhi&url=<?=urlencode('?m=system&s=store')?>" target="_blank" style="color:#0f7eb3;">点此设置</a>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_left">
                        	发货人姓名
                        </div>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_right">
                        	<input type="text" name="name" id="k_name" />
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_left">
                        	发货人电话
                        </div>
                    	<div class="dhd_chukufahuojl_dianzifahuo_01_right">
                        	<input type="text" name="phone" id="k_phone" />
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
            	</ul>
            </div>
        </div>
    	<div class="bjkh_jebangsjxx_3">
        	<a href="javascript:fahuo_kuaidiniao();" class="bjkh_jebangsjxx_3_01">确定</a><a href="javascript:$('#dhd_chukufahuojl_dianzifahuo_tc').hide();" class="bjkh_jebangsjxx_3_02">取消</a>
        </div>
    </div>
</form>
</div>
<!--电子面单发货弹出结束-->
<!--出库弹出-->
<div class="dhd_chukufahuojl_chuku_tc" id="dhd_chukufahuojl_chuku_tc" style="display:none;">
	<div class="bjkh_bj"></div>
    <div class="skqr_zf">
    	<div class="bjkh_jebangsjxx_1">
        	出库审批
        </div>
    	<div class="bjkh_jebangsjxx_2">
        	<div class="dh_ckfhjl_chuku_zu">
            	出库仓库：<span id="chuku_storeName"></span><br>
                商品种类：<span id="chuku_categorys"></span><br>
                商品数量：<span id="chuku_nums"></span>
            </div>
        </div>
    	<div class="bjkh_jebangsjxx_3">
        	<a href="javascript:dochuku();" class="bjkh_jebangsjxx_3_01">确定</a><a href="javascript:" onclick="$('#dhd_chukufahuojl_chuku_tc').hide();" class="bjkh_jebangsjxx_3_02">取消</a>
        </div>
    </div>
</div>
<!--出库弹出-->
<!--物流信息-->
<div class="dhd_chukufahuojl_chuku_tc" id="dhd_chukufahuojl_wuliu_tc" style="display:none;">
	<div class="bjkh_bj"></div>
    <div class="skqr_zf">
    	<div class="bjkh_jebangsjxx_1">
        	物流信息
        </div>
    	<div class="bjkh_jebangsjxx_2">
        	<div class="dh_ckfhjl_chuku_zu" style="text-align:left">
            	
            </div>
        </div>
    	<div class="bjkh_jebangsjxx_3">
        	<a href="javascript:" onclick="$('#dhd_chukufahuojl_wuliu_tc').hide();" class="bjkh_jebangsjxx_3_01">确定</a>
        </div>
    </div>
</div>
<!--出库弹出-->
<!--发货弹出-->
<div class="dhd_chukufahuojl_fahuoxinxi" id="dhd_chukufahuojl_fahuoxinxi" data-id="0" data-fahuo="0">
	<div class="dd_addshoukuan1">
        <div class="dd_addshoukuan_01">	
        	 发货信息
        </div>
        <div class="dhd_chukufahuojl_fahuoxinxi_02">
        	如确认已经发货，请填写发货信息： <img src="images/biao_85.png"/> <span>如果您是通过电子面单发货，推荐您发货后，通过“打印电子面单”功能获取物流单号</span>
        </div>      
        <div class="dd_addshoukuan_04">
        	<div class="dd_addshoukuan_04_down">
        		<form action="?m=system&s=dinghuo&a=addFahuo" id="fahuoForm">
            	<div class="dd_addshoukuan_04_down_left">
                	<ul>
                		<li>
                        	<div class="dd_addshoukuan_04_down_left_01">
                            	发货日期
                            </div>
                        	<div class="dd_addshoukuan_04_down_left_02">	
                            	<input type="text" name="fahuo_time" id="fahuo_time" value="<?=date("Y-m-d H:i")?>" class="dd_addshoukuan_04_down_left_02_input"/>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="dd_addshoukuan_04_down_left_01">
                            	物流公司
                            </div>
                        	<div class="dd_addshoukuan_04_down_left_02">
                            	<input type="text" name="fahuo_company" id="fahuo_company" placeholder="请输入物流公司" class="dd_addshoukuan_04_down_left_02_input"/>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                        <li>
                        	<div class="dd_addshoukuan_04_down_left_01">
                            	物流单号
                            </div>
                        	<div class="dd_addshoukuan_04_down_left_02">
                            	<input type="text" name="fahuo_order" id="fahuo_order" placeholder="请输入物流单号" class="dd_addshoukuan_04_down_left_02_input"/>
                            </div>
                        	<div class="clearBoth"></div>
                        </li>
                	</ul>
                </div>
            	<div class="dd_addshoukuan_04_down_right">
                	<div class="dd_addshoukuan_04_down_right_up">
                    	<div class="dd_addshoukuan_04_down_right_up_left">
                        	备注
                        </div>
                    	<div class="dd_addshoukuan_04_down_right_up_right">
                        	<textarea name="fahuo_beizhu" id="fahuo_beizhu"></textarea>
                        </div>
                    	<div class="clearBoth"></div>
                    </div>                	
                </div>
            	</form>
            	<div class="clearBoth"></div>
            </div>
        </div>
        <div class="dd_addshoukuan_05">
        	<a href="javascript:dofahuo(1);" class="kh_gjsousuo_04_1">确定</a><a href="javascript:hidefahuo(1);" class="kh_gjsousuo_04_2">取消</a>
        </div>
    </div>
</div>
<!--发货弹出结束-->
<input type="hidden" id="jiluId" value="<?=$jilu->id?>">
<script type="text/javascript">
		var productListTalbe;
		var productListForm;
		layui.use(['laydate','laypage','table','form'], function(){
			var laydate = layui.laydate
			,table = layui.table
			,form = layui.form
			productListForm = form;
			laydate.render({
				elem: '#fahuo_time'
				,max:'<?=date("Y-m-d H:i:s")?>'
				,type: 'datetime'
				,format: 'yyyy-MM-dd HH:mm'
			});
			<? if($jilu->status>1&&$jilu->chukuStatus<2){?>
			layer.load();
			productListTalbe = table.render({
				elem: '#product_list'
				,url: '?m=system&s=dinghuo&a=getChukuPdts&kehuStore=<?=$jilu->storeId?>'
				,page: false
				,cols: [[{field:'sn',title:'商品编码',width:150},{field:'title',title:'商品名称',width:200,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:200,style:"height:auto;line-height:22px;white-space:normal;"},{field:'kucun',title:'库存数量',width:100},{field:'shuliang',title:'订购数量',width:70},{field:'units',title:'单位',width:60},{field:'weight',title:'重量小计',width:70},{field:'hasNum',title:'已出库数',width:70},{field:'chuku',title:'本次出库数',width:120}]]
				,where: {
					jiluId:<?=$jilu->id?>
					,storeId:<?=$jilu->storeId?>
				}
				,done: function(res, curr, count){
					layer.closeAll('loading');
				}
			});
			form.on('select(storeId)',function(){
				reloadTable();
			});
			form.on('checkbox(ifkucun)',function(){
				reloadTable();
			});
			<? }?>
		});
	</script>
<script type="text/javascript" src="js/dinghuo_chuku.js"></script>
<div id="bg"></div>
<? require('views/help.html');?>
</body>
</html>