<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=充值记录.xls");
global $db,$request;
$allRows = array(
    "orderId"=>array("title"=>"流水号","rowCode"=>"{field:'orderId',title:'流水号',width:250}"),
    "name"=>array("title"=>"会员名称","rowCode"=>"{field:'name',title:'会员名称',width:100}"),
    "username"=>array("title"=>"账号","rowCode"=>"{field:'username',title:'账号',width:150}"),
    "dtTime"=>array("title"=>"充值时间","rowCode"=>"{field:'dtTime',title:'操作时间',width:150}"),
    "money"=>array("title"=>"金额(元)","rowCode"=>"{field:'money',title:'金额(元)',width:100}"),
    "yue"=>array("title"=>"账户余额","rowCode"=>"{field:'yue',title:'账户余额',width:150}"),
    "remark"=>array("title"=>"类型","rowCode"=>"{field:'remark',title:'类型',width:100}"),
    "orderInfo"=>array("title"=>"备注","rowCode"=>"{field:'orderInfo',title:'备注',width:400}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$userId = (int)$request['userId'];
$type = (int)$request['type'];
$pay_type = (int)$request['pay_type'];
$keyword = $request['keyword'];
$money_start = $request['money_start'];
$money_end = $request['money_end'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = (int)$request['page'];
$pageNum = (int)$request["limit"];
if(empty($request['order2'])){
	$order1 = 'id';
	$order2 = 'desc';
}
$sql = "select * from user_liushui$fenbiao where comId=$comId ";
if(!empty($userId)){
	$sql.=" and userId=$userId";
}
if(!empty($type)){
	$sql.=" and type=$type";
}
if(!empty($keyword)){
	$userIds = $db->get_var("select group_concat(id) from users where comId=$comId and nickname='$keyword' or username='$keyword'");
	if(empty($userIds))$userIds='0';
	$sql.=" and (orderId='$keyword' or userId in($userIds))";
}
if(!empty($pay_type)){
	switch ($pay_type) {
		case 1:
			$sql.=" and orderInfo like '支付宝充值，支付宝单号%'";
		break;
		case 2:
			$sql.=" and orderInfo like '微信充值，微信单号%'";
		break;
		case 99:
			$sql.=" and remark='后台充值'";
		break;
	}
}
if(!empty($money_start)){
	$sql.=" and money>='$money_start'";
}
if(!empty($money_end)){
	$sql.=" and money<='$money_end'";
}
if(!empty($startTime)){
	$sql.=" and dtTime>='$startTime 00:00:00'";
}
if(!empty($endTime)){
	$sql.=" and dtTime<='$endTime 23:59:59'";
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
			$u = $db->get_row("select nickname,username from users where id=$j->userId");
			$j->name = $u->nickname;
			$j->username = $u->username;
			$j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			//$j->money = $j->money>0?$j->money:$j->money;
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
