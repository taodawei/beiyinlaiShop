<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$kehu_title = $_SESSION[TB_PREFIX.'kehu_title'];
$allRows = array(
				"orderId"=>array("title"=>"订单号","rowCode"=>"{field:'orderId',title:'订单号',width:240}"),
				"dtTime"=>array("title"=>"下单时间","rowCode"=>"{field:'dtTime',title:'下单时间',width:150,sort:true}"),
				"kehuName"=>array("title"=>$kehu_title."名称","rowCode"=>"{field:'kehuName',title:'".$kehu_title."名称',width:250,sort:true,style:\"height:auto;line-height:22px;white-space:normal;\"}"),
				"kehuSn"=>array("title"=>$kehu_title."编号","rowCode"=>"{field:'kehuSn',title:'".$kehu_title."编号',width:150}"),
				"price"=>array("title"=>"金额","rowCode"=>"{field:'price',title:'金额',width:150}"),
				"chuku_fahuo"=>array("title"=>"出库/发货","rowCode"=>"{field:'chuku_fahuo',title:'出库/发货',width:150}"),
				"status"=>array("title"=>"状态","rowCode"=>"{field:'status',title:'状态',width:150,sort:true}"),
				"payStatus"=>array("title"=>"付款状态","rowCode"=>"{field:'payStatus',title:'付款状态',width:150}"),
				"beizhu"=>array("title"=>"备注","rowCode"=>"{field:'beizhu',title:'备注',width:250}"),
				"fapiaoType"=>array("title"=>"发票类型","rowCode"=>"{field:'fapiaoType',title:'发票类型',width:150}"),
				"jiaohuoTime"=>array("title"=>"交货日期","rowCode"=>"{field:'jiaohuoTime',title:'交货日期',width:120}"),
				"shouhuo_user"=>array("title"=>"收货人","rowCode"=>"{field:'shouhuo_user',title:'收货人',width:120}"),
				"shouhuo_phone"=>array("title"=>"联系方式","rowCode"=>"{field:'shouhuo_phone',title:'联系方式',width:150}"),
				"shouhuo_address"=>array("title"=>"收货地址","rowCode"=>"{field:'shouhuo_address',title:'收货地址',width:300}"),
				"userName"=>array("title"=>"制单人","rowCode"=>"{field:'userName',title:'制单人',width:100}")
			);
$showRowstr = $db->get_var("select showRows from demo_kehu_shezhi where comId=$comId");
if(empty($showRowstr)){
	$showRowsArry = array("orderId"=>1,"dtTime"=>1,"kehuName"=>1,"kehuSn"=>1,"price"=>1,"chuku_fahuo"=>1,"status"=>1,"payStatus"=>1,"beizhu"=>1,"fapiaoType"=>1,"jiaohuoTime"=>1,"shouhuo_user"=>1,"shouhuo_phone"=>1,"shouhuo_address"=>1,"userName"=>1);
	$showRows = json_encode($showRowsArry,JSON_UNESCAPED_UNICODE);
	$db->query("update demo_kehu_shezhi set showRows='$showRows' where comId=$comId");
}else{
	$showRowsArry = json_decode($showRowstr,true);
	if(empty($showRowsArry))$showRowsArry = array("orderId"=>1,"dtTime"=>1,"kehuName"=>1,"kehuSn"=>1,"price"=>1,"chuku_fahuo"=>1,"status"=>1,"payStatus"=>1,"beizhu"=>1,"fapiaoType"=>1,"jiaohuoTime"=>1,"shouhuo_user"=>1,"shouhuo_phone"=>1,"shouhuo_address"=>1,"userName"=>1);
}
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:\"display:none;\"}";
foreach ($showRowsArry as $row=>$isshow){
	if($isshow==1){
		$rowsJS.=','.$allRows[$row]['rowCode'];
	}
}
$rowsJS .=",{fixed:'right',width:49,title:'<img src=\"images/biao_22.png\" onclick=\"showRowset();\">',align:'center', toolbar: '#barDemo'}";
$status = $request['status'];
$keyword = $request['keyword'];
$orderId = $request['orderId'];
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$kehuName = $request['kehuName'];
$level = (int)$request['level'];
$shouhuoInfo = $request['shouhuoInfo'];
$departId = (int)$request['departId'];
$pdtInfo = $request['pdtInfo'];
$payStatus = $request['payStatus'];
$tags = $request['tags'];
$orderType = (int)$request['orderType'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['dinghuoPageNum'])?10:$_COOKIE['dinghuoPageNum'];
$levels = $db->get_results("select id,title from demo_kehu_level where comId=$comId order by ordering desc,id asc");
$nums = $db->get_results("select status,count(*) as num from demo_dinghuo_order where comId=$comId group by status");
$numArry = array();
$zongNum = 0;
if(!empty($nums)){
	foreach ($nums as $n){
		$zongNum+=$n->num;
		$numArry[$n->status] = $n->num;
	}
}
$daiqueren = (int)$db->get_var("select count(*) from demo_dinghuo_order where comId=$comId and status>0 and price_weikuan>0");
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
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="key_vals"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;cursor:pointer;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="chuku_fahuo"] div{line-height:20px;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		.table-tag{display:inline-block;position:static;margin-left:3px;}
		.table-tag .sub-tag{background-color:#03a9f3;color:#fff;padding:0px 2px;font-size:12px;}
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_109.png"/> 订货单
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up" style="height:118px;">
				<div class="splist_up_addtab">
	            	<ul>
	            		<li>
	                    	<a href="?m=system&s=dinghuo" <? if(empty($status)){?>class="splist_up_addtab_on"<? }?>>全部(<?=$zongNum?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=-2" <? if($status==-2){?>class="splist_up_addtab_on"<? }?>>待审核订单(<?=(int)$numArry[0]?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=1" <? if($status==1){?>class="splist_up_addtab_on"<? }?>>待财务审核(<?=$daiqueren?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=2,3" <? if($status=='2,3'){?>class="splist_up_addtab_on"<? }?>>待出库审核(<?=(int)($numArry[2]+$numArry[3])?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=4" <? if($status==4){?>class="splist_up_addtab_on"<? }?>>待发货确认(<?=(int)$numArry[4]?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=5" <? if($status==5){?>class="splist_up_addtab_on"<? }?>>待<?=$kehu_title?>收货(<?=(int)$numArry[5]?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=6" <? if($status==6){?>class="splist_up_addtab_on"<? }?>>已完成(<?=(int)$numArry[6]?>)</a>
	                    </li>
	                    <li>
	                    	<a href="?m=system&s=dinghuo&status=-1" <? if($status==-1){?>class="splist_up_addtab_on"<? }?>>已作废(<?=(int)$numArry[-1]?>)</a>
	                    </li>
	                    <div class="clearBoth"></div>
	            	</ul>
	            </div>
				<div class="splist_up_01">
					<div class="splist_up_01_left" style="display:none;">
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部订单</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectStatus('0','全部订单');" class="splist_up_01_left_02_down_on">全部订单</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('0,1,2,3,4','待处理订单');">待处理订单</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('0,1,2,3,4,5','未完成订单');">未完成订单</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('6','已完成订单');">已完成订单</a>
									</li>
									<li>
										<a href="javascript:" onclick="selectStatus('-1','已作废订单');">已作废订单</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入<?=$kehu_title?>名称/订单号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_2">
							<div class="splist_up_01_right_2_up">
								高级搜索
							</div>
							<div class="splist_up_01_right_2_down">
								<div class="splist_up_01_right_2_down1">
									<div class="splist_up_01_right_2_down1_01">
										高级搜索
									</div>
									<div class="splist_up_01_right_2_down1_02">
										<form id="searchForm" class="layui-form">
											<ul>
												<li>
													<div class="gaojisousuo_left">
														订货单号
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_orderId" value="<?=$orderId?>" class="gaojisousuo_right_input" placeholder="请输入订货单号"/>
													</div>
													<div class="gaojisousuo_left">
														下单时间
													</div>
													<div class="gaojisousuo_right" style="height:35px;">
														<div class="sprukulist_01" style="top:0px;margin-left:0px;z-index:999;">
															<div class="sprukulist_01_left">
																<span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
															</div>
															<div class="sprukulist_01_right">
																<img src="images/biao_76.png"/>
															</div>
															<div class="clearBoth"></div>
															<div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;">
																<div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
															</div>
															<input type="hidden" name="super_startTime" id="super_startTime" value="<?=$startTime?>">
															<input type="hidden" name="super_endTime" id="super_endTime" value="<?=$endTime?>">
														</div>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														<?=$kehu_title?>名称
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_kehuName" value="<?=$kehuName?>" class="gaojisousuo_right_input" placeholder="请输入<?=$kehu_title?>名称"/>
													</div>
													<div class="gaojisousuo_left">
														<?=$kehu_title?>级别
													</div>
													<div class="gaojisousuo_right">
														<select name="super_level" id="super_level" lay-search>
															<option value="">选择级别</option>
															<? if(!empty($levels)){
																foreach ($levels as $l) {
																	?>
																	<option value="<?=$l->id?>"><?=$l->title?></option>
																	<?
																}
															}?>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														收货信息
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_shouhuoInfo" value="<?=$shouhuoInfo?>" class="gaojisousuo_right_input" placeholder="请输入收货信息"/>
													</div>
													<div class="gaojisousuo_left">
														归属部门
													</div>
													<div class="gaojisousuo_right">
														<select name="super_departId" id="super_departId" lay-search>
															<option value="">选择部门</option>
														</select>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														商品信息
													</div>
													<div class="gaojisousuo_right">
														<input type="text" name="super_pdtInfo" value="<?=$pdtInfo?>" class="gaojisousuo_right_input" placeholder="请输入商品信息"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li style="display:none;">
													<div class="gaojisousuo_left">
														订单状态
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_status_all" lay-skin="primary" lay-filter="status" title="全选" checked />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="-2" title="待订单审核" />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="1" title="待财务审核" />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="2,3" title="待出库审核" />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="4" title="待发货确认" />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="5" title="待<?=$kehu_title?>收货" />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="6" title="已完成" />
														<input type="checkbox" name="super_status" pid="status" lay-skin="primary" lay-filter="nostatus" value="-1" title="已作废" />
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														付款状态
													</div>
													<div class="gaojisousuo_right">
														<input type="checkbox" name="super_payStatus_all" lay-skin="primary" lay-filter="payStatus" title="全部" checked />
														<input type="checkbox" name="super_payStatus" pid="payStatus" lay-skin="primary" lay-filter="nopayStatus" value="1" title="未付款" />
														<input type="checkbox" name="super_payStatus" pid="payStatus" lay-skin="primary" lay-filter="nopayStatus" value="2" title="付款待审核" />
														<input type="checkbox" name="super_payStatus" pid="payStatus" lay-skin="primary" lay-filter="nopayStatus" value="3" title="部分付款" />
														<input type="checkbox" name="super_payStatus" pid="payStatus" lay-skin="primary" lay-filter="nopayStatus" value="4" title="已付款" />
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														订单标签
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="super_tag" value="0" title="不限" checked/><input type="radio" name="super_tag" value="1" title="特价单"/><input type="radio" name="super_tag" value="2" title="非特价单"/><input type="radio" name="super_tag" value="3" title="秒杀订单"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														下单方式
													</div>
													<div class="gaojisousuo_right">
														<input type="radio" name="super_orderType" value="0" title="全部" checked/><input type="radio" name="super_orderType" value="1" title="自主下单"/><input type="radio" name="super_orderType" value="2" title="代下单"/>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_tijiao">
														<button class="layui-btn layui-btn-normal" lay-submit="" lay-filter="search" > 确 定 </button>
														<button type="layui-btn" lay-submit="" class="layui-btn layui-btn-primary" lay-filter="quxiao"> 取 消 </button>
														<button type="reset" class="layui-btn layui-btn-primary"> 重 置 </button>
													</div>
												</li>
											</ul>
										</form>
									</div>                                    
								</div>
							</div>
						</div>
						<? if($adminRole>=7||strstr($qx_arry['dinghuo']['functions'],'add')){?>
						<div class="splist_up_01_right_3">
							<!-- <a href="?m=system&s=product&a=daoru" class="splist_daoru">导 入</a> -->
							<a href="?m=system&s=dinghuo&a=create" class="splist_add">新 增</a>
						</div>
						<? }?>
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
					<div class="splist_up_02_3">
						<a href="javascript:" onclick="shenhe_order();"><img src="images/biao_110.png"> 订单审核</a>
						<a href="javascript:" onclick="shenhe_caiwu();"><img src="images/biao_111.png"> 财务审核</a>
						<!-- <a href="javascript:" onclick="shenhe_chuku();"><img src="images/biao_112.png"> 出库审核</a>
						<a href="javascript:" onclick="shenhe_fahuo();"><img src="images/biao_113.png"> 发货确认</a>
						<a href="javascript:" onclick="daochu();"><img src="images/biao_114.png"> 导出</a> -->
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
						<li id="sheheBtn">
							<a href="javascript:view();"><img src="images/biao_108.png"> 审核</a>
						</li>
						<li>
							<a href="javascript:view();"><img src="images/biao_30.png"> 订单详情</a>
						</li>
						<li id="shoukuanBtn">
							<a href="javascript:addShoukuan();" style="color:#ff7800;">+ 添加收款记录</a>
						</li>
					</ul>
				</div>
				<div class="xianshiziduan" id="xianshiziduan">
					<div class="xianshiziduan1">
						<div class="xianshiziduan_1">
							选择显示字段
						</div>
						<form action="?m=system&s=dinghuo&a=rowsSet" method="POST" id="rowsSetForm" class="layui-form">
							<div class="xianshiziduan_2">
								<ul>
									<?
									$i=0;
									foreach ($allRows as $field=>$row) {
										$i++;
										?>
										<li>
											<input type="checkbox" name="rowsSet[<?=$field?>]" lay-skin="primary" title="<?=$row['title']?>" <? if($field=='orderId'){?>disabled<?}if($showRowsArry[$field]==1){?> checked<? }?> />
											<? if($i>2){?>
											<span class="rowtodown" onclick="rowToDown(this);"><img src="images/biao_34.png"/></span><span class="rowtoup" onclick="rowToUp(this);"><img src="images/biao_33.png"/></span>
											<? }else if($i==2){?>
											<span class="rowtodown" onclick="rowToDown(this);"><img src="images/biao_34.png"/></span>
											<? }?>
										</li>
										<?
									}
									?>
								</ul>
							</div>
							<div class="xianshiziduan_3">
								<a href="javascript:" onclick="$('#rowsSetForm').submit();" class="xianshiziduan_3_01">确定</a><a href="javascript:" onclick="hideRowset();" class="xianshiziduan_3_02">取消</a>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="status" value="<?=$status?>">
	<input type="hidden" id="orderId" value="<?=$orderId?>">
	<input type="hidden" id="startTime" value="<?=$startTime?>">
	<input type="hidden" id="endTime" value="<?=$endTime?>">
	<input type="hidden" id="kehuName" value="<?=$kehuName?>">
	<input type="hidden" id="level" value="<?=$level?>">
	<input type="hidden" id="shouhuoInfo" value="<?=$shouhuoInfo?>">
	<input type="hidden" id="departId" value="<?=$departId?>">
	<input type="hidden" id="pdtInfo" value="<?=$pdtInfo?>">
	<input type="hidden" id="payStatus" value="<?=$payStatus?>">
	<input type="hidden" id="tags" value="<?=$tags?>">
	<input type="hidden" id="orderType" value="<?=$orderType?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
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
		  $(".laydate-btns-confirm").click(function(){
		  	$("#riqilan").slideUp(200);
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-200"
		    ,url: '?m=system&s=dinghuo&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	status:'<?=$status?>',
		    	orderId:'<?=$orderId?>',
		    	startTime:'<?=$startTime?>',
		    	keyword:'<?=$keyword?>',
		    	endTime:'<?=$endTime?>',
		    	kehuName:'<?=$kehuName?>',
		    	level:<?=$level?>,
		    	shouhuoInfo:'<?=$shouhuoInfo?>',
		    	departId:<?=$departId?>,
		    	pdtInfo:'<?=$pdtInfo?>',
		    	payStatus:'<?=$payStatus?>',
		    	tags:'<?=$tags?>',
		    	orderType:<?=$orderType?>
		    },done: function(res, curr, count){
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  
		  $("th[data-field='id']").hide();
		  table.on('sort(product_list)', function(obj){
		  	var status = $("#status").val();
		  	var orderId = $("#orderId").val();
		  	var startTime = $("#startTime").val();
		  	var keyword = $("#keyword").val();
		  	var endTime = $("#endTime").val();
		  	var kehuName = $("#kehuName").val();
		  	var level = $("#level").val();
		  	var shouhuoInfo = $("#shouhuoInfo").val();
		  	var departId = $("#departId").val();
		  	var pdtInfo = $("#pdtInfo").val();
		  	var payStatus = $("#payStatus").val();
		  	var tags = $("#tags").val();
		  	var orderType = $("#orderType").val();
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
			      ,status:status
			      ,orderId:orderId
			      ,startTime:startTime
			      ,keyword:keyword
			      ,endTime:endTime
			      ,kehuName:kehuName
			      ,level:level
			      ,shouhuoInfo:shouhuoInfo
			      ,departId:departId
			      ,pdtInfo:pdtInfo
			      ,payStatus:payStatus
			      ,tags:tags
			      ,orderType:orderType
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='id']").hide();
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
		  form.on('checkbox(payStatus)', function(data){
		  	if(data.elem.checked){
		  		$("input[pid='payStatus']").prop("checked",false);
		  	}
		  	form.render('checkbox');
		  });
		  form.on('checkbox(nopayStatus)', function(data){
		  	$("input[name='super_payStatus_all']").prop("checked",false);
		  	form.render('checkbox');
		  });
		  form.on('submit(search)', function(data){
		  	$("#orderId").val(data.field.super_orderId);
		  	$("#startTime").val(data.field.super_startTime);
		  	$("#endTime").val(data.field.super_endTime);
		  	$("#kehuName").val(data.field.super_kehuName);
		  	$("#level").val(data.field.level);
		  	$("#shouhuoInfo").val(data.field.super_shouhuoInfo);
		  	$("#departId").val(data.field.super_departId);
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
		  	if(data.field.super_payStatus_all=="on"){
		  		$("#payStatus").val('');
		  	}else{
		  		var cangkustr = '';
		  		$("input:checkbox[name='super_payStatus']:checked").each(function(){
		  			cangkustr = cangkustr+','+$(this).val();
		  		});
		  		if(cangkustr.length>0){
		  			cangkustr = cangkustr.substring(1);
		  		}
		  		$("#payStatus").val(cangkustr);
		  	}
		  	$("#tags").val(data.field.super_tag);
		  	$("#orderType").val(data.field.super_orderType);
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
		  $("#setTags").click(function(){
		  	active['setTags'].call(this);
		  });
		  ajaxpost=$.ajax({
		  	type: "POST",
		  	url: "/erp_service.php?action=getComDeparts",
		  	data: "",
		  	dataType:"text",timeout : 30000,
		  	beforeSend:function(){
		  		<? if($request['page']>1){?>
		  		reloadTable(1);
		  		<? }?>
		  	},
		  	success: function(resdata){
		  		$("#super_departId").append(resdata);
		  		form.render('select');
		  	},
		  	error: function() {
		  		layer.msg('数据请求失败', {icon: 5});
		  	}
		  });
		});
	</script>
	<script type="text/javascript" src="js/dinghuo_list.js"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>