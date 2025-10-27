<?php

namespace Zhishang;

//require_once(ABSPATH.'/aliyunoss/autoload.php');
use OSS\OssClient;
use OSS\Core\OssException;

class Index
{
    
    public function salesman()
    {
        global $db, $comId, $request; 
        
        $data = $db->get_results("select * from demo_salesman order by ordering desc,id desc ");
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $data;

        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function setKucun()
    {
        global $db, $comId, $request; 
        $inventorys = $db->get_results("select id,productId from demo_product_inventory order by id desc limit 0, 10000");
        
        $num = 0;
        foreach ($inventorys as $inventory){
            $hadKun = $db->get_row("select * from demo_kucun where inventoryId = $inventory->id ");
            if($hadKun){
                continue;
            }
            
            $kucun = array(
                'comId' => $comId,
                'inventoryId' => $inventory->id,
                'productId' => $inventory->productId,
                'storeId' => 5,
                'entitle' => 'Z',
                'kucun' => 100
            );
            
            $db->insert_update("demo_kucun", $kucun, "id");
            $num++;
        }
        
        // $channelIds = $db->get_var("select group_concat(id) from demo_product_channel where id = 864 or parentId = 864");
        // $products = $db->get_results("select p.id pid,a.synonym,a.id aid from demo_product p inner join demo_product_params a on a.productId=p.id where p.channelId in ($channelIds) and a.synonym <> ''  limit 0, 5000 ");
        
        // foreach ($products as $pro){
        //     $db->query("update demo_product_params set synonym = '' where id = $pro->aid");
        // }
        
        var_dump($num);die;
        
        
        
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = count($inventorys);

        return json_encode($return, JSON_UNESCAPED_UNICODE);
        
    }
    
    public function getArea()
    {
        global $db, $comId, $request;
        
        $parentId = (int)$request['parent_id'];
        $sql = "select id,title as name,parentId as parent_id from demo_area where 1=1";
        if (!empty($parentId)) {
            $sql .= " and parentId=$parentId";
        } else {
            $sql .= " and parentId=0";
        }
        $flist = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        foreach ($flist as $l) {
            $l->code = $l->id;
            $slist = $db->get_results("select id,title as name,parentId as parent_id from demo_area where 1=1 and parentId = $l->id");
            foreach ($slist as $sk => $cl) {
                $slist[$sk]->code = $cl->id;
                $tlist = $db->get_results("select id,title as name,parentId as parent_id from demo_area where 1=1 and parentId = $cl->id");
                foreach ($tlist as $ccl) {
                    $ccl->code = $ccl->id;
                }
                $slist[$sk]->child = $tlist;
            }
            $l->child = $slist;
            $return['data'][] = $l;
        }

        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function getLiteratures()
    {
        global $db, $comId, $request;
        
        $parentId = (int)$request['parent_id'];
        $sql = "select * from demo_iteratures where id > 0";
    
        $data = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = $data;
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    public function feedback()
    {
        global $db, $request, $comId;

        $tailor = [];
        $accessKey = ['content'];
        foreach ($accessKey as $val) {
            if (isset($request[$val])) {
                $tailor[$val] = $request[$val];
            } else {
                return '{"code":0,"message":"必要参数不能为空!"}';
            }
        }
        $tailor['userId'] = (int)$request['user_id'];
        $tailor['feed_type'] = $request['feed_type'];
        $tailor['originalPic'] = $request['images'];
        $tailor['email'] = $request['email'];
        $tailor['phone'] = $request['phone'] ? $request['phone'] : $db->get_var("select phone from users where id = " . $tailor['userId']);
        $tailor['name'] = $request['name'] ? $request['name'] : $db->get_var("select nickname from users where id = " . $tailor['userId']);
        $tailor['dtTime'] = date('Y-m-d H:i:s');
        $id = $db->insert_update("feedback_log", $tailor, 'id');
        if ($id) {
            return '{"code":1,"message":"信息反馈成功!"}';
        }

        return '{"code":0,"message":"信息反馈失败!"}';
    }

    public function index()
    {
        global $db, $request, $comId;
        
        $position = !empty($request['position'])?$request['position'] : 1 ; ////1pc 2 h5 3小程序
        $banners = $db->get_results("select originalPic,inventoryId,title,url from banner where comId=$comId and channelId=0 and position = $position order by ordering desc,id asc");
        $channels = $db->get_results("select * from banner_channel where comId=$comId order by ordering desc,id asc");
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array();
        $return['data']['banners'] = array();
        if (!empty($banners)) {
            $data = array();
            foreach ($banners as $i => $b) {
                $data[$i]->image = HTTP_URL . $b->originalPic;
                $data[$i]->inventory_id = $b->inventoryId;
                $data[$i]->title = $b->title;
                $data[$i]->channel_id = $b->channelId;
                $data[$i]->brand_id = 0;
                $data[$i]->url = $b->url;
				$data[$i]->type = '';
			    $data[$i]->tags = '';
                if($b->inventoryId>0){
					$data[$i]->channel_id = 0;
					$data[$i]->type = 'product';
                }else if(!empty($b->url) && strstr($b->url,'channel_id')){
					if(strstr($b->url,'channel_id')){
						$urlarr = explode('channel_id=',$b->url);
						$data[$i]->channel_id = (int)$urlarr[1];
						$data[$i]->type = 'channel';
					}
				}else if(!empty($b->url) && strstr($b->url,'tags')){
					if(strstr($b->url,'tags')){
						$urlarr = explode('tags=',$b->url);
						$data[$i]->tags = $urlarr[1];
						$data[$i]->type = 'tags';
					}
				}else if(!empty($b->url) && strstr($b->url,'brand_id')){
					if(strstr($b->url,'brand_id')){
						$urlarr = explode('brand_id=',$b->url);
						$data[$i]->brand_id = $urlarr[1];
						$data[$i]->type = 'brand';
					}
				}
            }
            
            $return['data']['banners'] = $data;
        }
        
        $return['data']['adverts'] = array();
        if (!empty($channels)) {
            foreach ($channels as $key => $val) {
                $banners = $db->get_results("select originalPic,inventoryId,title,url from banner where comId=$comId and channelId=$val->id and position = $position  order by ordering desc,id asc");
                if (!empty($banners)) {
                    $data = array();
                    foreach ($banners as $i => $b) {
                        $data[$i]->image = HTTP_URL . $b->originalPic;
                        $data[$i]->inventory_id = $b->inventoryId;
                        $data[$i]->title = $b->title;
                        $data[$i]->channel_id = $b->channelId;
                        $data[$i]->tags = '';
                        $data[$i]->brand_id = 0;
                        $data[$i]->url = $b->url;
                        $data[$i]->type = '';
                        if($b->inventoryId>0){
        					$data[$i]->channel_id = 0;
        					$data[$i]->type = 'product';
        				}else if(!empty($b->url) && strstr($b->url,'channel_id')){
        					if(strstr($b->url,'channel_id')){
        						$urlarr = explode('channel_id=',$b->url);
        						$data[$i]->channel_id = (int)$urlarr[1];
        						$data[$i]->type = 'channel';
        					}
        				}else if(!empty($b->url) && strstr($b->url,'tags')){
        					if(strstr($b->url,'tags')){
        						$urlarr = explode('tags=',$b->url);
        						$data[$i]->tags = $urlarr[1];
        						$data[$i]->type = 'tags';
        					}
        				}else if(!empty($b->url) && strstr($b->url,'brand_id')){
        					if(strstr($b->url,'brand_id')){
        						$urlarr = explode('brand_id=',$b->url);
        						$data[$i]->brand_id = $urlarr[1];
        						$data[$i]->type = 'brand';
        					}
        				}
                    }
                    
                    $advert = array();
                    $advert['title'] = $val->title;
                    $advert['images'] = $data;
                    $return['data']['adverts'][] = $advert;
                }
            }
        }
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function geturltype($url)
    {

    }
    
    public function popularBrands()
    {
        global $db, $comId;
        
        $page = empty($request['page']) ? 1 : (int)$request['page'];
        $pageNum = empty($request['page_num']) ? 10 : (int)$request['page_num'];
        $order1 = empty($request['order1']) ? 'ordering' : $request['order1'];
        $order2 = empty($request['order2']) ? 'desc' : $request['order2'];
        $if_index = (int)$request['if_index'];
        $sql = "select id brandId,title,originalPic,ordering from demo_product_brand where comId=$comId";
        
        if (!empty($keyword)) {
            $sql .= " and title like '%$keyword%'";
        }
        
        if($if_index){
            $sql .= " and if_index = $if_index ";
        }
        
        $count = $db->get_var(str_replace('id brandId,title,originalPic,ordering','count(distinct(id))',$sql));
        $sql .= " order by $order1 $order2 limit " . (($page - 1) * $pageNum) . "," . $pageNum;
        $lists = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array(
            'count' => $count,
            'list' => array()
        );
        if (!empty($lists)) {
            foreach ($lists as $list) {
               
                $return['data']['list'][] = $list;
            }
        }
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function kefu()
    {
        global $db, $comId;
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $return['data']['phone'] = $db->get_var("select com_phone from demo_shezhi where comId=$comId");
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function newsChannel()
    {
        global $db, $request, $comId;
        
        $parentId = (int)$request['channel_id'];
        $channels = $db->get_results("select id,title,originalPic from demo_list_channel where comId=$comId and parentId = $parentId order by id");
        foreach ($channels as $key => $channel){
            $channels[$key]->child = $db->get_results("select id,title,originalPic from demo_list_channel where comId=$comId and parentId = $channel->id order by id");
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = empty($channels) ? array() : $channels;
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function links()
    {
        global $db, $request, $comId;
        
        $links = $db->get_results("select id,title,originalPic as image,links from web_links where comId=$comId and parentId = 0 order by ordering, id");
        foreach ($links as &$link){
            $link->childs =  $db->get_results("select id,title,originalPic as image,links from web_links where comId=$comId and parentId = $link->id order by ordering, id");
            foreach ($link->childs as &$c){
                $c->childs =  $db->get_results("select id,title,originalPic as image,links from web_links where comId=$comId and parentId = $c->id order by ordering, id");
            }
        }
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $links;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function credentials()
    {
        global $db, $request, $comId;
        
        $channelId = $request['channel_id'];
        $keyword = $request['keyword'];
        $content_len = (int)$request['content_len'];
        $page = empty($request['page']) ? 1 : (int)$request['page'];
        $pageNum = empty($request['page_num']) ? 10 : (int)$request['page_num'];
        $order1 = empty($request['order1']) ? 'ordering' : $request['order1'];
        $order2 = empty($request['order2']) ? 'desc' : $request['order2'];
        $if_index = (int)$request['if_index'];
        $sql = "select  originalPic,inventoryId,title,en_title,url from banner where comId=$comId";
        if (!empty($channelId)) {
            $sql .= " and channelId=$channelId";
        }
        if (!empty($keyword)) {
            $sql .= " and title like '%$keyword%'";
        }
        
        $count = $db->get_var(str_replace('originalPic,inventoryId,title,en_title,url','count(distinct(id))',$sql));
        $sql .= " order by $order1 $order2 limit " . (($page - 1) * $pageNum) . "," . $pageNum;
        $lists = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array(
            'count' => $count,
            'list' => array()
        );
        if (!empty($lists)) {
            foreach ($lists as $list) {
                $list->image = $channel->originalPic;
                $return['data']['list'][] = $list;
            }
        }
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function newsList()
    {
        global $db, $request, $comId;
        
        $channelId = $request['channel_id'];
        $keyword = $request['keyword'];
        $content_len = (int)$request['content_len'];
        $language = (int)$request['language'];
        $page = empty($request['page']) ? 1 : (int)$request['page'];
        $pageNum = empty($request['page_num']) ? 10 : (int)$request['page_num'];
        $order1 = empty($request['order1']) ? 'ordering' : $request['order1'];
        $order2 = empty($request['order2']) ? 'desc' : $request['order2'];
        $if_index = (int)$request['if_index'];
        $sql = "select id,title,originalPic,content,dtTime,channelId,video,video_img,jianjie,views from demo_list where comId=$comId and language = $language and if_show = 1 ";
        if (!empty($channelId)) {
            if($channelId==29){
                $channelIds = $db->get_var("select group_concat(id) from demo_list_channel where parentId=29");
                $sql .= " and channelId in($channelIds)";
            }else{
                $sql .= " and channelId=$channelId"; 
            }
            
        }
        if (!empty($keyword)) {
            $sql .= " and title like '%$keyword%'";
        }
        
        if($if_index){
            $sql .= " and if_index = $if_index ";
        }
        
        $count = $db->get_var(str_replace('id,title,originalPic,content,dtTime,channelId,video,video_img,jianjie,views','count(distinct(id))',$sql));
        $sql .= " order by $order1 $order2 limit " . (($page - 1) * $pageNum) . "," . $pageNum;
        $lists = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array(
            'count' => $count,
            'list' => array()
        );
        if (!empty($lists)) {
            foreach ($lists as $list) {
                if ($content_len == 0) {
                    $list->content = '';
                } else {
                    $list->content = sys_substr(preg_replace('/((\s)*(\n)+(\s)*)/', '', strip_tags($list->content)), $content_len, true);
                }
                $channel = $db->get_row("select title,originalPic from demo_list_channel where id=$list->channelId");
                $list->channel_title = $channel->title;
                $list->image = $channel->originalPic;
                $return['data']['list'][] = $list;
            }
        }
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function newsDetail()
    {
        global $db, $request, $comId;
        
        $id = (int)$request['id'];
        $db->query("update demo_list set views = views +1 where id = $id ");
        $news = $db->get_row("select * from demo_list where id=$id and comId=$comId and if_show = 1 ");
        if (empty($news)) {
            return '{"code":0,"message":"新闻不存在或已删除！"}';
        }
        $channel = $db->get_row("select id,title,originalPic from demo_list_channel where id=$news->channelId");
        $content = preg_replace('/((\s)*(\n)+(\s)*)/', '', $news->content);
        $content = str_replace('src="/', 'src="http://' . $_SERVER['HTTP_HOST'] . "/", $content);
        $content = str_replace('src="https://beiyinlai.67.zhishangez.cn/', 'src="http://' . $_SERVER['HTTP_HOST'] . "/", $content);
        
       
        $content = str_replace('<img', '<img style="max-width:100%;height:auto;"', $content);
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
      
        $info = array();
        $info['title'] = $news->title;
        $info['dtTime'] = $news->dtTime;
        $info['originalPic'] = $news->originalPic;
        $info['jianjie'] = $news->jianjie;
        $info['content'] = $content;
        $info['channel_id'] = $channel->id;
        $info['channel_title'] = $channel->title;
        $info['channel_img'] = $channel->originalPic;
        $info['video'] = $news->video;
        $info['video_img'] = $news->video_img;
        $info['path'] = $news->path;
        $info['views'] = $news->views;
        
        $ifIndex = (int)$request['if_index'];
        $lastNewSql = "select id, title from demo_list where (ordering > $news->ordering or (ordering = $news->ordering and id < $news->id )) and id <> $news->id and channelId = $news->channelId and if_show = 1 ";//上一页
        $nextNewSql = "select id, title from demo_list where (ordering < $news->ordering or (ordering = $news->ordering and id > $news->id ))  and id <> $news->id  and channelId = $news->channelId and if_show = 1 ";//下一页
        if($ifIndex){
            $lastNewSql .= " and if_index = 1 ";
            $nextNewSql .= " and if_index = 1 ";
        }
        
        $lastNewSql .= " order by ordering desc,id asc limit 1";
        $nextNewSql .= " order by ordering asc,id asc limit 1";
        $lastNew = $db->get_row($lastNewSql);
        $nextNew = $db->get_row($nextNewSql);
        
        // $products = [];
        // if($news->product_channel){
        //     $pdtController = new \Zhishang\Product();
            
        //     $pdtNum = (int)$db->get_var("select tuijian_pnum from demo_shezhi where comId = $comId ");
           
        //     if($pdtNum){
        //         $request['channel_id'] = $news->product_channel;
        //         $request['pagenum'] = $pdtNum;
        //         $pdts_arr = json_decode($pdtController->plist());
        //         $products = $pdts_arr->data;
        //     }

        // }
   
        $return['data'] = array(
            'last_news' => $lastNew,
            'next_news' => $nextNew,
            'info' => $info,
            // 'recommend_proudcts' => $products
        );
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function article()
    {
        global $db, $request, $comId;
        $id = $request['article_id'];
        $article = $db->get_var("select content from demo_article where id=$id");
        $content = preg_replace('/((\s)*(\n)+(\s)*)/', '', $article);
        $content = str_replace('src="/', 'src="http://' . $_SERVER['HTTP_HOST'] . "/", $content);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $return['data']['content'] = $content;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    public function ossupload()
    {
        global $db, $request, $comId;
        require_once(ABSPATH . '/aliyunoss/autoload.php');
        require_once(ABSPATH . '/inc/class.paint.php');
        $accessKeyId = "LTAI5tQoZpND8jpkp3UbQn4B";
        $accessKeySecret = "T53TtDcFWbil7YYYXeElviNR5sgavw";
        $endpoint = "http://oss-cn-beijing.aliyuncs.com";
        $bucket = "sdhmx";
        $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
        global $db, $request;
        $crmdb = $db;
        // 	$comId = (int)$_SESSION[TB_PREFIX.'comId'];
        $picname = $_FILES['file']['name'];
        $picsize = $_FILES['file']['size'];
        //file_put_contents('request.txt',json_encode($request,JSON_UNESCAPED_UNICODE));
        if ($picname != "") {
            // 		if ($picsize > 2048000) {
            // 			echo '{"code":1,"msg":"图片不能大于2M","url":""}';
            // 			exit;
            // 		}
            $type = strrchr($picname, '.');
            $type = strtolower($type);
            if ($type != ".gif" && $type != ".jpg" && $type != ".png" && $type != ".jpeg" && $type != ".bmp" && $type != ".mp4" && $type != ".docx" && $type != ".doc") {
                echo '{"code":1,"msg":"文件格式不对！","url":""}';
                exit;
            }
            $rand = rand(10000, 99999);
            $pics = $comId . '_' . date("YmdHis") . $rand . $type;
            $lujing = ABSPATH . '/upload/' . date("Ymd") . '/';
            if (!is_dir($lujing)) {
                mkdir($lujing);
            }
            $pic_path = $lujing . $pics;
            move_uploaded_file($_FILES['file']['tmp_name'], $pic_path);
            $size = round($picsize / 1024 / 1024, 5);
            // 		if($request['limit_width']!='no'){
            // 			$paint = new Paint($pic_path);
            // 			$width = empty($request['width'])?800:$request['width'];
            // 			$height = empty($request['height'])?800:$request['height'];
            // 			$newImg = $paint->Resize($width,$height,'s_');
            // 			$newImg = ABSPATH.str_replace('..','',$newImg);
            // 			@unlink($pic_path);
            // 		}else{
            $newImg = $pic_path;
            // 		}
            if (!empty($newImg)) {
                try {
                    $ossClient->uploadFile($bucket, $comId . '/' . $pics, $newImg);
                    $image = "https://sdhmx.oss-cn-beijing.aliyuncs.com/" . $comId . '/' . $pics;
                    echo '{"code":1,"message":"上传成功","data":"' . $image . '"}';
                    exit;
                } catch (OssException $e) {
                    file_put_contents('upload_log.txt', $e->getMessage());
                    echo '{"code":1,"msg":"文件上传失败，请重试' . $e->getMessage() . '","url":""}';
                    exit;
                }
            }
        } else {
            echo '{"code":1,"msg":"未检测到文件","url":""}';
            exit;
        }
    }

    public function upload()
    {
        global $db, $request, $comId;
        $userId = (int)$request['user_id'];
        $if_touxiang = (int)$request['if_touxiang'];
        $target_path = "upload/";
        $return['code'] = 1;
        $return['message'] = '';
        if (!empty($_FILES['img']['name'])) {
            preg_match("/\.([a-zA-Z0-9]{2,4})$/", $_FILES['img']['name'], $exts);
            if ($exts[1] != 'gif' && $exts[1] != 'jpg' && $exts[1] != 'jpeg' && $exts[1] != 'bmp' && $exts[1] != 'png') {
                $return['code'] = 0;
                $return['message'] = '文件类型不正确，只支持gif，jpg，jpeg，bmp，png格式';
                return json_encode($return, JSON_UNESCAPED_UNICODE);
            }
            $fileName = date("YmdHis") . rand(1, 999) . '.' . $exts[1];
            $target_path1 = $target_path . $fileName;
            move_uploaded_file($_FILES['img']['tmp_name'], $target_path1);
            $image = 'https://' . $_SERVER['HTTP_HOST'] . '/' . $target_path1;
            if ($if_touxiang == 1) {
                $db->query("update users set image='$image' where id=$userId");
            }
            $return['message'] = '成功';
            $return['image'] = $image;
        } else {
            $return['code'] = 0;
            $return['message'] = '请上传图片！';
        }
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function recruitInfo()
    {
        global $db, $request, $comId;
        
        $id = $request['id'];
    
        $recruit = $db->get_row("select * from recruit where id=$id and is_del = 0 ");
        $recruit->channel_title = $db->get_var("select title from demo_recruit_channel where id = $recruit->channelId");
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '获取成功';
        $return['data'] = $recruit;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    public function applyJob(){
        global $db, $request, $comId;
        $data['name'] = $request['name'];
        $data['mobile'] = $request['mobile'];
        $data['email'] = $request['email'];
        $data['file'] = $request['file'];
        $data['job_id'] = $request['job_id'];
        $data['jbo_title']= $request['jbo_title'];
        $data['dtTime'] = date('Y-m-d H:i:s');
        $db->insert_update("apply_job", $data, 'id');
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '提交成功';
        $return['data'] = $data;
        return json_encode($return, JSON_UNESCAPED_UNICODE); 
    }
    
    public function recruitList()
    {
        global $db, $request, $comId;
        
        $channelId = $request['channel_id'];
        $keyword = $request['keyword'];
        $content_len = (int)$request['content_len'];
        $language = (int)$request['language'];
        $page = empty($request['page']) ? 1 : (int)$request['page'];
        $pageNum = empty($request['page_num']) ? 10 : (int)$request['page_num'];
        $order1 = empty($request['order1']) ? 'ordering' : $request['order1'];
        $order2 = empty($request['order2']) ? 'desc' : $request['order2'];
        $if_index = (int)$request['if_index'];
        $sql = "select * from recruit where is_del=0 and language = $language";
        if (!empty($channelId)) {
            $sql .= " and channelId=$channelId";
        }
        if (!empty($keyword)) {
            $sql .= " and title like '%$keyword%'";
        }
        
        $count = $db->get_var(str_replace('*','count(distinct(id))',$sql));
        $sql .= " order by $order1 $order2 limit " . (($page - 1) * $pageNum) . "," . $pageNum;
        $lists = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array(
            'count' => $count,
            'list' => array()
        );
        if (!empty($lists)) {
            foreach ($lists as $list) {
                if ($content_len == 0) {
                    $list->content = '';
                } else {
                    $list->content = sys_substr(preg_replace('/((\s)*(\n)+(\s)*)/', '', strip_tags($list->content)), $content_len, true);
                }
                $channel = $db->get_row("select title,originalPic from demo_list_channel where id=$list->channelId");
                $list->channel_title = $channel->title;
                $list->image = $channel->originalPic;
                $return['data']['list'][] = $list;
            }
        }
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    public function channelRecruits()
    {
        global $db, $request, $comId;
        
        $channelNum = empty($request['channel_num']) ? '' : 'limit ' . (int)$request['channel_num'];
        $pdtNum = empty($request['recruit_num']) ? 4 : (int)$request['recruit_num'];
        $channelId = $request['channel_id'];
        $tiaojian = " and is_hot = 1 ";
        if (!empty($channelId)) {
            $tiaojian = " and id in($channelId)";
        }

        $pdt_channels = $db->get_results("select id,title,en_title,originalPic,backimg from demo_recruit_channel where comId=$comId and parentId=0 $tiaojian order by ordering desc,id asc $channelNum");
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array();
        
        if (!empty($pdt_channels)) {
            foreach ($pdt_channels as $channel) {
                // $request['channel_id'] = $channel->id;
                // $request['pagenum'] = $pdtNum;
                // $pdts_arr = json_decode(self::recruitList());
                // $channel->recruits = $pdts_arr->data->list;
                $channel->channel_id = $channel->id;
   
                $return['data'][] = $channel;
            }
        }
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    //分类加商品
    public function channelPdts()
    {
        global $db, $request, $comId;
        $channelNum = empty($request['channel_num']) ? '' : 'limit ' . (int)$request['channel_num'];
        $pdtNum = empty($request['pdt_num']) ? 4 : (int)$request['pdt_num'];
        $channelId = $request['channel_id'];
        $tiaojian = "";
        if (!empty($channelId)) {
            $tiaojian = " and id in($channelId)";
        }

        $pdt_channels = $db->get_results("select id,title,originalPic,backimg from demo_product_channel where comId=$comId and parentId=0 $tiaojian order by ordering desc,id asc $channelNum");
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $pdtController = new \Zhishang\Product();
        if (!empty($pdt_channels)) {
            foreach ($pdt_channels as $channel) {
                $request['channel_id'] = $channel->id;
                $request['pagenum'] = $pdtNum;
                $pdts_arr = json_decode($pdtController->plist());
                $channel->products = $pdts_arr->data;
                $channel->originalPic = HTTP_URL . $channel->originalPic;
                $channel->backimg = HTTP_URL . $channel->backimg;
                $return['data'][] = $channel;
            }
        }
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    //首页弹窗
    public function tanchuang()
    {
        global $db, $request, $comId;
        $now = date("Y-m-d H:i:s");
        $tanchuang = $db->get_row("select * from demo_tanchuang where comId=$comId and startTime<'$now' and endTime>'$now' and status=1 limit 1");
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        if (!empty($tanchuang)) {
            $return['data']['id'] = $tanchuang->id;
            $return['data']['image'] = $tanchuang->image;
            $return['data']['inventoryId'] = $tanchuang->inventoryId;
            if ($tanchuang->inventoryId > 0) {
                $return['data']['channelId'] = 0;
                $return['data']['type'] = 'product';
            } else if (!empty($tanchuang->url)) {
                if (strstr($tanchuang->url, 'channel')) {
                    $urlarr = explode('channelId=', $tanchuang->url);
                    $return['data']['channelId'] = (int)$urlarr[1];
                    $return['data']['type'] = 'channel';
                } else if (strstr($tanchuang->url, 'yushou')) {
                    $return['data']['type'] = 'yushou';
                    $return['data']['channelId'] = 0;
                } else if (strstr($tanchuang->url, 'p=7')) {
                    $return['data']['type'] = 'miaosha';
                    $return['data']['channelId'] = 0;
                } else if (strstr($tanchuang->url, 'yhqList')) {
                    $return['data']['type'] = 'yhq';
                    $return['data']['channelId'] = 0;
                } else {
                    $return['data']['type'] = '';
                    $return['data']['channelId'] = 0;
                }
            } else {
                $return['data']['type'] = '';
                $return['data']['channelId'] = 0;
            }
        }
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    //秒杀列表
    public function miaoshas()
    {
        global $db, $request, $comId;
        $today = date("Y-m-d 00:00:00");
        $today_time = strtotime($today);
        $tomrrow = date("Y-m-d 00:00:00", strtotime('+1 day'));
        $now = date("Y-m-d H:i:s");
        $now_time = time();
        $miaoshas = $db->get_results("select * from cuxiao_pdt where comId=$comId and scene=1 and status=1 and endTime>'$now' and startTime<='$tomrrow' limit 5");
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        if (!empty($miaoshas)) {
            foreach ($miaoshas as $miaosha) {
                $data = array();
                $data['miaosha_id'] = $miaosha->id;
                $data['title'] = $miaosha->title;
                $data['time'] = strtotime($miaosha->startTime) > $today_time ? date("H:i", strtotime($miaosha->startTime)) : '00:00';
                $data['status'] = strtotime($miaosha->startTime) > $now_time ? 0 : 1;
                $data['endTime'] = strtotime($miaosha->endTime);
                $return['data'][] = $data;
            }
        }
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    //首页弹窗
    public function buymsg()
    {
        global $db, $comId;
        $fenbiao = getFenbiao($comId, 20);
        
        $orders = $db->get_results("select userId,product_json,dtTime from order$fenbiao where comId=$comId order by id desc limit 10");
        // if (empty($orders)) {
        //     return '{"code":0,"message":"暂无购买记录"}';
        // }
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();//1天前  5小时前   3小时前   2小时前  1小时前  半小时前 10分钟前 5分钟前 刚刚
        $data = array();
        if($orders){
            foreach ($orders as $order) {
                $u = $db->get_row("select nickname,image from users where id=$order->userId");
                $u->image = empty($u->image) ? 'http://' . $_SERVER['HTTP_HOST'] . '/skins/default/images/wode_1.png' : $u->image;
                $product_json = json_decode($order->product_json, true);
                $title = $product_json[0]['title'];
                $time = self::date_huan(strtotime($order->dtTime));
                $data[] = array("name" => $u->nickname, "product" => sys_substr($title, 10, true), 'dtTime' => $time);
            }
        }
        
        $jiaData = $db->get_var("select xieyi from demo_shezhi where comId = $comId");
        $jiaDataArr = explode('|', $jiaData);
        foreach ($jiaDataArr as $info){
            $infoArr = explode('-', $info);
            $temp = array();
            $temp['name'] = $infoArr[0];
            $temp['product'] = $infoArr[1];
            $rangeSecond = rand(86400 * 2, 0);
            $rangeSecond = time() - $rangeSecond;
            $temp['dtTime'] = self::date_huan($rangeSecond);
            $data[] = $temp;
        }
        shuffle($data);
        
        $return['data'][] = $data;
        
        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }
    
    function date_huan($timestamp) {
        $seconds = time() - $timestamp;
        if($seconds > 31536000) {
            return date('Y-n-j',$timestamp);
        } elseif($seconds > 2592000) {
            return ceil($seconds / 2592000).'月前';
        } elseif($seconds > 86400) {
            return ceil($seconds / 86400).'天前';
        } elseif($seconds > 3600) {
            return ceil($seconds / 3600).'小时前';
        } elseif($seconds > 60) {
            return ceil($seconds / 60).'分钟前';
        } else {
            return $seconds.'秒前';
        }
    }


    /*获取微信配置洗洗脑*/
    public function weixin()
    {
        $url = $request['url'];
        $jssdk = new \Zhishang\JSSDK("wx7a91a4f2eccb30db", "368a5e47cb481c6aebfe0376ef71a463", $url);
        $signPackage = $jssdk->GetSignPackage();
        $return = array("code" => 1, "message" => "", "data" => $signPackage);
        echo json_encode($return, JSON_UNESCAPED_UNICODE);
    }


    public function config()
    {
        global $db, $request, $comId;
        
        $info = $db->get_row("select * from demo_shezhi where comId=$comId");
        $info->reci = explode('@_@', $info->tuihuan_reason);
        unset($info->tuihuan_reason);
        $info->qx_reason = explode('@_@', $info->qx_reason);
        $info->wx_kefu = explode('|', $info->wx_kefu);
        $info->feed_type = explode('@_@', $info->feed_type);
        $zuobiao = explode('|', $info->com_coordinate);
        $info->longitude = $zuobiao[0];
        $info->latitude = $zuobiao[1];
        
        $info->qrcode_gongzhonghao = $info->zhishang_back;
        $rules = $db->get_results("select id,min,bili from zc_release order by min asc");
        foreach ($rules as $k => $val){
            $rules[$k]->bili = bcmul($val->bili, 100, 0)."%";
        }
        $info->fenhong_rule = $rules;
        $forBidKey = ['kdn_EBusinessID', 'kdn_key', 'kdn_port', 'kd100_key', 'kd100_customer'];
        foreach ($forBidKey as $key){
            unset($info->$key);
        }
        
        $prices = explode('|', $info->xieyi);
        $priceConfig = [];
        foreach ($prices as $price){
            $val = explode('@', $price);
            $temp = array(
                'title' => $val[0],
                'price_min' => $val[1],
                'price_max' => $val[2]
            );
            
            $priceConfig[] = $temp;
        }
        $info->price_search = $priceConfig;
        unset($info->xieyi);
        
        $info->chongzhi_rule = null;
        $huodong = $db->get_row("select type,guizes from chongzhi_gift where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and is_del = 0 limit 1");
        if($huodong){
            $rules = json_decode($huodong->guizes,true);
			$columns = array_column($rules,'man');
			array_multisort($columns,SORT_DESC,$rules);
			$info->chongzhi_rule = $rules;
        }
        $info->gongyis = $db->get_results("select id as brandId, title from demo_product_brand order by ordering desc ");
        
        
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = $info;

        return json_encode($return, JSON_UNESCAPED_UNICODE);
    }

    /*首页标签商品*/
    public function goodsList()
    {
        global $db, $request, $comId;
        $product = new \Zhishang\Product();
        $list = $product->goodsplist();
        echo $list;
    }
    public function conf(){
        global $db, $request, $comId;
        
    }
}