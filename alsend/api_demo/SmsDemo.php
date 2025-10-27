<?php
session_start();
require_once dirname(__DIR__) . '/api_sdk/vendor/autoload.php';

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\SendBatchSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

/**
 * Class SmsDemo
 *
 * 这是短信服务API产品的DEMO程序，直接执行此文件即可体验短信服务产品API功能
 * (只需要将AK替换成开通了云通信-短信服务产品功能的AK即可)
 * 备注:Demo工程编码采用UTF-8
 */
class SmsDemo
{

    static $acsClient = null;

    /**
     * 取得AcsClient
     *
     * @return DefaultAcsClient
     */
    public static function getAcsClient() {
        //产品名称:云通信短信服务API产品,开发者无需替换
        $product = "Dysmsapi";

        //产品域名,开发者无需替换
        $domain = "dysmsapi.aliyuncs.com";

        // TODO 此处需要替换成开发者自己的AK (https://ak-console.aliyun.com/)
        $accessKeyId = "LTAI5tGoUQzcPa17Cn3BAbg5"; // AccessKeyId

        $accessKeySecret = "mXE4WHD4Af3hpFOeAL6LA8EoLM3Ikw"; // AccessKeySecret

        // 暂时不支持多Region
        $region = "cn-hangzhou";

        // 服务结点
        $endPointName = "cn-hangzhou";


        if(static::$acsClient == null) {

            //初始化acsClient,暂不支持region化
            $profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);

            // 增加服务结点
            DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);

            // 初始化AcsClient用于发起请求
            static::$acsClient = new DefaultAcsClient($profile);
        }
        return static::$acsClient;
    }

    /**
     * 发送短信
     * @return stdClass
     */
    public static function sendSms($duanxinqianming, $phone, $mobanbianhao, $mobancanshu) {

        // 初始化SendSmsRequest实例用于设置发送短信的参数
        $request = new SendSmsRequest();

        //可选-启用https协议
        //$request->setProtocol("https");

        // 必填，设置短信接收号码
        $request->setPhoneNumbers($phone);

        // 必填，设置签名名称，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $request->setSignName($duanxinqianming);

        // 必填，设置模板CODE，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $request->setTemplateCode($mobanbianhao);

        // 可选，设置模板参数, 假如模板中存在变量需要替换则为必填项
        $request->setTemplateParam($mobancanshu);

        // 可选，设置流水号
//        $request->setOutId("yourOutId");

        // 选填，上行短信扩展码（扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段）
//        $request->setSmsUpExtendCode("1234567");

        // 发起访问请求
        $acsResponse = static::getAcsClient()->getAcsResponse($request);

        return $acsResponse;
    }

}
$verify = $_REQUEST['verify'];
$phone = $_REQUEST['phone'];
$yzm = $_REQUEST['yzm'];
$type = $_REQUEST['type'];
$v = md5(substr($phone.$yzm,5,5));

if($verify == $v && $type == 1){
    set_time_limit(0);
    header('Content-Type: text/plain; charset=utf-8');
    $duanxinqianming = '贝茵莱';
    $mobanbianhao = "SMS_269575421";//港澳台
    if($phone[0] == 1 && strlen($phone) ==11){
        $mobanbianhao = "SMS_269575421";//国内
    }

    $product = $_REQUEST['product'];
    $mobancanshu = json_encode(array("code"=>$yzm), JSON_UNESCAPED_UNICODE);
    $response = SmsDemo::sendSms($duanxinqianming, $phone, $mobanbianhao, $mobancanshu);
    
    file_put_contents('request.txt',PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.$phone."已发送短信，返回信息：".json_encode($response,JSON_UNESCAPED_UNICODE).PHP_EOL ,FILE_APPEND);
    
    return $response;
}else{
    set_time_limit(0);
    header('Content-Type: text/plain; charset=utf-8');
    $duanxinqianming = '贝茵莱';
    $mobanbianhao = "";//港澳台
    if($phone[0] == 1 && strlen($phone) ==11){
        $mobanbianhao = "SMS_269575421";//国内
        $msgData = array(
            'name' => $_REQUEST['name'],
            'startTime' => $_REQUEST['startTime'],
            'endTime' => $_REQUEST['endTime'],
            'roomtype' => $_REQUEST['roomtype'],
            'customerNum' => $_REQUEST['customerNum'],
            'crsOrderId' => $_REQUEST['crsOrderId']
        );
    }else{
        $mobanbianhao = "SMS_228846723";//国际港澳台
        $msgData = array(
            'name' => $_REQUEST['name'],
            'crsOrderId' => $_REQUEST['crsOrderId'],
            'address' => 'No.2-14, Sector 6, No.1, Sector 5, No. 288, Tanwang Road, Mentougou District, Beijing',
            'phone' => '86-010-60868888',
            'eml' => 'xitan@xitanhotel.com',
            'startTime' => $_REQUEST['startTime'],
            'endTime' => $_REQUEST['endTime'],
            'roomType' => $_REQUEST['roomType'],
            'customerNum' => $_REQUEST['customerNum'],
            'num' => $_REQUEST['num'],
            'taxes' => $_REQUEST['taxes'],
            'fee' => $_REQUEST['fee'],
            'total' => $_REQUEST['total']
        );
    }

   
    $mobancanshu = json_encode($msgData , JSON_UNESCAPED_UNICODE);
    $response = SmsDemo::sendSms($duanxinqianming, $phone, $mobanbianhao, $mobancanshu);
    var_dump($response);die;
    file_put_contents('request.txt',PHP_EOL.date('Y-m-d H:i:s').PHP_EOL.$phone."已发送短信，返回信息：".json_encode($response,JSON_UNESCAPED_UNICODE).PHP_EOL ,FILE_APPEND);
    
    return $response;
}