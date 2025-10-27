<?php
global $db,$request;
$fahuoTime = $request['fahuoTime'];
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=配送单-$fahuoTime.xls");
$allRows = array(
	"name"=>array("title"=>"收件姓名","rowCode"=>"{field:'name',title:'收件姓名',width:100}"),
	"xiangshu"=>array("title"=>"箱数","rowCode"=>"{field:'xiangshu',title:'箱数',width:150}"),
	"address"=>array("title"=>"到货地址","rowCode"=>"{field:'address',title:'到货地址',width:100}"),
	"phone"=>array("title"=>"收件电话","rowCode"=>"{field:'phone',title:'收件电话',width:150}"),
	"wuliu"=>array("title"=>"物流方式","rowCode"=>"{field:'wuliu',title:'物流方式',width:150}"),
	"wuliu_phone"=>array("title"=>"物流电话","rowCode"=>"{field:'wuliu_phone',title:'物流电话',width:150}"),
	"biaoji"=>array("title"=>"标记","rowCode"=>"{field:'biaoji',title:'标记',width:150}")
);
$comId = $_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$sql = "select shuohuo_json,rider_id from order_fahuo$fenbiao where comId=$comId and fahuoTime like '$fahuoTime%' and status>-1 ";
$sql.=" order by id";
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
			$shuohuo_json = json_decode($j->shuohuo_json,true);
			$j->name = $shuohuo_json['收件人'];
			$j->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$j->phone = $shuohuo_json['手机号'];
			$rider = $db->get_row("select * from demo_peisong_rider where id=$j->rider_id");
			$j->wuliu = $rider->row2;
			$j->wuliu_phone = $rider->phone;
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
