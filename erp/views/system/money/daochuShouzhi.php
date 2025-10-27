<?php
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$fenbiao = getFenbiao($comId,20);
$remark = $request['remark'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$account = $request['account'];
$keyword = $request['keyword'];
$level = (int)$request['level'];
$kehuId = (int)$_SESSION['kehuId'];
$sql="select * from demo_kehu_liushui$fenbiao where comId=$comId";
if(!empty($kehuId)){
	$sql.=" and kehuId=$kehuId";
}
if(!empty($account)){
	$sql.=" and typeInfo like '%$account%'";
}
if(!empty($startTime)){
	$sql.=" and dtTime>= '$startTime'";
}
if(!empty($endTime)){
	$sql.=" and dtTime<= '$endTime'";
}
if(!empty($remark)){
	$sql.=" and remark='$remark'";
}
if(!empty($keyword)){
	$dinghuoId = (int)$db->get_var("select id from demo_dinghuo_order where comId=$comId and orderId='$keyword' limit 1");
	$tuihuoId = (int)$db->get_var("select id from demo_tuihuo where comId=$comId and orderId='$keyword' limit 1");
	$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and title like '%$keyword%'");
	if(empty($kehuIds))$kehuIds='0';
	$sql.=" and (kehuId in($kehuIds) or orderId like '%$keyword%' or dinghuoId=$dinghuoId)";
}
if($level>0){
	$kehuIds = $db->get_var("select group_concat(id) from demo_kehu where comId=$comId and level=$level");
	if(empty($kehuIds))$kehuIds='0';
	$sql.=" and kehuId in($kehuIds)";
}
$sql.=" order by id desc";
$jilus = $db->get_results($sql);
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment; filename=收支明细.xls");
$allRows = array(
	"orderId"=>"支付流水号",
	"dinghuoOrderId"=>"订单号",
	"dtTime"=>"时间",
	"kehuName"=>$kehu_title."名称",
	"pay_type"=>"支付方式",
	"money"=>"金额",
	"account"=>"收款账户",
	"remark"=>"摘要",
	"status"=>"状态"
);
?>
<table border="1">
	<tbody>
		<tr>
			<?
			foreach ($allRows as $row=>$isshow){
				?>
				<td><?=$isshow?></td>
				<?
			}
			?>
		</tr>
		<?
		if(!empty($jilus)){
			foreach ($jilus as $j){
				$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
				$j->pay_type = getPayType($j->accountType);
				$j->account = $j->pay_type;
				if($j->account=='银行转账'){
					$account = json_decode($j->typeInfo);
					$j->account = $account->bank_name.'<br>'.$account->bank_account;
				}
				$status = '';
				$j->layclass = '';
				if($j->status==-1){
					$j->layclass = 'deleted';
					$status = '已作废';
				}else{
					$status = '已完成';
				}
				$j->status = $status;
				$j->detail = 'orderId|'.$j->orderId.',dtTime|'.$j->dtTime.',userName|'.$j->userName.',shenheUser|'.$j->shenheUser;
				$payType = getPayType($j->accountType);
				if(!empty($j->dinghuoId)){
					if($j->order_type==1){
						$j->detail .=',订货单号|'.$j->dinghuoOrderId;
						if($j->type==1){
							$j->detail .=',充值资金账户|'.$payType;
						}else{
							$j->detail .=',支付方式|'.$j->dinghuoOrderId;
						}
					}else{
						$j->detail .=',退货单号|'.$j->dinghuoOrderId;
						$j->detail .=',充值资金账户|'.$payType;
					}
				}else{
					if($j->type==1){
						$j->detail .=',充值资金账户|'.$payType;
					}else{
						$j->detail .=',支付方式|'.$j->dinghuoOrderId;
					}
				}
				$j->detail .=',金额|'.$j->money;
				$j->detail .=',备注|'.$j->remark;
				if($j->type==1){
					$j->money = '<span style="color:green">'.$j->money.'</span>';
				}else{
					$j->money = '<span style="color:red">'.$j->money.'</span>';
				}
				if(!empty($j->dinghuoId)&&$j->order_type==1){
					$j->dinghuoOrderId = '<span onclick="view_dinghuo('.$j->dinghuoId.')" style="cursor:pointer;">'.$j->dinghuoOrderId.'</span>';
				}else if(!empty($j->dinghuoId)&&$j->order_type==2){
					$j->dinghuoOrderId = '<span onclick="view_tuihuo('.$j->dinghuoId.')" style="cursor:pointer;">'.$j->dinghuoOrderId.'</span>';
				}
				$j->kehuName = $db->get_var("select title from demo_kehu where id=$j->kehuId");
				$j->remark = sys_substr($j->remark,10,true);
				?>
				<tr>
					<?
					foreach ($allRows as $row=>$isshow){
						?>
						<td style="vnd.ms-excel.numberformat:@"><?=$j->$row?></td>
						<?
					}
					?>
				</tr>
				<?
			}
		}
		?>
	</tbody></table>
