<?php
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=".$kehu_title."列表.xls");
global $db,$request;
$allRows = array(
				"title"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'title',title:'".$kehu_title."名称',width:200,sort:true}"),
				"sn"=>array("title"=>$kehu_title."编码","rowCode"=>"{field:'sn',title:'".$kehu_title."编码',width:200,sort:true}"),
				"username"=>array("title"=>"登录账号","rowCode"=>"{field:'username',title:'登录账号',width:200}"),
				"areaName"=>array("title"=>"地区","rowCode"=>"{field:'areaName',title:'地区',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"level"=>array("title"=>"级别","rowCode"=>"{field:'level',title:'级别',width:100}"),
				"name"=>array("title"=>"联系人","rowCode"=>"{field:'name',title:'联系人',width:100,sort:true}"),
				"phone"=>array("title"=>"联系方式","rowCode"=>"{field:'phone',title:'联系方式',width:120,sort:true}"),
				"dtTime"=>array("title"=>"创建时间","rowCode"=>"{field:'dtTime',title:'创建时间',width:150,sort:true}"),
				"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:100}")
			);
$comId = $_SESSION[TB_PREFIX.'comId'];
$level = (int)$request['level'];
$keyword = $request['keyword'];
$uname = $request['uname'];
$areaId = (int)$request['areaId'];
$status = (int)$request['status'];
$order1 = 'id';
$order2 = 'desc';
$sql="select id,title,sn,username,areaId,level,name,phone,dtTime,status from demo_kehu where comId=$comId ";
	if(!empty($level)){
		$sql.=" and level=$level";
	}
	if(!empty($status)){
		if($status==2)$status=0;
		$sql.=" and status=$status";
	}
	if(!empty($keyword)){
		$sql.=" and (title like '%$keyword%' or sn like '%$keyword%' or name like '%$keyword%' or phone like '$keyword')";
	}
	if(!empty($uname)){
		$sql.=" and uname like '%$uname%'";
	}
	if(!empty($areaId)){
		$areaIds = $areaId.getZiAreas($areaId);
		$sql.=" and areaId in($areaIds)";
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
			$status = '';
			$j->layclass = '';
			switch ($j->status){
				case -1:
					$j->layclass = 'deleted';
					$status = '<span style="color:red">已禁用</span>';
				break;
				case 0:
					$status = '未开通';
				break;
				case 1:
					$status = '<span style="color:green">已开通</span>';
				break;
			}
			$j->status = $status;
			$j->dtTime = date("Y-m-d",strtotime($j->dtTime));
			$j->level = $db->get_var("select title from demo_kehu_level where id=$j->level");
			$j->areaName = '';
			if(!empty($j->areaId)){
				$area = $db->get_row("select title,parentId from demo_area where id=$j->areaId");
				if(!empty($area)){
					$j->areaName = $area->title;
					if(!empty($area->parentId)){
						$area1 = $db->get_row("select title,parentId from demo_area where id=$area->parentId");
						if(!empty($area1)){
							$j->areaName = $area1->title.$j->areaName;
							if(!empty($area1->parentId)){
								$area2 = $db->get_var("select title from demo_area where id=$area1->parentId");
								if(!empty($area2)){
									$j->areaName = $area2.$j->areaName;
								}
							}
						}
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
