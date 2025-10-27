<?
global $db;
$comId = (int)$_SESSION['demo_comId'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$shezhi = $db->get_row("select tuanzhang_rule,fanli_type,if_shequ_tuan from demo_shezhi where comId=$comId");
$tuanzhang_rule = $shezhi->tuanzhang_rule;
$rule = array();
if(!empty($tuanzhang_rule)){
    $rule = json_decode($tuanzhang_rule,true);
}
if($comId==10){
    $db_service = getCrmDb();
    $userNums = (int)$db_service->get_var("select count(*) from demo_user where shangji=$userId");
    $u = $db_service->get_row("select earn,name,image,if_tuanzhang,tuan_id from demo_user where id=$userId");
    $earn = $u->earn;
    $uname = $u->name;
}else{
    $userNums = (int)$db->get_var("select count(*) from users where shangji=$userId");
    $u = $db->get_row("select earn,nickname,image,if_tuanzhang,tuan_id from users where id=$userId");
    $earn = $u->earn;
    $uname = $u->nickname;
}

$shengji = 1;$msg = '';
$rule['yaoqing_num'] = empty($rule['yaoqing_num'])?0:$rule['yaoqing_num'];
$rule['yaoqing_yongjin'] = empty($rule['yaoqing_yongjin'])?0:$rule['yaoqing_yongjin'];
if($userNums<$rule['yaoqing_num']){
    $shengji = 0;
    $msg = '邀请人数不足'.$rule['yaoqing_num'].'，不能升级成为团长';
}else if($earn<$rule['yaoqing_yongjin']){
    $shengji = 0;
    $msg = '佣金不足'.$rule['yaoqing_yongjin'].'，不能升级成为团长';
}
if($u->if_tuanzhang){
  $level = '团长';
}else if(empty($u->level)){
  $level = $comId==10?'小白购':'会员';
}else{
  $level = $db->get_var("select title from user_level where id=$user->level");
}
$width = (int)($userNums*100/$rule['yaoqing_num']);
$width1= (int)($u->earn*100/$rule['yaoqing_yongjin']);
if($width>100)$width=100;
if($width1>100)$width1=100;
if(!empty($u->image) && substr($u->image,0,4)!='http')$u->image='http://www.zhishangez.com'.$u->image;
?>
<link href="/skins/default/styles/yongjin.css" rel="stylesheet" type="text/css">
<div class="huiyuanshengji" style="background-color:#f6f6f6; background-image:url(/skins/default/images/huiyuanshengji_22.png); background-position:center top; background-repeat:no-repeat; background-size:100%;">
	<div class="huiyuanshengji_1" onclick="go_prev_page();">
    	<img src="/skins/default/images/fenlei_1.png" />
    </div>
	<div class="huiyuanshengji_2">
    	<img src="<? if(empty($u->image)){?>/skins/default/images/wode_1.png<? }else{echo $u->image;}?>"  class="huiyuanshengji_2_img1"/>
        <h2><?=$uname?></h2>
        <div class="huiyuanshengji_1_level">
            <img src="/skins/default/images/huiyuanshengji_11.png">
        &nbsp;&nbsp;<?=$level?></div>
    </div>
    <? if($shezhi->fanli_type==2){?>
	<div class="huiyuanshengji_3">
    	<div class="huiyuanshengji_3_01">
        	<div class="huiyuanshengji_3_01_left">
            	<img src="/skins/default/images/huiyuanshengji_12.png" /> 晋升团长
            </div>
        	<div class="huiyuanshengji_3_01_right" onclick="location.href='/index.php?p=1&a=shenqing_tuan';">
            	升级说明
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="huiyuanshengji_3_02">
        	<div class="huiyuanshengji_3_02_up">
            	邀请<?=(int)$rule['yaoqing_num']?>个用户注册
            </div>
        	<div class="huiyuanshengji_3_02_down">	
            	<div class="huiyuanshengji_3_02_down_left">
                	<span><i style="width:<?=$width?>%">&nbsp;</i></span> <?=$userNums?>/<?=$rule['yaoqing_num']?>
                </div>
            	<div class="huiyuanshengji_3_02_down_right">
                	<a href="/index.php?p=8&a=earn_yaoqing">去邀请</a>
                </div>
            	<div class="clearBoth"></div>
            </div>
        </div>
        <div class="huiyuanshengji_3_02">
        	<div class="huiyuanshengji_3_02_up">
            	累计结算佣金<?=$rule['yaoqing_yongjin']?>元
            </div>
        	<div class="huiyuanshengji_3_02_down">	
            	<div class="huiyuanshengji_3_02_down_left">
                	<span><i style="width:<?=$width1?>%">&nbsp;</i></span> <?=$earn?>/<?=$rule['yaoqing_yongjin']?>(元)
                </div>
            	<div class="huiyuanshengji_3_02_down_right">
                	<a href="/index.php?p=8&a=earn_yaoqing">去赚佣</a>
                </div>
            	<div class="clearBoth"></div>
            </div>
        </div>
    	<div class="huiyuanshengji_3_03">
        	<div class="huiyuanshengji_3_03_up">
            	<img src="/skins/default/images/huiyuanshengji_16.png" /> 完成晋升任务可获得权限 <img src="/skins/default/images/huiyuanshengji_16.png" />
            </div>
        	<div class="huiyuanshengji_3_03_down">
            	<ul>
            		<li>
                    	<img src="/skins/default/images/huiyuanshengji_13.png" />
                        <h2>自购省钱</h2>
                        自购每单奖励佣<br>金补贴
                    </li>
                    <li>
                    	<img src="/skins/default/images/huiyuanshengji_14.png" />
                        <h2>分享赚钱</h2>
                        粉丝购买，获得<br>高额佣金
                    </li>
                    <li>
                    	<img src="/skins/default/images/huiyuanshengji_15.png" />
                        <h2>一址开团</h2>
                        更低价格购买商<br>品
                    </li>
                    <div class="clearBoth"></div>
            	</ul>
            </div>
        </div>
    </div>
    <? }
    if($shezhi->if_shequ_tuan==1){
    ?>
	<div class="huiyuanshengji_4">
    	<div class="huiyuanshengji_3_01">
        	<div class="huiyuanshengji_3_01_left">
            	<img src="/skins/default/images/huiyuanshengji_12.png" /> 晋升社区团长
            </div>
        	<div class="huiyuanshengji_3_01_right" onclick="location.href='/index.php?p=1&a=shenqing_shequ';">
            	升级说明
            </div>
        	<div class="clearBoth"></div>
        </div>
        <div class="huiyuanshengji_4_02">
        	<div class="huiyuanshengji_4_02_left">	
            	承包整个小区的生意
            </div>
        	<div class="huiyuanshengji_4_02_right">
            	<a href="/index.php?p=1&a=shenqing_shequ">去申请</a>
            </div>
        	<div class="clearBoth"></div>
        </div>
        <div class="huiyuanshengji_3_031">
        	<div class="huiyuanshengji_3_03_up">
            	<img src="/skins/default/images/huiyuanshengji_16.png" /> 完成晋升任务可获得权限 <img src="/skins/default/images/huiyuanshengji_16.png" />
            </div>
        	<div class="huiyuanshengji_3_03_down">
            	<ul>
            		<li>
                    	<img src="/skins/default/images/huiyuanshengji_18.png" />
                        <h2>一址开团</h2>
                    </li>
                    <li>
                    	<img src="/skins/default/images/huiyuanshengji_19.png" />
                        <h2>双重佣金</h2>
                    </li>
                    <li>
                    	<img src="/skins/default/images/huiyuanshengji_20.png" />
                        <h2>拓宽人脉</h2>
                    </li>
                    <div class="clearBoth"></div>
            	</ul>
            </div>
        </div>
    </div>
    <? }
    if($u->tuan_id>0){
        if($comId==10){
            $tuanzhang = $db_service->get_row("select name as nickname,weixin_name,image,user_info from demo_user where id=$u->tuan_id");
        }else{
            $tuanzhang = $db->get_row("select nickname,weixin_name,image,user_info from users where id=$u->tuan_id");
        }
        if(!empty($tuanzhang->user_info)){
            $user_info = json_decode($tuanzhang->user_info,true);
            //$share_url = 'http://'.$_SERVER['HTTP_HOST'].'/index.php?p=8&a=reg&tuijianren='.$userId;
            if(!empty($tuanzhang->image) && substr($tuanzhang->image,0,4)!='http')$tuanzhang->image='http://www.zhishangez.com'.$tuanzhang->image;
            ?>
        	<div class="huiyuanshengji_5">
            	<div class="huiyuanshengji_5_01">
                	团长信息
                </div>
            	<div class="huiyuanshengji_5_02">
                	<div class="huiyuanshengji_5_02_img">
                    	<img src="<? if(empty($tuanzhang->image)){?>/skins/default/images/wode_1.png<? }else{echo $tuanzhang->image;}?>" />
                    </div>
                	<div class="huiyuanshengji_5_02_tt">
                    	<h2><?=$tuanzhang->nickname?></h2>
                        <span>团长</span>
                    </div>
                	<div class="clearBoth"></div>
                </div>
                <? if(!empty($user_info['wx_img'])){?>
            	<div class="huiyuanshengji_5_03">
                    <div id="erweima"><img src="<?=$user_info['wx_img']?>"></div>
                    <span onclick="layer.open({content:'长按二维码图片进行保存或分享',skin: 'msg',time: 2});">保存二维码</span>
                </div>
                <? }
                if(!empty($user_info['wxh'])){
                ?>
            	<div class="huiyuanshengji_5_04">
                	<span>
                    	<div class="huiyuanshengji_5_04_left">
                        	<input type="text" value="<?=$user_info['wxh']?>" readonly="true"/>
                        </div>
                    	<div class="huiyuanshengji_5_04_right">
                        	<a href="javascript:" id="copy_weixin" data-clipboard-text="<?=$user_info['wxh']?>">复制</a>
                        </div>
                    	<div class="clearBoth"></div>
                    </span>
                </div>
                <? }?>
            	<div class="huiyuanshengji_5_05">
                </div>
            </div>
        <? }
    }?>
	<div class="huiyuanshengji_6">
    </div>
	<div class="huiyuanshengji_7">
    </div>
</div>
<script type="text/javascript" src="/skins/resource/scripts/html2canvas.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/clipboard.min.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/jquery.qrcode.min.js"></script>
<script type="text/javascript">
    var share_url = '<?=$share_url?>';
    var share_title = '<?=$_SESSION['demo_com_title']?>';
    var share_img = '';
    var share_desc = '<?=$db->get_var("select share_desc from demo_shezhi where comId=$comId")?>';
    var userId = <?=$userId?>;
    $(function(){
        var html = document.documentElement;
        var htmlWidth = html.getBoundingClientRect().width;
        if(htmlWidth>960)htmlWidth=960;
        qrcode_width =Math.ceil(htmlWidth*4/4.5);
        /*$('#erweima').qrcode({width:qrcode_width,height:qrcode_width,text:share_url});
        var shareContent = document.getElementById("erweima");
        var width = shareContent.offsetWidth; 
        var height = shareContent.offsetHeight; 
        var canvas = document.createElement("canvas"); 
        var scale = 2;
        canvas.width = width * scale;
        canvas.height = height * scale;
        canvas.getContext("2d").scale(scale, scale);
        html2canvas(shareContent,{
            useCORS:true,
            scale: scale,
            canvas: canvas,
            width: width,
            height: height
        }
        ).then(canvas => {
            canvas.getContext("2d").scale(scale, scale);
            layer.closeAll();
            var img_data1 = canvas.toDataURL();
            $("#erweima").html('<img src="'+img_data1+'" width="100%">').css("padding-top","0px");
            //layer.open({content:'海报生成成功，请长按图片下载或分享',skin: 'msg',time: 2});
        });*/
        var url = window.location.href;
        url = encodeURIComponent(url);
        WeChat(url,share_url,share_title,share_img,share_desc,0);
        <?
        if($u->tuan_id>0){
        ?>
            btn1 = document.getElementById('copy_weixin');
            var clipboard1 = new ClipboardJS(btn1);
            clipboard1.on('success', function(e) {
                layer.open({
                    content: '已复制'
                    ,skin: 'msg'
                    ,time: 2
                });
            });
            clipboard1.on('error', function(e) {
                layer.open({
                    content: '您的浏览器不支持复制，请自行选择复制！'
                    ,skin: 'msg'
                    ,time: 2
                });
            });
        <? }?>
    });
</script>