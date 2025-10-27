<?php
namespace Zhishang;
class Express{
    
    public function jisuInfo()
    {
         $host = "https://jisukdcx.market.alicloudapi.com";
        $path = "/express/query";
        $method = "GET";
        $appcode = "fa1feac3ac404daabead29ef15071142";
        $headers = array();
        array_push($headers, "Authorization:APPCODE " . $appcode);
        //根据API的要求，定义相对应的Content-Type
        array_push($headers, "Content-Type".":"."application/json; charset=UTF-8");
        $querys = "mobile=mobile&number=9882830282425&type=auto";
        $bodys = "null";
        $url = $host . $path . "?" . $querys;
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FAILONERROR, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        if (1 == strpos("$".$host, "https://"))
        {
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        }
        curl_setopt($curl, CURLOPT_POSTFIELDS, $bodys);
        $data = curl_exec($curl); var_dump($data);die;
    }
    
    //快递100 
    public function kd100Info(){
        global $db,$request,$comId;
        
    	$fenbiao = getFenbiao($comId,20);
        $fahuoId = $request['fahuo_id'];
        $type = (int)$request['type'];
        
        $kuaiDiCompany = $kuaiDiOrder = '';
        switch ($type) {
            case 0:
                $fahuo = $db->get_row("select kuaidi_title,kuaidi_order,id,kuaidi_company from order_fahuo$fenbiao where id=$fahuoId");
           	    if(!$fahuo){
           	        return '{"code":0,"message":"发货信息不存在！"}';
           	    }
           	    
           	    if(!$fahuo->kuaidi_company || !$fahuo->kuaidi_order){
           	        return '{"code":0,"message":"物流信息不完善！"}';
           	    }
           	    $kuaiDiCompany = $fahuo->kuaidi_company;
           	    $kuaiDiOrder = $fahuo->kuaidi_order;
                break;
            
            case 1:
               
        }
   	    
        $expressCode = $fahuo->kuaidi_company;
        $expressOrder = $fahuo->kuaidi_order;
        
        $key = 'wGhOhcwv7309';                        // 客户授权key
        $customer = '4F86EF145B5D4026E571AED4EFB68A36';                   // 查询公司编号
        $param = array (
            'com' => $kuaiDiCompany,             // 快递公司编码
            'num' => $kuaiDiOrder,     // 快递单号
            'phone' => '',                // 手机号
            'from' => '',                 // 出发地城市
            'to' => '',                   // 目的地城市
            'resultv2' => '1',            // 开启行政区域解析
            'show' => '0',                // 返回格式：0：json格式（默认），1：xml，2：html，3：text
            'order' => 'desc'             // 返回结果排序:desc降序（默认）,asc 升序
        );
        
        //请求参数
        $post_data = array();
        $post_data['customer'] = $customer;
        $post_data['param'] = json_encode($param, JSON_UNESCAPED_UNICODE);
        $sign = md5($post_data['param'].$key.$post_data['customer']);
        $post_data['sign'] = strtoupper($sign);
        
        $url = 'https://poll.kuaidi100.com/poll/query.do';    // 实时查询请求地址
        
    // echo '请求参数：<br/><pre>';
    // echo print_r($post_data);
    // echo '</pre>';
        
        // 发送post请求
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        // 第二个参数为true，表示格式化输出json
        $data = json_decode($result, true);
        if($data['message'] == 'ok'){
            $return = array();
    		$return['code'] = 1;
    		$return['message'] = '返回成功！';
    		$return['data'] = $data['data'];
    		
    	    return json_encode($return,JSON_UNESCAPED_UNICODE);
        }
        
        return '{"code":0,"message":"获取物流信息失败，原因：'.$data['message'].'！"}';
    }

    
    //快递鸟
    public function getInfo(){
        global $db,$request,$comId;
        
    	$fenbiao = getFenbiao($comId,20);
        $fahuoId = $request['fahuo_id'];
        $type = (int)$request['type'];
        $phone = '';
        switch ($type) {
            case 0:
                $fahuo = $db->get_row("select kuaidi_title,kuaidi_order,id,kuaidi_company,shuohuo_json from order_fahuo$fenbiao where id=$fahuoId");
           	    if(!$fahuo){
           	        return '{"code":0,"message":"发货信息不存在！"}';
           	    }
           	    
           	    if(!$fahuo->kuaidi_company || !$fahuo->kuaidi_order){
           	        return '{"code":0,"message":"物流信息不完善！"}';
           	    }
           	    
           	    $shouHuo = json_decode($fahuo->shuohuo_json, true);
           	    $phone = $shouHuo['手机号'];
           	    
                break;
            
            case 1:
                $fahuo = $db->get_row("select kuaidi_title,kuaidi_order,id,kuaidi_company,shouhuo_json from kmd_change_log where id=$fahuoId");
           	    if(!$fahuo){
           	        return '{"code":0,"message":"发货信息不存在！"}';
           	    }
           	    
           	    if(!$fahuo->kuaidi_company || !$fahuo->kuaidi_order){
           	        return '{"code":0,"message":"物流信息不完善！"}';
           	    }
           	    
           	    $shouHuo = json_decode($fahuo->shouhuo_json, true);
           	    $phone = $shouHuo['mobile'];
           	    
                break;
        }
   	    
        $expressCode = $fahuo->kuaidi_company;
        $expressOrder = $fahuo->kuaidi_order;
        
        $shezhi = $db->get_row("select * from demo_shezhi where comId = $comId ");
        switch($shezhi->express_type){//快递类型：0-快递鸟  1-快递100  3-暂不接入
            case 0:
                $requestData= "{'OrderCode':'','ShipperCode':'".$expressCode."','LogisticCode':'".$expressOrder."'}";
                
                if($expressCode == 'SF'){
                    // var_dump($shouHuo);
                    $customerName = substr($phone,-4);
                    $requestData= "{'OrderCode':'','ShipperCode':'".$expressCode."','LogisticCode':'".$expressOrder."','CustomerName':'$customerName'}";
                    // var_dump($requestData);die;
                }
                
                $datas = array(
                    // 'EBusinessID' => '1696225',//测试
                    'EBusinessID' => $shezhi->kdn_EBusinessID,//正式
                    'RequestType' => $shezhi->kdn_port,
                    'RequestData' => urlencode($requestData) ,
                    'DataType' => '2',
                );
                // $datas['DataSign'] = self::encrypt($requestData,'3aac495a-ab91-429c-ab8a-3acbb59be1bb');//测试
                $datas['DataSign'] = self::encrypt($requestData, $shezhi->kdn_key);//正式
                // var_dump($datas);die;
                // var_dump($datas);die;
                $result= self::sendPost('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $datas);
             
                $return = array();
        		$return['code'] = 1;
        		$return['message'] = '返回成功！';
        		$return['data'] = json_decode($result, true);
        		
        	    return json_encode($return,JSON_UNESCAPED_UNICODE);
            case 1:
                $key = $shezhi->kd100_key;                        // 客户授权key
                $customer = $shezhi->kd100_customer;                   // 查询公司编号
                $param = array (
                    'com' => $kuaiDiCompany,             // 快递公司编码
                    'num' => $kuaiDiOrder,     // 快递单号
                    'phone' => '',                // 手机号
                    'from' => '',                 // 出发地城市
                    'to' => '',                   // 目的地城市
                    'resultv2' => '1',            // 开启行政区域解析
                    'show' => '0',                // 返回格式：0：json格式（默认），1：xml，2：html，3：text
                    'order' => 'desc'             // 返回结果排序:desc降序（默认）,asc 升序
                );
                
                //请求参数
                $post_data = array();
                $post_data['customer'] = $customer;
                $post_data['param'] = json_encode($param, JSON_UNESCAPED_UNICODE);
                $sign = md5($post_data['param'].$key.$post_data['customer']);
                $post_data['sign'] = strtoupper($sign);
                
                $url = 'https://poll.kuaidi100.com/poll/query.do';    // 实时查询请求地址
                
                // 发送post请求
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $result = curl_exec($ch);
                // 第二个参数为true，表示格式化输出json
                $data = json_decode($result, true);
                if($data['message'] == 'ok'){
                    $return = array();
            		$return['code'] = 1;
            		$return['message'] = '返回成功！';
            		$return['data'] = $data['data'];
            		
            	    return json_encode($return,JSON_UNESCAPED_UNICODE);
                }
                
                return '{"code":0,"message":"获取物流信息失败，原因：'.$data['message'].'！"}';
            default:
                return '{"code":0,"message":"获取物流信息失败，原因：未接入第三方查询！"}';
        }
        
    }
    
    function sendPost($url, $datas) {
        $temps = array();   
        foreach ($datas as $key => $value) {
            $temps[] = sprintf('%s=%s', $key, $value);      
        }   
        $post_data = implode('&', $temps);
        $url_info = parse_url($url);
        if(empty($url_info['port']))
        {
            $url_info['port']=80;   
        }
        $httpheader = "POST " . $url_info['path'] . " HTTP/1.0\r\n";
        $httpheader.= "Host:" . $url_info['host'] . "\r\n";
        $httpheader.= "Content-Type:application/x-www-form-urlencoded\r\n";
        $httpheader.= "Content-Length:" . strlen($post_data) . "\r\n";
        $httpheader.= "Connection:close\r\n\r\n";
        $httpheader.= $post_data;
        $fd = fsockopen($url_info['host'], $url_info['port']);
        fwrite($fd, $httpheader);
        $gets = "";
        $headerFlag = true;
        while (!feof($fd)) {
            if (($header = @fgets($fd)) && ($header == "\r\n" || $header == "\n")) {
                break;
            }
        }
        while (!feof($fd)) {
            $gets.= fread($fd, 128);
        }
        fclose($fd);  
        
        return $gets;
    }
    function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
    
}