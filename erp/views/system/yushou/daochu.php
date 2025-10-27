<?php
global $db,$request,$adminRole,$qx_arry;
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition:attachment; filename=订单未发货列表.xls");
$allRows = array(
	"orderId"=>array("title"=>"发货单号","rowCode"=>"{field:'orderId',title:'发货单号',width:200,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"product"=>array("title"=>"产品详情","rowCode"=>"{field:'product',title:'产品详情',width:200,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"username"=>array("title"=>"收货人","rowCode"=>"{field:'username',title:'收货人',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"telephone"=>array("title"=>"收货人电话","rowCode"=>"{field:'telephone',title:'收货人电话',width:250,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
	"address"=>array("title"=>"收货人地址","rowCode"=>"{field:'address',title:'收货人地址',width:100}"),
	"remark"=>array("title"=>"客户备注","rowCode"=>"{field:'remark',title:'客户备注',width:150}"),
	"dtTime"=>array("title"=>"成单时间","rowCode"=>"{field:'dtTime',title:'成单时间',width:100}"),
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
$sql="select * from order_fahuo$fenbiao where id in (".$ids.") and comId=$comId and status=0";
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
		$db->query("update order_fahuo$fenbiao set status=1,print_type=1,kuaidi_type=1 where id in (".$ids.") and comId=$comId and status=0");
		$fahuo_pici = array();
		$fahuo_pici['comId'] = $comId;
		$fahuo_pici['type'] = 1;
		$fahuo_pici['orderId'] = date("YmdHis").rand(1000000000,9999999999);//批次Id;
		$fahuo_pici['fahuoIds'] = $ids;
		$fahuo_pici['num'] = count($pdts);
		$fahuo_pici['realNum'] = $fahuo_pici['num'];
		$fahuo_pici['faliNum'] = 0;
		$fahuo_pici['dtTime'] = date("Y-m-d H:i:s");
		$fahuo_pici['storeId'] = $pdts[0]->storeId;
		$fahuo_pici['mendianId'] = $pdts[0]->mendianId;
		$fahuo_pici['username'] = $_SESSION[TB_PREFIX.'name'];
		$db->insert_update('fahuo_pici'.$fenbiao,$fahuo_pici,'id');
		foreach ($pdts as $pdt){
			addJilu($pdt->id,$fenbiao,1,'导出未发货订单','导出未发货订单');
			$k_title = '';
			$product_list = array();
			$db->query("update order$fenbiao set status=3 where id in (".$pdt->orderIds.")");
			$dingdan = $db->get_results("select pdtInfo,num from order_detail$fenbiao where orderId in (".$pdt->orderIds.")");
			foreach($dingdan as $o){
			   $pdt_old = json_decode($o->pdtInfo);
			   if(!empty($product_list[$pdt_old->sn])){
			       //增加它的数量
			      $product_list[$pdt_old->sn]['num']+=$o->num;
			   }else{
			       $arr = array();
			       $arr['num'] = $o->num;
			       $arr['title'] = $pdt_old->title;
			       $arr['key_vals'] = $pdt_old->key_vals;
			       $product_list[$pdt_old->sn] = $arr;
			   }
			}
			foreach ($product_list as $k => $v) {
				$k_title .= $v['title'].'['.$v['key_vals'].'] * '.$v['num'].',';
			}
			$k_title = substr($k_title, 0,strlen($k_title)-1);
			$pdt->product = $k_title;
			$pdtInfo = json_decode($pdt->pdtInfo);
			$pdt->key_vals = $pdtInfo->key_vals;
			$pdt->dtTime = date("Y-m-d H:i",strtotime($pdt->dtTime));
			$shuohuo_json = json_decode($pdt->shuohuo_json,true);
			$pdt->address = $shuohuo_json['所在地区'].$shuohuo_json['详细地址'];
			$pdt->address = str_replace("内蒙古省", "内蒙古自治区", $pdt->address);
			$pdt->address = str_replace("【", "-", $pdt->address);
			$pdt->address = str_replace("】", "-", $pdt->address);
			$pdt->username = $shuohuo_json['收件人'];
			$pdt->telephone = $shuohuo_json['手机号'];
			?>
			<tr>
				<?
				foreach ($allRows as $row=>$isshow){
					?>
					<td style="vnd.ms-excel.numberformat:@"><?=$pdt->$row?></td>
					<?
				}
				?>
			</tr>
			<?
		}
	}
	?>
</tbody></table>