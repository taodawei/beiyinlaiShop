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
	<script type="text/javascript" src="https://api.map.baidu.com/api?v=2.0&ak=cIUKusewZaKmqALQv6lKtIcY&s=1"></script>
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
        .yuandian_xx {width:85px}
	</style>
</head>
<?
$_SESSION['tijiao'] = 1;
$areas = $db->get_results("select * from demo_area where parentId=0");
?>
<body>
	<div class="cangkuguanli_1">
    	<div class="cangkuguanli_1_left">
        	<img src="images/biao_87.png"/> 仓库管理
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
							<a href="javascript:jin_store();"><img src="images/biao_88.png"> 禁用</a>
						</li>
						<li>
							<a href="javascript:del_store();"><img src="images/biao_32.png"> 删除</a>
						</li>
                        <li>
                            <a href="javascript:edit_kuaidi();"><img src="images/biao_31.png"> 电子面单</a>
                        </li>
                        <li>
                            <a href="javascript:edit_print();"><img src="images/biao_31.png"> 打印机设置</a>
                        </li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="cangkugl_xiugai" id="cangkugl_xiugai">
    	<div class="cangkugl_xiugai_01">
        	新增仓库
        </div>
        <form id="editForm" action="?m=system&s=store&a=editStore" method="post" class="layui-form">
        	<input type="hidden" name="id" id="storeId">
        	<div class="cangkugl_xiugai_02">
        		<ul>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 仓库名称
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_title" name="title" lay-verify="required" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					<span>*</span> 仓库编码
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_sn" name="sn" lay-verify="required" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<div class="clearBoth"></div>
        			<!-- <li>
        				<div class="cangkugl_xiugai_02_left">
        					发货人姓名
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_name" name="name" placeholder="用于电子面单发货" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					发货人电话
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_phone" name="phone" placeholder="用于电子面单发货" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li> -->
        			<div class="clearBoth"></div>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					 仓库所在地
        				</div>
        				<div class="cangkugl_xiugai_02_right" style="width:365px;">
        					<div style="width:32%;display:inline-block;">
        						<select id="ps1" lay-filter="ps1">
        							<option value="">选择省份</option>
        							<?if(!empty($areas)){
        								foreach ($areas as $hangye) {
        									?><option value="<?=$hangye->id?>"><?=$hangye->title?></option><?
        								}
        							}?>
        						</select>
        					</div>
        					<div style="width:32%;display:inline-block;">
        						<select id="ps2" lay-filter="ps2"><option value="">请先选择省</option>
        						</select>
        					</div>
        					<div style="width:32%;display:inline-block;">
        						<select id="ps3" lay-filter="ps3"><option value="">请先选择市</option>
        						</select>
        					</div>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<li>
        				<div class="cangkugl_xiugai_02_left">
        					仓库地址
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input id="e_address" name="address" onchange="searchMap();" class="layui-input" type="text"/>
        				</div>
        				<div class="clearBoth"></div>
        			</li>
        			<div class="clearBoth"></div>
        			<li style="width: 100%">
        				<div class="cangkugl_xiugai_02_left">
        					仓库坐标
        				</div>
        				<div class="cangkugl_xiugai_02_right">
        					<input type="text" id="TextBox1" class="layui-input" readonly="true" name="hengzuobiao" style="width:150px;display:inline-block;" />
        						<input type="text" id="TextBox2" name="zongzuobiao" readonly="true" class="layui-input" style="width:150px;display:inline-block;" /><span class="add_cont_zhuyi">点击地图标注签到详细坐标位置</span>
        					<div style="width:500px;height:200px;border:#ccc solid 1px;margin:0px;margin-top:10px;" id="container"></div>
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
        	<input type="hidden" name="areaId" id="psarea" value="0">
        </form>
    </div>
	<input type="hidden" id="nowIndex" value="">
	<input type="hidden" id="page" value="<?=$page?>">
	<script type="text/javascript">
		var productListTalbe;
		var productListForm;
		layui.use(['laypage','table','form'], function(){
		  var laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form;
		  productListForm = form;
		  form.on('select(ps1)',function(data){
            if(!isNaN(data.value)){
                layer.load();
                id = data.value;
                ajaxpost=$.ajax({
                    type:"POST",
                    url:"/erp_service.php?action=getAreas",
                    data:"id="+id,
                    timeout:"4000",
                    dataType:"text",
                    success: function(html){
                        $("#ps3").html('<option value="">请先选择市</option>');
                        if(html!=""){
                            $("#ps2").html(html);
                            $("#psarea").val(id);
                        }else{
                            $("#psarea").val(id);
                        }
                        form.render('select');
                        layer.closeAll('loading');
                    },
                    error:function(){
                        alert("超时,请重试");
                    }
                });
            }            
        });
        form.on('select(ps2)',function(data){
            if(!isNaN(data.value)){
                layer.load();
                id = data.value;
                ajaxpost=$.ajax({
                    type:"POST",
                    url:"/erp_service.php?action=getAreas",
                    data:"id="+id,
                    timeout:"4000",
                    dataType:"text",
                    success: function(html){
                        if(html!=""){
                            $("#ps3").html(html);
                            $("#psarea").val(id);
                        }else{
                            $("#psarea").val(id);
                        }
                        form.render('select');
                        layer.closeAll('loading');
                    },
                    error:function(){
                        alert("超时,请重试");
                    }
                });
            }
        });
        form.on('select(ps3)',function(data){
            if(!isNaN(data.value)){
                $("#psarea").val(data.value);
            }
        });
        productListTalbe = table.render({
        	elem: '#product_list'
        	,height: "full-80"
        	,url: '?m=system&s=store&a=getList'
        	,page: true
        	,cols: [[{field: 'id', title: 'id', width:0, sort: true,style:"display:none;"},{field: 'areaId', title: 'areaId', width:0, sort: true,style:"display:none;"},{field:'position', title: '坐标', width:0, sort: true,style:"display:none;"},{field:'name', title: 'name', width:0, sort: true,style:"display:none;"},{field:'phone', title: 'phone', width:0, sort: true,style:"display:none;"},{field:'title',title:'仓库名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'sn',title:'仓库编码',width:200},{field:'address',title:'仓库地址',width:450,style:"height:auto;line-height:22px;white-space:normal;"},{field:'status',title:'状态',width:187},{fixed:'right',title:'',align:'center', toolbar: '#barDemo'}]]

        	,done: function(res, curr, count){
        		$("#page").val(curr);
        	}
        });
        $("th[data-field='id']").hide();
        $("th[data-field='position']").hide();
        $("th[data-field='areaId']").hide();
        $("th[data-field='name']").hide();
        $("th[data-field='phone']").hide();
        form.on('submit(tijiao)', function(data){
        	layer.msg('库存数据生成中，请耐心等待', {
        		icon: 16
        		,shade:[0.1,'#000']
        		,time:30000
        	});
        });
    });
    function edit_print(){
        location.href='?s=mendian_set&a=dayin&storeId='+getPdtId();
    }
	</script>
	<script type="text/javascript" src="js/store.js"></script>
	<div id="bg"></div>
    <? require('views/help.html');?>
</body>
</html>