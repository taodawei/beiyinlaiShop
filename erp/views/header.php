<?

global $adminRole,$db,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$if_yibao = $db->get_var("select if_yibao from demo_shezhi where comId=$comId");
$userid=$_SESSION[TB_PREFIX.'admin_userID'];
$roles=$db->get_var("select a.roles from roles as a,roles_group as b where a.id=b.rolesId and b.userId=$userId");
$lists1=$db->get_results("select * from quanxian where topid=0 and isshow=1 and type=0 and id in($roles) order by sort desc, id asc");
$url="";
foreach($lists1 as $k=>&$v1){
    $v1->chlid=$db->get_results("select * from quanxian where topid=".$v1->id." and isshow=1 and type=0 and id in($roles) order by sort desc, id asc");
    if($k==0){
        $url=$v1->chlid[0]->url;
    }
    foreach($v1->chlid as $s=>&$sub1){
        $sub1->chlid=$db->get_results("select * from quanxian where topid=".$sub1->id." and isshow=1 and type=0 and id in($roles) order by sort desc, id asc");
    }
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link rel="shortcut icon" href="images/favicon.ico" />
	<link href="styles/common.css" rel="stylesheet" type="text/css" />
	<link href="styles/index.css" rel="stylesheet" type="text/css" />
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript">
		var max_msg_id = 0;
		var new_msg_num = 0;
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
			$(".left_up_02 > ul > li > a").click(function(){
				var nowA = $(".left_up_02 a.left_up_02_on");
				if(nowA.length>0){
					nowA.removeClass("left_up_02_on");
					imgR = nowA.attr("data-img").toString().split('|');
					nowA.find("img").eq(0).attr("src","images/biao_"+imgR[0]+".png");
				}
				$(this).addClass("left_up_02_on");
				imgN = $(this).attr("data-img").toString().split('|');
				$(this).find("img").eq(0).attr("src","images/biao_"+imgN[1]+".png");
				if($(this).attr("data-href")!=""){
					$("#mainFrame").attr("src",$(this).attr("data-href"));
				}
				nowIndex = $(this).parent().index();
				$(this).next().slideToggle(200);
				$(".left_up_02 ul li ul").each(function(){
					if($(this).parent().index()!=nowIndex){
						$(this).stop().slideUp(200);
					}
				});
			});
			$(".left_up_02 ul li ul li a").click(function(){
				$(".left_up_02 ul li ul li a.on").removeClass("on");
				$(this).addClass("on");
				if($(this).attr("data-href")!=""){
					$("#mainFrame").attr("src",$(this).attr("data-href"));
				}
			});
			$(".header_right1").hover(function(){
				$("#topshezhi").slideDown(100);
			},function(){
				$("#topshezhi").slideUp(100);
			});
			$(".topshezhi_up").click(function(){
				$(this).toggleClass('on');
				$(this).next().stop().slideToggle(200);
			});
			setInterval(function(){
				$.ajax({
					type: "POST",
					url: "?m=system&s=users&a=getyewuMsgNum",
					data: "erpMaxId="+max_msg_id,
					dataType:'json',timeout : 8000,
					success: function(resdata){
						max_msg_id = resdata.max_msg_id;
						new_msg_num += resdata.new_msg_num;
						if(resdata.new_msg_num>0){
							$("#audio")[0].play();
						}
						if(new_msg_num>0){
							$("#yewuMsg").text(new_msg_num).show();
						}						
					}
				});
			},10000);
		});
	</script>
	<style type="text/css">
	.topshezhi{width:198px;max-height:439px;position:absolute;z-index:999;top:44px;right:0px;background-color:#fff;box-shadow:0 0 5px #e7e7e7;overflow-y:auto;overflow-x:hidden;padding:15px 0;display:none;}
	.topshezhi ul li{padding-left:20px}
	.topshezhi_up{height:30px;line-height:30px;font-size:14px;color:#6d6d6d;cursor:pointer}
	.topshezhi_up img{vertical-align:middle;margin:0 5px}
	.topshezhi_up .topshezhi_up_biao{width:9px;margin-left: 10px;}
	.topshezhi_down{padding-left:40px;line-height:35px}
	.topshezhi_down a{font-size:13px;color:#848484}
	.header_right1,.topshezhi_up img{transition:.2s;}
	.header_right1:hover{background:#145F83;}
	.topshezhi_up.on .topshezhi_up_biao{-webkit-transform: rotate(180deg);transform: rotate(180deg);}
	.top_help{position:relative;cursor:pointer;}
	.top_help span{width:18px;height:18px;display:inline-block;background-color:#ff1200;border-radius:100%;text-align:center;line-height:18px;font-size:13px;color:#fff;position:absolute;z-index:99;top:15px;left:35px;display:none;}
	.layui-nav .layui-this:after{background:none;}
	.left_dhgl_02{height:0;overflow:hidden;padding:3px 0;
	    webkit-transition:all .35s;
        -moz-transition:all .35s;
        -ms-transition:all .35s;
        -o-transition:all .35s;
        transition:all .35s;
	}
	.left_dhgl_02.on{height:auto;}
	</style>
	<script>
	    $(function(){
	        
	        $(".left_up_02 ul li .left_dhgl_01").click(function(){
	            var index=$(this).data("index");
	            $(this).parent().find(".left_dhgl_02").removeClass("on");
	            $(this).parent().find(".left_dhgl_02").attr("style","");
	            var len=$(this).parent().find(".left_dhgl_02").eq(index).find("a").length;
	            $(this).parent().find(".left_dhgl_02").eq(index).css("height",len*40+'px');
	            $(this).parent().find(".left_dhgl_02").eq(index).addClass("on");
	        })
	    })
	</script>
</head>
<body style="min-width:1423px;position:relative;">
	<div id="header">
		<div class="logo">
			<span style="color:#fff;font-size:25px;line-height:40px;">后台管理系统</span>
		</div>
		<div class="header_right layui-nav" style="background:none;padding-left:0px">
			<li class="layui-nav-item">
	   			<a href="javascript:;"><?=$_SESSION[TB_PREFIX.'name']?></a>
				<dl class="layui-nav-child">
					<dd><a href="login.php?action=logout">退出登录</a></dd>
				</dl>
			</li>
		</div>
		<div class="header_right layui-nav" style="background:none;padding:0px">
		    <li class="layui-nav-item top_help" onclick="new_msg_num=0;$('#yewuMsg').hide();$('#mainFrame').attr('src','?m=system&s=msg');"><a href="javascript:" _href="?m=system&s=msg"><img src="images/bangzhu_1.png"></a><span id="yewuMsg">0</span></li>
		</div>
	        <div class="clearBoth"></div>
	    </div>
		<div class="clearBoth"></div>
	</div>
	<div id="left">
		<div class="left_up">
			<div class="left_up_01">
				我的工作台
			</div>
			<div class="left_up_02">
				<ul>
					<?
					    foreach($lists1 as $o=> $lists2){
					?>
					<li>
						<a href="javascript:" <? if($lists2->url!=""){ ?>data-href="<?=$lists2->url?>"<? } ?> data-img="<?=$lists2->imgon?>" <? if($o==0){ ?> class="left_up_02_on"<? } ?>><img src="<?=$lists2->imgurl?>"/> <?=$lists2->name?></a>
						<ul <? if($o==0){ ?> style="display:block;"<? } ?>>
							<li>
							    <?
							        foreach($lists2->chlid as $t=> $lists3){
							    ?>
							    <?
							        if(count($lists3->chlid)>0){
							    ?>
							        
        								<div class="left_dhgl_01" <? if($t==0){ ?>on<? } ?>" data-index="<?=$t?>" <? if($lists3->url!=""){ ?>style="cursor:pointer;" onclick="$('.left_up_02 ul li ul li a.on').removeClass('on');$('#mainFrame').attr('src','<?=$lists3->url?>');"<? } ?>>
        									<?=$lists3->name?>
        								</div>
        								<div class="left_dhgl_02 <? if($t==0){ ?>on<? } ?>">
        								    <?
            							        foreach($lists3->chlid as $lists4){
            							    ?>
            							        
        									    <a href="javascript:" data-href="<?=$lists4->url?>"><?=$lists4->name?></a>
        									<?
            							        }
        									?>
        								</div>
								<?
            					    }else{
            					?>
            					       
            					            <a href="javascript:" data-href="<?=$lists3->url?>"><?=$lists3->name?></a>
            					<?
            					    }
            					?>
								<?
            					    }
            					?>
							</li>
						</ul>
					</li>
					<?
					    }
					?>
				</ul>
			</div>
		</div>
	</div>
	<div id="right">
		<? 
			$right = $url;
			if(!empty($request['url'])){
				$right = urldecode($request['url']);
			}
		?>
		<iframe border="0" id="mainFrame" name="mainFrame" src="<?=$right?>" frameborder="0" height="100%" width="100%"></iframe>
	</div>
	<div class="bj1"></div>
	<audio id="audio" src="/erp/images/8858.mp3" preload="auto" style="display:none;"></audio>
</body>
</html>