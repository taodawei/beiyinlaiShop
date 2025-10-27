<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$if_fenxiao = 1;
$allRows = array(
	"id"=>array("title"=>"会员ID","rowCode"=>"{field:'id',title:'会员ID',width:120}"),
	"nickname"=>array("title"=>"昵称","rowCode"=>"{field:'nickname',title:'昵称',width:150}"),
	"phone"=>array("title"=>"手机号","rowCode"=>"{field:'phone',title:'手机号',width:180}"),
	"level"=>array("title"=>"会员等级","rowCode"=>"{field:'level',title:'会员等级',width:100}"),
// 	"cityname"=>array("title"=>"所在城市","rowCode"=>"{field:'cityname',title:'所在城市',width:160}"),
	"money"=>array("title"=>"余额","rowCode"=>"{field:'money',title:'余额',width:100,sort:true}"),
    // "yongjin"=>array("title"=>"佣金","rowCode"=>"{field:'yongjin',title:'佣金',width:150}"),
// 	"jifen"=>array("title"=>"积分","rowCode"=>"{field:'jifen',title:'积分',width:150,sort:true}"),
// 	"shangji"=>array("title"=>"上级会员","rowCode"=>"{field:'shangji',title:'上级会员',width:160}"),
    // 	"fans_num"=>array("title"=>"下级数量","rowCode"=>"{field:'fans_num',title:'下级数量',width:100}"),
    // 	"fans_num1"=>array("title"=>"团队数量","rowCode"=>"{field:'fans_num1',title:'团队数量',width:150}"),
	   // "yongjins"=>array("title"=>"佣金总收入","rowCode"=>"{field:'yongjin',title:'佣金总收入',width:150}"),
// 	"yhq"=>array("title"=>"优惠券","rowCode"=>"{field:'yhq',title:'优惠券',width:80}"),
// 	"cost"=>array("title"=>"累计消费","rowCode"=>"{field:'cost',title:'累计消费',width:200,sort:true}"),
	"status1"=>array("title"=>"账户状态","rowCode"=>"{field:'status1',title:'账户状态',width:150}"),
	"lastLogin"=>array("title"=>"最后登录时间","rowCode"=>"{field:'lastLogin',title:'最后登录时间',width:180}")
);

foreach($arrays as $val){
    if(!$val['if_must']){
        continue;
    }
    $colum = $val['title'];
    $title = $val['detail'];
    $allRows[$colum] = array(
        'title' => $title,
        ''
    );
}
$rowsJS = "{type:'checkbox', fixed: 'left'},{field: 'status', title: '状态', width:0, sort: false,style:\"display:none;\"},{field: 'zhishangId', title: 'zhishangId', width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS.=','.$isshow['rowCode'];
}
$rowsJS .=",{fixed:'right',width:100,title:'',align:'center', toolbar: '#barDemo','title':'详情设置'}";
$level = (int)$request['level'];
$mendianId = (int)$request['mendianId'];
$keyword = $request['keyword'];
$money_start = $request['money_start'];
$money_end = $request['money_end'];
$jifen_start = $request['jifen_start'];
$jifen_end = $request['jifen_end'];
$dtTime_start = $request['dtTime_start'];
$dtTime_end = $request['dtTime_end'];
$login_start = $request['login_start'];
$login_end = $request['login_end'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['userPageNum'])?10:$_COOKIE['userPageNum'];
$levels = $db->get_results("select id,title from user_level where comId=$comId order by ordering desc,id asc");
$mendians = $db->get_results("select id,title from mendian where comId=$comId order by id asc");
$province=$db->get_results("select * from demo_area where parentId=0 order by id asc");
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
	</style>
</head>
<body>
	<div class="right_up">
		<img src="images/biao_m.png"/> 会员列表
	</div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
			<div class="splist_up">
				<div class="splist_up_01">
					<div class="splist_up_01_left">
						<div class="splist_up_01_left_01" style="display:none;">
							<div class="splist_up_01_left_02_up">
								<span>全部等级</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down">
								<ul>
									<li>
										<a href="javascript:" onclick="selectLevel(0,'全部等级');" class="splist_up_01_left_02_down_on">全部等级</a>
									</li>
									<? if(!empty($levels)){
										foreach ($levels as $l) {
											?><li>
												<a href="javascript:" onclick="selectLevel(<?=$l->id?>,'<?=$l->title?>');"><?=$l->title?></a>
											</li><?
										}
									}?>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02">
							<div class="splist_up_01_left_02_up">
								<span>全部省份</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down" style="height:300px;overflow-y:auto;">
								<ul id="provincedata">
									<li>
										<a href="javascript:" onclick="selectProvince(0,'全部省份');" class="splist_up_01_left_02_down_on">全部省份</a>
									</li>
									<? if(!empty($province)){
										foreach ($province as $m) {
											?><li>
												<a href="javascript:" onclick="selectProvince(<?=$m->id?>,'<?=$m->title?>');"><?=$m->title?></a>
											</li><?
										}
									}?>
								</ul>
							</div>
						</div>
						<div class="splist_up_01_left_02" style="margin-left:10px;">
							<div class="splist_up_01_left_02_up">
								<span>全部城市</span> <img src="images/biao_20.png"/>
							</div>
							<div class="splist_up_01_left_02_down" style="height:300px;overflow-y:auto;">
								<ul id="citydata">
									<li>
										<a href="javascript:" onclick="selectCity(0,'全部城市');" class="splist_up_01_left_02_down_on">全部城市</a>
									</li>
								</ul>
							</div>
						</div>
						<div class="clearBoth"></div>
					</div>
					<div class="splist_up_01_right">	
						<div class="splist_up_01_right_1">
							<div class="splist_up_01_right_1_left">
								<input type="text" id="keyword" value="<?=$keyword?>" placeholder="请输入姓名/手机号"/>
							</div>
							<div class="splist_up_01_right_1_right">
								<a href="javascript:" onclick="reloadTable(0);"><img src="images/biao_21.gif"/></a>
							</div>
							<div class="clearBoth"></div>
						</div>
						<div class="splist_up_01_right_2" style="display:none;">
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
														余额区间
													</div>
													<div class="gaojisousuo_right">
														<div class="huiyuanlist_gjss_yue">
															<input type="number" name="super_money_start" step="1" value="<?=$money_start?>"> - <input type="number" name="super_money_end" step="1" value="<?=$money_end?>">
														</div>
													</div>
													<div class="gaojisousuo_left">
														积分区间
													</div>
													<div class="gaojisousuo_right">
														<div class="huiyuanlist_gjss_yue">
															<input type="number" name="super_jifen_start" step="1" value="<?=$jifen_start?>"> - <input type="number" name="super_jifen_end" step="1" value="<?=$jifen_end?>">
														</div>
													</div>
													<div class="clearBoth"></div>
												</li>
												<li>
													<div class="gaojisousuo_left">
														注册时间
													</div>
													<div class="gaojisousuo_right">
														<div class="huiyuanlist_gjss_yue">
															<input type="text" readonly="true" id="time1" name="super_dtTime_start" value="<?=$dtTime_start?>"> - <input type="text" id="time2" readonly="true" name="super_dtTime_end" step="1" value="<?=$dtTime_end?>">
														</div>
													</div>
													<div class="gaojisousuo_left">
														最近登录
													</div>
													<div class="gaojisousuo_right">
														<div class="huiyuanlist_gjss_yue">
															<input type="text" readonly="true" id="time3" name="super_login_start" value="<?=$login_start?>"> - <input type="text" id="time4" readonly="true" name="super_login_end" step="1" value="<?=$login_end?>">
														</div>
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
						<div class="splist_up_01_right_3">
						    <? chekurl($arr,'<a href="?m=system&s=users&a=daochu" id="daochuA" target="_blank" onclick="daochu();" class="splist_daochu">导 出</a>') ?>
							<!--<a href="?m=system&s=users&a=daoru" class="splist_daoru">导 入</a>-->
							<? chekurl($arr,'<a href="?m=system&s=users&a=create" class="splist_add">新 增</a>') ?>
							
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
				<div class="yuandian_xx" id="operate_row" data-id="0">
					<ul>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=users&a=basic" onclick="view()"><img src="images/biao_30.png"> 详情</a></li>') ?>
						<? chekurl($arr,'<li id="operate_jinyong"><a href="javascript:" _href="?m=system&s=users&a=jinyong" onclick="z_confirm(\'确定要禁用该会员吗？\',jin_user,\'\')"><img src="images/biao_120.png"> 禁用</a></li>') ?>
						<? chekurl($arr,'<li id="operate_jinyong"><a href="javascript:" _href="?m=system&s=users&a=qiyong" onclick="z_confirm(\'确定要启用该会员吗？\',qiyong_user,\'\')"><img src="images/biao_888.png"> 启用</a></li>') ?>
						<? chekurl($arr,'<li><a href="javascript:" _href="?m=system&s=users&a=delete" onclick="z_confirm(\'会员删除后无法找回，确定要删除吗？\',del_user,\'\')"><img src="images/biao_32.png"> 删除</a></li>') ?>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="level" value="<?=$level?>">
	<input type="hidden" id="mendianId" value="<?=$mendianId?>">
	<input type="hidden" id="money_start" value="<?=$money_start?>">
	<input type="hidden" id="money_end" value="<?=$money_end?>">
	<input type="hidden" id="jifen_start" value="<?=$jifen_start?>">
	<input type="hidden" id="jifen_end" value="<?=$jifen_end?>">
	<input type="hidden" id="dtTime_start" value="<?=$dtTime_start?>">
	<input type="hidden" id="dtTime_end" value="<?=$dtTime_end?>">
	<input type="hidden" id="login_start" value="<?=$login_start?>">
	<input type="hidden" id="login_end" value="<?=$login_end?>">
	<input type="hidden" id="order1" value="<?=$order1?>">
	<input type="hidden" id="order2" value="<?=$order2?>">
	<input type="hidden" id="page" value="<?=$page?>">
	<input type="hidden" id="selectedIds" value="">
	<input type="hidden" id="province" value="0">
	<input type="hidden" id="city" value="0">
	<script type="text/javascript">
		var productListTalbe;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,load = layer.load()
		  ,active = {
		  	setTags:function(){
				layer.open({
					type: 1
					,title: false
					,closeBtn: false
					,area: '530px;'
					,shade: 0.3
					,id: 'LAY_layuipro'
					,btn: ['提交', '取消']
					,yes: function(index, layero){
						return false;
					}
					,btnAlign: 'r'
					,content: '<div class="spxx_shanchu_tanchu" style="display: block;">'+
					'<form action="#" class="layui-form" method="post" id="setTagsForm"><div class="spxx_shanchu_tanchu_01">'+
					'<div class="spxx_shanchu_tanchu_01_left">'+'发放抵扣金'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_01_right">'+
					'<a href="javascript:layer.closeAll();"><img src="images/biao_47.png"></a>'+
					'</div>'+
					'<div class="clearBoth"></div>'+
					'</div>'+
					'<div class="spxx_shanchu_tanchu_02">'+
					'<div class="jiliang_tanchu">'+
					'<div style="float:left;width:80px;line-height:42px;">发放金额：</div><div style="float:left;width:350px;line-height:40px;">'+
					'<input type="number" id="money" style="width:60px;height:25px;padding-left:5px;">'+
					'</div>'+
					'</div>'+
					'</form></div>'
					,success: function(layero){
						form.render('checkbox');
						var btn = layero.find('.layui-layer-btn');
						btn.find('.layui-layer-btn0').attr({
							href: 'javascript:setAllTags();'
						});
						return false;
					}
				});
			}
		  }
		  $("#setTags").click(function(){
		  	active['setTags'].call(this);
		  });
		  laydate.render({
            elem: '#time1'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($dtTime_start)?'':",value:'$dtTime_start'"?>
            ,done: function(value, date, endDate){
                $("#time1").html(value);
            }
          });
          laydate.render({
            elem: '#time2'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($dtTime_end)?'':",value:'$dtTime_end'"?>
            ,done: function(value, date, endDate){
                $("#time2").html(value);
            }
          });
          laydate.render({
            elem: '#time3'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($login_start)?'':",value:'$login_start'"?>
            ,done: function(value, date, endDate){
                $("#time3").html(value);
            }
          });
          laydate.render({
            elem: '#time4'
            ,min: '2018-01-01'
            ,max: '<?=date("Y-m-d")?>'
            <?=empty($login_end)?'':",value:'$login_end'"?>
            ,done: function(value, date, endDate){
                $("#time4").html(value);
            }
          });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-140"
		    ,url: '?m=system&s=users&a=getList'
		    ,page: {curr:<?=$page?>}
		    ,limit:<?=$limit?>
		    ,cols: [[<?=$rowsJS?>]]
		    ,where:{
		    	level:'<?=$level?>',
		    	mendianId:'<?=$mendianId?>',
		    	keyword:'<?=$keyword?>',
		    	money_start:'<?=$money_start?>',
		    	money_end:'<?=$money_end?>',
		    	jifen_start:'<?=$jifen_start?>',
		    	jifen_end:'<?=$jifen_end?>',
		    	dtTime_start:'<?=$dtTime_start?>',
		    	dtTime_end:'<?=$dtTime_end?>',
		    	login_start:'<?=$login_start?>',
		    	login_end:'<?=$login_end?>'
		    },done: function(res, curr, count){
		    	$("th[data-field='status']").hide();
		    	$("th[data-field='zhishangId']").hide();
		    	layer.closeAll('loading');
			    $("#page").val(curr);
			  }
		  });
		  table.on('sort(product_list)', function(obj){
		  	var level = $("#level").val();
		  	var mendianId = $("#mendianId").val();
		  	var keyword = $("#keyword").val();
		  	var money_start = $("#money_start").val();
		  	var money_end = $("#money_end").val();
		  	var jifen_start = $("#jifen_start").val();
		  	var jifen_end = $("#jifen_end").val();
		  	var dtTime_start = $("#dtTime_start").val();
		  	var dtTime_end = $("#dtTime_end").val();
		  	var login_start = $("#login_start").val();
		  	var login_end = $("#login_end").val();
		  	$("#order1").val(obj.field);
		  	$("#order2").val(obj.type);
		  	var scrollLeft = $(".layui-table-body").scrollLeft();
		  	layer.load();
			table.reload('product_list', {
			    initSort: obj
			    ,height: "full-140"
			    ,where: {
			      order1: obj.field
			      ,order2: obj.type
			      ,level:level,
			      mendianId:mendianId,
			      keyword:keyword,
			      money_start:money_start,
			      money_end:money_end,
			      jifen_start:jifen_start,
			      jifen_end:jifen_end,
			      dtTime_start:dtTime_start,
			      dtTime_end:dtTime_end,
			      login_start:login_start,
			      login_end:login_end
			    },page: {
					curr: 1
				},done:function(){
					$(".layui-table-header").scrollLeft(scrollLeft);
					$(".layui-table-body").scrollLeft(scrollLeft);
					$("th[data-field='status']").hide();
					$("th[data-field='zhishangId']").hide();
					layer.closeAll('loading');
				}
			  });
		  });
		  form.on('submit(search)', function(data){
		  	$("#money_start").val(data.field.super_money_start);
		  	$("#money_end").val(data.field.super_money_end);
		  	$("#jifen_start").val(data.field.super_jifen_start);
		  	$("#jifen_end").val(data.field.super_jifen_end);
		  	$("#dtTime_start").val(data.field.super_dtTime_start);
		  	$("#dtTime_end").val(data.field.super_dtTime_end);
		  	$("#login_start").val(data.field.super_login_start);
		  	$("#login_end").val(data.field.super_login_end);
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
		    			ids = data[i].zhishangId;
		    		}else{
		    			ids = ids+','+data[i].zhishangId;
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
	</script>
	<script type="text/javascript" src="js/users.js?v=1.1"></script>
	<div id="bg" onclick="hideRowset();"></div>
	<? require('views/help.html');?>
</body>
</html>