<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$id = (int)$request['id'];
if(!empty($id))$cuxiao = $db->get_row("select * from cuxiao_pdt where id=$id");
if(!empty($cuxiao->guizes)){
    $guizes = json_decode($cuxiao->guizes,true);
}
if($id){
    $startTime = date('Y-m-d H:i:s', strtotime($cuxiao->startTime));
    $endTime = date('Y-m-d H:i:s', strtotime($cuxiao->endTime));
}
$today = date('Y-m-d');
$end = date('Y-m-d', strtotime('+1 week'));
$levels = $db->get_results("select id,title from user_level where comId=$comId order by jifen asc");
$kehu_levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$mendians = $db->get_results("select id,title from mendian where comId=$comId order by id asc");


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
    <link href="styles/dinghuoguanli.css" rel="stylesheet" type="text/css">
    <link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css" />
    <link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
    <script type="text/javascript"  src="layui/layui.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
    .sprukuadd_04 ul li{display:inline-block;width:523px;margin-right:20px;}
    .add_other,.add_pay{float:right}
    .add_other{padding:17px 15px 5px 0;font-size:13px;color:#6a6a6a;line-height:34px}
    .add_other div{padding-bottom:5px}
    #shouhuoDiv img,#fapiaoCont img{cursor:pointer;}
    .layui-table-cell{height:36px}
    .sprukuadd_03_tt_input{width:60px;}
    .yx_spcuxiaoadd_2_right_guize_01{margin-left:30px;margin-top:10px;}
    .yx_spcuxiaoadd_2_right_guize_01 input{height:30px;}
    .sprukuadd_03_tt .num{height: 25px;width: 100px;text-align: center;}
    .sprukulist_01_left span{padding: 0px;}
</style>
</head>
<body>
    <div class="right_up">
        <a href="?s=yyyx&a=cuxiao"><img src="images/back.gif"/></a> 新增商品限时促销
    </div>
    <div class="right_down">
     <div class="sprukuadd">
        <form id="addForm" action="" method="post" class="layui-form">
            <input type="hidden" name="startTime" id="startTime" value="<?=$startTime?>">
            <input type="hidden" name="endTime" id="endTime"  value="<?=$endTime?>">
            <input type="hidden" name="areaIds" id="areaIds" value="<?=$cuxiao->areaIds?>">
            <div class="dhd_adddinghuodan_1">
                <div class="dhd_adddinghuodan_1_left"><span>*</span> 限时促销：</div>
                <div class="dhd_adddinghuodan_1_right" style="width:400px;">
                    <div class="dhd_adddinghuodan_1_right_01">
                        <input type="text" name="title" lay-verify="required" value="<?=$cuxiao->title?>" class="layui-input" placeholder="输入限时促销主题">
                    </div>
                    <div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
                <div style="margin-top:10px;">
                    <div class="dhd_adddinghuodan_1_left"><span>*</span> 促销时间：</div>
                    <div class="dhd_adddinghuodan_1_right" style="border:0px;">
                        <div class="sprukulist_01" style="margin-left:0px;top:0px;width:400px;">
                            <div class="sprukulist_01_left" style="width:340px;">
                                <span id="s_time1" ><?=empty($startTime)?'&nbsp;&nbsp;&nbsp;&nbsp;选择日期&nbsp;&nbsp;&nbsp;&nbsp;':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'&nbsp;&nbsp;&nbsp;&nbsp;选择日期&nbsp;&nbsp;&nbsp;&nbsp;':$endTime?></span>
                            </div>
                            <div class="sprukulist_01_right">
                                <img src="images/biao_76.png"/>
                            </div>
                            <div class="clearBoth"></div>
                            <div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index:99">
                                <div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                            </div>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                </div>

                
                <div class="dhd_adddinghuodan_1_left" style="margin-top:5px;width:100%"><span>*</span> 促销产品：</div>
            </div>
            <div class="sprukuadd_03">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" id="dataTable" rows="<?=empty($cuxiao->pdtIds)?'1':count(explode(',',$cuxiao->pdtIds))?> 1">
                    <tr height="43">
                        <td bgcolor="#7bc8ed" width="70" class="sprukuadd_03_title" align="center" valign="middle"></td>
                        <td bgcolor="#7bc8ed" width="118" class="sprukuadd_03_title" align="center" valign="middle"></td>
                        <td bgcolor="#7bc8ed" width="166" class="sprukuadd_03_title" align="center" valign="middle">商品编码</td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">商品名称</td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">每人限购数量</td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">活动价格</td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">规格</td>
                        <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle">单位</td>
                    </tr>
                    <? if(empty($cuxiao->pdtIds)){?>
                        <tr height="48" id="rowTr1">
                            <td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"> 1</td>
                            <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">
                                <a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow(1);"><img src="images/biao_66.png"/></a>  
                            </td>
                            <td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">
                                <div class="sprukuadd_03_tt_addsp">
                                    <div class="sprukuadd_03_tt_addsp_left">
                                        <!--<input type="text" class="layui-input addRowtr" id="searchInput1" row="1" placeholder="输入编码/商品名称" >-->
                                    </div>
                                    <div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">
                                       ●●●
                                    </div>
                                    <div class="clearBoth"></div>
                                    <div class="sprukuadd_03_tt_addsp_erji" id="pdtList1">
                                        <ul><li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li></ul>
                                    </div>
                                </div>
                            </td>
                            <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                            <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                            <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                            <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        </tr>
                    <? }else{
                        $pdtIds = explode(',',$cuxiao->pdtIds);
                        foreach ($pdtIds as $key => $pdtId){
                            $inventory = $db->get_row("select id,title,sn,key_vals,productId from demo_product_inventory where id=$pdtId");
                            $units = $db->get_var("select untis from demo_product where id=$inventory->productId");
                            $unitarr = json_decode($units);
                            $shezhi = (int)$db->get_row("select num,price from cuxiao_pdt_xiangou where cuxiao_id=$cuxiao->id and inventoryId=$pdtId");
                            $xiangou_num = $shezhi->num;
                            $activity_price = $shezhi->price;
                            ?>
                            <tr height="48" id="rowTr<?=$key+1?>">
                                <td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                                <td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle"><a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"></a>  <a href="javascript:" onclick="delRow(<?=$key+1?>);"><img src="/erp/images/biao_66.png"></a></td>
                                <td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle"><?=$inventory->sn?></td>
                                <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><?=$inventory->title?></td>
                                <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><input type="number" step="1" name="xiangou[<?=$pdtId?>]" value="<?=$xiangou_num?>" class="num"></td>
                                 <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><input type="float" step="1" name="activity_price[<?=$pdtId?>]" value="<?=$activity_price?>" class="num"></td>
                                <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><?=$inventory->key_vals?></td>
                                <td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><?=$unitarr[0]->title?><input type="hidden" name="inventoryId[<?=$key+1?>]" value="<?=$pdtId?>"></td>
                            </tr>
                            <?
                        }
                    }?>
                </table>
                <script type="text/javascript">
                    var jishiqi;
                    var kehu_title = '<?=$kehu_title?>';
                    $('#searchInput1').bind('input propertychange', function() {
                        clearTimeout(jishiqi);
                        var row = $(this).attr('row');
                        var val = $(this).val();
                        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
                    });
                    $('#searchInput1').click(function(eve){
                        var startTime = $("#startTime").val();
                        var endTime = $("#timeRange option:selected").val();
                        // alert(startTime);
                        // alert(endTime);
                        if(startTime==''||endTime==''){
                            layer.msg("请先选择促销时间22222");
                            return false;
                        }
                        var nowRow = $(this).attr("row");
                        if($("#pdtList"+nowRow).css("display")=="none"){
                            showpdtList(nowRow,$(this).val());
                        }
                        stopPropagation(eve);
                    });
               </script>
            </div>
            <div class="dhd_adddinghuodan_3" style="display:none;">
             <ul>
                <li>
                    <div class="dhd_adddinghuodan_3_left">促销类型：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="radio" name="accordType" lay-filter="accord1" <? if($cuxiao->accordType==1){?>checked="true"<? }?> value="1" title="按数量">
                        <input type="radio" name="accordType" lay-filter="accord2" <? if($cuxiao->accordType==2){?>checked="true"<? }?> value="2" title="按金额">
                        <input type="radio" name="accordType" lay-filter="accord3" <? if(empty($cuxiao)|| $cuxiao->accordType==3){?>checked="true"<? }?> value="3" title="限时促销">
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <!--<li id="rule_li">
                    <div class="dhd_adddinghuodan_3_left">促销规则：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="yx_spcuxiaoadd_2_right_guize">
                            <input type="radio" name="type" value="1" lay-filter="type1" <? if(empty($cuxiao)||$cuxiao->type==1){?>checked="true"<? }?> title="满赠"/><br>
                            <div class="yx_spcuxiaoadd_2_right_guize_01" id="qujian1" rows="<?=$cuxiao->type==1?count($guizes):1?>" <? if(!empty($cuxiao)&&$cuxiao->type!=1){?>style="display:none"<? }?>>
                                <? if($cuxiao->type==1){
                                    foreach ($guizes as $key => $g){
                                        $pdtName = $db->get_var("select title from demo_product_inventory where id=".$g['inventoryId']);
                                        ?>
                                        <div id="rows_1_<?=$key+1?>">
                                            订购<font class="accord_name"><?=$cuxiao->accordType==2?'金额':'数量'?></font>每满<span><input type="number" step="1" name="man_1_<?=$key+1?>" value="<?=$g['man']?>" /></span>，获赠品<a href="javascript:" onclick="fanwei(<?=$key+1?>);" id="pdt_1_<?=$key+1?>" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp"><?=$pdtName?></a><span><input type="number" step="1" name="jian_1_<?=$key+1?>" value="<?=$g['jian']?>"/></span>个
                                            <input type="hidden" name="inventoryId_1_<?=$key+1?>" id="inventoryId_1_<?=$key+1?>" class="inventory_input" value="<?=$g['inventoryId']?>">
                                            <input type="hidden" name="rows_1[]" value="<?=$key+1?>" ><? if($key>0){?>&nbsp;<a href="javascript:" onclick="del_rows('1_<?=$key+1?>')"><img src="images/yingxiao_30.png"></a><? }?>
                                        </div>
                                        <?
                                    }
                                }else{?>
                                <div id="rows_1_1">
                                    订购<font class="accord_name"><?=$cuxiao->accordType==2?'金额':'数量'?></font>每满<span><input type="number" step="1" name="man_1_1" /></span>，获赠品<a href="javascript:" onclick="fanwei(1);" id="pdt_1_1" class="yx_spcuxiaoadd_2_right_guize_01_xuanzesp">请选择商品</a><span><input type="number" step="1" name="jian_1_1"/></span>个
                                    <input type="hidden" name="inventoryId_1_1" id="inventoryId_1_1" class="inventory_input" value="0">
                                    <input type="hidden" name="rows_1[]" value="1" >
                                </div>
                                <? }?>
                                <a href="javascript:" onclick="add_qujian(this,1);">+添加区间</a>
                            </div>
                            <input type="radio" name="type" value="2" lay-filter="type2" <? if($cuxiao->type==2){?>checked="true"<? }?> title="满减" /><br>
                            <div class="yx_spcuxiaoadd_2_right_guize_01" id="qujian2" rows="<?=$cuxiao->type==2?count($guizes):1?>" <? if($cuxiao->type!=2){?>style="display:none"<? }?>>
                                <? if($cuxiao->type==2){
                                    foreach ($guizes as $key => $g){
                                        ?>
                                        <div id="rows_2_<?=$key+1?>">
                                            订购<font class="accord_name"><?=$cuxiao->accordType==2?'金额':'数量'?></font>每满<span><input type="number" step="1" name="man_2_<?=$key+1?>" value="<?=$g['man']?>"/></span>，订单金额减<span><input type="number" step="1" name="jian_2_<?=$key+1?>" value="<?=$g['jian']?>"/></span>元
                                            <input type="hidden" name="rows_2[]" value="<?=$key+1?>" ><? if($key>0){?>&nbsp;<a href="javascript:" onclick="del_rows('2_<?=$key+1?>')"><img src="images/yingxiao_30.png"></a><? }?>
                                        </div>
                                        <?
                                    }
                                }else{?>
                                <div id="rows_2_1">
                                    订购<font class="accord_name"><?=$cuxiao->accordType==2?'金额':'数量'?></font>每满<span><input type="number" step="1" name="man_2_1"/></span>，订单金额减<span><input type="number" step="1" name="jian_2_1"/></span>元
                                    <input type="hidden" name="rows_2[]" value="1" >
                                </div>
                                <? }?>
                                <a href="javascript:" onclick="add_qujian(this,2);">+添加区间</a>
                            </div>  
                            <input type="radio" name="type" value="3" lay-filter="type3" <? if($cuxiao->type==3){?>checked="true"<? }?> title="满折" /> <b style="font-weight:normal;color:#b5b5b5;position:relative;top:5px;">（促销商品应付总额=促销商品原订货总额×促销折扣）</b>
                            <div class="yx_spcuxiaoadd_2_right_guize_01" id="qujian3" rows="<?=$cuxiao->type==3?count($guizes):1?>" <? if($cuxiao->type!=3){?>style="display:none"<? }?>>
                                <? if($cuxiao->type==3){
                                    foreach ($guizes as $key => $g){
                                        ?>
                                        <div id="rows_3_<?=$key+1?>">
                                            订购<font class="accord_name"><?=$cuxiao->accordType==2?'金额':'数量'?></font>每满<span><input type="number" step="1" name="man_3_<?=$key+1?>" value="<?=$g['man']?>"/></span>，订单金额打折<span><input type="number" step="0.01" name="jian_3_<?=$key+1?>" onchange="checkzhe('3_<?=$key+1?>');" value="<?=$g['jian']?>"/></span>%
                                            <input type="hidden" name="rows_3[]" value="<?=$key+1?>" ><? if($key>0){?>&nbsp;<a href="javascript:" onclick="del_rows('3_<?=$key+1?>')"><img src="images/yingxiao_30.png"></a><? }?>
                                        </div>
                                        <?
                                    }
                                }else{?>
                                <div id="rows_3_1">
                                    订购<font class="accord_name"><?=$cuxiao->accordType==2?'金额':'数量'?></font>每满<span><input type="number" step="1" name="man_3_1"/></span>，订单金额打折<span><input type="number" step="0.01" onchange="checkzhe('3_1');" name="jian_3_1"/></span>%
                                    <input type="hidden" name="rows_3[]" value="1" >
                                </div>
                                <? }?>
                                <a href="javascript:" onclick="add_qujian(this,3);">+添加区间</a>
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>-->
                <li>
                    <div class="dhd_adddinghuodan_3_left">应用场景：</div>
                    <div class="dhd_adddinghuodan_3_right">
                        <input type="radio" name="scene" lay-filter="scene1" <? if(empty($cuxiao)||$cuxiao->scene==1){?>checked="true"<? }?> value="1" title="线上商城">
                        <input type="radio" name="scene" lay-filter="scene2" <? if($cuxiao->scene==2){?>checked="true"<? }?> value="2" title="订货平台">
                        <input type="radio" name="scene" lay-filter="scene3" <? if($cuxiao->scene==3){?>checked="true"<? }?> value="3" title="线下门店">
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li id="levelli" <? if($cuxiao->scene==2){?>style="display:none;"<? }?>>
                    <div class="dhd_adddinghuodan_3_left">
                        促销对象：
                    </div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="addshengriquan_left_03_right_huiyuan">
                            <input type="radio" name="if_level" lay-filter="level1" value="0" <? if(empty($cuxiao->levelIds)){?>checked="true"<? }?> title="全部会员"><br>
                            <input type="radio" name="if_level" lay-filter="level2" value="1" <? if(!empty($cuxiao->levelIds)){?>checked="true"<? }?> title="指定会员级别">
                            <div style="margin-left:10px;margin-top:8px;<? if(empty($cuxiao->levelIds)){echo 'display:none;';}?>" id="level_div">
                                <? if(!empty($levels)){
                                    $levelArry = array();
                                    if(!empty($cuxiao->levelIds))$levelArry = explode(',',$cuxiao->levelIds);
                                    foreach ($levels as $l) {
                                        ?><input type="checkbox" name="levels[]" <? if(in_array($l->id,$levelArry)){?>checked="true"<? }?> value="<?=$l->id?>" lay-skin="primary" title="<?=$l->title?>"><?
                                    }
                                }?>
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li id="levelli1" <? if($cuxiao->scene!=2){?>style="display:none;"<? }?>>
                    <div class="dhd_adddinghuodan_3_left">
                        促销对象：
                    </div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="addshengriquan_left_03_right_huiyuan">
                            <input type="radio" name="if_level1" lay-filter="level11" value="0" <? if(empty($cuxiao->levelIds1)){?>checked="true"<? }?> title="全部<?=$kehu_title?>"><br>
                            <input type="radio" name="if_level1" lay-filter="level12" value="1" <? if(!empty($cuxiao->levelIds1)){?>checked="true"<? }?> title="指定<?=$kehu_title?>级别">
                            <div style="margin-left:10px;margin-top:8px;<? if(empty($cuxiao->levelIds1)){echo 'display:none;';}?>" id="level_div1">
                                <? if(!empty($kehu_levels)){
                                    $levelArry = array();
                                    if(!empty($cuxiao->levelIds1))$levelArry = explode(',',$cuxiao->levelIds1);
                                    foreach ($kehu_levels as $l) {
                                        ?><input type="checkbox" name="levels1[]" <? if(in_array($l->id,$levelArry)){?>checked="true"<? }?> value="<?=$l->id?>" lay-skin="primary" title="<?=$l->title?>"><?
                                    }
                                }?>
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li id="areasli" <? if($cuxiao->scene==3){?>style="display:none;"<? }?>>
                    <div class="dhd_adddinghuodan_3_left">
                        促销区域：
                    </div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="addshengriquan_left_03_right_quyu">
                            <div class="addshengriquan_left_03_right_quyu_1">
                                <input type="radio" name="if_area" lay-filter="area1" value="0" <? if(empty($cuxiao->areaIds)){?>checked="true"<? }?> title="全部地区"><br>
                                <input type="radio" name="if_area" lay-filter="area2" value="1" <? if(!empty($cuxiao->areaIds)){?>checked="true"<? }?> title="指定地区">
                            </div>
                            <div class="addshengriquan_left_03_right_quyu_2" id="area_div" <? if(empty($cuxiao->areaIds)){echo 'style="display:none;"';}?>>
                                <input type="text" id="areaIdsFanwei" placeholder="选择区域" onclick="area_fanwei('areaIds');" readonly="true" value="<?=empty($cuxiao->areaIds)?'':$db->get_var("select group_concat(title) from demo_area where id in($cuxiao->areaIds)");?>"/>
                                <div class="clearBoth"></div>
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li id="mendianli" <? if($cuxiao->scene!=3){?>style="display:none;"<? }?>>
                    <div class="dhd_adddinghuodan_3_left">
                        适用门店：
                    </div>
                    <div class="dhd_adddinghuodan_3_right">
                        <div class="addshengriquan_left_03_right_huiyuan">
                            <input type="radio" name="if_mendian" lay-filter="mendian1" value="0" <? if(empty($cuxiao->mendianIds)){?>checked="true"<? }?> title="所有门店"><br>
                            <input type="radio" name="if_mendian" lay-filter="mendian2" value="1" <? if(!empty($cuxiao->mendianIds)){?>checked="true"<? }?> title="指定门店">
                            <div class="addshengriquan_left_03_mendian" style="margin-left:10px;margin-top:8px;<? if(empty($cuxiao->mendianIds)){echo 'display:none;';}?>">
                                <? if(!empty($mendians)){
                                    $mendianArry = array();
                                    if(!empty($cuxiao->mendianIds))$mendianArry = explode(',',$cuxiao->mendianIds);
                                    foreach ($mendians as $l) {
                                        ?><input type="checkbox" name="mendians[]" <? if(in_array($l->id,$mendianArry)){?>checked="true"<? }?> value="<?=$l->id?>" lay-skin="primary" title="<?=$l->title?>"><?
                                    }
                                }?>
                            </div>
                        </div>
                    </div>
                    <div class="clearBoth"></div>
                </li>
            </ul>
        </div>
        <div class="sprukuadd_05">
            <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
        </div>
    </form>
</div>
</div>
<div class="sprkadd_xuanzesp">
    <div class="sprkadd_xuanzesp_01">
        <div class="sprkadd_xuanzesp_01_1">选择商品</div>
        <div class="sprkadd_xuanzesp_01_2" style="position:relative;">
            <div class="splist_up_01_left_01_up" style="height:37px;line-height:37px;">
                <span>全部分类</span> <img src="images/biao_20.png"/>
            </div>
            <div class="splist_up_01_left_01_down">
                <ul style="border-left:0px" id="ziChannels1">
                    <li class="allsort_01"><a href="javascript:selectChannel(0,'全部分类');">全部分类</a></li>
                    <? if(!empty($channels)){
                      foreach ($channels as $c) {
                          ?>
                          <li class="allsort_01">
                            <a href="javascript:" onclick="selectChannel(<?=$c->id?>,'<?=$c->title?>');" onmouseenter="loadZiChannels(<?=$c->id?>,2,<? if(!empty($c->channels)){echo 1;}else{echo 0;}?>);" class="allsort_01_tlte"><?=$c->title?> <? if(!empty($c->channels)){?><span><img src="images/biao_24.png"/></span><? }?></a>
                        </li>
                        <?}
                    }?>
                </ul>
            </div>
        </div>
        <div class="sprkadd_xuanzesp_01_3">
            <div class="sprkadd_xuanzesp_01_3_left">
                <input type="text" id="keyword" placeholder="请输入商品名称/编码/规格/关键字">
            </div>
            <div class="sprkadd_xuanzesp_01_3_right">
               <a href="javascript:reloadTable(0);"><img src="images/biao_21.gif"></a>
           </div>
           <div class="clearBoth"></div>
       </div>
       <div class="clearBoth"></div>
    </div>
    <div class="sprkadd_xuanzesp_02">
        <table id="product_list" lay-filter="product_list"></table>
    </div>
    <div class="sprkadd_xuanzesp_03">
       <a href="javascript:" id="sprkadd_xuanzesp_03_01" class="sprkadd_xuanzesp_03_01">确  认</a><a href="javascript:hideSearch();" class="sprkadd_xuanzesp_03_02">取  消</a>
    </div>
</div>
<input type="hidden" id="channelId" value="<?=$channelId?>">
<input type="hidden" id="departs" value="" />
<input type="hidden" id="users" value="" />
<input type="hidden" id="departNames" value=""/>
<input type="hidden" id="userNames" value="" />
<input type="hidden" id="editId" value="0">
<div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<script type="text/javascript">
    var productListTalbe;
    var productListForm;
    layui.use(['laydate', 'laypage','table','form','upload'], function(){
        var laydate = layui.laydate
        ,laypage = layui.laypage
        ,table = layui.table
        ,form = layui.form
        ,upload = layui.upload
        ,active = { //产品多选后的渲染
            appendCheckData: function(){
              var checkStatus = table.checkStatus('product_list')
              ,data = checkStatus.data;
              if(data.length>0){
               var num = parseInt($("#dataTable").attr("rows"));
               var rownums = $("#dataTable tr").length;
               $("#dataTable tr").eq(rownums-1).remove();
               for (var i = 0; i < data.length; i++) {
                   var inventoryId = data[i].id;
                   var sn = data[i].sn;
                   var title = data[i].title;
                   var key_vals = data[i].key_vals;
                   var units = data[i].units;
                   var productId = data[i].productId;
                   num = num+1;
                   var str = '<tr height="48" id="rowTr'+num+'"><td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                   '<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
                   '<a href="javascript:" onclick="addRow();"><img src="/erp/images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="/erp/images/biao_66.png"/></a> '+ 
                   '</td>'+
                   '<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
                   '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
                   '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><input type="number" step="1" name="xiangou['+inventoryId+']" placeholder="0或空代表不限购" class="num"></td>'+
                    '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle"><input type="float" step="1" name="activity_price['+inventoryId+']" placeholder="0或空代表原价" class="num"></td>'+
                   '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
                   '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+
                   '<input type="hidden" name="inventoryId['+num+']" value="'+inventoryId+'">'+
                   '<input type="hidden" name="inventorySn['+num+']" value="'+sn+'">'+
                   '<input type="hidden" name="inventoryTitle['+num+']" value="'+title+'">'+
                   '<input type="hidden" name="inventoryKey_vals['+num+']" value="'+key_vals+'">'+
                   '<input type="hidden" name="inventoryPdtId['+num+']" value="'+productId+'">'+
                   '</td></tr>';
                   $("#dataTable").append(str);
                }
                $("#dataTable").attr("rows",num);
                addRow1();
                hideSearch();
            }else{
                hideSearch();
            }
        }
    }
    laydate.render({
        elem: '#riqi1'
        ,show: true
        ,position: 'static'
        ,min: '<?=$today?>'
        ,max: '<?=$end?>'
        ,type:'datetime'
        ,format: 'yyyy-MM-dd HH:mm:ss'
        ,btns: ['confirm']
        ,done: function(value, date, endDate){
            $("#s_time1").html(value);
            $("#startTime").val(value);
        }
    });
    laydate.render({
        elem: '#riqi2'
        ,show: true
        ,position: 'static'
        ,min: '<?=$today?>'
        ,btns: ['confirm']
        ,type:'datetime'
        ,format: 'yyyy-MM-dd HH:mm:ss'
        ,done: function(value, date, endDate){
            $("#s_time2").html(value);
            $("#endTime").val(value);
        }
    });
    $(".laydate-btns-confirm").click(function(){
        $("#riqilan").slideUp(200);
    });
    productListForm = form;
    //渲染选择产品列表
    productListTalbe = table.render({
        elem: '#product_list'
        ,height: "full-250"
        ,url: '?m=system&s=caigou&a=getpdts&cuxiao=1'
        ,page: true
        ,cols: [[{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:"display:none;"},{field: 'productId', title: 'productId', width:0,style:"display:none;"},{field:'sn',title:'商品编码',width:150},{field:'title',title:'商品名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'units',title:'单位',width:175}]]
        ,done: function(res, curr, count){
            $("th[data-field='id']").hide();
            $("th[data-field='productId']").hide();
            $("#page").val(curr);
            layer.closeAll('loading');
        }
    });
    //类型切换
    form.on('radio(accord1)',function(){
        $("#rule_li").show();
        $(".accord_name").html('数量');
    });
    form.on('radio(accord2)',function(){
        $("#rule_li").show();
        $(".accord_name").html('金额');
    });
    form.on('radio(accord3)',function(){
        $("#rule_li").hide();
    });
    //规则切换
    form.on('radio(type1)',function(){
        $("#qujian1").show();
        $("#qujian2").hide();
        $("#qujian3").hide();
    });
    form.on('radio(type2)',function(){
        $("#qujian2").show();
        $("#qujian1").hide();
        $("#qujian3").hide();
    });
    form.on('radio(type3)',function(){
        $("#qujian3").show();
        $("#qujian1").hide();
        $("#qujian2").hide();
    });
    //切换应用场景
    form.on('radio(scene1)',function(){
        $("#mendianli").hide();
        $("#areasli").show();
        $("#levelli").show();
        $("#levelli1").hide();
    });
    form.on('radio(scene2)',function(){
        $("#mendianli").hide();
        $("#areasli").show();
        $("#levelli").hide();
        $("#levelli1").show();
    });
    form.on('radio(scene3)',function(){
        $("#mendianli").show();
        $("#areasli").hide();
        $("#levelli").show();
        $("#levelli1").hide();
    });
    form.on('radio(area1)',function(){
        $("#area_div").hide();
    });
    form.on('radio(area2)',function(){
        $("#area_div").show();
    });
    form.on('radio(level1)',function(){
        $("#level_div").hide();
    });
    form.on('radio(level2)',function(){
        $("#level_div").show();
    });
    form.on('radio(level11)',function(){
        $("#level_div").hide();
    });
    form.on('radio(level12)',function(){
        $("#level_div1").show();
    });
    form.on('radio(mendian1)',function(){
        $(".addshengriquan_left_03_mendian").hide();
    });
    form.on('radio(mendian2)',function(){
        $(".addshengriquan_left_03_mendian").show();
    });
    form.on('submit(tijiao)', function(data){
        var startTime = $("#startTime").val();
        var endTime = $("#timeRange option:selected").val();
        // alert(11111);
        if(startTime==''||endTime==''){
            layer.msg("请先选择促销时间111111",function(){});
            return false;
        }
        var pdtNums = $("input[name^='inventoryId[']").length;
        if(pdtNums==0){
            layer.msg("请先选择促销商品",function(){});
            return false;
        }
        var type = parseInt($("input[name='type']:checked").val());
        tijiao = 1;
        var accordType = data.field.accordType;
        if(accordType!=3){
            $("#qujian"+type+" input").each(function(){
                var val = parseFloat($(this).val());
                if(isNaN(val)||val<=0){
                    if($(this).attr('type')=='hidden'){
                        layer.msg("请选择促销赠品",function(){});
                    }else{
                        layer.msg("该字段必须是大于0的数字",function(){});
                    }
                    tijiao = 0;
                    return false;
                }
            });
        }
        if(tijiao==0){
            return false;
        }
        layer.load();
        $.ajax({
            type: "POST",
            url: "?s=yyyx&a=create_cuxiao&submit=1",
            data: $("#addForm").serialize(),
            dataType : "json",timeout : 10000,
            success: function(data) {
                layer.closeAll();
                if(data.code!=1){
                    layer.msg(data.message,{icon:5});
                    return false;
                }else{
                    layer.alert('创建成功',function(){
                        location.href='?s=yyyx&a=cuxiao';
                    });
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                layer.msg("请求超时，请检查网络");
            }
        });
        return false;
    });
    $("#sprkadd_xuanzesp_03_01").on("click", function(){
        active['appendCheckData'].call(this);
    });
});

function addRow1(){
	var num = parseInt($("#dataTable").attr("rows"));
	num = num+1;
	var str='<tr height="48" id="rowTr'+num+'">'+
                '<td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
                    '<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a>'+ 
                '</td>'+
                '<td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">'+
                    '<div class="sprukuadd_03_tt_addsp">'+
                        '<div class="sprukuadd_03_tt_addsp_left">'+
                            // '<input type="text" class="layui-input addRowtr" id="searchInput'+num+'" row="'+num+'" placeholder="输入编码/商品名称">'+
                        '</div>'+
                        '<div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">'+
                            '●●●'+
                        '</div>'+
                        '<div class="clearBoth"></div>'+
                        '<div class="sprukuadd_03_tt_addsp_erji" id="pdtList'+num+'">'+
                            '<ul>'+
                                '<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>'+
                            '</ul>'+
                        '</div>'+
                    '</div>'+
                '</td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
            '</tr>';
    $("#dataTable").append(str).attr("rows",num);
    $('#searchInput'+num).bind('input propertychange', function() {
    	clearTimeout(jishiqi);
        var row = $(this).attr('row');
        var val = $(this).val();
        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
    });
    $('#searchInput'+num).click(function(eve){
    	var kehuId = $("#kehuId").val();
    	if(kehuId==''||kehuId==0){
    		layer.msg('请先选择'+kehu_title+'！',function(){});
    		return false;
    	}
    	var nowRow = $(this).attr("row");
    	if($("#pdtList"+nowRow).css("display")=="none"){
    		showpdtList(nowRow,$(this).val());
    	}
    	stopPropagation(eve); 
    });
}
</script>
<script type="text/javascript" src="js/yyyx/create_cuxiao.js"></script>
<? require('views/help.html');?>
</body>
</html>