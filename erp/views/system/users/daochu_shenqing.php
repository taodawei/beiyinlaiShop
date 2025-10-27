<?php
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=经销商申请列表.xls");
global $db,$request;
$allRows = array(
	"userId"=>array("title"=>"会员id","rowCode"=>"{field:'userId',title:'会员id',width:100}"),
	"nickname"=>array("title"=>"姓名","rowCode"=>"{field:'nickname',title:'姓名',width:150}"),
	"username"=>array("title"=>"电话","rowCode"=>"{field:'username',title:'电话',width:150}"),
	"address"=>array("title"=>"地址","rowCode"=>"{field:'address',title:'地址',width:250}"),
	"shenfenzheng"=>array("title"=>"身份证","rowCode"=>"{field:'shenfenzheng',title:'身份证',width:150}"),
	"statusInfo"=>array("title"=>"状态","rowCode"=>"{field:'statusInfo',title:'状态',width:100}")
);
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$status = (int)$request['status'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$keyword = $request['keyword'];
if(empty($request['order2'])){
	$order1 = 'id';
	$order2 = 'desc';
}
$sql = "select * from demo_level_shenqing where comId=$comId ";
if(!empty($status)){
	if($status==2)$status=0;
	$sql.=" and status=$status";
}
if(!empty($keyword)){
	$sql.=" and content like '%$keyword%'";
}
if(!empty($startTime)){
	$sql.=" and dtTime>='$startTime 00:00:00'";
}
if(!empty($endTime)){
	$sql.=" and dtTime<='$endTime 23:59:59'";
}
$sql.=" order by $order1 $order2";
file_put_contents('request.txt',$sql);
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
			$statusInfo = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$statusInfo = '<font color="red">待审核</font>';
				break;
				case 1:
					$statusInfo = '<font color="green">已审核</font>';
				break;
				case -1:
					$j->layclass = 'deleted';
					$statusInfo = '<font>未通过</font>';
				break;
			}
			$j->statusInfo = $statusInfo;
			$content = json_decode($j->content);
			$j->nickname = $content->name;
			$j->username = $content->phone;
			$j->address = $content->address;
			
			$j->shenfenzheng ='<a href="'.$content->img_id.'" target="_blank">查看</a>';
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
