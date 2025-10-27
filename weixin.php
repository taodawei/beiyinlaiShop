<?php
	//声明一个常量定义一个token值, token
// 	define("TOKEN", "hudong789");
    error_reporting(0);
	//通过Wechat类， 创建一个对象
	$wechatObj = new Wechat();
	
	//如果没有通过GET收到echostr字符串， 说明不是再使用token验证
	if (!isset($_GET['echostr'])) {
		//调用wecat对象中的方法响应用户消息
   		$wechatObj->responseMsg();
   	    //$wechatObj->material();
	}else{
		//调用valid()方法，进行token验证
  		$wechatObj->valid();
	}


	//声明一个Wechat的类， 处理接收消息， 接收事件， 响应各种消息， 以及token验证
class Wechat {
	     
	//验证签名, 手册中原代码改写
	public function valid() {
        echo $_GET["echostr"];exit;
		//在开发者首次提交验证申请时，微信服务器将发送GET请求到填写的URL上，并且带上四个参数（signature、timestamp、nonce、echostr），开发者通过对签名（即signature）的效验，来判断此条消息的真实性。 
//       	 	$echoStr = $_GET["echostr"];    // 随机字符串 
//         	$signature = $_GET["signature"]; //微信加密签名，signature结合了开发者填写的token参数和请求中的timestamp参数、nonce参数。
//       		 $timestamp = $_GET["timestamp"]; //时间戳 
//       		 $nonce = $_GET["nonce"];  // 随机数 
		
// 		 //上面通过常量声明的token值
// 		 $token = TOKEN;

// 		 //将token、timestamp、nonce三个参数进行字典序排序
//         	$tmpArr = array($token, $timestamp, $nonce);
// 		 sort($tmpArr);


// 		 //将三个参数字符串拼接成一个字符串进行sha1加密
//         	$tmpStr = implode($tmpArr); //将数排序过的数组组合成一个字符
//         	$tmpStr = sha1($tmpStr);   //使用sha1加密

// 		//如果公众号上的签名和服务器上的签名是相等的则验正成功
//   		if( $tmpStr == $signature ){
//             		 return true;
//     		}else{
//             		 return false;
//      		}
		
   	 }

    //响应消息处理
    public function responseMsg()
    {  
	//接收微新传过来的xml消息数据    
	//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
	$postStr = file_get_contents('php://input');
	//如果接收到了就处理并回复
	if (!empty($postStr)){
	    //将接收到的XML字符串写入日志， 用R标记表示接收消息
	    $this->logger("R \n".$postStr);
	    //将接收的消息处理返回一个对象
	    $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
       
	    //从消息对象中获取消息的类型 text  image location voice vodeo link 
            $RX_TYPE = trim($postObj->MsgType);
      
            //消息类型分离, 通过RX_TYPE类型作为判断， 每个方法都需要将对象$postObj传入
            switch ($RX_TYPE)
            {
                // case "text":
                //     $result = $this->receiveText($postObj);     //接收文本消息
                //     break;
                // case "image":
                //     $result = $this->receiveImage($postObj);   //接收图片消息
                //     break;
                // case "location":
                //     $result = $this->receiveLocation($postObj);  //接收位置消息
                //     break;
                // case "voice":
                //     $result = $this->receiveVoice($postObj);   //接收语音消息
                //     break;
                // case "video":
                //     $result = $this->receiveVideo($postObj);  //接收视频消息
                //     break;
                // case "link":
                //     $result = $this->receiveLink($postObj);  //接收链接消息
                //     break;
                case "event":
                    // 事件推送
                    $result = $this->handleEvent($postObj);
                    break;
                default:
                    $result = "unknown msg type: ".$RX_TYPE;   //未知的消息类型
                    break;
	        }
	    //将响应的消息再次写入日志， 使用T标记响应的消息！
    
	    //输出消息给微新
	    echo "" ;exit();
	}else {
	    //如果没有消息则输出空，并退出
            echo "";
            exit();
        }
    }

     public function handleEvent($object)
    {
        $contentStr = "";
        switch ($object->Event) {
            case "subscribe":
                $contentStr = "欢迎关注茶语大师茶空间";
                $contentStr = '通茶语，会知己，欢迎关注【茶语大师茶】！
招商加盟、项目咨询，请<a href="https://www.wenjuan.com/s/UZBZJvcQSA">点击这里填写表单</a>，会有招商人员与您联系，进行详细介绍。

购好茶，买好器，预定空间，请<a href="https://d.xiumi.us/stage/v5/5keRh/273256838" data-miniprogram-appid="wxd1576a695b863e4c" data-miniprogram-path="/pages/indexCarShop/indexCarShop" data-miniprogram-nickname="茶语大师茶" data-miniprogram-type="text">点我前往小程序</a>

欢迎点击下方菜单栏↓↓↓，解锁更多功能~';

            $this->logger("responseText 53213123112312");
                $openid = (string)$object->FromUserName; //数据类型转换为字符串
                $refer_id = explode('_', $object->EventKey); //$object->EventKey返回的是qrsence_1232313这种类型
                //$this->logger("refer_id \n".$refer_id[1]);
                
                
                // if(!empty($refer_id[1])){
                //     $this->message($openid,$refer_id[1]);//发送客户消息 
                // }else{
                //     $this->logger("responseText :111111111111111111");
                //     $resultStr = $this->responseText($object, $contentStr); 
                //     $this->logger("resultStr111 \n".$resultStr);
                //     echo  $resultStr; die;
                // }
          
               
                //}
                $openid = (string)$object->FromUserName; //数据类型转换为字符串
    
                $scene_id = str_replace("qrscene_", "", $object->EventKey); 

                $this->createuserinfo($openid, $scene_id);//获取用户信息
                
                $contentStr = '请进入凰御商城';
                
                //  $contentStr = 'https://ycx.67.zhishangez.cn?inventoryId='.$object->EventKey;
                $resultStr = $this->responseText($object, $contentStr); 
                echo  $resultStr; die;
                
                break;
            case "SCAN":
                $contentStr = "您已关注过，谢谢！";
               
                $openid = (string)$object->FromUserName; //数据类型转换为字符串
    
                $scene_id = str_replace("qrscene_", "", $object->EventKey); 

                $this->createuserinfo($openid, $scene_id);//获取用户信息
                // $this->createuserinfo($openid, $refer_id[0]);//获取用户信息
                
                $contentStr = '请<a href="https://ycx.67.zhishangez.cn/h5/pages/goods_detail/goods_detail?id='.$inventoryId.'">点击这里查看商品详情</a>
// 欢迎点击下方菜单栏↓↓↓，解锁更多功能~';
                $contentStr = '请关注凰御商城';
                
                //  $contentStr = 'https://ycx.67.zhishangez.cn?inventoryId='.$object->EventKey;
                $resultStr = $this->responseText($object, $contentStr); 
                echo  $resultStr; die;
                break;
        }
        
        return true;
        //$resultStr = $this->responseText($object, $contentStr);
        //return $resultStr;
      
    }
    
   public function responseText($object, $content)
    {
                $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>";
                 $this->logger("responseText :2222222");
                $result = sprintf($textTpl, $object->FromUserName, $object->ToUserName, time(), $content);
                return $result;
   }
   /**
     * 获取用户详细信息
     * @param $openid
     * @param $refer_id
     */
    public function createuserinfo($openid, $refer_id)
    {
        if($refer_id){
                require_once 'config/dt-config.php';
                require_once 'inc/class.database.php';
                require_once 'inc/function.php';
                $user = $db->get_row("SELECT * from users WHERE openid = '$openid' ");
                if(!$user){
                    $access_token= '46_GogRVD1S4rHnQSiF_X2R5CXiXOQk_c2lWbxxaTmsOO1ZyXURrmhDCM2MsO2244L3lpj__HUGTm2tZIZEE1_9qwgCsMLzWyaiRbwYZ8_5b1IynEl2fPa8XvmkeBncpL2vG5nENDdpR8UKLj3XBDEhAHATVW';
                    $access_token= $this->getAccessToken();
                    // $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token=" .$access_token. "&openid=" . $openid;
                    $url = "https://api.weixin.qq.com/sns/userinfo?access_token=$access_token&openid=$openid&lang=zh_CN";
                    
 
                    $user = self::https_request($url);
                    $this->logger("responseText 5555555555555:".$url);
                    $this->logger("responseText 5555555555555:".json_encode($token_info,JSON_UNESCAPED_UNICODE));
        
                    // $user = json_decode($user, true);
                    $users = array(
                        'openid' => $openid,
                        'nickname' => $user['nickname'],
                        'avatar' => $user['headimgurl'],
                        'sex' => $user['sex'],
                        'unionid' => $user['unionid'],
                        'status' => 1,
                    );
                    $user_str = date('Y-m-d H:i:s') . "\t";
                    foreach ($users as $key => $value) {
                        $user_str .= $key . '=' . $value . "\t";
                    }
                    $user_str .= "\n";
                    
                    
                    $username = $openid;
                    $weixin_name = $user['nickname'] ? $user['nickname'] : '微信用户'.date("Hi").rand(1000,9999);
                    $tuijianren = 0;
                    $comId = 888;
        			$areaId = $tuan_id = $shangshangji = $shangji = $city = 0;
        			$password = rand(111111,999999);
        			$level_row = $db->get_row("select id,title from user_level where comId=$comId order by id asc limit 1");
        			$level = (int)$level_row->id;
        			if(!empty($tuijianren)){
        				$shangji = $tuijianren;
        				$shangshangji = (int)$db->get_var("select shangji from users where id=$tuijianren");
        				$tuan_id = (int)$db->get_var("select tuan_id from users where id=$tuijianren");
        			}
        			
        			$lastlogin = time();
				    $token = substr(md5($comId.$lastlogin),5,10);
        			
        			$db->query("insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,openId,mini_openId,applet_info,shangji,shangshangji,tuan_id,qrcode,token) value($comId,'$weixin_name','$username','$password',$areaId,0,$level,'".date("Y-m-d H:i:s")."',1,'$openid','','',$shangji,$shangshangji,$tuan_id,'$refer_id','$token')");
        			
        			$this->logger(" 插入数据 ： insert into users(comId,nickname,username,password,areaId,city,level,dtTime,status,openId,mini_openId,applet_info,shangji,shangshangji,tuan_id,qrcode) value($comId,'$weixin_name','$username','$password',$areaId,0,$level,'".date("Y-m-d H:i:s")."',1,'$openid','','',$shangji,$shangshangji,$tuan_id,'$refer_id') ");
        			$userId = $db->get_var("select last_insert_id();");

                }else{
                    $db->query("update users set qrcode = '$refer_id' where id = $user->id");
                }
                
                
        }

      // $this->logger("R \n".$postStr);
    }
    
    public function material(){
         $access_token= $this->getAccessToken();;
         $url ='https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$access_token;
         $data_string = '{
                "type":"image",
                "offset":0,
                "count":20
            }';
         $msg = self::curl_post($url,$data_string);
         var_dump($msg);die;
    }
    
    //发送客服消息
    public function message($openid,$refer_id){
        if($refer_id){
                $access_token= $this->getAccessToken();;
                $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
                require_once 'config/dt-config.php';
                require_once 'inc/class.database.php';
                require_once 'inc/function.php';
                $data = $db->get_row("SELECT s.title as s_title,m.title as m_title FROM mendian_space s INNER JOIN mendian m on  s.mendianId = m.id  WHERE s.id=$refer_id  limit 1");
                         //"title":"茶语大师【'.$refer_id.'号桌】",
                             $this->logger("d \n".json_encode($data));
                $data_string = '{
                        "touser":"'.$openid.'",
                        "msgtype":"miniprogrampage",
                        "miniprogrampage":
                        {
                            "title":"茶语大师【'.$data->m_title.' - '.$data->s_title.'】",
                            "appid":"wxd1576a695b863e4c",
                            "pagepath":"/pages/indexCarShop/indexCarShop?redirect=/pages2/adScan/adScan&linkType=link&id='.$refer_id.'",
                            "thumb_media_id":"fHCmBVrjkMEGTTMMMQrBJByPMvHuFsSCCE_wjF1dm0w"
                        }
                    }';
                    
                    
                // $data_arr['touser'] =   $openid;
                // $data_arr['msgtype'] =   "miniprogrampage";
                // $data_arr['miniprogrampage']['title'] =   "test小程序";
                // $data_arr['miniprogrampage']["appid"] =   "wxd1576a695b863e4c";
                // $data_arr['miniprogrampage']["pagepath"] =   "/pages/indexCarShop/indexCarShop";
                // $data_arr['miniprogrampage']["thumb_media_id"] =   "fHCmBVrjkMEGTTMMMQrBJLBHJO6f6lms7zsODLZkaOA";
                // $data_string = json_encode($data_arr);
                $msg = self::curl_post($url,$data_string);
        	    //$this->logger("U1 $openid \n".json_encode($data_string));
                $oo = json_decode($msg);
                if(!empty($oo['errmsg']) && $oo['errmsg'] =='ok'){
                    return true;
                }   
        }
  
        return false;
    }
    

//     //接收文本消息
//     private function receiveText($object)
//     {
// 	//从接收到的消息中获取用户输入的文本内容， 作为一个查询的关键字， 使用trim()函数去两边的空格
//         $keyword = trim($object->Content);
    
//             //自动回复模式
//              if (strstr($keyword, "文本")){
// 		     $content = "这是个文本消息";

// 	     }else if (strstr($keyword, "单图文")){

//                 $content = array();
// 		$content[] = array("Title"=>"小规模低性能低流量网站设计原则",  "Description"=>"单图文内容", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz/2j8mJHm8CogqL5ZSDErOzeiaGyWIibNrwrVibuKUibkqMjicCmjTjNMYic8vwv3zMPNfichUwLQp35apGhiciatcv0j6xwA/0", "Url" =>"http://mp.weixin.qq.com/s?__biz=MjM5NDAxMDEyMg==&mid=201222165&idx=1&sn=68b6c2a79e1e33c5228fff3cb1761587#rd");

//             }else if (strstr($keyword, "图文") || strstr($keyword, "多图文")){
//                 $content = array();
//                 $content[] = array("Title"=>"多图文1标题", "Description"=>"动手构建站点的时候，不要到处去问别人该用什么，什么熟悉用什么，如果用自己不擅长的技术手段来写网站，等你写完，黄花菜可能都凉了。", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz/2j8mJHm8CogqL5ZSDErOzeiaGyWIibNrwrVibuKUibkqMjicCmjTjNMYic8vwv3zMPNfichUwLQp35apGhiciatcv0j6xwA/0", "Url" =>"http://mp.weixin.qq.com/s?__biz=MjM5NDAxMDEyMg==&mid=201222165&idx=1&sn=68b6c2a79e1e33c5228fff3cb1761587#rd");
//                 $content[] = array("Title"=>"多图文2标题", "Description"=>"动手构建站点的时候，不要到处去问别人该用什么，什么熟悉用什么，如果用自己不擅长的技术手段来写网站，等你写完，黄花菜可能都凉了。", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz/2j8mJHm8CogqL5ZSDErOzeiaGyWIibNrwrVibuKUibkqMjicCmjTjNMYic8vwv3zMPNfichUwLQp35apGhiciatcv0j6xwA/0", "Url" =>"http://mp.weixin.qq.com/s?__biz=MjM5NDAxMDEyMg==&mid=201222165&idx=1&sn=68b6c2a79e1e33c5228fff3cb1761587#rd");
//                 $content[] = array("Title"=>"多图文3标题", "Description"=>"动手构建站点的时候，不要到处去问别人该用什么，什么熟悉用什么，如果用自己不擅长的技术手段来写网站，等你写完，黄花菜可能都凉了。", "PicUrl"=>"http://mmbiz.qpic.cn/mmbiz/2j8mJHm8CogqL5ZSDErOzeiaGyWIibNrwrVibuKUibkqMjicCmjTjNMYic8vwv3zMPNfichUwLQp35apGhiciatcv0j6xwA/0", "Url" =>"http://mp.weixin.qq.com/s?__biz=MjM5NDAxMDEyMg==&mid=201222165&idx=1&sn=68b6c2a79e1e33c5228fff3cb1761587#rd");
//             }else if (strstr($keyword, "音乐")){
//                 $content = array();
//                 $content = array("Title"=>"小歌曲你听听", "Description"=>"歌手：不是高洛峰", "MusicUrl"=>"http://wx.buqiu.com/app/hlw.mp3", "HQMusicUrl"=>"http://wx.buqiu.com/app/hlw.mp3");
//             }else{
//                 $content = date("Y-m-d H:i:s",time())."\n技术支持 高洛峰";
//             }
            
//             if(is_array($content)){
//                 if (isset($content[0]['PicUrl'])){
//                     $result = $this->transmitNews($object, $content);
//                 }else if (isset($content['MusicUrl'])){
//                     $result = $this->transmitMusic($object, $content);
//                 }
//             }else{
//                 $result = $this->transmitText($object, $content);
//             }
     

//         return $result;
//     }

//     //接收图片消息
//     private function receiveImage($object)
//     {
//         $content = array("MediaId"=>$object->MediaId);
//         $result = $this->transmitImage($object, $content);
//         return $result;
//     }

//     //接收位置消息
//     private function receiveLocation($object)
//     {
//         $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
//         $result = $this->transmitText($object, $content);
//         return $result;
//     }

//     //接收语音消息
//     private function receiveVoice($object)
//     {
//         if (isset($object->Recognition) && !empty($object->Recognition)){
//             $content = "你刚才说的是：".$object->Recognition;
//             $result = $this->transmitText($object, $content);
//         }else{
//             $content = array("MediaId"=>$object->MediaId);
//             $result = $this->transmitVoice($object, $content);
//         }

//         return $result;
//     }

//     //接收视频消息
//     private function receiveVideo($object)
//     {
//         $content = array("MediaId"=>$object->MediaId, "Title"=>"this is a test", "Description"=>"pai pai");
//         $result = $this->transmitVideo($object, $content);
//         return $result;
//     }

//     //接收链接消息
//     private function receiveLink($object)
//     {
//         $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
//         $result = $this->transmitText($object, $content);
//         return $result;
//     }

//     //回复文本消息
//     private function transmitText($object, $content)
//     {
//         $xmlTpl = "<xml>
// <ToUserName><![CDATA[%s]]></ToUserName>
// <FromUserName><![CDATA[%s]]></FromUserName>
// <CreateTime>%s</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[%s]]></Content>
// </xml>";
//         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
//         return $result;
//     }

//     //回复图片消息
//     private function transmitImage($object, $imageArray)
//     {
//         $itemTpl = "<Image>
//     <MediaId><![CDATA[%s]]></MediaId>
// </Image>";

//         $item_str = sprintf($itemTpl, $imageArray['MediaId']);

//         $xmlTpl = "<xml>
// <ToUserName><![CDATA[%s]]></ToUserName>
// <FromUserName><![CDATA[%s]]></FromUserName>
// <CreateTime>%s</CreateTime>
// <MsgType><![CDATA[image]]></MsgType>
// $item_str
// </xml>";

//         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
//         return $result;
//     }

//     //回复语音消息
//     private function transmitVoice($object, $voiceArray)
//     {
//         $itemTpl = "<Voice>
//     <MediaId><![CDATA[%s]]></MediaId>
// </Voice>";

//         $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

//         $xmlTpl = "<xml>
// <ToUserName><![CDATA[%s]]></ToUserName>
// <FromUserName><![CDATA[%s]]></FromUserName>
// <CreateTime>%s</CreateTime>
// <MsgType><![CDATA[voice]]></MsgType>
// $item_str
// </xml>";

//         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
//         return $result;
//     }

//     //回复视频消息
//     private function transmitVideo($object, $videoArray)
//     {
//         $itemTpl = "<Video>
//     <MediaId><![CDATA[%s]]></MediaId>
//     <Title><![CDATA[%s]]></Title>
//     <Description><![CDATA[%s]]></Description>
// </Video>";

//         $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['Title'], $videoArray['Description']);

//         $xmlTpl = "<xml>
// <ToUserName><![CDATA[%s]]></ToUserName>
// <FromUserName><![CDATA[%s]]></FromUserName>
// <CreateTime>%s</CreateTime>
// <MsgType><![CDATA[video]]></MsgType>
// $item_str
// </xml>";

//         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
//         return $result;
//     }

//     //回复图文消息
//     private function transmitNews($object, $newsArray)
//     {
//         if(!is_array($newsArray)){
//             return;
//         }
//         $itemTpl = "    <item>
//         <Title><![CDATA[%s]]></Title>
//         <Description><![CDATA[%s]]></Description>
//         <PicUrl><![CDATA[%s]]></PicUrl>
//         <Url><![CDATA[%s]]></Url>
//     </item>
// ";
//         $item_str = "";
//         foreach ($newsArray as $item){
//             $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
//         }
//         $xmlTpl = "<xml>
// <ToUserName><![CDATA[%s]]></ToUserName>
// <FromUserName><![CDATA[%s]]></FromUserName>
// <CreateTime>%s</CreateTime>
// <MsgType><![CDATA[news]]></MsgType>
// <ArticleCount>%s</ArticleCount>
// <Articles>
// $item_str</Articles>
// </xml>";

//         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($newsArray));
//         return $result;
//     }

//     //回复音乐消息
//     private function transmitMusic($object, $musicArray)
//     {
//         $itemTpl = "<Music>
//     <Title><![CDATA[%s]]></Title>
//     <Description><![CDATA[%s]]></Description>
//     <MusicUrl><![CDATA[%s]]></MusicUrl>
//     <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
// </Music>";

//         $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

//         $xmlTpl = "<xml>
// <ToUserName><![CDATA[%s]]></ToUserName>
// <FromUserName><![CDATA[%s]]></FromUserName>
// <CreateTime>%s</CreateTime>
// <MsgType><![CDATA[music]]></MsgType>
// $item_str
// </xml>";

//         $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
//         return $result;
//     }

    public function getAccessToken(){
		$token_file = $this->cache_get('wx_token',1222);

		if(true){
			$appid = 'wx84bd3968cbfc4777';
			$appsecret = '103d781af6d69bc1abd0aa37f4ea64fd';
			$token_url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appid&secret=$appsecret";
		  	$token_info = self::https_request($token_url);
		  	//file_put_contents('request.txt',json_encode($token_info,JSON_UNESCAPED_UNICODE));
		  	$this->cache_push('wx_token',1222,$token_info,4);
		  	return $token_info['access_token'];
		}else{
		  	return $token_file->access_token;
		}
	}

    //日志记录
    private function logger($log_content)
    {
      
	    $max_size = 100000;   //声明日志的最大尺寸

	    $log_filename = "wx_log.txt";  //日志名称

	    //如果文件存在并且大于了规定的最大尺寸就删除了
	    if(file_exists($log_filename) && (abs(filesize($log_filename)) > $max_size)){
		    unlink($log_filename);
	    }

	    //写入日志，内容前加上时间， 后面加上换行， 以追加的方式写入
	    file_put_contents($log_filename, date('H:i:s')." ".$log_content."\n", FILE_APPEND);
        
    }
    
        	
	public static function https_request($url){
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
	
		public static function curl_post($url,  $data_string)
	{  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json;',
        'Content-Length: ' . strlen($data_string))
        );
        ob_start();
        curl_exec($ch); 
        if (curl_errno($ch)) {
        //self::ErrorLogger('curl falied.  Error Info: '.curl_error($ch));
         //$this->logger("U2 \n".curl_error($ch));
        }
        $return_content = ob_get_contents();
        ob_end_clean();
        $return_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        return array($return_code, $return_content);
    }   
    
        //获取缓存 dir:目录名称 name:缓存名称
    public function cache_get($dir,$name){
    	$file_dir = '/cache/'.($dir==''?'':$dir.'/').$name.'.dat';
    	$str = file_get_contents($file_dir);
    	if(!empty($str)){
    		$now = time();
    		$datajson = json_decode($str);
    		if($datajson->expire > $now || $datajson->expire==0){
    		
    			return $datajson->data;
    		}else{
    			return '';
    		}
    	}else{
    		return '';
    	}
    }
    
    public function cache_push($dir,$name,$content,$expire=0){
    	if($dir!='' && !is_dir('/cache/'.$dir)){
    		@mkdir('/cache/'.$dir);
    	}
    	if($expire>0){
    		$expire = time()+$expire*60;
    	}
    	$data = array();
    	$data['data'] = $content;
    	$data['expire']=$expire;
    	$file_dir = '/cache/'.($dir==''?'':$dir.'/').$name.'.dat';
    	file_put_contents($file_dir,json_encode($data,JSON_UNESCAPED_UNICODE),LOCK_EX);
    }
}
?>
