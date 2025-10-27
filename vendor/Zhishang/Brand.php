<?php
namespace Zhishang;

class Brand
{
    
    public function compar()
    {
        global $db,$request,$comId;
		
        $fenbiao = getFenbiao($comId,20);
        $productIds = $request['productIds'];
        $sql="select i.productId,min(i.id) as inventoryId,min(i.price_sale) as price_sale,min(i.fanli_shequ) as fanli_shequ,sum(i.orders) as orders,sum(i.views) as views,i.title,i.dtTime,i.image,i.price_market,i.comId,i.ordering from demo_product_inventory i left join demo_product_params p on p.productId = i.productId where i.comId=$comId and i.if_lingshou=1  and i.productId in ($productIds) and i.status=1 group by i.productId  "; //779 积分产品
        
        $pdts = $db->get_results($sql);

		$return = array();
		$return['code'] = 1;
		$return['message'] = '返回数据成功';
		$return['data'] = array();
		
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
        		if($root_id == 864){
        		    $data['default_img'] ="https://admin.bio-swamp.com/upload/抗体.jpg"; 
        		    $data['img'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/product/$root_id/$pro->skuId/$pro->skuId".'_1.jpg'; 
        		}else{
        		    $data['img'] =$originalPic_;
        		    $data['default_img'] =$originalPic_;
        		}	
				
				
			//	$data['img'] = empty($inventory->originalPic)?ispic($pro->originalPic):ispic($inventory->originalPic);
				$data['imgs'] = explode('|',$pro->originalPic);
				$data['inventoryId'] = $pdt->inventoryId;
				
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
	
				$data['key_vals'] = $inventory->key_vals;
				$data['subtitle'] = $pro->subtitle;
				$data['views'] = $pdt->views;
	
				$return['data'][] = $data;
			}
		}
		
		$cache_content = json_encode($return,JSON_UNESCAPED_UNICODE);
	
		return $cache_content;
        
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function refund()
    {
        global $db, $request, $comId;
        
        $refunds = $db->get_results("select * from demo_product_refund order by ordering desc");
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $refunds;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function index()
    {
        global $db, $request, $comId;
        
        $channels = $db->get_results("select id channel_id,title,en_title from demo_product_channel where comId=$comId and parentId = 0 order by ordering desc");
        foreach ($channels as &$channel) {
            $childs = $db->get_results("select id channel_id,title,en_title,originalPic,ext_originalPic,miaoshu,en_miaoshu from demo_product_channel where parentId = $channel->channel_id order by ordering desc ");
            $channel->child = $childs;
        }
        $domain = $db->get_results("select id domain_id, title, en_title, originalPic,ext_originalPic from demo_product_brand where parentId = 0  order by ordering desc ");
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data']['channels'] = empty($channels) ? array() : $channels;
        $return['data']['domain'] = $domain;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function brandChannel()
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
			$departments = $db->get_results("select * from demo_product_brand where comId=$comId and parentId=0 order by ordering desc,id asc");
			$departs = array();
			if(!empty($departments)){
				foreach($departments as $department){
				    $department->originalPic=HTTP_URL.$department->originalPic;
					$departments1 = $db->get_results("select * from demo_product_brand where parentId=".$department->id."  order by ordering desc,id asc");
					$departs1=array();
					if(!empty($departments1)){
						foreach($departments1 as $department1){
						    $department1->originalPic=HTTP_URL.$department1->originalPic;
							$departments2 = $db->get_results("select * from demo_product_brand where parentId=".$department1->id." order by ordering desc,id asc");
							$departs2 = array();
							if(!empty($departments2)){
								foreach($departments2 as $department2){
								    $department2->originalPic=HTTP_URL.$department2->originalPic;
									$departments3 = $db->get_results("select * from demo_product_brand where parentId=".$department2->id." order by ordering desc,id asc");
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
    
    public function studyChannel()
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
			$departments = $db->get_results("select * from demo_study_channel where comId=$comId and parentId=0 order by ordering desc,id asc");
			$departs = array();
			if(!empty($departments)){
				foreach($departments as $department){
				    $department->originalPic=HTTP_URL.$department->originalPic;
					$departments1 = $db->get_results("select * from demo_study_channel where parentId=".$department->id."  order by ordering desc,id asc");
					$departs1=array();
					if(!empty($departments1)){
						foreach($departments1 as $department1){
						    $department1->originalPic=HTTP_URL.$department1->originalPic;
							$departments2 = $db->get_results("select * from demo_study_channel where parentId=".$department1->id." order by ordering desc,id asc");
							$departs2 = array();
							if(!empty($departments2)){
								foreach($departments2 as $department2){
								    $department2->originalPic=HTTP_URL.$department2->originalPic;
									$departments3 = $db->get_results("select * from demo_study_channel where parentId=".$department2->id." order by ordering desc,id asc");
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
	
	public function studyInfo()
	{
	    global $db,$request,$comId;
   
        $id = $request['id'];
        $userId= $request['user_id'];
        $info = $db->get_row("select * from demo_study where id = $id and is_del = 0 and status = 1 ");
        if(!$info){
            return '{"code":0,"message":"学习资料已经下架或者已经被删除！"}';
        }
        $info->channel = $db->get_row("select * from demo_study_channel where id = $info->channelId");
        $info->is_zengsong = 0;
        if($userId){
            $row = $db->get_row("select id from user_jifen8 where userId= $userId and video_id = $id");
            $info->is_zengsong = empty($row) ? 0 :1;
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $info;
        return json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	
	//看视频增加积分
	public function addJifen(){
	    global $db,$request,$comId;  
	    $id = $request['id'];
        $userId = $request['user_id'];
        $row = $db->get_row("select id from user_jifen8 where userId= $userId and video_id = $id");
        if($row){
            return '{"code":0,"message":"该视频已返积分！"}';
        }
	    $jifen = $db->get_var("select video_jifen from demo_shezhi where comId=888");
	    $db->query("update users set jifen=jifen+$jifen where id=$userId");
	    $jifen_jilu = array();
		$jifen_jilu['userId'] = $userId;
		$jifen_jilu['comId'] = $comId;
		$jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
		$jifen_jilu['jifen'] = $jifen;
		$jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
		$jifen_jilu['type'] = 1;
	    $jifen_jilu['video_id'] = $id;
		$jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
		$jifen_jilu['remark'] = '看视频返积分';
	    $db->insert_update('user_jifen8',$jifen_jilu,'id');
	    
	    $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $jifen_jilu;
        return json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	
	
	
	public function studyList()
	{
	    global $db,$request,$comId;
   
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $channelId = (int)$request['channel_id'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);
        $sql="select * from demo_study where status = 1 and is_del=0 ";
    
        if($channelId > 0){
            $channelIds = $db->get_var("select group_concat(id) from demo_study_channel where parentId = $channelId or id = $channelId ");
            $channelIds = $db->get_var("select group_concat(id) from demo_study_channel where parentId in ($channelIds) or id in ($channelIds) ");
            
            $sql .= " and channelId in ($channelIds) ";
        }
        
        if(!empty($keyword)){
            $sql.=" and (title like '%$keyword%' or jianjie like '%$keyword%')";
        }
        
        $count = $db->get_var(str_replace('*','count(*)',$sql));

        $sql.=" order by ordering desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
        $pdts = $db->get_results($sql);
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = $pdt;

                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function faqChannel()
	{
	    global $db, $request, $comId;
        
        $language = (int)$request['language'];
        $channels = $db->get_results("select id channel_id,title from demo_faq where status=1 and is_del = 0 and language = $language order by ordering desc");
        foreach ($channels as &$channel) {
            $childs = $db->get_results("select * from demo_faq_detail where faqId = $channel->channel_id order by id asc ");
            $channel->child = $childs;
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data']['channels'] = empty($channels) ? array() : $channels;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	
	public function book()
	{
	    global $db,$request,$comId;
	    
	    $productId = $request['productId'];
	    $product = $db->get_row("select * from demo_product where id = $productId");
	    if(!$product){
	        return '{"code":0,"message":"为获取到商品信息"}';
	    }
        
        $type = (int)$request['type'];
	    $info = $db->get_row("select * from demo_product_book where skuId = '$product->skuId' and type = $type order by id desc ");
	    
	    if(!$info){
	        return '{"code":0,"message":"未上传说明书"}';
	    }
	    
	    if($info->content){
	        $info->content = json_decode($info->content, JSON_UNESCAPED_UNICODE);
	    }
	    
	    $channel = $db->get_row("select * from demo_product_channel where id = $product->channelId");
	    $templateId = 0;
	    
	    
	    if($type == 1){
	        if(!empty($channel->cnTemplateId)){
    	        $templateId = $channel->cnTemplateId;
    	    }elseif($channel->parentId > 0){
    	        $parentChannel = $db->get_row("select * from demo_product_channel where id = $channel->parentId");
    	        $templateId = $parentChannel->cnTemplateId;
    	    }
	    }else{
	        if(!empty($channel->templateId)){
    	        $templateId = $channel->templateId;
    	    }elseif($channel->parentId > 0){
    	        $parentChannel = $db->get_row("select * from demo_product_channel where id = $channel->parentId");
    	        $templateId = $parentChannel->templateId;
    	    }
	    }
	    
	    //获取上级分类id
	    //864  863  853    861  862
	    $root_id = 864;
	    if($product->channelId == 861 || $product->channelId == 862){
	        $root_id = $product->channelId;
	    }else{
	       $root_id = $db->get_var("select parentId from demo_product_channel where id = $product->channelId"); 
	    }
	    $return = array();
        $return['data']['images'] = '';
        
        $inventoryPic = $db->get_var("select originalPic from demo_product_inventory where productId = $product->id and originalPic is not null and originalPic <> '' ");
        if($product->originalPic){
             $return['data']['images'] = explode('|', $product->originalPic);
        }elseif($inventoryPic){
             $return['data']['images'] = explode('|', $inventoryPic);
        }else{
            $objectUrl = "product/$root_id/$product->skuId/";
                 $objectUrl = "img/$product->skuId/";
            $fileList = listObjectsFile($objectUrl, 100);
            if(!empty($fileList['data'])){
                 $return['data']['images']  = $fileList['data']; 
            }
        }
	   
	    

        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data']['templateId'] = $templateId;
        $return['data']['info'] = $info;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
	}
	
	public function stepList()
	{
	    global $db,$request,$comId;
   
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $channelId = (int)$request['channel_id'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);
        $sql="select * from demo_study_step where status = 1 and is_del=0 ";
    
        if($channelId > 0){
            
            $sql .= " and faqId = $channelId ";
        }
        
        if(!empty($keyword)){
            $sql.=" and (title like '%$keyword%' or jianjie like '%$keyword%')";
        }
        
        $count = $db->get_var(str_replace('*','count(*)',$sql));

        $sql.=" order by ordering desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
        $pdts = $db->get_results($sql);
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = $pdt;

                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function thinkInfo()
	{
	    global $db,$request,$comId;
   
        $id = (int)$request['thinkId'];
        $info = $db->get_row("select * from demo_product_think where id = $id and status = 1 and is_del = 0 ");
        if(!$info){
            return '{"code":0,"message":"未找到专题信息，请核实"}';    
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $info;
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function topicInfo()
	{
	    global $db,$request,$comId;
   
        $id = (int)$request['id'];
        $info = $db->get_row("select * from demo_product_topic where id = $id and status = 1 and is_del = 0 ");
        if(!$info){
            return '{"code":0,"message":"未找到专题信息，请核实"}';    
        }
        
        $info->thinks = $db->get_results("select id thinkId, title, en_title, originalPic from demo_product_think where topicId = $id and status = 1 and is_del = 0 order by ordering desc ");
        
        $info->channels = $db->get_results("select * from demo_product_channel where find_in_set($id, tags) order by ordering desc ");
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $info;
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function topics()
	{
	    global $db,$request,$comId;
   
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];

        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);
        $sql="select id,title,en_title from demo_product_topic where status = 1 and is_del=0 ";
    
        
        if(!empty($keyword)){
            $sql.=" and (title like '%$keyword%' or en_title like '%$keyword%')";
        }
        
        $count = $db->get_var(str_replace('id,title,en_title','count(*)',$sql));

        $sql.=" order by ordering desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
        $pdts = $db->get_results($sql);
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = $pdt;

                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	public function importProduct()
	{
	    global $db,$request,$comId;
	    
	    $request['pandianJsonData'] = '[["类目","研究领域","货号","产品名称","货期","价格规格","价格","宿主物种","修饰","偶联物","免疫原物种","基因敲除验证","基因ID","同义词","类型","蛋白编码","抗体分型","计算分子量","纯度","推荐稀释比","存储缓冲液","免疫原","交叉反应","特异性","文献引用","纯化方式","储存条件","靶点背景信息",null,null],["人ELISA","CAR-T相关","PAB45851","GAPDH Polyclonal Antibody","1天","20ul/50ul/100ul","218/480/920","Rabbit",null,"Unconjugated","Human",null,2597,"G3PD;GAPD;HEL-S-162eP","Polyclonal","P04406","IgG","31kDa/36kDa","36kDa","WB 1:1000 - 1:10000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human GAPDH",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","This gene encodes a member of the glyceraldehyde-3-phosphate dehydrogenase protein family. The encoded protein has been identified as a moonlighting protein based on its ability to perform mechanistically distinct functions. The product of this gene catalyzes an important energy-yielding step in carbohydrate metabolism, the reversible oxidative phosphorylation of glyceraldehyde-3-phosphate in the presence of inorganic phosphate and nicotinamide adenine dinucleotide (NAD). The encoded protein has additionally been identified to have uracil DNA glycosylase activity in the nucleus. Also, this protein contains a peptide that has antimicrobial activity against E. coli, P. aeruginosa, and C. albicans. Studies of a similar protein in mouse have assigned a variety of additional functions including nitrosylation of nuclear proteins, the regulation of mRNA stability, and acting as a transferrin receptor on the cell surface of macrophage. Many pseudogenes similar to this locus are present in the human genome. Alternative splicing results in multiple transcript variants.",null,null],["人ELISA","CAR-T相关","PAB45870","IFNG Polyclonal Antibody","2天","20ul/50ul/101ul","218/480/921","Rabbit",null,"Unconjugated","Human",null,3458,"IFNG;IFG;IFI","Polyclonal","P01579","IgG","19kDa","17kDa/23kDa13kDa","WB 1:500 - 1:1000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human IFNGn",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","This gene encodes a soluble cytokine that is a member of the type II interferon class. The encoded protein is secreted by cells of both the innate and adaptive immune systems. The active protein is a homodimer that binds to the interferon gamma receptor which triggers a cellular response to viral and microbial infections. Mutations in this gene are associated with an increased susceptibility to viral, bacterial and parasitic infections and to several autoimmune diseases.",null,null],["人ELISA","CAR-T相关","PAB45871","IL6 Polyclonal Antibody","3天","20ul/50ul/102ul","218/480/922","Rabbit",null,"Unconjugated","Human",null,3569,"IL6;BSF-2;BSF2;CDF;HGF;HSF;IFN-beta-2;IFNB2;IL-6","Polyclonal","P05231","IgG","23kDa","24-26kDa","WB 1:500 - 1:1000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human IL6",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","This gene encodes a cytokine that functions in inflammation and the maturation of B cells. In addition, the encoded protein has beennshown to be an endogenous pyrogen capable of inducing fever in people with autoimmune diseases or infections. The protein isnprimarily produced at sites of acute and chronic inflammation, where it is secreted into the serum and induces a transcriptionalninflammatory response through interleukin 6 receptor, alpha. The functioning of this gene is implicated in a wide variety ofninflammation-associated disease states, including suspectibility to diabetes mellitus and systemic juvenile rheumatoid arthritis.nAlternative splicing results in multiple transcript ",null,null],["人ELISA","CAR-T相关","PAB45872","IL2 Polyclona lAntibody","4天","20ul/50ul/103ul","218/480/923","Rabbit",null,"Unconjugated","Human",null,3558,"IL2;IL-2;TCGF;lymphokine","Polyclonal","P60568","IgG","17kDa","18kDa","WB 1:500 - 1:1000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human IL2n",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","The protein encoded by this gene is a secreted cytokine that is important for the proliferation of T and B lymphocytes. The receptor ofnthis cytokine is a heterotrimeric protein complex whose gamma chain is also shared by interleukin 4 (IL4) and interleukin 7 (IL7). Thenexpression of this gene in mature thymocytes is monoallelic, which represents an unusual regulatory mode for controlling the precisenexpression of a single gene. The targeted disruption of a similar gene in mice leads to ulcerative colitis-like disease, which suggests an essential role of this gene in the immune response to antigenic stimuli",null,null],["人ELISA","CAR-T相关","PAB45874","PSMA7 Polyclonal Antibody","5天","20ul/50ul/104ul","218/480/924","Rabbit",null,"Unconjugated","Human",null,5688,"C6;HEL-S-276;HSPC;RC6-1;XAPC7","Polyclonal","O14818","IgG","16kDa/20kDa/27kDa","30kDa","WB 1:500 - 1:1000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human PSMA7n",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","Histones are basic nuclear proteins that are responsible for the nucleosome structure of the chromosomal fiber in eukaryotes. Thisnstructure consists of approximately 146 bp of DNA wrapped around a nucleosome, an octamer composed of pairs of each of the four core histones (H2A, H2B, H3, and H4). The chromatin fiber is further compacted through the interaction of a linker histone, H1, with the DNA between the nucleosomes to form higher order chromatin structures. This gene is intronless and encodes a replication-dependentnhistone that is a member of the histone H3 family. Transcripts from this gene lack polyA tails; instead, they contain a palindromicntermination element. This gene is found in the large histone gene cluster on chromosome 6p22-p21.3",null,null],["人ELISA","CAR-T相关","PAB45875","TNFRSF11A Polyclonal Antibody","6天","20ul/50ul/105ul","218/480/925","Rabbit",null,"Unconjugated","Human",null,8792,"CD265;FEO;LOH18CR1;ODFR;OFE;OPTB7;OSTS;PDB2;RANK;TRANCER","Polyclonal","Q9Y6Q6","IgG","28kDa/32kDa/36kDa/56kDa/64kDa/66kDa","90kDa","WB 1:500 - 1:1000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human TNFRSF11A",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","The protein encoded by this gene is a member of the TNF-receptor superfamily. This receptors can interact with various TRAF familynproteins, through which this receptor induces the activation of NF-kappa B and MAPK8/JNK. This receptor and its ligand arenimportant regulators of the interaction between T cells and dendritic cells. This receptor is also an essential mediator for osteoclastnand lymph node development. Mutations at this locus have been associated with familial expansile osteolysis, autosomal recessivenosteopetrosis, and Paget disease of bone. Alternatively spliced transcript variants have been described for",null,null],["人ELISA","CAR-T相关","PAB45876","MIF Polyclonal Antibody","7天","20ul/50ul/106ul","218/480/926","Rabbit",null,"Unconjugated","Human",null,4282,"GIF, GLIF, L dopachrome isomerase, L dopachrome tautomerase, MIF, MMIF, Phenylpyruvatentautomerase","Polyclonal","P14174","IgG","12kDa","12kDa","WB 1:500 - 1:1000nIHC 1:50 - 1:200nIF 1:50 - 1:200","Buffer:PBS with 1% BSA, 0.03% Proclin300 and 50% Glycerol.","Recombinant protein of Human MIFn",null,null,"知了窝链接","Affinity purification","Store at -20℃. Avoid freeze / thaw cycles.","This gene encodes a lymphokine involved in cell-mediated immunity, immunoregulation, and inflammation. It plays a role in thenregulation of macrophage function in host defense through the suppression of anti-inflammatory effects of glucocorticoids. Thisnlymphokine and the JAB1 protein form a complex in the cytosol near the peripheral plasma membrane, which may indicate an additional role in integrin signaling pathways.",null,null]]';
            
            $pandianJsonData = stripcslashes($request['pandianJsonData']);
            $jilus = json_decode($pandianJsonData,true);
            
            echo '<pre>';

            $hasSns = array();
            $errorJilus = array();
            $prev = array();
            if(!empty($jilus)){
                $formRow = array_shift($jilus);
                foreach($jilus as $jiu){
                    $channelTitle = $jiu[0];
                    $brandTitle = $jiu[1];
                    $skuId = $jiu[2];
                    $title = $jiu[3];
                    $skuDay = $jiu[4];
                    $specs = $jiu[5];
                    $prices = $jiu[6];
                    
                    // Step1 - 先存入主表数据
                    $channelId = (int)$db->get_var("select id from demo_product_channel where title = '$channelTitle' or en_title = '$channelTitle' ");
                    $brandId = (int)$db->get_var("select id from demo_product_brand where title = '$brandTitle' or en_title = '$brandTitle' ");
                    $product = array(
                        'comId' => 888,
                        'skuId' => $skuId,
                        'skuDay' => $skuDay,
                        'title' => $title,
                        'channelId' => $channelId,
                        'brandId' => $brandId,
                        'status' => 1
                    );
                    
                    // $productId = 2499;
                    $db->insert_update("demo_product", $product, "id");
                    $productId = $db->get_var("select last_insert_id();");
                    
                    if($productId){
                        // Step2 - 存入规格表数据
                        $priceArr = explode('/', $prices);
                        $specArr = explode('/', $specs);
                        $num = count($priceArr);
                        $db->query("insert into demo_product_key(productId,title,parentId,kg) value($productId,'".$formRow[5]."',0,0)");
                        $parentKeyId = $db->get_var("select last_insert_id();");
                        if($parentKeyId){
                            for($i = 0; $i < $num; $i++){
                                
                                $db->query("insert into demo_product_key(productId,title,parentId,kg) value($productId,'".$specArr[$i]."', $parentKeyId, $i)");
                                $key_ids = $db->get_var("select last_insert_id();");
                                if($key_ids){
                                    $inventory = array();
                                    $inventory['comId'] = 888;
                                    $inventory['productId'] = $productId;
                                    $inventory['channelId'] = $channelId;
                                    $inventory['title'] = $title;
                                    $inventory['key_vals'] = $specArr[$i];
                                    $inventory['key_ids'] = $key_ids;
                                    $inventory['price_sale'] = $priceArr[$i];
                                    $inventory['sn'] = $skuId."-".rand(1000,9999);
                                    $inventory['price_market'] = $priceArr[$i];
                                    $inventory['price_cost'] = 0;
                                    $inventory['dtTime'] = date("Y-m-d H:i:s");
                                    $inventory['status'] = 1;
                                    $inventory['if_lingshou'] = 1;
                                    
                                    $db->insert_update("demo_product_inventory", $inventory, "id");
                                }
                            }
                        }
                    }
                    
                    //Step3 添加副表数据
                    $param = array();
                    $param['id'] = (int)$db->get_var("select productId from demo_product_params where productId = $productId ");
                    $param['productId'] = $productId;
                    $param['channelId'] = $channelId;
                    $param['brandId'] = $brandId;
                    foreach ($formRow as $k => $val){
                        if($k > 6){//从第六个开始计算
                            $field = $db->get_row("select * from demo_product_fields where is_del = 0 and title = '$val' ");
                            if($field){
                                $param[$field->field_title] = $jiu[$k] ? $jiu[$k] : '';
                            }
                        }
                    }
 
                    $db->insert_update("demo_product_params", $param, "id");
                }
            }
	}
	
	
	
}