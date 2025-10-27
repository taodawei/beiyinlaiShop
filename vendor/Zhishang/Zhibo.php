<?php
namespace Zhishang;
/**
 * 直播
 */
class Zhibo
{
	/**
     * 小程序直播列表
     */
    function live(){
        global $db, $request, $comId;
        $start = (int)$request['start'];
        $limit = (int)$request['limit'];
        $token = self::getAccessToken();
        $url = 'https://api.weixin.qq.com/wxa/business/getliveinfo?access_token='.$token;
        $data['start'] = !empty($start) ?$start:0;
        $data['limit'] = !empty($limit) ?$limit:100;
        $data_json =json_encode($data);
        $res = json_decode(self::curl_post($url,$data_json));
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = $res;
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    /**
     * 小程序回放
     */
    function liveReplay(){
        global $db, $request, $comId;
        $start = (int)$request['start'];
        $limit = (int)$request['limit'];
        $room_id = (int)$request['room_id'];
        $token = self::getAccessToken();
        $url = 'https://api.weixin.qq.com/wxa/business/getliveinfo?access_token='.$token;
        $data['action'] = 'get_replay';
        $data['room_id'] = $room_id;
        $data['start'] = !empty($start) ?$start:0;
        $data['limit'] = !empty($limit) ?$limit:100;
        $data_json =json_encode($data);
        $res = json_decode(self::curl_post($url,$data_json));
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = $res;
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 小程序获取access_key
     * @return mixed|string
     */
    private static function getAccessToken() {
        global $db, $comId;
        // access_token 应该全局存储与更新，以下代码以写入到文件中做示例
        $data = json_decode(file_get_contents("access_token_".$comId.".json"));
        if ($data->expire_time < time()) {
            $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=3 limit 1");
            $weixin_arr = json_decode($weixin_set->info);
            $appid = $weixin_arr->appid;
            $secret = $weixin_arr->appsecret;
            //$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$secret}";
            $res = json_decode(self::curl_post($url,''));
            $access_token = $res->access_token;
            if ($access_token) {
                $data->expire_time = time() + 7000;
                $data->access_token = $access_token;
                $fp = fopen("access_token.json", "w");
                fwrite($fp, json_encode($data));
                fclose($fp);
            }
        } else {
            $access_token = $data->access_token;
        }
        return $access_token;
    }

    public static function curl_post($url , $data= array()){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        // POST数据
        curl_setopt($ch, CURLOPT_POST, 1);
        // 把post的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;

    }
}