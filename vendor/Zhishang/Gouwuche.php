<?php
namespace Zhishang;
class Gouwuche{
    
    public function lists(){
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $user_level = $db->get_var("select level from users where id=$userId");
        $gouwuche = array();
        $content = $db->get_var("select content from demo_gouwuche where userId=$userId and comId=$comId");
        if(!empty($content))$gouwuche=json_decode($content,true);
        
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array();
        if(!empty($gouwuche)){
            foreach ($gouwuche as $val) {
                $inventory = $db->get_row("select * from demo_product_inventory where id = ".$val['inventoryId']);
                $product = $db->get_row("select * from demo_product where id = ".$val['productId']);
                
                $val['price_sale'] = $inventory->price_sale;
                $val['kucun'] = (int)$db->get_var("select kucun from demo_kucun where inventoryId = ".$val['inventoryId']);
                
                $return['data'][] = $val;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function listsBak(){
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $user_level = $db->get_var("select level from users where id=$userId");
        $gouwuche = array();
        $content = $db->get_var("select content from demo_gouwuche where userId=$userId and comId=$comId");
        if(!empty($content))  $gouwuche=json_decode($content,true);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '获取成功';
        $return['data'] = array();
        if(!empty($gouwuche)){
            $mendianIdArr = [];
            foreach ($gouwuche as $val){
                $mendianIdArr[] = $val['mendianId'];
            }
            $mendianIdArr = array_unique($mendianIdArr);
            $data = [];
            foreach ($mendianIdArr as $mendianId){
                if($mendianId == 0){
                    $shop = array(
                        'id' => 0,
                        'title' => '平台自营',
                        'originalPic' => $db->get_var("select com_logo from demo_shezhi where comId = $comId")
                    );
                }else{
                    $shop = $db->get_row("select id, title, originalPic from demo_shequ where id = $mendianId");
                }
                
                $glist = [];
                foreach ($gouwuche as $val) {
                    if($val['mendianId'] == $mendianId){
                        $kucun =\Zhishang\Product::get_product_kucun($val['inventoryId'],0);
                        $val['kucun'] = $kucun>0 ? (int)$kucun : 0;
                        $val['price_sale'] = \Zhishang\Product::get_user_zhekou($val['inventoryId'],$val['price_sale'],$user_level);
                        $glist[] = $val;
                    }
                }
                
                $temp = array(
                    'shop' => $shop,
                    'list' => $glist
                );
                
                $data[] = $temp;
            }
            
            $return['data'] = $data;
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    
    public function add(){
        global $db,$request,$comId;
        $item = array();
        $userId = (int)$request['user_id'];
        $item['inventoryId'] = (int)$request['inventoryId'];
        $item['skuId'] = $skuId = $request['skuId'];

        $item['num'] = $request['num'];
        if(empty($request['inventoryId'])){
            $productId = $db->get_var("select * from demo_product where skuId = '$skuId' ");
            $inventory = $db->get_row("select * from demo_product_inventory where productId=$productId");
        }else{
            $inventory = $db->get_row("select * from demo_product_inventory where id=".$item['inventoryId']);
            $productId = $inventory->productId;
        }
        
        if(empty($inventory)){
            return '{"code":0,"message":"添加失败！未找到商品信息"}';
        }
        
        $skuId = $db->get_var("select skuId from demo_product where id = $productId ");
        $item['inventoryId'] = $inventory->id;
        
        //if($_SESSION['peisong_type']==4)$inventory->price_sale=$inventory->price_diancan;
        $item['productId'] = $inventory->productId;
        $item['comId'] = $inventory->comId;
        $item['if_kuaidi'] = $inventory->if_kuaidi;
        $item['skuId'] = $db->get_var("select skuId from demo_product where id = $inventory->productId");
        $item['channelId'] = $inventory->channelId;
        $item['price_sale'] = $inventory->price_sale;
        $item['price_market'] = $inventory->price_market;
        $item['price_gonghuo'] = $inventory->price_gonghuo;
        $item['title'] = $inventory->title;
        $item['key_vals'] = $inventory->key_vals;
        $item['image'] = $inventory->image;
        
        $root_id = 864;
	    $channelId = $db->get_row("select parentId,miaoshu_originalPic from demo_product_channel where id = $inventory->channelId"); 
	    if($inventory->channelId == 861 || $inventory->channelId == 862){
	        $root_id = $inventory->channelId;
	    }else{
	        $root_id = $channelId->parentId;
	    }
	    $originalPic_ = $channelId->miaoshu_originalPic;
	  //  $objectUrl = "product/$root_id/$product->skuId/";
//         $fileList = listObjectsFile($objectUrl, 100);
//         if(!empty($fileList['data'])){
//              $originalPics = $fileList['data']; 
//         }
	    if($root_id == 864){
    	    $item['default_img'] ="https://admin.bio-swamp.com/upload/抗体.jpg"; 
    	    $item['image'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/product/$root_id/$skuId/$skuId".'_1.jpg'; 
    	    $item['image'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$skuId/$skuId".'_1.jpg'; 
    	}else{
            $item['image'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$skuId/$skuId".'_1.jpg'; 
    	    $item['default_img'] =$originalPic_;
    	}	    
        		
        
        $item['mendianId'] = $db->get_var("select mendianId from demo_product where id = $inventory->productId");
        $gouwuche = array();
        $g = $db->get_row("select content,comId from demo_gouwuche where userId=$userId and comId=$comId limit 1");
        if(!empty($g)){
            $content = $g->content;
            if(!empty($content)){
                $gouwuche = json_decode($content,true);
            }
        }
        if(count($gouwuche)>=20){
            return '{"code":0,"message":"添加失败！购物车最多能添加20种商品，请分开下单"}';
        }
        if(array_key_exists($item['inventoryId'],$gouwuche)){
            $gouwuche[$item['inventoryId']]['num'] += $item['num'];
        }else{
            $gouwuche[$item['inventoryId']] = $item;
        }
        $gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
        if(empty($g)){
            $db->query("insert into demo_gouwuche(comId,userId,content) value($comId,$userId,'$gouwucheStr')");
        }else{
            $db->query("update demo_gouwuche set content='$gouwucheStr' where userId=$userId and comId=$comId");
        }
        $count = 0;
        foreach ($gouwuche as $g){
            $count+=$g['num'];
        }
        return '{"code":1,"message":"添加成功","count":'.$count.'}';
    }
    function del(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $ids =  explode(',',$request['inventoryId']);
        $gouwuche = array();
        $g = $db->get_row("select content,comId from demo_gouwuche where userId=$userId and comId=$comId");
        if(!empty($g)){
            $content = $g->content;
            if(!empty($content)){
                $gouwuche = json_decode($content,true);
            }
        }
        if(!empty($ids)){
            foreach ($ids as $id) {
                unset($gouwuche[$id]);
            }
        }
        $gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
        $db->query("update demo_gouwuche set content='$gouwucheStr' where userId=$userId and comId=$comId");
        $count = 0;
        foreach ($gouwuche as $g){
            $count+=$g['num'];
        }
        return '{"code":1,"message":"删除成功","count":'.$count.'}';
    }
    function updateNum(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $id = (int)$request['inventoryId'];
        $num = $request['num'];
        $gouwuche = array();
        $g = $db->get_row("select content,comId from demo_gouwuche where userId=$userId and comId=$comId");
        if(!empty($g)){
            $content = $g->content;
            if(!empty($content)){
                $gouwuche = json_decode($content,true);
                if(array_key_exists($id,$gouwuche)){
                    $gouwuche[$id]['num'] = $num;
                }else{
                    return '{"code":0,"message":"尚未添加该商品"}';
                }
            }
        }
        $gouwucheStr = json_encode($gouwuche,JSON_UNESCAPED_UNICODE);
        $db->query("update demo_gouwuche set content='$gouwucheStr' where userId=$userId and comId=$comId");
        $count = 0;
        foreach ($gouwuche as $g){
            $count+=$g['num'];
        }
        return '{"code":1,"message":"更新成功","count":'.$count.'}';
    }
    function delAll(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $db->query("update demo_gouwuche set content='' where userId=$userId and comId=$comId");
        return '{"code":1,"message":"操作成功","count":0}';
    }
}