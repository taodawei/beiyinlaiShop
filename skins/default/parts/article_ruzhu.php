<?
$_SESSION['tijiao'] = 1;
require_once "wxshare.php";
$jssdk = new JSSDK("wx884dbf4e2438fa18", "711ab512cdb945214a7d5c812b864c9b");
$signPackage = $jssdk->GetSignPackage();
?>
<link href="/skins/demo/styles/wode.css" rel="stylesheet" type="text/css">
<div id="shouye">
    <div class="wode_1">
        商家入驻
    </div>
    <div class="shangjiaruzhu">
        <form action="/index.php?p=1&a=ruzhu&tijiao=1" id="ruzhu_form" method="post">
            <input type="hidden" id="mendian_type" name="type" value="个人微商">
            <input type="hidden" name="img_zhizhao" id="img_zhizhao">
            <input type="hidden" name="img_shenfenzheng" id="img_shenfenzheng">
            <div class="shangjiaruzhu_1">
                <div class="shangjiaruzhu_1_left">
                    类型
                </div>
                <div class="shangjiaruzhu_1_right">
                    <ul>
                        <li>
                            <a href="javascript:" onclick="qiehuan_type(0,'个人微商')" class="shangjiaruzhu_1_right_on"><img src="/skins/default/images/querendingdan_18.png"/> 个人微商</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="qiehuan_type(1,'商家')"><img src="/skins/default/images/querendingdan_19.png"/> 商家</a>
                        </li>
                        <li>
                            <a href="javascript:" onclick="qiehuan_type(2,'同城')"><img src="/skins/default/images/querendingdan_19.png"/> 同城</a>
                        </li>
                        <div class="clearBoth"></div>
                    </ul>
                </div>
                <div class="clearBoth"></div>
            </div>
            <div class="shangjiaruzhu_2">
                <ul>
                    <li>    
                        <div class="shangjiaruzhu_2_left">
                            经营品类
                        </div>
                        <div class="shangjiaruzhu_2_right">
                            <input type="text" name="product_type" class="mustrow" id="product_type" placeholder="请输入经营品类"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li class="show_li_1">  
                        <div class="shangjiaruzhu_2_left">
                            微商授权证书
                        </div>
                        <div class="shangjiaruzhu_2_right" onclick="showPic(this,'img_zhizhao');">
                            <font>请上传</font> <img src="/skins/default/images/querendingdan_11.png"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li class="show_li_1">  
                        <div class="shangjiaruzhu_2_left">
                            微商级别价格表
                        </div>
                        <div class="shangjiaruzhu_2_right" onclick="showPic(this,'img_shenfenzheng');">
                            <font>请上传</font> <img src="/skins/default/images/querendingdan_11.png"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li class="show_li_3" style="display:none;">
                        <div class="shangjiaruzhu_2_left">
                            营业执照
                        </div>
                        <div class="shangjiaruzhu_2_right" onclick="showPic(this,'img_zhizhao');">
                            <font>请上传</font> <img src="/skins/default/images/querendingdan_11.png"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li class="show_li_2" style="display:none;">
                        <div class="shangjiaruzhu_2_left">
                            入驻其他电商平台
                        </div>
                        <div class="shangjiaruzhu_2_right" id="shangjiaruzhu_2_right">                     
                            <span class="shangjiaruzhu_2_right_on" onclick="qiehuan_type1(0);">
                                <img src="/skins/default/images/querendingdan_18.png"/> 是
                            </span>
                            <span onclick="qiehuan_type1(1);">
                                <img src="/skins/default/images/querendingdan_19.png"/> 否
                            </span>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li class="show_li_2" id="url_li" style="display:none;">
                        <input type="text" name="other_url" placeholder="请输入店铺连接地址~" class="shangjiaruzhu_2_right_input"/>
                    </li>
                </ul>
            </div>
            <div class="shangjiaruzhu_3">
                <textarea name="beizhu" id="beizhu" class="mustrow" cols="30" rows="10" placeholder="请简述一下您的产品优势~"></textarea>
            </div>
            <div class="shangjiaruzhu_4">   
                <div class="shangjiaruzhu_4_up">
                    联系方式
                </div>
                <div class="shangjiaruzhu_4_down">
                    <ul>
                        <li class="show_li_3" style="display:none;">
                            <div class="shangjiaruzhu_4_down_left">
                                商家名称
                            </div>
                            <div class="shangjiaruzhu_4_down_right">
                                <input type="text" name="title" placeholder="请输入商家名称"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shangjiaruzhu_4_down_left">
                                联系人
                            </div>
                            <div class="shangjiaruzhu_4_down_right">
                                <input type="text" name="name" id="name" class="mustrow" placeholder="请输入姓名"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shangjiaruzhu_4_down_left">
                                手机
                            </div>
                            <div class="shangjiaruzhu_4_down_right">
                                <input type="text" name="phone" id="phone" class="mustrow" placeholder="请输入手机号码"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                        <li>
                            <div class="shangjiaruzhu_4_down_left">
                                地址
                            </div>
                            <div class="shangjiaruzhu_4_down_right">
                                <input type="text" name="address" id="address" class="mustrow" placeholder="请输入地址"/>
                            </div>
                            <div class="clearBoth"></div>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="shangjiaruzhu_5">
                <a href="javascript:" onclick="shenqing();">提交申请</a>
            </div>
        </form>
    </div>
</div>
<?
require(ABSPATH.'/skins/demo/bottom.php');
?>
<script type="text/javascript">
    wx.config({
        debug: false,
        appId: '<?php echo $signPackage["appId"];?>',
        timestamp: <?php echo $signPackage["timestamp"];?>,
        nonceStr: '<?php echo $signPackage["nonceStr"];?>',
        signature: '<?php echo $signPackage["signature"];?>',
        jsApiList: [
            // 所有要调用的 API 都要加到这个列表中
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
<script type="text/javascript" src="/skins/demo/scripts/user/ruzhu.js?v=1.1"></script>