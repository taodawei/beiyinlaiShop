<?
global $db,$request;
$id = (int)$request['id'];
$comId = (int)$request['comId'];
$fenbiao = getFenbiao($comId,20);
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$type = $request['type'];
if($type=='pdt'){
    $fahuo = $db->get_row("select kuaidi_title,kuaidi_company,kuaidi_order from pdt_order_fahuo where id=$id");
}else{
    $order = $db->get_row("select * from order$fenbiao where id=$id");
    if(empty($order) || $order->userId!=$userId){
        die("<script>alert('订单不存在');history.go(-1);</script>");
    }
    $fahuo = $db->get_row("select kuaidi_title,kuaidi_company,kuaidi_order from order_fahuo$fenbiao where id=$order->fahuoId"); 
}
if(!empty($fahuo->kuaidi_order)&&!empty($fahuo->kuaidi_company)){
    $wlInfo=json_decode(getOrderTracesByJson($fahuo->kuaidi_company,$fahuo->kuaidi_order));
}
?>
<div class="wode" style="background-color:#f2f5f7;">
    <div class="wode_1">
        物流详情
        <div class="wode_1_left" onclick="go_prev_page();">
            <img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
    <div class="dingdanxiangqing_4">
        <div class="dingdanxiangqing_4_up">
            <ul>
                <li>
                    <div class="dingdanxiangqing_4_up_left">
                        快递公司：
                    </div>
                    <div class="dingdanxiangqing_4_up_right">
                        <?=$fahuo->kuaidi_title?>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="dingdanxiangqing_4_up_left">
                        物流单号：
                    </div>
                    <div class="dingdanxiangqing_4_up_right">
                        <?=$fahuo->kuaidi_order?>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
    </div>
    <div class="dingdanxiangqing_4">
        <div class="dingdanxiangqing_4_up">
            <ul>
                <? if(!empty($wlInfo) && !empty($wlInfo->Traces)){
                    foreach ($wlInfo->Traces as $key => $value) {
                    ?>
                    <li>
                        <div class="dingdanxiangqing_4_up_left">
                            <?php echo date("m-d H:i", strtotime($value->AcceptTime));?>
                        </div>
                        <div class="dingdanxiangqing_4_up_right">
                            <?=$value->AcceptStation?>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <?
                    }
                }else{
                    ?>
                    <li>
                        暂无物流信息
                        <div class="clearBoth"></div>
                    </li>
                <? }?>
            </ul>
        </div>
    </div>
</div>
<?
function getOrderTracesByJson($kuai_company,$kuaidi_order){
    $requestData= "{'OrderCode':'','ShipperCode':'".$kuai_company."','LogisticCode':'$kuaidi_order'}";
    $datas = array(
        'EBusinessID' => '1423675',
        'RequestType' => '8001',
        'RequestData' => urlencode($requestData) ,
        'DataType' => '2',
    );
    $datas['DataSign'] = encrypt($requestData,'8582b309-f88c-47fb-98c7-edf0876aa9ec');
    $result=sendPost('http://api.kdniao.com/Ebusiness/EbusinessOrderHandle.aspx', $datas);   
    return $result;
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
?>