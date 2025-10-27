<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=发票订单列表.xls");
global $db,$request;
$allRows = array(
	"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
	"kaipiao_status"=>array("title"=>"开票状态","rowCode"=>"{field:'kaipiao_status',title:'开票状态',width:150}"),
	"price"=>array("title"=>"开票金额","rowCode"=>"{field:'price',title:'开票金额',width:100,sort:true}"),
	"kaipiao_fangshi"=>array("title"=>"开票方式","rowCode"=>"{field:'kaipiao_fangshi',title:'开票方式',width:120}"),
	"kaipiao_type"=>array("title"=>"开票类型","rowCode"=>"{field:'kaipiao_type',title:'开票类型',width:120}"),
	"kaipiao_title"=>array("title"=>"发票抬头","rowCode"=>"{field:'kaipiao_title',title:'发票抬头',width:220}"),
	"kaipiao_shibie"=>array("title"=>"税号","rowCode"=>"{field:'kaipiao_shibie',title:'税号',width:220}"),
	"kaipiao_cont"=>array("title"=>"发票内容","rowCode"=>"{field:'kaipiao_cont',title:'发票内容',width:150}"),
	"username"=>array("title"=>"会员账号","rowCode"=>"{field:'username',title:'会员账号',width:150}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:250}"),
	"shouhuo"=>array("title"=>"收件人","rowCode"=>"{field:'shouhuo',title:'收件人',width:180}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$status = $request['status'];
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
$sql = "select id,orderId,userId,comId,status,ispay,price,ifkaipiao,kaipiao_status,shuohuo_json,fapiao_json from order$fenbiao where comId=10 and ifkaipiao>0 and status not in(-1,-5)";
if(!empty($status)){
	$status = str_replace('9','0',$status);
	if(strstr($status,'-1')){
		$last_time = date("Y-m-d H:i:s",strtotime("-1 hours"));
		$sql.=" and (status in($status) or (status=-5 and dtTime<'$last_time'))";
	}else{
		$sql.=" and status in($status)";
	}
}
if(!empty($keyword)){
	$userIds = $db->get_var("select group_concat(id) from users where comId=10 and (nickname like '%$keyword%' or username='$keyword')");
	if(empty($userIds))$userIds='0';
	$sql.=" and (orderId like '%$keyword%' or userId in($userIds))";
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
	$userIds = $db->get_var("select group_concat(id) from users where comId=10 and (nickname like '%$kehuName%' or username='$kehuName')");
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
	$jiluIds = $db->get_var("select group_concat(distinct(orderId)) from order_detail$fenbiao where comId=10 and pdtInfo like '%$pdtInfo%'");
	if(empty($jiluIds))$jiluIds='0';
	$sql.=" and id in($jiluIds)";
}
if(!empty($kaipiao)){
	$sql.=" and kaipiao_status=$kaipiao";
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
			$j->username = $db->get_var("select username from users where id=$j->userId");
			$j->kaipiao_fangshi = $j->ifkaipiao==2?'电子':'纸质';
			$j->status = $j->kaipiao_status;
			$j->kaipiao_status = $j->kaipiao_status==2?'<font color="green">已开票</font>':'<font color="red">待开票</font>';
			$fapiao_json = json_decode($j->fapiao_json,true);
			$j->kaipiao_type = $fapiao_json['发票类型'];
			$j->kaipiao_title = $fapiao_json['发票抬头'];
			$j->kaipiao_shibie = $fapiao_json['税号'];
			$j->kaipiao_cont = $fapiao_json['开票内容'];
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->shouhuo = $shuohuo_json['收件人'].'('.$shuohuo_json['手机号'].')';
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
