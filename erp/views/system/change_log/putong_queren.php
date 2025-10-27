<?
global $db,$request,$adminRole,$qx_arry;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
$fenbiao = getFenbiao($comId,20);
$allRows = array(
	"orderId"=>array("title"=>"导入编号","rowCode"=>"{field:'orderId',title:'导入编号',width:258}"),
	"dtTime"=>array("title"=>"导入时间","rowCode"=>"{field:'dtTime',title:'导入时间',width:175}"),
	"num"=>array("title"=>"导入数量","rowCode"=>"{field:'num',title:'导入数量',width:75}"),
	"realNum"=>array("title"=>"实际导入数量","rowCode"=>"{field:'realNum',title:'实际导入数量',width:100}"),
	"faliNum"=>array("title"=>"失败数量","rowCode"=>"{field:'faliNum',title:'失败数量',width:85}"),
	"username"=>array("title"=>"操作人","rowCode"=>"{field:'username',title:'操作人',width:138}"),
	"zhuangtai"=>array("title"=>"导入状态","rowCode"=>"{field:'zhuangtai',title:'导入状态',width:188}")
);
$rowsJS = "{field: 'id', title: 'id', width:0,style:\"display:none;\"},{field:'status',title:'status',width:0,style:\"display:none;\"}";
foreach ($allRows as $row=>$isshow){
	$rowsJS .= ','.$isshow['rowCode'];
}
//0当前订单 1.未打印 2.已打印
$startTime = $request['startTime'];
$endTime = $request['endTime'];
$username = $request['username'];
$order1 = empty($request['order1'])?'id':$request['order1'];
$order2 = empty($request['order2'])?'desc':$request['order2'];
$page = empty($request['page'])?1:$request['page'];
$limit = empty($_COOKIE['orderPageNum'])?10:$_COOKIE['orderPageNum'];
//计算各类型订单的数量
/*$num_sql = "select count(*) as num from fahuo_pici$fenbiao where comId=$comId and type=1";
$title = '导入';
$zongNum = 0;
$nums = $db->get_results($num_sql);*/
// var_dump($arr);die;
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
<? require('views/system/change_log/header.php')?>
<div id="content">
	<div class="content1">
    	<div class="content_1">
        	普通快递导入（<span style="color: red">单次最多导入100条</span>）
        </div>
    	<div class="content_2">
        	<div class="fhqr_ptkuaididaoru">
            	<div class="fhqr_ptkuaididaoru_up">
                	<div class="sprukulist_01" style="float: left;margin: 0 10px;top: 0px;">
                    	<div class="sprukulist_01_left">
                        	<span id="s_time1"><?=empty($startTime)?'选择日期':$startTime?></span> <span>~</span> <span id="s_time2"><?=empty($endTime)?'选择日期':$endTime?></span>
                        </div>
                    	<div class="sprukulist_01_right">
                        	<img src="images/biao_76.png"/>
                        </div>
                    	<div class="clearBoth"></div>
                    	<div id="riqilan" style="position:absolute;top:35px;width:550px;height:330px;display:none;left:-1px;z-index: 99">
                    		<div id="riqi1" style="float:left;width:272px;"></div><div id="riqi2" style="float:left;width:272px;"></div>
                    	</div>
                    </div>
                	<div class="dianzimiandan_21_01">
                        <div class="dianzimiandan_21_01_1" style="padding-top:5px;">
                            操作人：
                        </div>
                        <div class="dianzimiandan_21_01_2">
                            <input type="text" class="layui-input" name="username" id="username">
                        </div>
                        <div class="clearBoth"></div>
                    </div>
                    <div class="dianzimiandan_21_02" style="padding-top:5px;">
                        <a href="javascript:;" id="shaixuan">立即筛选</a>
                    </div>
                	<div class="fhqr_ptkuaididaoru_up_4">
                		<!--<a href="images/help.xls" target="_blank"> 下载示例文件</a>-->
                		<form id="forms" method="post" action="" style="display: inline-block;">
                		    <?if(strstr($arr, '?m=system&s=change_log&a=daoru_order')){ ?>
                    		    <a href="javascript:;" id="uploadFile"> 导入物流</a>
                    		<? } ?>
                    	</form>
                    </div>
                	<div class="clearBoth"></div>
                </div>
            	<div class="fhqr_ptkuaididaoru_down">
                	<table id="product_list" lay-filter="product_list">
					</table>
                </div>
            </div>
        </div>
    </div>
</div>
<!--是否打印-->
<div class="print_tc" style="display:none;">
    <div class="bj">
    </div>
    <div class="damx_genghuanwuliu">
        <div class="damx_genghuanwuliu_1">
            <div class="damx_genghuanwuliu_1_left">
                导入详情
            </div>
            <div class="damx_genghuanwuliu_1_right" onclick="$('.print_tc').hide();">
                <img src="images/miandan_13.png"/>
            </div>
            <div class="clearBoth"></div>
        </div>
        <div class="damx_genghuanwuliu_2" style="text-align: center;">
            <form id="pandianForm" action="?m=system&s=fahuo&a=daochuOrder" method="post" target="_blank">
    			<input type="hidden" name="pandianJsonData" id="pandianJsonData" value='<?=$pandianJsonData?>'>
    		</form>
            <div class="damx_genghuanwuliu_2_2" id="shuju">
                实际导入发货单0条，0个导入失败！
            </div>
        </div>
        <div class="damx_genghuanwuliu_3">
            <a href="javascript:$('#pandianForm').submit();">下载</a>
        </div>
    </div>
</div>
<input type="hidden" id="userId" value="<?=$userId?>">
<input type="hidden" id="startTime" value="<?=$startTime?>">
<input type="hidden" id="endTime" value="<?=$endTime?>">
<input type="hidden" id="order1" value="<?=$order1?>">
<input type="hidden" id="order2" value="<?=$order2?>">
<input type="hidden" id="page" value="<?=$page?>">
<input type="hidden" value="filepath" id="filepath" value="">
<script type="text/javascript">
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
	  		$("#startTime").val(value);
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
				$("#endTime").val(value);
			}
	  });
	  $(".laydate-btns-confirm").click(function(){
	  	$("#riqilan").slideUp(200);
	  });
	  productListTalbe = table.render({
	    elem: '#product_list'
	    ,height: "full-200"
	    ,url: '?s=change_log&a=get_daoru_list'
	    ,page: {curr:<?=$page?>}
	    ,limit:<?=$limit?>
	    ,cols: [[<?=$rowsJS?>]]
	    ,where:{
	    	userId:'<?=$userId?>',
	    	startTime:'<?=$startTime?>',
	    	endTime:'<?=$endTime?>',
	    	username:'<?=$username?>'
	    },done: function(res, curr, count){
	    	layer.closeAll('loading');
		    $("#page").val(curr);
		    $("th[data-field='id']").hide();
		    $("th[data-field='status']").hide();
		  }
	  });
	  $("#shaixuan").click(function(){
	  	$("#riqilan").slideUp(200);
	  	reloadTable(1);
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
	});
function xiaoshi(){
	$(".print_tc").hide();
}
</script>
<script type="text/javascript" src="js/change_log/daoru_list.js"></script>
</body>
</html>