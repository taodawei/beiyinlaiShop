<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$shenqing_id = (int)$request['shenqing_id'];
$dtTime = date("Y-m-d");
$userInfo = array();
$caiwu = array();
if(!empty($id)){
    $kehu = $db->get_row("select * from demo_shequ where id=$id");
    $dtTime = date("Y-m-d H:i",strtotime($kehu->dtTime));
}else if(!empty($shenqing_id)){
    $kehu = $db->get_row("select * from demo_shequ_shenqing where id=$shenqing_id");
}
$areaId = (int)$kehu->areaId;
$firstId=0;
$secondId=0;
$thirdId=0;
if($areaId>0){
    $area = $db->get_row("select * from demo_area where id=".$areaId);
    if($area->parentId==0){
        $firstId = $area->id;
    }else{
        $firstId = $area->parentId;
        $secondId = $area->id;
        $farea = $db->get_row("select * from demo_area where id=".$area->parentId);
        if($farea->parentId!=0){
            $firstId = $farea->parentId;
            $secondId = $farea->id;
            $thirdId=$area->id;
        }
    }
}
$areas = $db->get_results("select * from demo_area where parentId=0");
if($kehu->userId>0){
    $user = $db->get_row("select nickname,username from users where id=$kehu->userId");
}
$mendianId = isset($request['mendianId']) ? $request['mendianId'] : $_SESSION['mendianId'];

?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="styles/index.css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="styles/selectDparts.css">
    <link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript" src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
    <style type="text/css">
        .redselect .layui-form-checkbox span{color:#ff0101;}
        .cangkugl_xiugai_02_right .layui-select-title input{width:100%}
    </style>
</head>
<body>
	<div class="back1">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
        <div><? if(empty($kehu)){?>添加<? }else{ echo '修改';}?>经销商</div>
    </div>
    <div class="cont" style="height:100%">
        <form action="?s=users&a=add_shequ&tijiao=1&id=<?=$id?>&shenqing_id=<?=$shenqing_id?>" method="post" id="submitForm" class="layui-form">
            <input type="hidden" name="url" value="<?=urlencode($request['returnurl'])?>">
            <div class="provider_cont">
                <div class="cont_h"> 
                    经销商信息
                </div>
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>经销商名称
                            </div>
                            <div class="cont_left_input1">
                                <input name="title" id="title" class="layui-input" lay-verify="required" type="text" value="<?=$kehu->title?>" placeholder="请输入经销商名称" />
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%" style="display:none;">
                            <div class="provider_left_tt">
                                经销商Logo
                            </div>
                            <div class="cont_left_input1">
                                <a href="<?=$kehu->originalPic?>" <? if(empty($kehu->originalPic)){?>style="display:none;"<? }?> target="_blank"><img src="<?=$kehu->originalPic?>" id="img_zhizhao_img" width="100"></a>
                                <input type="hidden" name="originalPic" value="<?=$kehu->originalPic?>" id="img_zhizhao">
                                <button type="button" id="upload1" class="layui-btn">上传</button>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>联系人姓名
                            </div>
                            <div class="cont_left_input1">
                                <input name="name" class="layui-input" lay-verify="required" type="text" value="<?=$kehu->name?>" placeholder="联系人姓名"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td class="provider_td" width="50%">
                            <div class="cont_left_tt">
                                <span class="must">*</span>联系电话
                            </div>
                            <div class="cont_left_input1">
                                <input name="phone" class="layui-input" type="text" value="<?=$kehu->phone?>" placeholder="请输入联系电话"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr>
                        <td class="provider_td" width="50%">
                            <div class="provider_left_tt">
                                <span class="must">*</span>邮箱
                            </div>
                            <div class="cont_left_input1">
                                <input name="weixin" class="layui-input" lay-verify="required" type="text" value="<?=$kehu->weixin?>" placeholder="联系人邮箱"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        
                        <? if($mendianId == 0){ ?>
                        <td class="provider_td" width="50%" style="display:none;">
                            <div class="cont_left_tt">
                                <span class="must">*</span>抽成
                            </div>
                            <div class="cont_left_input1">
                                <input name="bili" class="layui-input" type="number" step="0.01" min="0" max="1" value="<?=$kehu->bili?>" placeholder="请输入0-1抽成比"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <? } ?>
                        
                        <!--<td style="display:none;" class="provider_td" width="50%">-->
                        <!--    <div class="cont_left_tt">-->
                        <!--        <span class="must">*</span>会员ID-->
                        <!--    </div>-->
                        <!--    <div class="cont_left_input1">-->
                        <!--        <input name="userId" class="layui-input" lay-verify="required|number" type="text" value="<?=$kehu->userId?>" placeholder="经销商会员ID/手机号" onchange="get_user_info(this.value,<?=$id?>);" style="width:140px;display:inline-block;"/>-->
                        <!--        <span id="user_info" style="color:red"><? if(!empty($user)){echo $user->nickname.'('.$user->username.')';}?></span>-->
                        <!--    </div>-->
                        <!--    <div class="clearBoth"></div>-->
                        <!--</td>-->
                    </tr>
                    <tr>
                        <td class="provider_td">
                            <div class="provider_left_tt">
                                <span class="must">*</span>所在区域
                            </div>
                            <div class="cont_left_input1">
                                <div style="width:32%;display:inline-block;">
                                    <input type="hidden" name="psarea" id="psarea" value="<?=$kehu->areaId?>" />
                                    <input type="hidden" name="shiId" id="shiId" value="<?=$kehu->shiId?>">
                                    <select id="ps1" lay-filter="ps1" lay-verify="required">
                                        <option value="">选择省份</option>
                                        <?if(!empty($areas)){
                                            foreach ($areas as $hangye) {
                                                ?><option value="<?=$hangye->id?>" <?=($hangye->id==$firstId?'selected="selected"':'')?>><?=$hangye->title?></option><?
                                            }
                                        }?>
                                    </select>
                                </div>
                                <div style="width:32%;display:inline-block;">
                                    <select id="ps2" lay-filter="ps2" lay-verify="required"><option value="">请先选择省</option>
                                        <?
                                        if($firstId>0){
                                            $areas1 = $db->get_results("select id,title from demo_area where parentId=$firstId");
                                            if(!empty($areas1)){
                                                foreach ($areas1 as $hangye) {?>
                                                <option value="<?=$hangye->id?>" <?=($hangye->id==$secondId?'selected="selected"':'')?> ><?=$hangye->title?></option>
                                                <?}
                                            }
                                        }?>
                                    </select>
                                </div>
                                <div style="width:32%;display:inline-block;">
                                    <select id="ps3" lay-filter="ps3"><option value="">请先选择市</option>
                                        <? if($secondId>0){
                                            $areas2 = $db->get_results("select id,title from demo_area where parentId=$secondId");
                                            if(!empty($areas2)){
                                                foreach ($areas2 as $hangye) {?>
                                                <option value="<?=$hangye->id?>" <?=($hangye->id==$thirdId?'selected="selected"':'')?> ><?=$hangye->title?></option>
                                                <?}
                                            }
                                        }?>
                                    </select>
                                </div>
                            </div>
                        </td>
                        <td class="provider_td" valign="top">
                            <div class="cont_left_tt">
                                详细地址
                            </div>
                            <div class="cont_left_input1">
                                <input name="address" class="layui-input" lay-verify="required" type="text" value="<?=$kehu->address?>" placeholder="请输入详细地址"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    <tr style="display:none;">
                        <td class="provider_td" valign="top">
                            <div class="provider_left_tt">
                                配送小区范围
                            </div>
                            <div class="cont_left_input1">
                                <input name="peisong_area" class="layui-input" type="text" value="<?=$kehu->peisong_area?>" placeholder="请输入配送范围内的小区/街道名称"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                        <td></td>
                    </tr>
                </table>
            </div>
               <!--<tr>
                <td class="provider_td" valign="top">
                    <div class="provider_left_tt">
						<span class="must">*</span>标注地址
					</div>
					<div class="churukushezhi_01_down_2">
						<style type="text/css">
							html,body{margin:0;padding:0;}
							.iw_poi_title {color:#CC5522;font-size:14px;font-weight:bold;overflow:hidden;padding-right:13px;white-space:nowrap}
							.iw_poi_content {font:12px arial,sans-serif;overflow:visible;padding-top:4px;white-space:-moz-pre-wrap;word-wrap:break-word}
						</style>
						<?php $heng=$kehu->longitude;$zong=$kehu->Latitude;?>
						<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=cIUKusewZaKmqALQv6lKtIcY&s=1"></script>
						地址坐标：
						<input type="text" id="TextBox1" name="hengzuobiao" value="<?=$heng?>" style="height: 35px;line-height: 35px;border: 1px #e1e4ea solid;" />
						<input type="text" id="TextBox2" name="zongzuobiao" value="<?=$zong?>" style="height: 35px;line-height: 35px;border: 1px #e1e4ea solid;" /> <span style="color:red;">点击地图标注签到详细坐标位置</span><br>
						<input id="address" type="text" class="new_qdgz_input" placeholder="收入所在地址" style="width:400px;">
						<input type="button" class="new_qdgz_input" onclick="searchMap();" value="检索" style="width:50px;padding-left:0px;height:37px;"/>
						<div style="width:600px;height:400px;border:#ccc solid 1px;margin:0px;margin-top:10px;" id="container"></div>

						<script type="text/javascript">
							var map = new BMap.Map("container");
							<? if($heng>0){?>
								map.centerAndZoom(new BMap.Point(<?=$heng?>, <?=$zong?>), 18);
								var point1 = new BMap.Point(<?=$heng?>,<?=$zong?>);
								var marker1 = new BMap.Marker(point1);
								map.addOverlay(marker1);
							<? }else{?>
								map.centerAndZoom("保定",12);
							<? }?>
							var top_left_control = new BMap.ScaleControl({anchor: BMAP_ANCHOR_TOP_LEFT});// 左上角，添加比例尺
							var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件      
							map.addControl(top_left_control);
							map.addControl(top_left_navigation);
							map.enableScrollWheelZoom();   //启用滚轮放大缩小，默认禁用
							map.enableContinuousZoom();
							//单击获取点击的经纬度
							map.addEventListener("click",function(e){
								document.getElementById('TextBox1').value = e.point.lng;
								document.getElementById('TextBox2').value = e.point.lat;
								map.clearOverlays();
								var point = new BMap.Point(e.point.lng,e.point.lat);
								var marker = new BMap.Marker(point);
								map.addOverlay(marker);
								//alert("标注成功，您标注的位置："+e.point.lng+","+e.point.lat);
							});
							function searchMap(){
								var add = $("#address").val();
								var local = new BMap.LocalSearch(map, {
									renderOptions:{map: map, panel:"r-result"},
									pageCapacity:5
								});
								local.search(add);
							}
						</script>
					</div>
					<div class="clearBoth"></div>
                </td>
                <td></td>
            </tr>-->
            
            
            <div class="purchase_affirm3">
                <div class="relevance_affirm">
                    <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                    <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
                </div>
                <div class="clearBoth"></div>
            </div>
        </form>
    </div>
    <div class="cangkugl_xiugai" id="cangkugl_xiugai" style="height:240px">
      <div class="cangkugl_xiugai_01">
        新增销售区域
      </div>
      <form id="editForm" method="post" class="layui-form">
        <div class="cangkugl_xiugai_02">
          <ul>
            <li>
              <div class="cangkugl_xiugai_02_left">
               销售区域
             </div>
             <div class="cangkugl_xiugai_02_right" style="width:365px;">
              <div style="width:32%;display:inline-block;">
                <select id="pss1" lay-filter="pss1">
                  <option value="">选择省份</option>
                  <?if(!empty($areas)){
                    foreach ($areas as $hangye) {
                      ?><option value="<?=$hangye->id?>"><?=$hangye->title?></option><?
                    }
                  }?>
                </select>
              </div>
              <div style="width:32%;display:inline-block;">
                <select id="pss2" lay-filter="pss2"><option value="">请先选择省</option>
                </select>
              </div>
              <div style="width:32%;display:inline-block;">
                <select id="pss3" lay-filter="pss3"><option value="">请先选择市</option>
                </select>
              </div>
            </div>
            <div class="clearBoth"></div>
          </li>
          <div class="clearBoth"></div>
        </ul>
      </div>
      <div class="cangkugl_xiugai_03">
        <button class="layui-btn" lay-submit="" lay-filter="tijiao1">添 加</button>
        <button class="layui-btn layui-btn-primary" onclick="$('#cangkugl_xiugai').hide();$('#bg').hide();return false;">取 消</button>
      </div>
      <input type="hidden" id="add_area_id" value="0">
    </form>
    </div>
    <div id="bg"></div>
    <div id="myModal" class="reveal-modal">
      <div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>
    </div>
    <script type="text/javascript">
      layui.use(['laydate','form','upload'], function(){
        var form = layui.form,
        upload = layui.upload,
        laydate = layui.laydate;
        upload.render({
            elem: '#upload1'
            ,url: '?m=system&s=upload&a=upload'
            ,before:function(){
                layer.load();
            }
            ,done: function(res){
                layer.closeAll('loading');
                if(res.code > 0){
                    return layer.msg(res.msg);
                }else{
                    $("#img_zhizhao").val(res.url);
                    $("#img_zhizhao_img").attr('src',res.url).parent().show().attr("href",res.url);
                }
            }
            ,error: function(){
                layer.msg('上传失败，请重试', {icon: 5});
            }
        });
        form.on('select(ps1)',function(data){
            if(!isNaN(data.value)){
                layer.load();
                id = data.value;
                ajaxpost=$.ajax({
                    type:"POST",
                    url:"/erp_service.php?action=getAreas",
                    data:"id="+id,
                    timeout:"4000",
                    dataType:"text",
                    success: function(html){
                        $("#ps3").html('<option value="">请先选择市</option>');
                        if(html!=""){
                            $("#ps2").html(html);
                        }
                        form.render('select');
                        layer.closeAll('loading');
                    },
                    error:function(){
                        alert("超时,请重试");
                    }
                });
            }            
        });
        form.on('select(ps2)',function(data){
            if(!isNaN(data.value)){
                layer.load();
                id = data.value;
                ajaxpost=$.ajax({
                    type:"POST",
                    url:"/erp_service.php?action=getAreas",
                    data:"id="+id,
                    timeout:"4000",
                    dataType:"text",
                    success: function(html){
                        if(html!=""){
                            $("#ps3").html(html);
                            $("#shiId").val(id);
                        }else{
                            $("#shiId").val(id);
                        }
                        form.render('select');
                        layer.closeAll('loading');
                    },
                    error:function(){
                        alert("超时,请重试");
                    }
                });
            }
        });
        form.on('select(ps3)',function(data){
            if(!isNaN(data.value)){
                $("#psarea").val(data.value);
            }
        });
        form.on('submit(tijiao)', function(data){
           layer.load();
        });
    });
    function get_user_info(uid,shequId){
        layer.load();
        $.ajax({
            type: "POST",
            url: "?m=system&s=users&a=get_user_info",
            data: "uid="+uid+"&shequId="+shequId,
            dataType:"json",timeout : 20000,
            success: function(resdata){
                layer.closeAll();
                if(resdata.code==0){
                    layer.msg(resdata.message,function(){});
                    $("#user_info").prev().val('');
                    return false;
                }else{
                    $("#user_info").text(resdata.user_info);
                }
            },
            error: function() {
                layer.closeAll();
                layer.msg('数据请求失败，请检查网络', {icon: 5});
            }
        });
    }
</script>
</body>
</html>