<?php
global $db,$request;
$fahuoTime = $request['fahuoTime'];
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=结算单-$fahuoTime.xls");
$allRows = array(
				"name"=>array("title"=>"客户姓名","rowCode"=>"{field:'name',title:'客户姓名',width:150}"),
				"dtTime"=>array("title"=>"下单日期","rowCode"=>"{field:'dtTime',title:'下单日期',width:150}"),
				"fahuoTime"=>array("title"=>"发货日期","rowCode"=>"{field:'fahuoTime',title:'发货日期',width:150}"),
				"peisongname"=>array("title"=>"配送姓名","rowCode"=>"{field:'peisongname',title:'配送姓名',width:150}"),
				"shoujian_name"=>array("title"=>"收件姓名","rowCode"=>"{field:'shoujian_name',title:'收件姓名',width:150}"),
				"sn"=>array("title"=>"品种编号","rowCode"=>"{field:'sn',title:'品种编号',width:150}"),
				"product"=>array("title"=>"品种名称","rowCode"=>"{field:'product',title:'品种名称',width:150}"),
				"penjing"=>array("title"=>"盆径","rowCode"=>"{field:'penjing',title:'盆径',width:150}"),
				"guige"=>array("title"=>"规格","rowCode"=>"{field:'guige',title:'规格',width:150}"),
				"toushu"=>array("title"=>"头数","rowCode"=>"{field:'toushu',title:'头数',width:150}"),
				"num"=>array("title"=>"件数","rowCode"=>"{field:'num',title:'件数',width:150}"),
				"shuliang"=>array("title"=>"数量","rowCode"=>"{field:'shuliang',title:'数量',width:150}"),
				"xiaoji"=>array("title"=>"小计","rowCode"=>"{field:'xiaoji',title:'小计',width:150}"),
				"daohuodizhi"=>array("title"=>"到货地址","rowCode"=>"{field:'daohuodizhi',title:'到货地址',width:150}"),
				"wuliufangshi"=>array("title"=>"物流方式","rowCode"=>"{field:'wuliufangshi',title:'物流方式',width:150}")
			);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$orderIds = $db->get_var("select group_concat(orderIds) from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1");
if(empty($orderIds))$orderIds='-1';
$sql = "select id,userId,orderId,productId,pdtInfo,num,unit_price from order_detail$fenbiao where orderId in($orderIds)";
$sql.=" order by userId asc";
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
			$o = $db->get_row("select shuohuo_json,remark,fahuoId,dtTime from order$fenbiao where id=$j->orderId");
			$fahuo = $db->get_row("select fahuoTime,rider_id from order_fahuo$fenbiao where id=$o->fahuoId");
			$j->dtTime = date("Y-m-d",strtotime($o->dtTime));
			$j->fahuoTime = date("Y-m-d",strtotime($fahuo->fahuoTime));
			$pdtInfo = json_decode($j->pdtInfo);
			$shouhuo = json_decode($o->shuohuo_json,true);
			$addrows = $db->get_var("select addrows from demo_product where id=$j->productId");
			$addrows_arr = json_decode($addrows,true);
			$j->name = $db->get_var("select nickname from users where id=$j->userId");
			$j->shoujian_name = $shouhuo['收件人'];
			$j->sn = $pdtInfo->sn;
			$j->product = $pdtInfo->title;
			$j->penjing = $addrows_arr['盆径'];
			$j->guige = $addrows_arr['规格'];
			$j->toushu = $addrows_arr['头数'];
			$j->shuliang = $j->num * intval($addrows_arr['数量']);
			$j->xiaoji = $j->num*$j->unit_price;
			$j->num = getXiaoshu($j->num,0);
			$j->wuliufangshi = $db->get_var("select row2 from demo_peisong_rider where id=$fahuo->rider_id");
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
