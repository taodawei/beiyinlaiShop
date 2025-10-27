<?
global $db;
$reason = $db->get_var("select tousu_reason from demo_shezhi where comId=10");
$reasons = explode('@_@', $reason);
require_once "wxshare.php";
$jssdk = new JSSDK("wx884dbf4e2438fa18", "711ab512cdb945214a7d5c812b864c9b");
$signPackage = $jssdk->GetSignPackage();
?>
<div class="sousuo" style="background-color:#f2f5f7;">
	<div class="wode_1">
    	投诉团长
        <div class="wode_1_left" onclick="go_prev_page();">
        	<img src="/skins/default/images/sousuo_1.png" />
        </div>
    </div>
    <form id="add_form" method="post" action="">
        <input type="hidden" name="reason" id="reason" value="">
    	<div class="tousutuanzhang">
        	<div class="tousutuanzhang_up">
            	<div class="tousutuanzhang_up_1">
                	<div class="tousutuanzhang_up_1_left">
                    	我要举报
                    </div>
                	<div class="clearBoth"></div>
                </div>
            	<div class="tousutuanzhang_up_2">
                	<input type="number" name="tuanzhangId" id="tuanzhangId" onchange="check_tuanzhang();" placeholder="输入团长的ID号"/>
                </div>
            	<div class="tousutuanzhang_up_3">
                	<div class="tousutuanzhang_up_3_left">
                    	举报类型
                    </div>
                	<div class="shouhoushenqing_2_up_right" id="select_rdiv">
                        请选择 <img src="/skins/default/images/querendingdan_11.png"/>
                    </div>
                	<div class="clearBoth"></div>
                </div>
            </div>
        	<div class="tousutuanzhang_down">
            	<div class="tousutuanzhang_down_1">
                	<textarea name="remark" id="remark" cols="30" rows="10" placeholder="输入投诉内容~"></textarea>
                </div>
            	<div class="shouhoushenqing_3_03">
                    <ul>
                        <li style="position:relative;width:2.825rem;" id="add_img_li" onclick="showPic(this);">
                            <img src="/skins/default/images/shenqingshouhou_1.gif" class="shouhoushenqing_3_03_more"/>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
            	<div class="tousutuanzhang_down_3">
                	<a href="javascript:" onclick="tijiao();">投诉</a>
                </div>
            </div>
        </div>
    </form>
</div>
<!--换货原因-->
<div class="shenqingshouhou_yuanyin_tc" id="shenqingshouhou_yuanyin_tc" style="display:none;">
    <div class="bj" style="background-color:rgba(0,0,0,0.8);" onclick="$('#shenqingshouhou_yuanyin_tc').hide();"></div>
    <div class="shenqingshouhou_yuanyin">
        <div class="shenqingshouhou_yuanyin_1">
            选择类型
        </div>
        <div class="shenqingshouhou_yuanyin_2">
            <span>投诉</span>
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
<script type="text/javascript" src="/skins/demo/scripts/user/tousu.js"></script>