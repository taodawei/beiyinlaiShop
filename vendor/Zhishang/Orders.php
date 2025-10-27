<?php
namespace Zhishang;
class Orders{
    public function del(){
        global $db,$request,$comId;
        $orderId = (int)$request['order_id'];
        $userId = (int)$request['user_id'];
        $fenbiao = getFenbiao($comId,20);
        $db->query("update order$fenbiao set is_del=1 where id=$orderId and userId=$userId and status in(-1,4)");
        return '{"code":"1","message":"删除成功"}';
    }
    //社区站长核销
    public function hexiao(){
        global $db,$request,$comId;
        $fenbiao = getFenbiao($comId,20);
        $userId = (int)$request['user_id'];
        $order_id = $this->get_hexiao_id($request['hexiaoma']);
        $order = $db->get_row("select status,shequ_id,userId,fahuoId from order$fenbiao where id=$order_id");
        if(empty($order)){
            return '{"code":0,"message":"未找到该订单，请检查核销码"}';
        }
        if($order->status!=3 && $order->status!=2){
            return '{"code":0,"message":"该订单状态不支持核销！请检查确认"}';
        }
        $if_user_shequ = $db->get_var("select id from demo_shequ where id=$order->shequ_id and userId=$userId");
        if(empty($if_user_shequ)){
            return '{"code":0,"message":"该订单不隶属于您的社区！请检查核实"}';
        }
        $db->query("update order_fahuo$fenbiao set status=3 where id=$order->fahuoId limit 1");
        $request['orderId'] = $order_id;
        return $this->qrshouhuo($order->userId);
    }
    //站长配送完成
    public function peisongDone(){
        global $db,$request,$comId;
        $fenbiao = getFenbiao($comId,20);
        $userId = (int)$request['user_id'];
        $order_id = (int)$request['order_id'];
        $order = $db->get_row("select status,shequ_id,userId,fahuoId from order$fenbiao where id=$order_id");
        if(empty($order)){
            return '{"code":0,"message":"未找到该订单，请检查核销码"}';
        }
        if($order->status!=3 && $order->status!=2){
            return '{"code":0,"message":"该订单状态不支持核销！请检查确认"}';
        }
        $if_user_shequ = $db->get_var("select id from demo_shequ where id=$order->shequ_id and userId=$userId");
        if(empty($if_user_shequ)){
            return '{"code":0,"message":"该订单不隶属于您的社区！请检查核实"}';
        }
        $db->query("update order_fahuo$fenbiao set status=3 where id=$order->fahuoId limit 1");
        $request['orderId'] = $order_id;
        return $this->qrshouhuo($order->userId);
    }
    /*源一拼 社区订单数量*/
    public function shequOrderNum(){
        global $db,$request,$comId;
        $fenbiao = getFenbiao($comId,20);
        $userId = (int)$request['user_id'];
        $now = time();
        $shequ_id = (int)$db->get_var("select id from demo_shequ where comId=$comId and userId=$userId and status=1 limit 1");
        $sql="select id,status,peisong_time,peisong_type from order$fenbiao where comId=$comId and shequ_id=$shequ_id and status>-1";
        $orders = $db->get_results($sql);
        $today_num = $db->get_var("select count(*) from order$fenbiao where comId=$comId and shequ_id=$shequ_id and peisong_time like '".date("Y-m-d")."%'");
        $tihuo_num = $db->get_var("select count(*) from order$fenbiao where comId=$comId and shequ_id=$shequ_id and status in(2,3) and peisong_type=1");
        $tixing_num = $db->get_var("select count(*) from order$fenbiao where comId=$comId and shequ_id=$shequ_id and status in(2,3) and UNIX_TIMESTAMP(peisong_time)<$now");
        $hexiao_num = $db->get_var("select count(*) from order$fenbiao where comId=$comId and shequ_id=$shequ_id and status=4");
        $all = $db->get_var("select count(*) from order$fenbiao where comId=$comId and shequ_id=$shequ_id");
        $data = array("today_num"=>$today_num,"tihuo_num"=>$tihuo_num,"tixing_num"=>$tixing_num,"hexiao_num"=>$hexiao_num,"zong_num"=>$all);
        return json_encode(array("code"=>1,"message"=>"成功","data"=>$data),JSON_UNESCAPED_UNICODE);
    }
    public function shequOrders(){
        global $db,$request,$comId;
        $fenbiao = getFenbiao($comId,20);
        $userId = (int)$request['user_id'];
        $keyword = $request['keyword'];
        $scene = (int)$request['scene'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $peisong_time = $request['peisong_time'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=20;
        $fenbiao = getFenbiao($comId,20);
        $shequ_id = (int)$db->get_var("select id from demo_shequ where comId=$comId and userId=$userId and status=1 limit 1");
        if($shequ_id==0){
            return '{"code":0,"message":"您不是社区站长或社区已关闭！"}';
        }
        $sql="select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type,shuohuo_json,peisong_time from order$fenbiao where comId=$comId and shequ_id=$shequ_id";
        if(!empty($scene)){
            switch($scene){
                case 1:
                    $sql.=" and status in(2,3) and peisong_type=2";
                    break;
                case 2:
                    $sql.=" and status in(2,3) and peisong_type=1";
                    break;
                case 3:
                    $sql.=" and status=4";
                    break;
                case 4://提醒取货订单
                    $now = time();
                    $sql.=" and status in(2,3) and peisong_type=1 and UNIX_TIMESTAMP(peisong_time)<$now";
                    break;
            }
        }
        if(!empty($keyword)){
            $sql.=" and (product_json like '%$keyword%' or shuohuo_json like '%$keyword%')";
        }
        if(!empty($peisong_time)){
            $sql.=" and peisong_time like '$peisong_time%'";
        }
        /*if($scene==4){
			file_put_contents('sql.txt',$sql);
		}*/
        //file_put_contents('request.txt',$sql);
        $count = $db->get_var(str_replace('id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,peisong_type,shuohuo_json,peisong_time','count(*)',$sql));
        $sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
        $pdts = $db->get_results($sql);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['count'] = $count;
        $return['pages'] = ceil($count/$pageNum);
        $return['data'] = array();
        $now = time();
        if(!empty($pdts)){
            foreach ($pdts as $i=>$pdt) {
                $data = array();
                $data['id'] = $pdt->id;
                $data['orderId'] = $pdt->orderId;
                switch ($pdt->status) {
                    case 0:
                        $data['status_info'] = '待成团';
                        break;
                    case 2:
                        $data['status_info'] = '待发货';
                        break;
                    case 3:
                        $data['status_info'] = '待收货';
                        break;
                    case 4:
                        $data['status_info'] = '已完成';
                        break;
                    case -3:
                        $data['status_info'] = '退换货';
                        break;
                    case -5:
                        $pay_end = strtotime($pdt->pay_endtime);
                        if($pay_end>$now){
                            $data['status_info'] = '待支付';
                        }else{
                            $data['status_info'] = '无效';
                        }
                        break;
                    case -1:
                        $data['status_info'] = '无效';
                        $qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
                        if(in_array($pdt->remark,$qx_remarks)){
                            $data['status_info'] = $pdt->remark;
                        }
                        break;
                }
                $product_json = json_decode($pdt->product_json);
                $shouhuo_json = json_decode($pdt->shuohuo_json,true);
                $data['products'] = $product_json;
                $data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
                $data['endTime'] = strtotime($pdt->pay_endtime)*1000;
                //$data['jishiqi'] = 0;
                //$data['jishiqi'] = $data['status_info']=='<span style="color:#cf2950;">待支付</span>'?1:0;
                $data['price'] = $pdt->price;
                $data['price_payed'] = $pdt->price_payed;
                $data['num'] = $pdt->pdtNums;
                $data['comId'] = $pdt->comId;
                $data['status'] = $pdt->status;
                $data['peisong_type_id'] = $pdt->peisong_type;
                $peisong_type = '普通快递';
                switch($pdt->peisong_type){
                    case 1:$peisong_type = '站点自提';break;
                    case 2:$peisong_type = '社区配送';break;
                }
                $data['peisong_type'] = $peisong_type;
                $data['peisong_time'] = $pdt->peisong_time;
                $data['address'] = $shouhuo_json['详细地址'];
                $return['data'][] = $data;
            }
        }
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    public function lists(){
        global $db,$request,$comId;
        $scene = (int)$request['scene'];
        $type = $request['type'];
        $userId = (int)$request['user_id'];
        $keyword = $request['keyword'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        $payType = (int)$request['pay_type'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=10;
        $now = date("Y-m-d H:i:s");

        $fenbiao = getFenbiao($comId,20);
        $sql="select id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,ifpingjia,fahuoId from order$fenbiao where comId=$comId and userId=$userId and is_del=0 and if_jifen = 0";
        
        if($payType > 0){
            $sql .= " and pay_type = $payType ";
        }
        
        if(!empty($scene)){
            switch($scene){
                case 1:
                    $sql.=" and status=-5 and pay_endtime>'$now'";
                    break;
                case 2:
                    $sql.=" and status=2";
                    break;
                case 3:
                    $sql.=" and status=3";
                    break;
                case 4:
                    $sql.=" and status=4 and ifpingjia=0";
                    break;
                case 5:
                    $sql.=" and status=4 and ifpingjia=1";
                    break;
                case 6:
                    $sql.=" and status=1";
                    break;
                case -1:
                    $sql.=" and status=-1";
                    break;
            }
        }
        if(!empty($keyword)){
            $sql.=" and (product_json like '%$keyword%' or shuohuo_json like '%$keyword%')";
        }
        //file_put_contents('request.txt',$sql);
        $count = $db->get_var(str_replace('id,orderId,dtTime,ispay,pay_endtime,price_payed,status,product_json,pdtNums,price,comId,remark,yushouId,tuan_id,ifpingjia,fahuoId','count(*)',$sql));


        $sql.=" order by dtTime desc limit ".((int)($page-1)*$pageNum).",".$pageNum;
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
                $data = array();
                $data['id'] = $pdt->id;
                $data['tuan_id'] = $pdt->tuan_id;
                $data['order_id'] = $pdt->orderId;
                $data['fahuo_id'] = $pdt->fahuoId;
                $data['if_cancel'] = $data['if_pingjia'] = $data['if_del'] = $data['if_pay'] = $data['if_shouhou'] = 0;
                switch ($pdt->status) {
                    case 0:
                        $data['status_info'] = '待成团';
                        break;
                    case 1:
                        $data['status_info'] = '待审核';
                        break;
                    case 2:
                        $data['status_info'] = '待发货';
                        break;
                    case 3:
                        $data['status_info'] = '待收货';
                        break;
                    case 4:
                        $data['status_info'] = '已完成';
                        break;
                    case -3:
                        $data['status_info'] = '退换货';
                        break;
                    case -5:
                        $pay_end = strtotime($pdt->pay_endtime);
                        if($pay_end>$now){
                            $data['status_info'] = '待支付';
                            $data['if_cancel'] = 1;
                            $data['if_pay'] = 1;
                        }else{
                            $data['status_info'] = '无效';
                            $data['if_del'] = 1;
                        }
                        break;
                    case -1:
                        $data['status_info'] = '无效';
                        $data['if_del'] = 1;
                        $qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
                        if(in_array($pdt->remark,$qx_remarks)){
                            $data['status_info'] = $pdt->remark;
                        }
                        break;
                }
                $product_json = json_decode($pdt->product_json);
                
                foreach ($product_json as $pk => $item) {
                    $pingjia_id = $db->get_var("select id from order_comment$fenbiao where orderId=$pdt->id and inventoryId=$item->id limit 1");
                    $ifShouhou = $db->get_var("select ifshouhou from order_detail$fenbiao where orderId = $pdt->id and inventoryId = $item->id limit 1 ");
                    $product_json[$pk]->ifshouhou = $ifShouhou;
                }
                
                $data['products'] = $product_json;
                $data['dtTime'] = date("Y-m-d H:i",strtotime($pdt->dtTime));
                $data['endTime'] = strtotime($pdt->pay_endtime)*1000;
                $data['jishiqi'] = 0;
                if($data['status_info']=='待支付' && $pdt->yushouId==0){
                    $data['jishiqi'] = 1;
                }
                //$data['jishiqi'] = $data['status_info']=='<span style="color:#cf2950;">待支付</span>'?1:0;
                $data['price'] = $pdt->price;
                $data['price_payed'] = $pdt->price_payed;
                $data['num'] = $pdt->pdtNums;
                $data['comId'] = $pdt->comId;
                $data['status'] = $pdt->status;
                $data['if_pingjia'] = $pdt->ifpingjia;
                $return['data'][] = $data;
            }
        }
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    public function detail(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $id = (int)$request['id'];
        $fenbiao = getFenbiao($comId,20);
        $order = $db->get_row("select * from order$fenbiao where id=$id");
        // var_dump($fenbiao);die;
        if(empty($order)){
            return '{"code":0,"message":"订单不存在！"}';
        }
        $shouhuo_json = json_decode($order->shuohuo_json,true);
        $product_json = json_decode($order->product_json);
        $now = time();
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $return['data']['shouhuo_info']['name'] = $shouhuo_json['收件人'];
        $return['data']['shouhuo_info']['phone'] = $shouhuo_json['手机号'];
        $return['data']['shouhuo_info']['address'] = $shouhuo_json['所在地区'].$shouhuo_json['详细地址'];
        if(!empty($product_json)){
            foreach ($product_json as $item) {
                $pingjia_id = $db->get_var("select id from order_comment$fenbiao where orderId=$order->id and inventoryId=$item->id limit 1");
                $ifShouhou = $db->get_var("select ifshouhou from order_detail$fenbiao where orderId = $order->id and inventoryId = $item->id limit 1 ");
                $item->ifshouhou = $ifShouhou;
                $item->ifpingjia = $pingjia_id>0?1:0;
                $return['data']['products'][] = $item;
            }
        }
        $return['data']['fahuo_info'] = array();
        if(!empty($order->fahuoId)){
            $fahuo = $db->get_row("select kuaidi_title,kuaidi_order from order_fahuo$fenbiao where id=$order->fahuoId");
            if(!empty($fahuo->kuaidi_title)){
                $return['data']['fahuo_info']['fahuo_id'] = $order->fahuoId;
                $return['data']['fahuo_info']['company'] = $fahuo->kuaidi_title;
                $return['data']['fahuo_info']['order_id'] = $fahuo->kuaidi_order;
            }
        }
        $return['data']['tuan_info'] = array();
        if(!empty($order->tuan_id)){
            $tuan = $db->get_row("select * from demo_tuan where id=$order->tuan_id");
            switch ($tuan->status) {
                case 0:
                    $pay_end = strtotime($tuan->endTime);
                    if($pay_end>$now){
                        $status_info = '待成团';
                        $dai_chengtuan = 1;
                    }else{
                        $status_info = '拼团失败';
                    }
                    break;
                case 1:
                    $status_info = '拼团成功';
                    break;
                case -1:
                    $status_info = '拼团失败';
                    break;
            }
            if($order->if_zong==1){
                $db_service = get_zhishang_db();
                $tuanzhang = $db_service->get_row("select name as nickname from demo_user where id=$tuan->tuanzhang");
            }else{
                $tuanzhang = $db->get_row("select nickname from users where id=$tuan->tuanzhang");
            }
            $return['data']['tuan_info']['id'] = $tuan->id;
            $return['data']['tuan_info']['status'] = $status_info;
            $return['data']['tuan_info']['tuanzhang'] = $tuanzhang;
        }
        $return['data']['order_id'] = $order->orderId;
        $return['data']['status'] = $order->status;
        $return['data']['if_cancel'] = $return['data']['if_pingjia'] = $return['data']['if_del'] = $return['data']['if_pay'] = 0;
        switch ($order->status) {
            case 0:
                $return['data']['status_info'] = '待成团';
                break;
            case 1:
                $return['data']['status_info'] = '待审核';
                break;
            case 2:
                $return['data']['status_info'] = '待发货';
                break;
            case 3:
                $return['data']['status_info'] = '待收货';
                break;
            case 4:
                $return['data']['status_info'] = '已完成';
                break;
            case -3:
                $return['data']['status_info'] = '退换货';
                break;
            case -5:
                $pay_end = strtotime($order->pay_endtime);
                if($pay_end>$now){
                    $return['data']['status_info'] = '待支付';
                    $return['data']['if_pay'] = 1;
                    $return['data']['if_cancel'] = 1;
                    $dai_pay = 1;
                }else{
                    $return['data']['status_info'] = '无效';
                    $return['data']['if_del'] = 1;
                }
                break;
            case -1:
                $return['data']['status_info'] = '无效';
                $return['data']['if_del'] = 1;
                $qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
                if(in_array($order->remark,$qx_remarks)){
                    $return['data']['status_info'] = $order->remark;
                }
                break;
        }
        $return['data']['remark'] = $order->remark;
        if($dai_pay==1){
            $show_pay = 1;
            if(!empty($order->yushouId) && $order->price_dingjin==0){
                $yushou = $db->get_row("select * from yushou where id=$order->yushouId");
                if($yushou->paytype==2){
                    $now = time();
                    $startTime1 = strtotime($yushou->startTime1);
                    $endTime1 = strtotime($yushou->endTime1);
                    if($now<$startTime1){
                        $show_pay = 0;
                    }else if($now>$endTime1){
                        $show_pay = 2;
                    }
                }
                if($yushou->type==2){
                    $price_json = json_decode($yushou->price_json,true);
                    $price = $price_json[0]['price'];
                    $columns = array_column($price_json,'num');
                    array_multisort($columns,SORT_DESC,$price_json);
                    foreach ($price_json as $val) {
                        if($yushou->num_saled>=$val['num']){
                            $order->price = $val['price'];
                            break;
                        }
                    }
                }
            }
            $return['data']['yushou']['if_yushou'] = 1;
            $return['data']['yushou']['yushou_ifpay'] = 0;
            if($yushou->paytype==2){
                $return['data']['yushou']['yushou_pay_starttime'] = $yushou->startTime1;
                $return['data']['yushou']['yushou_pay_endtime'] = $yushou->startTime1;
            }
            if($show_pay==1){
                $return['data']['yushou']['yushou_ifpay'] = 1;
            }
        }
        $return['data']['price'] = $order->price;
        $return['data']['price_need_pay'] = $order->price-$order->price_payed;
        $price_json = json_decode($order->price_json,true);
        $return['data']['yunfei'] = empty($price_json['yunfei']['price'])?0:$price_json['yunfei']['price'];
        $return['data']['dtTime'] = $order->dtTime;
        $return['data']['pay_info'] = array();
        $paypz = $db->get_row("select * from demo_paypz where orderId=$order->id");
        $return['data']['paypz'] = array();
        if(!empty($paypz)){
             $return['data']['paypz'] = $paypz;
        }
        
        if(!empty($order->price_json)){
            $price_json = json_decode($order->price_json,true);
            if(!empty($price_json['yhq'])){
                $return['data']['pay_info']['yhq'] = $price_json['yhq']['price'];
            }
        }
        if(!empty($order->pay_json)){
            $pay_json = json_decode($order->pay_json,true);
            if(!empty($pay_json['jifen'])){
                $return['data']['pay_info']['jifen'] = $pay_json['jifen']['price'];
            }
            if(!empty($pay_json['yue'])){
                if(!empty($pay_json['yue'])){
        		    $yue = 0;
        		    foreach ($pay_json['yue'] as $pv){
        		        $yue = bcadd($yue, $pv['price'], 2);
        		    }
        		}
                $return['data']['pay_info']['yue'] = $yue;
            }
            if(!empty($pay_json['weixin'])){
                $return['data']['pay_info']['weixin'] = $pay_json['weixin']['price'];
            }
            if(!empty($pay_json['applet'])){
                $return['data']['pay_info']['weixin'] = $pay_json['applet']['price'];
            }
            if(!empty($pay_json['alipay'])){
                $return['data']['pay_info']['alipay'] = $pay_json['alipay']['price'];
            }
            if(!empty($pay_json['lipinka'])){
                $return['data']['pay_info']['lipinka'] = $pay_json['lipinka']['price'];
            }
            if(!empty($pay_json['lipinka1'])){
                $return['data']['pay_info']['lipinka1'] = $pay_json['lipinka1']['price'];
            }
            if(!empty($pay_json['other'])){
                $return['data']['other']['lipinka1'] = $pay_json['other']['price'];
            }
            if(!empty($pay_json['dingjin'])){
                $return['data']['dingjin']['lipinka1'] = $pay_json['dingjin']['price'];
            }
            if(!empty($pay_json['yibao'])){
                $return['data']['yibao']['lipinka1'] = $pay_json['yibao']['price'];
            }
        }
        if($order->shequ_id>0){
            $tihuoma = self::get_36id($id);
            $peisong_type = '普通快递';
            switch($order->peisong_type){
                case 1:$peisong_type = '站点自提';break;
                case 2:$peisong_type = '社区配送';break;
            }
            $shequ = $db->get_row("select * from demo_shequ where id=$order->shequ_id");
            $tuanzhang = $db->get_row("select nickname,username,phone from users where id=$shequ->userId");
            $return['data']['shequ']['tihuoma'] = $tihuoma;
            $return['data']['shequ']['image'] = $shequ->originalPic;
            $return['data']['shequ']['title'] = $shequ->title;
            $return['data']['shequ']['address'] = $shequ->address;
            $return['data']['shequ']['name'] = $shequ->name;
            $return['data']['shequ']['phone'] = $shequ->phone;
            $return['data']['shequ']['peisong_type'] = $peisong_type;
            $return['data']['shequ']['peisong_type_id'] = $order->peisong_type;
            $return['data']['shequ']['peisong_fanwei'] = $shequ->peisong_area;
        }
        $return['data']['ifpingjia'] = $order->ifpingjia;
        $return['data']['peisong_type'] = $order->peisong_type;
        $return['data']['peisong_time'] = $order->peisong_time;
        $return['data']['if_share'] = $order->if_share;
        $return['data']['jifen_pay'] = array("if_open"=>0,"jifen"=>0,"money"=>0);
        if($dai_pay==1 && $pay_json['jifen']['price']==0){
            $daizhifu = $order->price-$order->price_payed;
            $jifen_pay = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
            $user_jifen = $db->get_var("select jifen from users where id=$order->userId");
            if($jifen_pay->if_jifen_pay==1 && !empty($jifen_pay->jifen_pay_rule) && !empty($user_jifen)){
                $jifen_rule = json_decode($jifen_pay->jifen_pay_rule);
                if($daizhifu>=$jifen_rule->man){
                    if($jifen_rule->if_bili==1 && !empty($jifen_rule->bili)){
                        $max_money = (int)($daizhifu*$jifen_rule->bili*100)/10000;
                    }else{
                        $max_money = $daizhifu;
                    }
                    if($jifen_rule->if_shangxian==1 && $max_money>$jifen_rule->shangxian){
                        $max_money = $jifen_rule->shangxian;
                    }
                    $need_jifen = $max_money*$jifen_rule->jifen;
                    if($need_jifen>$user_jifen){
                        $max_money = (int)($user_jifen*100/$jifen_rule->jifen)/100;
                        //$need_jifen = $max_money*$jifen_rule->jifen;
                    }
                    if($max_money>$daizhifu){
                        $max_money = $daizhifu;
                    }
                    if($max_money>0){
                        $need_jifen = $max_money*$jifen_rule->jifen;
                        $return['data']['jifen_pay']['if_open'] = 1;
                        $return['data']['jifen_pay']['jifen'] = $need_jifen;
                        $return['data']['jifen_pay']['money'] = $max_money;
                    }
                }
            }
        }
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    /*分享订单*/
    public function shareOrder(){
        global $db,$request,$comId;
        $id = (int)$request['id'];
        $fenbiao = getFenbiao($comId,20);
        $order = $db->get_row("select * from order$fenbiao where id=$id");
        if(empty($order)){
            return '{"code":0,"message":"订单不存在！"}';
        }
        $shouhuo_json = json_decode($order->shuohuo_json,true);
        $product_json = json_decode($order->product_json);
        $now = time();
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $return['data']['shouhuo_info']['name'] = sys_substr($shouhuo_json['收件人'],1,false).'**';
        $return['data']['shouhuo_info']['phone'] = substr($shouhuo_json['手机号'],0,3).'****'.substr($shouhuo_json['手机号'],-4);
        $return['data']['shouhuo_info']['address'] = $shouhuo_json['所在地区'].$shouhuo_json['详细地址'];
        $ids = '';
        if(!empty($product_json)){
            foreach ($product_json as $item) {
                $ids.=empty($ids)?$item->id:','.$item->id;
                $return['data']['products'][] = $item;
            }
        }
        $return['data']['orderId'] = $order->orderId;
        $return['data']['status'] = $order->status;
        switch ($order->status) {
            case 0:
                $return['data']['status_info'] = '待成团';
                break;
            case 2:
                $return['data']['status_info'] = '待发货';
                break;
            case 3:
                $return['data']['status_info'] = '待收货';
                break;
            case 4:
                $return['data']['status_info'] = '已完成';
                break;
            case -3:
                $return['data']['status_info'] = '退换货';
                break;
            case -5:
                $pay_end = strtotime($order->pay_endtime);
                if($pay_end>$now){
                    $return['data']['status_info'] = '待支付';
                    $dai_pay = 1;
                }else{
                    $return['data']['status_info'] = '无效';
                }
                break;
            case -1:
                $return['data']['status_info'] = '无效';
                $qx_remarks = array('管理员取消订单','订单已退款','订单已取消');
                if(in_array($order->remark,$qx_remarks)){
                    $return['data']['status_info'] = $order->remark;
                }
                break;
        }
        $return['data']['price'] = $order->price;
        $return['data']['price_payed'] = $order->price_payed;
        $return['data']['yhq'] = "0";
        $return['data']['dtTime'] = $order->dtTime;
        $return['data']['peisong_time'] = $order->peisong_time;
        $return['data']['if_share'] = $order->if_share;
        if(!empty($order->price_json)){
            $price_json = json_decode($order->price_json,true);
            if(!empty($price_json['yhq'])){
                $return['data']['yhq'] = $price_json['yhq']['price'];
            }
        }
        if($order->shequ_id>0){
            $tihuoma = self::get_36id($id);
            $peisong_type = '普通快递';
            switch($order->peisong_type){
                case 1:$peisong_type = '站点自提';break;
                case 2:$peisong_type = '社区配送';break;
            }
            $shequ = $db->get_row("select * from demo_shequ where id=$order->shequ_id");
            $tuanzhang = $db->get_row("select nickname,username,phone from users where id=$shequ->userId");
            $return['data']['shequ']['image'] = $shequ->originalPic;
            $return['data']['shequ']['title'] = $shequ->title;
            $return['data']['shequ']['address'] = $shequ->address;
            $return['data']['shequ']['name'] = $shequ->name;
            $return['data']['shequ']['phone'] = $shequ->phone;
            $return['data']['shequ']['peisong_type'] = $peisong_type;
            $return['data']['shequ']['peisong_type_id'] = $order->peisong_type;
            $return['data']['shequ']['peisong_fanwei'] = $shequ->peisong_area;
        }
        $userIds = $db->get_var("select group_concat(userId) from order_detail$fenbiao where inventoryId in($ids)");
        if(empty($userIds))$userIds='0';
        $tuan_users = $db->get_results("select nickname,image from users where id in($userIds) limit 10");
        $return['data']['buyers']= empty($tuan_users)?array():$tuan_users;
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    /*分享回调*/
    public function shareReturn(){
        global $db,$request,$comId;
        $remark = '分享订单';
        $userId = (int)$request['user_id'];
        $orderId = (int)$request['order_id'];
        if($orderId==0){
            return '{"code":0,"message":"order_id不能为0"}';
        }
        $fenbiao = getFenbiao($comId,20);
        $if_share = $db->get_var("select if_share from order$fenbiao where id=$orderId");
        if($if_share==0){
            $db->query("update order$fenbiao set if_share=1 where id=$orderId");
            $jifen_set = $db->get_row("select if_share,share_jifen,share_limit,share_dikoujin,share_limit_dikoujin from user_shezhi where comId=$comId");
            if($jifen_set->if_share==1){
                $fenbiao = getFenbiao($comId,20);
                $count = $db->get_var("select sum(jifen) from user_jifen$fenbiao where userId=$userId and comId=$comId and remark='$remark' and dtTime>='".date("Y-m-d")."'");
                if($count>=$jifen_set->share_limit && $jifen_set->share_limit>0){
                    return '{"code":1,"message":"成功"}';
                }else{
                    $db->query("update users set jifen=jifen+$jifen_set->share_jifen where id=$userId");
                    $yue = $db->get_var("select jifen from users where id=$userId");
                    $jifen_jilu = array();
                    $jifen_jilu['userId'] = $userId;
                    $jifen_jilu['comId'] = $comId;
                    $jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
                    $jifen_jilu['jifen'] = $jifen_set->share_jifen;
                    $jifen_jilu['yue'] = $yue;
                    $jifen_jilu['type'] = 1;
                    $jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
                    $jifen_jilu['remark'] = $remark;
                    $db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
                }
            }
        }
        return '{"code":1,"message":"成功"}';
    }
    //提货码
    function tihuoma(){
        global $db,$request,$comId;
        $orderId = (int)$request['order_id'];
        $tihuoma = self::get_36id($orderId);
        $share_url = 'tihuoma='.$tihuoma.'&order_id='.$orderId;
        $share_file = 'cache/pdts_qrcode/'.$orderId.'.png';
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $return['data']['qrcode'] = 'https://'.$_SERVER['HTTP_HOST'].'/'.$share_file;
        if(!is_file(ABSPATH.$share_file)){
            //echo ABSPATH.'erp/phpqrcode.php';
            require_once(ABSPATH.'shequ/phpqrcode.php');
            \QRcode::png($share_url,$share_file,'L',8);
        }
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    //根据下单商品获取可用的优惠券，参与的促销活动等信息
    public function getPayInfo(){
        global $db,$request,$comId;
        
        $userId = (int)$request['user_id'];
        $address_id = (int)$request['address_id'];
        $yushouId = (int)$request['yushou_id'];
        $tuan_id = (int)$request['tuan_id'];
        $tuan_type = (int)$request['tuan_type'];
        $shequ_id = (int)$request['shequ_id'];
        $peisong_type = (int)$request['peisong_type'];
        if($shequ_id>0 && $peisong_type==0)$peisong_type=2;
        $content = $request['product_info'];
        $peisong_time = $request['peisong_time'];
        $content = str_replace('\\"','"',$content);
        $content = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$content));
        /*if(empty($address_id)){
			return '{"code":0,"message":"收货地址address_id不能为空"}';
		}*/
        if(!empty($content))$gouwuche=json_decode($content,true);
        if(empty($gouwuche)){
            return '{"code":0,"message":"未检测到商品信息"}';
        }
        if(!empty($yushouId) && count($gouwuche)!=1){
            return '{"code":0,"message":"预售商品只能单独下单，不能跟其他的商品一起下单"}';
        }
        $user_level = $db->get_var("select level from users where id=$userId");
        if($tuan_id>0){
            $tuan = $db->get_row("select * from demo_tuan where id=$tuan_id");
            $tuan_type = (int)$tuan->type;
            switch ($tuan->status) {
                case 0:
                    $pay_end = strtotime($tuan->endTime);
                    if($pay_end>$now){
                        $status_info = '待成团';
                        $dai_chengtuan = 1;
                    }else{
                        $status_info = '拼团失败';
                    }
                    break;
                case 1:
                    $status_info = '拼团成功';
                    break;
                case -1:
                    $status_info = '拼团失败';
                    break;
            }
            if($dai_chengtuan!=1){
                return '{"code":0,"message":"该团购已结束"}';
            }
            if($tuan->type==2){
                $address_id = $tuan->addressId;
            }
        }
        if(!empty($address_id)){
            $address = $db->get_row("select * from user_address where id=$address_id");
        }
        $areaId = (int)$address->areaId;
        $nowProductId = 0;
        $shuliang = 0;
        $num = 0;
        $zong_price = 0;
        $kedi = 0;
        $pdtstr = '';
        
        foreach ($gouwuche as $i=>$g) {
            $nowProductId = $g['productId'];
            $inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,weight,image,status,comId,price_card,price_tuan,price_shequ_tuan from demo_product_inventory where id=".$g['inventoryId']);
            if($yushouId>0){
                
            }else{
                if($inventory->status!=1){
                    return '{"code":0,"message":"'.$inventory->title.'['.$inventory->key_vals.']产品已下架"}';
                }
                $kucun = \Zhishang\Product::get_product_kucun($g['inventoryId'],$areaId);
                if($g['num']>$kucun){
                    return '{"code":0,"message":"'.$inventory->title.'['.$inventory->key_vals.']产品库存不足"}';
                }
                $pro = $db->get_row("select yunfei_moban,yunfei_moban_ding from demo_product where id=".$g['productId']);
                $yunfei_moban = (int)$pro->yunfei_moban;
                if($tuan_type==1){
                    $price = $inventory->price_tuan;
                }else if($tuan_type==2){
                    $price = $inventory->price_shequ_tuan;
                }else{
                    $price = \Zhishang\Product::get_user_zhekou($g['inventoryId'],$inventory->price_sale,$user_level);
                }
                $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'","comId":'.$inventory->comId.'}}';
                $shuliang++;
                $num+=$g['num'];
                $zong_price+=$price*$g['num'];
                $kedi += $inventory->price_card*$g['num'];
                if($pro->yunfei_moban_ding>0){
                    $tihuo_time = date("m月d日 ",strtotime('+'.$pro->yunfei_moban_ding.' day')).$tihuo_info->tihuo_time;
                }
            }
        }
        if(!empty($pdtstr)){
            $pdtstr = substr($pdtstr,1);
            $pdt_arr = json_decode('['.$pdtstr.']');
        }
        if($comId==10){
            $max_kedi = empty($gift_cards)?0:$gift_cards[0]->yue;
            $kedi = $kedi>$max_kedi?$max_kedi:$kedi;
            $kedi = getXiaoshu($kedi,2);
            $zong_price-=$kedi;
            $zong_price = getXiaoshu($zong_price,2);
        }else{
            $kedi = 0;
        }
        //获取商品促销信息和订单促销信息
        $cuxiao_title = '';
        $zengpin = '';
        //if($areaId>0){
        $pdt_cuxiao = self::get_pdt_cuxiao($pdt_arr,$areaId,$user_level,$zong_price);
        if($pdt_cuxiao['jian']>0){
            $zong_price-=$pdt_cuxiao['jian'];
        }
        if(!empty($pdt_cuxiao['cuxiao_title'])){
            $cuxiao_title = $pdt_cuxiao['cuxiao_title'];
        }
        if(!empty($pdt_cuxiao['zengpin'])){
            foreach ($pdt_cuxiao['zengpin'] as $pdt) {
                $title= $db->get_var("select title from demo_product_inventory where id=".$pdt['id']);
                $zengpin.=','.$title.' * '.$pdt['num'];
            }
        }
        //订单促销
        $order_cuxiao = self::get_order_cuxiao($zong_price,$areaId,$user_level);
        if($order_cuxiao['jian']>0){
            $zong_price-=$order_cuxiao['jian'];
        }
        if(!empty($order_cuxiao['cuxiao_title'])){
            $cuxiao_title .= empty($cuxiao_title)?$order_cuxiao['cuxiao_title']:','.$order_cuxiao['cuxiao_title'];
        }
        if(!empty($order_cuxiao['zengpin'])){
            foreach ($order_cuxiao['zengpin'] as $pdt) {
                $title= $db->get_var("select title from demo_product_inventory where id=".$pdt['id']);
                $zengpin.=','.$title.' * '.$pdt['num'];
            }
        }
        //获取运费
        $peisong_rule = '';
        $shezhi = $db->get_row("select time_pay,storeId,user_bili,shangji_bili,fanli_type,time_tuan,shequ_yunfei,peisong_time_money,tihuo_info from demo_shezhi where comId=$comId");
        $peisong_time_money = array();
        if(!empty($shezhi->peisong_time_money)){
            $peisong_time_money = json_decode($shezhi->peisong_time_money,true);
        }
        $shequ_yunfei = json_decode($shezhi->shequ_yunfei);
        $peisongfei = isset($peisong_time_money[$peisong_time]['peisong_money'])?$peisong_time_money[$peisong_time]['peisong_money']:$shequ_yunfei->peisong_money;
        $peisongfei_man = isset($peisong_time_money[$peisong_time]['peisong_man'])?$peisong_time_money[$peisong_time]['peisong_man']:$shequ_yunfei->peisong_man;

        if($shequ_id>0){
            $yunfei = 0;
            if($peisong_type==2 && !empty($shezhi->shequ_yunfei)){
                //	$shequ_yunfei = json_decode($shezhi->shequ_yunfei);
                $yunfei = $peisongfei;
                if($peisongfei_man>0 && $zong_price>=$peisongfei_man){
                    $yunfei = 0;
                }
                $peisong_rule = ($peisongfei_man>0 && $peisongfei>0)?'满'.$peisongfei_man.'免配送费':'';
            }else if($peisong_type==1 && !empty($shezhi->shequ_yunfei)){
                $shequ_yunfei = json_decode($shezhi->shequ_yunfei);
                if($zong_price<$shequ_yunfei->peisong_qisong1){
                    //return '{"code":0,"message":"订单满'.$shequ_yunfei->peisong_qisong.'才能下单~~~"}';
                }
            }

        }else{
            //	$yunfei = get_yunfei($pdt_arr,$zong_price,$areaId);

            $yunfei = $peisongfei;
            if($zong_price >= $peisongfei_man && !empty($peisongfei_man)){
                $yunfei = 0;
            }
        }
        $yunfei = 0;
        
        //获取优惠券
        if($comId==1137){
            //每天只能用2张
            $fenbiao = getFenbiao($comId,20);
            $has_used_num = $db->get_var("select count(*) from order$fenbiao where comId=$comId and userId=$userId and dtTime like '".date("Y-m-d")."%' and status!=-1 and price_json like '%yhq%'");
            if($has_used_num>=2){
                $yhqs = array();
            }else{
                $yhqs = self::get_yhqs($pdt_arr,$zong_price);
            }
        }else{
            $yhqs = self::get_yhqs($pdt_arr,$zong_price);
        }
        /*if(!empty($yhqs)&&$_SESSION['if_tongbu']!=1){
	            $zong_price-=$yhqs[0]['jian'];
	        }*/
        if(!empty($yunfei)){
            $zong_price+=$yunfei;
        }
        //}
        if(!empty($zengpin))$zengpin=substr($zengpin,1);
        $jifen = self::get_order_jifen($pdt_arr,$zong_price);
        $zong_price = getXiaoshu($zong_price,2);
        $return = array();
        $return['code'] = 1;
        $return['message'] = '';
        $return['data'] = array();
        $return['data']['yunfei'] = empty($yunfei)?0:$yunfei;
        $return['data']['jifen'] = $jifen;
        $return['data']['cuxiao_info'] = $cuxiao_title;
        $return['data']['gift_info'] = $zengpin;
        $return['data']['yhq_list'] = empty($yhqs)?array():$yhqs;
        $return['data']['order_price'] = $zong_price;
        $return['data']['money'] = $db->get_var("select money from users where id = $userId");
        $return['data']['peisong_rule'] = $peisong_rule;
        $return['data']['jifen_pay'] = array("if_open"=>0,"jifen"=>0,"money"=>0);
        $daizhifu = $zong_price;
        $jifen_pay = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
        $user_jifen = (int)$db->get_var("select jifen from users where id=$userId");

        if($jifen_pay->if_jifen_pay==1 && !empty($jifen_pay->jifen_pay_rule) && !empty($user_jifen)){
            $jifen_rule = json_decode($jifen_pay->jifen_pay_rule);
            $jifen_rule->jifen = (int)$jifen_rule->jifen;
           
            if($daizhifu>=$jifen_rule->man){
                if($jifen_rule->if_bili==1 && !empty($jifen_rule->bili)){
                    $max_money = (int)($daizhifu*$jifen_rule->bili*100)/10000;
                    $max_money = bcadd($max_money, 0, 2);
                }else{
                    $max_money = $daizhifu;
                }
                if($jifen_rule->if_shangxian==1 && $max_money>$jifen_rule->shangxian){
                    $max_money = $jifen_rule->shangxian;
         
                }
                $need_jifen = bcmul($max_money, $jifen_rule->jifen, 0);
                if($need_jifen>$user_jifen){
                    $max_money = bcdiv($user_jifen, $jifen_rule->jifen, 2);
                }
                if($max_money>$daizhifu){
                    $max_money = $daizhifu;
                }
                
                if($max_money>0){
                    $need_jifen = bcmul($max_money,$jifen_rule->jifen, 0);
                    $return['data']['jifen_pay']['if_open'] = 1;
                    $return['data']['jifen_pay']['jifen'] = $need_jifen;
                    $return['data']['jifen_pay']['money'] = $max_money;
                    $return['data']['jifen_pay']['rule'] = $jifen_rule->jifen;
                    
                }
            }
        }
       
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
    //创建订单
    public function create(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $order_fenbiao = $fenbiao = getFenbiao($comId,20);
        $shequ_id = (int)$request['shequ_id'];
        $shezhi = $db->get_row("select time_pay,storeId,user_bili,shangji_price,shangshangji_price,shangshangji_jt_price,fanli_type,time_tuan,shequ_yunfei,peisong_time_money,shang_bili,tihuo_info from demo_shezhi where comId=$comId");
        $time_pay = $shezhi->time_pay;
        $time_pay+=1;
        $user_level = (int)$db->get_var("select level from users where id=$userId");
        $address_id = (int)$request['address_id'];
        if(!empty($request['shouhuo_name']) && !empty($request['shouhuo_phone'])){
            $name = $request['shouhuo_name'];
            $phone = $request['shouhuo_phone'];
            $address_id = (int)$db->get_var("select id from user_address where userId=$userId order by moren desc,id desc limit 1");
            $uaddress = array();
            $uaddress['id'] = $address_id;
            $uaddress['comId'] = $comId;
            $uaddress['userId'] = $userId;
            $uaddress['name'] = $name;
            $uaddress['phone'] = $phone;
            $address_id = $db->insert_update('user_address',$uaddress,'id');
        }
        $address = $db->get_row("select * from user_address where id=$address_id");
        $areaId = (int)$address->areaId;
        $yhq_id = (int)$request['yhq_id'];
        $yushouId = (int)$request['yushou_id'];
        $tuan_id = (int)$request['tuan_id'];
        $tuan_type = (int)$request['tuan_type'];
        $content = $request['product_info'];
        $remark = $request['remark'];
        $peisong_type = (int)$request['peisong_type'];
        if($peisong_type==1 && $shequ_id==0){
            return '{"code":0,"message":"自提需要先选择社区！"}';
        }
        $peisong_time = $request['peisong_time'];

        $content = str_replace('\\"','"',$content);
        $content = trim(preg_replace('/((\s)*(\n)+(\s)*)/','',$content));
        if(!empty($content))$gouwuche=json_decode($content,true);
        if(empty($gouwuche)){
            return '{"code":0,"message":"未检测到商品信息"}';
        }
        if(!empty($yushouId) && count($gouwuche)!=1){
            return '{"code":0,"message":"预售商品只能单独下单，不能跟其他的商品一起下单"}';
        }
        if($tuan_id>0){
            $tuan = $db->get_row("select * from demo_tuan where id=$tuan_id");
            $tuan_type = (int)$tuan->type;
            switch ($tuan->status) {
                case 0:
                    $pay_end = strtotime($tuan->endTime);
                    if($pay_end>$now){
                        $status_info = '待成团';
                        $dai_chengtuan = 1;
                    }else{
                        $status_info = '拼团失败';
                    }
                    break;
                case 1:
                    $status_info = '拼团成功';
                    break;
                case -1:
                    $status_info = '拼团失败';
                    break;
            }
            if($dai_chengtuan!=1){
                return '{"code":0,"message":"该团购已结束"}';
            }
            if($tuan->type==2){
                $address_id = $tuan->addressId;
            }
        }
        $check_pay_time = strtotime("+$time_pay minutes");
        $num = 0;
        $zong_price = 0;
        $zong_gonghuo_price = 0;
        $zong_weight = 0;
        $pdtstr = '';
        $product_json_arry = array();
        $has_ids = array();
        if($peisong_type==3){
            $shequ_id = 0;
        }
        $shequ_user_id = 0;
        if($shequ_id>0){
            $shequ_user_id = $db->get_var("select userId from demo_shequ where id=$shequ_id");
        }
      
        //返利信息
        $fanli_json = array('shangji' =>0,'shangji_fanli' =>0,'shangshangji' =>0,'shangshangji_fanli' =>0,'tuijian' =>0,'tuijian_fanli' =>0,'shop_fanli' =>0,'pingtai_fanli' =>0,'shequ_fanli'=>0,"shequ_id"=>$shequ_user_id,"buyer_fanli"=>0);
        $shop = $db->get_row("select tuijianren,tuijian_bili,pay_info,pingtai_fanli from demo_shops where comId=$comId");
        if($tuan_type>0){
            if($tuan_id>0){
                $tuanzhang = (int)$db->get_var("select tuanzhang from demo_tuan where id=$tuan_id");
                $fanli_json['shangji'] = $tuanzhang;
               
                $u = $db->get_row("select shangji,shangshangji,tuan_id from users where id=$tuanzhang");
                $fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangji;
   
            }else{
                $fanli_json['shangji'] = (int)$userId;
              
                $u = $db->get_row("select shangji,shangshangji,tuan_id from users where id=".$userId);
                $fanli_json['shangshangji'] = $shezhi->fanli_type==2?$u->tuan_id:$u->shangji;
                
            }
        }else{
            $u = $db->get_row("select shangji,shangshangji,tuan_id from users where id=".$userId);
            $fanli_json['shangji'] = $u->shangji;
            $fanli_json['shangshangji'] = $u->shangshangji;//根据返利类型设定返利的上上级会员
        }
        $fanli_json['tuijian'] = $shop->tuijianren;
        //计算社区返利和团长返利
        $fanli_shequ =0;$fanli_tuanzhang = 0;
        foreach ($gouwuche as $i=>$g) {
            $has_ids[] = $g['inventoryId'];
            $nowProductId = $g['productId'];
            $inventory = $db->get_row("select id,productId,channelId,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost,fanli_shequ,fanli_tuanzhang,price_tuan,price_shequ_tuan,tuan_num from demo_product_inventory where id=".$g['inventoryId']);
            
            $shequ_id = $db->get_var("select mendianId from demo_product where id = $inventory->productId");
            if($tuan_type>0){
                $tuan_inventory = $inventory;
            }
            $order_comId = $inventory->comId;
            if(!empty($yushouId)){
                
            }else{
                if($inventory->status!=1){
                    return ('{"code":0,"message":"商品“'.$inventory->title.'”已下架"}');
                }
                $kucun = \Zhishang\Product::get_product_kucun($g['inventoryId'],$areaId);
                if($g['num']>$kucun)$g['num']=$kucun;
                if($kucun<=0){
                    return ('{"code":0,"message":"商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”库存不足"}');
                }
                if($tuan_type==1){
                    $price = $inventory->price_tuan;
                }else if($tuan_type==2){
                    $price = $inventory->price_shequ_tuan;
                }else{
                    $price = \Zhishang\Product::get_user_zhekou($g['inventoryId'],$inventory->price_sale,$user_level);
                }
                $zong_price+=$price*$g['num'];
            }
            $zong_gonghuo_price+=$inventory->price_cost*$g['num'];
            $pro = $db->get_row("select yunfei_moban,yunfei_moban_ding,skuId from demo_product where id=".$g['productId']);
            $yunfei_moban = (int)$pro->yunfei_moban;
            $pdtstr.=',{"'.$g['inventoryId'].'":{"productId":'.$g['productId'].',"yunfei_moban":'.$yunfei_moban.',"num":"'.$g['num'].'","weight":"'.$inventory->weight.'","price":"'.$price.'"}}';
            $num+=(int)$g['num'];

            $zong_weight+=$inventory->weight*$g['num'];
            $pdt = array();
            $pdt['id'] = $inventory->id;
            $pdt['productId'] = $g['productId'];
            $pdt['title'] = $inventory->title;
            $pdt['sn'] = $inventory->sn;
            $pdt['key_vals'] = $inventory->key_vals;
            $pdt['weight'] = $inventory->weight;
            $pdt['num'] = $g['num'];
            $pdt['image'] = ispic($inventory->image);
            
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
    		    $pdt['default_img'] ="https://admin.bio-swamp.com/upload/抗体.jpg"; 
    		    $pdt['img'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/product/$root_id/$pro->skuId/$pro->skuId".'_1.jpg'; 
    		    $pdt['img'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$pro->skuId/$pro->skuId".'_1.jpg'; 
    		}else{
    		    $pdt['img'] =$originalPic_;
    		    $pdt['img'] ="https://bio-swamp.oss-cn-nanjing.aliyuncs.com/img/$pro->skuId/$pro->skuId".'_1.jpg'; 
    		    $pdt['default_img'] =$originalPic_;
    		}	   
            
            $pdt['price_sale'] = $price;
            $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
            $pdt['price_card'] = $inventory->price_card;
            $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
            $units_arr = json_decode($units);
            $pdt['unit'] = $units_arr[0]->title;
            $product_json_arry[] = $pdt;
            if(!empty($yushouId)){
                break;
            }
            $fanli_shequ +=$inventory->fanli_shequ*$g['num'];
            $fanli_tuanzhang +=$inventory->fanli_tuanzhang*$g['num'];
            if($pro->yunfei_moban_ding>0){
                $peisong_time = date("Y-m-d ",strtotime('+'.$pro->yunfei_moban_ding.' day')).$tihuo_info->tihuo_time;
            }
        }

        //价格相关
        $price_json = new \StdClass();
        $price_json_product = new \StdClass();
        $order_total_price =  $price_json_product->price = $zong_price;
        $price_json_product->desc = '';
        $price_json->goods = $price_json_product;
        if(!empty($pdtstr)){
            $pdtstr = substr($pdtstr,1);
            $pdt_arr = json_decode('['.$pdtstr.']');
        }
        $cuxiao_money = $zong_price;
        if(!empty($lpk_id) && !empty($lpk_kedi)){
            $cuxiao_money-=$lpk_kedi;
        }
        //限购判断
        $xiangou_sql = "insert into cuxiao_pdt_buy(cuxiao_id,inventoryId,userId,num,comId,orderId) values";
        $xiangou_sql1 = '';
        $pdt_cuxiao = self::get_pdt_cuxiao($pdt_arr,$areaId,$user_level,$zong_price);
        if(!empty($pdt_cuxiao['cuxiao_ids'])){
            foreach ($pdt_cuxiao['cuxiao_ids'] as $key => $cuxiaoId) {
                $cuxiao_pdtIds = $db->get_var("select pdtIds from cuxiao_pdt where id=$cuxiaoId");
                $pdtArr = explode(',',$cuxiao_pdtIds);
                foreach ($gouwuche as $i=>$g) {
                    $inventId = $g['inventoryId'];
                    $num = (int)$g['num'];
                    if(in_array($inventId,$pdtArr)){
                        $buy_num = (int)$db->get_var("select num from cuxiao_pdt_buy where cuxiao_id=$cuxiaoId and inventoryId=$inventId and userId=$userId");
                        $xiangou_num = (int)$db->get_var("select num from cuxiao_pdt_xiangou where cuxiao_id=$cuxiaoId and inventoryId=$inventId");
                        if($xiangou_num>0 && ($buy_num+$num)>$xiangou_num){
                            $inventory = $db->get_row("select id,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card from demo_product_inventory where id=$inventId");
                            return '{"code":0,"message":"下单失败，商品“'.$inventory->title.'【'.$inventory->key_vals.'】'.'”限购'.$xiangou_num.'份！您还可购买'.($xiangou_num-$buy_num).'份"}';
                        }else{
                            if($buy_num>0){
                                $db->query("update cuxiao_pdt_buy set num=num+$num where cuxiao_id=$cuxiaoId and inventoryId=$inventId and userId=$userId");
                            }else{
                                $xiangou_sql1.=",($cuxiaoId,$inventId,$userId,$num,$comId,order_id)";
                            }
                        }
                    }
                }
            }
        }

        if($pdt_cuxiao['jian']>0){
            $zong_price-=$pdt_cuxiao['jian'];
            $price_json_cuxiao = new \StdClass();
            $price_json_cuxiao->price = $pdt_cuxiao['jian'];
            $price_json_cuxiao->desc = $pdt_cuxiao['cuxiao_title'];
            $price_json->cuxiao = $price_json_cuxiao;
        }
        //订单促销
        $order_cuxiao = self::get_order_cuxiao($zong_price,$areaId,$user_level);
        if($order_cuxiao['jian']>0){
            $zong_price-=$order_cuxiao['jian'];
            $price_json_order = new \StdClass();
            $price_json_order->price = $order_cuxiao['jian'];
            $price_json_order->desc = $order_cuxiao['cuxiao_title'];
            $price_json->cuxiao_order = $price_json_order;
        }
        //返利相关
        $zongfanli = $zong_gonghuo_price;//商家返利


        // $fanli_json['shequ_fanli'] = $fanli_shequ;
        $fanli_json['user_type'] = 2;
        $user_fanli = $fanli_tuanzhang;
        $shezhi->shangji_bili = $shezhi->shang_bili = 0;
        $fanli_json['shangshangji_fanli'] = intval($user_fanli * $shezhi->shangji_bili)/100;
        $fanli_json['buyer_fanli'] = intval($user_fanli * $shezhi->shang_bili)/100;
        if(!empty($fanli_json['shangji'])){
            $fanli_json['shangji_fanli'] = $user_fanli-$fanli_json['shangshangji_fanli']-$fanli_json['buyer_fanli'];
        }
        if(empty($fanli_json['shangshangji'])){
            $fanli_json['shangshangji_fanli'] = 0;
        }
        
        //获取运费
        if($shequ_id>0){
            $yunfei = 0;
            if($peisong_type==2 && !empty($shezhi->shequ_yunfei)){
                $shequ_yunfei = json_decode($shezhi->shequ_yunfei);
                $peisong_time_money = array();
                if(!empty($shezhi->peisong_time_money)){
                    $peisong_time_money = json_decode($shezhi->peisong_time_money,true);
                }
                $peisongfei = isset($peisong_time_money[$peisong_time]['peisong_money'])?$peisong_time_money[$peisong_time]['peisong_money']:$shequ_yunfei->peisong_money;
                $peisongfei_man = isset($peisong_time_money[$peisong_time]['peisong_man'])?$peisong_time_money[$peisong_time]['peisong_man']:$shequ_yunfei->peisong_man;
                $yunfei = $peisongfei;
                if($peisongfei_man>0 && $zong_price>=$peisongfei_man){
                    $yunfei = 0;
                }
            }
        }else{
            $yunfei = get_yunfei($pdt_arr,$zong_price,$areaId);
        }
        $yunfei = 0;
        //获取优惠券
        $now = date("Y-m-d H:i:s");
        $yhq = $db->get_row("select id,yhqId,title,man,jian,jiluId from user_yhq$fenbiao where id=$yhq_id and userId=$userId and status=0 and endTime>='$now' and startTime<='$now'");
        
        if($yhq_id > 0 && !$yhq){
            return ('{"code":0,"message":"优惠券不可用"}');
        }
        
        if(!empty($yhq)){
            $zong_price-=$yhq->jian;
            $price_json_yhq = new \StdClass();
            $yhq_price = $price_json_yhq->price = $yhq->jian;
            $price_json_yhq->desc = $yhq->id;
            $price_json->yhq = $price_json_yhq;
            if($zong_price<0)$zong_price=0;
        }
        if(!empty($yunfei)){
            $zong_price+=$yunfei;
            $price_json_yunfei = new \StdClass();
            $price_json_yunfei->price = $yunfei;
            $price_json_yunfei->desc = '';
            $price_json->yunfei = $price_json_yunfei;
        }
        if(!empty($pdt_cuxiao['zengpin'])){
            foreach ($pdt_cuxiao['zengpin'] as $zeng) {
                $inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$zeng['id']);
                $pdt = array();
                $pdt['id'] = $inventory->id;
                $pdt['productId'] = $inventory->productId;
                $pdt['title'] = $inventory->title;
                $pdt['sn'] = $inventory->sn;
                $pdt['key_vals'] = $inventory->key_vals;
                $pdt['weight'] = $inventory->weight;
                $pdt['num'] = $zeng['num'];
                $pdt['price_sale'] = 0;
                $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
                $pdt['price_card'] = 0;
                $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
                $units_arr = json_decode($units);
                $pdt['unit'] = $units_arr[0]->title;
                $product_json_arry[] = $pdt;
                $num+=$zeng['num'];
                $zong_weight+=$inventory->weight*$zeng['num'];
            }
        }
        if(!empty($order_cuxiao['zengpin'])){
            foreach ($order_cuxiao['zengpin'] as $zeng) {
                $inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,weight,image,status from demo_product_inventory where id=".$zeng['id']);
                $pdt = array();
                $pdt['id'] = $inventory->id;
                $pdt['productId'] = $inventory->productId;
                $pdt['title'] = $inventory->title;
                $pdt['sn'] = $inventory->sn;
                $pdt['key_vals'] = $inventory->key_vals;
                $pdt['weight'] = $inventory->weight;
                $pdt['num'] = $zeng['num'];
                $pdt['price_sale'] = 0;
                $pdt['price_market'] = getXiaoshu($inventory->price_market,2);
                $pdt['price_card'] = 0;
                $units = $db->get_var("select untis from demo_product where id=".$g['productId']);
                $units_arr = json_decode($units);
                $pdt['unit'] = $units_arr[0]->title;
                $product_json_arry[] = $pdt;
                $num+=$zeng['num'];
                $zong_weight+=$inventory->weight*$zeng['num'];
            }
        }
        $product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
        $jifen = self::get_order_jifen($pdt_arr,$zong_price);
        $storeId = \Zhishang\Product::get_fahuo_store($areaId);
        $shouhuo_json = array();
        if(!empty($address)){
            $shouhuo_json['收件人'] = $address->name;
            $shouhuo_json['手机号'] = $address->phone;
            $shouhuo_json['所在地区'] = $address->areaName;
            $shouhuo_json['详细地址'] = $address->address;
        }else if(!empty($shequ_id) && $tuan_type==2){
            $shequ = $db->get_row("select * from demo_shequ where id=$shequ_id");
            $shouhuo_json['收件人'] = $shequ->name;
            $shouhuo_json['手机号'] = $shequ->phone;
            $shouhuo_json['所在地区'] = $db->get_var("select title from demo_area where id=$shequ->areaId");
            $shouhuo_json['详细地址'] = $shequ->address;
        }
        if($tuan_type>0 && $tuan_id==0){
            $tuan = array();
            $tuan['comId'] = $comId;
            $tuan['inventoryId'] = $tuan_inventory->id;
            $tuan['productId'] = $tuan_inventory->productId;
            $tuan['type'] = $tuan_type;
            $tuan['pdt_comId'] = $tuan_inventory->comId;
            $tuan['user_num'] = $tuan_inventory->tuan_num;
            $tuan['tuanzhang'] = (int)$userId;
            $tuan['addressId'] = $address_id;
            $tuan['dtTime'] = date("Y-m-d H:i:s");
            $tuan['endTime'] = date("Y-m-d H:i:s",strtotime('+ '.$shezhi->time_tuan.' hours'));
            $tuan['shouhuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
            $tuan_id = $db->insert_update('demo_tuan',$tuan,'id');
            $time_tuan = $shezhi->time_tuan;
            $check_tuan_time = strtotime("+$time_tuan hours");
            $timed_task = array();
            $timed_task['comId'] = $comId;
            $timed_task['dtTime'] = $check_tuan_time;
            $timed_task['router'] = 'order_checkTuan';
            $timed_task['params'] = '{"tuan_id":'.$tuan_id.'}';
            $db->insert_update('demo_timed_task',$timed_task,'id');
        }
        
//        //=======================设置返利金额=================================
//        if(!empty($fanli_json['shangji'])){
//            $shangjiBili = bcdiv($shezhi->shangji_bili, 100, 2);
//            $fanli_json['shangji_fanli'] = bcmul($zong_price, $shangjiBili, 2);
//        }
//        if(!empty($fanli_json['shangshangji'])){
//            $shangshangjiBili = bcdiv($shezhi->shangshangji_bili, 100, 2);
//            $fanli_json['shangshangji_fanli'] = bcmul($zong_price, $shangshangjiBili, 2);
//        }
        //=====================================================================
        
        $order = array();
        $order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
        $order['userId'] = $userId;
      
        $order['comId'] = (int)$order_comId;
        $order['mendianId'] = $shequ_id;
        $order['yushouId'] = $yushouId;
        $order['type'] = $tuan_type>0?$tuan_type:1; //2社区团 1普通订单或普通团单
        $order['status'] = -5;//待支付
        $order['dtTime'] = date("Y-m-d H:i:s");
        $order['remark'] = $remark;
        $order['pay_endtime'] = date("Y-m-d H:i:s",$check_pay_time);
        $order['price'] = $zong_price;
        if($yushou->paytype==2){
            $order['price_dingjin'] = $yushou->dingjin;
        }
        $order['inventoryId'] = (int)$tuan_inventory->id;
        $order['storeId'] = $storeId;
        $order['pdtNums'] = $num;
        $order['pdtChanel'] = 0;
        $order['ifkaipiao'] = 0;
        $order['weight'] = $zong_weight;
        $order['jifen'] = $jifen;
        $order['areaId'] = $areaId;
        $order['address_id'] = $address_id;
        $order['product_json'] = $product_json;
        $order['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
        $order['price_json'] = json_encode($price_json,JSON_UNESCAPED_UNICODE);
        $order['fanli_json'] = json_encode($fanli_json,JSON_UNESCAPED_UNICODE);
        $order['shangji'] = $fanli_json['shangji'];
        $order['shangshangji'] = $fanli_json['shangshangji'];
        $order['ifkaipiao'] = (int)$request['if_fapiao'];
        if($request['if_fapiao']>0){
            if($request['fapiao_id']==0){
                $fapiao = array();
                $fapiao['userId'] = $userId;
                $fapiao['comId'] = $comId;
                $fapiao['type'] = $request['fapiao_type'];
                
                $fapiao['com_title'] = trim($request['fapiao_com_title']);
                $fapiao['shibiema'] = trim($request['fapiao_shibiema']);
                $fapiao['shoupiao_phone'] = trim($request['shoupiao_phone']);
                $fapiao['shoupiao_email'] = trim($request['shoupiao_email']);
                $fapiao['address'] = $request['fapiao_address'];
                $fapiao['phone'] = $request['fapiao_phone'];
                $fapiao['bank_name'] = $request['fapiao_bank_name'];
                $fapiao['bank_card'] = $request['fapiao_bank_card'];
                
                $db->insert_update('user_fapiao',$fapiao,'id');
            }else{
                $fapiaoRow = $db->get_row("select * from user_fapiao where id = ".$request['fapiao_id']);
                $request['fapiao_type'] = $fapiaoRow->type;
                $request['fapiao_com_title'] = $fapiaoRow->com_title;
                $request['shibiema'] = $fapiaoRow->shibiema;
                $request['address'] = $fapiaoRow->address;
                $request['fapiao_bank_name'] = $fapiaoRow->bank_name;
                $request['fapiao_bank_card'] = $fapiaoRow->bank_card;
                $request['shoupiao_phone'] = $fapiaoRow->shoupiao_phone;
                $request['shoupiao_email'] = $fapiaoRow->shoupiao_email;
            }
            $fapiao_json = array();
            $fapiao_json['发票类型'] = $request['fapiao_leixing'];
            $fapiao_json['抬头类型'] = $request['fapiao_type']==1?'个人':'公司';
            if($request['fapiao_type']==2){
                $fapiao_json['公司名称'] = $request['fapiao_com_title'];
                $fapiao_json['识别码'] = $request['fapiao_shibiema'];
                $fapiao_json['注册地址'] = $request['fapiao_address'];
                $fapiao_json['注册电话'] = $request['fapiao_phone'];
                $fapiao_json['开户银行'] = $request['fapiao_bank_name'];
                $fapiao_json['银行账号'] = $request['fapiao_bank_card'];
            }
            $fapiao_json['发票明细'] = $request['fapiao_cont'];
            $fapiao_json['收票人手机'] = $request['shoupiao_phone'];
            $fapiao_json['收票人邮箱'] = $request['shoupiao_email'];
            
            $order['fapiao_json'] = json_encode($fapiao_json,JSON_UNESCAPED_UNICODE);
        }
       
        $order['if_zong'] = 0;
        $order['shequ_id'] = $shequ_id;
        $order['peisong_type'] = $peisong_type;
        $order['peisong_time'] = $peisong_time;
        $order['tuan_id'] = $tuan_id;
        
        $order_fenbiao = getFenbiao($order_comId,20);
        //file_put_contents('request.txt',$fenbiao.$order_fenbiao.json_encode($order,JSON_UNESCAPED_UNICODE));
        $db->insert_update('order'.$order_fenbiao,$order,'id');
        $order_id = $db->get_var("select last_insert_id();");
        
        if(!$order_id){
            return '{"code":0,"message":"订单创建失败"}';
        }
        
        if(!empty($xiangou_sql1)){
            $xiangou_sql1 = str_replace('order_id', $order_id, $xiangou_sql1);
            $xiangou_sql1 = substr($xiangou_sql1,1);
            $db->query($xiangou_sql.$xiangou_sql1);
        }
        $timed_task = array();
        $timed_task['comId'] = (int)$comId;
        $timed_task['dtTime'] = $check_pay_time;
        $timed_task['router'] = 'order_checkPay';
        $timed_task['params'] = '{"order_id":'.$order_id.'}';
        $db->insert_update('demo_timed_task',$timed_task,'id');
        if(!empty($yhq_id)){
            $db->query("update user_yhq$fenbiao set status=1,orderId=$order_id where id=$yhq_id");
        }
        $contents = $db->get_row("select content,content1 from demo_gouwuche where userId=$userId and comId=$comId limit 1");
        $gouwuches = array();
        if(!empty($contents->content)){
            $gouwuches = json_decode($contents->content,true);
        }
        if(!empty($yhq_price)){ //计算优惠劵推荐金额
             $product_json_arry = $this->getYhqPrice($product_json_arry,$yhq_price,$order_total_price);    
        } 
        foreach ($product_json_arry as $detail) {
            $pdt = new \StdClass();
            $pdt->sn = $detail['sn'];
            $pdt->title = $detail['title'];
            $pdt->key_vals = $detail['key_vals'];
            $pdt->price_sale = $detail['price_sale'];
            $pdt->price_market = $detail['price_market'];
            
            $order_detail = array();
            $order_detail['comId'] = (int)$order_comId;
            $order_detail['mendianId'] = $shequ_id;
            $order_detail['userId'] = $userId;
            $order_detail['orderId'] = $order_id;
            $order_detail['inventoryId'] = $detail['id'];
            $order_detail['productId'] = $detail['productId'];
            $order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
            $order_detail['num'] = $detail['num'];
            $order_detail['unit'] = $detail['unit'];
            $order_detail['unit_price'] = $detail['price_sale'];
            $order_detail['price'] = $detail['price_sale']*$detail['num'];  //总金额
            $order_detail['refund_price'] = !empty($detail['refund_price'])?$detail['refund_price']: $detail['price_sale']*$detail['num'];  //退款金额
            $order_detail['yh_bili'] = $detail['yh_bili'] ? $detail['yh_bili'] : 0;  //优惠比例
            $order_detail['yh_price'] = $detail['yh_price'] ? $detail['yh_price'] : 0;  //优惠金额
            $order_detail['dtTime'] = date('Y-m-d H:i:s');
            

            $db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
            if(!empty($gouwuches[$detail['id']]))unset($gouwuches[$detail['id']]);
            // if($tuan_type==0){
            //     $db->query("update demo_kucun set yugouNum=yugouNum+".$detail['num']." where inventoryId=".$detail['id']." and storeId=$storeId limit 1");
            // }
        }
        
        //减库存
        deductKuCun($order['product_json']); 
        
        $lpk_id = (int)$request['lpk_id'];
        $lpk_kedi = $request['lpk_kedi'];
        if(!empty($lpk_id) && !empty($lpk_kedi)){
            card_pay($order_id,$comId,$lpk_id,$lpk_kedi);
        }
        
        $content = '';
        if(!empty($gouwuches)){
            $content=json_encode($gouwuches,JSON_UNESCAPED_UNICODE);
        }
        
        $db->query("update demo_gouwuche set content='$content' where userId=$userId and comId=$comId limit 1");
        
        return '{"code":1,"message":"下单成功","order_id":'.$order_id.',"comId":'.$order_comId.'}';
    }
    
    public function getYhqPrice($product_json_arry,$yhq_price,$order_price){
        //优惠券计算优惠金额逻辑
        //取出所有商品 按金额从小到大排序， 乘以商品数量， 除以  订单总金额  四舍五入 取两位小数 = 优惠比例   
        //优惠比例 * 优惠卷金额  = 优惠金额 。     
        //商品金额  - 优惠金额  =   退款金额
        
        //按金额从小到大排序
        $last_ages = array_column($product_json_arry,'price_sale');
        array_multisort($last_ages ,SORT_ASC,$product_json_arry);
        //商品个数
        $product_num = count($product_json_arry);
         

        foreach($product_json_arry as $k=>$v){
            //计算优惠比例
            //最后一款产品比例计算
            if(($k+1) == $product_num){
                
                 $product_json_arry[$k]['yh_bili']  = $bili = 1 - $total_bili;
        
            }else{
                 $product_json_arry[$k]['yh_bili'] =  $bili = round($v['price_sale'] * $v['num'] / $order_price ,2);
            }
            
             //总优惠比例
             $total_bili += $bili;
            //优惠金额
             $product_json_arry[$k]['yh_price'] =  $yh_price = $yhq_price * $bili ;
            //计算退款金额
             $product_json_arry[$k]['refund_price'] = $v['price_sale'] * $v['num']  - $yh_price;
        }
        return $product_json_arry;
    }
    
    //确认收货
    public function qrshouhuo($userId=0){
        global $db,$request,$comId;
        
        $orderId = (int)$request['orderId'];
        $userId = empty($userId)?(int)$request['user_id']:$userId;
        $order_comId = $comId;
        $order_fenbiao = getFenbiao($order_comId,20);
        $order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
        $user  =  $db->get_row("select * from users where id=$userId");
        $yzFenbiao = $fenbiao = getFenbiao($comId,20);
        if($order->status!=2 && $order->status!=3 && $order->peisong_type!=4){
            return '{"code":0,"message":"订单不是待收货状态"}';
        }
        $db->query("update order$order_fenbiao set status=4 where id=$orderId");
        $db->query("update order_detail$order_fenbiao set status=2 where orderId=$orderId limit 1");
        $jilu = array();
        $jilu['orderId'] = $orderId;
        $jilu['username'] = '';
        $jilu['dtTime'] = date("Y-m-d H:i:s");
        $jilu['type'] = 1;
        $jilu['remark'] = '手动确认收货';
        $jilu['operate'] = '确认收货';
        $db->insert_update('order_jilu'.$fenbiao,$jilu,'id');
        $date = date("Y-m-d H:i:s");
        if($order->jifen>0){
            $db->query("update users set jifen=jifen+$order->jifen where id=$order->userId");
            $jifen_jilu = array();
            $jifen_jilu['userId'] = $order->userId;
            $jifen_jilu['comId'] = $comId;
            $jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
            $jifen_jilu['jifen'] = $order->jifen;
            $jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$order->userId);
            $jifen_jilu['type'] = 1;
            $jifen_jilu['oid'] = $order->id;
            $jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
            $jifen_jilu['remark'] = '购买商品返积分';
            //$fenbiao = getYzFenbiao($fanli_json->shangshangji,20);
            $db->insert_update('user_jifen'.$order_fenbiao,$jifen_jilu,'id');

        }

        // handleFanLi($user,$order);
		if(!empty($order->fanli_json)){
			$fanli_json = json_decode($order->fanli_json);
			
			//自营收入，如果shagnji为0算到平台收益
			if($fanli_json->shangji_fanli>0 && $fanli_json->shangji){
				//返利给团长
		        $shangji = $fanli_json->shangji;
		        $personalFenhong = $fanli_json->shangji_fanli;
                $db->query("update users set yongjin = yongjin+$personalFenhong, yongjins=yongjins+$personalFenhong where id = $shangji");
                $liushui = array();
                $liushui['comId'] = $comId;
                $liushui['userId']=$shangji;
                $liushui['orderId']= $order->orderId;
                $liushui['order_id'] = $order->id;
                $liushui['bili'] = 1;
                $liushui['order_total'] = $order->price;
                $liushui['money'] = $personalFenhong;
                $liushui['yue'] = $db->get_var("select yongjin from users where id = $shangji");
                $liushui['type'] = 0;
                $liushui['from_userId'] = $order->userId;
                $liushui['from_mendianId'] = $order->mendianId;
                $liushui['remark'] = '直推下单返利';
                $liushui['orderInfo'] = "直推下单返利，直推用户：".$user->nickname."(".$user->phone.")";
                $liushui['dtTime']=date("Y-m-d H:i:s");
    
                $db->insert_update('user_yongjin8', $liushui,'id');
			}
			//团队奖励
			if($fanli_json->shangshangji_fanli>0 && $fanli_json->shangshangji>0){

				$db->query("update users set money=money+".$fanli_json->shangshangji_fanli.",earn=earn+".$fanli_json->shangshangji_fanli." where id=$fanli_json->shangshangji");
				$yue = $db->get_var("select money from users where id=$fanli_json->shangshangji");
				
				$liushui = array();
				$liushui['userId']=$fanli_json->shangshangji;
				$liushui['comId']=$comId;
				$liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
				$liushui['money']=$fanli_json->shangshangji_fanli;
				$liushui['yue']=$yue;
				$liushui['type']=2;
				$liushui['dtTime']=$date;
				$liushui['remark']='间推返利';
				$liushui['orderInfo']='间推返利，订单号：'.$order->orderId;
				$liushui['order_id']=$orderId;
				$liushui['from_user']=$userId;
		
				$db->insert_update('user_liushui'.$yzFenbiao,$liushui,'id');
			}
		}
        
        //设置自动好评
        $days = $db->get_var("select time_comment from demo_shezhi where comId=$order->comId");
        if(empty($days))$days=10;
        $comment_time = strtotime("+ $days day");
        $timed_task = array();
        $timed_task['comId'] = (int)$order->comId;
        $timed_task['dtTime'] = $comment_time;
        $timed_task['router'] = 'order_autoComment';
        $timed_task['params'] = '{"order_id":'.$orderId.'}';
        $db->insert_update('demo_timed_task',$timed_task,'id');
        
        return '{"code":1,"message":"操作成功"}';
    }
    public function comment(){
        global $db,$request,$comId;
        $orderId = (int)$request['order_id'];
        //$inventoryId = (int)$request['inventoryId'];
        $inventIds = explode(',',$request['inventoryId']);
        $userId = (int)$request['user_id'];
        $star = (int)$request['star'];
        $star1 =(int)$request['star1'];
        $star2 =(int)$request['star2'];
        $imgs = $request['uploadedfile1'];
        if(!empty($request['uploadedfile2'])){
            $imgs.='|'.$request['uploadedfile2'];
        }
        if(!empty($request['uploadedfile3'])){
            $imgs.='|'.$request['uploadedfile3'];
        }
        $content = $request['content'];
        $fenbiao = getFenbiao($comId,20);
        /*$order = $db->get_row("select status,ifpingjia,orderId,storeId,mendianId from order$fenbiao where id=$orderId and userId=$userId");
		if($order->status!=4 || $order->ifpingjia==1){
			die('{"code":0,"message":"您已经评价过该订单了！"}');
		}*/
        if(!empty($inventIds)){
            foreach ($inventIds as $inventoryId) {
                $inventoryId = (int)$inventoryId;
                $db->query("update order_detail$fenbiao set ifpingjia=1 where orderId=$orderId and inventoryId=$inventoryId limit 1");
                $ifhas = $db->get_var("select id from order_detail$fenbiao where orderId=$orderId and ifpingjia=0 limit 1");
                if(empty($ifhas)){
                    $db->query("update order$fenbiao set ifpingjia=1 where id=$orderId");
                }
                $u = $db->get_row("select nickname from users where id=$userId");
                $p = $db->get_row("select productId,title from demo_product_inventory where id=$inventoryId");
                $comment = array();
                $comment['orderId'] = $orderId;
                $comment['if_jifen'] = (int)$db->get_var("select if_jifen from order$fenbiao where id = $orderId");
                $comment['pdtId'] = $p->productId;
                $comment['inventoryId'] = $inventoryId;
                $comment['comId'] = $comId;
                $comment['userId'] = $userId;
                //$comment['mendianId'] = $order->mendianId;
                $comment['name'] = $u->nickname;
                $comment['order_orderId'] = $order->orderId;
                $comment['pdtName'] = $p->title;
                $comment['star'] = $star;
                $comment['star1'] = $star1;
                $comment['star2'] = $star2;
                $comment['cont1'] = $content;
                $comment['images1'] = $imgs;
                $comment['dtTime1'] = date('Y-m-d H:i:s');
     
                //$comment['storeId'] = $order->storeId;
                $db->insert_update('order_comment'.$fenbiao,$comment,'id');
            }
        }
        return '{"code":1,"message":"评价成功"}';
    }
    
    //微信扫码支付
	public function wxScanCodePay(){
		global $db,$request,$comId;

		$id = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
        
        $order = $db->get_row("select * from order$fenbiao where id = $id");
        if(!$order){
             return '{"code":0,"message":"未找到相应的订单信息！"}';
        }
        if($order->ispay){
             return '{"code":0,"message":"已经支付完毕的订单请勿重复支付！"}';
        }
        
	    $subject = '支付订单，单号：'.$order->orderId;
        $notifyUrl = "http://".$_SERVER['HTTP_HOST']."/notify.php";
		
        
        $daizhifu = bcsub($order->price, $order->price_payed, 2);
		$price = round($daizhifu*100);
		$orderId = $order->orderId;
		
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
		if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置信息有误"}';
		}

     
        require_once ABSPATH.'/inc/pay/WxpayAPI_php_v3/example/log.php';  
        require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");  
		require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php"); 
		require_once("inc/pay/WxpayAPI_php_v3/example/log.php"); 
	
		$weixin_arr = json_decode($weixin_set->info);

		define('WX_APPID',$weixin_arr->appid);
		define('WX_MCHID',$weixin_arr->mch_id);
		define('WX_KEY',$weixin_arr->key);
		define('WX_APPSECRET',$weixin_arr->appsecret);
		
		$payLog = array(
		    'userId' => $userId,
		    'type' => 0,
		    'source' => 1,
		    'typeId' => $id,
		    'payNo' => 'O'.date("YmdHis").rand(10000,99999),
		    'status' => 0,
		    'dtTime' => date("Y-m-d H:i:s")
		);
		
		$db->insert_update("pay_log", $payLog, "id");
		
		$dtTime = date("YmdHis",strtotime($order->dtTime));
		$expireTime = date("YmdHis", time() + 60*60*24);
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($subject);
		$input->SetAttach(1);
		$input->SetOut_trade_no($payLog['payNo']);
		$input->SetTotal_fee("$price");
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire(date("YmdHis", time() + 600));
		$input->SetGoods_tag("test");
		$input->SetNotify_url($notifyUrl);
		$input->SetTrade_type("NATIVE");
		$input->SetProduct_id($orderId);

		require_once "inc/pay/WxpayAPI_php_v3/example/WxPay.NativePay.php";	  
		$notify = new \NativePay();
		$result = $notify->GetPayUrl($input);
		
		if(isset($result['return_code']) && $result['return_code'] == 'FAIL'){
		    return '{"code":0,"message":"发起支付失败,原因：'.$result['return_msg'].'！"}';
		}

		$share_url = $result["code_url"];
		$share_file = 'cache/wx_scan/'.$orderId.date('YmdHis').'.png';
		$return['code'] = 1;
		$return['message'] = '返回成功';
		$return['data'] = array();
		$return['data']['qrcode'] = 'https://'.$_SERVER['HTTP_HOST'].'/'.$share_file;
		$return['data']['timeStamp'] = time();
	
		if(!is_file(ABSPATH.$share_file)){
		    require_once ABSPATH."/inc/pay/WxpayAPI_php_v3/example/phpqrcode/phpqrcode.php"; 
			\QRcode::png($share_url,$share_file,'L',8);
		}
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	//线下转款
	public function offlinePay()
	{
	    global $db,$request,$comId;
	    
		$orderId = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$order_fenbiao = $fenbiao = getFenbiao($comId,20);
		$order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");
		if(empty($order)){
			return '{"code":0,"message":"订单不存在"}';
		}
		
		if($order->status!=-5){
			return '{"code":0,"message":"订单当前不是待支付状态"}';
		}
		
		$o = array();
		$o['id'] = $orderId;
		$o['pay_type'] = 6;
		
		$db->insert_update('order'.$order_fenbiao,$o,'id');
		
		return '{"code":1,"message":"线下转款设置成功，请尽快完成转款"}';
	}
	
    
    public function wxPay(){
        global $request,$db,$comId,$order;
        
        $orderId = (int)$request['order_id'];
        $userId = (int)$request['user_id'];
        $type = (int)$request['type'];
        $type = !empty($type) ? $type : 3;  //默认小程序
        $fenbiao = getFenbiao($comId,20);
        $order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
        if(empty($order)){
            return '{"code":0,"message":"订单不存在"}';
        }
        if($order->status!=-5){
            return '{"code":0,"message":"订单不是待支付状态，不能进行支付"}';
        }
        $pay_end = strtotime($order->pay_endtime);
        $now = time();
        if($pay_end<$now){
            return '{"code":0,"message":"该订单已超过支付时间"}';
        }

        $weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=$type limit 1");
        if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
            return '{"code":0,"message":"微信配置信息有误"}';
        }
        require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
        require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
        require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
        $weixin_arr = json_decode($weixin_set->info);
        define('WX_APPID',$weixin_arr->appid);
        define('WX_MCHID',$weixin_arr->mch_id);
        define('WX_KEY',$weixin_arr->key);
        define('WX_APPSECRET',$weixin_arr->appsecret);
        //初始化日志
        $logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
        $log = \Log::Init($logHandler, 15);
        
        $payLog = array(
		    'userId' => $userId,
		    'type' => 0,
		    'source' => $type,
		    'typeId' => $order->id,
		    'payNo' => "O".date("YmdHis").rand(100000,999999),
		    'dtTime' => date('Y-m-d H:i:s') 
		);
		
		$db->insert_update("pay_log", $payLog, "id");
        

        //echo 3333;
        //①、获取用户openid
        $tools = new \JsApiPay();
        $field = 'openId';
        if($type == 3){
            $field = 'mini_openId';
        }
        $openId = $db->get_var("select $field from users where id=$order->userId");
        if(empty($openId)){
            return '{"code":0,"message":"获取不到会员的openId"}';
        }
        $daizhifu = getXiaoshu($order->price-$order->price_payed,2);
        if($order->price_dingjin>0){
            $daizhifu = getXiaoshu($order->price_dingjin-$order->price_payed,2);
        }
        $product_json = json_decode($order->product_json);
        foreach ($product_json as $pdt) {
            $subject.=','.$pdt->title.'*'.$pdt->num;
        }
        $body = substr($subject,1);
        $subject = sys_substr($body,30,true);
        $subject = str_replace('_','',$subject).'_'.$comId;
        $pay_price = round($daizhifu*100);

        $dtTime = date("YmdHis",strtotime($order->dtTime));
        $expireTime = date("YmdHis", time() + 60*60*24);

        //②、统一下单
        $input = new \WxPayUnifiedOrder();
        $input->SetBody($subject);
        $input->SetAttach($type);//自定义数据
        $input->SetOut_trade_no($payLog['payNo']);
        $input->SetTotal_fee($pay_price);
        $input->SetTime_start(date("YmdHis"));
        $input->SetTime_expire($expireTime);
        $input->SetGoods_tag($subject);
        $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify.php");
        $input->SetTrade_type("JSAPI");
        $input->SetOpenid($openId);
        if($comId==1114){
            file_put_contents('wxpay.txt',serialize($input));
        }
        $orders = \WxPayApi::unifiedOrder($input);
        file_put_contents('wxpay.txt',json_encode($orders,JSON_UNESCAPED_UNICODE));
    
        if($orders['appid']==NULL){
            return '{"code":0,"message":"获取支付信息失败，请联系技术人员，微信反馈错误'.$orders['return_msg'].'"}';
        }
        
        if(isset($orders['err_code'])){
             return '{"code":0,"message":"获取支付信息失败，请联系技术人员，微信反馈错误'.$orders['err_code_des'].'"}';
        }
 
        $resultData = json_decode($tools->GetJsApiParameters($orders));
        $return = array();
        $return['code'] = 1;
        $return['message'] = '返回成功';
        $return['data'] = array();
        $return['data']['appId'] = $resultData->appId;
        $return['data']['timeStamp'] = $resultData->timeStamp;
        $return['data']['nonceStr'] = $resultData->nonceStr;
        $return['data']['package'] = $resultData->package;
        $return['data']['signType'] = $resultData->signType;
        $return['data']['paySign'] = $resultData->paySign;
        
        return json_encode($return,JSON_UNESCAPED_UNICODE);
    }
 
    //余额支付订单
    public function yuePay(){
        global $db,$request,$comId;
        
        $orderId = (int)$request['order_id'];
        $userId = (int)$request['user_id'];
        $zhifumm = $request['pay_pass'];
        $order_fenbiao = $fenbiao = getFenbiao($comId,20);
        $u = $db->get_row("select payPass,wx_money from users where id=$userId");
        require_once(ABSPATH.'/inc/class.shlencryption.php');
        $shlencryption = new \shlEncryption($zhifumm);
        if($u->payPass!=$shlencryption->to_string()){
            return '{"code":0,"message":"支付密码不正确"}';
        }
        $order = $db->get_row("select * from order$order_fenbiao where id=$orderId and userId=$userId");

        if(empty($order)){
            return '{"code":0,"message":"订单不存在"}';
        }
        if($order->status!=-5){
            return '{"code":0,"message":"订单当前不是待支付状态"}';
        }
        
        $needPay = bcsub($order->price ,$order->price_payed, 2);
        
        
        if($u->wx_money == 0){
            return '{"code":0,"message":"您的余额为0，不能发起支付"}';
        }
        
        // $cardId = (int)$request['cardId'];
        // $card = $db->get_row("select * from user_card where userId = $userId and id = $cardId");
        
        // if(!$card){
        //     return '{"code":0,"message":"未找到相应的储值卡信息"}';
        // }
        
        // if($card->yue == 0 || $card->yue < 0){
        //     return '{"code":0,"message":"当前储值卡已经没有余额"}';    
        // }
        
        // if($needPay > $card->yue){
        //     $canPay = $card->yue;
        //     $isOver = 0;
        // }else{
        //     $canPay = $needPay;
        //     $isOver = 1;
        // }
        
        if($needPay > $u->wx_money){
            $canPay = $u->wx_money;
            $isOver = 0;
        }else{
            $canPay = $needPay;
            $isOver = 1;
        }
        
        $pay_end = strtotime($order->pay_endtime);
        $now = time();
        if($pay_end < $now){
            return '{"code":0,"message":"该订单已超过支付时间"}';
        }
       
        // $db->query("update user_card set yue=yue-$canPay where id= $card->id");
        $db->query("update users set wx_money=wx_money-$canPay where id= $userId");
        
        $liushui = array();
        $liushui['userId']=$userId;
        $liushui['comId']=$comId;
        $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
        $liushui['money']=-$canPay;
      //  $liushui['cardId'] = $card->id;
        $liushui['yue']= $db->get_var("select wx_money from users where id = $userId");
        $liushui['type']=1;
        $liushui['dtTime']=date("Y-m-d H:i:s");
        $liushui['remark']='订单支付';
        $liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
        $liushui['order_id']=$orderId;
        
        $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
        //修改订单信息
        $o = array();
        $o['id'] = $orderId;
        $o['pay_types'] = $order->pay_types.",2";
        if($isOver==1){
            $o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
            $o['ispay'] = 1;
            $o['pay_type'] = 1;
        }
        $o['price_payed'] = bcadd($order->price_payed, $canPay, 2);
        $pay_json = array();
        if(!empty($order->pay_json)){
            $pay_json = json_decode($order->pay_json,true);
        }
        $temp = array(
            'card_no' => $card->card_no,
            'price' => $canPay,
            'cardId' => $card->id
        );
        $pay_json['yue'][] = $temp;
       
        $o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
    
        $db->insert_update('order'.$order_fenbiao, $o,'id');
        
        if($isOver){
            //调用支付完成、生成发货单或者更改拼团状态
            $order->price+=$order->price_payed;
            order_pay_done($order);
        }
        
        return '{"code":1,"message":"支付成功","buy_type":'.$order->type.',"is_over":'.$isOver.'}';
    }
    
    //微信充值订单
    public function chongzhiPay(){
        global $db,$request,$comId;
        
        $orderId = (int)$request['order_id'];
        $userId = (int)$request['user_id'];
        $zhifumm = $request['pay_pass'];
        $order_fenbiao = $fenbiao = getFenbiao($comId,20);
        $u = $db->get_row("select payPass,money from users where id=$userId");
        require_once(ABSPATH.'/inc/class.shlencryption.php');
        $shlencryption = new \shlEncryption($zhifumm);
        if($u->payPass!=$shlencryption->to_string()){
            // return '{"code":0,"message":"支付密码不正确"}';
        }
        
        $order = $db->get_row("select * from order$fenbiao where id = $orderId and userId = $userId");
        if(!$order){
            return '{"code":0,"message":"未找到对应订单，请检查"}';    
        }
        
        $wxMoney = $db->get_var("select wx_money from users where id = $userId");
        
        $needPay = bcsub($order->price ,$order->price_payed, 2);
        
        if($wxMoney == 0 || $wxMoney < 0){
            return '{"code":0,"message":"当前微信充值已经没有余额"}';    
        }
        
        if($needPay > $wxMoney){
            $canPay = $wxMoney;
            $isOver = 0;
        }else{
            $canPay = $needPay;
            $isOver = 1;
        }
        
        $pay_end = strtotime($order->pay_endtime);
        $now = time();
        if($pay_end < $now){
            return '{"code":0,"message":"该订单已超过支付时间"}';
        }

        $db->query("update users set wx_money=wx_money-$canPay where id= $userId");
        
        $liushui = array();
        $liushui['userId']=$userId;
        $liushui['comId']=$comId;
        $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
        $liushui['money']=-$canPay;
        $liushui['cardId'] = 0;
        $liushui['yue']= $db->get_var("select wx_money from users where id = $userId");
        $liushui['type']=1;
        $liushui['dtTime']=date("Y-m-d H:i:s");
        $liushui['remark']='订单支付';
        $liushui['orderInfo']='订单支付，订单号：'.$order->orderId;
        $liushui['order_id']=$orderId;
        
        $db->insert_update('user_liushui'.$fenbiao,$liushui,'id');
        //修改订单信息
        $o = array();
        $o['id'] = $orderId;
        $o['pay_types'] = $order->pay_types.",1";
        if($isOver==1){
            $o['status'] = empty($order->tuan_id)?2:0;//普通订单要设置为待发货状态，并且添加发货单
            $o['ispay'] = 1;
            $o['pay_type'] = 2;
        }
        $o['price_payed'] = bcadd($order->price_payed, $canPay, 2);
        $pay_json = array();
        if(!empty($order->pay_json)){
            $pay_json = json_decode($order->pay_json,true);
        }
        
        $pay_json['weixin']['price'] = $canPay;
		$pay_json['weixin']['desc'] = $userId;
       
        $o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
    
        $db->insert_update('order'.$order_fenbiao, $o,'id');
        
        if($isOver){
            //调用支付完成、生成发货单或者更改拼团状态
            $order->price+=$order->price_payed;
            order_pay_done($order);
        }
        
        return '{"code":1,"message":"支付成功","buy_type":'.$order->type.',"is_over":'.$isOver.'}';
    }
    
    //积分支付
    public function jifenPay(){
        global $db,$request,$comId;
        $orderId = (int)$request['order_id'];
        $userId = (int)$request['user_id'];
        $jifen = $request['jifen'];
        $yzFenbiao = $fenbiao = getFenbiao($comId,20);
        $u = $db->get_row("select jifen from users where id=$userId");
        $order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
        if(empty($order)){
            return '{"code":0,"message":"订单不存在"}';
        }
        if($order->status!=-5){
            return '{"code":0,"message":"订单当前不是待支付状态"}';
        }
        if($u->jifen<$jifen){
            return '{"code":0,"message":"积分不足！请刷新重试"}';
        }
        $pay_end = strtotime($order->pay_endtime);
        $now = time();
        if($pay_end<$now){
            return '{"code":0,"message":"该订单已超过支付时间"}';
        }
        $jifen_pay = $db->get_row("select if_jifen_pay,jifen_pay_rule from user_shezhi where comId=$comId");
        if($jifen_pay->if_jifen_pay!=1){
            return '{"code":0,"message":"积分抵现功能已关闭"}';
        }
        $jifen_rule = json_decode($jifen_pay->jifen_pay_rule);

        $money = bcdiv($jifen, $jifen_rule->jifen, 2);
        $daizhifu = bcsub($order->price, $order->price_payed, 2);
        
        if($money > $daizhifu){
            return '{"code":0,"message":"数据异常,积分抵现金额大于实际需要支付金额!"}';
        }
 
        if($order->price_dingjin>0){
            $daizhifu = $order->price_dingjin-$order->price_payed;
        }

        //修改账号余额及流水记录
        $db->query("update users set jifen=jifen-$jifen where id=$userId");
        $jifen_jilu = array();
        $jifen_jilu['userId'] = $userId;
        $jifen_jilu['comId'] = $order->comId;
        $jifen_jilu['orderId'] = date("YmdHis").rand(1000000000,9999999999);
        $jifen_jilu['jifen'] = -$jifen;
        $jifen_jilu['yue'] = $db->get_var('select jifen from users where id='.$userId);
        $jifen_jilu['type'] = 2;
        $jifen_jilu['oid'] = $order->id;
        $jifen_jilu['dtTime'] = date("Y-m-d H:i:s");
        $jifen_jilu['remark'] = '积分支付订单';
        $db->insert_update('user_jifen'.$fenbiao,$jifen_jilu,'id');
        //修改订单信息
        $o = array();
        $o['id'] = $orderId;
        $o['if_jifen'] = 1;
        if($money==$daizhifu && $order->price_dingjin==0){
            $o['status'] = 2;//普通订单要设置为待发货状态，并且添加发货单
            $o['ispay'] = 1;
            $o['pay_type'] = 1;
            $isOver = 1;
        }else{
            $isOver = 0;
        }
        $o['price_payed'] = bcadd($order->price_payed ,$money, 2);
        $pay_json = array();
        
        if(!empty($order->pay_json)){
            $pay_json = json_decode($order->pay_json,true);
        }
        $pay_json['jifen']['price'] = $money;
        $pay_json['jifen']['desc'] = $jifen;
        $o['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
        $db->insert_update('order'.$fenbiao,$o,'id');

        if($money==$daizhifu){
            order_pay_done($order);
        }
        
        return '{"code":1,"message":"支付成功","is_over":'.$isOver.'}';
    }
    
    //取消订单
    public function qxOrder(){
        global $db,$request,$comId;
        $orderId = (int)$request['order_id'];
        $userId = (int)$request['user_id'];
        $fenbiao = getFenbiao($comId,20);
        $order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
        if(empty($order)){
            return '{"code":0,"message":"订单不存在"}';
        }
        if($order->status==0 || $order->status==-5 || $order->status==2){
            if($order->tuan_id>0){
                $tuan = $db->get_row("select nums,status,userIds,orderIds from demo_tuan where id=$order->tuan_id");
                if($tuan->status!=0){
                    return '{"code":0,"message":"团购已结束，不能取消订单"}';
                }
                $userIds = explode(',',$tuan->userIds);
                $orderIds= explode(',',$tuan->orderIds);
                foreach($userIds as $k=>$v) {
                    if($order->userId == $v){
                        unset($userIds[$k]);
                        break;
                    }
                }
                foreach($orderIds as $k=>$v) {
                    if($order->id == $v){
                        unset($orderIds[$k]);
                        break;
                    }
                }
                $uids = empty($userIds)?'':implode(',',$userIds);
                $oids = empty($orderIds)?'':implode(',',$orderIds);
                $nums = $tuan->nums - $order->pdtNums;
                $db->query("update demo_tuan set nums=$nums,userIds='$uids',orderIds='$oids' where id=$order->tuan_id");
                $db->query("update order$fenbiao set status=-1,remark='订单已取消',qx_time='".date("Y-m-d H:i:s")."' where id=$orderId");
                $db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
                //恢复库存数量
                addKuCun($orderId);
                if($order->price_payed>0){
                    tuikuan($order);
                }
                
            }else{
                $db->query("update order_fahuo$fenbiao set status=-1 where id=$order->fahuoId");
                $db->query("update order$fenbiao set status=-1,remark='订单已取消',qx_time='".date("Y-m-d H:i:s")."' where id=$orderId");
                $db->query("update order_detail$fenbiao set status=-1 where orderId=$orderId");
                //恢复库存数量
                addKuCun($orderId);

                $db->query("delete from cuxiao_pdt_buy where orderId=$orderId and comId=$comId");
                if($order->price_payed>0){
                    tuikuan($order);
                }
            }
            $db->query("update user_yugu_shouru set status=-1 where comId=$comId and orderId=$order->id and order_type=1");
        }else{
            return '{"code":0,"message":"订单当前状态不支持取消"}';
        }
        return '{"code":"1","message":"取消成功"}';
    }
    
    //微信h5支付
	public function wxH5Pay(){
		global $request,$db,$comId,$order;
		$orderId = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		$order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
		if(empty($order)){
			return '{"code":0,"message":"订单不存在"}';
		}
		if($order->status!=-5){
			return '{"code":0,"message":"订单不是待支付状态，不能进行支付"}';
		}
		$pay_end = strtotime($order->pay_endtime);
		$now = time();
		if($pay_end<$now){
			return '{"code":0,"message":"该订单已超过支付时间"}';
		}
		
		$weixin_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=1 limit 1");
		if(empty($weixin_set)||$weixin_set->status==0||empty($weixin_set->info)){
			return '{"code":0,"message":"微信配置信息有误"}';
		}
		require_once("inc/pay/WxpayAPI_php_v3/lib/WxPay.Api.php");//echo 111;
		require_once("inc/pay/WxpayAPI_php_v3/example/WxPay.JsApiPay.php");
		require_once("inc/pay/WxpayAPI_php_v3/example/log.php");
		$weixin_arr = json_decode($weixin_set->info);
		define('WX_APPID',$weixin_arr->appid);
		define('WX_MCHID',$weixin_arr->mch_id);
		define('WX_KEY',$weixin_arr->key);
		define('WX_APPSECRET',$weixin_arr->appsecret);
		//初始化日志
		$logHandler= new \CLogFileHandler("inc/pay/WxpayAPI_php_v3/logs/".date('Y-m-d').'.log');
		$log = \Log::Init($logHandler, 15);

		//①、获取用户openid
		$tools = new \JsApiPay();

		$daizhifu = getXiaoshu($order->price-$order->price_payed,2);
		if($order->price_dingjin>0){
		    $daizhifu = getXiaoshu($order->price_dingjin-$order->price_payed,2);
		}
		$product_json = json_decode($order->product_json);
		foreach ($product_json as $pdt) {
			$subject.=','.$pdt->title.'*'.$pdt->num;
		}
		$body = substr($subject,1);
		$subject = sys_substr($body,30,true);
		$subject = str_replace('_','',$subject).'_'.$comId;
		$pay_price = round($daizhifu*100);

		$dtTime = date("YmdHis",strtotime($order->dtTime));
		$expireTime = date("YmdHis", time() + 60*60*24);

		//②、统一下单
		$input = new \WxPayUnifiedOrder();
		$input->SetBody($subject);
		$input->SetAttach($comId);//自定义数据
		$input->SetOut_trade_no($order->orderId);
		$input->SetTotal_fee($pay_price);
		$input->SetTime_start(date("YmdHis"));
		$input->SetTime_expire($expireTime);
		$input->SetGoods_tag($subject);
		$input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/notify.php");
		$input->SetTrade_type("MWEB");
		$orders = \WxPayApi::unifiedOrder($input);
		file_put_contents('wxpay.txt',json_encode($orders,JSON_UNESCAPED_UNICODE));
		if($orders['appid']==NULL){
			return '{"code":0,"message":"获取支付信息失败，请联系技术人员"}';
		}
		
		$return = array();
		$return['code'] = 1;
		$return['message'] = '';
		$return['data'] = array();
		$return['data']['mweb_url'] = $orders['mweb_url'];
		
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
	//支付宝扫码支付
	public function aliScanCodePay(){
	    global $db,$request,$comId;
		$id = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
		
        $order = $db->get_row("select * from order$fenbiao where id = $id");
        if(!$order){
             return '{"code":0,"message":"未找到相应的订单信息！"}';
        }
        if($order->ispay){
             return '{"code":0,"message":"已经支付完毕的订单请勿重复支付！"}';
        }
        
		require_once(ABSPATH."/inc/pay/alipay/alipay.config.php");
		$payment_type = "1";
		$notify_url =trim($aliapy_config['notify_url']);
		$return_url = trim($aliapy_config['return_url']);
		$return_url = get_referer().'/orderDetail?order_id='.$id;
		$seller_email = trim($aliapy_config['seller_email']);
		$out_trade_no = $order->orderId;
		$subject = '订单支付';
		$total_fee = $order->price;
		$body = '订单支付';
		$show_url = "http://".$_SERVER['HTTP_HOST'];
		$anti_phishing_key = "";
		$exter_invoke_ip = "";
		//非局域网的外网IP地址，如：221.0.0.1


    	/************************************************************/
    
    	//构造要请求的参数数组，无需改动
		$parameter = array(
			"service" => "create_direct_pay_by_user",
			"partner" => trim($alipay_config['partner']),
			"payment_type"	=> $payment_type,
			"notify_url"	=> $notify_url,
			"return_url"	=> $return_url,
			"seller_email"	=> $seller_email,
			"out_trade_no"	=> $out_trade_no,
			"subject"	=> $subject,
			"total_fee"	=> $total_fee,
			"body"	=> $body,
			"show_url"	=> $show_url,
			"anti_phishing_key"	=> $anti_phishing_key,
			"exter_invoke_ip"	=> $exter_invoke_ip,
			"_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
		);
// 		file_put_contents('ali_pay_err.log',"paypal notify info--$today:\r\n".json_encode($parameter).PHP_EOL,FILE_APPEND);
		//建立请求
		require_once("inc/pay/alipay/lib/alipay_submit.class.php");
		$alipaySubmit = new \AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "");
		
		$return['code'] = 1;
		$return['message'] = '请求成功';
		$return['data'] = $html_text;

		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	//支付宝H5支付
	public function aliwpay(){
	    global $db,$request,$comId;
	    
		$orderId = (int)$request['order_id'];
		$userId = (int)$request['user_id'];
		$fenbiao = getFenbiao($comId,20);
        
        $order = $db->get_row("select * from order$fenbiao where id=$orderId and userId=$userId");
        if(!$order){
             return '{"code":0,"message":"未找到相应的订单信息！"}';
        }
        if($order->ispay){
             return '{"code":0,"message":"已经支付完毕的订单请勿重复支付！"}';
        }
        
    	$alipay_set = $db->get_row("select status,info from demo_kehu_pay where comId=$comId and type=2 limit 1");
    	if(empty($alipay_set)||$alipay_set->status==0||empty($alipay_set->info)){
    		die('支付宝配置信息有误');
    	}
    	$alipay_arr = json_decode($alipay_set->info);
    	$subject = $body = '订单支付';
  
    	require_once(ABSPATH."/inc/pay/wappay/alipay.config.php");
    	//合作者id
    	$alipay_config['partner'] = $alipay_arr->partnerId;
    	$alipay_config['seller_id']	= $alipay_config['partner'];
    	$alipay_config['private_key']	= $alipay_arr->private_key;
    	$alipay_config['alipay_public_key']= $alipay_arr->alipay_public_key;
    	$notify_url =trim($aliapy_config['notify_url']);
// 		$return_url = trim($aliapy_config['return_url']);
		$return_url = get_referer().'/orderDetail?order_id='.$orderId;
		
		
		
    	require_once(ABSPATH."/inc/pay/wappay/lib/alipay_submit.class.php");
    	$out_trade_no = $order->orderId;
    	$total_fee =  $order->price;
    	//$show_url = "http://".$_SERVER['HTTP_HOST']."/index.php?p=19&a=view&id=$orderId";
    	$parameter = array(
    		"service"       => $alipay_config['service'],
    		"partner"       => $alipay_config['partner'],
    		"seller_id"  => $alipay_config['seller_id'],
    		"payment_type"	=> $alipay_config['payment_type'],
    		"notify_url"	=> $notify_url,
    		"return_url"	=> $return_url,
    		"_input_charset"	=> trim(strtolower($alipay_config['input_charset'])),
    		"out_trade_no"	=> $out_trade_no,
    		"subject"	=> $subject,
    		"total_fee"	=> $total_fee,
    		"show_url"	=> $show_url,
    		"body"	=> $body,
    	);
    	
    	$alipaySubmit = new \AlipaySubmit($alipay_config);
    	$html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
    	$return['code'] = 1;
		$return['message'] = '请求成功';
		$return['data'] = $html_text;
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
	
    //上传支付凭证新
	public function uploadPz(){
		global $db,$comId,$request;
        $pay = array();
        $pay['userId'] = $request['user_id'];;
        $pay['comId'] = $comId;
        $orderId = $pay['orderId'] = $request['order_id'];
        $pay['paypz'] =$request['paypz'];
        $pay['id'] = $request['id'];
        if(empty($id)){
            $pay['dtTime'] = date("Y-m-d H:i:s"); 
        }else{
            $pay['updateTime'] = date("Y-m-d H:i:s");
        }
        //var_dump($pay);die;
        $db->insert_update('demo_paypz',$pay,'id');
        //修改订单状态为待审核  
        $db->query("update order8 set status=1,is_xxzf = 1 where id=$orderId");
		return '{"code":1,"message":"上传支付凭证成功,等待后台审核"}';
	}
    

    //根据购物车获取商品促销信息
    public static function get_pdt_cuxiao($pdts,$areaId,$level,$money=0){
        global $db,$comId;
        $pareaId = (int)$db->get_var("select parentId from demo_area where id=$areaId");
        $cuxiaos = $db->get_results("select * from cuxiao_pdt where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and mendianIds='' and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds) or find_in_set($pareaId,areaIds))");
        //echo "select * from cuxiao_pdt where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and mendianIds='' and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds) or find_in_set($pareaId,areaIds))";
        $return = array();
        $return['jian'] = 0;//总优惠价格
        $return['zengpin'] = array();
        $return['cuxiao_pdt'] = array();
        $return['cuxiao_ids'] = array();
        $return['cuxiao_title'] = '';
        if(!empty($cuxiaos)){
            foreach ($cuxiaos as $cuxiao){
                $zong_money = 0;
                $zong_num = 0;
                $pdtIds = explode(',', $cuxiao->pdtIds);
                $rules = json_decode($cuxiao->guizes,true);
                if(!empty($rules)){
                    $columns = array_column($rules,'man');
                    array_multisort($columns,SORT_DESC,$rules);
                    //计算出符合条件的商品的总数量和总价格

                    foreach ($pdts as $pdts1){
                        foreach ($pdts1 as $inventoryId => $pdt) {
                            if(in_array($inventoryId, $pdtIds)){
                                $zong_money += $pdt->price*$pdt->num;
                                $zong_num += $pdt->num;
                                if(!in_array($cuxiao->id,$return['cuxiao_ids'])){
                                    $return['cuxiao_ids'][] = $cuxiao->id;
                                }
                            }
                        }
                    }
                    if($money>0)$zong_money = $money;
                    //如果是已数量来计算
                    if($cuxiao->accordType==1){
                        foreach ($rules as $rule){
                            if($zong_num>=$rule['man']){
                                if(!in_array($cuxiao->id,$return['cuxiao_pdt'])){
                                    $return['cuxiao_pdt'][] = $cuxiao->id;
                                    $return['cuxiao_title'].=empty($return['cuxiao_title'])?$cuxiao->title:','.$cuxiao->title;
                                }
                                //满折
                                if($cuxiao->type==3){
                                    $zhekou_money = (int)($zong_money*$rule['jian'])/100;
                                    $return['jian'] += $zong_money-$zhekou_money;
                                    break;
                                }else if($cuxiao->type==1){//满赠
                                    $zengpin = array();
                                    $zengpin['id'] = $rule['inventoryId'];
                                    $zengpin['num'] = floor($zong_num/$rule['man']);
                                    $return['zengpin'][] = $zengpin;
                                    $zong_num = $zong_num%$rule['man'];//剩余的数量继续判断是否满足后边的条件
                                }else if($cuxiao->type==2){//满减
                                    $cheng = floor($zong_num/$rule['man']);
                                    $return['jian'] += $cheng*$rule['jian'];
                                    $zong_num = $zong_num%$rule['man'];//剩余的数量继续判断是否满足后边的条件
                                }
                            }
                        }
                    }else{
                        foreach ($rules as $rule){
                            if($zong_money>=$rule['man']){
                                $return['cuxiao_pdt'][] = $cuxiao->id;
                                //满折
                                if($cuxiao->type==3){
                                    $zhekou_money = (int)($zong_money*$rule['jian'])/100;
                                    $return['jian'] += $zong_money-$zhekou_money;
                                    break;
                                }else if($cuxiao->type==1){//满赠
                                    $zengpin = array();
                                    $zengpin['id'] = $rule['inventoryId'];
                                    $zengpin['num'] = floor($zong_money/$rule['man']);
                                    $return['zengpin'][] = $zengpin;
                                    $zong_money = $zong_money%$rule['man'];//剩余的数量继续判断是否满足后边的条件
                                }else if($cuxiao->type==2){//满减
                                    $cheng = floor($zong_money/$rule['man']);
                                    $return['jian'] += $cheng*$rule['jian'];
                                    $zong_money = $zong_money%$rule['man'];//剩余的数量继续判断是否满足后边的条件
                                }
                            }
                        }
                    }
                }else{
                    foreach ($pdts as $pdts1){
                        foreach ($pdts1 as $inventoryId => $pdt) {
                            if(in_array($inventoryId, $pdtIds)){
                                if(!in_array($cuxiao->id,$return['cuxiao_ids'])){
                                    $return['cuxiao_ids'][] = $cuxiao->id;
                                    $return['cuxiao_pdt'][] = $cuxiao->id;
                                    $return['cuxiao_title'].=empty($return['cuxiao_title'])?$cuxiao->title:','.$cuxiao->title;
                                }
                            }
                        }
                    }
                }
            }
        }
        $return['cuxiao_pdt'] = array_unique($return['cuxiao_pdt']);
        return $return;
    }
    //根据购物车获取订单促销信息
    public static function get_order_cuxiao($price,$areaId,$level){
        global $db,$comId;
        $pareaId = (int)$db->get_var("select parentId from demo_area where id=$areaId");
        $cuxiaos = $db->get_results("select * from cuxiao_order where comId=$comId and startTime<'".date("Y-m-d H:i:s")."' and endTime>'".date("Y-m-d H:i:s")."' and scene=1 and status=1 and mendianIds='' and (levelIds='' or find_in_set($level,levelIds)) and (areaIds='' or find_in_set($areaId,areaIds) or find_in_set($pareaId,areaIds))");

        $return = array();
        $return['jian'] = 0;//总优惠价格
        $return['zengpin'] = array();
        $return['cuxiao_order'] = '';
        $return['cuxiao_title'] = '';
        if(!empty($cuxiaos)){
            foreach ($cuxiaos as $cuxiao){

                $rules = json_decode($cuxiao->guizes,true);
                $columns = array_column($rules,'man');
                array_multisort($columns,SORT_DESC,$rules);

                foreach ($rules as $rule) {
                    if($price>=$rule['man']){
                        $return['cuxiao_order'] = $cuxiao->id;
                        $return['cuxiao_title'] = $cuxiao->title;
                        if($cuxiao->type==3){//满折
                            $zhekou_money = (int)($price*$rule['jian'])/100;
                            $return['jian'] += $price-$zhekou_money;
                            break;
                        }else if($cuxiao->type==1){//满赠
                            $zengpin = array();
                            $zengpin['id'] = $rule['inventoryId'];
                            $zengpin['num'] = floor($price/$rule['man']);
                            $return['zengpin'][] = $zengpin;
                            $price = $price%$rule['man'];//剩余的数量继续判断是否满足后边的条件
                        }else if($cuxiao->type==2){//满减
                            $cheng = floor($price/$rule['man']);
                            $return['jian'] += $cheng*$rule['jian'];
                            $price = $price%$rule['man'];//剩余的数量继续判断是否满足后边的条件
                        }
                    }
                }
                if(!empty($return['cuxiao_order'])){
                    break;
                }
            }
        }
        return $return;
    }
    //获取可用优惠券
    public static function get_yhqs($pdts,$zong_price){
        global $db,$comId,$request;
        $userId = (int)$request['user_id'];
        $fenbiao = getFenbiao($comId,20);
        $return = array();
        $now = date("Y-m-d H:i:s");
        $yhqs = $db->get_results("select id,yhqId,title,man,jian,jiluId,startTime,endTime from user_yhq$fenbiao where comId=$comId and userId=$userId and status=0 and endTime>='$now' and startTime<='$now' and man<='$zong_price' order by id desc");
        if(!empty($yhqs)){
            foreach ($yhqs as $yhq) {
                $money = 0;
                $jilu = $db->get_row("select mendianIds,useType,channels,pdts from yhq where id=$yhq->jiluId");
                if($jilu->mendianIds!=''){
                    continue;
                }
                if(empty($jilu->mendianIds) && empty($jilu->channels) && empty($jilu->pdts)){
                    foreach ($pdts as $pdts1) {
                        foreach ($pdts1 as $inventoryId => $pdt) {
                            $money += $pdt->price*$pdt->num;
                        }
                    }
                }else if(!empty($jilu->channels) || !empty($jilu->pdts)){
                    foreach ($pdts as $pdts1) {
                        foreach ($pdts1 as $inventoryId => $pdt) {
                            $inventory = $db->get_row("select fenleiId,channelId from demo_product_inventory where id=$inventoryId");
                            $jilupdts = array();$jiluchanels = array();
                            if(!empty($jilu->pdts))$jilupdts = explode(',', $jilu->pdts);
                            if(!empty($jilu->channels))$jiluchanels = explode(',', $jilu->channels);
                            if(in_array($inventoryId,$jilupdts) || in_array($inventory->fenleiId,$jiluchanels)){
                                $money += $pdt->price*$pdt->num;
                            }
                        }
                    }
                }else if(!empty($jilu->mendianIds)){
                    foreach ($pdts as $pdts1) {
                        foreach ($pdts1 as $inventoryId => $pdt) {
                            if($pdt->comId==$jilu->mendianIds){
                                $money += $pdt->price*$pdt->num;
                            }
                        }
                    }
                }
                $money = round($money,2);
                //file_put_contents('request.txt',$money.'-'.$yhq->man.'-'.$zong_price);
                if($money<$yhq->man || $money==0){
                    continue;
                }
                $return[] = $yhq;
            }
        }
        $arry = json_decode(json_encode($return,JSON_UNESCAPED_UNICODE),true);
        $columns = array_column($arry,'jian');
        array_multisort($columns,SORT_DESC,$arry);
        return $arry;
    }
    //获取可用礼品卡
    function get_lipinkas($pdts){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $return = array();
        $now = date("Y-m-d H:i:s");
        $yhqs = $db->get_results("select id,typeInfo,yue,mendianId,channels,pdts,endTime from lipinka where userId=$userId and (endTime is NULL or endTime>='$now') and yue>0 order by id desc");

        if(!empty($yhqs)){
            foreach ($yhqs as $yhq) {
                $yhq->kedi = 0;
                foreach ($pdts as $pdts1) {
                    foreach ($pdts1 as $inventoryId => $pdt) {
                        if(empty($yhq->mendianId) && empty($yhq->channels) && empty($yhq->pdts)){
                            $yhq->kedi+=$pdt->price*$pdt->num;
                        }else{
                            $jilupdts = array();$jiluchanels = array();
                            if(!empty($yhq->pdts))$jilupdts = explode(',', $yhq->pdts);
                            if(!empty($yhq->channels))$jiluchanels = explode(',', $yhq->channels);
                            $inventory = $db->get_row("select fenleiId,channelId from demo_product_inventory where id=$inventoryId");
                            if(!empty($yhq->pdts) && in_array($inventoryId,$jilupdts)){
                                $yhq->kedi+=$pdt->price*$pdt->num;
                            }else if(!empty($yhq->channels)){
                                if(empty($yhq->mendianId) && in_array($inventory->fenleiId,$jiluchanels)){
                                    $yhq->kedi+=$pdt->price*$pdt->num;
                                }else if(!empty($yhq->mendianId) && in_array($inventory->channelId,$jiluchanels)){
                                    $yhq->kedi+=$pdt->price*$pdt->num;
                                }
                            }else if(empty($yhq->pdts) && empty($yhq->channels) && $pdt->comId==$yhq->mendianId){
                                $yhq->kedi+=$pdt->price*$pdt->num;
                            }
                        }
                    }
                }
                if($yhq->kedi>$yhq->yue){
                    $yhq->kedi = $yhq->yue;
                }
                if($yhq->kedi>0)$return[] = $yhq;
            }
        }
        $arry = json_decode(json_encode($return,JSON_UNESCAPED_UNICODE),true);
        $columns = array_column($arry,'kedi');
        array_multisort($columns,SORT_DESC,$arry);
        return $arry;
    }
    //获得积分
    public static function get_order_jifen($pdts,$zong_price){
        global $db,$comId;
        $jifen_rule = $db->get_row("select jifen_type,jifen_content from user_shezhi where comId=$comId");
        $jifen = 0;
        if(!empty($jifen_rule->jifen_content)){
            $rule = json_decode($jifen_rule->jifen_content);
            switch ($jifen_rule->jifen_type) {
                case 1:
                    $jifen = floor($zong_price/$rule->money);
                    if($rule->shangxin>0 && $jifen>$rule->shangxin){
                        $jifen = $rule->shangxin;
                    }
                    break;
                case 2:
                    $jifen = floor($zong_price/$rule->man)*$rule->song;
                    break;
                case 3:
                    foreach ($pdts as $pdts1) {
                        foreach ($pdts1 as $inventoryId => $pdt) {
                            $channelId = (int)$db->get_var("select channelId from demo_product_inventory where id=$inventoryId");
                            $fuChannel = (int)$db->get_var("select parentId from demo_product_channel where id=$channelId");
                            foreach ($rule->items as $item) {
                                $channels = array();
                                $pdts = array();
                                if(!empty($item->channels)){
                                    $channels = explode(',',$item->channels);
                                }
                                if(!empty($item->pdts)){
                                    $pdts = explode(',',$item->pdts);
                                }
                                if(in_array($inventoryId,$pdts) || in_array($channelId, $channels) || in_array($fuChannel, $channels)){
                                    $jifen+=$item->jifen * $pdt->num;
                                    break;
                                }
                            }
                        }
                    }
                    break;
            }
        }
        return $jifen;
    }
    public static function get_36id($char){
        $num = intval($char);
        if ($num <= 0)
            return false;
        $charArr = array("1","2","3","4","5","6","7","8","9",'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $char = '';
        do {
            $key = ($num - 1) % 35;
            $char= $charArr[$key] . $char;
            $num = floor(($num - $key) / 35);
            //echo $num;
        } while ($num > 0);
        $char = buling($char,6);
        return $char;
    }
    //36进制转10进制
    function get_hexiao_id($char){
        $array=array("1","2","3","4","5","6","7","8","9","A", "B", "C", "D","E", "F", "G", "H", "I", "J", "K", "L","M", "N", "O","P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y","Z");
        while (true) {
            if(substr($char,0,1)=='0'){
                $char = substr($char,1);
            }else{
                break;
            }
        }
        $len=strlen($char);
        for($i=0;$i<$len;$i++){
            $index=array_search($char[$i],$array);
            $sum+=($index+1)*pow(35,$len-$i-1);
        }
        return $sum;
    }

    //卡券列表
    public function cardList(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $token = $request['token'];
        $status= $request['status'];
        $is_give = $request['is_give'];
        $is_end = $request['is_end'];
        $type= $request['type'];
        $page = (int)$request['page'];
        $pageNum = (int)$request['pagenum'];
        if($page<1)$page=1;
        if(empty($pageNum))$pageNum=20;
        $where= "";
        if($type == 1){
            $where .= " AND status <= 1";
        }else{
            $where .= " AND status > 2";
        }
        if($status){
            $where .= " AND status = $status";
        }
        if($is_give){
            $where .= " AND is_give = 1";
        }else{
            $where .= " AND is_give = 0";
        }
        if($is_end){
            $where .= " AND end_at <".time();
        }
        $sql = "select * from cards where userId =$userId $where ";
        $res = $db->get_results($sql." limit ".(($page-1)*$pageNum).",".$pageNum);
        $count = $db->get_var(str_replace('*','count(id)',$sql));
        foreach ($res as $k=>$v){
            $product_json = json_decode($v->product_json,true);
            $res[$k]->product_json = $product_json;
            $res[$k]->address = json_decode($v->address,true);
            $res[$k]->created_at = date('Y-m-d H:is',$v->created_at);
            $res[$k]->end_at = date('Y-m-d H:i:s',$v->end_at);
            if($v->use_at){
                $res[$k]->use_at = date('Y-m-d H:i:s',$v->use_at);
            }

        }
        $return['code'] = 1;
        $return['data'] = $res;
        $return['count'] = $count;
        $return['message'] = '请求成功';
        return json_encode($return,JSON_UNESCAPED_UNICODE);

    }

    //添加卡券
    public function addCard(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $token = $request['token'];
        $code = $request['code'];
        $pass = $request['pass'];
        $bool = $db->get_row("select * from code where code='$code' AND pass= '$pass'");
        if(!$bool){
            return '{"code":0,"message":"卡密不存在！"}';
        }
        if($bool->is_use == 1){
            return '{"code":0,"message":"卡密已使用！"}';
        }


        $time =  strtotime($bool->endTime);
        if($time < time()){
            return '{"code":0,"message":"卡密已过期！"}';
        }
        $product = $db->get_row("select * from demo_product_inventory where id=$bool->productId");
        $product_json['id'] = $product->productId;
        $product_json['inventory_id'] = $product->id;
        $product_json['title'] = $product->title;
        $product_json['remark'] = $product->remark;
        $product_json['image'] = $product->image;
        $data['userId'] = $userId;
        $data['is_card'] = 1;
        $data['productId'] = $bool->productId;
        $data['product_json'] =json_encode($product_json,JSON_UNESCAPED_UNICODE);
        $data['created_at'] = time();
        $data['end_at'] = $time;

        $db->insert_update('cards',$data,'id');
        $updated_at = date('Y-m-d H:i:s',time());
        $db->query("update code set is_use=1, updated_at = '$updated_at' where id=$bool->id");
        $return['code'] = 1;
        $return['message'] = '请求成功';

        return json_encode($return,JSON_UNESCAPED_UNICODE);

    }

    // 转增卡券
    public function giveCard(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $token = $request['token'];
        $mobile = $request['mobile'];
        $id = $request['id'];
        $cards = $db->get_row("select * from cards where id=$id AND userId= $userId");
        if(!$cards){
            return '{"code":0,"message":"礼品卡不存在！"}';
        }
        if($cards->status > 1){
            return '{"code":0,"message":"礼品卡已使用！"}';
        }
        if($cards->is_give == 1){
            return '{"code":0,"message":"礼品卡已转赠！"}';
        }
        $giveUserId = $db->get_var("select id from users where username='$mobile'");
        if(!$giveUserId){
            return '{"code":0,"message":"转赠人不存在！"}';
        }
        $time = time();
        $db->query("update cards set is_give=1, use_at= $time,give_user = '$mobile' where id=$cards->id");

        $data['userId'] = $giveUserId;
        $data['orderId'] = $cards->orderId;
        $data['is_give'] = 0;
        $data['status'] = $cards->status;
        $data['productId'] = $cards->productId;
        $data['product_json'] = $cards->product_json;
        $data['created_at'] = $time;
        $db->insert_update('cards',$data,'id');
        $return['code'] = 1;
        $return['message'] = '请求成功';
        return json_encode($return,JSON_UNESCAPED_UNICODE);

    }

    // 使用卡券
    public function useCard(){
        global $db,$request,$comId;
        $userId = (int)$request['user_id'];
        $token = $request['token'];
        $id = $request['id'];
        //var_dump($request);die;
        $mobile = $request['mobile'];
        $name = $request['name'];
        $address = $request['address'];
        $content = $request['content'];
        $cards = $db->get_row("select * from cards where id=$id AND userId= $userId");
        if(!$cards){
            return '{"code":0,"message":"礼品卡不存在！"}';
        }
        // if($cards->status > 1){
        //     return '{"code":0,"message":"礼品卡已使用！"}';
        // }
        if($cards->is_give == 1){
            return '{"code":0,"message":"礼品卡已转赠！"}';
        }

        $data['id'] = $id;
        $user_address['name'] = $name;
        $user_address['mobile'] = $mobile;
        $user_address['address'] = $address;
        $data['address'] = json_encode($user_address,JSON_UNESCAPED_UNICODE);
        $data['status'] =3;
        $data['use_no'] = $comId.'-'.time().$id;
        $data['content'] = $content;
        $data['use_at'] = time();
        //$db->insert_update('cards',$data,'id');
        //todo 做下单流程和发货单生成
        self::createCardOrder($userId, $cards->productId, $user_address, $id, $content);
        $return['code'] = 1;
        $return['message'] = '请求成功';
        return json_encode($return,JSON_UNESCAPED_UNICODE);

    }

    private function createCardOrder($userId, $inventoryId, $user_address, $cardId, $content)
    {
        global $db,$request,$comId;
        $order_comId = $comId;

        $num = 1;
        $jifen = 0;

        $inventory = $db->get_row("select id,productId,title,sn,key_vals,price_sale,price_market,price_gonghuo,weight,image,status,comId,price_card,price_cost,fanli_shequ,fanli_tuanzhang,price_tuan,price_shequ_tuan,tuan_num from demo_product_inventory where id=$inventoryId");
        $pdt = array();
        $pdt['id'] = $inventory->id;
        $pdt['productId'] = $inventory->productId;
        $pdt['title'] = $inventory->title;
        $pdt['sn'] = $inventory->sn;
        $pdt['key_vals'] = $inventory->key_vals;
        $pdt['weight'] = $inventory->weight;
        $pdt['num'] = $num;
        $pdt['jifen'] = $jifen;
        $pdt['image'] = ispic($inventory->image);
        $pdt['price_sale'] = $inventory->price_sale;
        $pdt['price_market'] = getXiaoshu($inventory->price_market,2);

        //$pdt['price_card'] = $inventory->price_card;
        $units = $db->get_var("select untis from demo_product where id=$inventory->productId");
        $units_arr = json_decode($units);
        $pdt['unit'] = $units_arr[0]->title;
        $product_json_arry[] = $pdt;

        $areaId = 0;
        $storeId = \Zhishang\Product::get_fahuo_store($areaId,$order_comId);
        $shouhuo_json = array();
        if(!empty($user_address)){
            $shouhuo_json['收件人'] = $user_address['name'];
            $shouhuo_json['手机号'] = $user_address['mobile'];
            $shouhuo_json['所在地区'] = $user_address['address'];
            $shouhuo_json['详细地址'] = '';
        }
        $product_json = json_encode($product_json_arry,JSON_UNESCAPED_UNICODE);
        $order = array();
        $order['orderId'] = $order_comId.'_'.date("YmdHis").rand(10000,99999);
        $order['userId'] = $userId;
        $order['comId'] = (int)$order_comId;
        $order['mendianId'] = 0;
        $order['yushouId'] = 0;
        $order['type'] = 1; //2社区团 1普通订单或普通团单
        $order['status'] = 2;//待支付
        $pay_json = array();
        $pay_json['cards']['price'] = 0;
        $pay_json['cards']['desc'] = $cardId;
        $order['pay_json'] = json_encode($pay_json,JSON_UNESCAPED_UNICODE);
        $order['dtTime'] = date("Y-m-d H:i:s");
        $order['remark'] = $content;
        $order['pay_endtime'] = date("Y-m-d H:i:s");
        $order['price'] = 0;
        $order['inventoryId'] = (int)$inventory->id;
        $order['storeId'] = $storeId;
        $order['pdtNums'] = $num;
        $order['pdtChanel'] = 0;
        $order['ifkaipiao'] = 0;
        $order['weight'] = 0;
        $order['jifen'] = 0;
        $order['areaId'] = $areaId;
        $order['address_id'] = $address_id;
        $order['product_json'] = $product_json;
        $order['shuohuo_json'] = json_encode($shouhuo_json,JSON_UNESCAPED_UNICODE);
        $order['price_json'] = '';
        $order['fanli_json'] = '';
        $order['ifkaipiao'] = 0;
        $order['if_zong'] = 0;
        $order['ispay'] = 1;
        $order['shequ_id'] = $shequ_id = (int)$db->get_var("select shequ_id from users where id=$userId");
        $order['peisong_type'] = 0;
        $order['peisong_time'] = '';
        $order['tuan_id'] = 0;
        $order_fenbiao = getFenbiao($order_comId,20);
        //file_put_contents('request.txt',$fenbiao.$order_fenbiao.json_encode($order,JSON_UNESCAPED_UNICODE));
        $db->insert_update('order'.$order_fenbiao,$order,'id');
        $order_id = $db->get_var("select last_insert_id();");
        $db->query("update cards set orderId = '".$order['orderId']."' where id = $cardId");
        /*if(!empty($xiangou_sql1)){
			$xiangou_sql1 = str_replace('order_id', $order_id, $xiangou_sql1);
	    	$xiangou_sql1 = substr($xiangou_sql1,1);
	    	$db->query($xiangou_sql.$xiangou_sql1);
	    }
		$timed_task = array();
		$timed_task['comId'] = (int)$_SESSION['demo_comId'];
		$timed_task['dtTime'] = $check_pay_time;
		$timed_task['router'] = 'order_checkPay';
		$timed_task['params'] = '{"order_id":'.$order_id.'}';
		$db->insert_update('demo_timed_task',$timed_task,'id');*/
        /*if(!empty($yhq_id)){
			$db->query("update user_yhq$fenbiao set status=1,orderId=$order_id where id=$yhq_id");
		}*/
        foreach ($product_json_arry as $detail) {
            $pdt = new \StdClass();
            $pdt->sn = $detail['sn'];
            $pdt->title = $detail['title'];
            $pdt->key_vals = $detail['key_vals'];
            $order_detail = array();
            $order_detail['comId'] = (int)$order_comId;
            $order_detail['mendianId'] = 0;
            $order_detail['userId'] = $userId;
            $order_detail['orderId'] = $order_id;
            $order_detail['inventoryId'] = $detail['id'];
            $order_detail['productId'] = $detail['productId'];
            $order_detail['pdtInfo'] = json_encode($pdt,JSON_UNESCAPED_UNICODE);
            $order_detail['num'] = $detail['num'];
            $order_detail['unit'] = $detail['unit'];
            $order_detail['unit_price'] = $detail['price_sale'];
            $order_detail['status'] = 1;
            $db->insert_update('order_detail'.$order_fenbiao,$order_detail,'id');
            if(!empty($gouwuches[$detail['id']]))unset($gouwuches[$detail['id']]);
            if($tuan_type==0){
                $db->query("update demo_kucun set yugouNum=yugouNum+".$detail['num']." where inventoryId=".$detail['id']." and storeId=$storeId limit 1");
            }
            $db->query("update demo_product_inventory set orders=orders+$num where id=".$detail['id']);
            $db->query("update demo_product set orders=orders+$num where id=".$detail['productId']);
        }
        //发货
        $fahuo = array();
        $fahuo['comId'] = $comId;
        $fahuo['mendianId'] = 0;
        $fahuo['addressId'] = $address_id;
        $fahuo['orderId'] = date("YmdHis").rand(1000000000,9999999999);
        $fahuo['orderIds'] = $order_id;
        $fahuo['type'] = 1;
        $fahuo['showTime'] = date("Y-m-d H:i:s");
        $fahuo['storeId'] = $storeId;
        $fahuo['dtTime'] = date("Y-m-d H:i:s");
        $fahuo['shuohuo_json'] = $order['shuohuo_json'];
        $fahuo['productId'] = 0;
        $fahuo['tuanzhang'] = $userId;
        $fahuo['product_title'] = $inventory->title;
        $fahuo['fahuo_title'] = $inventory->title;
        $fahuo['product_num'] = $num;
        $fahuo['weight'] = 0;
        $fahuo['remark'] = $content;
        $fahuo['areaId'] = $areaId;
        $fahuo['shequ_id'] = $shequ_id;
        $db->insert_update('order_fahuo'.$order_fenbiao,$fahuo,'id');
        $fahuoId = $db->get_var("select last_insert_id();");
        $db->query("update order$order_fenbiao set fahuoId=$fahuoId where id=$order_id");

        return '{"code":1,"message":"兑换成功","order_id":'.$order_id.'}';
    }
    
    public function fenHongTaskNew()
    {
        global $db,$comId,$request; 
        
        /*
        * 返利逻辑（最新版）：
        * 1.计算个人销售佣金  拿到上月个人业绩 和 佣金金额
        * 2.计算下级低于10万的下级业绩总和，然后乘以对应的分润比例，然后在减去每个下级推广应拿佣金金额
        * 3.计算个人直推佣金  拿到上月个人直推佣金总额 和 直推订单总金额
        * 4.计算个人间推佣金  拿到上月个人间推佣金总额 和 间推订单总金额
        * 5.1号先计算上月分润，不发送实际分润，只做记录。8号删除1号记录，重新计算分润，发送实际分润，记录日志
        */
        //返利逻辑 查询当前用户业绩，确定返利等级,业绩超过10万， 不统计该用户的业绩 上级业绩 减去下级业绩为当前返利
        //获取配置
        $fenbiao = getFenbiao($comId,20);
        
        $config = $db->get_results("select * from zc_release where id > 0");
        //获取vip 用户级别以上业绩
        $users = $db->get_results("select id,order_price from users where level >=74 and id = 16 ");
        // echo '<pre>';

        echo 'start';
         
       
        
        $sw_price = 100000;    //达标 
        $startTime = date('Y-m-01 00:00:00', strtotime('-1 month'));
        $endTime = date('Y-m-01 00:00:00');
        $endSecond = strtotime($endTime) - 1;
        $endTime = date('Y-m-d H:i:s', $endSecond);
        // var_dump($startTime,$endTime);die;
        // $startTime = date('Y-m-01 00:00:00');
        // $endTime = date('Y-m-01 00:00:00', strtotime('+1 month'));

        $ifSend = $request['if_send'];
        if($ifSend){//真正要发的时候，先删除老数据
            $db->query("delete from user_month_fenhong where startTime = '$startTime' ");
        }
        foreach($users as $k=>$v){
            $userId = $v->id;
            $selfTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $userId and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $userId ");
            $selfTotal = $selfTotal ? $selfTotal : 0;
            // var_dump($selfTotal);
            $bili = $selfFenhong = 0;
            if($selfTotal > 0){
                foreach($config as $kk=>$vv){ 
                    if($selfTotal >= $vv->min && $selfTotal < $vv->max){
                         $bili = $vv->bili ;
                    }
                }
                $selfFenhong = bcmul($selfTotal, $bili, 2);
            }

            if($selfFenhong > 0 && $ifSend){     
                //本月业绩
                $db->query("update users set money=money+$selfFenhong,earn=earn+$selfFenhong where id=".$userId);
                $liushui = array();
                $liushui['userId']=$userId;
                $liushui['comId']=$comId;
                $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                $liushui['order_num']  = $selfFenhong;
                $liushui['total_order_num']= $selfTotal;
                $liushui['total_order_num_bak']= $selfTotal;
                $liushui['bili']=$bili;
                $liushui['dtTime']=date("Y-m-d H:i:s");
                $liushui['time']=date("Y年m月d日");
                $liushui['remark']='销售佣金';
                $db->insert_update('user_tuan_fenhong' , $liushui, 'id');   
   
                $liushui = array();
                $liushui['userId']=$userId;
                $liushui['comId']=$comId;
                $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                $liushui['money']  = $selfFenhong;
                $liushui['yue']= $db->get_var('select money from users where id='.$userId);
                //类型(0所有   1消费   2收入    3提现)
                $liushui['type']=2;
                $liushui['dtTime']=date("Y-m-d H:i:s");
                $liushui['remark']=date("Y年m月d日" ).'销售佣金分润';
                $liushui['orderInfo']=date("Y年m月d日" ).'销售佣金分润';
                $liushui['order_id']='';
                $liushui['from_user']='';
                $db->insert_update('user_liushui8' , $liushui, 'id');  

                echo '用户销售分红id：'.$userId.' 分红:'.$selfFenhong;
            }

            $user_price = $xiaji_price = $xiajisub_price = $bili = 0;
            //查询所有下级  大于10万用户
            $user_xiaji = $db->get_results("select id,nickname,level from users where  shangji =$v->id ");
            foreach($user_xiaji as $user_xiaji_k=>$user_xiaji_v){
                //当前用户 返利金额减去所有下级 返利金额
                $childTotal = $db->get_var("select sum(money) from user_tuan_price where userId = $user_xiaji_v->id and dtTime > '$startTime' and dtTime < '$endTime' and from_user = $user_xiaji_v->id ");
                $childTotal = $childTotal ? $childTotal : 0;
                $xiaji_price = bcadd($xiaji_price, $childTotal, 2);
                foreach($config as $kk=>$vv){
                    if($childTotal >= $vv->min && $childTotal < $vv->max){
                        $childFenhong = bcmul($vv->bili, $childTotal, 2);
                        $xiajisub_price = bcadd($xiajisub_price, $childFenhong, 2);
                    }
                }        
            }
            
            foreach($config as $kk=>$vv){ 
                //当前用户返利金额及比例
                if($xiaji_price >= $vv->min && $xiaji_price < $vv->max){
                     $bili = $vv->bili ;
                }
            }
            
            $user_price1 = bcmul($xiaji_price, $bili, 2);
            $user_price = bcsub($user_price1, $xiajisub_price, 2);
            if($user_price > 0 && $ifSend){     
                //本月业绩
                    $db->query("update users set money=money+$user_price,earn=earn+$user_price,is_dabiao = 0 where id=".$v->id);
                    $liushui = array();
                    $liushui['userId']=$v->id;
                    $liushui['comId']=$comId;
                    $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                    $liushui['order_num']  = $user_price;
                    $liushui['total_order_num']= $xiaji_price;
                    $liushui['total_order_num_bak']= $user_price1;
                    $liushui['bili']=$bili;
                    $liushui['dtTime']=date("Y-m-d H:i:s");
                    $liushui['time']=date("Y年m月d日");
                    $liushui['remark']='团队分红';
                    $db->insert_update('user_tuan_fenhong' , $liushui, 'id');   
                    
                    $liushui = array();
                    $liushui['userId']=$v->id;
                    $liushui['comId']=$comId;
                    $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                    $liushui['money']  = $user_price;
                    $liushui['yue']= $db->get_var('select money from users where id='.$v->id);
                    //类型(0所有   1消费   2收入    3提现)
                    $liushui['type']=2;
                    $liushui['dtTime']=date("Y-m-d H:i:s");
                    $liushui['remark']=date("Y年m月d日" ).'新增业绩分润';
                    $liushui['orderInfo']=date("Y年m月d日" ).'新增业绩分润';
                    $liushui['order_id']='';
                    $liushui['from_user']='';
                    $db->insert_update('user_liushui8' , $liushui, 'id');  
                    
                    echo '用户新增业绩分红id：'.$v->id.' 分红:'.$user_price;
            }
            
            $directTotal = $directFenhong = $indirectTotal = $indirectFenhong = 0;
            $directTotal = $db->get_var("select sum(l.order_price) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '直推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            $directTotal = $directTotal ? $directTotal : 0;
            if($directTotal > 0){
                $directFenhong = $db->get_var("select sum(l.money) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '直推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            }
            
            $indirectTotal = $db->get_var("select sum(l.order_price) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '间推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            $indirectTotal = $indirectTotal ? $indirectTotal : 0;
            if($indirectTotal > 0){
                $indirectFenhong = $db->get_var("select sum(l.money) from order$fenbiao o inner join user_tuan_price l on l.order_id = o.id where l.userId = $userId and l.remark = '间推返利' and l.dtTime > '$startTime' and l.dtTime < '$endTime' ");
            }
            
            if($directFenhong > 0 && $ifSend){     
                //本月业绩
                $db->query("update users set money=money+$directFenhong,earn=earn+$directFenhong,is_dabiao = 0 where id=".$v->id);
                $liushui = array();
                $liushui['userId']=$v->id;
                $liushui['comId']=$comId;
                $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                $liushui['order_num']  = $directFenhong;
                $liushui['total_order_num']= $directTotal;
                $liushui['total_order_num_bak']= $directTotal;
                $liushui['bili']=$bili;
                $liushui['dtTime']=date("Y-m-d H:i:s");
                $liushui['time']=date("Y年m月01日");
                $liushui['remark']='直推返利';
                $db->insert_update('user_tuan_fenhong' , $liushui, 'id');   
                
                $liushui = array();
                $liushui['userId']=$v->id;
                $liushui['comId']=$comId;
                $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                $liushui['money']  = $directFenhong;
                $liushui['yue']= $db->get_var('select money from users where id='.$v->id);
                //类型(0所有   1消费   2收入    3提现)
                $liushui['type']=2;
                $liushui['dtTime']=date("Y-m-d H:i:s");
                $liushui['remark']=date("Y年m月d日" ).'直推返利';
                $liushui['orderInfo']=date("Y年m月d日" ).'直推返利';
                $liushui['order_id']='';
                $liushui['from_user']='';
                $db->insert_update('user_liushui8' , $liushui, 'id');  
                
                echo '用户直推返利id：'.$v->id.' 分红:'.$directFenhong;
            }
            
            if($indirectFenhong > 0 && $ifSend){     
                //本月业绩
                $db->query("update users set money=money+$indirectFenhong,earn=earn+$indirectFenhong,is_dabiao = 0 where id=".$v->id);
                $liushui = array();
                $liushui['userId']=$v->id;
                $liushui['comId']=$comId;
                $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                $liushui['order_num']  = $indirectFenhong;
                $liushui['total_order_num']= $indirectTotal;
                $liushui['total_order_num_bak']= $indirectTotal;
                $liushui['bili']=$bili;
                $liushui['dtTime']=date("Y-m-d H:i:s");
                $liushui['time']=date("Y年m月01日");
                $liushui['remark']='间推返利';
                $db->insert_update('user_tuan_fenhong' , $liushui, 'id');   
                
                $liushui = array();
                $liushui['userId']=$v->id;
                $liushui['comId']=$comId;
                $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                $liushui['money']  = $indirectFenhong;
                $liushui['yue']= $db->get_var('select money from users where id='.$v->id);
                //类型(0所有   1消费   2收入    3提现)
                $liushui['type']=2;
                $liushui['dtTime']=date("Y-m-d H:i:s");
                $liushui['remark']=date("Y年m月d日" ).'间推返利';
                $liushui['orderInfo']=date("Y年m月d日" ).'间推返利';
                $liushui['order_id']='';
                $liushui['from_user']='';
                $db->insert_update('user_liushui8' , $liushui, 'id');  
                
                echo '用户间推返利id：'.$v->id.' 分红:'.$indirectFenhong;
            }
            
            $totalFenhong = bcadd($selfFenhong, $user_price, 2);
            $totalFenhong = bcadd($totalFenhong, $directFenhong, 2);
            $totalFenhong = bcadd($totalFenhong, $indirectFenhong, 2);
            $fenhongLog = array(
                'userId' => $userId,
                'startTime' => $startTime,
                'endTime' => $endTime,
                'self_total' => $selfTotal,
                'self_fenhong' => $selfFenhong,
                'team_total' => $xiaji_price,
                'team_fenhong' => $user_price,
                'direct_total' => $directTotal,//直推业绩
                'direct_fenhong' => $directFenhong,//直推分红
                'indirect_total' => $indirectTotal,//间推业绩
                'indirect_fenhong' => $indirectFenhong,//间推分红
                'total_fenhong' => $totalFenhong,
                'dtTime' => date('Y-m-d H:i:s')
            );

            $db->insert_update('user_month_fenhong', $fenhongLog, 'id');
        }
        
        if($ifSend == 0){
            $db->query("update users set order_price = 0  where id > 0");
        }

        echo 'end';
    }

    //每月1号凌晨定时统计分红
    public function fenHongTask(){
        global $db,$comId; 
        die;
        //返利逻辑 查询当前用户业绩，确定返利等级,业绩超过10万， 不统计该用户的业绩 上级业绩 减去下级业绩为当前返利
        //获取配置
        $config = $db->get_results("select * from zc_release where id > 0");
        //获取vip 用户级别以上业绩
        $users = $db->get_results("select id,order_price from users where level >=74");
        
         echo 'start';
         

        $sw_price = 100000;    //达标 
        

        foreach($users as $k=>$v){
            $user_price = $xiaji_price = $bili = 0;
            //查询所有下级  大于10万用户
            $xiaji_order_price = $db->get_var("select sum(order_price) from users where level >=74 and shangji =$v->id and order_price >=$sw_price");
            foreach($config as $kk=>$vv){ 
                //当前用户返利金额及比例
                
                if($v->order_price > $vv->min && $v->order_price < $vv->max){
                   
                     //$totail_money = $user_price = $vv->bili * $v->order_price;
                     $bili = $vv->bili ;
  
                }
            }
            $user_price = ($v->order_price - $xiaji_order_price) * $bili;
            
            $user_xiaji = $db->get_results("select sum(order_price) order_price from users where level >=74 and shangji =$v->id and order_price < $sw_price");
            foreach($user_xiaji as $user_xiaji_k=>$user_xiaji_v){
                //当前用户 返利金额减去所有下级 返利金额
                foreach($config as $kk=>$vv){
                    if($user_xiaji_v->order_price > $vv->min && $user_xiaji_v->order_price < $vv->max){
                        $user_price = $user_price - $vv->bili * $user_xiaji_v->order_price;
                    }
                }        
            }
            
            if($user_price > 0){     
                //本月业绩
                    $db->query("update users set money=money+$user_price,earn=earn+$user_price,is_dabiao = 0 where id=".$v->id);
                    $liushui = array();
                    $liushui['userId']=$v->id;
                    $liushui['comId']=$comId;
                    $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                    $liushui['order_num']  = $user_price;
                    $liushui['total_order_num']= $v->order_price - $xiaji_order_price;
                    $liushui['total_order_num_bak']= $v->order_price;
                    $liushui['bili']=$bili;
                    $liushui['dtTime']=date("Y-m-d H:i:s");
                    $liushui['time']=date("Y年m月d日");
                    $liushui['remark']='团队分红';
                    $db->insert_update('user_tuan_fenhong' , $liushui, 'id');   
                    
                    
                    $liushui = array();
                    $liushui['userId']=$v->id;
                    $liushui['comId']=$comId;
                    $liushui['orderId']= date("YmdHis").rand(1000000000,9999999999);
                    $liushui['money']  = $user_price;
                    $liushui['yue']= $db->get_var('select money from users where id='.$v->id);
                    //类型(0所有   1消费   2收入    3提现)
                    $liushui['type']=2;
                    $liushui['dtTime']=date("Y-m-d H:i:s");
                    $liushui['remark']=date("Y年m月d日" ).'新增业绩分润';
                    $liushui['orderInfo']=date("Y年m月d日" ).'新增业绩分润';
                    $liushui['order_id']='';
                    $liushui['from_user']='';
                    $db->insert_update('user_liushui8' , $liushui, 'id');  
                    
                    echo '用户分红id：'.$v->id.' 分红:'.$user_price;
            }
          
        }
          $db->query("update users set order_price = 0  where id > 0");
          echo 'end';
    }
    
    //售后原因
	public function reason(){
	    global $db,$request,$comId;
	    $qx_reason = $db->get_var("select qx_reason from demo_shezhi where comId = $comId");  
	    $return = array();
		$return['code'] = 1;
		$return['message'] = '返回数据';
		$return['data'] = explode('@_@',$qx_reason);
		return json_encode($return,JSON_UNESCAPED_UNICODE);
	}
    
    
    


}