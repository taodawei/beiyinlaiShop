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
$cangkus = $db->get_results("select id,title from demo_kucun_store where comId=$comId and status=1 order by id asc");
$storeId = 0;
$step = 1;$price_xiaoshu = 100;
if($product_set->number_num>0){
	$chushu = pow(10,$product_set->number_num);
	$step = 1/$chushu;
}
if($product_set->price_num>0){
  $price_xiaoshu = pow(10,$product_set->price_num);
}
$caigouId = (int)$request['caigouId'];
$orderId = $kucun_set->caigou_tuihuo_pre.'_'.date("Ymd").'_'.getOrderId($comId,5);
$channels = array();
if(is_file("../cache/channels_$comId.php")){
	$content = file_get_contents("../cache/channels_$comId.php");
	$channels = json_decode($content);
}
$caigous = $db->get_results("select id,orderId from demo_caigou where comId=$comId and status=1 and rukuStatus>0 order by id desc limit 30");
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
    <link href="styles/supplier.css" rel="stylesheet" type="text/css">
	<link href="layui/css/layui.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="js/jquery.min.js"></script>
	<script type="text/javascript"  src="layui/layui.js"></script>
	<script type="text/javascript" src="js/common.js"></script>
</head>
<body>
	<div class="right_up">
    	<a href="javascript:history.go(-1);"><img src="images/biao_63.png"/> 新增采购退货单</a>
    </div>
	<div class="right_down">
    	<div class="sprukuadd">
    		<form id="addForm" action="?m=system&s=caigou_tuihuo&a=add&tijiao=1" method="post" class="layui-form">
        	<div class="sprukuadd_01">
            	<div class="sprukuadd_01_canku">
                	<div class="sprukuadd_01_canku_left">
                    	<span>*</span>关联采购订单
                    </div>
                	<div class="sprukuadd_01_canku_right">
                    	<select name="caigouId" id="caigouId" lay-filter="changeCaigou" lay-verify="required">
                            <option value="">请选择要退货的采购单</option>
                            <? foreach($caigous as $caigou){
                                ?><option value="<?=$caigou->id?>" <? if($caigouId==$caigou->id){?>selected="selected"<? }?>><?=$caigou->orderId?></option><?
                            }?>
                        </select>
                    </div>
                	<div class="clearBoth"></div>
                </div>
                <div class="sprukuadd_01_riqi">
                    <div class="sprukuadd_01_riqi_left">
                        <span style="color:red">*</span>退货仓库
                    </div>
                    <div class="sprukuadd_01_riqi_right">
                        <select name="storeId" id="storeId" lay-filter="changeStore" lay-verify="required">
                            <option value="">请选择要退货的仓库</option>
                            <? foreach($cangkus as $cangku){
                                ?><option value="<?=$cangku->id?>"><?=$cangku->title?></option><?
                            }?>
                        </select>
                    </div>
                    <div class="sprukuadd_01_riqi_left" style="margin-left:20px;width:auto;display:inline-block;">
                        采购商：<span id="caigoushang"><? if(!empty($caigouId)){echo $db->get_var("select title from demo_supplier where id=(select supplierId from demo_caigou where id=$caigouId)");}?></span>
                    </div>
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
                        <td bgcolor="#7bc8ed" width="100" class="sprukuadd_03_title" align="center" valign="middle"> 
                            采购数量
                        </td>
                        <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle" onmouseenter="tips(this,'1.未入库的不能退货<br>2.退货量不能大于库存数量<br>3.可退货量会减去已退货的数量',1);" onmouseleave="hideTips();"> 
                        	退货数量 <img src="images/biao_83.png" style="position:relative;top:-2px;">
                        </td>
                        <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
                            退货单价
                        </td>
                        <td bgcolor="#7bc8ed" width="175" class="sprukuadd_03_title" align="center" valign="middle"> 
                        	小计
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
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0</td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle"></td>
                        <td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">0.00</td>
                    </tr>
                    <tr> 
                        <td class="add_td5" colspan="10">
                            <div class="add_other">
                                <div style="opacity:.8;"><input type="checkbox" name="ifxieshang" lay-filter="ifxieshang" lay-skin="primary" title="已通过协商，获批退款金额为：">
                                    <input name="xieshangMoney" id="xieshangMoney" value="0" lay-verify="required|number" type="text" style="width:100px;display:inline-block;" readonly="true" class="layui-input disabled" onchange="$('#price_all').html($(this).val());">
                                        元           
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
                        var caigouId = $("#caigouId option:selected").val();
                        var storeId = $("#storeId option:selected").val();
                        if(caigouId==''){
                            layer.msg('请先选择采购订单！',function(){});
                            return false;
                        }
                        if(storeId==''){
                            layer.msg('请先选择退货仓库！',function(){});
                            return false;
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
                        	<span>*</span>退货单号
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input name="orderId" lay-verify="required" value="<?=$orderId?>" type="text" placeholder="您也可以自定义单号" class="layui-input">
                        </div>
                        <div class="sprukuadd_04_right">
                        	<a href="index.php?a=shezhi&url=%3Fm%3Dsystem%26s%3Dkucun_set%26a%3Dchuruku" target="_blank">设置采购退货单号 ></a>
                        </div>
                    	<div class="clearBoth"></div>
                    </li>
                    <li>
                        <div class="sprukuadd_04_left">
                            <span>*</span>退货日期
                        </div>
                        <div class="sprukuadd_04_right" style="width:340px;">
                            <input type="text" name="dtTime" id="dtTime" class="layui-input" value="<?=date("Y-m-d")?>" lay-verify="required">
                        </div>
                        <div class="clearBoth"></div>
                    </li>
                    <li>
                    	<div class="sprukuadd_04_left">
                        	经办人
                        </div>
                    	<div class="sprukuadd_04_right" style="width:340px;">
                        	<input type="text" name="jingbanren" class="layui-input">
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
                    <li>
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
                        var price = $("#price_"+inventoryId).val();
                        num = num+1;
                        var str = '<tr height="48" id="rowTr'+num+'"><td bgcolor="#ffffff" width="70" class="sprukuadd_03_tt" align="center" valign="middle"></td>'+
                        '<td bgcolor="#ffffff" width="118" class="sprukuadd_03_tt" align="center" valign="middle">'+
                            '<a href="javascript:" onclick="addRow();"><img src="images/biao_65.png" class="sprukuadd_03_tt_zeng"/></a>  <a href="javascript:" onclick="delRow('+num+');"><img src="images/biao_66.png"/></a> '+ 
                        '</td>'+
                        '<td bgcolor="#ffffff" width="166" class="sprukuadd_03_tt" align="center" valign="middle">'+sn+'</td>'+
                        '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+title+'</td>'+
                        '<td bgcolor="#ffffff" width="265" class="sprukuadd_03_tt" align="center" valign="middle">'+key_vals+'</td>'+
                        '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+units+'</td>'+
                        '<td bgcolor="#ffffff" class="sprukuadd_03_tt" align="center" valign="middle">'+data[i].nums+'</td>'+
                        '<td bgcolor="#ffffff" width="175" class="sprukuadd_03_tt" align="center" valign="middle">'+
                            '<input type="text" lay-verify="kucun" onchange="renderPrice('+num+');" onmouseenter="tips(this,\'最多可退'+max+'\',1)" onmouseout="hideTips();" max="'+max+'" name="inventoryNum['+num+']" value="'+shuliang+'" class="sprukuadd_03_tt_input">'+
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
		    ,url: '?m=system&s=caigou_tuihuo&a=getpdts'
		    ,page: true
            ,where: {
                storeId:<?=$storeId?>,
                caigouId:<?=$caigouId?>
            }
		    ,cols: [[{type:'checkbox', fixed: 'left'},{field: 'id', title: 'id', width:0,style:"display:none;"},{field: 'productId', title: 'productId', width:0,style:"display:none;"},{field:'sn',title:'商品编码',width:150},{field:'title',title:'商品名称',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'key_vals',title:'商品规格',width:300,style:"height:auto;line-height:22px;white-space:normal;"},{field:'units',title:'单位',width:100},{field:'nums',title:'采购数量',width:100},{field:'shuliang',title:'退货数量',width:120},{field:'price',title:'退货价',width:120}]]
		    ,done: function(res, curr, count){
			    $("#page").val(curr);
			    layer.closeAll('loading');
			  }
		  });
		  $("th[data-field='id']").hide();
		  $("th[data-field='productId']").hide();
		  form.verify({
            kucun:function(value,item){
                if(value<0){
                    return '字段不能小于0';
                }
                var max = parseFloat($(item).attr('max'));
                if(value>max){
                    $(item).focus();
                    return '退货数量'+value+'不能大于'+max;
                }
            }
          });
          form.on('select(changeStore)',function(){
            readerAllItems();
          });
          form.on('select(changeCaigou)',function(data){
            readerAllItems();
            $.ajax({
                type: "POST",
                url: "/erp_service.php?action=getSupplierByCaigou&id="+data.value,
                data: "",
                dataType:'text',timeout : 8000,
                success: function(resdata){
                    $("#caigoushang").html(resdata);
                    //$("#pdtList"+id+" ul").html(resdata);
                }
            });
          });
          form.on('radio(gys1)',function(){
            $("#ifsupplier1").show();
            $("#ifsupplier0").hide();
          });
          form.on('checkbox(ifxieshang)',function(data){
            if(data.elem.checked){
                $("#xieshangMoney").prop("readonly",false).removeClass('disabled');
                $(".add_other div").eq(0).css("opacity","1");
                $("#price_all").html($("#xieshangMoney").val());
            }else{
                $("#xieshangMoney").prop("readonly",true).addClass('disabled');
                $(".add_other div").eq(0).css("opacity",".8");
                $("#price_all").html($("#price").val());
            }
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
                layer.msg('请先添加退货产品',function(){});
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
	<script type="text/javascript" src="js/caigou_tuihuo_add.js"></script>
    <? require('views/help.html');?>
</body>
</html>