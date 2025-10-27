<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$sql = "select * from demo_pdt_area where comId=$comId ";
$sql.=" order by if_remen desc,orders asc";
$units = $db->get_results($sql);
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
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="jiliangdanwei">
		<div class="jiliangdanwei_up">
			<div class="jiliangdanwei_up_left">
				<img src="images/biao_35.png"/> 热门城市管理
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="shangpinguanli" style="padding-top:20px">
			<ul>
				<?
				if(!empty($units)){
					foreach ($units as $c){
						?>
						<li id="li_<?=$c->id?>">
							<div class="shangpinguanli_01">
								<div class="shangpinguanli_01_left">
									<?=$c->title?>
									<font style="margin-left:20px;color:<?=$c->if_remen==1?'green':'red'?>"><?=$c->if_remen==0?'非':''?>热门</font>
								</div>
								<div class="shangpinguanli_01_right">
									<? if($c->if_remen==0){?>
										<a href="javascript:" onclick="z_confirm('确定要将<?=$c->title?>设为热门城市吗？',setremen,<?=$c->id?>);">设为热门</a>
									<? }else{?>
										<a href="javascript:" onclick="z_confirm('确定要将<?=$c->title?>取消热门城市吗？',qxremen,<?=$c->id?>);">取消热门</a>
									<? }?>
									<!-- <a href="javascript:" onclick="z_confirm('确定要删除“<?=$c->title?>”吗？',delarea,<?=$c->id?>);"><img src="images/biao_48.png"/> 删除</a> -->
								</div>
								<div class="clearBoth"></div>
							</div>
						</li>
					<?
					}
				}?>
			</ul>
		</div>
	</div>
<script type="text/javascript">
	function delarea(id){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=pdts&a=delarea",
			data: "&id="+id,
			dataType:"json",
			timeout : 20000,
			success: function(resdata) {
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message, {icon: 5});
				}else{
					$("#li_"+id).remove();
				}
			},
			error: function() {
				layer.closeAll('loading');
				layer.msg('超时，数据请求失败', {icon: 5});
			}
		});
	}
	function setremen(id){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=pdts&a=setremen",
			data: "&id="+id,
			dataType:"json",
			timeout : 20000,
			success: function(resdata) {
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message, {icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll('loading');
				layer.msg('超时，数据请求失败', {icon: 5});
			}
		});
	}
	function qxremen(id){
		layer.load();
		ajaxpost=$.ajax({
			type: "POST",
			url: "?m=system&s=pdts&a=qxremen",
			data: "&id="+id,
			dataType:"json",
			timeout : 20000,
			success: function(resdata) {
				layer.closeAll('loading');
				if(resdata.code==0){
					layer.msg(resdata.message, {icon: 5});
				}else{
					location.reload();
				}
			},
			error: function() {
				layer.closeAll('loading');
				layer.msg('超时，数据请求失败', {icon: 5});
			}
		});
	}
</script>
</body>
</html>