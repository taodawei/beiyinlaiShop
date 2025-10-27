<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$allRows = array(
				"name"=>array("title"=>"收件姓名","rowCode"=>"{field:'name',title:'收件姓名',width:100}"),
				"xiangshu"=>array("title"=>"箱数","rowCode"=>"{field:'xiangshu',title:'箱数',width:150}"),
				"address"=>array("title"=>"到货地址","rowCode"=>"{field:'address',title:'到货地址',width:100}"),
				"phone"=>array("title"=>"收件电话","rowCode"=>"{field:'phone',title:'收件电话',width:150}"),
				"wuliu"=>array("title"=>"物流方式","rowCode"=>"{field:'wuliu',title:'物流方式',width:150}"),
				"wuliu_phone"=>array("title"=>"物流电话","rowCode"=>"{field:'wuliu_phone',title:'物流电话',width:150}"),
				"biaoji"=>array("title"=>"标记","rowCode"=>"{field:'biaoji',title:'标记',width:150}")
			);
$rowsJS = "";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS = substr($rowsJS,1);
$keyword = $request['keyword'];
$status = (int)$request['status'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = 10;
?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="address"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_94.png"/> 配送单列表（<font color="red">PS：打印配送单之前请先进行发货操作</font>）
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01" style="margin-top: 15px;">
							发货日期：<input type="text" autocomplete="off" style="height:35px;padding-left:5px;background: 0 0;font-size: 12px;color: grey;border: 1px solid #ccc;border-radius: 3px;" id="fahuo_time" value="" placeholder="请选择发货日期"/>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_3">
							<a href="?s=peisongdan&a=daochu" id="daochuB" onclick="daochu1();" class="splist_add">导 出</a>
							<a href="?s=peisongdan&a=prints" id="daochuA" onclick="daochu();" class="splist_add">打 印</a>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="status" value="<?=$status?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		var fahuoTime = '<?=date("Y-m-d")?>';
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form;
		  laydate.render({
		  	elem: '#fahuo_time'
            ,value:'<?=date("Y-m-d")?>',
            done: function(value, date, endDate){
            	fahuoTime = value;
            	reloadTable(0);
            }
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=peisongdan&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	fahuoTime:fahuoTime
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	$("th[data-field='status']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		});
	</script>
	<div id="bg" onclick="hideRowset();"></div>
	<script type="text/javascript" src="js/fahuo/peisongdan.js?v=1"></script>
	<? require('views/help.html');?>
</body>
</html>