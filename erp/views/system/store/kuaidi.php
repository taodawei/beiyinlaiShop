<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<title><? echo SITENAME;?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="renderer" content="webkit" />
	<link href="styles/common.css" rel="stylesheet" type="text/css">
	<link href="styles/index.css" rel="stylesheet" type="text/css">
	<link href="styles/kucunpandian.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
	<style>
		.layui-table-body tr{height:50px}
		.layui-table-view{margin:10px;}
		td[data-field="title"] div,td[data-field="sn"] div,td[data-field="address"] div{height:auto;line-height:20px;white-space:normal;word-break:break-all;max-height:60px;overflow:hidden;}
		td[data-field="image"] div{height:auto;text-align:center;}
		td[data-field="image"] img{border:#abd3e7 1px solid}
		.cangkugl_xiugai_02_right .layui-form-select input{width:100%;}
		.cangkugl_xiugai_02_right input {
			width: 283px;
		}
		.cangkugl_xiugai_02 ul li {
			padding-bottom: 17px;
			width: 49%;margin-right:.4;
			float: left;
		}
	</style>
</head>
<?
$storeId = (int)$request['storeId'];
$storeName = $db->get_var("select title from demo_kucun_store where id=$storeId");
?>
<body>
	<div class="cangkuguanli_1">
    	<div class="cangkuguanli_1_left">
        	<img src="images/biao_87.png"/> <?=$storeName?>-快递管理
        </div>
        <div class="cangkuguanli_1_right">
            <a href="javascript:" onclick="edit_store(0);"> <b>+</b> 新 增</a>
        </div>
    	<div class="clearBoth"></div>
    </div>
	<div class="right_down" style="padding-bottom:0px;">
		<div class="splist">
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
						<li>
							<a href="javascript:edit_store(1);"><img src="images/biao_31.png"> 编辑</a>
						</li>
                        <li>
                            <a href="javascript:del_store(1);"><img src="images/biao_32.png"> 删除</a>
                        </li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="cangkugl_xiugai" id="cangkugl_xiugai">
    	<div class="cangkugl_xiugai_01">
        	新增快递
        </div>
        <form id="editForm" action="?m=system&s=store&a=editKuaidi" method="post" class="layui-form">
        	<input type="hidden" name="id" id="storeId">
        	<div class="cangkugl_xiugai_02">
        		<ul>
                    <li>
                        <div class="cangkugl_xiugai_02_left">
                           <span>*</span>EBusinessID
                        </div>
                        <div class="cangkugl_xiugai_02_right">
                            <input id="EBusinessID" name="EBusinessID" class="layui-input" placeholder="快递鸟EBusinessID" lay-verify="required" type="text"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cangkugl_xiugai_02_left">
                           <span>*</span> AppKey
                        </div>
                        <div class="cangkugl_xiugai_02_right">
                            <input id="AppKey" name="AppKey" placeholder="快递鸟AppKey" class="layui-input" lay-verify="required" type="text"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 选择快递
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<select name="kuaidi_company" id="kuaidi_company" lay-filter="company" lay-verify="required">
                                <option value="">请选择</option>
                                <option value="SF">顺丰</option>
                                <option value="EMS">EMS</option>
                                <option value="ZJS">宅急送</option>
                                <option value="YTO">圆通</option>
                                <option value="HTKY">百世快递</option>
                                <option value="ZTO">中通</option>
                                <option value="YD">韵达</option>
                                <option value="STO">申通</option>
                                <option value="HHTT">天天快递</option>
                                <option value="YZPY">邮政快递包裹</option>
                                <option value="DBL">德邦</option>
                                <option value="UC">优速</option>
                                <option value="XFEX">信丰</option>
                                <option value="QFKD">全峰</option>
                                <option value="KYSY">跨越速运</option>
                                <option value="ANE">安能小包</option>
                                <option value="FAST">快捷快递</option>
                                <option value="GTO">国通</option>
                                <option value="ZTKY">中铁快运</option>
                                <option value="YZBK">邮政国内标快</option>
                            </select>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					客户号
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="CustomerName" name="CustomerName" class="layui-input" placeholder="部分快递需要" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<div class="clearBoth"></div>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					客户密码
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="CustomerPwd" name="CustomerPwd" placeholder="部分快递需要" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					月结号
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="MonthCode" name="MonthCode" placeholder="部分快递需要" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
                    <li>
                        <div class="cangkugl_xiugai_02_left">
                            所属网点
                        </div>
                        <div class="cangkugl_xiugai_02_right">
                            <input id="SendSite" name="SendSite" placeholder="部分快递需要" class="layui-input" type="text"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cangkugl_xiugai_02_left">
                            <span>*</span> 发件人姓名
                        </div>
                        <div class="cangkugl_xiugai_02_right">
                            <input id="fahuo_user" name="fahuo_user" placeholder="发件人姓名" lay-verify="required" class="layui-input" type="text"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cangkugl_xiugai_02_left">
                            <span>*</span> 发件人电话
                        </div>
                        <div class="cangkugl_xiugai_02_right">
                            <input id="fahuo_phone" name="fahuo_phone" placeholder="发件人手机号" lay-verify="required" class="layui-input" type="text"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="cangkugl_xiugai_02_left">
                            <span>*</span> 打印机名称
                        </div>
                        <div class="cangkugl_xiugai_02_right">
                            <input id="print_name" name="print_name" placeholder="“设置-控制面板-设备和打印机”中查看" lay-verify="required" class="layui-input" type="text"/>
                        </div>
                        <div class="clearBoth"></div>
                    </li>
        			<div class="clearBoth"></div>
        		</ul>
        	</div>
        	<div class="cangkugl_xiugai_03">
        		<button class="layui-btn" lay-submit="" lay-filter="tijiao">提 交</button>
        		<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
        	</div>
        	<input type="hidden" name="kuaidiId" id="kuaidiId" value="0">
            <input type="hidden" name="storeId" id="storeId" value="<?=$storeId?>">
            <input type="hidden" name="kuaidi_title" id="kuaidi_title" value="">
        </form>
    </div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="page" value="<?=$page?>">
	<script type="text/javascript">
		var productListTalbe;
		var productListForm;
        var storeId = <?=$storeId?>;
		layui.use(['laypage','table','form'], function(){
		  var laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form;
		  productListForm = form;
        form.on('select(company)',function(data){
            $("#kuaidi_title").val($("#kuaidi_company option:selected").text());
        });
        productListTalbe = table.render({
        	elem: '#product_list'
        	,height: "full-80"
        	,url: '?m=system&s=store&a=getKuaidiList&storeId='+storeId
        	,page: true
        	,cols: [[{field: 'id', title: 'id', width:0, sort: true,style:"display:none;"},{field:'kuaidi_company', title: 'kuaidi_company', width:0,style:"display:none;"},{field:'EBusinessID', title: 'EBusinessID', width:150},{field:'AppKey', title: 'AppKey', width:150},{field:'kuaidi_title', title: '快递公司', width:150},{field:'CustomerName',title:'客户号',width:150},{field:'CustomerPwd',title:'客户密码',width:100},{field:'MonthCode', title: '月结号', width:100},{field:'SendSite', title: '所属网点', width:200},{field:'fahuo_user', title: '发件人', width:100},{field:'fahuo_phone', title: '发件人电话', width:150},{field:'print_name', title: '打印机名称', width:200},{fixed:'right',title:'',align:'center', toolbar: '#barDemo'}]]

        	,done: function(res, curr, count){
                $("th[data-field='id']").hide();
                $("th[data-field='kuaidi_company']").hide();
        		$("#page").val(curr);
        	}
        });
        
        form.on('submit(tijiao)', function(data){
        	layer.msg('库存数据生成中，请耐心等待', {
        		icon: 16
        		,shade:[0.1,'#000']
        		,time:30000
        	});
        });
    });
	</script>
	<script type="text/javascript" src="js/shezhi/kuaidi.js?v=1"></script>
	<div id="bg"></div>
</body>
</html>