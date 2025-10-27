<?php
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$fenbiao = getFenbiao($comId,20);
$keyword = $request['keyword'];
$areaId = $request['areaId'];
$level = $request['level'];
$kehuStatus = $request['kehuStatus'];
$zong = $db->get_results("select type,sum(money) as money from demo_kehu_account where comId=$comId group by type");
$zong1 = 0.00;
$zong2 = 0.00;
$zong3 = 0.00;
$zong4 = 0.00;
if(!empty($zong)){
    foreach ($zong as $z) {
        if($z->type==1){
            $zong1 = $z->money;
        }else if($z->type==2){
            $zong2 = $z->money;
        }else if($z->type==3){
            $zong3 = $z->money;
        }else if($z->type==4){
            $zong4 = $z->money;
        }
    }
}
$kehu_shezhi = $db->get_row("select * from demo_kehu_shezhi where comId=$comId");
$sql="select id,title from demo_kehu where comId=$comId";
if(!empty($keyword)){
	$sql.=" and title like '%$keyword%'";
}
if(!empty($areaId)){
	$areaIds = $areaId.getZiAreas($areaId);
	$sql.=" and areaId in($areaIds)";
}
if(!empty($level)){
	$sql.=" and level=$level";
}
if(!empty($kehuStatus)){
	if($kehuStatus==2)$kehuStatus=0;
	$sql.=" and status=$kehuStatus";
}
$sql.=" order by id desc ";
$jilus = $db->get_results($sql);
header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment; filename=订单收款统计.xls");
$allRows = array(
	"title"=>$kehu_title."名称",
	"account1"=>$kehu_shezhi->acc_xianjin_name,
	"account2"=>$kehu_shezhi->acc_yufu_name,
	"account3"=>$kehu_shezhi->acc_fandian_name,
	"account4"=>$kehu_shezhi->acc_baozheng_name
);
?>
<table border="1">
	<tbody>
		<tr><td colspan="5">
			<?=$kehu_shezhi->acc_xianjin_name?>余额总计：<?=$zong1?><Br>
			<?=$kehu_shezhi->acc_yufu_name?>余额总计：<?=$zong2?><Br>
			<?=$kehu_shezhi->acc_fandian_name?>余额总计：<?=$zong3?><Br>
			<?=$kehu_shezhi->acc_baozheng_name?>余额总计：<?=$zong4?><Br>
		</td></tr>
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
				$j->account1 = 0;
				$j->account2 = 0;
				$j->account3 = 0;
				$j->account4 = 0;
				$accounts = $db->get_results("select type,money from demo_kehu_account where kehuId=$j->id and type in(1,2,3,4) limit 4");
				if(!empty($accounts)){
					foreach ($accounts as $a){
						switch ($a->type) {
							case 1:
							$j->account1 = $a->money;
							break;
							case 2:
							$j->account2 = $a->money;
							break;
							case 3:
							$j->account3 = $a->money;
							break;
							case 4:
							$j->account4 = $a->money;
							break;
						}
					}
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
