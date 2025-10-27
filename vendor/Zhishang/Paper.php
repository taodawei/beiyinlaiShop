<?php
namespace Zhishang;

class Paper
{
    
    private $appId;
	private $appSecret;
	private $brandTitle;
	private $url;


	public function __construct() {
	    global $db,$request;
		$this->appId = '992400';
		$this->appSecret = 'ixh8qz5e7utei5gu2fdo';
		$this->brandTitle = 'Bioswamp';
		
		$this->url = 'https://marketing.zhiliaowo.cn/openapi/list/brand/paper';
	}
	
	public function sku()
	{
        global $request,$db,$comId;
        
        $url = "https://marketing.zhiliaowo.cn/openapi/list/product/paper?appId=992400&brand=Bioswamp";
        
        $searchData = array();
        $searchData[] = array(
            'key' => 'timestamp',
            'val' => self::get_msectime() 
        );
        
        $searchData[] = array(
            'key' => 'version',
            'val' => 1.1
        );
        
        $page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
        if($page > 0){
            $searchData[] = array(
                'key' => 'pageNo',
                'val' => $page
            );
        }
        
        if($pageNum > 0){
            $searchData[] = array(
                'key' => 'pageSize',
                'val' => $pageNum
            );
        }
        
        $start = $request['start'];
        $end = $request['end'];
        if(!empty($start)){
            $searchData[] = array(
                'key' => 'start',
                'val' => $start
            );
        }
        
        if(!empty($end)){
            $searchData[] = array(
                'key' => 'end',
                'val' => $end
            );
        }
        
        $min = (int)$request['min'];
        $max = (int)$request['max'];
        if(!empty($min)){
            $searchData[] = array(
                'key' => 'min',
                'val' => $min
            );
        }
        
        if(!empty($max)){
            $searchData[] = array(
                'key' => 'max',
                'val' => $max
            );
        }
        
        $sku = $request['sku'];
        if(!$sku){
            return '{"code":0,"message":"必要参数不能为空！"}';
        }
        
        $searchData[] = array(
            'key' => 'sku',
            'val' => $sku
        );
        
        foreach ($searchData as $search){
            $url .= "&".$search['key']."=".$search['val'];
        }
        
        $data = self::https_request($url);
        if($data['code'] == 500){
            $data['result']['totalCount'] = 0;
            $data['result']['data'] = [];
        }
        $dataJson = array(
            "code" => 1,
            "message" => '获取成功',
            "count" => $data['result']['totalCount'],
            "data"=> $data['result']['data']
        );

        return json_encode($dataJson, JSON_UNESCAPED_UNICODE);
	}
    
    public function brand()
    {
        global $request,$db,$comId;
        
        $url = $this->url."?appId=$this->appId&brand=$this->brandTitle";
        
        $searchData = array();
        $searchData[] = array(
            'key' => 'timestamp',
            'val' => self::get_msectime() 
        );
        
        $searchData[] = array(
            'key' => 'version',
            'val' => 1.1
        );
        
        $page = (int)$request['page'];
		$pageNum = (int)$request['pagenum'];
        if($page > 0){
            $searchData[] = array(
                'key' => 'pageNo',
                'val' => $page
            );
        }
        
        if($pageNum > 0){
            $searchData[] = array(
                'key' => 'pageSize',
                'val' => $pageNum
            );
        }
        
        $start = $request['start'];
        $end = $request['end'];
        if(!empty($start)){
            $searchData[] = array(
                'key' => 'start',
                'val' => $start
            );
        }
        
        if(!empty($end)){
            $searchData[] = array(
                'key' => 'end',
                'val' => $end
            );
        }
        
        $min = (int)$request['min'];
        $max = (int)$request['max'];
        if(!empty($min)){
            $searchData[] = array(
                'key' => 'min',
                'val' => $min
            );
        }
        
        if(!empty($max)){
            $searchData[] = array(
                'key' => 'max',
                'val' => $max
            );
        }
        
        foreach ($searchData as $search){
            $url .= "&".$search['key']."=".$search['val'];
        }
        

        $data = self::https_request($url);
        $dataJson = array(
            "code" => 1,
            "message" => '获取成功',
            "count" => $data['result']['totalCount'],
            "data"=> $data['result']['data']
        );

        return json_encode($dataJson, JSON_UNESCAPED_UNICODE);
    }
    
    function get_msectime(){

        list($msec, $sec) = explode(' ', microtime());
        
        $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);
        
        return $msectime;
    }
    
    private function httpGet($url) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_URL, $url);
		$res = curl_exec($curl);
		curl_close($curl);
		return $res;
	}
	
	public function https_request($url){

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
    
}