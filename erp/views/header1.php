<? 
global $qx_arry,$adminRole,$db;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$right = 'index.php?m=system&s=product_channel';
if(!empty($request['url'])){
	$right = urldecode($request['url']);
}
$baseArr = array('?m=system&s=product_channel','?m=system&s=product_brand','?m=system&s=product_unit','?m=system&s=quanxian','?m=system&s=store');
$canshuArr = array('?m=system&s=product_set','?m=system&s=kucun_set','?m=system&s=kucun_set&a=churuku');
$dinghuoArr = array('?m=system&s=dinghuo_set','?m=system&s=dinghuo_set&a=shoukuan','?m=system&s=dinghuo_set&a=level');
$mendianArr = array('?m=system&s=mendian_set','?m=system&s=mendian_set&a=type','?m=system&s=mendian_set&a=addrows','?m=system&s=mendian_set&a=level','?m=system&s=mendian_set&a=jifen','?m=system&s=mendian_set&a=yue');
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css" />
	<link href="styles/index.css" rel="stylesheet" type="text/css" />
	<link href="styles/duanxin.css" rel="stylesheet" type="text/css" />
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript">
		window.onload = function() {
			if (typeof(Worker) !== "undefined") {
			}else {
				alert("系统检测到您的浏览器版本过低，请使用“360/QQ等浏览器的急速模式”或“Chrome浏览器”");
			}
		}
		document.onkeydown=function(event){
			var e = event || window.event || arguments.callee.caller.arguments[0];
			var src = document.getElementById("mainFrame").src;
			if(e && e.keyCode==116){
				event.preventDefault();
				$("#mainFrame").attr("src",src);
				return false;
			}
		};
		layui.use(['element'], function(){
			var element = layui.element;
		});
		$(function(){
			$(".left_down_02 > ul > li > a").click(function(){
				nowIndex = $(this).parent().index();
				var nowA = $(".left_down_02 a.on");
				if(nowA.parent().index()!=nowIndex){
					nowA.removeClass("on");
				}
				$(this).toggleClass("on");
				if($(this).attr("data-href")!=""){
					$("#mainFrame").attr("src",$(this).attr("data-href"));
				}
				
				$(this).next().slideToggle(200);
				$(".left_down_02 ul li ul").each(function(){
					if($(this).parent().index()!=nowIndex){
						$(this).stop().slideUp(200);
					}
				});
			});
			$(".left_down_02 ul li ul li a").click(function(){
				$(".left_down_02 ul li ul li a.on").removeClass("on");
				$(this).addClass("on");
				if($(this).attr("data-href")!=""){
					$("#mainFrame").attr("src",$(this).attr("data-href"));
				}
			});
		});
	</script>
</head>
<body style="min-width:1423px;position:relative;">
	<div id="header">
		<div class="logo">
			<span style="color:#fff;font-size:25px;line-height:40px;">后台管理系统</span>
		</div>
		<div class="header_right layui-nav" style="background:none;">
			<li class="layui-nav-item"><a href="index.php">< 返回库存主页</a></li>
		</div>
		<div class="clearBoth"></div>
	</div>
	<div id="left">
		<div class="left_up">
			<span>系统设置</span>
		</div>
		<div class="left_down">
			<div class="left_down_02">
				<ul>
					<? if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'product_channel')||strstr($qx_arry['shezhi']['functions'],'product_brand')||strstr($qx_arry['shezhi']['functions'],'product_unit')||strstr($qx_arry['shezhi']['functions'],'quanxian')||strstr($qx_arry['shezhi']['functions'],'store')){?>
					<li>
						<a href="javascript:" <? if(in_array($right,$baseArr)){?>class="on"<? }?>><img src="images/duanxin_11.png"/> 基础设置 <span class="left_down_1"></span></a>
						<ul <? if(!in_array($right,$baseArr)){?>style="display:none"<? }?>>
					
							<?
	                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'product_unit')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=product_unit">计量单位</a>
							</li>
							<? }
	                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'quanxian')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=adminlist">角色权限设置</a>
							</li>
							<? }
	                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'store')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=store">仓库/电子面单</a>
							</li>
							<? }
							if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'shoukuan')){
								$if_yibao = $db->get_var("select if_yibao from demo_shezhi where comId=$comId");
								if($_SESSION['if_tongbu']==1 || $if_yibao==1){
									?>
									<li>
										<a href="javascript:" data-href="?m=system&s=dinghuo_set&a=yibao">支付设置</a>
									</li>
									<?
								}else{
									?>
									<li>
										<a href="javascript:" data-href="?m=system&s=dinghuo_set&a=shoukuan">收款帐户设置</a>
									</li>
									<?
								}
							}
							?>
						</ul>
					</li>
					 <? }
					 if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'product_set')||strstr($qx_arry['shezhi']['functions'],'kucun_set')||strstr($qx_arry['shezhi']['functions'],'churuku')){
					 ?>
					<li>
						<a href="javascript:" <? if(in_array($right,$canshuArr)){?>class="on"<? }?>><img src="images/duanxin_12.png"/> 系统参数设置 <span class="left_down_1"></span></a>
						<ul <? if(!in_array($right,$canshuArr)){?>style="display:none"<? }?>>
							<? if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'product_set')){?>
							<li>
								<a href="javascript:" data-href="?m=system&s=product_set">商品设置</a>
							</li>
							<? }
	                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'kucun_set')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=kucun_set">库存设置</a>
							</li>
							<? }
	                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'churuku')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=kucun_set&a=churuku">出入库设置</a>
							</li>
							<? }?>
						</ul>
					</li>
					<? }
					if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'dinghuo_set')||strstr($qx_arry['shezhi']['functions'],'level')||strstr($qx_arry['shezhi']['functions'],'shoukuan')){
					?>
					<li>
						<a href="javascript:" <? if(in_array($right,$dinghuoArr)){?>class="on"<? }?>><img src="images/duanxin_19.png"/>  订货管理设置 <span class="left_down_1"></span></a>
						<ul <? if(!in_array($right,$dinghuoArr)){?>style="display:none"<? }?>>
							<? if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'dinghuo_set')){?>
							<li>
								<a href="javascript:" data-href="?m=system&s=dinghuo_set"><?=$kehu_title?>设置</a>
							</li>
							<? }
	                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'level')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=dinghuo_set&a=level"><?=$kehu_title?>级别</a>
							</li>
							<? }
	                        ?>
						</ul>
					</li>
					<? }
					if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set')){
					?>
					<li>
						<a href="javascript:" <? if(in_array($right,$mendianArr)){?>class="on"<? }?>><img src="images/duanxin_19.png"/>  零售设置 <span class="left_down_1"></span></a>
						<ul <? if(!in_array($right,$mendianArr)){?>style="display:none"<? }?>>
							<? if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set_index')){?>
							<li>
								<a href="javascript:" data-href="?m=system&s=mendian_set">基础设置</a>
							</li>
							<li>
								<a href="javascript:" data-href="?m=system&s=mendian_set&a=index1">店铺设置</a>
							</li>
							<!-- <li>
								<a href="javascript:" data-href="?m=system&s=mendian_set&a=dayin">打印设置</a>
							</li> -->
							<? }
							if($_SESSION['if_tongbu']!=1){
		                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set_type')){
		                        ?>
								<li>
									<!--<a href="javascript:" data-href="?m=system&s=mendian_set&a=type" onclick="return false;" style="color:#aaa;">门店类型</a>-->
								</li>
								<? }
		                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set_addrows')){
		                        ?>
								<li>
									<!--<a href="javascript:" data-href="?m=system&s=mendian_set&a=addrows">会员字段设置</a>-->
								</li>
								<? }
		                        if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set_level')){
		                        ?>
								<li>
									<a href="javascript:" data-href="?m=system&s=mendian_set&a=level">会员等级</a>
								</li>
								<? }
							}
							if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set_jifen')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=mendian_set&a=jifen">积分规则</a>
							</li>
							<? }
							if($adminRole>=7||strstr($qx_arry['shezhi']['functions'],'mendian_set_yue')){
	                        ?>
							<li>
								<a href="javascript:" data-href="?m=system&s=mendian_set&a=yue">余额设置</a>
							</li>
							<? }
							?>
						</ul>
					</li>
					<? }?>
				</ul>
			</div>
		</div>
	</div>
	<div id="right">
		<iframe border="0" id="mainFrame" name="mainFrame" src="<?=$right?>" frameborder="0" height="100%" width="100%"></iframe>
	</div>
	<div class="bj1"></div>
</body>
</html>