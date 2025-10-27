<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$id = (int)$request['id'];
$card_jilu = $db->get_row("select * from gift_card_jilu where id=$id and comId=$comId");
if(empty($card_jilu))die("异常访问");
$allRows = array(
	"typeInfo"=>array("title"=>"礼品卡名称","rowCode"=>"{field:'typeInfo',title:'礼品卡名称',width:240}"),
	"cardId"=>array("title"=>"卡号","rowCode"=>"{field:'cardId',title:'卡号',width:150}"),
	"money"=>array("title"=>"面额（元）","rowCode"=>"{field:'money',title:'面额（元）',width:100}"),
	"yue"=>array("title"=>"余额（元）","rowCode"=>"{field:'yue',title:'余额（元）',width:100}"),
	"binduser"=>array("title"=>"绑定帐号","rowCode"=>"{field:'binduser',title:'绑定帐号',width:150}"),
	"bind_time"=>array("title"=>"绑定时间","rowCode"=>"{field:'bind_time',title:'绑定时间',width:180}")
);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:49,title:'',align:'center', toolbar: '#barDemo'}";
$status = (int)$request['status'];
$keyword = $request['keyword'];
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
	<link href="styles/mendianhuiyuan.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="time"] div,td[data-field="content"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		.layui-layer-tips .layui-layer-content{width:250px;}
	</style>
</head>
<body>
	<div class="right_up">
		<a href="<?=urldecode($request['returnurl'])?>"><img src="images/users_39.png"/></a> <?=$card_jilu->title?>-领用记录
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部状态</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus('0','全部');" class="splist_up_01_left_02_down_on">全部</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('1','未绑定');">未绑定</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('2','已绑定');">已绑定</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="yx_lipinkalingyongjilu_1_left_02">
                        	总数量： <span><?=$card_jilu->num?></span> 张   &nbsp; | &nbsp;  已绑定 <span><?=$card_jilu->bind_num?></span> 张  &nbsp; | &nbsp;  未绑定 <span><?=$card_jilu->num-$card_jilu->bind_num?></span> 张 
                        </div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入卡号/绑定账号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="clearBoth"></div>
				</div>
			</div>
			<div class="splist_down1">
				<table id="product_list" lay-filter="product_list">
				</table>
				<script type="text/html" id="barDemo">
					<div class="yuandian" lay-event="detail" onclick="showNext(this);" onmouseleave="hideNext();">
						<span class="yuandian_01" ></span><span class="yuandian_01"></span><span class="yuandian_01"></span>
					</div>
				</script>
				<div class="yuandian_xx" id="operate_row" data-id="0" style="width:100px;">
					<ul>
						<li id="mingxiBtn">
							<a href="javascript:view();"><img src="images/yingxiao_22.png"> 消费明细</a>
						</li>
						<? if($card_jilu->type==2){?>
							<li id="bindBtn">
								<a href="javascript:bind();"><img src="images/yingxiao_37.png"> 绑定账户</a>
							</li>
						<? }?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="sprkadd_xuanzesp">
		<div class="sprkadd_xuanzesp_01">
			<div class="sprkadd_xuanzesp_01_1">选择会员</div>
			<div class="sprkadd_xuanzesp_01_3">
				<div class="sprkadd_xuanzesp_01_3_left">
					<input type="text" id="keyword_user" placeholder="请输入会员姓名/手机号">
				</div>
				<div class="sprkadd_xuanzesp_01_3_right">
					<a href="javascript:reloadTable1(0);"><img src="images/biao_21.gif"></a>
				</div>
				<div class="clearBoth"></div>
			</div>
			<div class="clearBoth"></div>
		</div>
		<div class="sprkadd_xuanzesp_02">
			<table id="product_list1" lay-filter="product_list1"></table>
		</div>
		<div class="sprkadd_xuanzesp_03">
			<a href="javascript:hideSearch();" class="sprkadd_xuanzesp_03_02">取  消</a>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="status" value="<?=$status?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<input type="hidden" id="returnurl" value="<?=urlencode($request['returnurl'])?>">
	<input type="hidden" id="jiluId" value="<?=$id?>">
	<input type="hidden" id="editId" value="0">
	<script type="text/javascript">
		var productListTalbe,userListTable;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?s=yyyx&a=getCardDetails'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	status:'<?=$status?>',
		    	keyword:'<?=$keyword?>',
		    	jiluId:'<?=$id?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='id']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  userListTable = table.render({
		    elem: '#product_list1'
		    ,height: "full-250"
		    ,url: '?s=users&a=get_select_users'
		    ,page: true
		    ,limit:10
		    ,cols: [[{field:'nickname',title:'姓名',width:150},{field:'username',title:'手机号码',width:150},{field:'level',title:'会员等级',width:150},{field:'select',title:'选择',width:80}]]
		  });
		});
	</script>
	<script type="text/javascript" src="js/yyyx/viewCardJilu.js"></script>
	<? require('views/help.html');?>
</body>
</html>