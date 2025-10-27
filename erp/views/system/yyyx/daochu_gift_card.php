<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$id = (int)$request['id'];
$title = $db->get_var("select title from gift_card_jilu where id=$id");
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment; filename=".$title."-礼品卡数据.xls");
$allRows = array(
	"typeInfo"=>array("title"=>"礼品卡名称","rowCode"=>"{field:'typeInfo',title:'礼品卡名称',width:240}"),
	"cardId"=>array("title"=>"卡号","rowCode"=>"{field:'cardId',title:'卡号',width:150}"),
	"money"=>array("title"=>"面额（元）","rowCode"=>"{field:'money',title:'面额（元）',width:100}"),
	"yue"=>array("title"=>"余额（元）","rowCode"=>"{field:'yue',title:'余额（元）',width:100}"),
	"binduser"=>array("title"=>"绑定帐号","rowCode"=>"{field:'binduser',title:'绑定帐号',width:150}"),
	"bind_time"=>array("title"=>"绑定时间","rowCode"=>"{field:'bind_time',title:'绑定时间',width:180}")
);
$sql = "select * from gift_card$fenbiao where comId=$comId and jiluId=$id";
$jilus = $db->get_results($sql);
?>
<table border="1">
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
	if(!empty($jilus)){
		foreach ($jilus as $j){
			$j->title = $title;
			if($j->userId>0){
				$j->binduser = $db->get_var("select nickname from users where id=$j->userId");
				$j->bind_time = date("Y-m-d H:i",strtotime($j->bind_time));
			}
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
