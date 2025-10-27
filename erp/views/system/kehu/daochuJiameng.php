<?php
global $db,$request;
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=加盟信息.xls");
$allRows = array(
				"title"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'title',title:'".$kehu_title."名称',width:250}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:100}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:150}"),
				"address"=>array("title"=>"地址","rowCode"=>"{field:'address',title:'地址',width:200}"),
				"dtTime"=>array("title"=>"申请时间","rowCode"=>"{field:'dtTime',title:'申请时间',width:130,sort:true}"),
				"tuijianren"=>array("title"=>"推荐人","rowCode"=>"{field:'tuijianren',title:'推荐人',width:100}"),
				"beizhu"=>array("title"=>"附言","rowCode"=>"{field:'beizhu',title:'附言',width:250}")
			);
$comId = $_SESSION[TB_PREFIX.'comId'];
$keyword = $request['keyword'];
$order1 = 'id';
$order2 = 'desc';
$sql="select * from demo_kehu_jiameng where comId=$comId ";
if(!empty($keyword)){
	$sql.=" and title like '%$keyword%'";
}
$sql.=" order by $order1 $order2";
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
