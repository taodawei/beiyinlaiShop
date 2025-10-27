<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$jilu = $db->get_row("select * from demo_dinghuo_order where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$js = $db->get_results("select * from demo_dinghuo_money where jiluId=$id order by id desc");
$jilus = array();
$daiTuikuan = 0;
$tuikuans = array();
foreach ($js as $j){
	if($j->type==0){
		$jilus[] = $j;
	}else{
		$tuikuans[] = $j;
		if($j->status==0){
			$daiTuikuan += $j->money;
		}
	}
}
$daiQueren = 0;
if(!empty($jilus)){
	foreach ($jilus as $k){
		if($k->status==0){
			$daiQueren+=$k->money;
		}
	}
}
$kehuSet = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
$banks = $db->get_results("select * from demo_kehu_bank where comId=$comId and status=1 ");
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
	<link href="styles/shoukuan.css" rel="stylesheet" type="text/css" />
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
			<div class=""><a href="?m=system&s=dinghuo&a=detail&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">订单详情</a></div>
			<? if($jilu->status>1){?>
			<div><a href="?m=system&s=dinghuo&a=chuku&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>&url=<?=urlencode($request['url'])?>">出库发货记录</a></div>
			<? }?>
			<div class="purchase_order"><a href="javascript:">收款记录</a></div>
		</div>
	</div>
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="dhd_shoukuanjilu_01">
				<div class="dhd_shoukuanjilu_01_left">
					订单号：<?=$jilu->orderId?><? if($jilu->orderType==2){?>&nbsp;<div class="" style="display: inline-block;position:static;"><div class="sub-tag" style="background-color:#03a9f3;color:#fff;padding:0px 2px">代下单</div></div><? }?><br>
					订单金额：<?=$jilu->price?>　　　已付款：<?=$jilu->price_payed?>　　　
					<? if($jilu->status==-1){?>待退款：<?=$daiTuikuan?><?}else{?>待确认：<?=$daiQueren?>	　　待支付：<?=$jilu->price_weikuan-$daiQueren?><? }?>
				</div>
				<? if($jilu->price_weikuan-$daiQueren>0&&$jilu->status>-1&&($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu')||strstr($qx_arry['dinghuo']['functions'],'all'))){?>
				<div class="dhd_shoukuanjilu_01_right">
					<a href="javascript:" onclick="addShoukuan(<?=$jilu->id?>);">+ 添加收款记录</a>
				</div>
				<? }?>
				<div class="clearBoth"></div>
			</div>
			<div class="dhd_shoukuanjilu">
            	<div class="dhd_shoukuanjilu_02">
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="46">
                        	<td bgcolor="#e8f2f6" align="center" valign="middle">
                            	支付流水号
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	时间
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	付款金额
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	支付方式
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	收款账户
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	 状态
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	 操作
                            </td>
                        </tr>
                        <? if(!empty($jilus)){
                        	foreach ($jilus as $j) {
                        		$pay_type = getPayType($j->pay_type);
                        		$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->beizhu);
                        		$j->beizhu = str_replace(',','，',$j->beizhu);
                        		$showStr = 'dinghuoId|'.$jilu->orderId.',money|'.$j->money.',pay_type|'.$pay_type.',beizhu|'.$j->beizhu.',orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheCont.',fujian|'.str_replace('|','~',$j->files);
                        	?>
                        	<tr height="46" <? if($j->status==-1){?>class="deleted"<? }?>>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=$j->orderId?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=$j->dtTime?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=getXiaoshu($j->money,2)?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=$pay_type ?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<? switch($j->pay_type){
                        				case 6:
                        					$shoukuan_info = json_decode($j->shoukuan_info);
                        					echo '<span onmouseover="tips(this,\'开户银行：'.$shoukuan_info->bank_name.'<br>账户名称：'.$shoukuan_info->bank_user.'\',1);" onmouseout="hideTips();">'.$shoukuan_info->bank_account.'&nbsp;<img src="images/bank_icon.png"></span>';
                        				break;
                        				default:echo $pay_type;break;
                        			}?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<? switch($j->status){
                        				case -1:echo '已作废';break;
                        				case 0:echo '待审核';break;
                        				case 1:echo '已收款';break;
                        			}?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                                    <? if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu')){?>
                        			<a href="javascript:" onclick="viewInfo(<?=$j->id?>,<?=$j->status?>,'<?=$showStr?>')" class="dd_shoukuan_xiangqing"><? if($j->status==0){?><img src="images/biao_95.png"/> 审核<? }else{?><img src="images/biao_129.png"/> 详情<? }?></a> <? if($j->status==0){?><a href="javascript:" onclick="zuofei(<?=$j->id?>,<?=$id?>)"><img src="images/biao_127.png"/> 作废</a><? }
                                    }
                                    ?>
                        		</td>
                        	</tr>
                        	<? }
                        	}
                        ?>
                    </table>
                </div>
                <? if(!empty($tuikuans)){?>
                <div class="dhd_shoukuanjilu_02">
                	<div style="margin-top:30px;margin-bottom:5px;">退款记录</div>
                	<table width="100%" border="0" cellpadding="0" cellspacing="0">
                    	<tr height="46">
                        	<td bgcolor="#e8f2f6" align="center" valign="middle">
                            	支付流水号
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	时间
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	退款金额
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	退款账户
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	 状态
                            </td>
                            <td bgcolor="#e8f2f6" align="center" valign="middle">
                            	 操作
                            </td>
                        </tr>
                        <? if(!empty($tuikuans)){
                        	foreach ($tuikuans as $j) {
                        		$pay_type = '';
                        		switch($j->pay_type){
                        			case 1:$pay_type= '现金账号余额';break;
                        			case 2:$pay_type= '预付款账户余额';break;
                        			case 3:$pay_type= '返点账户余额';break;
                        			case 4:$pay_type= '保证金账户余额';break;
                        			case 5:$pay_type= '现金';break;
                        			case 6:$pay_type= '银行转账';break;
                        			case 7:$pay_type= '支付宝';break;
                        			case 8:$pay_type= '微信';break;
                        			case 9:$pay_type= '其他';break;
                        		}
                        	?>
                        	<tr height="46" <? if($j->status==-1){?>class="deleted"<? }?>>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=$j->orderId?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=$j->dtTime?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=getXiaoshu($j->money,2)?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<?=$pay_type ?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<? switch($j->status){
                        				case 0:echo '待审核';break;
                        				case 1:echo '已退款';break;
                        			}?>
                        		</td>
                        		<td bgcolor="#ffffff" align="center" valign="middle">
                        			<? if($j->status==0&&($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'caiwu')||strstr($qx_arry['dinghuo']['functions'],'all'))){?><a href="javascript:" onclick="z_tongguo(<?=$j->id?>)"><img src="images/biao_95.png"> 审核</a><? }?>
                        		</td>
                        	</tr>
                        	<? }
                        	}
                        ?>
                    </table>
                </div>
                <? }?>
            </div>
		</div>
	</div>
<div class="shoukuanqueren_xiangqing_tc" id="shoukuanqueren_xiangqing_tc" data-id="0" style="display:none;">
	<div class="bjkh_bj"></div>
    <div class="skqr_xx" style="top:5%">
    	<div class="bjkh_jebangsjxx_1">
        	订单付款详情
        </div>
    	<div class="bjkh_jebangsjxx_2">
        	<div class="skqr_xx_01">
            	<ul>
                    <li>
                    	<div class="skqr_xx_01_left">
                        	订单号： 
                        </div>
                    	<div class="skqr_xx_01_right" id="show_dinghuoId">
                        	
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="skqr_xx_01_left">
                        	金额：
                        </div>
                    	<div class="skqr_xx_01_right" id="show_money">
                        	
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="skqr_xx_01_left">
                        	支付方式：
                        </div>
                    	<div class="skqr_xx_01_right" id="show_pay_type">
                        	   
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="skqr_xx_01_left">
                        	备注：
                        </div>
                    	<div class="skqr_xx_01_right" id="show_beizhu">
                        	
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="skqr_xx_01_left">
                            附件：
                        </div>
                        <div class="skqr_xx_01_right" id="show_fujian">
                            
                        </div>
                        <div class="clearBoth"></div>
                    </li>
            	</ul>
            </div>
            <div class="skqr_xx_02">
            	流水号：<span id="show_orderId"></span>　　　　　　日期：<span id="show_dtTime"></span><br>
   				操作人：<span id="show_userName"></span>　　　　　　 审核人：<span id="show_shenheUser"></span>
            </div>
        </div>
    	<div class="bjkh_jebangsjxx_3">
            <a href="javascript:t_queren();" id="qrbtn" class="bjkh_jebangsjxx_3_01">确认收款</a>
        	<a href="javascript:hideInfo();" class="bjkh_jebangsjxx_3_01" style="background:#999;border:#999 1px solid;">关闭</a>
        </div>
    </div>
</div>
<div class="min-form" id="shoukuanDiv">
	<form class="layui-form" method="post" action="?m=system&s=dinghuo&a=add_shoukuan" id="addShoukuanForm">
		<input type="hidden" name="dinghuoId" id="a_dinghuoId" value="0">
	<div class="min-form-inner">
		<legend>添加收款记录</legend>
		<div class="min-form-body">
			<div class="incomeAdd-wrap">
				<div class="incomeAdd-infor">
					<div class="incomeAdd-money-lg">
						<span class="danger mr40">
							待支付：<span id="a_daizhifu">0.00</span>
						</span>
					</div>
					<div class="incomeAdd-money">
						<span class="mr25">
							订单金额：<span id="a_money">0.00</span>
						</span>
						<span class="mr25">
							已付款：<span id="a_payed">0.00</span>
						</span>
						<span>
							待确认：<span id="a_daiqueren">0.00</span>
						</span>
						<span class="cgray">|</span>
						<span id="a_orderId">0.00</span>
					</div>
				</div>
				<div class="incomeAdd-form">
					<form class="ant-form ant-form-horizontal">
						<div class="incomeAdd-form-table">
							<div class="ant-row">
								<div class="ant-col-24">
									<div>使用资金</div>
									<div>账户付款</div>
								</div>
							</div>
							<div class="ant-row mb10">
								<div class="account-list">
									<div class="ant-col-8">
										<div class="ant-row ant-form-item <? if(cnStrLen($kehuSet->acc_xianjin_name)>5){echo 'multi-line';}?>">
											<div class="ant-col-6 ant-form-item-label">
												<label class="ant-form-item-required" >
													<span><?=$kehuSet->acc_xianjin_name?></span>
												</label>
											</div>
											<div class="ant-col-18 ant-form-item-control-wrapper">
												<div class="ant-form-item-control has-success">
													<input type="number" min="0" step="0.01" value="0" name="a_account1" id="a_account1" class="ant-input ant-input-lg">
												</div>
											</div>
										</div>
									</div>
									<div class="ant-col-4 lh35 cgray">
										&nbsp;余额 <span id="a_yue_account1">0.00</span>
									</div>
								</div>
								<? if($kehuSet->acc_ifyufu==1){?>
								<div class="account-list">
									<div class="ant-col-8">
										<div class="ant-row ant-form-item <? if(cnStrLen($kehuSet->acc_yufu_name)>5){echo 'multi-line';}?>">
											<div class="ant-col-6 ant-form-item-label">
												<label for="fundAccount2" class="ant-form-item-required" title="">
													<span><?=$kehuSet->acc_yufu_name?></span>
												</label>
											</div>
											<div class="ant-col-18 ant-form-item-control-wrapper">
												<div class="ant-form-item-control has-success">
													<input type="number" min="0" step="0.01" value="0" name="a_account2" id="a_account2" class="ant-input ant-input-lg">
												</div>
											</div>
										</div>
									</div>
									<div class="ant-col-4 lh35 cgray">
										&nbsp;余额 <span id="a_yue_account2">0.00</span>
									</div>
								</div>
								<? }
								if($kehuSet->acc_iffandian==1){
								?>
								<div class="account-list">
									<div class="ant-col-8">
										<div class="ant-row ant-form-item <? if(cnStrLen($kehuSet->acc_fandian_name)>5){echo 'multi-line';}?>">
											<div class="ant-col-6 ant-form-item-label">
												<label for="fundAccount3" class="ant-form-item-required" title="">
													<span><?=$kehuSet->acc_fandian_name?></span>
												</label>
											</div>
											<div class="ant-col-18 ant-form-item-control-wrapper">
												<div class="ant-form-item-control has-success">
													<input type="number" min="0" step="0.01" value="0" name="a_account3" id="a_account3" class="ant-input ant-input-lg">
												</div>
											</div>
										</div>
									</div>
									<div class="ant-col-4 lh35 cgray">
										&nbsp;余额 <span id="a_yue_account3">0.00</span>
									</div>
								</div>
								<? }?>
							</div>
						</div>
						<div class="incomeAdd-form-table">
							<div class="ant-row">
								<div class="ant-col-24">
									<div>
										客户已
									</div>
									<div>
										线下付款
									</div>
								</div>
							</div>
							<div class="ant-row">
								<div class="ant-col-12">
									<div class="ant-row ant-form-item">
										<div class="ant-col-4 ant-form-item-label">
											<label class="ant-form-item-required" title="付款金额">
												付款金额
											</label>
										</div>
										<div class="ant-col-18 ant-form-item-control-wrapper">
											<div class="ant-form-item-control has-success">
												<input type="number" min="0" step="0.01" value="0" name="a_payMoney" id="a_payMoney" class="ant-input ant-input-lg">
											</div>
										</div>
									</div>
									<div class="ant-row ant-form-item">
										<div class="ant-col-4 ant-form-item-label">
											<label for="payTime" class="ant-form-item-required" title="付款日期">
												付款日期
											</label>
										</div>
										<div class="ant-col-18 ant-form-item-control-wrapper">
											<div class="ant-form-item-control has-success">
												<input type="text" name="dtTime" id="a_dtTime" class="ant-input ant-input-lg">
											</div>
										</div>
									</div>
									<div class="ant-row ant-form-item">
										<div class="ant-col-4 ant-form-item-label">
											<label for="offlineReceiptMethod" class="ant-form-item-required" title="收款方式">
												收款方式
											</label>
										</div>
										<div class="ant-col-18 ant-form-item-control-wrapper">
											<div class="ant-form-item-control has-success">
												<input type="radio" name="pay_type" lay-filter="pay_type1" value="5" title="现金" checked>
                        						<input type="radio" name="pay_type" lay-filter="pay_type2" value="6" <? if(empty($banks)){echo 'disabled';}?> title="银行转账">
											</div>
										</div>
									</div>
									<div class="ant-row ant-form-item" id="a_bankDiv" style="display:none;">
										<div class="ant-col-4 ant-form-item-label">
											<label for="offlineReceiptMethod" class="ant-form-item-required" title="收款方式">
												收款账号
											</label>
										</div>
										<div class="ant-col-18 ant-form-item-control-wrapper">
											<div class="ant-form-item-control has-success">
												<select name="bank_id" id="a_bank">
													<? if(!empty($banks)){
														foreach ($banks as $b){?>
															<option value="<?=$b->id?>"><?=$b->bank_name?>&nbsp;<?=$b->bank_account?></option>	
														<?}
													}?>
												</select>
											</div>
										</div>
									</div>
									<div class="ant-row ant-form-item">
										<div class="ant-col-4 ant-form-item-label">
											<label class="" title="备注">　　　备注
											</label>
										</div>
										<div class="ant-col-18 ant-form-item-control-wrapper">
											<textarea type="textarea" class="layui-textarea" name="remark" id="a_remark"></textarea>
										</div>
									</div>
									<div class="ant-row ant-form-item">
										<div class="ant-col-4 ant-form-item-label">
											<label for="attachmentFileList" class="" title="附件">　　　附件</label>
										</div>
										<div class="ant-col-18 ant-form-item-control-wrapper">
											<div class="ant-form-item-control ">
												<input type="hidden" name="files" id="originalPic" value="">
												<a href="javascript:" id="uploadPdtImage" class="dhd_adddinghuodan_3_right_fujian_add"><img src="images/biao_123.png">  添加附件</a><font style="color:#98a8b8;font-size:12px;">（附件最大1M，支持格式：JPG、PNG、BMP、GIF）</font>
												<div class="photo_tu">
													<ul>
														<div class="clearBoth" id="uploadImages" data-num="0"></div>
													</ul>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<div class="min-form-footer dd_addshoukuan_05">
			<a href="javascript:" onclick="shoukuanTijiao();" class="kh_gjsousuo_04_1">确定</a><a href="javascript:hideShoukuan();" class="kh_gjsousuo_04_21">取消</a>
		</div>
	</div>
</form>
</div>
<input type="hidden" id="jiluId" value="<?=$jilu->id?>">
<script type="text/javascript" src="js/dinghuo_shoukuan.js"></script>
<script type="text/javascript" src="js/dinghuo_add_shoukuan.js"></script>
<div id="bg"></div>
<? require('views/help.html');?>
</body>
</html>