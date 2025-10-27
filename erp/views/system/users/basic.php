<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$user = $db->get_row("select * from users where id=$id and comId=$comId");
$rezhengTitle = '';
if($user->renzheng == -1){//认证：0-未提交  1-待审核 2-审核通过  -1审核失败
    $rezhengTitle = '认证失败，等待重新提交';
}elseif ($user->renzheng == 1) {
    $rezhengTitle = '认证提交，等待审核';
}

// var_dump(checkUrl($arr,'?m=system&s=users&a=shenhe1'));die;
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit" />
    <link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style>
        .layui-table-body tr{height:50px}
        .layui-table-view{margin:10px;}
    </style>
</head>
<body>
    <div class="mendianguanli"> 
        <div class="mendianguanli_up">
            <a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"></a> <b style="color:#369dd0;"><?=$user->nickname?></b> 会员详情
        </div>
        <div class="mendianguanli_down">
            <div class="huiyuanxinxi">
                <? require('views/system/users/head.php')?>
                <div class="huiyuanxinxi_down">
                    <div class="hyxx_jibenziliao">
                        <div class="hyxx_jibenziliao_1">
                            <div class="hyxx_jibenziliao_1_up">
                                <div class="hyxx_jibenziliao_1_up_left">
                                    基本信息
                                </div>
                                <? if($_SESSION['if_tongbu']!=1){?>
                                <div class="hyxx_jibenziliao_1_up_right">
                                    <a href="?m=system&s=users&a=create&id=<?=$request['id']?>&returnurl=<?=urlencode($request['returnurl'])?>"><img src="images/users_40.png"> 修改会员信息</a>
                                </div>
                                <? }?>
                                <div class="clearBoth"></div>
                            </div>
                            <div class="hyxx_jibenziliao_1_down">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tbody><tr height="44">
                                        <td width="255" rowspan="4" align="center" valign="middle">
                                            <img src="images/touxiang_1.png">
                                        </td>
                                        <td bgcolor="#f9fafb" width="134" align="center" valign="middle">
                                            会员ID    
                                        </td>
                                        <td align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$user->id?></div>
                                        </td>
                                    </tr>
                                    <tr height="44">
                                        <td bgcolor="#f9fafb" width="134" align="center" valign="middle">
                                            姓名  
                                        </td>
                                        <td align="left" valign="middle">
                                            <div style="color:#d39900; padding-left:10px;"><?=$user->nickname?></div>
                                        </td>
                                    </tr>
                                    <tr height="44">
                                        <td bgcolor="#f9fafb" width="134" align="center" valign="middle">
                                            手机号 
                                        </td>
                                        <td align="left" valign="middle">
                                            <div style="color:#d39900; padding-left:10px;"><?=$user->username?></div>
                                        </td>
                                    </tr>
                                    <tr height="44">
                                        <td bgcolor="#f9fafb" width="134" align="center" valign="middle">
                                             会员等级
                                        </td>
                                        <td align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$db->get_var("select title from user_level where id=$user->level")?></div>
                                        </td>
                                    </tr>
                                </tbody></table>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tbody>
                                    <tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">会员等级</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px; color:#ff1f1f;"> <?=$db->get_var("select title from user_level where id=$user->level")?>    </div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">所属门店</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><? if($user->mendianId>0){
                                                echo $db->get_var("select title from mendian where id=$user->mendianId");
                                            }else{echo '无';}?></div>
                                        </td>
                                    </tr>
                                    <tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">注册成为会员时间</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=date("Y-m-d H:i",strtotime($user->dtTime))." token: ".$user->token?></div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">最近一次登录时间 </div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <?=date("Y-m-d H:i",strtotime($user->lastLogin))?> <a href="?m=system&s=users&a=operate&type=1&id=<?=$id?>&returnurl=<?=urlencode($request['returnurl'])?>">查看登录日志</a></div>
                                        </td>
                                    </tr>
                                </tbody></table>
                            </div>
                        </div>
                        <div class="hyxx_jibenziliao_2">    
                            <div class="hyxx_jibenziliao_2_up">
                                <b>认证信息</b> <? if($user->renzheng==2){?><img src="images/users_41.png"> 认证成功<? }else{?>  <img src="images/users_42.png"> <?=$rezhengTitle?><? }?>
                            </div>
                            <? if($user->renzheng <> 0){
                              
                            ?>
                            <div class="hyxx_jibenziliao_1_down">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tbody><tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">真实姓名</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$user->real_name?></div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">身份证号</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$user->identity_id?></div>
                                        </td>
                                    </tr>
                                   
                                    <tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">身份证正面</div>
                                        </td>
                                       <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <? if(!empty($user->identity_card_front)){?><a href="<?=$user->identity_card_front?>" target="_blank">查看证件</a><? }?></div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">身份证反面</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <? if(!empty($user->identity_card_back)){?><a href="<?=$user->identity_card_back?>" target="_blank">查看证件</a><? }?></div>
                                        </td>
                                    </tr>
                                    
                                    <? if($user->renzheng==1 && checkUrl($arr,'?m=system&s=users&a=shenhe1')){?> 
                                    <tr height="47">
                                        <td colspan="2">
                							<a href="javascript:" onclick="shenhe1(2);" ><img src="images/biao_888.png">审核成功</a>
                						</td>
                						 <td colspan="2">
                							<a href="javascript:" onclick="shenhe1(-1);" ><img src="images/users_42.png">审核失败</a>
                						</td>
                                    </tr>
                                    <? } ?>
                                </tbody></table>
                            </div>
                        <? }?>
                        </div>
                        <?
                        $fapiaos = $db->get_results("select * from user_fapiao where userId=$id order by id asc");
                        if(!empty($fapiaos)){
                            ?>
                            <div class="hyxx_jibenziliao_2">    
                                <div class="hyxx_jibenziliao_2_up">
                                    <b>发票信息</b>
                                </div>
                                <div class="hyxx_jibenziliao_1_down">
                            <?
                            foreach ($fapiaos as $f) {
                                ?>
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tbody><tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">单位名称</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$f->com_title?></div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">纳税人识别码</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$f->shibiema?></div>
                                        </td>
                                    </tr>
                                    <tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">注册地址</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <?=$f->address?> </div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">注册电话</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <?=$f->phone?> </div>
                                        </td>
                                    </tr>
                                    <tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">开户银行</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <?=$f->bank_name?> </div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">银行账户</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"> <?=$f->bank_card?> </div>
                                        </td>
                                    </tr>
                                </tbody></table>
                                <div style="height:10px;"></div>
                                <?
                            }?>
                                </div>
                            </div>
                            <?
                        }
                        $bank = $db->get_row("select * from user_bank where userId=$id");
                        if(!empty($bank)){
                        ?>
                        <div class="hyxx_jibenziliao_2">    
                            <div class="hyxx_jibenziliao_2_up">
                                <b>提现管理</b>
                            </div>
                            <div class="hyxx_jibenziliao_1_down">
                                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                    <tbody><tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">开户姓名</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$bank->name?></div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">开户银行</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$bank->bank_name?></div>
                                        </td>
                                    </tr>
                                    <tr height="47">
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;">银行账户</div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"><?=$bank->bank_card?></div>
                                        </td>
                                        <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                            <div style="padding-right:10px;"> </div>
                                        </td>
                                        <td bgcolor="#ffffff" align="left" valign="middle">
                                            <div style="padding-left:10px;"></div>
                                        </td>
                                    </tr>
                                </tbody></table>
                            </div>
                        </div>
                        <? }
                        $addresss = $db->get_results("select * from user_address where userId=$id order by moren desc,id desc");
                        if(!empty($addresss)){
                        ?>
                        <div class="hyxx_jibenziliao_2">    
                            <div class="hyxx_jibenziliao_2_up">
                                <b>收货信息</b>
                            </div>
                            <div class="hyxx_jibenziliao_1_down">
                                <? foreach($addresss as $a){?>
                                    <table width="100%" border="0" cellpadding="0" cellspacing="0">
                                        <tbody><tr height="47">
                                            <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                                <div style="padding-right:10px;">收件人</div>
                                            </td>
                                            <td bgcolor="#ffffff" align="left" valign="middle">
                                                <div style="padding-left:10px;"><?=$a->name?><? if($a->moren==1){?> <span style="color:#ff0000;">( 默认 )</span><? }?></div>
                                            </td>
                                            <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                                <div style="padding-right:10px;">手机号</div>
                                            </td>
                                            <td bgcolor="#ffffff" align="left" valign="middle">
                                                <div style="padding-left:10px;"><?=$a->phone?></div>
                                            </td>
                                        </tr>
                                        <tr height="47">
                                            <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                                <div style="padding-right:10px;">收货区域</div>
                                            </td>
                                            <td bgcolor="#ffffff" align="left" valign="middle">
                                                <div style="padding-left:10px;"><?=getAreaName($a->areaId)?></div>
                                            </td>
                                            <td bgcolor="#f3f6f8" width="174" align="right" valign="middle">
                                                <div style="padding-right:10px;">详细地址</div>
                                            </td>
                                            <td bgcolor="#ffffff" align="left" valign="middle">
                                                <div style="padding-left:10px;"><?=$a->address?></div>
                                            </td>
                                        </tr>
                                    </tbody></table>
                                    <div style="height:10px;"></div>
                                <? }?>
                            </div>
                        </div>
                    <? }?>
                    </div>
                </div>
            </div>
        </div>
     </div>
     <script>
        function shenhe1(status){
            if(status == 2){
                var title = '审核成功';
            }else{
                var title = '审核失败';
            }
        	layer.open({
        		type: 1
        		,title: false
        		,closeBtn: false
        		,area: '530px;'
        		,shade: 0.3
        		,id: 'LAY_layuipro'
        		,btn: ['确定', '取消']
        		,yes: function(index, layero){
        			var beizhu = $("#e_beizhu").val();
        			
        			layer.load();
        			$.ajax({
        				type: "POST",
        				url: "?m=system&s=users&a=shenhe1",
        				data: "jiluId=<?=$id?>&status="+status+"&cont="+beizhu,
        				dataType:'json',timeout:30000,
        				success: function(resdata){
        					layer.closeAll();
        					if(resdata.code==0){
        						layer.msg("error",{icon:5});
        					}else{
        						$("#addBeizhu").before("success");
        					}
        					window.location.href = window.location.href;
        				}
        			});
        		}
        		,btnAlign: 'r'
        		,content: '<div class="spxx_shanchu_tanchu layui-form" style="display: block;">'+
        		'<div class="spxx_shanchu_tanchu_01">'+
        		'<div class="spxx_shanchu_tanchu_01_left">添加'+title+'备注</div>'+
        		'<div class="spxx_shanchu_tanchu_01_right">'+
        		'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
        		'</div>'+
        		'<div class="clearBoth"></div>'+
        		'</div>'+
        		'<div class="spxx_shanchu_tanchu_02" style="height:220px;padding:0px;margin-top:10px;">'+
        		'<textarea id="e_beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入'+title+'备注信息"></textarea>'+
        		'</div>'+
        		'</div>'
        	});
        }
     </script>
    <? require('views/help.html');?>
</body>
</html>