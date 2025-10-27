<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];

$id = (int)$request['id'];
$pid = (int)$request['pid'];
$tags = [];
$occasionArr = [];
if(!empty($id)){
	$channel = $db->get_row("select * from demo_product_brand where comId=$comId and id = {$id}");
	if($channel->tags){
	    $tags = explode(',', $channel->tags);
	}
	
	if($channel->occasionIds){
	    $occasionArr = explode(',', $channel->occasionIds);
	}
    if($channel){
        $pid = $channel->parentId;
    }
}


$tagArr = [];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/spshezhi.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
    <!-- <link href="styles/spgl.css" rel="stylesheet" type="text/css" /> -->
	<link href="scripts/colpick.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="scripts/colpick.js"></script>	
	<style>
		/* .shangpinguanli_01_right{display: block;}
		em.layui-badge {font-style: normal;margin-right: 10px;}
		.theme_color_div{position: relative;display: inline-block;} */
        .layui-form{margin-left: 200px;}
		#pickcolor{position:absolute;right:5px;top:5px;bottom:5px;width:28px;background-color: <?=$channel?$channel->floor_theme:'#FFFFFF'?>;}
        .layui-input-block{margin-left:160px}
        .layui-form-label{color: #5b5b5b;width:130px}
        .layui-form-label>span{color: #ff5a00;margin-right: 5px;}
	</style>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				 研究领域添加/修改
			</div>
			<div class="jiliangdanwei_up_right">
				<a href="?m=system&s=product_brand">列表</a>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="shangpinguanli" style="padding-top:20px">
            <form action="?m=system&s=product_brand&a=addProductBrand&submit=1&id=<?=$id?>" class="layui-form" method="post">
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span>*</span>所属领域</label>
                        <div class="layui-input-block" style="width: 500px;">
                            <select name="parentId" lay-search id="channellist"><option value="0">顶级领域</option></select>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span>*</span>中文名称</label>
                        <div class="layui-input-block" style="width: 500px;">
                            <input type="text" class="layui-input" placeholder="领域名称" name="title" id="channel_title" value="<?=$channel?$channel->title:''?>">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span>*</span>英文名称</label>
                        <div class="layui-input-block" style="width: 500px;">
                            <input type="text" class="layui-input" placeholder="领域名称" name="en_title" id="channel_title" value="<?=$channel?$channel->en_title:''?>">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span></span>领域描述</label>
                        <div class="layui-input-block" style="width: 500px;">
                             <textarea name="miaoshu" style="width:500px;height:200px;border: medium dashed black;" class="layui-textarea" placeholder="输入领域的中文简介描述"><?=$channel->miaoshu?></textarea>
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span></span>英文描述</label>
                        <div class="layui-input-block" style="width: 500px;">
                             <textarea name="en_miaoshu" style="width:500px;height:200px;border: medium dashed lightblue;" class="layui-textarea" placeholder="输入领域的英文简介描述"><?=$channel->en_miaoshu?></textarea>
                        </div>
                    </div>
                    
                    <div class="layui-form-item" style="display:none;">
                        <div class="layui-inline">
                            <label  class="layui-form-label"><span>*</span>楼层排序</label>
                            <div class="layui-input-inline" style="width:80px;" >
                                <input type="text" class="layui-input" placeholder="楼层排序" name="floor" value="<?=$channel?$channel->floor:0?>">
                            </div>
                            <div class="layui-form-mid">数字越大越靠前</div>                           
                        </div>                        
                    </div>
                   
                    <!--<div class="layui-form-item">-->
                    <!--    <div class="layui-inline">-->
                    <!--        <label  class="layui-form-label">所属属性</label>-->
                    <!--        <div class="layui-input-block">-->
                    <!--            <input type="checkbox" name="tags[]"  lay-skin="primary" title="员工福利推荐" value="员工福利推荐" <?=(in_array('员工福利推荐',$tags))?'checked':'' ?>>-->
                    <!--            <input type="checkbox" name="tags[]" lay-skin="primary"  title="市场促销推荐" value="市场促销推荐" <?=(in_array('市场促销推荐',$tags))?'checked':'' ?>>-->
                    <!--            <input type="checkbox" name="tags[]" lay-skin="primary"  title="商务答谢推荐" value="商务答谢推荐" <?=(in_array('商务答谢推荐',$tags))?'checked':'' ?>>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <!-- <div class="layui-form-item">-->
                    <!--    <div class="layui-inline">-->
                    <!--        <label  class="layui-form-label"> 所属场景</label>-->
                    <!--        <div class="layui-input-block">-->
                    <!--            <? foreach($occasions as $occa){ ?>-->
                    <!--            <input type="checkbox" name="occasionIds[]"  lay-skin="primary" title="<?=$occa->title?>" value="<?=$occa->id?>" <?=(in_array($occa->id,$occasionArr))?'checked':'' ?>>-->
                    <!--            <? } ?>-->
                    <!--        </div>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    <div class="layui-form-item" style="display:none;">
                        <label  class="layui-form-label"><span>*</span>楼层主题颜色</label>
                        <div class="layui-input-block" style="width: 120px;position:relative">
                            <input type="text" name="floor_theme" id="theme_color" autocomplete="off" style="cursor: pointer;" readonly value="<?=$channel?$channel->floor_theme:'#FFFFFF'?>" class="layui-input"/>
                            <span id="pickcolor" id="pickcolor"></span>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span>*</span>领域图标</label>
                        <div class="layui-input-block" style="width: 120px;position:relative">
                            <img src="<?=($channel && $channel->originalPic)?$channel->originalPic:'images/add.jpg' ?>" style="cursor:pointer;height:50px;" id="channel_img"> &nbsp;<a href="javascript:" onclick="del_channel_img();">删除</a>
                        </div>
                    </div>
                    <div class="layui-form-item" style="display:none;">
                        <label  class="layui-form-label"><span>*</span>背景图</label>
                        <div class="layui-input-block" style="width: 120px;position:relative">
                            <img src="<?=($channel && $channel->backimg)?$channel->backimg:'images/add.jpg' ?>" style="cursor:pointer;height:50px;" id="banner_img"> &nbsp;<a href="javascript:" onclick="del_banner_img();">删除</a>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label  class="layui-form-label"><span>*</span>激活图标</label>
                        <div class="layui-input-block" style="width: 120px;position:relative">
                        <img src="<?=($channel && $channel->ext_originalPic)?$channel->ext_originalPic:'images/add.jpg' ?>" style="cursor:pointer;height:50px;" id="exbanner_img"> &nbsp;<a href="javascript:" onclick="del_ext_banner_img();">删除</a>
                        </div>
                    </div>
                    
                    <div class="layui-form-item" style="margin-top: 30px;">
                        <label class="layui-form"></label>
                        <div class="layui-input-block">
                            <button type="submit" class="layui-btn layui-btn-noraml">立即保存</button> 
                            <a href="?m=system&s=product_brand" class="layui-btn layui-btn-primary">返回列表</a>
                        </div>
                    </div>
				    <input type="hidden" name="originalPic" id="channel_imgurl" value="<?=$channel?$channel->originalPic:''?>">
				    <input type="hidden" name="backimg" id="banner_imgurl" value="<?=$channel?$channel->backimg:''?>">
					<input type="hidden" name="ext_originalPic" id="exbanner_imgurl" value="<?=$channel?$channel->ext_originalPic:''?>">
			</form>
		</div>
	</div>
    
	<script>
        layui.use(['form','upload'],function(){
            var form = layui.form,layupload = layui.upload;
            
            $.get("/erp_service.php?action=get_product_brands&id=<?=$id?>&pid=<?=$pid?>",function(res){
                $('#channellist').html('<option value="0">顶级领域</option>'+res);
                form.render();  
            })
            form.render();                             

            layupload.render({
                elem: '#channel_img'
                ,url: '?m=system&s=upload&a=upload&limit_width=no'
                ,before:function(){
                    layer.load();
                }
                ,done: function(res){
                    layer.closeAll('loading');
                    if(res.code > 0){
                    return layer.msg(res.msg);
                    }else{
                    $("#channel_img").attr("src",res.url);
                    $("#channel_imgurl").val(res.url);
                    }
                }
                ,error: function(){
                    layer.msg('上传失败，请重试', {icon: 5});
                }
            });
            layupload.render({
                elem: '#exbanner_img'
                ,url: '?m=system&s=upload&a=upload&limit_width=no'
                ,before:function(){
                    layer.load();
                }
                ,done: function(res){
                    layer.closeAll('loading');
                    if(res.code > 0){
                    return layer.msg(res.msg);
                    }else{
                    $("#exbanner_img").attr("src",res.url);
                    $("#exbanner_imgurl").val(res.url);
                    }
                }
                ,error: function(){
                    layer.msg('上传失败，请重试', {icon: 5});
                }
            });
            layupload.render({
                elem: '#banner_img'
                ,url: '?m=system&s=upload&a=upload&limit_width=no'
                ,before:function(){
                    layer.load();
                }
                ,done: function(res){
                    layer.closeAll('loading');
                    if(res.code > 0){
                    return layer.msg(res.msg);
                    }else{
                    $("#banner_img").attr("src",res.url);
                    $("#banner_imgurl").val(res.url);
                    }
                }
                ,error: function(){
                    layer.msg('上传失败，请重试', {icon: 5});
                }
            });
            // form.render();
        })
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
            function del_channel_img(){
                $("#channel_img").attr("src",'images/add.jpg');
                $("#channel_imgurl").val('');
            }
            function del_ext_banner_img(){
                $("#exbanner_img").attr("src",'images/add.jpg');
                $("#exbanner_imgurl").val('');
            }
            function del_banner_img(){
                $("#banner_img").attr("src",'images/add.jpg');
                $("#banner_imgurl").val('');
            }
	</script>
</body>
</html>