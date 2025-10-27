<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$shequs = $db->get_results("select id,title,name,phone from demo_shequ where comId=$comId and status=1");
$pdts = $db->get_results("select id,title,key_vals from zhuisu_pdt where comId=$comId and status=1");
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
	<link href="styles/yingxiaoguanli.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.form.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="/keditor/kindeditor1.js"></script>
	<style type="text/css">html,body,form,.content_edit{height:100%}</style>
</head>
<body>
	<form action="?m=system&s=zhuisu&a=create&tijiao=1" method="post" id="createPdtForm" class="layui-form" enctype="multipart/form-data">
		<input type="hidden" name="url" value="<?=$url?>">
		<div class="content_edit">
			<div class="edit_h">
				<a href="<?=urldecode($request['url'])?>"><img src="images/back.jpg" /></a>
				<span>生成追溯码</span>
			</div>
			<div class="edit_jichu">
				<div class="jichu_h">基础信息</div>
				<div class="clearBoth"></div>
				<div class="jichu_message">
					<ul>
						<li style="width:100%">
							<div class="gaojisousuo_left">
								<span>*</span>选择商品 
							</div>
							<div class="gaojisousuo_right">
								<select name="pdtId" lay-verify="required" lay-search>
									<option value="">选择或搜索商品</option>
									<? if(!empty($pdts)){
										foreach ($pdts as $p) {
											?>
											<option value="<?=$p->id?>"><?=$p->title?>（<?=$p->key_vals?>）</option>
											<?
										}
									}?>
								</select>
							</div>
						</li>
						<li style="width:100%">
							<div class="gaojisousuo_left">
								<span>*</span>选择经销商 
							</div>
							<div class="gaojisousuo_right">
								<select name="shequId" lay-verify="required" lay-search>
									<option value="">选择或搜索经销商</option>
									<? if(!empty($shequs)){
										foreach ($shequs as $s) {
											?>
											<option value="<?=$s->id?>"><?=$s->title?>（<?=$s->name.'-'.$s->phone?>）</option>
											<?
										}
									}?>
								</select>
							</div>
						</li>
						<div class="clearBoth"></div>
					</ul>
				</div>
			</div>
		<div class="edit_save">
			<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
			<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
		</div>
	</div>
</div>
</form>
<script type="text/javascript">
  layui.use(['form'], function(){
    form = layui.form;
    form.on('submit(tijiao)', function(data){
        layer.load();
    });
});
</script>
</body>
</html>