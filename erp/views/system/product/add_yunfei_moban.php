<?
global $db,$request;
$comId = $_SESSION[TB_PREFIX.'comId'];
$company = json_decode($_SESSION[TB_PREFIX.'company']);
$id = (int)$request['id'];
$mobanid = (int)$request['mobanid'];
$_SESSION['tijiao']=1;
$rules = array();
if(!empty($id)){
    $moban = $db->get_row("select * from yunfei_moban where id=$id");
    $rules = $db->get_results("select * from yunfei_moban_rule where mobanId=$id order by id asc");
}else if(!empty($mobanid)){
    $moban = $db->get_row("select * from yunfei_moban where id=$mobanid");
    $rules = $db->get_results("select * from yunfei_moban_rule where mobanId=$mobanid order by id asc");
}
$rows = 1;
if(!empty($rules))$rows = count($rules);
$weight = $db->get_var("select weight from demo_product_set where comId=$comId");
$showWeight = $moban->accordby==2?$weight:'';
$scene = empty($moban)?$request['scene']:$moban->scene;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title><?=SITENAME?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="styles2/index.css" rel="stylesheet" type="text/css" />
<link href="styles2/lrtk.css" rel="stylesheet" type="text/css" />
<link href="styles/set_common.css" rel="stylesheet" type="text/css" />
<link href="styles/shouye/qiyerenzheng.css" rel="stylesheet" type="text/css" />
<link href="styles/shouye/qiyeshezhi.css" rel="stylesheet" type="text/css" />
<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
<link href="styles/yingyongguanli.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="js2/jquery-1.4.4.min.js"></script>
<script type="text/javascript" src="js2/jquery.reveal.js"></script>
<script type="text/javascript" src="layui/layui.js"></script>
</head>
<body>
<div id="root">
    <div id="main">
    	<div class="cont">
            <div class="cont_01">
                <a href="javascript:history.go(-1)"><img src="images/back.jpg"></a> 运费设置
            </div>
            <form action="?m=system&s=product&a=add_yunfei_moban&submit=1&id=<?=$id?>" method="post" class="layui-form" id="addForm">
                <input type="hidden" name="scene" value="1">
                <div class="yfxinjianmoban">
                    <div class="yfxinjianmoban_1">
                        <div class="yfxinjianmoban_1_left" style="float:left">
                            <div class="yfxinjianmoban_1_left_01">
                                模板名称
                            </div>
                            <div class="yfxinjianmoban_1_left_02" style="width:300px">
                                <input type="text" name="title" placeholder="请输入模板名称" maxlength="30" <? if(!empty($id)){?>value="<?=$moban->title?>"<?}?> class="layui-input" lay-verify="required">
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="yfxinjianmoban_1_right">
                            <input type="checkbox" name="if_man" lay-filter="if_man" value="1" <? if($moban->if_man==1){?>checked="true"<? }?> lay-skin="primary" title="">
                            订单满 <input name="man" type="text" id="man" class="layui-input" style="width:80px;display:inline-block;" <? if($moban->if_man!=1){?>class="disabled" readonly="true"<? }?> value="<?=$moban->man?>" lay-verify="shuzi" /> 元 免<span id="mantype" style="display:inline-block;width:80px;"><select name="mantype"><option value="1">全部</option><option value="2" <? if($moban->mantype==2){?>selected="true"<? }?>>基础</option></select></span>运费
                        </div>
                        <div class="clearBoth"></div>
                        <div class="yfxinjianmoban_1_left">
                            <div class="yfxinjianmoban_1_left_01">
                                计费依据
                            </div>
                            <div class="yfxinjianmoban_1_left_02">
                                <select name="accordby" lay-filter="accordby">
                                    <option value="1" <? if($moban->accordby==1){?>selected="true"<? }?>>按数量</option>
                                    <option value="2" <? if($moban->accordby==2){?>selected="true"<? }?>>按重量</option>
                                </select>
                            </div>
                            <div class="clearBoth"></div>
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="yfxinjianmoban_2">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" id="row_table" data-row="<?=$rows?>">
                            <tbody><tr height="55">
                                <td bgcolor="#eff7fa" width="52" align="center" valign="middle">
                                    
                                </td>
                                <td bgcolor="#eff7fa" width="82" align="center" valign="middle">
                                    
                                </td>
                                <td bgcolor="#eff7fa" width="316" align="center" valign="middle">
                                    计费依据
                                </td>
                                <td bgcolor="#eff7fa" width="170" align="center" valign="middle">
                                    起算量 <br><span style="color:#a6a6a6;">(不足起算量按起算量计)   </span>
                                </td>
                                <td bgcolor="#eff7fa" width="152" align="center" valign="middle">
                                    运费(元)
                                </td>
                                <td bgcolor="#eff7fa" width="147" align="center" valign="middle">
                                    续件
                                </td>
                                <td bgcolor="#eff7fa" width="140" align="center" valign="middle">
                                    续运费(元)
                                </td>
                                <td bgcolor="#eff7fa" width="140" align="center" valign="middle">
                                    模板介绍
                                </td>
                            </tr>
                            <?
                            if(!empty($rules)){
                                foreach ($rules as $i=>$rule){
                                    ?>
                                    <tr height="43" id="row_<?=$i+1?>">
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            1
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            <a href="javascript:addRow();"><img src="images/yingyong_21.png"></a>
                                            <? if($i>0){?>
                                                &nbsp;&nbsp;<a href="javascript:delRow(<?=$i+1?>);"><img src="images/yingyong_22.png"></a>
                                            <? }?>
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            <span style="color:#1f87eb;cursor:pointer;" id="areaFanwei_<?=$i+1?>" <? if($i>0){?>onclick="area_fanwei(<?=$i+1?>);"<? }?>><?=$rule->areaNames?></span>
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            <input type="text" name="base_<?=$i+1?>" value="<?=$rule->base?>" lay-verify="required|shuzi" class="yfxinjianmoban_2_input"> <span class="weight"><?=$showWeight?></span>
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            <input type="text" name="base_price_<?=$i+1?>" value="<?=$rule->base_price?>" lay-verify="required|shuzi" class="yfxinjianmoban_2_input">
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            每 <input type="text" name="add_num_<?=$i+1?>" value="<?=$rule->add_num?>" lay-verify="required|shuzi" class="yfxinjianmoban_2_input"> <span class="weight"><?=$showWeight?></span>
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            <input type="text" name="add_price_<?=$i+1?>" value="<?=$rule->add_price?>" lay-verify="required|shuzi" class="yfxinjianmoban_2_input">
                                            <input type="hidden" class="area_input" name="areaIds_<?=$i+1?>" id="areaIds_<?=$i+1?>" value="<?=$rule->areaIds?>">
                                            <input type="hidden" name="areaNames_<?=$i+1?>" id="areaNames_<?=$i+1?>" value="<?=$rule->areaNames?>">
                                            <input type="hidden" name="rows[]" value="<?=$i+1?>">
                                        </td>
                                        <td bgcolor="#ffffff" align="center" valign="middle">
                                            <input type="text" name="content_<?=$i+1?>" onclick="con(this)" value="<?=$rule->content?>" class="yfxinjianmoban_2_input">
                                        </td>
                                    </tr>
                                    <?
                                }
                            }else{
                                ?>
                                <tr height="43" id="row_1">
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        1
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        <a href="javascript:addRow();"><img src="images/yingyong_21.png"></a>
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        <span style="color:#1f87eb;">通用</span>
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        <input type="text" name="base_1" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input"> <span class="weight"><?=$showWeight?></span>
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        <input type="text" name="base_price_1" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input">
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        每 <input type="text" name="add_num_1" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input"> <span class="weight"><?=$showWeight?></span>
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        <input type="text" name="add_price_1" value="" lay-verify="required|shuzi" class="yfxinjianmoban_2_input">
                                        <input type="hidden" class="area_input" name="areaIds_1" id="areaIds_1" value="0">
                                        <input type="hidden" name="areaNames_1" id="areaNames_1" value="通用">
                                        <input type="hidden" name="rows[]" value="1">
                                    </td>
                                    <td bgcolor="#ffffff" align="center" valign="middle">
                                        <input type="text" name="content_1" onclick="con(this)" value="" class="yfxinjianmoban_2_input">
                                    </td>
                                </tr>
                                <?
                            }
                            ?>
                        </tbody></table>
                    </div>
                    <div class="yfxinjianmoban_4">
                        <button class="layui-btn" lay-submit="" lay-filter="tijiao">提交</button>
                        <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取消并返回</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<input type="hidden" id="departs" value="" />
<input type="hidden" id="users" value="" />
<input type="hidden" id="departNames" value=""/>
<input type="hidden" id="userNames" value="" />
<input type="hidden" id="editId" value="0">
<div id="myModal" class="reveal-modal" style="opacity: 1; visibility: hidden; top:30px;"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<script type="text/javascript">
    var weight = '<?=$weight?>';
    var showWeight = '<?=$showWeight?>';
    <? if($moban->if_man!=1){?>
    window.onload=function(){
        $("#man").addClass("disabled").attr("readonly",true);
        $("#mantype input").addClass("disabled");
    }
    <? }?>
    function con(curr){
        var content  = $(curr).val();
        layui.use('layer', function(){
            layer.open({
                title: '模板介绍'
                ,area: ['500px', '300px']
                ,content: '<textarea id="content" style="width:100%;height:100%;">'+content+'</textarea>'
                ,yes: function(index, layero){
                    $(curr).val($("#content").val());
                    $("#content").val('');
                    layer.closeAll();
                }
            });    
        });  
        console.log(content);
    }
</script>
<script type="text/javascript" src="js/set_yunfei.js"></script>
</body>
</html>
