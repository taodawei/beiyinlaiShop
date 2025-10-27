<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=订单列表.xls");
global $db,$request;
$allRows = array(
	"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
	"pdt_info"=>array("title"=>"商品信息","rowCode"=>"{field:'pdt_info',title:'商品信息',width:250}"),
	"type"=>array("title"=>"订单类型","rowCode"=>"{field:'type',title:'订单类型',width:100}"),
	"dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
	"price"=>array("title"=>"订单总额","rowCode"=>"{field:'price',title:'订单总额',width:100,sort:true}"),
	"status_info"=>array("title"=>"订单状态","rowCode"=>"{field:'status_info',title:'订单状态',width:120}"),
	"payStatus"=>array("title"=>"付款状态","rowCode"=>"{field:'payStatus',title:'付款状态',width:90}"),
	"fapiao"=>array("title"=>"是否开票","rowCode"=>"{field:'fapiao',title:'是否开票',width:90}"),
	"fahuoStatus"=>array("title"=>"发货状态","rowCode"=>"{field:'fahuoStatus',title:'发货状态',width:150}"),
	"username"=>array("title"=>"会员账号","rowCode"=>"{field:'username',title:'会员账号',width:150}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:250}"),
	"shouhuo"=>array("title"=>"收件人","rowCode"=>"{field:'shouhuo',title:'收件人',width:180}"),
	"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:250}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$status = $request['status'];
$ifJifen = (int)$request['if_jifen'];
$keyword = $request['keyword'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$shouhuoInfo = $request['shouhuoInfo'];
$moneystart = $request['moneystart'];
$moneyend = $request['moneyend'];
$payStatus = $request['payStatus'];
$pdtInfo = $request['pdtInfo'];
$kaipiao = (int)$request['kaipiao'];
$page = (int)$request['page'];
$pageNum = (int)$request["limit"];
setcookie('orderPageNum',$pageNum,time()+3600*24*30);
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
if(empty($request['order2'])){
	$order1 = 'id';
	$order2 = 'desc';
}
$sql = "select id,orderId,userId,comId,mendianId,type,status,dtTime,remark,ispay,ifkaipiao,price,fahuo_json,shuohuo_json,product_json from order$fenbiao where comId=$comId and (ispay=1 or type=2) and if_jifen = $ifJifen ";

$paytype = (int)$request['paytype'];
if($paytype > 0){
   	$sql.=" and find_in_set($paytype, pay_types) ";
}

$card = $request['card'] ? $request['card'] : '';
if($card){
    $sql .= " and pay_json like '%$card%' ";
}

if(!empty($status)){
	$status = str_replace('-5','0',$status);
	$sql.=" and status in($status)";
}
if(!empty($keyword)){
	$sql.=" and (orderId='%$keyword%' or shuohuo_json like '%$keyword%')";
}
if(!empty($startTime)){
	$sql.=" and dtTime>='$startTime 00:00:00'";
}
if(!empty($endTime)){
	$sql.=" and dtTime<='$endTime 23:59:59'";
}
if(!empty($orderId)){
	$sql.=" and orderId like '%$orderId%'";
}
if(!empty($kehuName)){
	$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and (nickname like '%$kehuName%' or username='$kehuName')");
	if(empty($userIds))$userIds='0';
	$sql.=" and userId in($userIds)";
}
if(!empty($shouhuoInfo)){
	$sql.=" and shuohuo_json like '%$shouhuoInfo%'";
}
if(!empty($moneystart)){
	$sql.=" and price>='$moneystart'";
}
if(!empty($moneyend)){
	$sql.=" and price<='$moneyend'";
}
if(!empty($payStatus)){
	$payStatus = $payStatus%2;
	$sql.=" and payStatus=$payStatus";
}
if(!empty($pdtInfo)){
	$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=$comId and pdtInfo like '%$pdtInfo%'");
	if(empty($jiluIds))$jiluIds='0';
	$sql.=" and id in($jiluIds)";
}
if(!empty($kaipiao)){
	$kaipiao = $kaipiao%2;
	$sql.=" and ifkaipiao=$kaipiao";
}
$sql.=" order by $order1 $order2 limit 30000";
$pdts = $db->get_results($sql);
?>
<table border="1" >
	<tbody><tr>
		<?
		foreach ($allRows as $row=>$isshow){
				?>
				<td><?=$isshow['title']?></td>
				<?
		}
		?>
	</tr>
	<?
	if(!empty($pdts)){
		foreach ($pdts as $j){
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$status = '';
			switch ($j->status) {
				case 0:
					$status = '待审核';
				break;
				case 1:
					$status = '待财务审核';
				break;
				case 2:
					$status = '待发货';
				break;
				case 3:
					$status = '待收货';
				break;
				case 4:
					$status = '已完成';
				break;
				case -2:
					$status = '异常';
				break;
				case -3:
					$status = '退换货';
				break;
				case -1:
					$status = '无效';
				break;
			}
			$j->status_info = $status;
			switch ($j->ispay){
				case 0:
					$j->payStatus = '未付款';
				break;
				case 1:
					$j->payStatus = '已付款';
				break;
				case 2:
					$j->payStatus = '部分退款';
				break;
				case 3:
					$j->payStatus = '全部退款';
				break;
			}
			switch ($j->type){
				case 1:
					$j->type = '商城订单';
				break;
				case 2:
					$j->type = '货到付款';
				break;
				case 3:
					$j->type = '门店订单';
				break;
				case 4:
					$j->type = '预售订单';
				break;
			}
			$j->username = $db->get_var("select username from users where id=$j->userId");
			$j->fahuoStatus = empty($j->fahuo_json)?'无':'已发货';
			switch ($j->ifkaipiao){
				case 0:
					$j->fapiao = '不开发票';
				break;
				case 1:
					$j->fapiao = '纸质发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
				case 2:
					$j->fapiao = '电子发票';
					if(!empty($j->fapiao_json)){
						$fapiao_json = json_decode($j->fapiao_json,true);
						$j->fapiao.=$fapiao_json['发票类型'];
					}
				break;
			}
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
			$product_array = json_decode($j->product_json);
			$j->pdt_info = '';
			foreach ($product_array as $val) {
				$j->pdt_info.= ','.$val->title.'['.$val->key_vals.']'.' * '.$val->num.$val->unit;
			}
			$j->pdt_info = substr($j->pdt_info, 1);
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
