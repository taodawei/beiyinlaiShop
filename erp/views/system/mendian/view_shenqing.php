<?
global $db,$request;
$id = (int)$request['id'];
$shenqing = $db->get_row("select * from demo_mendian_shenqing where id=$id");
if(empty($shenqing)){
	echo '<script>alert("记录不存在");history.go(-1);</script>';
}
$tuijianren = '无';
if(!empty($shenqing->tuijianren)){
	$tuijianren = $db->get_var("select nickname from users where id=$shenqing->tuijianren");
}
$statusInfo = '';
switch ($j->status) {
	case 0:
	$statusInfo = '<font color="red">待审核</font>';
	break;
	case 1:
	$statusInfo = '<font color="green">已审核</font>';
	break;
	case -1:
	$statusInfo = '<font>未通过</font>';
	break;
}
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title>申请明细</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/mendian.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="root">
		<div class="return">
			<div><a href="<?=urldecode($request['returnurl'])?>"><img src="images/back.gif"/></a></div>
			<div>申请详情</div>
		</div>
		<div class="clearBoth"></div>
		<div class="cont">
			<div class="shenqingren">
				<ul>
					<li>联系人：<span><?=$shenqing->name?> <?=$shenqing->phone?></span>  </li>
					<li>推荐人：<?=$tuijianren?> </li>
					<li>申请时间：<?=date("Y-m-d H:i",strtotime($shenqing->dtTime))?></li>
					<li>审核状态：<?=$statusInfo?></li>
				</ul>
				<div class="clearBoth"></div>
			</div> 
			<div class="shenqingcont1">
				<ul>
					<li>
						商家名称：<span><?=$shenqing->title?></span>
					</li>
					<li>
						申请类别：<span><?=$shenqing->type?></span>
					</li>
					<li>
						经营品类：<span><?=$shenqing->product_type?></span>
					</li>
					<li>
						入驻其他电商平台地址：<span><?=$shenqing->other_url?></span>
					</li>
				</ul>
			</div>
			<div class="clearBoth"></div>
			<div class="shenqingcont2">
				<ul>					
					<li>
						<div class="shenqingcont2_b">申请说明：</div>
						<div class="shenqingcont2_tt">
							<?=$shenqing->beizhu?>
						</div>
						<div class="clearBoth"></div>
					</li>
					<li>
						<div class="shenqingcont2_b">营业执照:</div>
						<div class="shenqingcont2_tt"><? if(!empty($shenqing->img_zhizhao)){?><a href="<?=$shenqing->img_zhizhao?>" target="_blank"><img src="<?=$shenqing->img_zhizhao?>" width="500px"></a><? }?><? if(!empty($shenqing->img_shenfenzheng)){?><a href="<?=$shenqing->img_shenfenzheng?>" target="_blank"><img src="<?=$shenqing->img_shenfenzheng?>" width="500px"></a><? }?></div>
						<div class="clearBoth"></div>
					</li>
				</ul>
			</div>
		</div>
		<div class="shenqingcaozuo">
			<ul>
				<? if($shenqing->status==0){?>
				<li>
					<a href="?s=mendian&a=add_mendian&shenqing_id=<?=$shenqing->id?>" ><span>通过</span></a>
				</li>
				<li>
					<a href="javascript:" onclick="z_confirm('确定要删除该申请吗？',bohui,'<?=$id?>');"><span>驳回</span></a>
				</li>
				<? }?>
				<li>
					<a href="javascript:" onclick="z_confirm('确定要删除该申请吗？',del_shenqing,'<?=$id?>');">删除</a>
				</li>
				<li>
					<a href="<?=urldecode($request['returnurl'])?>">返回</a>
				</li>
			</ul>
			<div class="clearBoth"></div>
		</div>
	</div>
	<script type="text/javascript">
		var returnurl = '<?=urldecode($request['returnurl'])?>';
	</script>
	<script type="text/javascript" src="js/mendian/shenqing.js"></script>
</body>
</html>