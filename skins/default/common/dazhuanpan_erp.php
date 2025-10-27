<?php
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$dazhuanpan_id = $id = (int)$request['id'];
$userId = (int)$_SESSION[TB_PREFIX.'user_ID'];
$dazhuanpan = $db->get_row("select * from demo_dazhuanpan where id=$dazhuanpan_id and comId=$comId");
if(empty($dazhuanpan)){
	die("活动不存在");
}
//是否可以抽奖
$if_kechou = 1;
$startTime = strtotime($dazhuanpan->startTime);
$endTime = strtotime($dazhuanpan->endTime);
$now = time();
if($now<$startTime){
	$if_kechou = 0;
	$reason = '活动尚未开始';
}else if($now>$endTime){
	$if_kechou = 0;
	$reason = '活动尚已结束';
}
//可抽奖次数
$has_nums = (int)$db->get_var("select nums from demo_dazhuanpan_jilu where dazhuanpan_id=$dazhuanpan_id and user_id=$userId".($dazhuanpan->per_type==2?" and dtTime='".date("Y-m-d")."'":''));
$last_num = $dazhuanpan->per_num - $has_nums;
if($last_num<1){
	$if_kechou = 0;
	$reason = '您已用完抽奖次数';
}
//积分是否够用
if($dazhuanpan->per_jifen>0){
	if($comId==10){
		$db_service = getCrmDb();
		$jifen = $db_service->get_var("select jifen from demo_user where id=$userId");
	}else{
		$jifen = $db->get_var("select jifen from users where id=$userId");
	}
	if($jifen<$dazhuanpan->per_jifen){
		$if_kechou = 0;
		$reason = '积分不足';
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimum-scale=1.0, maximum-scale=1.0,maximum-scale=1, user-scalable=no">
<title><?=$dazhuanpan->title?></title>
<!-- <script src="/skins/default/dazhuanpan_js/jquery-1.8.2.min.js"></script>
<script src="/skins/default/dazhuanpan_js/jquery.mobile-1.2.0.min.js"></script>
<script src="/skins/default/dazhuanpan_js/mobile.js"></script> -->
<link href="/skins/default/dazhuanpan_css/style.css" rel="stylesheet" type="text/css">
<link rel="stylesheet" type="text/css" href="/skins/resource/layui/css/layui.mobile.css">
<script type="text/javascript" src="/skins/default/dazhuanpan_js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/skins/default/dazhuanpan_js/awardRotate.js"></script>
<script type="text/javascript" src="/skins/resource/scripts/layer.js"></script>


<script type="text/javascript">
	var if_click = 1;
var turnplate={
		restaraunts:[],				//大转盘奖品名称
		prizeids:[],			    //大转盘奖品id
		prizeitems:[],			    //大转盘奖品的位置
		colors:[],					//大转盘奖品区块对应背景颜色
		outsideRadius:192,			//大转盘外圆的半径
		textRadius:155,				//大转盘奖品位置距离圆心的距离
		insideRadius:68,			//大转盘内圆的半径
		startAngle:0,				//开始角度
		
		bRotate:false				//false:停止;ture:旋转
};

$(document).ready(function(){
	//动态添加大转盘的奖品与奖品区域背景颜色
	<?php 
	    //获取最新的奖项
	    $prize=$db->get_results("select * from demo_dazhuanpan_prize where dazhuanpan_id=$dazhuanpan_id and status=1 order by ordering desc limit 10");
	    if(!empty($prize)){
	    	$counts=count($prize);
//	    	$shangyu=10-$counts;
//	    	$prizestr='';
//	    	$prizeids='';
//	    	$prizeitems='';
	    	$item=0;
	    	if($counts!=10){//如果不满10个奖项则有谢谢参与奖项，否则获取数据库里所有奖项
	    		$prizestr='"谢谢参与",';
				$prizeids='"0",';
				$prizeitems='"1",';
				$item++;
	    	}else{
	    		$prizestr='';
				$prizeids='';
				$prizeitems='';
	    	}
			$colors='';
	    	foreach ($prize as $p){
	    		$item++;
	    		$prizestr .='"'.$p->name.'",';
	    		$prizeids .='"'.$p->id.'",';
	    		$prizeitems .='"'.$item.'",';
	    	}
	    	//颜色
	    	for($i=1;$i<=$counts+1;$i++){
	    		if($i%2>0){
	    			$colors .='"#FFF4D6",';
	    		}else{
	    			$colors .='"#FFFFFF",';
	    		}
	    	}
	    	$prizestr = substr($prizestr,0,strlen($prizestr)-1);
	    	$prizeids = substr($prizeids,0,strlen($prizeids)-1);
	    	$prizeitems = substr($prizeitems,0,strlen($prizeitems)-1);
	    	$colors = substr($colors,0,strlen($colors)-1);
	    }
	?>
	
	turnplate.restaraunts = [<?php echo $prizestr;?>];
	turnplate.prizeids = [<?php echo $prizeids;?>];
	turnplate.prizeitems = [<?php echo $prizeitems;?>];
	turnplate.colors = [<?php echo $colors;?>];

	
	var rotateTimeOut = function (){
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:2160,
			duration:8000,
			callback:function (){
				alert('网络超时，请检查您的网络设置！');
			}
		});
	};

	//旋转转盘 item:奖品位置; txt：提示语;
	var rotateFn = function (item,txt,id){
		$.ajax({
			type:"POST",
			url:"/?p=23&a=jilu&dazhuanpan_id=<?=$dazhuanpan_id?>",
			data:"id="+id,
			timeout:"40000",
			dataType:"json",                                 
			success: function(html){}
		});
		var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
		if(angles<270){
			angles = 270 - angles; 
		}else{
			angles = 360 - angles + 270;
		}
		$('#wheelcanvas').stopRotate();
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:angles+1800,
			duration:8000,
			callback:function (){
				if(txt=='谢谢参与'){
					txts='谢谢参与！';
				}else{
					txts='恭喜您中奖'+txt+',请填写您的个人信息<br><input id="e_name" placeholder="输入姓名" style="width:80%;height:30px;margin:5px;padding-left: 5px;" /><br><input id="e_tel" placeholder="联系电话" style="width:80%;height:30px;margin:5px;padding-left: 5px;" />';
				}
				layer.open({
				    content: txts
				    ,btn: '确定'
				    ,shadeClose:false
				    ,yes: function(elem){
				    	if(txts!='谢谢参与！'){
				    		txts = '谢谢参与！';
			    		  var name = $("#e_name").val();
					      var tel = $("#e_tel").val();
					      if(tel.length!=11){
					      	alert('请输入正确的手机号');
					      	return false;
					      }
					      $.ajax({
								type:"POST",
								url:"/?p=23&a=win&dazhuanpan_id=<?=$dazhuanpan_id?>",
								data:"id="+id+"&name="+name+"&phone="+tel,
								timeout:"40000",
								dataType:"json",
								success: function(html){
									location.reload();
								}
						 });
				    	}else{
						  setTimeout(function(){location.reload();},1500);
						}
					}
				  });
				//alert(txt);
				//调用中奖方法
				turnplate.bRotate = !turnplate.bRotate;
			}
		});
	};

	$('.pointer').click(function (){
		if(if_click==0){
			return false;
		}
		if_click = 0;
		<?
		if($if_kechou==0){
			?>
			layer.open({content:'<?=$reason?>',skin: 'msg',time: 2});
			return false;
			<?
		}
		?>
		if(turnplate.bRotate)return;
		turnplate.bRotate = !turnplate.bRotate;
		//获取随机数(奖品个数范围内)
		$.ajax({
			type:"POST",
			url:"/?p=23&a=get_rnd&dazhuanpan_id=<?=$dazhuanpan_id?>",
			data:"",
			timeout:"40000",
			dataType:"json",                                 
			success: function(res){
				var item = res.id;
				rotateFn(item, turnplate.restaraunts[item-1],turnplate.prizeids[item-1]);
			}
		});
	});
	//积分不足
	$('.pointers').click(function (){
		alert("积分不足");
	});
});

function rnd(n, m){
	
	
//	var random = Math.floor(Math.random()*(m-n+1)+n);
//	return random;
}


//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
window.onload=function(){
	drawRouletteWheel();
};

function drawRouletteWheel() {    
  var canvas = document.getElementById("wheelcanvas");    
  if (canvas.getContext) {
	  //根据奖品个数计算圆周角度
	  var arc = Math.PI / (turnplate.restaraunts.length/2);
	  var ctx = canvas.getContext("2d");
	  //在给定矩形内清空一个矩形
	  ctx.clearRect(0,0,422,422);
	  //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
	  ctx.strokeStyle = "#FFBE04";
	  //font 属性设置或返回画布上文本内容的当前字体属性
	  ctx.font = '16px Microsoft YaHei';      
	  for(var i = 0; i < turnplate.restaraunts.length; i++) {       
		  var angle = turnplate.startAngle + i * arc;
		  ctx.fillStyle = turnplate.colors[i];
		  ctx.beginPath();
		  //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
		  ctx.arc(211, 211, turnplate.outsideRadius, angle, angle + arc, false);    
		  ctx.arc(211, 211, turnplate.insideRadius, angle + arc, angle, true);
		  ctx.stroke();  
		  ctx.fill();
		  //锁画布(为了保存之前的画布状态)
		  ctx.save();   
		  
		  //----绘制奖品开始----
		  ctx.fillStyle = "#E5302F";
		  var text = turnplate.restaraunts[i];
		  var line_height = 17;
		  //translate方法重新映射画布上的 (0,0) 位置
		  ctx.translate(211 + Math.cos(angle + arc / 2) * turnplate.textRadius, 211 + Math.sin(angle + arc / 2) * turnplate.textRadius);
		  
		  //rotate方法旋转当前的绘图
		  ctx.rotate(angle + arc / 2 + Math.PI / 2);
		  
		  /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
		  if(text.indexOf("M")>0){//流量包
			  var texts = text.split("M");
			  for(var j = 0; j<texts.length; j++){
				  ctx.font = j == 0?'bold 20px Microsoft YaHei':'16px Microsoft YaHei';
				  if(j == 0){
					  ctx.fillText(texts[j]+"M", -ctx.measureText(texts[j]+"M").width / 2, j * line_height);
				  }else{
					  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
				  }
			  }
		  }else if(text.indexOf("M") == -1 && text.length>6){//奖品名称长度超过一定范围 
			  text = text.substring(0,6)+"||"+text.substring(6);
			  var texts = text.split("||");
			  for(var j = 0; j<texts.length; j++){
				  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
			  }
		  }else{
			  //在画布上绘制填色的文本。文本的默认颜色是黑色
			  //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
			  ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
		  }
		  
		  //添加对应图标
		  if(text.indexOf("闪币")>0){
			  var img= document.getElementById("shan-img");
			  img.onload=function(){  
				  ctx.drawImage(img,-15,10);      
			  }; 
			  ctx.drawImage(img,-15,10);  
		  }else if(text.indexOf("谢谢参与")>=0){
			  var img= document.getElementById("sorry-img");
			  img.onload=function(){  
				  ctx.drawImage(img,-15,10);      
			  };  
			  ctx.drawImage(img,-15,10);  
		  }
		  //把当前画布返回（调整）到上一个save()状态之前 
		  ctx.restore();
		  //----绘制奖品结束----
	  }     
  } 
}
</script>
</head>
	<body>
		<div style="text-align:center;margin:20px 0; font:normal 14px/24px 'MicroSoft YaHei';">
			<img src="/skins/default/dazhuanpan_images/dazhuanpan_1.png" width="100%"/>
		</div>
		<br>
		<!-- 代码 开始 -->
		<img src="/skins/default/dazhuanpan_images/1.png" id="shan-img" style="display:none;" />
		<img src="/skins/default/dazhuanpan_images/2.png" id="sorry-img" style="display:none;" />
		<div class="banner">
			<div class="turnplate" style="background-image:url(/skins/default/dazhuanpan_images/turnplate-bg.png);background-size:100% 100%;">
				 <canvas class="item" id="wheelcanvas" width="422px" height="422px"></canvas>	
				 <?php 
				 	if($user->jifen<$dazhuanpan->use_points){
				 		?>
				 			 <img class="pointers" src="/skins/default/dazhuanpan_images/turnplate-pointer.png"/>
				 		<?
				 	}else{
				 		?>
				 			 <img class="pointer" src="/skins/default/dazhuanpan_images/turnplate-pointer.png"/>
				 		<?
				 	}
				 ?>		
			</div>
		</div>
		<!-- 代码 结束 -->
		<div style="text-align:center;margin:20px 0; font:normal 14px/24px 'MicroSoft YaHei';">
			<? if($dazhuanpan->per_jifen>0){?>
				<div class="dzp_1">
			    	<div class="dzp_1_left">
			        	我的积分：<font id="myjifen"><?php echo $jifen;?></font>
			        </div>
			        <div class="dzp_1_right">
			        	<?php echo $dazhuanpan->per_jifen; ?>积分/每次
			        </div>
			        <div style="clear:both;"></div>
			    </div>
			<? }?>
			    <div class="dzp_2">
			    	<div class="dzp_2_up">
			        	活动说明
			        </div>
			        <div class="dzp_2_down">
			        	<?='每人'.($dazhuanpan->per_type==2?'每天':'').'可抽奖'.$dazhuanpan->per_num.'次';?><br>
			        	<?php echo $dazhuanpan->content; ?>
			        </div>
			    </div>
			    <div class="dzp_3" style="width:90%">
			    	<div class="dzp_2_up">
			        	我的奖品
			        </div>
			        <div class="dzp_2_down" style="color:#fff;padding: 10px 0px;">
			        	<? $records = $db->get_results("select * from demo_dazhuanpan_record where dazhuanpan_id=$dazhuanpan_id and user_id=$userId order by id desc");
			        	//echo "select * from demo_dazhuanpan_record where dazhuanpan_id=$dazhuanpan_id and user_id=$userId order by id desc";
			        	if(!empty($records)){?>
							<table border="1" width="100%" style="border-color:#fff;text-align:center;">
								<tr><th>奖品</th><th>中奖时间</th><th>领取状态</th></tr>
								<? foreach($records as $record){
									?><tr><td><?=$record->prizeName?></td><td><?=$record->dtTime?></td><td><?=$record->isduihuan==1?'已领':'未领'?></td></tr><?
								}?>
							</table>
			        	<?	}else{
			        		echo '暂无中奖信息';
			        	}
			        	?>
			        </div>
			    </div>
		</div>
		</body>
</html>