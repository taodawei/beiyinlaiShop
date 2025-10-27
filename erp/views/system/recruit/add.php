<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$shenqing_id = (int)$request['shenqing_id'];
$dtTime = date("Y-m-d");
$userInfo = array();
$caiwu = array();
if(!empty($id)){
    $kehu = $db->get_row("select * from recruit where id=$id");
    $dtTime = date("Y-m-d H:i",strtotime($kehu->dtTime));
    if($kehu->originalPic){
        $originalPics = explode('|',$kehu->originalPic);  
    }else{
        $originalPics =[];
    }
    $channelIds = explode(',', $kehu->channelIds);
    $occasionIds = explode(',', $kehu->occasionIds);
    
}else if(!empty($shenqing_id)){
    $kehu = $db->get_row("select * from demo_shequ_shenqing where id=$shenqing_id");
}

$firstChannelIds = $db->get_var("select group_concat(id) from demo_change_channel where parentId = 0 ");
if(!$firstChannelIds) $firstChannelIds = 0;

$channels = $db->get_results("select id, title from demo_change_channel where id in ($firstChannelIds) order by ordering desc ");

$occasions = [];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spgl.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="styles/index.css">
	<link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="styles/selectDparts.css">
    <link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="js/jquery.min.js"></script>
    <script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript" src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" src="/ueditor/ueditor.all.js"></script>

    <link href="scripts/colpick.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="scripts/colpick.js"></script>
    <style type="text/css">
        .redselect .layui-form-checkbox span{color:#ff0101;}
        .cangkugl_xiugai_02_right .layui-select-title input{width:100%}
        #pickcolor{position:absolute;right:5px;top:5px;bottom:5px;width:28px;background-color: <?=$kehu->theme_color?$kehu->theme_color:'#FFFFFF'?>;}
        ul li {float:left}
    </style>
</head>
<body>
	<div class="back1">
        <div><a href="javascript:history.go(-1);"><img src="images/back.gif" /></a></div>
        <div><? if(empty($kehu)){?>添加<? }else{ echo '修改';}?>招聘</div>
    </div>
    <div class="cont">
        <form action="?s=recruit&a=add&tijiao=1&id=<?=$id?>" method="post" id="submitForm" class="layui-form">
            <input type="hidden" name="id" value="<?=$id?>">
            <!--<input type="hidden" name="originalPic" value="<?=$kehu->originalPic?>" id="originalPic">-->
            <!--<input type="hidden" name="startTime" id="startTime" value="<?=$startTime?>">-->
            <!--<input type="hidden" name="endTime" id="endTime" value="<?=$endTime?>">-->
            <div class="provider_cont">
                <div class="cont_h"> 
                    招聘信息
                </div>
               
                <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                         <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>岗位名称
                            </div>
                            <div class="cont_left_input1">
                                <input name="title" class="layui-input" type="text" value="<?=$kehu->title?>" lay-verify="required" placeholder="请输入岗位名称"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    
                    <tr>
                         <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>招聘分类
                            </div>
                            <div class="cont_left_input1">
                                <div class="layui-form-select" onmouseenter="tips(this,'请选择卡分类',1);" onmouseout="hideTips();">
									<div class="layui-select-title" id="selectChannel"><input type="text" readonly placeholder="请选择分类" value="<?=$db->get_var("select title from demo_change_channel where id=".(int)$kehu->channelId);?>" class="layui-input"><i class="layui-edge"></i></div>
									<dl class="layui-anim layui-anim-upbit" id="selectChangeChannels"></dl>
								</div>
								<input type="hidden" name="channelId" id="channelId" value="<?=$kehu->channelId?>" lay-verify="required">
                            </div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                    
                    <tr>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>招聘人数
                            </div>
                            <div class="cont_left_input1">
                                <input name="num" class="layui-input" type="number" min="1" step="1" value="<?=$kehu->num?>" lay-verify="required" placeholder="招聘人数"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td> 
                    </tr> 
                        
                    <tr>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>工作地点
                            </div>
                            <div class="cont_left_input1">
                                <input name="address" class="layui-input" type="text" value="<?=$kehu->address?>" lay-verify="required" placeholder="工作地点"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td> 
                    </tr>  
                    
                    <tr>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must">*</span>语言选择
                            </div>
                            <div class="cont_left_input1">
                                <input type="radio" name="language" value="0" title="中文" <? if(empty($kehu->language)){?>checked="checked"<? }?>>
                                <input type="radio" name="language" value="1" title="英文" <? if($kehu->language==1){?>checked="checked"<? }?>>
                            </div>
                            <div class="clearBoth"></div>
                        </td> 
                    </tr>
                    
                    <tr>
                        <td class="provider_td">
                            <div class="cont_left_tt">
                                <span class="must"></span>排序
                            </div>
                            <div class="cont_left_input1">
                                <input name="ordering" class="layui-input" type="number" min="0" step="1" value="<?=$kehu->ordering?>" placeholder="排序"/>
                            </div>
                            <div class="clearBoth"></div>
                        </td> 
                    </tr> 
                    
        <!--            <tr>-->
        <!--                <td class="provider_td" width="50%">-->
        <!--                    <div class="provider_left_tt">-->
        <!--                        <span class="must">*</span>背景图-->
        <!--                    </div>-->
        <!--                    <div class="cont_left_input1">-->
        <!--                        <a href="<?=$kehu->story_img?>" <? if(empty($kehu->story_img)){?>style="display:none;"<? }?> target="_blank"><img src="<?=$kehu->story_img?>" id="img_zhizhao_img" width="100"></a>-->
        <!--                        <input type="hidden" name="logo" value="<?=$kehu->story_img?>" id="img_zhizhao">-->
        <!--                        <button type="button" id="upload2" class="layui-btn">上传</button>-->
        <!--                        <span style="color:#777;margin-left:10px">建议尺寸 600*380 </span>-->
        <!--                    </div>-->
        <!--                    <div class="clearBoth"></div>-->
        <!--                </td>-->
                        
        <!--                <td class="provider_td">-->
                           
        <!--                </td>-->
                      
        <!--            </tr>-->
                    
        <!--            <tr>-->
        <!--                 <td class="provider_td">-->
        <!--                    <div class="cont_left_tt">-->
        <!--                        <span class="must">*</span>字体颜色-->
        <!--                    </div>-->
        <!--                    <div class="cont_left_input1" style="position:relative;width:120px">-->
        <!--                        <input type="text" name="theme_color" id="theme_color" autocomplete="" readonly value="<?=$kehu->theme_color?>" class="layui-input" style="display:inline-flex;"/>-->
								<!--<span id="pickcolor"></span>-->
        <!--                    </div>-->
        <!--                    <div class="clearBoth"></div>-->
        <!--                </td>-->
        <!--            </tr>-->
                    <div class="clearBoth"></div>
                   
                    <tr>
                        <td class="provider_td">
            					<div class="miaoshu_fenlei" id="pdtcontMenu">
            						<ul>
            							<li><a href="javascript:" id="pdtcontMenu1" onclick="qiehuan('pdtcont',1,'on');" class="on">内容</a></li>
            						</ul>
            					</div>
            					<div class="miaoshu_edit pdtcontCont" id="pdtcontCont1">
            						<?php
            						ewebeditor(EDITORSTYLE,'content',empty($kehu->content)?'':$kehu->content);
            						?>
            					</div>
            				
            				</div>
                            <div class="clearBoth"></div>
                        </td>
                    </tr>
                </table>
            </div>
            <div>
                <div class="relevance_affirm">
                    <button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
                    <button class="layui-btn layui-btn-primary" onclick="history.go(-1);return false;">取 消</button>
                </div>
                <div class="clearBoth"></div>
            </div>
        </form>
    </div>
    <div id="bg"></div>
    <div id="myModal" class="reveal-modal">
      <div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif" /></div>
    </div>
    <script type="text/javascript">
       var channelId = 0;
       var unit_type = 1;
      layui.use(['laydate','form','upload'], function(){
        var form = layui.form,
        upload = layui.upload,
        laydate = layui.laydate;
        upload.render({
            elem: '#upload2'
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
        
        ajaxpost = $.ajax({
    		type: "POST",
    		url: "/erp_service.php?action=get_recruit_channels1",
    		data: "pid="+channelId,
    		dataType:"text",timeout : 8000,
    		success: function(resdata){
    			$("#selectChangeChannels").append(resdata);
    			if(unit_type==1){
    				$("#danunit").next().hide();
    			}
    		},
    		error: function() {
    			layer.msg('数据请求失败', {icon: 5});
    		}
    	});
	
        
        laydate.render({
            elem: '#riqi1'
            ,show: true
            ,position: 'static'
            ,min: '2018-01-01'
            ,type:'date'
            ,format: 'yyyy-MM-dd'
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
            ,min: '2018-01-01'
            ,btns: ['confirm']
            ,type:'date'
            ,format: 'yyyy-MM-dd'
            ,done: function(value, date, endDate){
                $("#s_time2").html(value);
                $("#endTime").val(value);
            }
        });
        
        $(".laydate-btns-confirm").click(function(){
            $("#riqilan").slideUp(200);
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
        
        form.on('select(ps3)',function(data){
            if(!isNaN(data.value)){
                $("#psarea").val(data.value);
            }
        });
        // form.on('submit(tijiao)', function(data){
        //   layer.load();
        // });
    });
    
$("#selectChannel").click(function(){
	$(this).parent().toggleClass('layui-form-selected');
}); 
    
function showNextMenus(eve,dom,id){
	$(dom).toggleClass('menuLeftOn');
	$("#next_menu"+id).slideToggle(200);
	stopPropagation(eve);
}
function selectMenu(eve,dom){
	if($(dom).find('.menuLeft').length>0){
		var id = $(dom).attr("lay-value");
		showNextMenus(eve,$(dom).find('span').eq(0),id);

	}else{
		$("#channelId").val($(dom).attr("lay-value"));
		$("#selectChannel").find('input').val($(dom).html());
		$("#selectChannel").click();
	}
}


$('#theme_color').colpick({
    layout:'hex',
    submit:0,
    colorScheme:'white',
    onChange:function(hsb,hex,rgb,el,bySetColor) {
    	$('#pickcolor').css('background-color','#'+hex);
    	if(!bySetColor) $(el).val('#'+hex);
    }
    }).keyup(function(){
    	$(this).colpickSetColor('#'+this.value);
    });
			
			
$(function(){
	$('.moban-thumb').click(function(){
		layer.photos({
			area: ['600px', '100%'],
			photos: '#moban-list'
			,shift: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
		});
	})
})
</script>
<!--<script type="text/javascript" src="js/product_edit.js"></script>-->
<script type="text/javascript" src="js/yyyx/create_cuxiao.js"></script>
</body>
</html>