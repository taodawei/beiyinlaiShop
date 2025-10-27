<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$k = $db->get_row("select title,level from demo_kehu where id=$jilu->kehuId");
$jiluDetails = $db->get_results("select * from demo_dinghuo_detail$fenbiao where jiluId=".(int)$jilu->id.' order by id asc');
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
if(!empty($jilu->beizhu)){
	$beizhus = json_decode($jilu->beizhu,true);
}
if(!empty($jilu->shouhuoInfo)){
	$shouhuoInfo = json_decode($jilu->shouhuoInfo,true);
}
if(!empty($jilu->fapiaoInfo)){
	$fapiaoInfo = json_decode($jilu->fapiaoInfo,true);
}
if(!empty($jilu->fujianInfo)){
	$files = explode('|',$jilu->fujianInfo);
}
$liucheng = getLiucheng();
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
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>"><img src="images/biao_63.png"/> 订单详情</a>
	</div>
	<div class="purchase_xiang" style="background:#fff">
		<div class="purchase_class">
			<div class="purchase_order"><a href="javascript:">订单详情</a></div>
			<? if($jilu->status>1&&($liucheng['if_chuku']==1||$liucheng['if_fahuo']==1)){?>
			<div class="storage"><a href="?m=system&s=dinghuo&a=chuku&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">出库发货记录</a></div>
			<? }?>
			<div><a href="?m=system&s=dinghuo&a=shoukuan&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">收款记录</a></div>
		</div>
		<div class="state_right spchukuxiangxi_01_right_right">
			<? if($jilu->status==0&&($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'shenhe')||strstr($qx_arry['dinghuo']['functions'],'all'))){?>
				<a href="javascript:" onclick="bohui(<?=$id?>);" class="spchukuxiangxi_01_right_right_02" style="float:right;">审批驳回</a>
				<a href="javascript:" onclick="tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01" style="float:right;">审批通过</a>
			<? }else if($jilu->status==3&&($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'chuku')||strstr($qx_arry['dinghuo']['functions'],'all'))){?>
				<a href="javascript:" onclick="bohui(<?=$id?>);" class="spchukuxiangxi_01_right_right_02" style="float:right;">审批驳回</a>
				<a href="javascript:" onclick="tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01" style="float:right;">审批通过</a>
			<? }else if($jilu->status==2){?>
			<a href="?m=system&s=dinghuo&a=chuku&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>" class="spchukuxiangxi_01_right_right_01" style="float:right;">出库</a>
			<? }?>
			<div class="derive">
				<div><a href="?m=system&s=dinghuo&a=daochu&id=<?=$id?>" target="_blank" style="width:auto;margin-right:0px;"><img src="images/derive.gif">导出</a></div>
				<div><a href="javascript:doPrint();location.reload();" style="width:auto;margin-right:0px;"><img src="images/print2.gif">打印</a></div>
			</div>
		</div>
	</div>
	<!--startprint-->
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="sprukuxiangxi_01">	
				<div class="sprukuxiangxi_01_left">
					<span style="color:#ff4747;font-size:18px;"><?
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
					?></span>
					<span>订货单号：<?=$jilu->orderId?><? if($jilu->orderType==2){?>&nbsp;<div class="" style="display: inline-block;position:static;"><div class="sub-tag" style="background-color:#03a9f3;color:#fff;padding:0px 2px">代下单</div></div><? }?></span>
					<span><?=$kehu_title?>名称：<?=$k->title?>【<?=$db->get_var("select title from demo_kehu_level where id=$k->level");?>】</span>
					<span>业务员：<?=$jilu->yewuyuan?></span>
					<span>日期：<?=date("Y-m-d H:i",strtotime($jilu->dtTime))?></span>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div class="sprukuxiangxi_02">
				<table width="100%" border="0" cellpadding="0" cellspacing="0">
					<tr height="43">
						<td class="sprukuxiangxi_02_title" width="167" bgcolor="#7bc8ed" valign="middle" align="center"> 
							编码
						</td>
						<td class="sprukuxiangxi_02_title" width="592" bgcolor="#7bc8ed" valign="middle" align="center"> 
							商品
						</td>
						<td class="sprukuxiangxi_02_title" width="258" bgcolor="#7bc8ed" valign="middle" align="center"> 
							规格
						</td>
						<td class="sprukuxiangxi_02_title" width="103" bgcolor="#7bc8ed" valign="middle" align="center"> 
							单位
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							数量
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							单价
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							重量小计
						</td>
						<td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							小计
						</td>
						<td class="sprukuxiangxi_02_title" width="85" bgcolor="#7bc8ed" valign="middle" align="center"> 
							备注
						</td>
					</tr>
					<?
					$heji = 0;$zongNum=0;$zongweight=0;$zongPrice=0;
					if(!empty($jiluDetails)){
						foreach ($jiluDetails as $detail) {
							$pdtInfo = json_decode($detail->pdtInfo);
							$num = getXiaoshu($detail->dinghuoNum,$product_set->number_num);
							$zongNum +=$num;
							$zongweight +=$detail->weight*$num;
							$zongPrice +=$detail->price;
							?>
							<tr height="53">
								<td class="sprukuxiangxi_02_tt" width="167" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$pdtInfo->sn?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="592" bgcolor="#ffffff" valign="middle" align="left"> 
									<span><?=$pdtInfo->title?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="258" bgcolor="#ffffff" valign="middle" align="center"> 
									<span><?=$pdtInfo->key_vals?></span>
								</td>
								<td class="sprukuxiangxi_02_tt" width="103" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->dinghuoUnit?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$num?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=getXiaoshu($detail->unit_price*$detail->UnitNum,$product_set->price_num)?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->weight*$num*$detail->UnitNum?><?=$product_set->weight?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->price?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="85" bgcolor="#ffffff" valign="middle" align="center"> 
									<?
									$detail->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$detail->beizhu);
									$detail->beizhu = str_replace('"','',$detail->beizhu);
									$detail->beizhu = str_replace("'",'',$detail->beizhu);
									$detail->beizhu = '<span onmouseover="tips(this,\''.$detail->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($detail->beizhu),5,true).'</span>';
									echo $detail->beizhu;
									?>
								</td>
							</tr>
							<?
						}
					}
					?>
					<tr height="53">
						<td class="sprukuxiangxi_02_tt" width="167" bgcolor="#ffffff" valign="middle" align="center"> 
							合计
						</td>
						<td></td><td></td><td></td>
						<td align="center"><?=$zongNum?></td>
						<td></td>
						<td align="center"><?=$zongweight?><?=$product_set->weight?></td>
						<td align="center"><?=$zongPrice?></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="9" height="130">
							 <ul>                		
							 	<li>
							 		<div class="dhd_adddinghuodan_2_down_left">
							 			<? if($jilu->status==0&&($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'shenhe')||strstr($qx_arry['dinghuo']['functions'],'all'))){?><a href="javascript:" onclick="editYunfei(<?=$jilu->id?>);"><img src="images/biao_121.png"></a><? }?> 运费：
							 		</div>
							 		<div class="dhd_adddinghuodan_2_down_right" id="price_wuliu" data-price="<?=$jilu->price_wuliu?>">
							 			￥<?=$jilu->price_wuliu?>
							 		</div>
							 		<div class="clearBoth"></div>
							 	</li>
							 	<li>
							 		<div class="dhd_adddinghuodan_2_down_left">
							 			应付总额：
							 		</div>
							 		<div class="dhd_adddinghuodan_2_down_right" id="price_all">
							 			<span>￥<?=$jilu->price?></span>
							 		</div>
							 		<div class="clearBoth"></div>
							 	</li>
							 </ul>
						</td>
					</tr>
				</table>
			</div>
			<div class="dhd_dingdanxiangqing_4">
            	<ul>
            		<li>
                    	收货信息：客户名称：<?=$shouhuoInfo['company']?>&nbsp;&nbsp;联系人：<?=$shouhuoInfo['name']?>&nbsp;&nbsp;联系电话：<?=$shouhuoInfo['phone']?>&nbsp;&nbsp;地址：<?=$shouhuoInfo['address']?>
                    </li>
                    <li>
                    	交货日期：
                        <span>
                        	<div class="dhd_dingdanxiangqing_4_left">
                            	<input type="text" <? if($jilu->status==0&&($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'shenhe')||strstr($qx_arry['dinghuo']['functions'],'all'))){?>id="jiaohuoTime"<? }else{?>readonly="true" style="cursor:not-allowed;"<? }?> value="<?=($jilu->jiaohuoTime=='0000-00-00')?'':$jilu->jiaohuoTime?>">
                            </div>
                        	<div class="dhd_dingdanxiangqing_4_right">
                            	<img src="images/biao_76.png">
                            </div>
                        	<div class="clearBoth"></div>
                        </span>
                    </li>
                    <li>
                    	制单人：<?=$jilu->username?>
                    </li>
                    <li>
                    	发票信息：<? switch($fapiaoInfo['type']){
                    		case 0:echo '不开发票';break;
                    		case 1:echo '（普通发票）&nbsp;&nbsp;发票抬头：'.$fapiaoInfo['taitou'].'&nbsp;&nbsp;发票内容：'.$fapiaoInfo['content'].'&nbsp;&nbsp;纳税人识别号：'.$fapiaoInfo['shibie'];break;
                    		case 2:echo '（增值税发票）&nbsp;&nbsp;发票抬头：'.$fapiaoInfo['taitou'].'&nbsp;&nbsp;发票内容：'.$fapiaoInfo['content'].'&nbsp;&nbsp;纳税人识别号：'.$fapiaoInfo['shibie'].'&nbsp;&nbsp;地址：'.$fapiaoInfo['address'].'&nbsp;&nbsp;电话：'.$fapiaoInfo['phone'].'&nbsp;&nbsp;开户名称：'.$fapiaoInfo['kaihuming'].'&nbsp;&nbsp;开户银行：'.$fapiaoInfo['kaihuhang'].'&nbsp;&nbsp;银行账号：'.$fapiaoInfo['kaihubank'];break;
                    	}
                    	?>
                    </li>
            		<li>
            			<div style="display:inline-block;vertical-align:top;">备注信息：</div><div style="display:inline-block;width:1000px"><? if(!empty($beizhus)){
            				foreach ($beizhus as $b) {
            					echo '<div style="padding-bottom:10px;">'.$b['content'].'【'.$b['name'].'&nbsp;/&nbsp;'.$b['company'].'&nbsp;&nbsp;'.$b['time'].'】</div>';
            				}
            			}?>
                    	<a href="javascript:" onclick="addBeizhu();" class="dhd_dingdanxiangqing_4_xiugaibeizhu" id="addBeizhu" style="padding-left:0px;"><img src="images/biao_122.png"> 添加备注</a>
                    	</div>
                    </li>
                    <li>
                    	附件信息：<a href="javascript:" id="uploadPdtImage" class="dhd_adddinghuodan_3_right_fujian_add"><img src="images/biao_123.png">  添加附件</a><font style="color:#98a8b8;font-size:12px;">（附件最大1M，支持格式：JPG、PNG、BMP、GIF）</font>
                    	<div class="photo_tu">
                    		<ul>
                    			<? if(!empty($files)){
                    				foreach ($files as $f){
                    					?>
                    					<li><a href="<?=$f?>" target="_blank"><img src="<?=$f?>?x-oss-process=image/resize,w_122" width="122" height="122"></a></li>
                    					<?
                    				}
                    			}?>
                    			<div class="clearBoth" id="uploadImages" data-num="0"></div>
                    		</ul>
                    	</div>
                    </li>
            	</ul>
            </div>
            <div class="dhd_dingdanxiangqing_5">
            	<div class="dhd_dingdanxiangqing_5_up">
                	操作日志 <img src="images/biao_67.png">
                </div>
            	<div class="dhd_dingdanxiangqing_5_down" style="display:none;">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tbody><tr height="43" id="jiluHeader">
                        	<td bgcolor="#badcec" class="dhd_dingdanxiangqing_5_down_title" align="left" valign="middle">
                            	公司名称
                            </td>
                            <td bgcolor="#badcec" class="dhd_dingdanxiangqing_5_down_title" align="left" valign="middle">
                            	操作人
                            </td>
                            <td bgcolor="#badcec" class="dhd_dingdanxiangqing_5_down_title" align="left" valign="middle">
                             	时间
                            </td>
                            <td bgcolor="#badcec" class="dhd_dingdanxiangqing_5_down_title" align="left" valign="middle">
                            	操作类别
                            </td>
                            <td bgcolor="#badcec" class="dhd_dingdanxiangqing_5_down_title" align="left" valign="middle">
                            	操作日志
                            </td>
                        </tr>
                    </tbody></table>
                </div>
            </div>
			<!--endprint-->
		</div>
	</div>
	<input type="hidden" id="jiluId" value="<?=$jilu->id?>">
	<script type="text/javascript" src="js/dinghuo_detail.js"></script>
	<? require('views/help.html');?>
</body>
</html>