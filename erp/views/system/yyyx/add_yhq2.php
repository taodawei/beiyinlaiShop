<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$type = (int)$request['type'];
if($id>0){
    $yhq = $db->get_row("select * from yhq where id=$id and comId=$comId");
    $type = $yhq->type;
}
$mendianArry = array();
$levelArry = array();
if(!empty($yhq->levelIds))$levelArry=explode(',',$yhq->levelIds);
if(!empty($yhq->mendianIds))$mendianArry=explode(',',$yhq->mendianIds);
$levels = $db->get_results("select id,title from user_level where comId=$comId order by jifen asc");
$mendians = $db->get_results("select id,title from mendian where comId=$comId");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
    <link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
    <div class="mendianguanli">	
		<div class="mendianguanli_up">
        	<a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png" alt=""/></a> 新增赠送券
        </div>
        <div class="mendianguanli_down">
        	<div class="addshengriquan">
            	<div class="addshengriquan_left">
                	<form action="?s=yyyx&a=add_yhq&submit=1&type=<?=$type?>&id=<?=$id?>" method="post" id="tijiaoForm" class="layui-form">
                	<input type="hidden" name="color" id="color" value="<?=empty($yhq->color)?'#3c8bec':$yhq->color?>">
                	<input type="hidden" name="areaIds" id="areaIds" value="<?=$yhq->areaIds?>">
                	<div class="addshengriquan_left_02">
                    	<ul>
                            <li>
                            	<div class="addshengriquan_left_02_left">
                                	<span>*</span> 赠送券标题：
                                </div>
                            	<div class="addshengriquan_left_02_right">
                                	<div class="addshengriquan_left_02_right_biaoti">
                                    	<div class="addshengriquan_left_02_right_biaoti_up">
                                        	<input type="text" id="title" name="title" lay-verify="required" maxlength="10" value="<?=$yhq->title?>" />
                                        </div>
                                    </div>
                                </div>
                            	<div class="clearBoth"></div>
                            </li>
                            <li>
                            	<div class="addshengriquan_left_02_left">
                                	<span>*</span> 面值：
                                </div>
                            	<div class="addshengriquan_left_02_right">
                                	<div class="addshengriquan_left_02_right_biaoti">
                                    	<div class="addshengriquan_left_02_right_biaoti_up">
                                        	<input type="text" name="money" id="money" value="<?=str_replace('.00','',$yhq->money)?>" lay-verify="shuzi|required" style="width:82px;"/> 元
                                        </div>
                                    </div>
                                </div>
                            	<div class="clearBoth"></div>
                            </li>
                            <li>
                            	<div class="addshengriquan_left_02_left">
                                	<span>*</span> 使用门槛：
                                </div>
                            	<div class="addshengriquan_left_02_right">
                                	<div class="addshengriquan_left_menkan">
                                    	<div class="addshengriquan_left_menkan_01">
                                    		<input type="radio" name="ifman" value="0" <? if(empty($yhq->man)){?>checked="true"<? }?> lay-filter="man1" title="不限制">
                                        </div>
                                    	<div class="addshengriquan_left_menkan_01">
                                        	<input type="radio" name="ifman" value="1" <? if(!empty($yhq->man)){?>checked="true"<? }?> lay-filter="man2" title="满"> <div style="display:inline-block;top:5px;position:relative;"><input type="text" name="man" id="man" value="<?=$yhq->man?>" <? if(empty($yhq->man)){?>readonly="true" class="disabled"<? }?> lay-verify="shuzi"/> 元可使用</div>
                                        </div>
                                    </div>
                                </div>
                            	<div class="clearBoth"></div>
                            </li>
                            <li>
                            	<div class="addshengriquan_left_02_left">
                                	<span>*</span> 赠送券颜色：
                                </div>
                            	<div class="addshengriquan_left_02_right">
                                	<div class="addshengriquan_left_yanse">
                                    	<div class="addshengriquan_left_yanse_up">
                                        	<div class="addshengriquan_left_yanse_up_left">
                                            	<span <? if(!empty($yhq->color)){?>style="background:<?=$yhq->color?>"<? }?>></span>
                                            </div>
                                        	<div class="addshengriquan_left_yanse_up_right">
                                            	<img src="images/biao_20.png" alt=""/>
                                            </div>
                                        	<div class="clearBoth"></div>
                                        </div>
                                    	<div class="addshengriquan_left_yanse_down" style="display:none;">
                                    		<a href="javascript:" onclick="changeColor('#3c8bec');" style="background-color:#3c8bec;"></a>
                                        	<a href="javascript:" onclick="changeColor('#efbd3a');" style="background-color:#efbd3a;"></a>
                                            <a href="javascript:" onclick="changeColor('#106c99');" style="background-color:#106c99;"></a>
                                            <a href="javascript:" onclick="changeColor('#fe4155');" style="background-color:#fe4155;"></a>
                                        </div>
                                    </div>
                                </div>
                            	<div class="clearBoth"></div>
                            </li>
                            <li>
                            	<div class="addshengriquan_left_02_left">
                                	<span>*</span> 使用说明：
                                </div>
                            	<div class="addshengriquan_left_02_right">
                                	<textarea name="content" id="content" cols="30" rows="10" placeholder="填写活动的详细说明，支持换行；" lay-verify="required"><?=$yhq->content?></textarea>
                                </div>
                            	<div class="clearBoth"></div>
                            </li>
                    	</ul>
                    </div>
                	<div class="addshengriquan_left_03">
                    	<div class="addshengriquan_left_03_up">
                        	基本规则
                        </div>
                    	<div class="addshengriquan_left_03_down">
                        	<ul>
                                <li>
                                	<div class="addshengriquan_left_03_left">
                                    	<span>*</span> 适用门店：
                                    </div>
                                	<div class="addshengriquan_left_03_right">
                                    	<div class="addshengriquan_left_03_right_huiyuan">
                                    		<input type="radio" name="if_mendian" lay-filter="mendian1" value="0" <? if(empty($yhq->mendianIds)){?>checked="true"<? }?> title="所有门店"><br>
                                        	<input type="radio" name="if_mendian" lay-filter="mendian2" value="1" <? if(!empty($yhq->mendianIds)){?>checked="true"<? }?> title="指定门店">
                                            <div class="addshengriquan_left_03_mendian" style="margin-left:10px;margin-top:8px;<? if(empty($yhq->mendianIds)){echo 'display:none;';}?>">
                                            	<? if(!empty($mendians)){
                                            		foreach ($mendians as $l) {
                                            			?><input type="checkbox" name="mendians[]" <? if(in_array($l->id,$mendianArry)){?>checked="true"<? }?> value="<?=$l->id?>" lay-skin="primary" title="<?=$l->title?>"><?
                                            		}
                                            	}?>
                                            </div>
                                        </div>
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                                <li>
                                	<div class="addshengriquan_left_03_left">
                                    	<span>*</span> 适用商品：
                                    </div>
                                	<div class="addshengriquan_left_03_right">
                                    	<div class="addshengriquan_left_03_right_quyu">
                                        	<div class="addshengriquan_left_03_right_quyu_1">
                                        		<input type="radio" name="useType" lay-filter="type1" value="1" <? if($yhq->useType==1||$yhq->useType==0){?>checked="true"<? }?> title="全部商品"><br>
                                        		<input type="radio" name="useType" lay-filter="type2" value="2" <? if($yhq->useType==2){?>checked="true"<? }?> title="指定商品">
                                            </div>
                                        	<div class="addshengriquan_left_03_right_quyu_2" id="pdt_div" <? if($yhq->useType==1||$yhq->useType==0){echo 'style="display:none;"';}?>>
                                        		<?
                                        		$fanwei = $yhq->channelNames;
                                        		if(!empty($yhq->pdtNames)){
                                        			if(empty($fanwei)){
                                        				$fanwei = $yhq->pdtNames;
                                        			}else{
                                        				$fanwei = $fanwei.','.$yhq->pdtNames;
                                        			}
                                        		}
                                        		?>
                                            	<input type="text" id="fanwei_1" readonly="true" onclick="fanwei('1');" placeholder="选择分类/商品<?=$yhq->useType?>" onclick="fanwei('areaIds');" readonly="true" value="<?=$fanwei?>"/>
                                            	<input type="hidden" name="channels" id="departs_1" value="<?=$yhq->channels?>">
                                            	<input type="hidden" name="pdts" id="users_1" value="<?=$yhq->pdts?>">
                                            	<input type="hidden" name="channelNames" id="departNames_1" value="<?=$yhq->channelNames?>">
                                            	<input type="hidden" name="pdtNames" id="userNames_1" value="<?=$yhq->pdtNames?>">
                                            	<div class="clearBoth"></div>
                                            </div>
                                        </div>
                                    </div>
                                	<div class="clearBoth"></div>
                                </li>
                        	</ul>
                        </div>
                    </div>
                	<div class="addshengriquan_left_04">
                    	<button class="layui-btn" lay-submit="" lay-filter="tijiao">保 存</button>
                    </div>
                </form>
                </div>
            	<div class="addshengriquan_right">
                	<div class="addshengriquan_right_01">
                    	知商优惠券
                    </div>
                	<div class="addshengriquan_right_02">
                    	<div class="addshengriquan_right_02_1">
                        </div>
                    	<div class="addshengriquan_right_02_2" <? if(!empty($yhq)){?>style="background-color:<? echo $yhq->color.'"';}?>>
                        	<h2><span>￥</span><font id="yulan_money"><?=empty($yhq)?'0':str_replace('.00','',$yhq->money)?></font></h2><font id="yulan_man"><?=empty($yhq->man)?'不限制':'满'.$yhq->man.'可用'?></font>
                        </div>
                    	<div class="addshengriquan_right_02_3">
                        	<div class="addshengriquan_right_02_3_up">	
                            	<h2 id="yulan_title"><?=empty($yhq)?'优惠券标题':$yhq->title?></h2><span id="yulan_time">优惠券有效期</span>
                            </div>
                        	<div class="addshengriquan_right_02_3_down">
                            	<span id="yulan_xianzhi"><? if($yhq->useType==2){?>部门商品可使用<?}else{?>全部商品可使用<? }?></span> <img src="images/biao_20.png" alt=""/>
                            </div>
                        </div>
                    	<div class="clearBoth"></div>
                    </div>
                	<div class="addshengriquan_right_03" id="yulan_content">
                    	使用说明：<pre><?=$yhq->content?></pre>
                    </div>
                </div>
            	<div class="clearBoth"></div>
            </div>
        </div>
    </div>
    <input type="hidden" id="departs" value="" />
    <input type="hidden" id="users" value="" />
    <input type="hidden" id="departNames" value=""/>
    <input type="hidden" id="userNames" value="" />
    <input type="hidden" id="editId" value="0">
    <div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
    <div class="reveal-modal-bg" style="display:none; cursor: pointer;"></div>
<script type="text/javascript" src="js/yyyx/add_yhq.js"></script>
<? require('views/help.html');?>
</body>
</html>