<?php
namespace Zhishang;

class Product{
    
    public function allRow()
    {
        global $db,$request;
        

        $allkeys = $db->get_results("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'demo_product_params'");
        
        $rowsStr = '';
        foreach ($allkeys as $nowKey){
            $currentKey = $nowKey->COLUMN_NAME;
            if(in_array($currentKey, ['id', 'channelId', 'productId', 'brandId', 'productId'])){
                continue;
            }

            $rowsStr .= "'".$currentKey."',";
        }
        
        $rowsStr = rtrim($rowsStr, ',');
   
        $data = $db->get_results("select * from demo_product_fields where field_title in ($rowsStr) and is_del = 0 order by ordering desc ");
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = $data;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function getRow()
    {
        global $db,$request;
        
		$channelId = (int)$request['channel_id'];
        $channel = $db->get_row("select * from demo_product_channel where id = $channelId");
        if(!$channel){
            return '{"code":"0","message":"未找到分类信息"}';
        }
        
        $data = array();
        if(empty($channel->rowDatas) && $channel->parentId > 0){
            $parentChannel = $db->get_row("select * from demo_product_channel where id = $channel->parentId");
        
            $rows = $parentChannel->rowDatas;
        }elseif ($channel->parentId == 0) {
            $rows = $db->get_var("select rowDatas from demo_product_channel where parentId = $channel->id and rowDatas <> '' ");
            if(!$rows) $rows = '';
        }else{
            $rows = $channel->rowDatas;
        }

        if($rows){
            $rows = explode(',', $rows);
            $total = count($rows);
            $rowsStr = '';
            foreach ($rows as $k => $row){
                $rowsStr .= "'".$row."',";
            }
            
            $rowsStr = rtrim($rowsStr, ',');
            $data = $db->get_results("select * from demo_product_fields where field_title in ($rowsStr) and is_del = 0 order by ordering desc ");
        }
        
		
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = $data;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function getSearch()
    {
        global $db,$request;
        
		$channelId = (int)$request['channel_id'];
        $channel = $db->get_row("select * from demo_product_channel where id = $channelId");
        if(!$channel){
            return '{"code":"0","message":"未找到分类信息"}';
        }
        
        if(empty($channel->searchDatas) && $channel->parentId > 0){
            $channel->searchDatas = $db->get_var("select searchDatas from demo_product_channel where id = $channel->parentId");
        }
        
        if(empty($channel->searchDatas)){
            $return = array();
    		$return['code'] = 1;
    		$return['message'] = '返回成功';
    		$return['data'] = array();
    		
    		return json_encode($return,JSON_UNESCAPED_UNICODE);
        }
        
        $allSearchs = $db->get_results("select * from demo_search_channel where id in ($channel->searchDatas) order by ordering desc ");
        if($allSearchs){
            foreach ($allSearchs as &$search){
                $search->child = $db->get_results("select * from demo_search_channel where parentId = $search->id order by ordering desc ");
            }
        }
		
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = $allSearchs;
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
    }

	public function channel()
	{
		global $db,$request,$comId;
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = array();
		if(false){
			$content = file_get_contents(ABSPATH."cache/channels_$comId.php");
  			$channels = json_decode($content);
  			$return['data'] = $channels;
		}else{
			$departments = $db->get_results("select * from demo_product_channel where comId=$comId and parentId=0   order by ordering desc,id asc");
			$departs = array();
			if(!empty($departments)){
				foreach($departments as $department){
				    $department->originalPic=HTTP_URL.$department->originalPic;
					$departments1 = $db->get_results("select * from demo_product_channel where parentId=".$department->id."    order by ordering desc,id asc");
					$departs1=array();
					if(!empty($departments1)){
						foreach($departments1 as $department1){
						    $department1->originalPic=HTTP_URL.$department1->originalPic;
							$departments2 = $db->get_results("select * from demo_product_channel where parentId=".$department1->id."   order by ordering desc,id asc");
							$departs2 = array();
							if(!empty($departments2)){
								foreach($departments2 as $department2){
								    $department2->originalPic=HTTP_URL.$department2->originalPic;
									$departments3 = $db->get_results("select * from demo_product_channel where parentId=".$department2->id."   order by ordering desc,id asc");
									if(!empty($departments3)){
										$department2->channels = $departments3;
									}else{
										$department2->channels = array();
									}
									$departs2[]=$department2;
								}
							}
							if(!empty($departs2)){
								$department1->channels = $departs2;
							}else{
								$department1->channels = array();
							}
							$departs1[]=$department1;
						}
					}
					if(!empty($departs1)){
						$department->channels = $departs1;
					}else{
						$department->channels = array();
					}
					$departs[] = $department;
				}
			}
			$return['data'] = $departs;
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function hotChannel()
	{
	    global $db,$request,$comId;
		
        $fenbiao = getFenbiao($comId,20);
        
        $channels = $db->get_results("select * from demo_product_channel where comId=$comId and is_hot = 1 order by ordering desc,id asc");
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $channels;

        return json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	
	public function searchPlist()
	{
	    global $db,$request,$comId;
		
        $fenbiao = getFenbiao($comId,20);
        $keyword = $request['keyword'];
        
        $page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		
		$sql = "select id, title, skuId from demo_product where (title like '%$keyword%' or skuId like '%$keyword%') and status =1 ";
		

		$channelId = (int)$request['channel_id'];
		if($channelId){
		    $channelIds = $channelId.self::getZiIds($channelId);
			$sql.=" and channelId in($channelIds)";
		}



		$count = $db->get_var(str_replace('id, title, skuId','count(distinct(id))',$sql));

		$sql.=" order by ordering desc limit ".(($page-1)*$pageNum).",".$pageNum;
		
        $products = $db->get_results("select id, title, skuId from demo_product where title like '%$keyword%' or skuId like '%$keyword%' limit 20 ");
        if($products){
            foreach ($products as &$pro){
                $pro->inventoryId = $db->get_var("select id from demo_product_inventory where productId = $pro->id  limit 1 ");
            }
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['data'] = array(
            'count' => $count,
            'list' => $products
        );

        return json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	
	public function plistNew()
	{
	    global $db,$request,$comId;
		
        $fenbiao = getFenbiao($comId,20);
		$channelId = (int)$request['channel_id'];
	    $is_relative_id = (int)$request['is_relative_id'];
		$tags = $request['tags'];
		$yhq_id = (int)$request['yhq_id'];
		$keyword = $request['keyword'];
		
		$shoucang = (int)$request['shoucang'];//我的收藏
		$history = (int)$request['history'];//浏览历史
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$order1 = empty($request['order1'])?'ordering':$request['order1'];
		$order2 = empty($request['order2'])?'desc':$request['order2'];
		$user_level =0;
		$sale_area = 0;
		$userId = (int)$request['user_id'];
		if(!empty($request['user_id'])){
			$user_level = $db->get_var("select level from users where id=$userId");
			$sale_area = (int)$db->get_var("select areaId from user_address where comId=$comId and userId=$userId order by moren desc,id desc limit 1");
		}
		if($order1=='title'){
			$order1 = 'CONVERT(title USING gbk)';
		}
		if(empty($request['order2'])){
			$order1 = 'ordering';
			$order2 = 'desc,i.id desc';
		}
		if(!empty($request['miaoshaId']) && $request['rand']==1){
			$order1 = 'rand()';
			$order2 = '';
		}

		//是否缓存结果，如果满足条件直接读取缓存文件，减少数据库压力

		$sql="select i.productId,min(i.id) as inventoryId,min(i.price_sale) as price_sale,min(i.fanli_shequ) as fanli_shequ,sum(i.orders) as orders,sum(i.views) as views,i.title,i.dtTime,i.image,i.price_market,i.comId,i.ordering from demo_product_inventory i left join demo_product_params p on p.productId = i.productId where  i.if_lingshou=1 "; //779 积分产品
		
		$sql="select i.productId,i.id as inventoryId,i.price_sale as price_sale,i.fanli_shequ as fanli_shequ,i.orders as orders,i.views as views,i.title,i.dtTime,i.image,i.price_market,i.comId,i.ordering from demo_product_inventory i left join demo_product_params p on p.productId = i.productId where  i.if_lingshou=1 "; //779
        //	$spliteKeys = ['reaction_species', 'host_species', 'immunogen_species', 'useTo'];
        $allkeys = $db->get_results("select COLUMN_NAME from information_schema.COLUMNS where table_name = 'demo_product_params'");
        foreach ($allkeys as $nowKey){
            $currentKey = $nowKey->COLUMN_NAME;
            if(in_array($currentKey, ['id', 'channelId', 'productId', 'brandId', 'productId'])){
                continue;
            }

            if(isset($request[$currentKey]) && !empty($request[$currentKey])){
                $rowDatas = explode(',', $request[$currentKey]);
                $sql .= " and ( ";
                if($rowDatas){
                    foreach ($rowDatas as $rowData){
                        $sql .= " $currentKey like '%$rowData%' and";
                    }
                    $sql = rtrim($sql, 'and')." ) ";
                }
            }
        }
// echo  $sql;die;
		if(!empty($yhq_id)){
			$yhq_comId = $_SESSION['if_tongbu']==1?10:$comId;
			$fenbiao = getFenbiao($comId,20);
			$yhq=$db->get_row("select * from yhq where id=(select jiluId from user_yhq$fenbiao where id=$yhq_id)");
			if(!empty($yhq->mendianIds)){
				$sql.=" and comId in($yhq->mendianIds)";
			}
			if($yhq->useType>1){
				if(!empty($yhq->channels)){
					$sql.=" and i.channelId in($yhq->channels)";
				}
			}
			if(!empty($yhq->pdts)){
				$sql.=" and i.id in($yhq->pdts)";
			}
		}
	    if($is_relative_id){
		     $sql.=" and i.id  != $is_relative_id";   
		}
		if(!empty($channelId)){
			$channelIds = $channelId.self::getZiIds($channelId);
			$sql.=" and i.channelId in($channelIds)";
		}
		
		$yi_id_arr = explode(',','855'.self::getZiIds(855)); //仪器
		
	    $sj_id_arr = explode(',','853'.self::getZiIds(853)); //试剂盒
		
		if(!empty($keyword)){
		    $keyword = trim($keyword, " ");
		    $keyword = trim($keyword, " ");
		    
			$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
			if(empty($pdtIds))$pdtIds='0';
			
			$pdtIds1 = $db->get_var("select group_concat(id) from demo_product where title like '%$keyword%' or skuId like '%$keyword%' ");
			if(empty($pdtIds1))$pdtIds1='0';
			
			$pdtIds2 = $db->get_var("select group_concat(productId) from demo_product_params where synonym like '%$keyword%' or gene_id like '%$keyword%' or another_name like '%$keyword%' ");
			if(empty($pdtIds2))$pdtIds2='0';
			
			$pdtIds = rtrim($pdtIds, ',');
			$pdtIds1 = rtrim($pdtIds1, ',');
			$pdtIds2 = rtrim($pdtIds2, ',');
// 			var_dump($pdtIds, $pdtIds1, $pdtIds2);die;
			
			$sql.=" and (i.title like '%$keyword%' or i.productId in($pdtIds) or i.productId in ($pdtIds1) or i.productId in ($pdtIds2) )";
			//search_history($keyword);
		}
		if(!empty($tags)){
			$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and tags like '%$tags%'");
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and i.productId in($pdtIds)";
		}
		
		$brandId = $request['brandId'] ? $request['brandId'] : (int)$request['brand_id'];
		if(!empty($brandId)){
			$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=".$brandId);
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and i.productId in($pdtIds)";
		}
		
		
		if($shoucang==1){
			$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_collect where userId=$userId and comId=$comId");
			if(empty($shoucangIds))$shoucangIds='0';
			$sql.=" and i.id in($shoucangIds)";
		}
		if($history==1){
			$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_history where userId=$userId and comId=$comId");
			if(empty($shoucangIds))$shoucangIds='0';
			$sql.=" and i.id in($shoucangIds)";
		}
	
// 		$sql.=" and i.status=1";
		

// 		$count = $db->get_var(str_replace('i.productId,min(i.id) as inventoryId,min(i.price_sale) as price_sale,min(i.fanli_shequ) as fanli_shequ,sum(i.orders) as orders,sum(i.views) as views,i.title,i.dtTime,i.image,i.price_market,i.comId,i.ordering','count(distinct(i.productId))',$sql));
		
// 		echo str_replace('i.productId,i.id as inventoryId,i.price_sale as price_sale,i.fanli_shequ as fanli_shequ,i.orders as orders,i.views as views,i.title,i.dtTime,i.image,i.price_market,i.comId,i.ordering','count(distinct(i.id))',$sql);die;
		$count = $db->get_var(str_replace('i.productId,i.id as inventoryId,i.price_sale as price_sale,i.fanli_shequ as fanli_shequ,i.orders as orders,i.views as views,i.title,i.dtTime,i.image,i.price_market,i.comId,i.ordering','count(distinct(i.productId))',$sql));
		$sql.=" group by i.productId";
		
		$sql.=" order by i.$order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;   
        // echo $sql;die;
		$pdts = $db->get_results($sql);

		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回数据成功';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['shoucang'] = $shoucang;
		$return['data'] = array();
		//$zhekou = get_user_zhekou();
		$now = date("Y-m-d H:i:s");
		/*分享佣金比例*/
		//$user_bili = $db->get_row("select shangji_bili,shang_bili from demo_shezhi where comId=$comId");
		//$shangji_bili = 100-$user_bili->shangji_bili-$user_bili->shang_bili;
  		$spliteKeys = ['reaction_species', 'host_species', 'immunogen_species', 'useTo'];
  		$paramData_ = array();
		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$inventory = $db->get_row("select price_market,fenleiId,key_vals,orders,price_card,originalPic from demo_product_inventory where id=$pdt->inventoryId");
				$pro = $db->get_row("select brandId,price_name,originalPic,untis,remark,subtitle,yunfei_moban_ding,skuId,skuDay,channelId,book_url,originalPic,skuId from demo_product where id=$pdt->productId");
				$paramData = $db->get_row("select * from demo_product_params where productId = $pdt->productId ");
				
				
				$data = array();
				$data['id'] = $pdt->productId;
				$data['skuId'] = $pro->skuId;
			    $data['channelId'] = $pro->channelId;
				$data['paper_num'] = 0;
				if($pro->skuId){
				    $request['sku'] = $data['skuId'];
				    
				    $paperData =\Zhishang\Paper::sku();
				    $paperData = json_decode($paperData, true);
				    
				    $data['paper_num'] = $paperData['count'];
				}
				$data['skuDay'] = $pro->skuDay;
				$data['book_url'] = $pro->book_url;
				$data['title'] = $pdt->title;
				

			    //获取上级分类id
        		  //864  863  853    861  862
    		    $root_id = 864;
    		    $channelId = $db->get_row("select parentId,miaoshu_originalPic from demo_product_channel where id = $pro->channelId"); 
    		    if($pro->channelId == 861 || $pro->channelId == 862){
    		        $root_id = $pro->channelId;
    		    }else{
    		      
    		       $root_id = $channelId->parentId;
    		    }
    		    $originalPic_ = $channelId->miaoshu_originalPic;
    		  //  $objectUrl = "product/$root_id/$product->skuId/";
        //         $fileList = listObjectsFile($objectUrl, 100);
        //         if(!empty($fileList['data'])){
        //              $originalPics = $fileList['data']; 
        //         }
                
                $showPic = '';
                if($pro->originalPic) {
                    $showPic = ispic($pro->originalPic); 
                }elseif($inventory->originalPic){
                    $showPic = ispic($inventory->originalPic); 
                }else{
                    $hadImage = $db->get_var("select originalPic from demo_product_inventory where productId = $product_inventory->productId and originalPic <> '' and originalPic is not null ");
                    if($hadImage) $showPic = ispic($hadImage);
                }    
                
        		if($root_id == 864){
  
        		    $data['default_img'] ="https://admin.bio-swamp.com/upload/抗体.jpg"; 
        		    
        		    
        		    $data['img'] =$showPic? $showPic : "https://bio-swamp.oss-cn-nanjing.aliyuncs.com/product/$root_id/$pro->skuId/$pro->skuId".'_1.jpg'; 
        		    
        		    $data['img'] =$showPic? $showPic : "https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$pro->skuId/$pro->skuId".'_1.jpg'; 
        		    
        		}else{
        		  //  $data['img'] =$showPic? $showPic : $originalPic_;
        		  //  $data['img'] =$showPic? $showPic : "https://bio-swamp.oss-cn-nanjing.aliyuncs.com/product/$root_id/$pro->skuId/$pro->skuId".'_1.jpg'; 
        		    $data['img'] =$showPic? $showPic : "https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$pro->skuId/$pro->skuId".'_1.jpg'; 
        		    $data['default_img'] =$originalPic_;
        		}	    
				
				
				
				
				$data['imgs'] = explode('|',$pro->originalPic);
				$data['inventoryId'] = $pdt->inventoryId;
				$data['is_yiqi'] = in_array( $pro->channelId,$yi_id_arr);
				$data['is_sj'] = in_array( $pro->channelId,$sj_id_arr);
				$data['price_sale'] = self::get_user_zhekou($pdt->inventoryId,$pdt->price_sale,$user_level);
				// var_dump($data['price_sale']);die;
				// var_dump($data['price_sale']);die;
				$data['price_market'] =getXiaoshu($inventory->price_market,2);
				$data['price_user'] = getXiaoshu($data['price_sale']-$inventory->price_card,2);
		        $units_arr = json_decode($pro->untis);
		        $data['unit'] = $units_arr[0]->title;
				$data['orders'] = $pdt->orders;
				$data['remark'] = $pro->remark;
				$data['brand'] = '';
				
				//分隔

				foreach ($spliteKeys as $key){
				    if(isset($paramData->$key)){
				        if(!empty($paramData->$key)){
				            $paramData->$key = explode(',', $paramData->$key);
				        }else{
				            $paramData->$key = [];
				        }
				    }
				}

				
				$data['param_info'] = $paramData;
				
				$brandId = $pro->brandId;
				if($brandId>0){
					$data['brand'] = $db->get_var("select originalPic from demo_product_brand where id=$brandId");
				}
	
				$data['key_vals'] = $inventory->key_vals;
			

				$data['kucun'] = self::get_product_kucun($pdt->inventoryId,$areaId);
				$data['subtitle'] = $pro->subtitle;
				$data['views'] = $pdt->views;
	
				$return['data'][] = $data;
			}
		}
		
		$cache_content = json_encode($return,JSON_UNESCAPED_UNICODE);
	
		return $cache_content;
	}
	
    public function plist(){  
		global $db,$request,$comId;
		
        $fenbiao = getFenbiao($comId,20);
		$channelId = (int)$request['channel_id'];
		$fenleiId = (int)$request['fenleiId'];
		$tags = $request['tags'];
		$cuxiao_id = (int)$request['cuxiao_id'];
		$yhq_id = (int)$request['yhq_id'];
		$lipinka_id = (int)$request['lipinka_id'];
		$keyword = $request['keyword'];
		$miaoshaId = (int)$request['miaosha_id'];//秒杀活动id
		$shoucang = (int)$request['shoucang'];//我的收藏
		$history = (int)$request['history'];//浏览历史
		$if_miaosha = (int)$request['if_miaosha'];
		$if_yushou = (int)$request['if_yushou'];
		$if_cuxiao = (int)$request['if_cuxiao'];
		$if_tuangou = (int)$request['if_tuangou'];
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$order1 = empty($request['order1'])?'ordering':$request['order1'];
		$order2 = empty($request['order2'])?'desc':$request['order2'];
		$user_level =0;
		$sale_area = 0;
		$userId = (int)$request['user_id'];
		if(!empty($request['user_id'])){
			$user_level = $db->get_var("select level from users where id=$userId");
			$sale_area = (int)$db->get_var("select areaId from user_address where comId=$comId and userId=$userId order by moren desc,id desc limit 1");
		}
		if($order1=='title'){
			$order1 = 'CONVERT(title USING gbk)';
		}
		if(empty($request['order2'])){
			$order1 = 'ordering';
			$order2 = 'desc,id desc';
		}
		if(!empty($request['miaoshaId']) && $request['rand']==1){
			$order1 = 'rand()';
			$order2 = '';
		}
		//是否缓存结果，如果满足条件直接读取缓存文件，减少数据库压力
	
		$if_cache = 0;
// 		if(empty($tags) && empty($yhq_id) && empty($lipinka_id) && empty($keyword) && empty($shoucang) && empty($history) && empty($request['rand']) && empty($request['shopId']) &&empty($if_miaosha)&&empty($if_yushou)&&empty($if_cuxiao)&&empty($if_tuangou)){
// 			$if_cache = 1;
// 		}
// 		if($if_cache==1){
// 			$chache_file = $comId.'-'.$channelId.'-'.$fenleiId.'-'.$cuxiao_id.'-'.$miaoshaId.'-'.$page.'-'.$pageNum.'-'.$request['order1'].'-'.$request['order2'].'.dat';
// 			$cache_content = file_get_contents(ABSPATH.'/cache/product/'.$chache_file);
// 			if(!empty($cache_content)){
// 				$now = time();
// 				$caches = json_decode($cache_content);
// 				if($caches->endTime>$now){
// 					return $cache_content;
// 				}
// 			}
// 		}
		$sql="select productId,min(id) as inventoryId,min(price_sale) as price_sale,min(fanli_shequ) as fanli_shequ,sum(orders) as orders,sum(views) as views,title,dtTime,image,price_market,comId,ordering from demo_product_inventory where comId=$comId and if_lingshou=1 "; //779 积分产品

		if($if_miaosha==1){
			$now = date("Y-m-d H:i:s");
			//$miaoshas = $db->get_results("select * from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$row' and startTime<='$tomrrow' limit 5");
			$cuxiao_jilu = $db->get_row("select id,endTime,pdtIds from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and startTime<='$now' and accordType=3 order by id desc limit 1");
			//$pdtIds = $db->get_var("select group_concat(pdtIds) from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and startTime<='$now' and accordType=3");
			$pdtIds = $cuxiao_jilu->pdtIds;
			if(empty($pdtIds))$pdtIds = '0';
			$sql.=" and id in($pdtIds)";
		}
		if($if_cuxiao==1){
			$now = date("Y-m-d H:i:s");
			//$miaoshas = $db->get_results("select * from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$row' and startTime<='$tomrrow' limit 5");
			$cuxiao_jilu = $db->get_row("select id,endTime,pdtIds from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and startTime<='$now' order by id desc limit 1");
			//$pdtIds = $db->get_var("select group_concat(pdtIds) from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and startTime<='$now'");
			$pdtIds = $cuxiao_jilu->pdtIds;
			if(empty($pdtIds))$pdtIds = '0';
			$sql.=" and id in($pdtIds)";
		}
		if($if_yushou==1){
			$now = date("Y-m-d H:i:s");
			$pdtIds = $db->get_var("select group_concat(pdtId) from yushou where comId=$comId and startTime<'".date('Y-m-d H:i:s')."' and endTime>'".date('Y-m-d H:i:s')."' and status=1");
			if(empty($pdtIds))$pdtIds = '0';
			$sql.=" and id in($pdtIds)";
		}
		if($if_tuangou==1){
			$sql.=" and sale_tuan=1 and tuan_num>0";	
		}
		if(!empty($yhq_id)){
			$yhq_comId = $_SESSION['if_tongbu']==1?10:$comId;
			$fenbiao = getFenbiao($comId,20);
			$yhq=$db->get_row("select * from yhq where id=(select jiluId from user_yhq$fenbiao where id=$yhq_id)");
			if(!empty($yhq->mendianIds)){
				$sql.=" and comId in($yhq->mendianIds)";
			}
			if($yhq->useType>1){
				if(!empty($yhq->channels)){
					$sql.=" and ".($comId==10?'fenleiId':'channelId')." in($yhq->channels)";
				}
			}
			if(!empty($yhq->pdts)){
				$sql.=" and id in($yhq->pdts)";
			}
		}
		if(!empty($lipinka_id)){
			$lipinka_jilu=$db->get_row("select mendianId,channels,pdts from lipinka_jilu where id=(select jiluId from lipinka where id=$lipinka_id)");
			if(!empty($lipinka_jilu->mendianId)){
				$sql.=" and comId=$lipinka_jilu->mendianId";
			}
			if(!empty($lipinka_jilu->channels)){
				$sql.=" and fenleiId in($lipinka_jilu->channels)";
			}
			if(!empty($lipinka_jilu->pdts)){
				$sql.=" and id in($lipinka_jilu->pdts)";
			}
		}
		if(!empty($miaoshaId)){
			$pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$miaoshaId");
			if(empty($pdtIds))$pdtIds = '0';
			$sql.=" and id in($pdtIds)";
		}
		if(!empty($channelId)){
			$channelIds = $channelId.self::getZiIds($channelId);
			$sql.=" and channelId in($channelIds)";
		}
		if(!empty($fenleiId)){
			$channelIds = $fenleiId.self::getZiIds($fenleiId);
			$sql.=" and fenleiId in($channelIds)";
		}
		if(!empty($keyword)){
			$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and (title like '%$keyword%' or productId in($pdtIds))";
			//search_history($keyword);
		}
		if(!empty($tags)){
			$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and tags like '%$tags%'");
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and productId in($pdtIds)";
		}
		
		$brandId = $request['brandId'] ? $request['brandId'] : (int)$request['brand_id'];
		if(!empty($brandId)){
			$pdtIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=".$brandId);
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and productId in($pdtIds)";
		}
		
		$priceMin = $request['price_min'];
		$priceMax = $request['price_max'];
		if(!empty($priceMin)){
		    $sql .= " and price_sale >= $priceMin ";
		}
		
		if(!empty($priceMax)){
		    $sql .= " and price_sale <= $priceMax ";
		}
		
		if(!empty($cuxiao_id)){
			$sql.=" and find_in_set($cuxiao_id,cuxiao_ids)";
		}
		if($shoucang==1){
			$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_collect where userId=$userId and comId=$comId");
			if(empty($shoucangIds))$shoucangIds='0';
			$sql.=" and id in($shoucangIds)";
		}
		if($history==1){
			$shoucangIds = $db->get_var("select group_concat(inventoryId) from user_pdt_history where userId=$userId and comId=$comId");
			if(empty($shoucangIds))$shoucangIds='0';
			$sql.=" and id in($shoucangIds)";
		}
	
		$sql.=" and status=1";
		

		$count = $db->get_var(str_replace('productId,min(id) as inventoryId,min(price_sale) as price_sale,min(fanli_shequ) as fanli_shequ,sum(orders) as orders,sum(views) as views,title,dtTime,image,price_market,comId,ordering','count(distinct(productId))',$sql));
		$sql.=" group by productId";
		$sql.=" order by $order1 $order2 limit ".(($page-1)*$pageNum).",".$pageNum;

		$pdts = $db->get_results($sql);

		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回数据成功';
		$return['count'] = $count;
		$return['pages'] = ceil($count/$pageNum);
		$return['shoucang'] = $shoucang;
		$return['data'] = array();
		//$zhekou = get_user_zhekou();
		$now = date("Y-m-d H:i:s");
		/*分享佣金比例*/
		//$user_bili = $db->get_row("select shangji_bili,shang_bili from demo_shezhi where comId=$comId");
		//$shangji_bili = 100-$user_bili->shangji_bili-$user_bili->shang_bili;

		if(!empty($pdts)){
			foreach ($pdts as $i=>$pdt) {
				$inventory = $db->get_row("select price_market,fenleiId,key_vals,orders,price_card,originalPic from demo_product_inventory where id=$pdt->inventoryId");
				$pro = $db->get_row("select brandId,price_name,originalPic,untis,remark,subtitle,yunfei_moban_ding,skuId,skuDay,channelId from demo_product where id=$pdt->productId");
				$paramData = $db->get_row("select * from demo_product_params where productId = $pdt->productId ");
				
				
				$data = array();
				$data['id'] = $pdt->productId;
				$data['skuId'] = $pro->skuId;
			    $data['channelId'] = $pro->channelId;
				$data['paper_num'] = 0;
				if($pro->skuId){
				    $request['sku'] = $data['skuId'];
				    
				    $paperData =\Zhishang\Paper::sku();
				    $paperData = json_decode($paperData, true);
				    
				    $data['paper_num'] = $paperData['count'];
				}
				$data['skuDay'] = $pro->skuDay;
				$data['title'] = $pdt->title;
				$data['img'] = empty($inventory->originalPic)?ispic($pro->originalPic):ispic($inventory->originalPic);
				$data['imgs'] = explode('|',$pro->originalPic);
				$data['inventoryId'] = $pdt->inventoryId;
				$data['price_sale'] = self::get_user_zhekou($pdt->inventoryId,$pdt->price_sale,$user_level);
				// var_dump($data['price_sale']);die;
				// var_dump($data['price_sale']);die;
				$data['price_market'] =getXiaoshu($inventory->price_market,2);
				$data['price_user'] = getXiaoshu($data['price_sale']-$inventory->price_card,2);
		        $units_arr = json_decode($pro->untis);
		        $data['unit'] = $units_arr[0]->title;
				$data['orders'] = $pdt->orders;
				$data['remark'] = $pro->remark;
				$data['brand'] = '';
				$data['param_info'] = $paramData;
				
				$brandId = $pro->brandId;
				if($brandId>0){
					$data['brand'] = $db->get_var("select originalPic from demo_product_brand where id=$brandId");
				}
				$price_name = empty($pro->price_name)?'市场价':$pro->price_name;
				$data['price_name'] = $price_name;
				$data['key_vals'] = $inventory->key_vals;
			

				$data['kucun'] = self::get_product_kucun($pdt->inventoryId,$areaId);
				$data['subtitle'] = $pro->subtitle;
				$data['views'] = $pdt->views;
	
				$return['data'][] = $data;
			}
		}
		if($if_cache==1){
			$cache_endtime = strtotime('+10 minutes');
			$return['endTime'] = $cache_endtime;
		}
		$cache_content = json_encode($return,JSON_UNESCAPED_UNICODE);
		if($if_cache==1){
			file_put_contents(ABSPATH.'/cache/product/'.$chache_file,$cache_content,LOCK_EX);
		}
		return $cache_content;
	}
	
	function getRowFieldByChannelId($channelId)
	{
	    
	}
	
    function detail(){
		global $db,$request,$comId;
		
		$id = $inventoryId = (int)$request['inventoryId'];
		$product_inventory = $db->get_row("select * from demo_product_inventory where id=$id ".($comId==10?'':"and comId=$comId")."  ");
// 		if(empty($product_inventory)){
// 			return '{"code":0,"message":"商品不存在或已下架"}';
// 		}
		$db->query("update demo_product_inventory set views=views+1 where id=$id");
		$userId =(int)$request['user_id'];
		if($userId>0){
			$ifhas = $db->get_var("select userId from user_pdt_history where userId=$userId and comId=$comId and inventoryId=$id limit 1");
			if(empty($ifhas)){
				$db->query("insert into user_pdt_history(userId,inventoryId,dtTime,comId) value($userId,$id,'".date("Y-m-d H:i:s")."',$comId)");
			}else{
				$db->query("update user_pdt_history set dtTime='".date("Y-m-d H:i:s")."' where userId=$userId and comId=$comId and inventoryId=$id");
			}
		}
		$productId = $product_inventory->productId;
		$product = $db->get_row("select * from demo_product where id=$productId");
		$comId = $product_inventory->comId;
		$userId = (int)$request['user_id'];
		$user_level =0;
		$areaId = $sale_area = 0;
		if(!empty($userId)){
			$user_level = (int)$db->get_var("select level from users where id=$userId");
			$user_address = $db->get_row("select * from user_address where comId=$comId and userId=$userId order by moren desc,id desc limit 1");
			$areaId = $sale_area = (int)$user_address->areaId;
			$ifshoucang = $db->get_var("select inventoryId from user_pdt_collect where userId=$userId and inventoryId=$inventoryId");
			$ifshoucang = empty($ifshoucang)?0:1;
		}
		$fenbiao = getFenbiao($comId,20);
		$inventorys = array();
		$keys = $db->get_results("select * from demo_product_inventory where productId=$productId order by id asc");
		if(count($keys)>1){
			foreach ($keys as $key){
				$k = array();
				$k['inventoryId']=$key->id;
				$k['key_vals'] = $key->key_vals;
				$k['is_selected'] = $key->id==$product_inventory->id?1:0;
				
				$k['price_sale'] = self::get_user_zhekou($key->id,$key->price_sale,$user_level);
				$k['kucun'] = self::get_product_kucun($key->id,$areaId);
				$k['sn'] = $key->sn;
				$k['price_user'] = getXiaoshu($k['price_sale'] - $key->price_card);
				$k['price_tuan'] = $key->price_tuan;
				$k['price_shequ_tuan'] = $key->price_shequ_tuan;
				$k['price_market'] = $key->price_market;
				$k['status'] = $key->status;
				
				$inventorys[] = $k;
			}
		}
		$originalPics = array();
		
        if(!$product_inventory->originalPic){
            $hadImage = $db->get_var("select originalPic from demo_product_inventory where productId = $product_inventory->productId and originalPic <> '' and originalPic is not null ");
            if($hadImage) $product_inventory->originalPic = $hadImage;
        }
        
		if(!empty($product->originalPic)){
		    $originalPics = explode('|',$product->originalPic);
		}else if(!empty($product_inventory->originalPic)){
			$originalPics = explode('|',$product_inventory->originalPic);
		}else{
		    //获取上级分类id
		    //864  863  853    861  862
		    $root_id = 864;
		    $channelId = $db->get_row("select parentId,miaoshu_originalPic from demo_product_channel where id = $product_inventory->channelId"); 
		    if($product_inventory->channelId == 861 || $product_inventory->channelId == 862){
		        $root_id = $product_inventory->channelId;
		    }else{

                $root_id = $channelId->parentId;
               
		    }
	        $originalPic_ = $channelId->miaoshu_originalPic;
		    $objectUrl = "product/$root_id/$product->skuId/";
		    $objectUrl = "img/$product->skuId/";
            $fileList = listObjectsFile($objectUrl, 100);
            if(!empty($fileList['data'])){
                 $originalPics = $fileList['data']; 
            }else{
                if($root_id == 864){
                    $originalPics[] ="https://admin.bio-swamp.com/upload/抗体.jpg"; 
                }else{
                   $originalPics[] =$originalPic_;
                } 
            }
		}
		
	
		$now = date("Y-m-d H:i:s");
		//是否预售
		$yushou = $db->get_row("select * from yushou where pdtId=$inventoryId and comId=$comId and status=1 and startTime<'$now' and endTime>'$now' limit 1");
		$yushou_info = array();
		$max_num = $kucun = self::get_product_kucun($inventoryId,$areaId);;
		if(!empty($yushou)){
		    $max_num = $yushou->num_limit;
		    $left = $yushou->num - $yushou->num_saled;
		    if(empty($max_num) || $max_num>$left){
		        $max_num = $left;
		    }
		    $price_json = json_decode($yushou->price_json,true);
		    $yushou_money = $price_json[0]['price'];
		    if($yushou->type==2){
		        $columns = array_column($price_json,'num');
		        array_multisort($columns,SORT_DESC,$price_json);
		        foreach ($price_json as $val) {
		            if($yushou->num_saled>=$val['num']){
		                $yushou_money = $val['price'];
		                break;
		            }
		        }
		    }
		    $yushou_info['id'] = $yushou->id;
		    $yushou_info['money'] = $yushou_money;
		    $yushou_info['paytype'] = $yushou->paytype;
		    $yushou_info['dingjin'] = $yushou->dingjin;
		    $yushou_info['num'] = $yushou->num;
		    $yushou_info['num_saled'] = $yushou->num_saled;
		    $yushou_info['startTime'] = $yushou->startTime;
		    $yushou_info['endTime'] = $yushou->endTime;
		    $yushou_info['startTime1'] = $yushou->startTime1;
		    $yushou_info['endTime1'] = $yushou->endTime1;
		    $yushou_info['fahuo_time'] = $yushou->fahuoTime;
		    $yushou_info['rules'] = '';
		    if($yushou->type==2){
                $price_json = json_decode($yushou->price_json);
                $yushou_info['rules'] = '预售价:<span style="color:#ff0000;">￥'.$price_json[0]->price.'元</span>';
                foreach ($price_json as $i => $price) {
                    if($i>0){
                        $yushou_info['rules'] .= '<br>满'.$price->num.'份:<span style="color:#ff0000;">￥'.$price->price.'元</span>';
                    }
                }
            }
		}
		//是否促销活动
		$cuxiao_pdt = $db->get_row("select id,startTime,endTime,guizes,accordType,type from cuxiao_pdt where comId=$comId and find_in_set($id,pdtIds) and status=1 and startTime<'$now' and endTime>'$now' order by startTime asc limit 1");
		$cuxiao_info = array();
		if(!empty($cuxiao_pdt)){
		    $cuxiao_xiangou = $db->get_var("select num from cuxiao_pdt_xiangou where cuxiao_id=$cuxiao_pdt->id and inventoryId=$id");
		    if($cuxiao_xiangou>0){
		        $buy_num = (int)$db->get_var("select num from cuxiao_pdt_buy where cuxiao_id=$cuxiao_pdt->id and inventoryId=$id and userId=$userId");
		        $cuxiao_xiangou-=$buy_num;
		        $max_num = $max_num>($cuxiao_xiangou-$buy_num)?($cuxiao_xiangou-$buy_num):$max_num;
		    }
		    $content = '';
            $type1 = $cuxiao_pdt->accordType == '1'?'个':'元';
            $type2 = $cuxiao_pdt->type==1?'赠':($cuxiao_pdt->type==2?'减':'享');
            $contents = json_decode($cuxiao_pdt->guizes);
            if(!empty($contents)){
                foreach ($contents as $i=>$rule){
                    $content .='满'.$rule->man.$type1.$type2.$rule->jian;
                    switch ($cuxiao_pdt->type) {
                        case 1:
                            $inventory = $db->get_row("select title,key_vals from demo_product_inventory where id=$rule->inventoryId");
                            $content .=$inventory->title.($inventory->key_vals=='无'?'':'【'.$inventory->key_vals.'】');
                        break;
                        case 2:
                            $content .='元';
                        break;
                        case 3:
                            $content .='%';
                        break;
                    }
                    if($i<(count($contents)-1))$content.='；';
                }
            }
            $cuxiao_info['id'] = $cuxiao_pdt->id;
            $cuxiao_info['content'] = $content;
            $cuxiao_info['startTime'] = $cuxiao_pdt->startTime;
            $cuxiao_info['endTime'] = $cuxiao_pdt->endTime;
            $zongNum = $kucun + $product_inventory->orders;
            $cuxiao_info['width'] = intval($product_inventory->orders*10000/$zongNum)/100;
		}
		$sql = "select * from order_comment$fenbiao where pdtId=$productId ";
		$countsql = str_replace('*','count(*)',$sql);
		$comment_num = (int)$db->get_var($countsql);
		$sql.=" order by id desc limit 3";
		$comments = $db->get_results($sql);
		$comment_list = array();
		//$db_service = get_zhishang_db();
		if(!empty($comments)){
			foreach ($comments as $i=>$j) {
				$pingjia = array();
				/*if($_SESSION['if_tongbu']==1){
					$u = $db_service->get_row("select name as nickname,image from demo_user where id=$j->userId");
				}else{*/
				$u = $db->get_row("select nickname,image from users where id=$j->userId");
				if(!empty($u->image) && substr($u->image,0,4)!='http'){
					$u->image = 'http://www.zhishangez.com'.$u->image;
				}
				//}
				$pingjia['touxiang'] = $u->image;
				$pingjia['username'] = sys_substr($u->nickname,1,false).'**';
				$pingjia['dtTime'] = date("Y-m-d H:i",strtotime($j->dtTime1));
				$j->cont1 = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->cont1);
				$pingjia['content'] = $j->cont1;
				$pingjia['imgs'] = empty($j->images1)?array():explode('|',$j->images1);
				$pingjia['key_vals'] = $j->key_vals;
				$pingjia['star'] = $j->star;
				$comment_list[] = $pingjia;
			}
		}

		$level = $user_level;
		$channelId = $product_inventory->channelId;
		$pchannel = (int)$db->get_var("select parentId from demo_product_channel where id=$channelId");
		$sql = "select * from yhq where comId=$comId and endTime>'$now' and num>hasnum and status=1 and (mendianIds='' or mendianIds='$comId') and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds)) and (useType=1 or find_in_set($channelId,channels) or find_in_set($pchannel,channels) or find_in_set($inventoryId,pdts)) limit 5";
		//file_put_contents('request.txt',$sql);
		$res = $db->get_results($sql);
	
		$yhq_list = array();
		if($res){
	      foreach ($res as $key) {
	      	if($key->num_day>0){
	      		$hasNum = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and jiluId=$key->id and dtTime>='$today 00:00:00' and dtTime<='$today 23:59:59'");
	      		if($hasNum>=$key->num_day)continue;
	      	}
	      	$tiaojian = '通用';
			if($key->useType>1){
				$tiaojian = $key->channelNames;
				if(!empty($key->pdtNames)){
					$tiaojian.=empty($tiaojian)?$key->pdtNames:','.$key->pdtNames;
				}
			}
			$key->tiaojian = $tiaojian;
	      	$key->startTime = date("Y-m-d",strtotime($key->startTime));
	      	$key->endTime = date("Y-m-d",strtotime($key->endTime));
	      	$key->money = floatval($key->money);
	      	$key->man = floatval($key->man);
	      	//$userId = $_SESSION['if_tongbu']==1?$_SESSION['demo_zhishangId']:$userId;
	      	$lingqu_num = $db->get_var("select count(*) from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id");
		    if($lingqu_num>0){
		    	$key->lingqu_id = $db->get_var("select id from user_yhq$fenbiao where comId=$comId and userId=$userId and jiluId=$key->id limit 1");
		    }
		    $key->if_lingqu = $lingqu_num>0?1:0;
		    if($key->numlimit > 0){
		        $key->if_ke_lingqu = $lingqu_num<$key->numlimit?1:0;
		    }else{
		        $key->if_ke_lingqu = 1;
		    }
		    
	      	$yhq_list[] = $key;
	      }
	  	}

	  	$cont1 = preg_replace('/((\s)*(\n)+(\s)*)/','',(empty($product_inventory->cont1)?$product->cont1:$product_inventory->cont1));
	  	$cont2 = preg_replace('/((\s)*(\n)+(\s)*)/','',(empty($product_inventory->cont2)?$product->cont2:$product_inventory->cont2));
	  	$cont3 = preg_replace('/((\s)*(\n)+(\s)*)/','',(empty($product_inventory->cont3)?$product->cont3:$product_inventory->cont3));
	  	$cont1 = str_replace('src="/','src="http://'.$_SERVER['HTTP_HOST']."/",$cont1);
	  	$cont2 = str_replace('src="/','src="http://'.$_SERVER['HTTP_HOST']."/",$cont2);
	  	$cont3 = str_replace('src="/','src="http://'.$_SERVER['HTTP_HOST']."/",$cont3);
	  	$video_vid = self::get_video_vid($cont1);
	  	$cont1 = preg_replace("/<(\/?i?frame.*?)>/si","",$cont1); //过滤frame标签
		$return['code'] = 1;
		$return['message'] = '成功';
		$return['data'] = array();
		
		$return['data']['skuId'] = $product->skuId;
		$return['data']['skuDay'] = $product->skuDay;
		$return['data']['book_url'] = $product->book_url;
		
		$hadOnline = $db->get_row("select * from demo_product_inventory where productId = $product->id and status = 1"); 
		
		$return['data']['product_status'] = $hadOnline ? 1 : -1;
		
		$paramData = $db->get_row("select * from demo_product_params where productId = $product->id ");
		
		$return['data']['param_info'] = $paramData;
		
		$return['data']['paper_num'] = 0;
		if($product->skuId){
		    $request['sku'] = $product->skuId;
		    
		    $paperData =\Zhishang\Paper::sku();
		    $paperData = json_decode($paperData, true);
		    
		    $return['data']['paper_num'] = $paperData['count'];
		}
		
		$return['data']['title'] = $product_inventory->title;
		$return['data']['key_vals'] = $product_inventory->key_vals;
		$return['data']['sn'] = $product_inventory->sn;
		$return['data']['tags'] = explode(',', $product->tags);
		$return['data']['remark'] = $product->remark;
		$return['data']['send_address'] = $db->get_var("select com_address from demo_shezhi where comId = $comId");
		if($product->mendianId > 0){
		    $mendian = $db->get_row("select * from demo_shequ where id = $product->mendianId");
		    $return['data']['send_address'] = $mendian->peisong_area;
		}
		$return['data']['inventoryId'] = $product_inventory->id;
		$return['data']['productId'] = $productId;
		$return['data']['channelId'] = $product_inventory->channelId;
				
		$yi_id_arr = explode(',','855'.self::getZiIds(855));  //仪器
		$sj_id_arr = explode(',','853'.self::getZiIds(853));  //试剂盒
		$return['data']['is_yiqi'] = in_array($product_inventory->channelId,$yi_id_arr);
		$return['data']['is_sj'] = in_array($product_inventory->channelId,$sj_id_arr);
		$return['data']['content'] = $cont1;
		$return['data']['cont2'] = $cont2;
		$return['data']['cont3'] = $cont3;
		$return['data']['images'] = $originalPics;
		$return['data']['price_sale'] = self::get_user_zhekou($inventoryId,$product_inventory->price_sale,$user_level);
		$return['data']['price_user'] = $product_inventory->price_sale;
		$return['data']['price_market'] = $product_inventory->price_market;
		$return['data']['orders'] = $product->orders;
		$return['data']['brand_name'] = $db->get_var("select title from demo_product_brand where id = $product->brandId ");
		$return['data']['kucun'] = $kucun;
		$return['data']['max_num'] = $max_num;
		$return['data']['if_shoucang'] = (int)$ifshoucang;
		$return['data']['inventorys'] = $inventorys;
		$return['data']['comment_num'] = $comment_num;
		$return['data']['comment_list'] = $comment_list;
		//$return['data']['gwc_num'] = $gouwuche_num;
		$return['data']['yhq_list'] = $yhq_list;
		$return['data']['if_yushou'] = empty($yushou)?0:1;
		$return['data']['yushou_info'] = $yushou_info;
		$return['data']['if_cuxiao'] = empty($cuxiao_pdt)?0:1;
		$return['data']['cuxiao_info'] = $cuxiao_info;
		$return['data']['share_img'] = $product->share_img;
		$return['data']['subtitle'] = $product->subtitle;
		$return['data']['views'] = $db->get_var("select sum(views) from demo_product_inventory where productId=$productId");
		$units = json_decode($product->untis,true);
		$return['data']['unit'] = $units[0]['title'];
		$return['data']['video_vid'] = $video_vid;

		$return['data']['today_orders'] = (int)$db->get_var("select sum(a.num) from order_detail$fenbiao a left join order$fenbiao b on a.orderId=b.id where a.inventoryId=$inventoryId and b.dtTime like '".date("Y-m-d")."%' and a.status>-1");
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	/*提取内容中的腾讯视频id*/
	public static function get_video_vid($cont)
	{
		$vid = '';
		preg_match('/<iframe[^>]*\s+src="([^"]*)"[^>]*>/is', $cont, $matched);
		if(!empty($matched[1]) && strstr($matched[1],'vid')){
			
			$arr = explode('vid=',$matched[1]);
			$vid = empty($arr[1])?'':$arr[1];
		}
		return $vid;
	} 
	
	public function comments()
	{
		global $db,$request;
		$productId = (int)$request['productId'];
		$comId = (int)$db->get_var("select comId from demo_product where id=$productId");
		$fenbiao = getFenbiao($comId,20);
		$page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
		$star = (int)$request['star'];
		if($page<1)$page=1;
		if(empty($pageNum))$pageNum=10;
		$sql = "select * from order_comment$fenbiao where pdtId=$productId and status=1 ";
		if(!empty($star)){
			switch ($star) {
				case 1:
					$sql.=" and star>3";
				break;
				case 2:
					$sql .=" and star=3";
				break;
				case 3:
					$sql .=" and star<3";
				break;
			}
		}
		//$countsql = str_replace('*','count(*)',$sql);
		//$count = $db->get_var($countsql);
		$sql.=" order by id desc limit ".(($page-1)*$pageNum).",".$pageNum;
		$jilus = $db->get_results($sql);
		$counts = $db->get_results("select star,count(*) from order_comment$fenbiao where pdtId=$productId and status=1 group by star");
		$count = 0;$count_good = 0;$count_bad = 0;$count_middle=0;
		if(!empty($counts)){
			foreach ($counts as $key => $val) {
				$count++;
				if($val->star>3){
					$count_good++;
				}else if($val->star==3){
					$count_middle++;
				}else{
					$count_bad++;
				}
			}
		}
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = $count;
		$return['count_good'] = $count_good;
		$return['count_bad'] = $count_bad;
		$return['count_middle'] = $count_middle;
		$return['pages'] = ceil($count/$pageNum);
		$return['data'] = array();
		//$db_service = get_crm_db();
		if(!empty($jilus)){
			foreach ($jilus as $i=>$j) {
				$pingjia = array();
				/*if($_SESSION['if_tongbu']==1){
					$u = $db_service->get_row("select name as nickname,image from demo_user where id=$j->userId");
				}else{*/
					$u = $db->get_row("select nickname,image from users where id=$j->userId");
				//}
				$pingjia['touxiang'] = $u->image;
				$pingjia['username'] = sys_substr($j->name,1,false).'**';
				$pingjia['dtTime'] = date("Y-m-d H:i",strtotime($j->dtTime1));
				$j->cont1 = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->cont1);
				$pingjia['content'] = $j->cont1;
				$pp=explode('|',$j->images1);
				foreach ($pp as $v){
				    $v=HTTP_URL.$v;
				}
				$pingjia['imgs'] = empty($j->images1)?array():$pp;
				$pingjia['reply'] = '';
				if(!empty($j->reply)){
					$j->reply = preg_replace('/((\s)*(\n)+(\s)*)/','<br>',$j->reply);
					$pingjia['reply'] = '掌柜回复：'.$j->reply;
				}
				$pingjia['star'] = $j->star;
				$pingjia['key_vals'] = $db->get_var("select key_vals from demo_product_inventory where id=$j->inventoryId");
				$return['data'][] = $pingjia;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	//获取所有下级的分类，用,分开
	public static function getZiIds($id)
	{
		global $db,$comId;
		$str = '';
		$ziIds = $db->get_results("select id from demo_product_channel where comId=$comId and parentId=$id order by ordering desc,id asc");
		if(!empty($ziIds)){
			foreach ($ziIds as $ziId) {
				$str .= ','.$ziId->id.self::getZiIds($ziId->id);
			}
		}
		return $str;
	}
	
	//获取会员的折扣
	public static function get_user_zhekou($inventoryId,$price,$level)
	{
		global $db,$comId;

		$shezhi = $db->get_row("select if_fixed_zhekou,fixed_zhekou from user_shezhi where comId=$comId");
		if($shezhi->if_fixed_zhekou==1){
			$if_price = $db->get_var("select price from demo_product_price where inventoryId=$inventoryId and levelId=$level");
			$price = empty($if_price)?$price:$if_price;
			$price = getXiaoshu($price,2);
		}else{
			$zhekou = $db->get_var("select zhekou from user_level where id=$level and comId=$comId");
			$zhekou = $zhekou/10;
			if(empty($zhekou))$zhekou=1;
			$price = getXiaoshu($price*$zhekou,2);
		}
		
		$activityType = self::inventoryActivity($inventoryId); 
// 		var_dump($activityType);die;
		if($activityType == 3){
	        $now = date('Y-m-d H:i:s');
            $cuxiao_jilu = $db->get_row("select id,endTime,pdtIds from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and accordType=3 and find_in_set ($inventoryId, pdtIds)  order by id asc ");
            $cuxiaoPrice = $db->get_var("select price from cuxiao_pdt_xiangou where cuxiao_id = $cuxiao_jilu->id and inventoryId = $inventoryId ");
            if($cuxiaoPrice != 0 ){
                $price = $cuxiaoPrice;
            }
		}
		
		return $price;
	}
	
	public static function get_product_kucun_by_mendianId($inventoryId, $mendianId)
	{
	    global $db,$comId;
	    
		$kc = $db->get_row("select kucun from mendian_product where inventoryId=$inventoryId and mendianId = $mendianId limit 1");
		if(empty($kc->kucun)){
		    return 0;
		}
	
		return $kc->kucun;
	} 
	
	//获取库存
	public static function get_product_kucun($inventoryId,$areaId)
	{
		global $db,$comId;
		
		$storeId = self::get_fahuo_store($areaId);
		//$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$inventoryId and storeId=$storeId limit 1");
		$kc = $db->get_row("select kucun from demo_kucun where inventoryId=$inventoryId limit 1");
		if(empty($kc->kucun) || $kc->kucun < 0){
		    return 0;
		}
	
		return $kc->kucun;
	}
	
	//获取发货仓库
	public static function get_fahuo_store($areaId)
	{
		global $db,$comId;
		if(!empty($areaId)){
			$fuarea = (int)$db->get_var("select parentId from demo_area where id=$areaId");
			$fuarea1 = (int)$db->get_var("select parentId from demo_area where id=$fuarea");
			$storeId = $db->get_var("select storeId from demo_shezhi_fahuo where comId=$comId and (find_in_set($areaId,areaIds) or find_in_set($fuarea,areaIds) or find_in_set($fuarea1,areaIds)) limit 1");
			if(empty($storeId)){
				$storeId = $db->get_var("select storeId from demo_shezhi where comId=$comId");
			}
		}else{
			$storeId = $db->get_var("select storeId from demo_shezhi where comId=$comId");
		}
		return $storeId;
	}
	
	//以下是订货需要的接口
	public function lists()
	{
		global $db,$request,$comId;
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
		}
		if(is_file("cache/kucun_set_$comId.php")){
			$kucun_set = json_decode(file_get_contents("cache/kucun_set_$comId.php"));
		}else{
			$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
		}
		$userId = (int)$request['user_id'];
		$db_service = get_zhishang_db();
		$department = $db_service->get_var("select department from demo_user where id=$userId");
		$kehuId = (int)$request['kehu_id'];
		$channelId = (int)$request['channel_id'];
		$brandId = (int)$request['brand_id'];
		$status = (int)$request['status'];
		$page = empty($request['page'])?1:(int)$request['page'];
		$pageNum = empty($request['pagenum'])?10:(int)$request['pagenum'];
		$keyword = $request['keyword'];
		$tags = $request['tags'];
		$order1 = empty($request['order1'])?'ordering':$request['order1'];
		$order2 = empty($request['order2'])?'desc':$request['order2'];
		if($order1=='title'){
			$order1 = 'CONVERT(title USING gbk)';
		}
		if(empty($request['order2'])){
			$order1 = 'ordering';
			$order2 = 'desc,id desc';
		}
		$storeId = (int)$db->get_var("select storeIds from demo_quanxian where comId=$comId and(find_in_set($userId,userIds) or find_in_set($department,departs)) and model='kucun' limit 1");

		$sql="select a.productId,min(a.id) as inventoryId,min(a.shichangjia) as price,sum(a.kucun) as kucun,a.sn,a.title,a.dtTime from demo_kucun b left join demo_product_inventory a on b.inventoryId=a.id where b.comId=$comId and b.storeId=$storeId and a.status=1";
		if($status==1){
			$sql.=" and b.kucun>0";
		}else if($status==-1){
			$sql.=" and b.kucun<=0";
		}
		if($shoucang==1){
			$shoucangIds = $db->get_var("select group_concat(inventoryId) from dinghuo_shoucang where kehuId=$kehuId");
			if(empty($shoucangIds))$shoucangIds='0';
			$sql.=" and a.id in($shoucangIds)";
		}
		if(!empty($channelId)){
			$channelIds = $channelId.self::getZiIds($channelId);
			$sql.=" and a.channelId in($channelIds)";
		}
		if(!empty($brandId)){
			$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
			if(empty($productIds))$productIds='0';
			$sql.=" and a.productId in($productIds)";
		}
		if(!empty($keyword)){
			$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and (a.title like '%$keyword%' or a.productId in($pdtIds))";
		}
		if(!empty($tags)){
			$pdtIdsql = "select group_concat(id) from demo_product where comId=$comId";
			$pdtIdsql.=" and(1!=1";
			foreach ($tags as $t) {
				$pdtIdsql.=" or a.tags like '%$t%'";
			}
			$pdtIdsql.=")";
			$pdtIds = $db->get_var($pdtIdsql);
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and a.productId in($pdtIds)";
		}
		//$count = $db->get_var(str_replace('a.productId,min(a.id) as inventoryId,min(a.shichangjia) as price,sum(a.kucun) as kucun,a.sn,a.title,a.dtTime','count(distinct(productId))',$sql));
		$sql.=" group by a.productId";
		$sql.=" order by inventoryId limit ".(($page-1)*$pageNum).",".$pageNum;
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = 0;
		$return['data'] = array();
		
		if(!empty($pdts)){
			foreach ($pdts as $pdt) {
				$inventory = $db->get_row("select key_vals,sn from demo_product_inventory where id=$pdt->inventoryId");
				$data = array();
				$data['inventory_id'] = $pdt->inventoryId;
				$data['product_id'] = $pdt->productId;
				$data['title'] = $pdt->title;
				$data['key_vals'] = $inventory->key_vals=='无'?'':$inventory->key_vals;
				//$data['sn'] = $inventory->sn;
				$data['img'] = '/inc/img/nopic.svg';
				$product=$db->get_row("select untis,tags,dinghuo_units,originalPic from demo_product where id=".$pdt->productId);
				if(!empty($product->originalPic)){
					$pics = explode('|',$product->originalPic);
					$data['img'] = $pics[0].'?x-oss-process=image/resize,w_227';
				}
				$units = json_decode($product->untis,true);
				$dinghuo_units = explode(',',$product->dinghuo_units);
				$data_units = array();
				foreach ($units as $u){
					if(in_array($u['title'],$dinghuo_units)){
						$un = array();
						$un['title'] = $u['title'];
						$un['num'] = $u['num'];
						$data_units[] = $un;
					}
				}
				$data['unit'] = $units[0]['title'];
				//$data['units'] = $data_units;
				//$data['inventoryId'] = $pdt->inventoryId;
				$price = self::getKehuPrice($pdt->inventoryId,$kehuId);
				$data['unit_price'] = getXiaoshu($price['price'],$product_set->price_num);
				//$data['price'] = floatval($data['unit_price'])*floatval($data_units[0]['num']);
				//$data['min'] = $price['min'];
				//$data['max'] = $price['max'];
				$data['kucun'] = 0;
				if($data['unit_price']>0){
					$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$pdt->inventoryId and storeId=$storeId limit 1");
						$data['kucun'] = $kc->kucun-$kc->yugouNum;
				}
				//$data['kucun'] = $pdt->kucun;
				$inventorys = array();
				$keys = $db->get_results("select id,key_vals from demo_product_inventory where productId=$pdt->productId order by id asc");
				if(!empty($keys)){
					foreach ($keys as $key){
						$k = array();
						$k['inventory_id'] = $key->id;
						if($k['inventory_id']==$data['inventory_id']){
							$k['key_vals'] = $data['key_vals'];
							$k['unit_price'] = $data['unit_price'];
							$k['kucun'] = $data['kucun'];
						}else{
							$k['key_vals'] = $key->key_vals;
							$price = self::getKehuPrice($key->id,$kehuId);
							$k['unit_price'] = getXiaoshu($price['price'],$product_set->price_num);
							$k['kucun'] = 0;
							if($k['unit_price']>0){
								$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$key->id and storeId=$storeId limit 1");
								$k['kucun'] = getXiaoshu($kc->kucun-$kc->yugouNum,$product_set->number_num);
							}
						}
						
						$inventorys[] = $k;
					}
				}
				$data['specs'] = $inventorys;
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function snlists()
	{
		global $db,$request,$comId;
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
		}
		if(is_file("cache/kucun_set_$comId.php")){
			$kucun_set = json_decode(file_get_contents("cache/kucun_set_$comId.php"));
		}else{
			$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
		}
		$userId = (int)$request['user_id'];
		$db_service = get_zhishang_db();
		$department = $db_service->get_var("select department from demo_user where id=$userId");
		$kehuId = (int)$request['kehu_id'];
		$channelId = (int)$request['channel_id'];
		$brandId = (int)$request['brand_id'];
		$status = (int)$request['status'];
		$page = empty($request['page'])?1:(int)$request['page'];
		$pageNum = empty($request['pagenum'])?10:(int)$request['pagenum'];
		$keyword = $request['keyword'];
		$tags = $request['tags'];
		$order1 = empty($request['order1'])?'ordering':$request['order1'];
		$order2 = empty($request['order2'])?'desc':$request['order2'];
		if($order1=='title'){
			$order1 = 'CONVERT(title USING gbk)';
		}
		if(empty($request['order2'])){
			$order1 = 'ordering';
			$order2 = 'desc,id desc';
		}
		$storeId = (int)$db->get_var("select storeIds from demo_quanxian where comId=$comId and(find_in_set($userId,userIds) or find_in_set($department,departs)) and model='kucun' limit 1");
		$sql="select a.productId,a.id as inventoryId,a.shichangjia as price,a.kucun as kucun,a.sn,a.title,a.dtTime from demo_kucun b left join demo_product_inventory a on b.inventoryId=a.id where b.comId=$comId and b.storeId=$storeId and a.status=1";
		if($status==1){
			$sql.=" and b.kucun>0";
		}else if($status==-1){
			$sql.=" and b.kucun<=0";
		}
		if($shoucang==1){
			$shoucangIds = $db->get_var("select group_concat(inventoryId) from dinghuo_shoucang where kehuId=$kehuId");
			if(empty($shoucangIds))$shoucangIds='0';
			$sql.=" and a.id in($shoucangIds)";
		}
		if(!empty($channelId)){
			$channelIds = $channelId.self::getZiIds($channelId);
			$sql.=" and a.channelId in($channelIds)";
		}
		if(!empty($brandId)){
			$productIds = $db->get_var("select group_concat(id) from demo_product where comId=$comId and brandId=$brandId");
			if(empty($productIds))$productIds='0';
			$sql.=" and a.productId in($productIds)";
		}
		if(!empty($keyword)){
			$pdtIds = $db->get_var("select group_concat(productId) from demo_product_keyword where comId=$comId and keyword='$keyword'");
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and (a.title like '%$keyword%' or a.productId in($pdtIds))";
		}
		if(!empty($tags)){
			$pdtIdsql = "select group_concat(id) from demo_product where comId=$comId";
			$pdtIdsql.=" and(1!=1";
			foreach ($tags as $t) {
				$pdtIdsql.=" or a.tags like '%$t%'";
			}
			$pdtIdsql.=")";
			$pdtIds = $db->get_var($pdtIdsql);
			if(empty($pdtIds))$pdtIds='0';
			$sql.=" and a.productId in($pdtIds)";
		}
		//$count = $db->get_var(str_replace('a.productId,min(a.id) as inventoryId,min(a.shichangjia) as price,sum(a.kucun) as kucun,a.sn,a.title,a.dtTime','count(distinct(productId))',$sql));
		//$sql.=" group by a.productId";
		$sql.=" order by a.id limit ".(($page-1)*$pageNum).",".$pageNum;
		//echo $sql;
		$pdts = $db->get_results($sql);
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['count'] = 0;
		$return['data'] = array();
		
		if(!empty($pdts)){
			foreach ($pdts as $pdt) {
				$inventory = $db->get_row("select key_vals,sn from demo_product_inventory where id=$pdt->inventoryId");
				$data = array();
				$data['inventory_id'] = $pdt->inventoryId;
				$data['product_id'] = $pdt->productId;
				$data['title'] = $pdt->title;
				$data['key_vals'] = $inventory->key_vals=='无'?'':$inventory->key_vals;
				//$data['sn'] = $inventory->sn;
				$data['img'] = '/inc/img/nopic.svg';
				$product=$db->get_row("select untis,tags,dinghuo_units,originalPic from demo_product where id=".$pdt->productId);
				if(!empty($product->originalPic)){
					$pics = explode('|',$product->originalPic);
					$data['img'] = $pics[0].'?x-oss-process=image/resize,w_227';
				}
				$units = json_decode($product->untis,true);
				$dinghuo_units = explode(',',$product->dinghuo_units);
				$data_units = array();
				foreach ($units as $u){
					if(in_array($u['title'],$dinghuo_units)){
						$un = array();
						$un['title'] = $u['title'];
						$un['num'] = $u['num'];
						$data_units[] = $un;
					}
				}
				$data['unit'] = $units[0]['title'];
				//$data['units'] = $data_units;
				//$data['inventoryId'] = $pdt->inventoryId;
				$price = self::getKehuPrice($pdt->inventoryId,$kehuId);
				$data['unit_price'] = getXiaoshu($price['price'],$product_set->price_num);
				//$data['price'] = floatval($data['unit_price'])*floatval($data_units[0]['num']);
				//$data['min'] = $price['min'];
				//$data['max'] = $price['max'];
				$data['kucun'] = getXiaoshu($pdt->kucun,$product_set->number_num);
				if($data['unit_price']>0){
					$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$pdt->inventoryId and storeId=$storeId limit 1");
						$data['kucun'] = $kc->kucun-$kc->yugouNum;
				}
				//$data['kucun'] = $pdt->kucun;
				/*$inventorys = array();
				$keys = $db->get_results("select id,key_vals from demo_product_inventory where productId=$pdt->productId order by id asc");
				if(!empty($keys)){
					foreach ($keys as $key){
						$k = array();
						$k['inventory_id'] = $key->id;
						if($k['inventory_id']==$data['inventory_id']){
							$k['key_vals'] = $data['key_vals'];
							$k['unit_price'] = $data['unit_price'];
							$k['kucun'] = $data['kucun'];
						}else{
							$k['key_vals'] = $key->key_vals;
							$price = self::getKehuPrice($key->id,$kehuId);
							$k['unit_price'] = getXiaoshu($price['price'],$product_set->price_num);
							$k['kucun'] = 0;
							if($k['unit_price']>0){
								$kc = $db->get_row("select kucun,yugouNum from demo_kucun where inventoryId=$key->id and storeId=$storeId limit 1");
								$k['kucun'] = $kc->kucun-$kc->yugouNum;
							}
						}
						
						$inventorys[] = $k;
					}
				}
				$data['specs'] = $inventorys;*/
				$return['data'][] = $data;
			}
		}
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	function delhistory(){
	    global $db,$request,$comId;
	    
		$userId = (int)$request['user_id'];
		$inventoryId = $request['inventoryId'];
	
		$db->query("delete from user_pdt_history where userId=$userId and comId=$comId and inventoryId in ($inventoryId)");
	
		return '{"code":1,"message":"操作成功"}';
	}
	
	function collect()
	{
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
		$inventoryId = (int)$request['inventoryId'];
		$ifshoucang = (int)$request['collect_type'];
		if($ifshoucang==0){
			$db->query("insert into user_pdt_collect(userId,inventoryId,dtTime,comId) value($userId,$inventoryId,'".date("Y-m-d H:i:s")."',$comId)");
		}else{
			$db->query("delete from user_pdt_collect where userId=$userId and comId=$comId and inventoryId=$inventoryId limit 1");
		}
		
		return '{"code":1,"message":"操作成功"}';
	}
	
	function delcollect(){
	    global $db,$request,$comId;
	    
		$userId = (int)$request['user_id'];
		$inventoryId = $request['inventoryId'];
	
		$db->query("delete from user_pdt_collect where userId=$userId and comId=$comId and inventoryId in ($inventoryId)");
	
		return '{"code":1,"message":"操作成功"}';
	}
	
	function getKehuPrice($inventoryId,$kehuId)
	{
		global $db,$comId;
		if(is_file("cache/product_set_$comId.php")){
			$product_set = json_decode(file_get_contents("cache/product_set_$comId.php"));
		}else{
			$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
		}
		$return = array();
		$dinghuo = $db->get_row("select ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where inventoryId=$inventoryId and kehuId=$kehuId limit 1");
		
		if(!empty($dinghuo)){
			if($dinghuo->ifsale==1){
				$return['price'] = $dinghuo->price_sale;
			}else{
				$return['price'] = '0.00';
			}
			$return['min'] = empty($dinghuo->dinghuo_min)?0:$dinghuo->dinghuo_min;
			$return['max'] = empty($dinghuo->dinghuo_max)?0:$dinghuo->dinghuo_max;
		}else{
			$level = (int)$db->get_var("select level from demo_kehu where id=$kehuId");
			$dinghuo = $db->get_row("select ifsale,price_sale,dinghuo_min,dinghuo_max from demo_product_dinghuo where inventoryId=$inventoryId and levelId=$level limit 1");

			if(!empty($dinghuo)){
				if($dinghuo->ifsale==1){
					$return['price'] = $dinghuo->price_sale;
				}else{
					$return['price'] = '0.00';
				}
				$return['min'] = empty($dinghuo->dinghuo_min)?0:$dinghuo->dinghuo_min;
				$return['max'] = empty($dinghuo->dinghuo_max)?0:$dinghuo->dinghuo_max;
			}else{
				$return['price'] = '0.00';
				$return['min'] = 0;
				$return['max'] = 0;
			}
		}
		$return['price'] = getXiaoshu($return['price'],$product_set->price_num);
		if($product_set->if_dinghuo_min==0){$return['min'] = 0;}
		if($product_set->if_dinghuo_max==0){$return['max'] = 0;}
		return $return;
	}
	
	/*购买记录*/
	public function buyRecord()
	{
		global $db,$request,$comId;
		$ids = explode(',',$request['ids']);
		if(!empty($ids)){
			foreach ($ids as $key => $val) {
				$ids[$key] = (int)$val;
			}
		}
		$idstr = implode(',',$ids);
		$return = array("code"=>1,"message"=>"","data"=>array());
		$fenbiao = getFenbiao($comId,20);
		$userIds = $db->get_var("select group_concat(userId) from order_detail$fenbiao where inventoryId in($idstr)");
		if(empty($userIds))$userIds='0';
		$tuan_users = $db->get_results("select nickname,image from users where id in($userIds) limit 10");
		$return['data']= empty($tuan_users)?array():$tuan_users;
		return json_encode($return);
	}
	
	public function getCode()
	{
		global $db,$request,$comId;

		$userId = (int)$request['user_id'];
		$inventoryId = (int)$request['inventoryId'];
		$type = (int)$request['type'];
		
		if(!in_array($type, [1,3])){
		    return '{"code":0,"message":"类型不合法，请确认"}';
		}
		
		if($type == 3){
		    $filename = $comId.'_'.$inventoryId.'_'.$userId.'.png'; //新图片名称
    		$newFilePath = ABSPATH.'upload/code/'.$filename;
    		$url = "https://".$_SERVER['HTTP_HOST'].'/upload/code/'.$filename;
    		
    		if(is_file($newFilePath)){
    			return '{"code":1,"message":"","data":"'.$url.'"}';
    		}
    		$access_token = self::getAccessToken(3);
    	  	$ewm_url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=$access_token";
    	  	$params = array("scene"=>"invite_id=".$userId."&id=".$inventoryId,"page"=>"pages/goods_detail/goods_detail");
    	  	$ewm = self::curl_post($ewm_url,$params);
    	    $result = json_decode($ewm, true);
    	    if(isset($result['errmsg'])){
    	        return '{"code":0,"message":"生成失败，失败原因：'.$result['errmsg'].'"}';
    	    }
    		$newFile = fopen($newFilePath,"w"); //打开文件准备写入
    		fwrite($newFile,$ewm); //写入二进制流到文件
    		fclose($newFile);
		}else{
		    
		    /**http请求方式: POST URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN POST数据格式：json POST数据例子：
		     * {"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}} 
		     * 或者也可以使用以下 POST 数据创建字符串形式的二维码参数： 
		     * {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "test"}}}
		    */
		    
		    $accessToken = self::getAccessToken(1);
		    $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token={$accessToken}";
		    $qrStr = "productshare-".$userId."-".$inventoryId;
		    $newFilePath = ABSPATH.'upload/code/'.$qrStr.".png";
		    $fileUrl = "https://".$_SERVER['HTTP_HOST'].'/upload/code/'.$qrStr.".png";
		    if(is_file($newFilePath)){
    			return '{"code":1,"message":"","data":"'.$fileUrl.'"}';
    		}
		    
		    $params = [
                "action_name" => "QR_LIMIT_STR_SCENE",
                "action_info" => [
                    'scene' => ['scene_str' => $qrStr],
                ],
            ];
     
    	  	$ewm = self::curl_post($url,$params);
    	  	$result = json_decode($ewm, true);
    	    if(isset($result['errmsg'])){
    	        return '{"code":0,"message":"生成失败，失败原因：'.$result['errmsg'].'"}';
    	    }
            
            $showUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
    	    $codeUrl = $showUrl."?ticket=".$result['ticket'];
    	    
    	    $ewm = file_get_contents($codeUrl);
    	    $newFile = fopen($newFilePath,"w"); //打开文件准备写入
    		fwrite($newFile,$ewm); //写入二进制流到文件
    		fclose($newFile);
    	 
    	   
            // return '{"code":1,"message":"","data":"'.$codeUrl.'"}';
    	    return '{"code":1,"message":"","data":"'.$fileUrl.'"}';
		}
		
		
	  	return '{"code":1,"message":"","data":"'.$url.'"}';
	}
	
	public static function getAccessToken($type = 3)
	{
		global $db,$comId;
		$token_file = cache_get('token',$comId);
		if(true){
			$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=$type limit 1");
			if(empty($weixin_set)||empty($weixin_set->info)){
				return '{"code":0,"message":"微信配置有误，无法登录"}';
			}
			$weixin_arr = json_decode($weixin_set->info);
			$appid = $weixin_arr->appid;
			$appsecret = $weixin_arr->appsecret;
			$token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		  	$token_info = self::https_request($token_url);
		  //	var_dump($token_info);die;
		  	//file_put_contents('request.txt',json_encode($token_info,JSON_UNESCAPED_UNICODE));
		  //	cache_push('token',$comId,$token_info,110);
		  //	var_dump($token_info);
		  	return $token_info['access_token'];
		}else{
		  	return $token_file->access_token;
		}
	}
	
	public static function https_request($url)
	{
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, $url);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
	    curl_setopt($curl,CURLOPT_HEADER,0); //
	    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10); //
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);        
	    $response = curl_exec($curl);  
	    curl_close($curl);
	    $jsoninfo = json_decode($response,true); 
	    return $jsoninfo;
	}
	
	public static function curl_post($url, array $params = array())
	{
		$data_string = json_encode($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt(
			$ch,
			CURLOPT_HTTPHEADER,
			array(
			'Content-Type: application/json'
			)
		);
		$data = curl_exec($ch);
		curl_close($ch);
		return ($data);
	}
	
	/**
	 * 获取品牌列表
	 * 
	 **/
	public function brandList(){
		global $db,$request,$comId;
		$userId = (int)$request['user_id'];
        $data = $db->get_results("select * from demo_product_brand where comId=$comId");
        $return['code'] = 1;
        $return['message'] = '请求成功';
        $return['data'] = $data;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
        
	}
	
	/**
     * znum 获取指定商品数量，不分页。分页不传值
     * pagenum 分页，每页显示多少条数据，默认10条
     * page 获取当前第几页数据
     * tags 获取当前标签的数据
     * price_section 获取价格区域的数据，比如0-50,50-100,100（表示100以上）
     * channel_id  获取指定商品分类的数据
     * keyword 获取搜索关键词的数据，匹配商品设置的关键词和标题
     * if_tuangou 是否获取团购数据，0否，1是
     * shoucang 是否获取我的收藏数据，0否，1是
     * user_id 用户ID，shoucang=1必传
     * order1=price 商品价格排序
     * order2=desc/asc 倒序或正序
     */
	public function goodsplist()
	{
	    global $db,$request,$comId;
	    $znum=(int)$request['znum'];
	    $pagenum=(int)$request['pagenum'];
	    $page=(int)$request['page'];
	    $tags=$request['tags'];
	    $price_section=$request['price_section'];
	    $channelId = (int)$request['channel_id'];
	    $keyword = $request['keyword'];
	    $if_tuangou = (int)$request['if_tuangou'];
	    $shoucang = (int)$request['shoucang'];//我的收藏
	    $userId=(int)$request['user_id'];
	    $order1=$request['order1'];
	    $order2=$request['order2'];
	    $tijiao="";
	    $limit="";
	    if(!empty($tags)){
	        $tijiao.=" and a.tags like'%$tags%'";
	    }
	    if($znum>0){
	        $limit="limit $znum";
	    }else{
	        if($page<1){
	            $page=1;
	        }
		    if(empty($pagenum)){
		        $pagenum=10;
		    }
		    $limit="limit ".(($page-1)*$pagenum).",".$pagenum;
	    }
	    if(!empty($price_section)){
		    $prices=explode('-',$price_section);
		    foreach($prices as $pp=>$kk){
		        if($pp==0){
		            $tijiao.=" and price_sale>=".$kk;
		        }else{
		            $tijiao.=" and price_sale<=".$kk;
		        }
		    }
		}
		if($channelId>0){
		    $tijiao.=" and a.channelId=$channelId";
		}
		if(!empty($keyword)){
		    $tijiao.=" and (a.title like '%$keyword%' or a.keywords like '%$keyword%')";
		}
		if($if_tuangou==1){
		    $tijiao.=" and b.sale_tuan=1 and b.tuan_num>0";
		}
		if($shoucang==1){
		    $arr=[];
		    $scids=$db->get_results("select distinct productId from user_pdt_collect where userId=$userId");
		    foreach($scids as $p){
		        array_push($arr,$p->productId);
		    }
		    $arr=implode(",",$arr);
		    $tijiao.=" and b.productId in($arr)";
		}
		$order="";
		if($order1=="price"&&$order2=="desc"){
		    $order=" b.price_sale desc,";
		}
		if($order1=="price"&&$order2=="asc"){
		    $order=" b.price_sale asc,";
		}
	    $sql="select a.title,a.originalPic,a.channelId,a.keywords,b.key_ids,b.key_vals,b.id as inventoryId,b.productId,b.price_sale,b.price_market,b.orders,b.sale_tuan,b.tuan_num from demo_product as a left JOIN demo_product_inventory as b on b.productId=a.id where a.status=1 and a.comId=$comId $tijiao group by b.productId ORDER BY $order b.orders desc,inventoryId asc $limit";
	    $product=$db->get_results($sql);
	    foreach($product as &$j){
	        if($j->originalPic!=""){
    	        $imgs=explode('|',$j->originalPic);
    	        $j->originalPic=$imgs[0];
    	        $j->kucun=$db->get_var("select kucun from demo_kucun where comId=$comId and inventoryId=$j->inventoryId");
	        }
	    }
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '获取商品列表成功';
		$return['data'] = $product;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
  	//活动：1-普通商品  3-秒杀   4-团购  
	private function inventoryActivity($inventoryId){
	    global $db,$comId;
	    
	    $type = 1;//普通商品
	    $inventory = $db->get_row("select * from demo_product_inventory where id = $inventoryId");
	    if($inventory->sale_tuan == 1 && $inventory->tuan_num > 0){
	        $type = 4;//团购商品
	    }else{
	        $now = date('Y-m-d H:i:s');
	        $cuxiao_jilus = $db->get_results("select id,endTime,pdtIds from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and accordType=3 order by id desc ");
            foreach ($cuxiao_jilus as $ck => $jilu){
                $pdtIds = explode(',', $jilu->pdtIds);
                if(in_array($inventoryId, $pdtIds)){
                    $type = 3;
                    break;
                }
            }
	    }
	    
	    return $type;
	}
	
}