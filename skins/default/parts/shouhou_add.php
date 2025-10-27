<?
global $db,$request;
$id = (int)$request['id'];
$comId = (int)$_SESSION['demo_comId'];
if(!empty($request['comId'])){
    $comId = (int)$request['comId'];
}
$fenbiao = getFenbiao($comId,20);
$order = $db->get_row("select price_payed,product_json,orderId,pdtNums,status,type from order$fenbiao where id=$id");
if(empty($order)||$order->status!=3){
    echo '<script>alert("该订单当前状态不支持售后申请");history.go(-1);</script>';
    exit;
}

$product_json = json_decode($order->product_json);
$reason = $db->get_var("select tuihuan_reason from demo_shezhi where comId=".($_SESSION['if_tongbu']==1?10:$comId));
$reasons = explode('@_@', $reason);
require_once "wxshare.php";
$jssdk = new JSSDK("wx7a91a4f2eccb30db", "368a5e47cb481c6aebfe0376ef71a463");
$signPackage = $jssdk->GetSignPackage();
?>
<div class="wode" style="background-color:#f6f6f6;">
	<div class="wode_1">
    	申请售后
        <div class="wode_1_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
    <form id="add_form" method="post" action="">
            <input type="hidden" name="type" id="type" value="2">
            <input type="hidden" name="reason" id="reason" value="">
            <div class="shouhoushenqing_1">
                <div class="shouhoushenqing_1_up">
                    服务类型
                </div>
                <div class="shouhoushenqing_1_down">
                    <ul>
                        <li>
                            <a href="javascript:" onclick="qiehuan_type(0,2)" class="shouhoushenqing_1_down_on">退货退款</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="qiehuan_type(1,3)">换货</a>
                        </li>
                        
                        <!-- <li>
                            <a href="javascript:" onclick="qiehuan_type(2,1)">退款补偿（无需退货）</a>
                        </li> -->
                        <div class="clearBoth"></div>
                    </ul>
                </div>
            </div>
        	<div class="shouhoushenqing">
                <? if(!empty($product_json)){
                    foreach ($product_json as $val) {
                        ?>
                        <div class="dingdanxiangqing_3">
                            <div class="querendingdan_3_img">
                                <a href="/index.php?p=4&a=view&id=<?=$val->id?>"><img src="<?=$val->image?>"/></a>
                            </div>
                            <div class="querendingdan_3_tt">
                                <div class="querendingdan_3_tt_01">
                                    <a href="/index.php?p=4&a=view&id=<?=$val->id?>"><?=$val->title.'【'.$val->key_vals.'】'?></a>
                                </div>
                                <div class="querendingdan_3_tt_02">
                                    <div class="querendingdan_3_tt_02_left">
                                        ¥ <b><?=$val->price_sale?></b> <span>¥<?=$val->price_market?></span>&nbsp;×<?=$val->num?>份
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                                <div class="querendingdan_3_tt_03">
                                    <div class="querendingdan_3_tt_02_left">
                                        <div class="shouhoushenqing_2_up_left" style="line-height:1rem">
                                            数量：
                                        </div>
                                        <div class="tuihuo_num" style="line-height:1rem"><?=$val->num?></div>
                                        <div class="shouhoushenqing_2_down_right huanhuo_num" style="display:none">
                                            <div class="shouhoushenqing_2_down_right_01" onclick="num_edit(-1,<?=$val->id?>);">
                                                <img src="/skins/default/images/shangpinxx_31.png"/>
                                            </div>
                                            <div class="shouhoushenqing_2_down_right_02">
                                                <input type="text" name="nums[<?=$val->id?>]" id="nums_<?=$val->id?>" readonly="true" data-max="<?=$val->num?>" value="<?=$val->num?>" />
                                            </div>
                                            <div class="shouhoushenqing_2_down_right_03" onclick="num_edit(1,<?=$val->id?>);">
                                                <img src="/skins/default/images/shangpinxx_32.png"/>
                                            </div>
                                            <div class="clearBoth"></div>
                                        </div>
                                    </div>
                                    <div class="clearBoth"></div>
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                    <? }
                }?>
        	<div class="shouhoushenqing_2">
            	<div class="shouhoushenqing_2_up">
                	<div class="shouhoushenqing_2_up_left">
                    	申请原因
                    </div>
                	<div class="shouhoushenqing_2_up_right" id="select_rdiv">
                    	请选择 <img src="/skins/default/images/biao_17.png"/>
                    </div>
                	<div class="clearBoth"></div>
                </div>
                <div class="shouhoushenqing_2_up hide_3">
                    <div class="shouhoushenqing_2_up_left">
                        退款金额
                    </div>
                    <div class="shouhoushenqing_2_up_right">
                        <input type="number" step="0.01" name="money" id="money" readonly="true" value="<?=$order->price_payed?>" placeholder="请提前与客服沟通好金额" style="width:100%;height:1.2rem;text-align:right;color:#e72551;border:0px;">
                    </div>
                    <div class="clearBoth"></div>
                </div>
            	<!-- <div class="shouhoushenqing_2_down hide_1">
                	<div class="shouhoushenqing_2_up_left">
                    	提交数量
                    </div>
                	<div class="shouhoushenqing_2_down_right">
                    	<div class="shouhoushenqing_2_down_right_01" onclick="num_edit(-1);">
                        	<img src="/skins/default/images/querendingdan_12.png"/>
                        </div>
                    	<div class="shouhoushenqing_2_down_right_02">
                        	<input type="text" name="nums" id="nums" readonly="true" data-max="<?=$order->pdtNums?>" value="<?=$order->pdtNums?>" />
                        </div>
                    	<div class="shouhoushenqing_2_down_right_03" onclick="num_edit(1);">
                        	<img src="/skins/default/images/querendingdan_13.png"/>
                        </div>
                    	<div class="clearBoth"></div>
                    </div>
                	<div class="clearBoth"></div>
                </div> -->
                <div class="shouhoushenqing_2_down hide_1">
                    <div class="shouhoushenqing_2_up_left">
                        运费负责：
                    </div>
                    <div class="shouhoushenqing_2_up_right">
                        <input type="radio" name="yffz" value="2" onclick="change_kuaidi_type(2)" /> <label for="yffz1">商家</label>
                        &nbsp;&nbsp;
                        <input type="radio" name="yffz" value="1" onclick="change_kuaidi_type(1)" /> <label for="yffz2">自理</label>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="shouhoushenqing_2_down hide_1" id="yunfei_div">
                    <div class="shouhoushenqing_2_up_left">
                        运费：
                    </div>
                    <div class="shouhoushenqing_2_down_right" style="position:relative;top:.6rem;">
                        <div class="shouhoushenqing_2_down_right_02" style="width:100%">
                            <input type="number" name="kuaidi_money" id="kuaidi_money" />
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="clearBoth"></div>
                </div>
            </div>
        	<div class="shouhoushenqing_3">
            	<div class="shouhoushenqing_3_01">
                	问题描述
                </div>
            	<div class="shouhoushenqing_3_02">
                	<div class="shouhoushenqing_3_02_up">
                    	<textarea name="remark" id="remark" cols="30" rows="10" maxlength="300" placeholder="请您详细填写申请说明···"></textarea>
                    </div>
                </div>
            	<div class="shouhoushenqing_3_03">
                	<ul>
                        <li style="position:relative;width:2.825rem;" id="add_img_li" onclick="showPic(this);">
                        	<img src="/skins/default/images/shenqingshouhou_1.gif" class="shouhoushenqing_3_03_more"/>
                        </li>
                        <div class="clearBoth"></div>
                	</ul>
                </div>
            </div>
        	<div class="shouhoushenqing_4 hide_1">
            	<h2>商品退回方式</h2>
                商品寄回地址将在审核通过后以短信形式告知，或在申请记录中查询。
            </div>
        	<div class="shouhoushenqing_6">
            	<a href="javascript:" onclick="tijiao();">提交申请</a>
            </div>
            <input type="hidden" name="kuaidi_type" id="kuaidi_type" value="0">
        </form>
    </div>
</div>
<!--换货原因-->
<div class="shenqingshouhou_yuanyin_tc" id="shenqingshouhou_yuanyin_tc" style="display:none;">
	<div class="bj" style="background-color:rgba(0,0,0,0.8);" onclick="$('#shenqingshouhou_yuanyin_tc').hide();"></div>
	<div class="shenqingshouhou_yuanyin">
    	<div class="shenqingshouhou_yuanyin_1">
        	申请原因
        </div>
    	<div class="shenqingshouhou_yuanyin_2">
        	<span>换货</span>
        </div>
    	<div class="shenqingshouhou_yuanyin_3">
        	<ul style="max-height:16.3rem;overflow-y:auto;">
                <? foreach($reasons as $i=>$r){?>
                    <li>
                        <a href="javascript:" onclick="select_reason(<?=$i?>,'<?=$r?>');">
                            <div class="shenqingshouhou_yuanyin_3_left">
                                <?=$r?>
                            </div>
                            <div class="shenqingshouhou_yuanyin_3_right">
                                <img src="/skins/default/images/shenqingshouhou_11.png"/>
                            </div>
                            <div class="clearBoth"></div>
                        </a>
                    </li>
                <? }?>
        	</ul>
        </div>
    	<div class="shenqingshouhou_yuanyin_4">
        	<a href="javascript:" onclick="$('#shenqingshouhou_yuanyin_tc').hide();">确定</a>
        </div>
    </div>
</div>
<script type="text/javascript">
    var max_num = <?=$order->pdtNums?>;
    var price_payed = '<?=$order->price_payed?>';
    var price_sale = '<?=(int)($order->price_payed*100/$order->pdtNums)/100?>';
    var orderId = <?=$id?>;
    var comId = <?=$comId?>;
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage'
        ]
    });
    wx.ready(function () {
        wx.checkJsApi({
            jsApiList: [
                'chooseImage',
                'previewImage',
                'uploadImage',
                'downloadImage'
            ],
            success: function (res) {
                if (res.checkResult.getLocation == false) {
                    alert('你的微信版本太低，不支持微信JS接口，请升级到最新的微信版本！');
                    return;
                }else{
                    wxChooseImage();
                }
            }
        });
    });
    var images = {
        localId: [],
        serverId: []
    };
</script>
<script type="text/javascript" src="/skins/default/scripts/shouhou/add.js?v=1.2"></script>