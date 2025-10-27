<?
global $db,$request;
$comId = (int)$_SESSION[TB_PREFIX.'comId'];
if(is_file("../cache/product_set_$comId.php")){
	$product_set = json_decode(file_get_contents("../cache/product_set_$comId.php"));
}else{
	$product_set = $db->get_row("select * from demo_product_set where comId=$comId");
}
if(is_file("../cache/kucun_set_$comId.php")){
	$kucun_set = json_decode(file_get_contents("../cache/kucun_set_$comId.php"));
}else{
	$kucun_set = $db->get_row("select * from demo_kucun_set where comId=$comId");
}
$step = 1;$price_xiaoshu = 100;
if($product_set->number_num>0){
    $chushu = pow(10,$product_set->number_num);
    $step = 1/$chushu;
}
if($product_set->price_num>0){
  $price_xiaoshu = pow(10,$product_set->price_num);
}
$orderInt = $db->get_var("select orderInt from demo_caigou where comId=$comId order by id desc limit 1");
$orderInt = buling($orderInt+1,6);
$orderId = $kucun_set->caigou_pre.'_'.date("Ymd").'_'.$orderInt;
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
$suppliers = $db->get_results("select id,title from demo_supplier where comId=$comId and status=1 order by id desc");
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
	<style type="text/css">
		.sprukuadd_04 ul li{display:inline-block;width:523px;margin-right:20px;}
		.add_other,.add_pay{float:right}
		.add_other{padding:17px 15px 5px 0;font-size:13px;color:#6a6a6a;line-height:34px}
		.add_other div{padding-bottom:5px}
	</style>
</head>
<body>
	<div class="right_up">
    	<a href="javascript:history.go(-1);"><img src="images/back.gif"/></a> 新增采购订单
    </div>
	<div class="right_down">
    	<div class="sprukuadd">
    		<form id="addForm" action="?m=system&s=caigou&a=add&tijiao=1" method="post" class="layui-form">
			<div class="add_choice_1" style="margin-bottom:20px;">
				<input type="radio" name="ifsupplier" value="1" lay-filter="gys1" title="从固定供应商采购" <? if(empty($suppliers)){?>disabled<? }else{?>checked<? }?> /><input type="radio" name="ifsupplier" lay-filter="gys2" value="0" <? if(empty($suppliers)){?>checked<? }?> title="从临时供应商采购" />
			</div>
        	<div class="sprukuadd_01">
            	<div class="sprukuadd_01_canku">
                	<div class="sprukuadd_01_canku_left">
                    	<span>*</span>供应商
                    </div>
                	<div class="sprukuadd_01_canku_right" id="ifsupplier1" <? if(empty($suppliers)){?>style="display:none;"<? }?>>
                    	<select name="supplierId" id="supplierId" lay-filter="changeStore">
                    		<option value="">选择供应商</option>
                    		<?
                    		if(!empty($suppliers)){
                    			foreach ($suppliers as $s){
                    				?><option value="<?=$s->id?>"><?=$s->title?></option><?
                    			}
                    		}
                    		?>
                    	</select>
                    </div>
                    <div class="sprukuadd_01_canku_right" style="width:460px;margin-right:0px;<? if(!empty($suppliers)){?>display:none;<? }?>" id="ifsupplier0" >
                    	<input type="text" name="supplierName" id="supplierName" style="width:280px;display:inline-block;" class="layui-input" placeholder="输入供应商名称">
                    	<div style="width:150px;display:inline-block;margin-left:25px;"><input type="checkbox" name="ifAddSupp" value="1" lay-skin="primary" title="设为固定供应商"></div>
                    </div>
                	<div class="clearBoth"></div>
                </div>
                <div class="sprukuadd_01_riqi" style="position:relative;top:-5px;">
                	<input type="checkbox" name="ifjiaji" value="1" lay-skin="primary" title="<font color='#ff5d5d'>紧急采购</font>">
                	<div class="clearBoth"></div>
                </div>
                <div class="clearBoth"></div>
            </div>
        	<div class="sprukuadd_03">
            	<table width="100%" border="0" cellpadding="0" cellspacing="0" id="dataTable" rows="1">
                	<tr height="43">
                    	<td bgcolor="#7bc8ed" width="70" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	
                        </td>
                        <td bgcolor="#7bc8ed" width="118" class="sprukuadd_03_title" align="center" valign="middle"> 
                        </td>
                        <td bgcolor="#7bc8ed" width="166" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	商品编码
                        </td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">                         
                        	商品名称
                        </td>
                        <td bgcolor="#7bc8ed" width="300" class="sprukuadd_03_title" align="center" valign="middle">
                        	规格 
                        </td>
                        <td bgcolor="#7bc8ed" width="100" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	单位
                        </td>
                        <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	采购数
                        </td>
                        <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	采购单价（元）
                        </td>
                        <td bgcolor="#7bc8ed" width="276" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	小计（元）
                        </td>
                    </tr>
                    <tr height="48" id="rowTr1">
                    	<td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"> 
                        	1
                        </td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">
                        	<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow(1);"><img src="images/biao_66.png"/></a>  
                        </td>
                        <td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle">                         
                        	<div class="sprukuadd_03_tt_addsp">
                            	<div class="sprukuadd_03_tt_addsp_left">
                                	<input type="text" class="layui-input addRowtr" id="searchInput1" row="1" placeholder="输入编码/商品名称" >
                                </div>
                            	<div class="sprukuadd_03_tt_addsp_right" onclick="showAllpdts();">
                                	●●●
                                </div>
                            	<div class="clearBoth"></div>
                                <div class="sprukuadd_03_tt_addsp_erji" id="pdtList1">
                                	<ul>
                                		<li style="padding:20px;text-align:center;"><img src="images/loading.gif"></li>
                                	</ul>
                                </div>
                            </div>
                        </td> 
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                    </tr>
                    <tr height="48" id="rowTrHeji">
                    	<td bgcolor="#ffffff"  class="sprukuadd_03_tt" align="center" valign="middle"> 
                        	合计
                        </td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" colspan="2" class="sprukuadd_03_tt" align="center" valign="middle"> </td> 
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0</td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0.00</td>
                    </tr>
                    <tr> 
                    	<td class="add_td5" colspan="9">
                    		<div class="add_other">
                    			<div>其他金额：
                    				<input name="price_other" id="price_other" onchange="renderAllPrice();" class="layui-input" value="0.00" type="text" style="width:114px;display:inline-block;" lay-verify="required|number">			
                    			</div>
                    			<div class="clearBoth"></div>
                    			应付金额：<span style="color:#ff0000; font-size:24px; line-height:24px;" id="price_all">0.00</span>
                    		</div>
                    	</td>
                    </tr>
                </table>
                <script type="text/javascript">
                	var jishiqi;
                    $('#searchInput1').bind('input propertychange', function() {
                        clearTimeout(jishiqi);
                        var row = $(this).attr('row');
                        var val = $(this).val();
                        jishiqi=setTimeout(function(){getPdtInfo(row,val);},500);
                    });
                	$('#searchInput1').click(function(eve){
                		var supplierType = $("input[name='ifsupplier']:checked").val();
                		var supplierId = $("#supplierId option:selected").val();
                		if(supplierType==1&&supplierId==''){
                			layer.msg('请先选择供应商！',function(){});
                			return false
                		}
                		var nowRow = $(this).attr("row");
                		if($("#pdtList"+nowRow).css("display")=="none"){
                			showpdtList(nowRow,$(this).val());
                		}
                		stopPropagation(eve); 
                	});
                </script>
            </div>
        	<div class="sprukuadd_04">
            	<ul>
            		<li>
                    	<div class="sprukuadd_04_left">
                        	<span>*</span>采购单号
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input name="orderId" lay-verify="required" value="<?=$orderId?>" type="text" placeholder="您也可以自定义单号" class="layui-input">
                        </div>
                        <div class="sprukuadd_04_right">
                        	<a href="index.php?a=shezhi&url=%3Fm%3Dsystem%26s%3Dkucun_set%26a%3Dchuruku" target="_blank">设置采购单号 ></a>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	<span>*</span>采购日期
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input type="text" name="dtTime" id="dtTime" class="layui-input" value="<?=date("Y-m-d")?>" lay-verify="required">
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	采购员
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input type="text" name="caigouyuan" class="layui-input">
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	<span>*</span>采购方式
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;line-height:30px;">
                    		<input type="radio" name="price_type" lay-filter="type1" value="1" title="现购" checked>
                        	<input type="radio" name="price_type" lay-filter="type2" value="2" title="赊购">
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	<span>*</span>已付款
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input type="text" name="price_payed" id="price_payed" readonly="true" lay-verify="number" class="layui-input disabled" style="width:200px;display:inline-block;"> 元
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	<span>*</span>物流费用
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input type="text" name="price_wuliu" lay-verify="required|number" value="0.00" class="layui-input" style="width:200px;display:inline-block;"> 元
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	到货仓库
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<select name="storeId">
                                <option value="">请选择仓库</option>
	                    		<? foreach($cangkus as $cangku){
	                    			?><option value="<?=$cangku->id?>"><?=$cangku->title?></option><?
	                    		}?>
	                    	</select>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	制单人
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input type="text" placeholder="<?=$_SESSION[TB_PREFIX.'name']?>" readonly="true" class="layui-input disabled" >
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li style="width:auto;">
                    	<div class="sprukuadd_04_left">
                        	备注
                        </div>
                    	<div class="sprukuadd_04_right" style="width:640px;">
                        	<textarea name="beizhu" cols="30" rows="10" class="layui-textarea" placeholder="输入备注信息"></textarea>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
            	</ul>
            </div>
            <div class="sprukuadd_05">
            	<button class="layui-btn" lay-submit="" lay-filter="tijiao">立即提交</button>
				<button class="layui-btn layui-btn-primary" onclick="quxiao();return false;">取 消</button>
            </div>
            <input type="hidden" name="price" id="price" value="0.00">
        </form>
        </div>
    </div>
    <div class="sprkadd_xuanzesp">
    	<div class="sprkadd_xuanzesp_01">
        	<div class="sprkadd_xuanzesp_01_1">
            	选择商品
            </div>
        	<div class="sprkadd_xuanzesp_01_2" style="position:relative;">
        		<div class="splist_up_01_left_01_up" style="height:37px;line-height:37px;">
        			<span>全部分类</span> <img src="images/biao_20.png"/>
        		</div>
        		<div class="splist_up_01_left_01_down">
        			<ul style="border-left:0px" id="ziChannels1">
        				<li class="allsort_01">
        					<a href="javascript:selectChannel(0,'全部分类');">全部分类</a>
        				</li>
        				<? if(!empty($channels)){
        					foreach ($channels as $c) {
        						?>
        						<li class="allsort_01">
        							<a href="javascript:" onclick="selectChannel(<?=$c->id?>,'<?=$c->title?>');" onmouseenter="loadZiChannels(<?=$c->id?>,2,<? if(!empty($c->channels)){echo 1;}else{echo 0;}?>);" class="allsort_01_tlte"><?=$c->title?> <? if(!empty($c->channels)){?><span><img src="images/biao_24.png"/></span><? }?></a>
        						</li>
        						<?
        					}
        					?><?
        				}?>
        			</ul>
        		</div>
            </div>
        	<div class="sprkadd_xuanzesp_01_3">
            	<div class="sprkadd_xuanzesp_01_3_left">
                	<input type="text" id="keyword" placeholder="请输入商品名称/编码/规格/关键字">
                </div>
            	<div class="sprkadd_xuanzesp_01_3_right">
                	<a href="javascript:reloadTable(0);"><img src="images/biao_21.gif"></a>
                </div>
            	<div class="clearBoth"></div>
            </div>
        	<div class="clearBoth"></div>
        </div>
    	<div class="sprkadd_xuanzesp_02">
        	<table id="product_list" lay-filter="product_list">
			</table>
        </div>
    	<div class="sprkadd_xuanzesp_03">
        	<a href="javascript:" id="sprkadd_xuanzesp_03_01" class="sprkadd_xuanzesp_03_01">确  认</a><a href="javascript:hideSearch();" class="sprkadd_xuanzesp_03_02">取  消</a>
        </div>
    </div>
    <input type="hidden" id="channelId" value="<?=$channelId?>">
    <script type="text/javascript">
		var step = <?=$step?>;
        var price_xiaoshu = <?=$price_xiaoshu?>;
		var productListTalbe;
		var productListForm;
		layui.use(['laydate', 'laypage','table','form'], function(){
		  var laydate = layui.laydate
		  ,laypage = layui.laypage
		  ,table = layui.table
		  ,form = layui.form
		  ,active = {
		  	appendCheckData: function(){
		  		var checkStatus = table.checkStatus('product_list')
		  		,data = checkStatus.data;
		  		if(data.length>0){
		  			var num = parseInt($("#dataTable").attr("rows"));
		  			var rownums = $("#dataTable tr").length;
		  			$("#rowTrHeji").prev().remove();
		  			$("#dataTable tr").eq(rownums-1).remove();
			    	for (var i = 0; i < data.length; i++) {
			    		var inventoryId = data[i].id;
			    		var sn = data[i].sn;
			    		var title = data[i].title;
			    		var key_vals = data[i].key_vals;
			    		var shuliang = $("#shuliang_"+inventoryId).val();
                        var max = $("#shuliang_"+inventoryId).attr('max');
			    		var units = data[i].units;
			    		var productId = data[i].productId;
			    		var price = $("#price_"+inventoryId).val(); data[i].price;
			    		num = num+1;
			    		var str = '<tr height="48" id="rowTr'+num+'"><td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
						'<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
							'<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a> '+ 
						'</td>'+
						'<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
						'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
						'<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
						'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>'+
						'<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
							'<input type="text" lay-verify="kucun" onchange="renderPrice('+num+');" name="inventoryNum['+num+']" value="'+shuliang+'" class="sprukuadd_03_tt_input">'+
							'<input type="hidden" name="inventoryId['+num+']" value="'+inventoryId+'">'+
							'<input type="hidden" name="inventorySn['+num+']" value="'+sn+'">'+
							'<input type="hidden" name="inventoryTitle['+num+']" value="'+title+'">'+
							'<input type="hidden" name="inventoryKey_vals['+num+']" value="'+key_vals+'">'+
							'<input type="hidden" name="inventoryBeizhu['+num+']" id="beizhu'+num+'" value="">'+
							'<input type="hidden" name="inventoryPdtId['+num+']" value="'+productId+'">'+
							'<input type="hidden" name="inventoryUnits['+num+']" value="'+units+'">'+
						'</td>'+
						'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
						'<input type="text" lay-verify="required|number|kucun" onchange="renderPrice('+num+');" value="'+price+'" name="inventoryPrice['+num+']" class="sprukuadd_03_tt_input">'+
						'</td>'+
						'<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+
						'<input type="text" lay-verify="required|number|kucun" onchange="renderHeji('+num+');" name="inventoryHeji['+num+']" class="sprukuadd_03_tt_input">'+
						'</td>';
						$("#rowTrHeji").before(str);
                        renderPrice(num);
			    	}
			    	$("#dataTable").attr("rows",num);
			    	hideSearch();
			    	addRow();
			    }else{
			    	hideSearch();
			    }
		  	}
		  }
		  productListForm = form;
		  laydate.render({
		  	elem: '#dtTime'
		  	,max:'<?=date("Y-m-d H:i:s")?>'
            ,value:'<?=date("Y-m-d H:i")?>'
            ,type: 'datetime'
            ,format: 'yyyy-MM-dd HH:mm'
		  });
		  productListTalbe = table.render({
		    elem: '#product_list'
		    ,height: "full-250"
		    ,url: '?m=system&s=caigou&a=getpdts'
		    ,page: true
		    ,cols: [[{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:"display:none;"},{field: 'productId', title: 'productId', width:0,style:"display:none;"},{field:'sn',title:'商品编码',width:150},{field:'title',title:'商品名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'units',title:'单位',width:100},{field:'shuliang',title:'采购数量',width:120},{field:'price',title:'采购价',width:120}]]
		    ,done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='productId']").hide();
		  form.verify({
            kucun:function(value,item){
                if(value<=0){
                    return '字段不能小于或等于0';
                }
            }
          });
          form.on('select(changeStore)',function(){
          	var trs = $("#dataTable tr");
			var length = trs.length;
            if(trs.length>4){
                $("#dataTable tr").each(function(){
                	var i = $(this).index();
                	if($(this).attr("id")=='rowTrHeji'){
                		return false;
                	}else if(i>0){
                        $(this).remove();
                    }
                });
                addRow();
                readerTr();
                renderAllPrice();
                layer.closeAll();
            }
          });
          form.on('radio(gys1)',function(){
          	$("#ifsupplier1").show();
          	$("#ifsupplier0").hide();
          });
          form.on('radio(gys2)',function(){
          	$("#ifsupplier1").hide();
          	$("#ifsupplier0").show();
          	$("#supplierId option[value='']").prop('selected',true);
          	form.render('select');
          	var trs = $("#dataTable tr");
			var length = trs.length;
            if(trs.length>4){
                $("#dataTable tr").each(function(){
                	var i = $(this).index();
                	if($(this).attr("id")=='rowTrHeji'){
                		return false;
                	}else if(i>0){
                        $(this).remove();
                    }
                });
                addRow();
                readerTr();
                renderAllPrice();
                layer.closeAll();
            }
          });
          form.on('radio(type1)',function(){
          	var price = $("#price").val();
          	$("#price_payed").prop('readonly',true).addClass('disabled').val(price);
          });
          form.on('radio(type2)',function(){
          	$("#price_payed").prop('readonly',false).removeClass('disabled');
          });
          form.on('submit(tijiao)', function(data){
          	var price = parseFloat($("#price").val());
          	var price_payed = parseFloat($("#price_payed").val());
          	if(price<=0){
          		layer.msg('请先添加采购产品',function(){});
          		return false;
          	}
          	if(price_payed>price){
          		layer.msg('已付款金额不能大于应付金额',function(){});
          		return false;
          	}
          	var supplierType = $("input[name='ifsupplier']:checked").val();
          	var supplierId = $("#supplierId option:selected").val();
          	if(supplierType==1&&supplierId==''){
          		layer.msg('请先选择供应商！',function(){});
          		return false;
          	}
          	var supplierName = $("#supplierName").val();
          	if(supplierType==0&&supplierName==''){
          		$("#supplierName").focus();
          		layer.msg('请输入供应商名称！',function(){});
          		return false;
          	}
			layer.load();
		  });
		  $("#sprkadd_xuanzesp_03_01").on("click", function(){
		    active['appendCheckData'].call(this);
		  });
          table.on('checkbox(product_list)',function(obj){
            if(typeof(obj.data)=='undefined'){
                if(obj.checked){
                    $("#product_list").next().find(".sprkadd_xuanzesp_02_tt_input").removeClass('disabled').removeAttr('readonly');
                }else{
                    $("#product_list").next().find(".sprkadd_xuanzesp_02_tt_input").addClass('disabled').prop('readonly',true);
                }
            }else{
                var pdtId = obj.data.id;
                if(obj.data.LAY_CHECKED){
                    $("#shuliang_"+pdtId).removeClass('disabled').removeAttr('readonly');
                    $("#price_"+pdtId).removeClass('disabled').removeAttr('readonly');
                }else{
                    $("#shuliang_"+pdtId).addClass('disabled').prop('readonly',true);
                    $("#price_"+pdtId).addClass('disabled').prop('readonly',true);
                }
            }
          });
		});
	</script>
	<script type="text/javascript" src="js/caigou_add.js"></script>
    <? require('views/help.html');?>
</body>
</html>