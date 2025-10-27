<?php
    global $db,$request;
    //让程序一直运行
    set_time_limit(0);
    //设置程序运行内存
    ini_set('memory_limit', '128M');
    ob_clean();
    ob_end_clean();
    $fileName = '产品数据导出';
    header('Content-Encoding: UTF-8');
    header("Content-type:application/vnd.ms-excel;charset=UTF-8");
    header('Content-Disposition: attachment;filename="' . $fileName . '.csv"');

    //打开php标准输出流
    $fp = fopen('php://output', 'a');
 
    //添加BOM头，以UTF8编码导出CSV文件，如果文件头未添加BOM头，打开会出现乱码。
    fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));
    //添加导出标题
    fputcsv($fp, ['商品编码', '商品名称','商品规格','零售价', '市场价','库存数量','所属分类','排序', '状态','创建时间']);
 
    //链接数据库，换成你的
    
    $level = (int)$request['level'];
	$mendianId = (int)$request['mendianId'];
	$shangji = (int)$request['shangji'];
	$keyword = $request['keyword'];
	$money_start = $request['money_start'];
	$money_end = $request['money_end'];
	$jifen_start = (int)$request['jifen_start'];
	$jifen_end = (int)$request['jifen_end'];
	$dtTime_start = $request['dtTime_start'];
	$dtTime_end = $request['dtTime_end'];
	$login_start = $request['login_start'];
	$login_end = $request['login_end'];
// 	echo '<pre>';
// 	var_dump($request);die;
	
    $sql = 'SELECT i.id,i.productId,i.channelId,i.title,i.key_vals,i.sn,i.price_sale,i.price_market,i.status,i.ordering,i.dtTime,i.updateTime,k.kucun FROM `demo_product_inventory` i left join demo_kucun k on k.inventoryId = i.id where 1=1 ';
    
    $channelId = (int)$request['channelId'];
    $status = (int)$request['status'];
    $keyword = $request['keyword'];
    
    if(!empty($channelId)){
		$channelIds = $channelId.getZiIds($channelId);
		$sql.=" and i.channelId in($channelIds)";
	}

	if(!empty($status)){
		$sql.=" and i.status=$status";
	}
	if(!empty($keyword)){
		$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
		if(empty($pdtIds))$pdtIds='0';
		
		$pdtIds1 = $db->get_var("select group_concat(id) from demo_product where skuId like '%$keyword%' ");
		if(empty($pdtIds1)) $pdtIds1 = 0;
		
		$sql.=" and (i.title like '%$keyword%' or i.sn='$keyword' or i.key_vals like '%$keyword%' or i.productId in($pdtIds) or i.productId in ($pdtIds1) )";
	}
    
//     if(!empty($level)){
//     	$sql.=" and level=$level and if_certification = 1 ";
//     }
//     if(!empty($keyword)){
//     	$sql.=" and (nickname like '%$keyword%' or phone like '%$keyword%')";
//     }
//     if(!empty($mendianId)){
//     	$sql.=" and mendianId=$mendianId";
//     }
//     if(!empty($money_start)){
//     	$sql.=" and money>='$money_start'";
//     }
//     if(!empty($money_end)){
//     	$sql.=" and money<='$money_end'";
//     }
//     if(!empty($jifen_start)){
//     	$sql.=" and jifen>=$jifen_start";
//     }
//     if(!empty($jifen_end)){
//     	$sql.=" and jifen<=$jifen_end";
//     }
//     if(!empty($dtTime_start)){
//     	$sql.=" and dtTime>='$dtTime_start'";
//     }
//     if(!empty($dtTime_end)){
//     	$sql.=" and dtTime<='$dtTime_end'";
//     }
    
//     $sex = (int)$request['sex'];
// 	if($sex > 0){
// 	    $sql .= " and sex = $sex ";
// 	}
	
// 	$industry = $request['industry'];
// 	if($industry){
// 	    $sql .= " and industry = '$industry' ";
// 	}
	
// 	$hobby = $request['hobby'];
// 	if($hobby){
// 	    $sql .= " and hobby like '%$hobby%' ";
// 	}
	
// 	$freedom = $request['freedom'];
// 	if($freedom){
// 	    $sql .= " and freedom = '$freedom' ";
// 	}
	
// 	$if_certification = (int)$request['if_certification'];
// 	if(in_array($if_certification, [0,1,2,-1])){
// 	    $sql .= " and if_certification = $if_certification ";
// 	}

    $step = 60; //循环次数
    $nums = 1000; //每次导出数量
 
   // 循环次数 >= 导出次数，是效率最高的
    $channels = $db->get_results("SELECT id, title from demo_product_channel ");
    $channelArr = [];
    foreach ($channels as $level){
        $channelArr[$level->id] = $level->title;
    }
    
    // $sexArr = ['未设置', '男', '女'];
    // $renzhengArr = [0 => '未认证', 1=> '已认证', 2=> '待审核', -1 => '审核失败'];
 
    for($i = 0; $i < $step; $i++) {
        $start = $i * 1000;
        $lastSql = $sql. " ORDER BY i.ordering desc,i.id desc LIMIT {$start},{$nums}";
        
        $result = $db->get_results($lastSql);
        foreach ($result as $item) {
            $status = $item->status == 1 ? '已上架':'已下架';
            // $level = $levelArr[$item->level];
            // if($item->if_certification != 1){
            //     $level = '普通用户';
            // }
            $temp = array(//  fputcsv($fp, ['商品编码', '商品名称','商品规格','零售价', '市场价','库存数量','所属分类','排序', '状态','创建时间']);
                $item->sn,
                $item->title,
                $item->key_vals,
                $item->price_sale,
                $item->price_market,
                (int)$item->kucun,
                $channelArr[$item->channelId],
                $item->ordering,
                $status,
                $item->dtTime
            );
            fputcsv($fp, $temp);
        }
        //每1万条数据就刷新缓冲区
        if (ob_get_level() > 0) {
            ob_flush();
        }
        flush();
    }