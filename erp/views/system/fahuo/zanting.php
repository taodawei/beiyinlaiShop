<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
	"view"=>array("title"=>"查看","rowCode"=>"{field:'view',title:'查看',width:44,fixed: 'left'}"),
	"orderId"=>array("title"=>"发货单号","rowCode"=>"{field:'orderId',title:'发货单号',width:220}"),
	"shouhuo"=>array("title"=>"收货人","rowCode"=>"{field:'shouhuo',title:'收货人',width:200}"),
	"tel"=>array("title"=>"收货人电话","rowCode"=>"{field:'tel',title:'收货人电话',width:113}"),
	"address"=>array("title"=>"收货地址","rowCode"=>"{field:'address',title:'收货地址',width:242}"),
	"beizhu"=>array("title"=>"客户备注","rowCode"=>"{field:'beizhu',title:'客户备注',width:228}"),
	"days"=>array("title"=>"下单距今","rowCode"=>"{field:'days',title:'下单距今',width:89}"),
	"dtTime"=>array("title"=>"成单时间","rowCode"=>"{field:'dtTime',title:'成单时间',width:159}")
);
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
//0当前订单 1.未打印 2.已打印
$scene = (int)$request['scene'];
$type = (int)$request['type'];
$status = $request['status'];
$keyword = $request['keyword'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$shouhuoInfo = $request['shouhuoInfo'];
$moneystart = $request['moneystart'];
$moneyend = $request['moneyend'];
$mendian = $request['mendian'];
$storeId = $request['storeId'];
$payStatus = $request['payStatus'];
$pdtInfo = $request['pdtInfo'];
$kaipiao = (int)$request['kaipiao'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['orderPageNum'])?10:$_COOKIE['orderPageNum'];
//计算各类型订单的数量
$num_sql = "select count(*) as num from order_fahuo$fenbiao where comId=$comId and status=-2 ";
$title = '暂停发货';
$zongNum = $db->get_var($num_sql);
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
$mendians = $db->get_results("select id,title from mendian where comId=$comId order by id asc");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/dianzimiandan.css" rel="stylesheet" type="text/css">
	<link href="styles/shangchengdingdan.css" rel="stylesheet" type="text/css">
	<link href="styles/selectUsers.css" rel="stylesheet" type="text/css" />
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reveal.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<script type="text/javascript" src="js/clipboard.min.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="beizhu"] div,td[data-field="address"],td[data-field="mendian"],td[data-field="pdt_info"]{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		.layui-anim.layui-icon{font-size:20px;}
		.layui-form-radio{margin-top:0px;line-height:22px;margin-right:0px;}
		.layui-form-radio i{margin-right:3px;}
		.layui-form-radio span{font-size:12px;}
		.layui-form-select .layui-input{height:25px;}
		.ddxx_jibenxinxi_2_01_down_right .layui-form-select{margin-bottom:2px;}
		.layui-form-selected dl{top:25px;min-height:200px;}
	</style>
</head>
<body>
<? require('views/system/fahuo/header.php')?>
<div id="content">
	<div class="right_up">
		<img src="images/biao_109.png"/> <?=$title.'('.$zongNum.')'?>
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up" style="height:118px;">
				<div class="splist_up_addtab">
	            	<ul>
	            		<li>
	                    	<a href="?s=fahuo&a=zanting" class="splist_up_addtab_on">全部(<?=$zongNum?>)</a>
	                    </li>
	                    <div class="clearBoth"></div>
	            	</ul>
	            </div>
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>发货仓库</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStore('0','全部仓库');">全部仓库</a>
									</li>
									<? if(!empty($cangkus)){
										foreach ($cangkus as $cangku) {
											?><li><a href="javascript:" onclick="selectStore('<?=$cangku->id?>','<?=$cangku->title?>');"><?=$cangku->title?></a></li><?
										}
									}?>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02" style="margin-left:20px;">
							<div class="splist_up_01_left_02_up">
								<span>来源店铺</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectMendian('0','全部门店');">全部门店</a>
									</li>
									<? if(!empty($mendians)){
										foreach ($mendians as $m) {
											?><li><a href="javascript:" onclick="selectMendian('<?=$m->id?>','<?=$m->title?>');"><?=$m->title?></a></li><?
										}
									}?>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="发货单号/订单号/收货人信息/商品名称"/>
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
				<div class="splist_up_02">
					<div class="splist_up_02_1">
						<img src="images/biao_25.png"/>
					</div>
					<div class="splist_up_02_2">
						已选择 <span id="selectedNum">0</span> 项
					</div>
					<div class="dangqiandd_2_down_3">
						<a href="javascript:;" id="huifu_fahuo"><img src="images/miandan_12.png" /> 恢复发货</a>
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
	<div class="dqddxiangqing" id="dqddxiangqing" data-id="0" style="display:none;">
		<div class="dqddxiangqing_up" id="orderInfoMenu">
			<ul>
				<li>
					<a href="javascript:" id="orderInfoMenu1" onclick="qiehuan('orderInfo',1,'dqddxiangqing_up_on');" class="dqddxiangqing_up_on">发货单详情</a>
				</li>
				<li>
					<a href="javascript:" id="orderInfoMenu2" onclick="qiehuan('orderInfo',2,'dqddxiangqing_up_on');order_xiangqing_index(0);">货品详情</a>
				</li> 
				<!-- <li>
					<a href="javascript:" id="orderInfoMenu3" onclick="qiehuan('orderInfo',3,'dqddxiangqing_up_on');order_tuihuan_index(0);">物流单详情</a>
				</li>
				<li>
					<a href="javascript:" id="orderInfoMenu4" onclick="qiehuan('orderInfo',4,'dqddxiangqing_up_on');order_service_index(0);">订单服务</a>
				</li> -->
				<li>
					<a href="javascript:" id="orderInfoMenu5" onclick="qiehuan('orderInfo',5,'dqddxiangqing_up_on');order_jilu_index(0);">操作记录</a>
				</li>
				<div class="clearBoth"></div>
			</ul>
		</div>
		<div class="dqddxiangqing_down">
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont1">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont2" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont3" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont4" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
			<div class="dqddxiangqing_down_01 orderInfoCont" id="orderInfoCont5" style="display:none;">
				<div class="loading"><img src="images/loading.gif"></div>
			</div>
		</div>
	</div>
</div>
<!--批量服务分配-->
<div class="ddfw_piliangfenpei_tc" id="ddfw_piliangfenpei_tc" data-type="0" data-id="0" style="display:none;">
	<div class="bj"></div>
    <div class="ddfw_adddingdangfuwu">
    	<div class="dqpiliangshenhe_01">
        	<div class="dqpiliangshenhe_01_left">
            	服务分配
            </div>
        	<div class="dqpiliangshenhe_01_right" onclick="$('#ddfw_piliangfenpei_tc').hide();">
            	<img src="images/close_1.png" alt=""/>
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="ddfw_piliangfenpei1">
        	<ul>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        <span>*</span> 服务人员：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="fanwei_1" readonly="true" onclick="fanwei(1);" placeholder="选择服务人员" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        联系电话：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="service_phone" placeholder="请输入服务人员电话" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
                <li>
                    <div class="ddfw_adddingdangfuwu1_1_title">
                        预约服务时间：
                    </div>
                    <div class="ddfw_adddingdangfuwu1_1_tt">
                        <input type="text" id="service_time" placeholder="请选择预约服务时间" style="width:410px;"/>
                    </div>
                    <div class="clearBoth"></div>
                </li>
        	</ul>
        </div>
    	<div class="dqpiliangshenhe_03">
        	<a href="javascript:" onclick="service_fenpei();">立即分配</a>
        </div>
        <input type="hidden" id="editId" value="0">
        <input type="hidden" id="users" value="0">
        <input type="hidden" id="userNames" value="">
    </div>
</div>
<!--批量服务分配结束-->
<!--普通发货-发货弹出-->
<div class="putongfh_fahuo_tc" style="display:none;">
	<div class="bj"></div>
    <div class="damx_genghuanwuliu">
    	<div class="damx_genghuanwuliu_1">
        	<div class="damx_genghuanwuliu_1_left">
            	普通快递发货
            </div>
        	<div class="damx_genghuanwuliu_1_right" onclick="fahuo_hide();">
            	<img src="images/miandan_13.png" alt=""/>
            </div>
        	<div class="clearBoth"></div>
        </div>
        <form method="post" id="forms" class="layui-form" action="">
        <input type="hidden" name="id" id="orderId" value="">
    	<div class="putongfh_fahuo_02">
        	<div class="putongfh_fahuo_02_up">
            	<ul>
            		<li>
                    	<div class="putongfh_fahuo_02_up_left">
                        	物流公司
                        </div>
                    	<div class="putongfh_fahuo_02_up_right">
                        	<input type="text" name="kuaidi_company" required lay-verify="required" placeholder="请填入物流公司" class="putongfh_fahuo_02_up_right_input"/>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="putongfh_fahuo_02_up_left">
                        	物流单号
                        </div>
                    	<div class="putongfh_fahuo_02_up_right">
                        	<input type="text" name="kuaidi_order" required lay-verify="required" placeholder="请填入物流单号" class="putongfh_fahuo_02_up_right_input"/>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
            	</ul>
            </div>
        	<div class="putongfh_fahuo_02_down">
            	<img src="images/miandan_16.png" alt=""/> 单独发货后，直接进入已发货状态
            </div>
        </div>
    	<div class="damx_genghuanwuliu_3">
        	<a href="javascript:;" lay-submit lay-filter="formDemo">确认发货</a>
        </div>
    	</form>
    </div>
</div>
<!--修改收货信息-->
<div class="damx_genghuanwuliu_tc" id="shouhuo_div" style="display:none;">
    <div class="bj" onclick="$('#shouhuo_div').hide()">
    </div>
    <div class="damx_genghuanwuliu">
        <div class="damx_genghuanwuliu_1">
            <div class="damx_genghuanwuliu_1_left">
                收货信息
            </div>
            <div class="damx_genghuanwuliu_1_right" onclick="$('#shouhuo_div').hide()">
                <img src="images/miandan_13.png" alt=""/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="damx_genghuanwuliu_2">
            <div class="damx_genghuanwuliu_2_2" style="height:30px;">
                　收件人：<input type="text" name="name" id="shuohuo_edit_name" />
            </div>
			<div class="damx_genghuanwuliu_2_2" style="height:30px;">
                　手机号：<input type="text" name="phone" id="shuohuo_edit_phone" />
            </div>
			<div class="damx_genghuanwuliu_2_2" style="height:30px;">
                所在地区：<input type="text" name="diqu" id="shuohuo_edit_diqu" />
            </div>
			<div class="damx_genghuanwuliu_2_2" style="height:30px;">
                详细地址：<input type="text" name="address" style="width:350px" id="shuohuo_edit_address" />
            </div>
        </div>
		<input type="hidden" id="shuohuo_edit_id" value="0">
        <div class="damx_genghuanwuliu_3">
            <a href="javascript:;" onclick="update_shouhuo();">确定</a>
        </div>
    </div>
</div>
<!--普通发货-发货弹出结束-->
<div id="myModal" class="reveal-modal"><div style="text-align:center;padding:20px 0px;"><img src="images/loading.gif"></div></div>
<input type="hidden" id="nowIndex" value="">
<input type="hidden" id="scene" value="<?=$scene?>">
<input type="hidden" id="status" value="<?=$status?>">
<input type="hidden" id="type" value="<?=$type?>">
<input type="hidden" id="orderId" value="<?=$orderId?>">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="kehuName" value="<?=$kehuName?>">
<input type="hidden" id="shouhuoInfo" value="<?=$shouhuoInfo?>">
<input type="hidden" id="pdtInfo" value="<?=$pdtInfo?>">
<input type="hidden" id="payStatus" value="<?=$payStatus?>">
<input type="hidden" id="moneystart" value="<?=$moneystart?>">
<input type="hidden" id="moneyend" value="<?=$moneyend?>">
<input type="hidden" id="kaipiao" value="<?=$kaipiao?>">
<input type="hidden" id="mendian" value="<?=$mendian?>">
<input type="hidden" id="storeId" value="<?=$storeId?>">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" id="selectedIds" value="">
<script type="text/javascript">
	//验证表单
	layui.use('form', function(){
	  var form = layui.form;
	  
	  //监听提交
	  form.on('submit(formDemo)', function(data){
	  	var content = $("#forms").serialize();
	    ajaxpost=$.ajax({
			type: "POST",
			url: "?s=fahuo&a=order_fahuo",
			data: content,
		    dataType : "json",
		    timeout : 20000,
			success: function(data) {
				order_info_index(1);
				$("#forms")[0].reset();
				$(".putongfh_fahuo_tc").hide();
				layer.msg(data.message);
			},
			error: function() {
	            layer.msg('网络错误，请检查网络',{icon:5});
	        }
		});
	  });
	});
	//验证表单
	var productListTalbe,lay_date;
	layui.use(['laydate', 'laypage','table','form'], function(){
	  var laydate = layui.laydate
	  ,laypage = layui.laypage
	  ,table = layui.table
	  ,form = layui.form;
	  lay_date = laydate;
	  //,load = layer.load()
	  laydate.render({
	  	elem: '#riqi1'
	  	,show: true
	  	,position: 'static'
	  	,min: '2018-01-01'
	  	,max: '<?=date("Y-m-d")?>'
	  	<?=empty($startTime)?'':",value:'$startTime'"?>
	  	,btns: []
	  	,done: function(value, date, endDate){
	  		$("#s_time1").html(value);
	  		$("#super_startTime").val(value);
	  	}
	  });
	  laydate.render({
	  	elem: '#riqi2'
	  	,show: true
	  	,position: 'static'
	  	<?=empty($endTime)?'':",value:'$endTime'"?>
	  	,min: '2018-01-01'
			,max: '<?=date("Y-m-d")?>'
			,btns: ['confirm']
			,done: function(value, date, endDate){
				$("#s_time2").html(value);
				$("#super_endTime").val(value);
			}
	  });
	  laydate.render({
	  	elem: '#service_time'
	  	,min: '<?=date("Y-m-d",strtotime("-1 days"))?>'
	  	,type: 'datetime'
	  	,format:'yyyy-MM-dd HH:mm'
	  });
	  $(".laydate-btns-confirm").click(function(){
	  	$("#riqilan").slideUp(200);
	  });
	  productListTalbe = table.render({
	    elem: '#product_list'
	    ,height: "full-200"
	    ,url: '?s=fahuo&a=getList&print_type=-2'
	    ,page: {curr:<?=$page?>}
	    ,limit:<?=$limit?>
	    ,cols: [[<?=$rowsJS?>]]
	    ,where:{
	    	scene:'<?=$scene?>',
	    	status:'<?=$status?>',
	    	type:'<?=$type?>',
	    	orderId:'<?=$orderId?>',
	    	startTime:'<?=$startTime?>',
	    	keyword:'<?=$keyword?>',
	    	mendian:'<?=$mendian?>',
	    	storeId:'<?=$storeId?>',
	    	endTime:'<?=$endTime?>',
	    	kehuName:'<?=$kehuName?>',
	    	shouhuoInfo:'<?=$shouhuoInfo?>',
	    	moneystart:'<?=$moneystart?>',
	    	moneyend:'<?=$moneyend?>',
	    	payStatus:'<?=$payStatus?>',
	    	pdtInfo:'<?=$pdtInfo?>',
	    	kaipiao:'<?=$kaipiao?>'
	    },done: function(res, curr, count){
	    	layer.closeAll('loading');
		    $("#page").val(curr);
		    $("th[data-field='id']").hide();
		    $("th[data-field='status']").hide();
		  }
	  });
	  table.on('sort(product_list)', function(obj){
	  	var scene = $("#scene").val();
	  	var status = $("#status").val();
	  	var type = $("#type").val();
	  	var orderId = $("#orderId").val();
	  	var startTime = $("#startTime").val();
	  	var keyword = $("#keyword").val();
	  	var mendian = $("#mendian").val();
	  	var storeId = $("#storeId").val();
	  	var endTime = $("#endTime").val();
	  	var kehuName = $("#kehuName").val();
	  	var moneystart = $("#moneystart").val();
	  	var moneyend = $("#moneyend").val();
	  	var shouhuoInfo = $("#shouhuoInfo").val();
	  	var pdtInfo = $("#pdtInfo").val();
	  	var payStatus = $("#payStatus").val();
	  	var kaipiao = $("#kaipiao").val();
	  	$("#order1").val(obj.field);
	  	$("#order2").val(obj.type);
	  	var scrollLeft = $(".layui-table-body").scrollLeft();
	  	layer.load();
		table.reload('product_list', {
		    initSort: obj
		    ,height: "full-200"
		    ,where: {
		      order1: obj.field
		      ,order2: obj.type
		      ,scene:scene
		      ,status:status
		      ,type:type
		      ,orderId:orderId
		      ,startTime:startTime
		      ,keyword:keyword
		      ,mendian:mendian
		      ,storeId:storeId
		      ,endTime:endTime
		      ,kehuName:kehuName
		      ,moneystart:moneystart
		      ,moneyend:moneyend
		      ,shouhuoInfo:shouhuoInfo
		      ,kaipiao:kaipiao
		      ,pdtInfo:pdtInfo
		      ,payStatus:payStatus
		    },page: {
				curr: 1
			},done:function(){
				$(".layui-table-header").scrollLeft(scrollLeft);
				$(".layui-table-body").scrollLeft(scrollLeft);
				$("th[data-field='id']").hide();
				$("th[data-field='status']").hide();
				layer.closeAll('loading');
			}
		  });
	  });
	  form.on('checkbox(status)', function(data){
	  	if(data.elem.checked){
	  		$("input[pid='status']").prop("checked",false);
	  	}
	  	form.render('checkbox');
	  });
	  form.on('checkbox(nostatus)', function(data){
	  	$("input[name='super_status_all']").prop("checked",false);
	  	form.render('checkbox');
	  });
	  form.on('submit(search)', function(data){
	  	$("#orderId").val(data.field.super_orderId);
	  	$("#startTime").val(data.field.super_startTime);
	  	$("#endTime").val(data.field.super_endTime);
	  	$("#kehuName").val(data.field.super_kehuName);
	  	$("#moneystart").val(data.field.super_moneystart);
	  	$("#moneyend").val(data.field.super_moneyend);
	  	$("#shouhuoInfo").val(data.field.super_shouhuoInfo);
	  	$("#pdtInfo").val(data.field.super_pdtInfo);
	  	if(data.field.super_status_all=="on"){
	  		$("#status").val('');
	  	}else{
	  		var cangkustr = '';
	  		$("input:checkbox[name='super_status']:checked").each(function(){
	  			cangkustr = cangkustr+','+$(this).val();
	  		});
	  		if(cangkustr.length>0){
	  			cangkustr = cangkustr.substring(1);
	  		}
	  		$("#status").val(cangkustr);
	  	}
	  	$("#payStatus").val(data.field.super_payStatus);
	  	$("#kaipiao").val(data.field.super_kaipiao);
	  	hideSearch();
	  	reloadTable(0);
	  	return false;
	  });
	  form.on('submit(quxiao)', function(){
	  	hideSearch();
	  	return false;
	  });
	  table.on('checkbox(product_list)', function(obj){
		var checkStatus = table.checkStatus('product_list')
	    ,data = checkStatus.data;
	    if(data.length>0){
	    	var ids = '';
	    	for (var i = 0; i < data.length; i++) {
	    		if(i==0){
	    			ids = data[i].id;
	    		}else{
	    			ids = ids+','+data[i].id;
	    		}
	    	}
	    	$("#selectedIds").val(ids);
	    	$(".splist_up_01").hide();
	    	$(".splist_up_02").show().find(".splist_up_02_2 span").html(data.length);
	    }else{
	    	$(".splist_up_02").hide();
	    	$(".splist_up_01").show();
	    }
	  });
	});
function daochu(){
	var ids = $("#selectedIds").val();
	$("#daochu").attr("href",$("#daochu").attr("href")+"&ids="+ids);
	//console.log($("#daochu").attr("src"));
	reloadTable(0);
}
function update_shouhuo(){
		var id = $("#shuohuo_edit_id").val();
		var name = $("#shuohuo_edit_name").val();
		var phone = $("#shuohuo_edit_phone").val();
		var diqu = $("#shuohuo_edit_diqu").val();
		var address = $("#shuohuo_edit_address").val();
		layer.load();
		$.ajax({
			type: "POST",
			url: "?m=system&s=fahuo&a=update_shouhuo&id="+id,
			data: "name="+name+"&phone="+phone+"&diqu="+diqu+"&address="+address,
			dataType:'json',timeout : 10000,
			success: function(resdata){
				layer.closeAll();
				$("#shouhuo_div").hide();
				order_dianzi_info(1);
			},
			error: function(XMLHttpRequest, textStatus, errorThrown) {
				layer.msg("请求超时，请检查网络");
				hide_myModal();
			}
		});
	}
	function xiugai_shouhuo(id,name,phone,diqu,address){
		$("#shuohuo_edit_id").val(id);
		$("#shuohuo_edit_name").val(name);
		$("#shuohuo_edit_phone").val(phone);
		$("#shuohuo_edit_diqu").val(diqu);
		$("#shuohuo_edit_address").val(address);
		$("#shouhuo_div").show();
	}
</script>
<script type="text/javascript" src="js/fahuo/order_list.js"></script>
<script type="text/javascript" src="js/fahuo/order_info.js"></script>
<div id="bg" onclick="hideRowset();"></div>
</body>
</html>