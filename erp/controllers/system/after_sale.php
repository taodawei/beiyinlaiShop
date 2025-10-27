<?php


function index(){}

/**
 * 退货列表
 */
function getSalesReturnList(){
    global $db,$request,$comId;
    $type = $request['type'];
    $stauts = $request['status'];
    $fenbiao = getFenBiao($comId,20);
    $return = ['code'=>0,'msg'=>'请求成功'];
    $sql = "select * from tuihuan as t where comId=$comId ";
    if ($type){
        $sql .= " and t.type = $type";
    }
    if ($stauts){
        $sql .= " and t.status=$stauts";
    }
    $sql .= " order by dtTime desc";
    $tuihuan_data = $db->get_results($sql);

    if ($tuihuan_data){

        foreach ($tuihuan_data as $k=>$v){
            #获取用户名称
            $v->nickname = $db->get_var("select
                                            nickname
                                        from
                                            users
                                        where
                                            id = $v->userId
                                        and comId = $comId");
            #获取订单号
            $v->orderno = $db->get_var("select
                                            orderId
                                        from
                                            order$fenbiao
                                        where
                                            comId = $comId
                                        and userId = $v->userId");
            $pdtInfo = $db->get_var("select pdtInfo from order_detail$fenbiao where id=$v->detailId");
            $v->title = json_decode($pdtInfo,true)['title'].','.
                json_decode($pdtInfo,true)['key_vals'];
            $v->view = '<a href="javascript:" onclick="order_show('.$k.')"><img src="images/shangchengdd_14.png" class="dq_dingdan_button"></a>';

        }
    }
    $return['data'] = $tuihuan_data;
    echo json_encode($return);die;
}

/**
 * 返回退换货的详细信息
 */
function order_info_index(){
    global $db,$request,$comId;
    if (is_file("../cache/product_set_$comId.php")){
        $product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
    }else{
        $product_set = $db->get_row("select * from demo_product_set where comId=$comId");
    }
    $fenbiao = getFenBiao($comId,20);
    #退换货的id
    $id = $request['id'];
    #退换信息
    $tuihuaninfo = $db->get_row("select * from tuihuan where id=$id");
    #订单信息
    $orderinfo = $db->get_row("select * from order$fenbiao where id=$tuihuaninfo->orderId");
    #订单详情信息
    $detailinfo = $db->get_row("select * from order_detail$fenbiao where id=$tuihuaninfo->detailId");
    $userinfo = $db->get_row("select * from users where id=$tuihuaninfo->userId");

    $str = '<div class="body">
                <div class="top-body">
                    <span>订单详情</span>
                </div>
                <div class="buttom-body">
                    <div class="title-body">
                        <span> 商品售后信息：</span>       <span style="font-size: 20px;color: #ff7700;">'.getAfterSaleStatus($tuihuaninfo->type,$tuihuaninfo->status).'</span>
                    </div>
                    

                    <table class="onetable">
                        <tr>
                            <td width="50px" class="with-black"></td>
                            <td width="266px" class="with-black">商品编码</td>
                            <td width="650px" class="with-black">商品名称</td>
                            <td width="234px" class="with-black">规格</td>
                            <td width="120px" class="with-black">数量</td>
                            <td width="127px" class="with-black">金额</td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>'.json_decode($detailinfo->pdtInfo,true)['sn'].'</td>
                            <td>'.json_decode($detailinfo->pdtInfo,true)['title'].'</td>
                            <td>'.json_decode($detailinfo->pdtInfo,true)['key_vals'].'</td>
                            <td>'.$tuihuaninfo->num.'</td>
                            <td>'.$tuihuaninfo->refund_amount.'</td>
                        </tr>
                    </table>
                    
                    
                    <table class="twotable">
                        <tr>
                            <td class="with-black">售后编码</td>
                            <td>'.$tuihuaninfo->sn.'</td>
                            <td class="with-black">退货单号</td>
                            <td>111111111</td>
                        </tr>
                        <tr>
                            <td  class="with-black">售后类型</td>
                            <td>'.changeType($tuihuaninfo->type).'</td>
                            <td class="with-black">申请时间</td>
                            <td>'.$tuihuaninfo->dtTime.'</td>
                        </tr>
                        <tr>
                            <td class="with-black">退货原因</td>
                            <td>'.$tuihuaninfo->reason.'</td>
                            <td class="with-black">申请人</td>
                            <td>'.$userinfo->nickname.'</td>
                        </tr>
                        <tr>
                            <td class="with-black">退货详情</td>
                            <td>'.$tuihuaninfo->remark.'</td>
                            <td class="with-black">图片凭证</td>
                            <td>'.switchImages($tuihuaninfo->uploadimgs).'</td>
                        </tr>
                        <tr>
                            <td class="with-black">客户运费</td>
                            <td>'.changeExpressage($tuihuaninfo->expressage_type,$tuihuaninfo->expressage_price).'</td>
                            <td class="with-black">物流信息</td>
                            <td>'.$tuihuaninfo->user_logistics_info.'</td>
                        </tr>
                        <tr>
                            <td class="with-black">处理记录</td>
                            <td colspan="3">'.ProcessingRecord($tuihuaninfo->processing_record).'</td>
                        </tr>
                        '.getFahuoInfo($tuihuaninfo).'
                    </table>
                    '.getStatusBtn($tuihuaninfo->status,$tuihuaninfo->type).'
                    <div style="margin-bottom: 20px">
                        <span>订单明细</span>
                    </div>
                    
                    <table class="threetable">
                        <tr>
                            <td class="with-black" width="50px"></td>
                            <td class="with-black">商品编码</td>
                            <td class="with-black">商品名称</td>
                            <td class="with-black">规格</td>
                            <td class="with-black">数量</td>
                            <td class="with-black">库存</td>
                            <td class="with-black">单位</td>
                            <td class="with-black">单价</td>
                            <td class="with-black">小计</td>
                            <td class="with-black">已发货</td>
                            <td class="with-black">已退货数量</td>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>'.json_decode($detailinfo->pdtInfo,true)['sn'].'</td>
                            <td>'.json_decode($detailinfo->pdtInfo,true)['title'].'</td>
                            <td>'.json_decode($detailinfo->pdtInfo,true)['key_vals'].'</td>
                            <td>'.$detailinfo->num.'</td>
                            <td>'.$db->get_var('select kucun from demo_product_inventory where id='.$detailinfo->inventoryId).'</td>
                            <td>'.$db->get_var('select dinghuo_units from demo_product where id='.$detailinfo->productId).'</td>
                            <td>'.$detailinfo->unit_price.'</td>
                            <td>'.$tuihuaninfo->num*$detailinfo->unit_price.'</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </table>
                    <div class="information">
                        <div class="information-parent">
                            <div class="information-son-top">
                                订单信息
                            </div>
                            <div class="information-son-body">
                                <ul>
                                    <li>订单总金额：<span class="total-amount">￥'.$orderinfo->price_payed.'</span></li>
                                    <li>支付方式：'.getPayType($orderinfo->pay_type).'</li>
                                    <li>订单号：'.$orderinfo->orderId.'</li>
                                    <li>可得积分：'.$orderinfo->jifen.'</li>
                                    <li>配送方式：'.json_decode($orderinfo->fahuo_json,true)['kuaidi_type'].'</li>
                                    <li>商品重量：'.$orderinfo->weight.$product_set->weight.'</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="information-parent">
                            <div class="information-son-top">
                                收货人信息
                            </div>
                            '.getInformation(json_decode($orderinfo->shuohuo_json,true)).'
                        </div>
                        
                        <div class="information-parent">
                            <div class="information-son-top">
                                会员信息
                            </div>
                            <div class="information-son-body">
                                <ul>
                                    <li>会员名称：'.$userinfo->nickname.'</li>
                                    <li>手机号：'.$userinfo->phone.'</li>
                                    <li>会员等级：'.$userinfo->level.'</li>
                                    <li>会员积分：'.$userinfo->jifen.'</li>
                                    <li>单量：'.$userinfo->order_num.'</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="information-parent">
                            <div class="information-son-top">
                                发票信息'.getFaPiaoInformation(json_decode($orderinfo->fapiao_json,true),$orderinfo->ifkaipiao).'
                            
                        </div>
                    </div>
                </div>
            </div>';

    echo $str;die;

}

/**
 * 添加商家收货地址
 */
function addMerchantAddress(){
    global $db,$request,$comId;
    $tuihuan_id = $request['tuihuan_id'];
    $merchantinfo['商家姓名'] = $request['merchant_name'];
    $merchantinfo['收货电话'] = $request['merchant_phone'];
    $merchantinfo['商家地址'] = $request['merchant_address'];
    $merchantinfo['备注'] = $request['merchant_remark'];
    $admin_name = $_SESSION[TB_PREFIX.'name'];
    $msg = getProcessingRecord($admin_name,$request['msg']);
    $merchantinfo = json_encode($merchantinfo,JSON_UNESCAPED_UNICODE);
    if($db->query("update tuihuan set merchantinfo='$merchantinfo',processing_record='$msg',status=2 where id=$tuihuan_id")){
        recordAfterLog($tuihuan_id,'merchantinfo,processing_record,status',$merchantinfo,','.$msg.',2');

        succeed('成功');
    }
    fail('失败');
}

/**
 * 审核操作
 */
function approveSales(){
    global $db,$request,$comId;
    $id = $request['id'];
    #管理员名称
    $fenbiao = getFenBiao($comId,20);
    $username = $_SESSION[TB_PREFIX.'name'];
    $status = $request['status'];
    $reason = $request['reason'];
    $msg = $request['msg'];
    if (empty($id)){
        fail('无此条退换信息');
    }

    #查询退换货详细信息
    $tuihuan_info = $db->get_row('select * from tuihuan where id='.$id.' and comId='.$comId);
    if($status == -1){
        $db->query("update order_detail$fenbiao set status=1,tuihuoNum=0 where id=$tuihuan_info->detailId");
    }
    if (empty($tuihuan_info)){
        fail('无此条退换信息');
    }
    if ($tuihuan_info->status != 1){
        fail('当前状态不可通过');
    }
    if ($tuihuan_info->type == 3){
        $status = 7;
    }
    #生成操作记录
    $processing_record = getProcessingRecord($username,$msg,$reason);
    if ($db->query("update tuihuan set status=$status,processing_record='$processing_record' where id=$id")){
        recordAfterLog($id,'status,processing_record',$status.','.$processing_record);
        succeed('成功');
    }
    fail('系统错误');
}

/**
 * 确认收货
 */
function confirmReceipt(){
    global $db,$request,$comId;
    $id = $request['id'];
    # | 拆分时需要用到
    $msg = '|'.$request['msg'];
    $tuihuan_info = $db->get_row('select * from tuihuan where id='.$id);
    if ($tuihuan_info->status != 2){
        fail('当前状态不可确认收货');
    }
    #添加处理记录，status改变为待退款状态(3)
    $username = $_SESSION[TB_PREFIX.'name'];
    $msg = $tuihuan_info->processing_record.getProcessingRecord($username,$msg);
    if ($tuihuan_info->type == 2){
        if ($db->query("update tuihuan set status=5,processing_record='$msg' where id=$id")){
            recordAfterLog($id,'status,processing','5,'.$msg);
            succeed('确认成功');
        }
    }else{
        if ($db->query("update tuihuan set status=3,processing_record='$msg' where id=$id")){
            recordAfterLog($id,'status,processing','3,'.$msg);
            succeed('确认成功');
        }
    }
    fail('失败');
}

/**
 * 确认退款
 */
function confirmRefund(){
    global $db,$request,$comId;
    $id = $request['id'];
    $beizhu = $request['beizhu'];
    $refund_amount = $request['tuihuan_price'];
    $msg = '|'.$request['msg'];
    $tuihuaninfo = $db->get_row('select * from tuihuan where id='.$id);
    if (empty($tuihuaninfo) ) fail('退换货信息或者当前状态不可退款');

    if (empty($refund_amount)) fail('退款金额不能为空');
    $orderinfo = checkOrder($tuihuaninfo);
    if (empty($orderinfo)) fail('当前退款的订单不存在');
    #执行退款操作
    // succeed(1,startRefund($orderinfo,$tuihuaninfo,$beizhu,$refund_amount,$msg));
    if (($tuihuaninfo->type == 1 && $tuihuaninfo->status==3) || ($tuihuaninfo->type == 4 && $tuihuaninfo->status==1)){
        if(startRefund($orderinfo,$tuihuaninfo,$beizhu,$refund_amount,$msg)==1){
            succeed('成功');
        }else fail('失败');
    }
    fail('系统错误');
}

/**
 * 确认发货单号
 */
function immedilateDelivery(){
    global $db,$request,$comId;
    $tuihuan_id = $request['tuihuan_id'];
    $wuliubianhao = $request['wuliubianhao'];
    $wuliugongsi = $request['wuliugongsi'];
    $msg = $request['msg'];
    $admin_name = $_SESSION[TB_PREFIX.'name'];
    $tuihuaninfo = $db->get_row("select * from tuihuan where id=$tuihuan_id");
    if(empty($wuliugongsi)) fail('错误，物流单号不能为空');
    if(empty($wuliugongsi)) fail('错误，物流公司不能为空');
    if (empty($tuihuaninfo)) fail('错误,不存在该数据');

    if (($tuihuaninfo->status != 5 && $tuihuaninfo->type ==2) || ($tuihuaninfo->type==3 &&$tuihuaninfo->status != 7)) fail('该状态不可填写发货信息');

    $msg = $tuihuaninfo->processing_record.'|'.getProcessingRecord($admin_name,$msg);

    if ($db->query("update tuihuan set status=6,fahuo_no='$wuliubianhao',fahuo_company='$wuliugongsi',processing_record='$msg' where id=$tuihuan_id")){
        recordAfterLog($tuihuan_id,'status,fahuo_no,fahuo_company,processing_record',"6,'$wuliubianhao','$wuliugongsi','$msg'");
        #创建定时收货任务 定时任务文件位置 TODO /zong_timed_task/time_worker.php
        $shouhuo_day = $db->get_var('select time_shouhuo from demo_shezhi where comId='.$comId);
        $shouhuo_time = strtotime("+$shouhuo_day day");
        $timed_task['comId'] = $comId;
        $timed_task['dtTime'] = $shouhuo_time;
        $timed_task['router'] = 'order_receivedReturn';
        $timed_task['params'] = '{"tuihuan_id":'.$tuihuan_id.'}';
        $db->insert_update('demo_timed_task',$timed_task,'id');
        succeed('成功');
    }
    fail('错误');
}

/**
 * 验证退换的订单是否存在 存在返回订单基本信息
 * @param $tuihuaninfo object 退换货的基本信息
 * @return array|null
 */
function checkOrder($tuihuaninfo){
    global $db,$comId;
    $fenbiao = getFenBiao($comId,20);
    $orderinfo = $db->get_row("SELECT
                                    *
                                FROM
                                    order$fenbiao AS o
                                INNER JOIN order_detail19 AS d ON o.id = d.orderId
                                WHERE
                                    o.id = $tuihuaninfo->orderId
                                AND d.id = $tuihuaninfo->detailId
                                AND o.userId = $tuihuaninfo->userId");
    if (empty($orderinfo)){
        fail('操作错误！无此订单');
    } else {
        return $orderinfo;
    }
}

/**
 * 开始退款
 * @param $orderinfo object 订单的基本信息 order表与detail表
 * @param $tuihuaninfo object 退换货的基本信息
 * @param $beizhu string 备注
 * @param $refund_amount float 退款金额
 * @param $msg string 信息
 * @return int
 */
function startRefund($orderinfo,$tuihuaninfo,$beizhu,$refund_amount,$msg){
    global $adminRole,$db,$comId;
    if (!$adminRole>=7 || empty((int)$_SESSION[TB_PREFIX.'admin_userID'])){
        fail('无此权限');
    }
    #判断支付方式，进行原路返回
    $pay_json = json_decode($orderinfo->pay_json,true);
    $fenbiao = getFenBiao($comId,20);
    $admin_name = $_SESSION[TB_PREFIX.'admin_name'];
    $processing_record = $tuihuaninfo->processing_record.getProcessingRecord($admin_name,$msg,$beizhu);
    $time = date('Y-m-d H:i:s');
    $orderno = $db->get_var("select orderId from order$fenbiao where id=$tuihuaninfo->orderId");
    $orderInfo = '订单取消，订单号：'.$orderno;
    if (!empty($pay_json['yue']['price'])){ #余额支付返还
        try {
            $db->query('begin;');
            #返还积分
            $db->query("update users set money=money+$refund_amount where id=$tuihuaninfo->userId");
            // file_put_contents('test.txt',"update users set money=money+$refund_amount where id=$tuihuaninfo->userId");
            #退换货表修改状态 添加信息
            $db->query("update tuihuan
                        set status = 4,
                         refund_amount = $refund_amount,
                         processing_record = '$processing_record',
                         refund_time = '$time'
                        where
                            id = $tuihuaninfo->id");
            $db->query("update order$fenbiao set status=-1 where id=$tuihuaninfo->orderId");
            #orderdetail表修改退货数量
            $db->query("update order_detail$fenbiao set tuihuoNum=$tuihuaninfo->num where id=$tuihuaninfo->detailId");
            #添加用户积分记录
            $yue = $db->get_row('select jifen,money from users where id='.$tuihuaninfo->userId);
            $db->query("insert into user_liushui$fenbiao (
                            userId,
                            comId,
                            orderId,
                            money,
                            type,
                            dtTime,
                            remark,
                            yue,
                            orderInfo,
                            order_id
                        )
                        values (
                            $tuihuaninfo->userId,
                            $comId,
                            '$orderno',
                            $refund_amount,
                            -5,
                            '$time',
                            '订单退款',
                            $yue->money,
                            '$orderInfo',
                            $tuihuaninfo->orderId
                            )");
            $db->query("insert into user_jifen$fenbiao (
                            userId,
                            comId,
                            orderId,
                            yue,
                            type,
                            dtTime,
                            remark,
                            orderInfo,
                            jifen_jieyu,
                            yue_jieyu
                        )
                        values (
                            $tuihuaninfo->userId,
                            $comId,
                            '$orderno',
                            $refund_amount,
                            -5,
                            '$time',
                            '订单退款',
                            '$orderInfo',
                            $yue->jifen,
                            $yue->money
                            )");
            recordAfterLog($tuihuaninfo->id,'status,refund_amount,processing_record,refund_time','4,'.$refund_amount.','.$processing_record.','.$time);
            $db->query('commit;');
            return 1;
        }catch (\Exception $e){
            $db->query('rollback;');
            return $e;
        }

    }
    if (!empty($pay_json['jifen']['price'])){
        try {
            $db->query('begin;');
            #返还积分
            $db->query("update users set jifen=jifen+$refund_amount where id=$tuihuaninfo->userId");
            // file_put_contents('test.txt',"update users set jifen=jifen+$refund_amount where id=$tuihuaninfo->userId");
            #退换货表修改状态 添加信息
            $db->query("update tuihuan
                        set status = 4,
                         refund_amount = $refund_amount,
                         processing_record = '$processing_record',
                         refund_time = '$time'
                        where
                            id = $tuihuaninfo->id");
            #orderdetail表修改退货数量
            $db->query("update order_detail$fenbiao set tuihuoNum=$tuihuaninfo->num where id=$tuihuaninfo->detailId");
            #添加用户积分记录
            $yue = $db->get_row('select jifen,money from users where id='.$tuihuaninfo->userId);
            $db->query("insert into user_jifen$fenbiao (
                            userId,
                            comId,
                            orderId,
                            jifen,
                            type,
                            dtTime,
                            remark,
                            orderInfo,
                            jifen_jieyu,
                            yue_jieyu
                        )
                        values (
                            $tuihuaninfo->userId,
                            $comId,
                            '$orderno',
                            $refund_amount,
                            -5,
                            '$time',
                            '订单退款',
                            '$orderInfo',
                            $yue->jifen,
                            $yue->money
                            )");
            recordAfterLog($tuihuaninfo->id,'status,refund_amount,processing_record,refund_time','4,'.$refund_amount.','.$processing_record.','.$time);
            $db->query('commit;');
            return 1;
        }catch (\Exception $e){
            $db->query('rollback;');
            return $e;
        }
    }
}

/**
 * 每次insert或者update退换货表进行记录
 * @param $id int 退换货id
 * @param $filed string insert或者update的列 需要处理为 filed1,filed2的格式
 * @param $dat string insert或者update列的数据 同上
 */
function recordAfterLog($id,$filed,$dat){
    global $db,$comId;
    $data = [
        'tuihuanId'=>$id,
        'comId'=>$comId,
        'field'=>$filed,
        'data'=>$dat,
        'dtTime'=>date('Y-m-d H:i:s')
    ];
    $db->insert_update('tuihuan_log',$data,'id');
}

/**
 * 根据状态生成动态按钮
 * @param $status
 * @return string
 */
function getStatusBtn($status,$type){
    if ($status == 1 && $type!=4){
        return '<div class="check">
                    <button class="tongguo" onclick="tuihuan_tongguo()">审批通过</button>
                    <button class="bohui" onclick="tuihuan_bohui()">审批驳回</button> 
                </div>
                
                <div class="bohui-text">
                    <span style="margin-left: 20px">驳回原因：</span>
                    <textarea name="bohui_content" id="bohui_content" cols="50" rows="4" style="margin-top: 30px"></textarea>
                    <button class="bohui" style="text-align: center">审批驳回</button>
                </div>
                ';
    }
    if ($status == 2){
        return '<div class="check">
                    <button class="qrsh" onclick="tuihuan_qrsh()">确认收货</button>
                </div>';
    }
    if ($status == 3 || ($type==4 && $status==1)){
        return '<div>
                    <span>提交退款信息</span>
                    <div class="tuikuan_table">
                        <table>
                            <tr>
                                <td>退款金额</td>
                                <td><input type="text" name="tuikuan_price" id="tuikuan_price"></td>
                                <td style="color: #979797; text-align: left;"><span style="color: red;" >*</span>这里需要减去活动优惠差价</td>
                            </tr>
                            <tr>
                                <td>备注</td>
                                <td><textarea name="beizhu" id="beizhu" cols="61" rows="4"></textarea></td>
                            </tr>
                        </table>
                        <button class="ljtk" onclick="tuihuan_ljtk()">立即退款</button>
                    </div>
                </div>';
    }
    if ($status == 5 || $status == 7){
        return '<div>
                    <span>提交物流信息</span>
                    <div>
                        <table class="wuliu_table">
                            <tr>
                                <td width="100px">订单编号：</td>
                                <td style="text-align: left"><input type="text" id="wuliubianhao"></td>
                            </tr>
                            <tr>
                                <td width="100px">物流公司</td>
                                <td style="text-align: left"><input type="text" id="wuliugongsi"></td>
                            </tr>
                        </table>
                    </div>
                    <button id="ljfh" onclick="tuihuan_ljfh()">立即发货</button>
                </div>';
    }
}

/**
 * 处理记录
 * @param $processing_record
 * @return string
 */
function ProcessingRecord($processing_record){
    if (empty($processing_record)){
        return $processing_record;
    }
    $processing_record = explode('|',$processing_record);
    $str = '';
    if (!empty($processing_record)){
        foreach ($processing_record as $v){
            $str .= $v.'<br>';
        }
    }
    return $str;
}

/**
 * 生成处理记录
 * @param $username string 管理员名称
 * @param $type int 状态
 * @param $reason string 备注
 * @return string 拼接信息
 */
function getProcessingRecord($username,$msg,$reason=''){
    if (!empty($reason)){
        $msg .= ',原因：'.$reason;
    }
    $msg.='【'.$username.date('Y-m-d H:i:s').'】';
    return $msg;
}

/**
 * 获取发货信息
 * @param $tuihuaninfo
 * @return string
 */
function getFahuoInfo($tuihuaninfo){

    if ($tuihuaninfo->type == 2 && $tuihuaninfo->status>=6){
        return "<tr>
                    <td class='with-black'>订单编号</td>
                    <td>$tuihuaninfo->fahuo_no</td>
                    <td class='with-black'>物流公司</td>
                    <td>$tuihuaninfo->fahuo_company</td>
                </tr>";
    }
}

/**
 * 生成售后类型
 * @param $type
 * @return mixed
 */
function changeType($type){
    $typeInfo = [1=>'退货',2=>'换货',3=>'补发商品',4=>'补款'];
    return $typeInfo[$type];
}

/**
 * 拆解图片
 * @param $images
 * @return string
 */
function switchImages($images){
    if (empty($images)){
        return '';
    }
    $image_arr = explode(',',$images);
    $img_str = '';
    foreach ($image_arr as $k=>$v){
        $img_str .= "<img src='$v' class='tuihuo-image' width='60px' height='60px'/>";
    }
    return $img_str;
}

/**
 * 生成客户运费
 * @param $type int 1买家承担 2卖家承担
 * @param $price float 运费
 * @return string
 */
function changeExpressage($type,$price){
    return $type==2 ? '卖家独自承担，运费：￥'.$price : '买家独自承担'  ;
}

/**
 * 发票信息
 * @param $fapiaoxinxi
 * @param $ifkaipiao
 * @return string
 */
function getFaPiaoInformation($fapiaoxinxi,$ifkaipiao){
    if ($ifkaipiao == 0){
        return '<span class="not-kaipiao">不需要开票</span></div>';
    }else{
        return '</div>'.getInformation($fapiaoxinxi);
    }
}

/**
 * 生成商品售后信息
 * @param $type
 * @param $status
 * @return string
 */
function getAfterSaleStatus($type,$status){
    $status_code = [
        #退货
        1=>[
            -2  =>    '已取消',
            -1  =>    '已驳回',
            1   =>    '待审核',
            2   =>    '已通过，待收货',
            3   =>    '待退款',
            4   =>    '已完成'
        ],
        #换货
        2=>[
            -2  =>    '已取消',
            -1  =>    '已驳回',
            1   =>    '待审核',
            2   =>    '已通过，待收货',
            4   =>    '已完成',
            5   =>    '换货发货',
            6   =>    '待客户收货'
        ],
        #补发商品
        3=>[
            -2  =>    '已取消',
            -1  =>    '已驳回',
            1   =>    '待审核',
            4   =>    '已完成',
            6   =>    '待客户收货',
            7   =>    '待发货'
        ],
        #补款
        4=>[
            -2  =>    '已取消',
            -1  =>    '已驳回',
            1   =>     '待审核',
            4   =>     '已完成'
        ]

    ];
    $type_code = [
        1   =>     '退货',
        2   =>     '换货',
        3   =>     '补发商品',
        4   =>     '补款'
    ];
    return $type_code[$type].$status_code[$type][$status];
}

/**
 * 分解json信息
 * @param $informationArr
 * @return string
 */
function getInformation($informationArr){
    $str = '<div class="information-son-body">
                <ul>';
    foreach ($informationArr as $k=>$v){
        $str .= '<li>'.$k.'：'.$v.'</li>';
    }
    $str .= '</ul>
            </div>';
    return $str;
}

/**
 * 成功返回信息
 * @param $code
 * @param $msg
 * @param string $data
 * @return false|string
 */
function succeed($msg,$data=[],$code=1){
    echo json_encode(['code'=>$code,'msg'=>$msg,'data'=>$data]);die;
}

/**
 * 错误返回信息
 * @param $code
 * @param $msg
 * @return false|string
 */
function fail($msg,$code=0){
    echo json_encode(['code'=>$code,'msg'=>$msg]);die;
}



//function alertForm($tuihuaninfo){
//    if ($tuihuaninfo->type==2 && $tuihuaninfo->status==1){
//        return '<div id="alert-from">
//                    <span>商家姓名</span><input type="text" id="merchant-name"><br>
//                    <span>商家电话</span><input type="text" id="merchant-phone"><br>
//                    <span>商家地址</span><input type="text" id="merchant-address"><br>
//                    <span>备注</span><input type="text" id="merchant-remark"><br>
//                </div>';
//    }
//}