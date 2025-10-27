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
$jilu = $db->get_row("select * from demo_tuihuo where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$k = $db->get_row("select title,level from demo_kehu where id=$jilu->kehuId");
$jiluDetails = $db->get_results("select * from demo_tuihuo_detail where jiluId=".(int)$jilu->id.' order by id asc');
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
if(!empty($jilu->beizhu)){
	$beizhus = json_decode($jilu->beizhu,true);
}
if(!empty($jilu->shoukuanInfo)){
	$shoukuanInfo = json_decode($jilu->shoukuanInfo,true);
}
if(!empty($jilu->fujianInfo)){
	$files = explode('|',$jilu->fujianInfo);
}
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
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
		<a href="<?=urldecode($request['url'])?>"><img src="images/biao_63.png"/> 订单详情</a>
	</div>
	<div class="purchase_xiang" style="background:#fff">
		<div class="purchase_class">
			<div class="purchase_order"><a href="javascript:">订单详情</a></div>
			<? if($jilu->status>2){?>
			<div><a href="?m=system&s=tuihuo&a=shoukuan&id=<?=$request['id']?>&url=<?=urlencode($request['url'])?>">退款记录</a></div>
			<? }?>
		</div>
		<div class="state_right spchukuxiangxi_01_right_right">
			<? if($jilu->status==0&&($adminRole>=7||strstr($qx_arry['tuihuo']['functions'],'shenhe')||strstr($qx_arry['tuihuo']['functions'],'all'))){
				?>
				<a href="javascript:" onclick="zuofei(<?=$id?>);" class="spchukuxiangxi_01_right_right_02" style="float:right;">作废</a>
				<a href="javascript:" onclick="tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01" style="float:right;">通过</a>
			<? }else if($jilu->status==1&&($adminRole>=7||strstr($qx_arry['tuihuo']['functions'],'shouhuo')||strstr($qx_arry['tuihuo']['functions'],'all'))){
				?>
				<a href="javascript:" onclick="tuihui(<?=$id?>);" class="spchukuxiangxi_01_right_right_02" style="float:right;">退回</a>
				<a href="javascript:" onclick="ruku(<?=$id?>);" class="spchukuxiangxi_01_right_right_01" style="float:right;">通过</a>
				<?
			}else if($jilu->status==2&&($adminRole>=7||strstr($qx_arry['tuihuo']['functions'],'caiwu')||strstr($qx_arry['tuihuo']['functions'],'all'))){?>
				<a href="javascript:" onclick="caiwu_tongguo(<?=$id?>);" class="spchukuxiangxi_01_right_right_01" style="float:right;">通过</a>
			<? }?>
			<div class="derive">
				<div><a href="?m=system&s=tuihuo&a=daochu&id=<?=$id?>" target="_blank" style="width:auto;margin-right:0px;"><img src="images/derive.gif">导出</a></div>
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
						$status = '待收货审核';
						break;
						case 2:
						$status = '待财务审核';
						break;
						case 3:
						$status = '已完成';
						break;
						case 4:
						$status = '待收款确认';
						break;
						case -1:
						$status = '已作废';
						break;
					}
					echo $status;
					?></span>
					<span>退货单号：<?=$jilu->orderId?><? if($jilu->orderType==2){?>&nbsp;<div class="" style="display: inline-block;position:static;"><div class="sub-tag" style="background-color:#03a9f3;color:#fff;padding:0px 2px">代下单</div></div><? }?></span>
					<span><?=$kehu_title?>名称：<?=$k->title?>【<?=$db->get_var("select title from demo_kehu_level where id=$k->level");?>】</span>
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
							小计
						</td>
						<!-- <td class="sprukuxiangxi_02_title" width="124" bgcolor="#7bc8ed" valign="middle" align="center"> 
							审批价
						</td> -->
						<td class="sprukuxiangxi_02_title" width="85" bgcolor="#7bc8ed" valign="middle" align="center"> 
							备注
						</td>
					</tr>
					<?
					$heji = 0;$zongNum=0;$zongPrice=0;
					if(!empty($jiluDetails)){
						foreach ($jiluDetails as $detail) {
							$pdtInfo = json_decode($detail->pdtInfo);
							$num = getXiaoshu($detail->num,$product_set->number_num);
							$zongNum +=$num;
							$zongPrice +=$detail->price;
							?>
							<tr height="53" id="rowTr<?=$detail->id?>">
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
									<?=$detail->units?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$num?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=getXiaoshu($detail->unit_price,$product_set->price_num)?>
								</td>
								<td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<?=$detail->price?>
								</td>
								<!-- <td class="sprukuxiangxi_02_tt" width="124" bgcolor="#ffffff" valign="middle" align="center"> 
									<input type="number" min="0" class="sprkadd_xuanzesp_02_tt_input" name="prices[<?=$detail->id?>]" value="<?=$detail->price?>">
								</td> -->
								<td class="sprukuxiangxi_02_tt " width="85" bgcolor="#ffffff" valign="middle" align="center"> 
									<?
									if(!empty($detail->beizhu)){
										$detail->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$detail->beizhu);
										$detail->beizhu = str_replace('"','',$detail->beizhu);
										$detail->beizhu = str_replace("'",'',$detail->beizhu);
										$detail->beizhu = '<span sprukuadd_03_tt_addbeizhu onmouseover="tips(this,\''.$detail->beizhu.'\',1);" onmouseout="hideTips()" onclick="editBeizhu('.$detail->id.')">'.sys_substr(strip_tags($detail->beizhu),5,true).'</span>';
										echo $detail->beizhu;
									}
									?>
									<input type="hidden" name="beizhus[<?=$detail->id?>]" id="beizhu<?=$detail->id?>" value="<?=$detail->beizhu?>">
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
						<td align="center"><?=$zongPrice?></td>
						<!-- <td align="center"><?=$zongPrice?></td> -->
						<td></td>
					</tr>
					<tr>
						<td colspan="9" height="130">
							 <ul>
							 	<li>
							 		<div class="dhd_adddinghuodan_2_down_left">
							 			已申请退款,获批退款金额为：
							 		</div>
							 		<div class="dhd_adddinghuodan_2_down_right" id="price_all">
							 			<span>￥<?=$jilu->money?></span>
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
                    	退款信息：联系人：<?=$shoukuanInfo['name']?>&nbsp;&nbsp;联系电话：<?=$shoukuanInfo['phone']?>&nbsp;&nbsp;开户名称：<?=$shoukuanInfo['kaihuming']?>&nbsp;&nbsp;开户银行：<?=$shoukuanInfo['kaihuhang']?>&nbsp;&nbsp;银行账号：<?=$shoukuanInfo['kaihubank']?>
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
	<script type="text/javascript">
		var cangku_options = '<option value="">选择仓库</option>';
		<? foreach ($cangkus as $c){?>
			cangku_options = cangku_options+'<option value="<?=$c->id?>"><?=$c->title?></option>';
		<? }?>
	</script>
	<script type="text/javascript" src="js/tuihuo_detail.js"></script>
	<? require('views/help.html');?>
</body>
</html>