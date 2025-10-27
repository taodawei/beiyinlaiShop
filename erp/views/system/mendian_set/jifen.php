<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$user_shezhi = $db->get_row("select jifen_type,jifen_content from user_shezhi where comId=$comId");
if(!empty($user_shezhi->jifen_content)){
    $content = json_decode($user_shezhi->jifen_content,true);
}
$rows = 1;
if(!empty($content['items']))$rows = count($content['items']);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/mendianshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .layui-form-radio span{font-size:16px}
    </style>
</head>
<body>
	<div class="yueshezhi">
        <div class="yueshezhi_up">
            <img src="images/mdsz_12.png" alt=""/>  积分规则
        </div>
        <div class="jifenguize">
            <div class="jifenguize_up">
                <ul>
                    <li>
                        <a href="javascript:" class="jifenguize_up_on">交易积分</a>
                    </li>
                    <li>
                        <a href="?s=mendian_set&a=jifen_jiazhi">价值管理</a>
                    </li>
                    <!--<li>-->
                    <!--    <a href="?s=mendian_set&a=jifen_qiandao">每日签到</a>-->
                    <!--</li>-->
                    <!--<li>-->
                    <!--    <a href="?s=mendian_set&a=jifen_share">分享积分/抵扣金</a>-->
                    <!--</li>-->
                    <div class="clearBoth"></div>
                </ul>
            </div>
            <div class="jifenguize_down">
                <div class="jifenguize_down_01">
                    <div class="jiaoyijifen">
                        <form action="?s=mendian_set&a=jifen&submit=1" method="post" class="layui-form">
                        <div class="jiaoyijifen_up">
                            <div class="jiaoyijifen_up_1">
                                用户【积分】 来源渠道
                            </div>
                            <div class="jiaoyijifen_up_2">
                                <div class="jiaoyijifen_up_2_03">
                                    *实际过程中，订单金额非整数时，积分取整数值部分 4.9积分 取4积分
                                </div>
                                <ul>
                                    <li class="jiaoyijifen_up_2_li <? if($user_shezhi->jifen_type==1){echo 'on';}?>">
                                        <div class="jiaoyijifen_up_2_up">
                                            <input type="radio" name="jifen_type" lay-filter="type1" value="1" <? if($user_shezhi->jifen_type==1){?>checked="true"<? }?> title="消费送积分" /> 
                                        </div>
                                        <div class="jiaoyijifen_up_2_down" <? if($user_shezhi->jifen_type==1){?>style="display:block"<? }?>>
                                            实际消费金额，生成积分比例 消费金额  <input type="text" name="content[money]" lay-verify="shuzi" value="<?=$content['money']?>"/> 元 转化为1积分 (数额需为正整数)，单笔消费送积分上限： <input type="text" lay-verify="shuzi" value="<?=$content['shangxian']?>" name="content[shangxian]" />
                                        </div>
                                    </li>
                                    <li style="display:none;" class="jiaoyijifen_up_2_li <? if($user_shezhi->jifen_type==2){echo 'on';}?>">
                                        <div class="jiaoyijifen_up_2_up">
                                            <input type="radio" name="jifen_type" lay-filter="type2" value="2" <? if($user_shezhi->jifen_type==2){?>checked="true"<? }?> title="满送积分"/> 
                                        </div>
                                        <div class="jiaoyijifen_up_2_down" <? if($user_shezhi->jifen_type==2){?>style="display:block"<? }?>>
                                            单笔消费金额满 <input type="text" name="content[man]" lay-verify="shuzi" value="<?=$content['man']?>" /> 元 送积分 <input type="text" name="content[song]" lay-verify="shuzi" value="<?=$content['song']?>" /> <span>*不满，不送。消费金额倍数，方可累计赠送</span>
                                        </div>
                                    </li>
                                    <li style="display:none;" class="jiaoyijifen_up_2_li <? if($user_shezhi->jifen_type==3){echo 'on';}?>">
                                        <div class="jiaoyijifen_up_2_up">
                                            <input type="radio" name="jifen_type" lay-filter="type3" value="3" <? if($user_shezhi->jifen_type==3){?>checked="true"<? }?> title="购商品送积分"/> 
                                        </div>
                                        <div class="jiaoyijifen_up_zhidingshangpin jiaoyijifen_up_2_down" <? if($user_shezhi->jifen_type==3){?>style="display:block"<? }?>>
                                            <div class="jiaoyijifen_up_zhidingshangpin_2">
                                                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="row_table" data-row="<?=$rows?>">
                                                    <tr height="33">
                                                        <td bgcolor="#f6f6f6" width="450" align="left" valign="middle">
                                                            商品分类/名称
                                                        </td>
                                                        <td bgcolor="#f6f6f6" width="100" align="left" valign="middle">送积分</td>
                                                        <td bgcolor="#f6f6f6" width="100" align="left" valign="middle">
                                                            操作
                                                        </td>
                                                    </tr>
                                                    <? if(empty($content['items'])){?>
                                                        <tr height="48" id="row_1">
                                                            <td bgcolor="#ffffff" width="450" align="left" valign="middle">
                                                                <input type="text" id="fanwei_1" readonly="true" onclick="fanwei('1');" class="jiaoyijifen_up_zhidingshangpinfenlei_2" placeholder="请选择分类/商品">
                                                            </td>
                                                            <td bgcolor="#ffffff" width="100" align="left" valign="middle">
                                                                <input type="text" name="jifen_1" lay-verify="shuzi" class="jiaoyijifen_up_zhidingshangpinfenlei_22">
                                                            </td>
                                                            <td bgcolor="#ffffff" width="100" align="left" valign="middle">
                                                                <a href="javascript:" onclick="del_row(1);">删除</a>
                                                            </td>
                                                            <input type="hidden" name="departs_1" id="departs_1" value="">
                                                            <input type="hidden" name="users_1" id="users_1" value="">
                                                            <input type="hidden" name="departNames_1" id="departNames_1" value="">
                                                            <input type="hidden" name="userNames_1" id="userNames_1" value="">
                                                            <input type="hidden" name="rows[]" value="1">
                                                        </tr>
                                                    <? }else{
                                                        foreach ($content['items'] as $i => $item){
                                                            $fanwei = $item['departNames'];
                                                            if(!empty($item['userNames'])){
                                                                if(empty($fanwei)){
                                                                    $fanwei = $item['userNames'];
                                                                }else{
                                                                    $fanwei = $fanwei.','.$item['userNames'];
                                                                }
                                                            }
                                                            ?>
                                                            <tr height="48" id="row_<?=$i+1?>">
                                                                <td bgcolor="#ffffff" width="450" align="left" valign="middle">
                                                                    <input type="text" id="fanwei_<?=$i+1?>" readonly="true" onclick="fanwei('<?=$i+1?>');" class="jiaoyijifen_up_zhidingshangpinfenlei_2" value="<?=$fanwei?>" placeholder="请选择分类/商品">
                                                                </td>
                                                                <td bgcolor="#ffffff" width="100" align="left" valign="middle">
                                                                    <input type="text" name="jifen_<?=$i+1?>" value="<?=$item['jifen']?>" lay-verify="shuzi" class="jiaoyijifen_up_zhidingshangpinfenlei_22">
                                                                </td>
                                                                <td bgcolor="#ffffff" width="100" align="left" valign="middle">
                                                                    <a href="javascript:" onclick="del_row(<?=$i+1?>);">删除</a>
                                                                </td>
                                                                <input type="hidden" name="departs_<?=$i+1?>" id="departs_<?=$i+1?>" value="<?=$item['channels']?>">
                                                                <input type="hidden" name="users_<?=$i+1?>" id="users_<?=$i+1?>" value="<?=$item['pdts']?>">
                                                                <input type="hidden" name="departNames_<?=$i+1?>" id="departNames_<?=$i+1?>" value="<?=$item['departNames']?>">
                                                                <input type="hidden" name="userNames_<?=$i+1?>" id="userNames_<?=$i+1?>" value="<?=$item['userNames']?>">
                                                                <input type="hidden" name="rows[]" value="<?=$i+1?>">
                                                            </tr>
                                                            <?
                                                        }
                                                    ?>

                                                    <? }?>
                                                </table>
                                            </div>
                                            <div class="jiaoyijifen_up_zhidingshangpin_3">
                                                <a href="javascript:" onclick="addRow();">+添加商品</a>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="yueshezhi_down_03">
                            <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
    <div class="reveal-modal-bg" style="display:none; cursor: pointer;"></div>
    <input type="hidden" id="departs" value="" />
    <input type="hidden" id="users" value="" />
    <input type="hidden" id="departNames" value=""/>
    <input type="hidden" id="userNames" value="" />
    <input type="hidden" id="editId" value="0" />
    <script type="text/javascript">
    	layui.use(['form'], function(){
    		var form = layui.form;
	    	form.verify({
	    		shuzi:function(value,item){
	    			value = parseInt(value);
	    			if(isNaN(value)||value<1){
	    				return '请输入有效的数字';
	    			}
	    		}
	    	});
            form.on('radio(type1)',function(){
                $(".jiaoyijifen_up_2_down").hide();
                $(".jiaoyijifen_up_2_down").eq(0).show(100);
                $(".jiaoyijifen_up_2_li").removeClass("on");
                $(".jiaoyijifen_up_2_li").eq(0).addClass("on");
            });
            form.on('radio(type2)',function(){
                $(".jiaoyijifen_up_2_down").hide();
                $(".jiaoyijifen_up_2_down").eq(1).show(100);
                $(".jiaoyijifen_up_2_li").removeClass("on");
                $(".jiaoyijifen_up_2_li").eq(1).addClass("on");
            });
            form.on('radio(type3)',function(){
                $(".jiaoyijifen_up_2_down").hide();
                $(".jiaoyijifen_up_2_down").eq(2).show(100);
                $(".jiaoyijifen_up_2_li").removeClass("on");
                $(".jiaoyijifen_up_2_li").eq(2).addClass("on");
            });
	    	form.on('submit(tijiao)', function(data){
	    		layer.load();
	    	});
	    });
    </script>
    <script type="text/javascript" src="js/users/jifen.js"></script>
    <? require('views/help.html');?>
</body>
</html>