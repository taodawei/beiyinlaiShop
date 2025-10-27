<?php
global $db,$request,$adminRole,$qx_arry;
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=兑换单未发货列表.xls");
$allRows = array(
	"orderId"=>array("title"=>"发货单号","rowCode"=>"{field:'orderId',title:'发货单号',width:150}"),
	"shouhuo"=>array("title"=>"收货人","rowCode"=>"{field:'shouhuo',title:'收货人',width:150}"),
	"tel"=>array("title"=>"收货人电话","rowCode"=>"{field:'tel',title:'收货人电话',width:150}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:242}"),
	"pdt_info"=>array("title"=>"兑换商品","rowCode"=>"{field:'pdt_info',title:'兑换商品',width:220}"),
	"dtTime"=>array("title"=>"成单时间","rowCode"=>"{field:'dtTime',title:'成单时间',width:159}"),
	"kuaidi_company"=>array("title"=>"物流公司","rowCode"=>"{field:'kuaidi_company',title:'物流公司',width:200,sort:true}"),
	"kuaidi_order"=>array("title"=>"物流单号","rowCode"=>"{field:'kuaidi_order',title:'物流单号',width:100}"),
);
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select price_num,number_num,showRows from demo_product_set where comId=$comId");
}
$fenbiao = getFenBiao($comId,20);
$ids = $request['ids'];
$sql="select * from kmd_change_log where id in (".$ids.") and 1=1 and status in(0,1)";
$sql.=" order by id desc";
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
		$db->query("update kmd_change_log set print_type=1 where id in (".$ids.") and 1=1 and status=0");
// 		$fahuo_pici = array();
// 		$fahuo_pici['comId'] = $comId;
// 		$fahuo_pici['type'] = 1;
// 		$fahuo_pici['orderId'] = date("YmdHis").rand(1000000000,9999999999);//批次Id;
// 		$fahuo_pici['fahuoIds'] = $ids;
// 		$fahuo_pici['num'] = count($pdts);
// 		$fahuo_pici['realNum'] = $fahuo_pici['num'];
// 		$fahuo_pici['faliNum'] = 0;
// 		$fahuo_pici['dtTime'] = date("Y-m-d H:i:s");
// 		$fahuo_pici['storeId'] = $pdts[0]->storeId;
// 		$fahuo_pici['mendianId'] = $pdts[0]->mendianId;
// 		$fahuo_pici['username'] = $_SESSION[TB_PREFIX.'name'];
// 		$db->insert_update('fahuo_pici'.$fenbiao,$fahuo_pici,'id');
		foreach ($pdts as $j){
	        $j->dtTime = date("Y-m-d H:i",strtotime($j->dtTime));
			$status = '';
			$j->layclass = '';
			switch ($j->status) {
				case 0:
					$status = '<span style="color:#ff3333;">待发货</span>';
				break;
				case 1:
					$status = '<span style="color:#ff3333;">待上传/确认</span>';
				break;
				case 2:
					$status = '<span style="color:#ff3333;">已配货</span>';
				break;
				case 3:
					$status = '<span style="color:#ff3333;">已完成</span>';
				break;
				case -1:
					$status = '<span style="color:green;">无效</span>';
				break;
				case -2:
					$status = '<span style="color:#f00;">暂停</span>';
				break;
			} 
			$zero1=strtotime (date("Y-m-d h:i:s")); //当前时间  ,注意H 是24小时 h是12小时 
			$zero2=strtotime ($j->dtTime);  //过年时间，不能写2014-1-21 24:00:00  这样不对 
			$j->days=abs(ceil(($zero1-$zero2)/86400)).'天'; //60s*60min*24h   
			if($j->status==0){
				$j->daochutype='待导出';
			}else{
				$j->daochutype='已导出';
			}
			$j->dayinStatus = '未打印';
			$j->status_info = $status;
			$j->beizhu = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->remark);
			$j->beizhu = str_replace('"','',$j->beizhu);
			$j->beizhu = str_replace("'",'',$j->beizhu);
			$shuohuo_json = json_decode($j->shouhuo_json,true);
			if(strpos($shuohuo_json['详细地址'],'【')===false){
				//$xiaoqu = $db->get_var("select title from user_address where id=$j->addressId");
				if(!empty($xiaoqu))$shuohuo_json['详细地址'] = $shuohuo_json['详细地址'];
			}
			if($j->is_hebing==1){
				$j->orderId='<span style="color:#f00;">'.$j->orderId.'</span>';
			}
			$j->address = $shuohuo_json['address'];
			$j->shouhuo = $shuohuo_json['name'];
			$j->tel = $shuohuo_json['mobile'];
			$j->beizhu = '<span onmouseover="tips(this,\''.$j->beizhu.'\',1);" onmouseout="hideTips()">'.sys_substr(strip_tags($j->beizhu),20,true).'</span>';
			$j->mendian = $db->get_var("select title from mendian where id=$j->mendianId");
		
			$pdtInfo = json_decode($j->pdtInfo);
			
			$j->pdt_info = '';
			if(!empty($pdtInfo)){
			    $j->pdt_info = $pdtInfo->title."-".$pdtInfo->key_vals."*".$pdtInfo->num; 
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