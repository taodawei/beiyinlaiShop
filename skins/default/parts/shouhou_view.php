<?
global $db,$request;
$id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$tuihuan = $db->get_row("select * from order_tuihuan where id=$id");
if(empty($tuihuan) || $tuihuan->userId!=$userId){
    die("<script>alert('售后不存在');history.back();</script>");
}
$product_json = json_decode($tuihuan->pdtInfo);
$order = $db->get_row("select * from order0 where id=$tuihuan->orderId");
?>
<div class="wode">
	<div class="wode_1">
    	售后详情
        <div class="wode_1_left" onclick="history.back();">
        	<img src="/skins/default/images/sousuo_1.png"/>
        </div>
    </div>
	<div class="tuihuanhuoxx_daishenhe">
    	<div class="tuihuanhuoxx_daishenhe_1">
            <? if(!empty($product_json)){
                foreach ($product_json as $pdt) {
                    ?>
                    <div class="tuihuanhuoxx_daishenhe_1_img">
                        <a href="/index.php?p=4&a=view&id=<?=$pdt->id?>"><img src="<?=ispic($pdt->image)?>"/></a>
                    </div>
                    <div class="tuihuanhuoxx_daishenhe_1_tt">
                        <a href="/index.php?p=4&a=view&id=<?=$pdt->id?>"><?=$pdt->title.'<br>【规格：'.$pdt->key_vals.'】'?></a>
                        ¥ <b><?=$pdt->price_sale?></b>&nbsp;×<?=$pdt->nums?><?=$pdt->unit?>
                    </div>
                    <div class="clearBoth"></div>
                    <?
                }
            }?>
        </div>
    	<div class="tuihuanhuoxx_yishenhe_1">
        	<ul>
        		<li>	
                	<div class="tuihuanhuoxx_yishenhe_1_left">
                    	<img src="/skins/default/images/tuihuanhuoxx_12.png"/>
                    </div>
                	<div class="tuihuanhuoxx_yishenhe_1_right">
                    	<div class="tuihuanhuoxx_yishenhe_1_right_up">
                        	<?=$tuihuan->dtTime?>
                        </div>
                    	<div class="tuihuanhuoxx_yishenhe_1_right_down">
                        	您的申请提交成功<? if($tuihuan->status==1){?>，请耐心等待商家审核哦~<? }?>
                        </div>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <? if($tuihuan->status==-1){?>
                    <li>
                        <div class="tuihuanhuoxx_yishenhe_1_left">  
                            <img src="/skins/default/images/tuihuanhuoxx_12.png"/>
                        </div>
                        <div class="tuihuanhuoxx_yishenhe_1_right">
                            <div class="tuihuanhuoxx_yishenhe_1_right_up">
                                <?=$tuihuan->dealTime?>
                            </div>
                            <div class="tuihuanhuoxx_yishenhe_1_right_down">
                                您的售后申请未通过审核，原因：<br>
                                <span><?=$tuihuan->dealCont?></span><br>
                                <a href="/index.php?p=21&a=add&id=<?=$tuihuan->orderId?>">重新申请</a>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                <?}else if($tuihuan->status>1){
                    $genjin_json = json_decode($tuihuan->genjin_json,true);
                    if($tuihuan->type>1 && !empty($tuihuan->shouhuo_json)){
                        $shouhuo_json = json_decode($tuihuan->shouhuo_json,true);
                        ?>
                        <li>    
                            <div class="tuihuanhuoxx_yishenhe_1_left">  
                                <img src="/skins/default/images/tuihuanhuoxx_12.png">
                            </div>
                            <div class="tuihuanhuoxx_yishenhe_1_right">
                                <div class="tuihuanhuoxx_yishenhe_1_right_up">
                                    <?=$genjin_json[0]['time']?>
                                </div>
                                <div class="tuihuanhuoxx_yishenhe_1_right_down">
                                    您的换货申请审核成功，请将商品邮寄至<br>
                                    <span>地址：<?=$shouhuo_json['address']?></span><br>
                                    <span>收件人：<?=$shouhuo_json['name']?>      电话：<?=$shouhuo_json['phone']?></span><br>
                                    并填写物流公司、订单号（运费由<?=$tuihuan->kuaidi_type==1?'买家自行':'卖家'?>承担）
                                </div>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <?
                        if(!empty($tuihuan->fahuo_json)){
                            $fahuo_json = json_decode($tuihuan->fahuo_json,true);
                            ?>
                            <li>    
                                <div class="tuihuanhuoxx_yishenhe_1_left">  
                                    <img src="/skins/default/images/tuihuanhuoxx_12.png" alt="">
                                </div>
                                <div class="tuihuanhuoxx_yishenhe_1_right">
                                    <div class="tuihuanhuoxx_yishenhe_1_right_up">
                                        <?=$genjin_json[2]['time']?>
                                    </div>
                                    <div class="tuihuanhuoxx_yishenhe_1_right_down">
                                        卖家确认收货，已重新为您发货
                                        <br>物流公司：<?=$fahuo_json['company']?>
                                        <br>订单号：<?=$fahuo_json['orderId']?>
                                        <br>请注意查收！
                                    </div>
                                </div>
                                <div class="clearBoth"></div>
                            </li>
                            <?
                        }
                    }
                }
                if($tuihuan->status==6 && $tuihuan->type<3){
                    ?>
                    <li>    
                        <div class="tuihuanhuoxx_yishenhe_1_left">  
                            <img src="images/tuihuanhuoxx_12.png" alt=""/>
                        </div>
                        <div class="tuihuanhuoxx_yishenhe_1_right">
                            <div class="tuihuanhuoxx_yishenhe_1_right_up">
                                <?=$tuihuan->dealTime?>
                            </div>
                            <div class="tuihuanhuoxx_yishenhe_1_right_down">
                                申请处理完成，退款已退款至付款账户，请注意查收！
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <?
                }
                ?>
                
        	</ul>
        </div>
        <? if($tuihuan->type>1 && $tuihuan->status==2 && empty($tuihuan->kuaidi_json)){?>
    	<div class="tuihuanhuoxx_yishenhe_2">
        	*请填写物流公司及订单号，以便客服收到后查看确认
        </div>
    	<div class="tuihuanhuoxx_yishenhe_3">
        	<ul>
        		<li>
                	<div class="tuihuanhuoxx_yishenhe_3_left">
                    	<span>*</span> 物流公司
                    </div>
                	<div class="tuihuanhuoxx_yishenhe_3_right">
                    	<input type="text" id="kuaidi_company" placeholder="请输入快递公司"/>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="tuihuanhuoxx_yishenhe_3_left">
                    	<span>*</span> 物流单号
                    </div>
                	<div class="tuihuanhuoxx_yishenhe_3_right">
                    	<input type="text" id="kuaidi_orderId" placeholder="请输入物流单号"/>
                    </div>
                	<div class="clearBoth"></div>
                </li>
        	</ul>
        </div>
    	<div class="tuihuanhuoxx_yishenhe_4">
        	注：运费由<?=$tuihuan->kuaidi_type==1?'买家自行':'卖家'?>承担
        </div>
    	<div class="tuihuanhuoxx_yishenhe_5">
        	<a href="javascript:" onclick="tijiao_kuaidi();">确认</a>
        </div>
        <? }?>
    	<div class="tuihuanhuoxx_daishenhe_3">
        	<ul>
        		<li>
                	<div class="tuihuanhuoxx_daishenhe_3_left">
                    	服务类型
                    </div>
                	<div class="tuihuanhuoxx_daishenhe_3_right">
                    	<?
                        switch($tuihuan->type){
                            case 1:
                                echo '退款补偿';
                            break;
                            case 2:
                                echo '退货退款';
                            break;
                            case 3:
                                echo '换货';
                            break;
                        }
                        ?>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <li>
                	<div class="tuihuanhuoxx_daishenhe_3_left">
                    	申请数量
                    </div>
                	<div class="tuihuanhuoxx_daishenhe_3_right">
                    	<?=$tuihuan->nums?>
                    </div>
                	<div class="clearBoth"></div>
                </li>
				<? if($tuihuan->type>1){?>
				<li>
                	<div class="tuihuanhuoxx_daishenhe_3_left">
                    	运费负责
                    </div>
                	<div class="tuihuanhuoxx_daishenhe_3_right">
                    	<?=$tuihuan->kuaidi_type==1?'买家自行':'卖家'?>承担
                    </div>
                	<div class="clearBoth"></div>
                </li>
				<li>
                	<div class="tuihuanhuoxx_daishenhe_3_left">
                    	运费
                    </div>
                	<div class="tuihuanhuoxx_daishenhe_3_right">
                    	￥<?=$tuihuan->kuaidi_money?>
                    </div>
                	<div class="clearBoth"></div>
                </li>
				<? }?>
                <li>
                    <div class="tuihuanhuoxx_daishenhe_3_left">
                        申请原因
                    </div>
                    <div class="tuihuanhuoxx_daishenhe_3_right">
                        <?=$tuihuan->reason?>
                    </div>
                    <div class="clearBoth"></div>
                </li>
				<li>
                    <div class="tuihuanhuoxx_daishenhe_3_left">
                        图片
                    </div>
                    <div class="tuihuanhuoxx_daishenhe_3_right">
                        <? 
							$imgs = explode('|',$tuihuan->images);
							foreach($imgs as $img){?>
								<img src="<?=$img?>" style="margin-right:.5rem;height:3rem">
							<?}
						?>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <? if($tuihuan->type<3){?>
                <li>
                	<div class="tuihuanhuoxx_daishenhe_3_left">
                    	退款金额
                    </div>
                	<div class="tuihuanhuoxx_daishenhe_3_right">
                    	<?=$tuihuan->money?>
                    </div>
                	<div class="clearBoth"></div>
                </li>
                <? }?>
        	</ul>
        </div>
        <? 
        if($tuihuan->status==-1){
            ?>
            <div class="tuihuanhuoxx_daishenhe_4">
                <a href="/index.php?p=21&a=add&id=<?=$tuihuan->orderId?>" onclick="quxiao();">重新申请</a>
            </div>
            <?
        }else if($tuihuan->status<3){?>
            <div class="tuihuanhuoxx_daishenhe_4">
                <a href="javascript:" onclick="quxiao();">取消申请</a>
            </div>
        <? }?>
    </div>
</div>
<script type="text/javascript">
    var shouhou_id = <?=$id?>;
    function tijiao_kuaidi(){
        var kuaidi_company = $("#kuaidi_company").val();
        var kuaidi_orderId = $("#kuaidi_orderId").val();
        if(kuaidi_company.length<2||kuaidi_orderId.length<2){
            layer.open({content:'请认真填写快递信息',skin: 'msg',time: 2});
            return false;
        }
        layer.open({type:2});
        $.ajax({
            type: "POST",
            url: "/index.php?p=21&a=add_kuaidi&shouhou_id="+shouhou_id,
            data: "kuaidi_company="+kuaidi_company+"&kuaidi_orderId="+kuaidi_orderId,
            dataType:"json",timeout : 8000,
            success: function(res){
                layer.closeAll();
                layer.open({content:res.message,skin: 'msg',time: 2});
                if(res.code==1){
                    setTimeout(function(){
                        location.reload();
                    },1500);
                }
            },
            error: function() {
                layer.closeAll();
                layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
            }
        });
    }
    function quxiao(){
        layer.open({
            content: '您确定要取消该售后申请吗？'
            ,btn: ['确定', '不要']
            ,yes: function(index){
                layer.open({type:2});
                $.ajax({
                    type: "POST",
                    url: "/index.php?p=21&a=quxiao&shouhou_id="+shouhou_id,
                    data: "",
                    dataType:"json",timeout : 8000,
                    success: function(res){
                        layer.closeAll();
                        layer.open({content:res.message,skin: 'msg',time: 2});
                        if(res.code==1){
                            location.href='/index.php?p=8';
                        }
                    },
                    error: function() {
                        layer.closeAll();
                        layer.open({content:'数据请求失败，请刷新页面重试',skin: 'msg',time: 2});
                    }
                });
            }
          });
    }
</script>