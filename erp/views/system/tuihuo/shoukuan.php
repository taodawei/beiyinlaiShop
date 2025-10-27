<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$jilu = $db->get_row("select * from demo_tuihuo where id=$id and comId=$comId");
if(empty($jilu)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$jilus = $db->get_results("select * from demo_tuihuo_money where jiluId=$id order by id desc");
$kehuSet = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
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
		<a href="<?=urldecode($request['url'])?>&url=<?=urlencode($request['url'])?>"><img src="images/biao_63.png"/> 订单详情</a>
	</div>
	<div class="purchase_xiang" style="background:#fff">
		<div class="purchase_class">
			<div><a href="?m=system&s=tuihuo&a=jilu_detail&id=<?=$request['id']?>&url=<?=urlencode($request['url'])?>">订单详情</a></div>
			<? if($jilu->status>2){?>
			<div class="purchase_order"><a href="javascript:">退款记录</a></div>
			<? }?>
		</div>
	</div>
	<div class="right_down">
		<div class="sprukuxiangxi">
			<div class="dhd_shoukuanjilu_01">
				<div class="dhd_shoukuanjilu_01_left">
					退单号：<?=$jilu->orderId?><? if($jilu->orderType==2){?>&nbsp;<div class="" style="display: inline-block;position:static;"><div class="sub-tag" style="background-color:#03a9f3;color:#fff;padding:0px 2px">代下单</div></div><? }?>　　　客户名称：<?=$jilu->kehuName?><br>
					退款金额：<?=$jilu->money?>　　　已退金额：<?=$jilu->money-$jilu->money_weikuan?>　　　待退款：<?=$jilu->money_weikuan?>
				</div>
				<? if($jilu->money_weikuan>0&&($adminRole>=7||strstr($qx_arry['tuihuo']['functions'],'caiwu'))){?>
				<div class="dhd_shoukuanjilu_01_right">
					<a href="javascript:" onclick="addShoukuan(<?=$jilu->id?>);">+ 添加退款记录</a>
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
                        		$showStr = 'dinghuoId|'.$jilu->orderId.',money|'.$j->money.',pay_type|'.$pay_type.',beizhu|'.$j->beizhu.',orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheCont.',fujian|'.$j->files;
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
                        			<a href="javascript:" onclick="viewInfo(<?=$j->id?>,'<?=$showStr?>')" class="dd_shoukuan_xiangqing"><img src="images/biao_129.png"/> 详情</a>
                        		</td>
                        	</tr>
                        	<? }
                        	}
                        ?>
                    </table>
                </div>
            </div>
		</div>
	</div>
<div class="shoukuanqueren_xiangqing_tc" id="shoukuanqueren_xiangqing_tc" style="display:none;">
	<div class="bjkh_bj"></div>
    <div class="skqr_xx">
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
                        <div class="skqr_xx_01_right" id="show_fujian"></div>
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
        	<a href="javascript:hideInfo();" class="bjkh_jebangsjxx_3_01">确定</a>
        </div>
    </div>
</div>
<div class="min-form" id="shoukuanDiv">
	<form class="layui-form" method="post" action="?m=system&s=tuihuo&a=add_shoukuan" id="addShoukuanForm">
		<input type="hidden" name="dinghuoId" id="a_dinghuoId" value="0">
	<div class="min-form-inner">
		<legend>添加退款记录</legend>
		<div class="min-form-body">
			<div class="incomeAdd-wrap">
				<div class="incomeAdd-infor">
					<div class="incomeAdd-money-lg">
						<span class="danger mr40">
							待退款：<span id="a_daizhifu">0.00</span>
						</span>
					</div>
					<div class="incomeAdd-money">
						<span class="mr25">
							订单金额：<span id="a_money">0.00</span>
						</span>
						<span class="mr25">
							已退款：<span id="a_payed">0.00</span>
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
									<div>退款至</div>
									<div>资金账户</div>
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
								<div class="clearBoth"></div>
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
								<div class="clearBoth"></div>
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
								<div class="clearBoth"></div>
								<div class="account-list">
									<div class="ant-col-8">
										<div class="ant-row ant-form-item">
											<div class="ant-col-6 ant-form-item-label">
												<label for="fundAccount3" title="">
													<span>经办人</span>
												</label>
											</div>
											<div class="ant-col-18 ant-form-item-control-wrapper">
												<div class="ant-form-item-control has-success">
													<input type="text" name="a_jingbanren" id="a_jingbanren" class="ant-input ant-input-lg">
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="clearBoth"></div>
								<div class="account-list">
									<div class="ant-col-8">
										<div class="ant-row ant-form-item">
											<div class="ant-col-6 ant-form-item-label">
												<label for="fundAccount3" title="">
													<span>备注</span>
												</label>
											</div>
											<div class="ant-col-18 ant-form-item-control-wrapper">
												<div class="ant-form-item-control has-success">
													<textarea type="textarea" class="layui-textarea" name="remark" id="a_remark"></textarea>
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
<script type="text/javascript" src="js/tuihuo_add_shoukuan.js"></script>
<div id="bg"></div>
<? require('views/help.html');?>
</body>
</html>